<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GreenCoffeeLot;
use App\Models\SensoryProfile;
use App\Models\LotAttachment;
use App\Models\RoastingBatch; 
use App\Models\InventorySession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class GreenCoffeeLotController extends Controller
{
public function index(Request $request)
{
    $query = GreenCoffeeLot::with(['sensoryProfile', 'attachments', 'roastingBatches.machine', 'locations', 'movements'])
        ->where('total_weight', '>', 0);

    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('origin', 'like', '%' . $request->search . '%')
              ->orWhere('reference', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('profile')) {
        $query->where('sensory_profile_id', $request->profile);
    }

    $lotes = $query->orderBy('created_at', 'desc')->get();
    $perfis = SensoryProfile::all(); 

    $mapData = [];
    foreach ($lotes as $lote) {
        foreach ($lote->locations as $loc) {
            $coord = strtoupper($loc->address_row . $loc->address_pos);
            $mapData[$coord][] = [
                'id' => $lote->id,
                'color' => $lote->sensoryProfile->color_hex ?? '#777',
                'reference' => $lote->reference
            ];
        }
    }

    $lastSubtractions = RoastingBatch::with('demand.lot')
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get()
        ->map(function($batch) {
            return [
                'description' => "Torra: " . ($batch->demand->lot->reference ?? 'Lote N/A'),
                'value' => number_format($batch->weight_in, 1, ',', '.') . "kg",
                'type' => 'danger'
            ];
        });

    // --- NOVA PARTE: BUSCA AS ÚLTIMAS SAÍDAS MANUAIS PARA O FEED ---
    $ultimasSaidas = \App\Models\StockMovement::with('lote')
        ->where('type', 'out')
        ->latest()
        ->take(2)
        ->get();

    $session = InventorySession::whereIn('status', ['aberto', 'iniciado'])
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$session) {
        $session = InventorySession::create([
            'status' => 'aberto',
            'user_id' => Auth::id(),
            'inventory_date' => now()->setTimezone('America/Sao_Paulo')->toDateString()
        ]);
    }

    $adjustments = DB::table('inventory_adjustments')
        ->where('inventory_session_id', $session->id)
        ->get()
        ->groupBy('green_coffee_lot_id');

    $history = InventorySession::where('status', 'finalizado')
        ->orderBy('id', 'desc') 
        ->get()
        ->toArray(); 

    return view('green-coffee.index', compact(
        'lotes', 'perfis', 'mapData', 'lastSubtractions', 'session', 'adjustments', 'history', 'ultimasSaidas'
    ));
}
    public function startInventory(Request $request)
{
    $session = InventorySession::where('status', 'aberto')->first();
    if (!$session) return response()->json(['error' => 'Sessão não encontrada.'], 422);
    
    try {
        DB::transaction(function () use ($session) {
            // 1. Busca todos os lotes que possuem peso em estoque
            $lotesComEstoque = GreenCoffeeLot::with('locations')
                ->where('total_weight', '>', 0)
                ->get();

            foreach ($lotesComEstoque as $lote) {
                // 2. Se o lote tiver localizações, criamos um ajuste para cada uma
                if ($lote->locations->count() > 0) {
                    foreach ($lote->locations as $loc) {
                        DB::table('inventory_adjustments')->insert([
                            'inventory_session_id' => $session->id,
                            'green_coffee_lot_id' => $lote->id,
                            'new_address_row'      => strtoupper($loc->address_row),
                            'new_address_pos'      => $loc->address_pos,
                            'closed_bags'          => $loc->quantity_bags,
                            'bag_weight'           => $lote->unit_weight ?: 60,
                            'remainder_weight'     => $loc->remainder_weight,
                            'physical_weight'      => ($loc->quantity_bags * ($lote->unit_weight ?: 60)) + $loc->remainder_weight,
                            'system_weight'        => $lote->total_weight,
                            'difference_kg'        => 0, // Inicialmente sem divergência
                            'created_at'           => now(),
                            'updated_at'           => now()
                        ]);
                    }
                } else {
                    // 3. Caso o lote tenha peso mas não tenha localização (exceção), cria uma linha vazia
                    DB::table('inventory_adjustments')->insert([
                        'inventory_session_id' => $session->id,
                        'green_coffee_lot_id' => $lote->id,
                        'new_address_row'      => null,
                        'new_address_pos'      => null,
                        'closed_bags'          => 0,
                        'bag_weight'           => $lote->unit_weight ?: 60,
                        'remainder_weight'     => $lote->total_weight,
                        'physical_weight'      => $lote->total_weight,
                        'system_weight'        => $lote->total_weight,
                        'difference_kg'        => 0,
                        'created_at'           => now(),
                        'updated_at'           => now()
                    ]);
                }
            }

            // 4. Muda o status para iniciado
            $session->update([
                'status' => 'iniciado',
                'inventory_date' => now()->setTimezone('America/Sao_Paulo')->toDateString()
            ]);
        });

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Erro ao processar snapshot: ' . $e->getMessage()], 500);
    }
}

    public function storeInventoryAdjustment(Request $request)
    {
        $session = InventorySession::where('status', 'iniciado')->first();
        if (!$session) return response()->json(['error' => 'Inicie o inventário.'], 403);

        DB::transaction(function () use ($request, $session) {
            // Limpa os ajustes antigos deste lote para salvar os novos (múltiplas linhas)
            DB::table('inventory_adjustments')
                ->where('inventory_session_id', $session->id)
                ->where('green_coffee_lot_id', $request->lot_id)
                ->delete();

            foreach ($request->locations as $loc) {
                DB::table('inventory_adjustments')->insert([
                    'inventory_session_id' => $session->id,
                    'green_coffee_lot_id' => $request->lot_id,
                    'new_address_row' => strtoupper($loc['row']),
                    'new_address_pos' => $loc['pos'],
                    'closed_bags' => $loc['closed_bags'] ?? 0,
                    'bag_weight' => $loc['bag_weight'] ?? 60,
                    'remainder_weight' => $loc['remainder_weight'] ?? 0,
                    'physical_weight' => $loc['total_line'],
                    'system_weight' => $request->system_weight,
                    'difference_kg' => 0, // Calculado na finalização
                    'reason' => $request->reason,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function registrarSaida(Request $request)
{
    return DB::transaction(function () use ($request) {
        $lote = GreenCoffeeLot::findOrFail($request->green_coffee_lot_id);

        // 1. Desconta o peso do lote
        $lote->decrement('total_weight', $request->weight);

        // 2. CRIA O REGISTRO DE MOVIMENTAÇÃO (Isso corrige o erro do Modal)
        $lote->movements()->create([
            'user_id' => auth()->id(),
            'location_id' => $request->location_id,
            'type' => 'out',
            'weight' => $request->weight,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Saída registrada no histórico!');
    });
}

    public function finishInventory(Request $request)
{
    // 1. Busca a sessão que está REALMENTE iniciada
    $session = InventorySession::where('status', 'iniciado')->first();
    if (!$session) {
        return response()->json(['error' => 'Você precisa clicar em "INICIAR INVENTÁRIO" antes de finalizar.'], 422);
    }

    $adjustments = DB::table('inventory_adjustments')->where('inventory_session_id', $session->id)->get();

    try {
        DB::transaction(function () use ($session, $adjustments) {
            $totalSessionDiff = 0;
            $lotsToProcess = $adjustments->groupBy('green_coffee_lot_id');

            foreach ($lotsToProcess as $lotId => $adjs) {
                $lote = GreenCoffeeLot::find($lotId);
                if (!$lote) continue;

                $totalPhysicalLot = $adjs->sum('physical_weight');
                $lotDiff = $totalPhysicalLot - $lote->total_weight;

                // 2. Atualiza o Peso do Lote e calcula sacas (evita divisão por zero)
                $lote->total_weight = $totalPhysicalLot;
                $unitWeight = ($lote->unit_weight > 0) ? $lote->unit_weight : 60;
                $lote->quantity_bags = floor($totalPhysicalLot / $unitWeight);
                $lote->save();

                // 3. Atualiza a diferença na tabela de ajustes para o histórico
                DB::table('inventory_adjustments')
                    ->where('inventory_session_id', $session->id)
                    ->where('green_coffee_lot_id', $lotId)
                    ->update(['difference_kg' => $lotDiff]);

                // 4. Atualiza Localizações (Limpa antigas e grava novas)
                $lote->locations()->delete();
                foreach ($adjs as $adj) {
                    if (!empty($adj->new_address_row)) {
                        $lote->locations()->create([
                            'address_row' => strtoupper($adj->new_address_row),
                            'address_pos' => $adj->new_address_pos,
                            'quantity_bags' => floor($adj->physical_weight / $unitWeight),
                            'remainder_weight' => fmod($adj->physical_weight, $unitWeight)
                        ]);
                    }
                }
                
                $totalSessionDiff += $lotDiff;
            }
            
            // 5. Finaliza a sessão
            $session->update([
                'status' => 'finalizado', 
                'total_diff_kg' => $totalSessionDiff,
                'inventory_date' => now() // Garante a data do fechamento
            ]);
        });

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Erro ao processar banco: ' . $e->getMessage()], 500);
    }
}

    public function getInventoryDetail($id)
    {
        $session = InventorySession::with('user')->findOrFail($id);
        $adjustments = DB::table('inventory_adjustments')
            ->join('green_coffee_lots', 'inventory_adjustments.green_coffee_lot_id', '=', 'green_coffee_lots.id')
            ->where('inventory_session_id', $id)
            ->select('inventory_adjustments.*', 'green_coffee_lots.reference')
            ->get();
        return response()->json(['session' => $session, 'adjustments' => $adjustments]);
    }

    public function store(Request $request) { $request->validate(['reference' => 'required', 'origin' => 'required', 'sensory_profile_id' => 'required', 'unit_weight' => 'required', 'locations' => 'required', 'quantity_bags' => 'required', 'total_weight' => 'required']); $lote = new GreenCoffeeLot(); $lote->user_id = Auth::id(); $lote->reference = $request->reference; $lote->origin = $request->origin; $lote->origin_id = $request->origin_id; $lote->origin_type = $request->origin_type; $lote->sensory_profile_id = $request->sensory_profile_id; $lote->unit_weight = $request->unit_weight; $lote->quantity_bags = $request->quantity_bags; $lote->total_weight = $request->total_weight; $lote->save(); foreach ($request->locations as $loc) { if (!empty($loc['row'])) { $lote->locations()->create(['address_row' => strtoupper($loc['row']), 'address_pos' => $loc['pos'], 'quantity_bags' => (int)$loc['bags'], 'remainder_weight' => $loc['remainder'] ?? 0]); } } return redirect()->route('estoque.index')->with('success', 'Lote registrado!'); }
    public function destroy($id)
{
    // Buscamos o lote com todas as relações que precisam ser limpas
    $lote = GreenCoffeeLot::with(['attachments', 'locations', 'roastDemands.batches'])->findOrFail($id);

    try {
        DB::transaction(function () use ($lote, $id) {
            // 1. Deletar ajustes de inventário vinculados a este lote
            DB::table('inventory_adjustments')->where('green_coffee_lot_id', $id)->delete();

            // 2. Deletar localizações no armazém
            $lote->locations()->delete();

            // 3. Deletar Demandas de Torra e seus respectivos Ciclos (Batches)
            foreach ($lote->roastDemands as $demand) {
                // Primeiro removemos os ciclos (batches) daquela demanda
                $demand->batches()->delete();
                // Depois removemos a demanda em si
                $demand->delete();
            }

            // 4. Gerenciar arquivos físicos e registros de anexos
            foreach ($lote->attachments as $at) {
                if (Storage::disk('public')->exists($at->file_path)) {
                    Storage::disk('public')->delete($at->file_path);
                }
            }
            $lote->attachments()->delete();

            // 5. Finalmente, excluir o lote
            $lote->delete();
        });

        return redirect()->route('estoque.index')->with('success', 'Lote e todas as demandas vinculadas foram excluídos!');
    } catch (\Exception $e) {
        // Retornamos o erro caso algo falhe na transação
        return redirect()->back()->with('error', 'Erro ao excluir o lote: ' . $e->getMessage());
    }
}
    public function storeAttachment(Request $request, $id) { $request->validate(['file' => 'required|file|max:2048']); $lote = GreenCoffeeLot::findOrFail($id); if ($request->hasFile('file')) { $path = $request->file('file')->store('attachments', 'public'); $lote->attachments()->create(['file_name' => $request->file('file')->getClientOriginalName(), 'file_path' => $path]); } return back()->with('success', 'Anexo salvo!'); }
    public function destroyAttachment($id) { $attachment = LotAttachment::findOrFail($id); if (Storage::disk('public')->exists($attachment->file_path)) Storage::disk('public')->delete($attachment->file_path); $attachment->delete(); return redirect()->back()->with('success', 'Anexo removido!'); }
    public function capture($id) { $lote = GreenCoffeeLot::findOrFail($id); session(['captured_lot' => $lote->id, 'captured_lot_name' => $lote->reference]); return redirect()->back()->with('success', 'Lote capturado!'); }
    public function clearCapture() { session()->forget(['captured_lot', 'captured_lot_name']); return redirect()->back()->with('info', 'Lote solto.'); }
    public function show($id) { return back(); }
}