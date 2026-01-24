<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GreenCoffeeLot;
use App\Models\SensoryProfile;
use App\Models\LotAttachment;
use App\Models\RoastingBatch; 
use App\Models\StockMovement; // Importado corretamente
use Carbon\Carbon;
use App\Models\InventorySession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class GreenCoffeeLotController extends Controller
{
    public function index(Request $request)
    {
        // 1. Consulta base de lotes com estoque positivo - Filtrado por empresa via Trait automática
        $query = GreenCoffeeLot::with(['sensoryProfile', 'attachments', 'roastingBatches.machine', 'locations', 'movements'])
            ->where('total_weight', '>', 0);

        // 2. Filtros de Busca e Perfil Sensorial
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

        // 3. MAPEAMENTO DINÂMICO DO ARQUIVO SVG
        $mapView = auth()->user()->mainCompany->map_view ?? 'default';

        // 4. Processamento dos dados para preenchimento das cores no mapa
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

        // 5. Histórico de subtrações (Torras recentes)
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

        // 6. Últimas Saídas Manuais para o Feed
        $ultimasSaidas = StockMovement::with('lote')
            ->where('type', 'out')
            ->latest()
            ->take(2)
            ->get();

        // 7. Gestão de Sessão de Inventário
        $session = InventorySession::where('main_company_id', auth()->user()->main_company_id)
            ->whereIn('status', ['aberto', 'iniciado'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$session) {
            $session = InventorySession::create([
                'main_company_id' => auth()->user()->main_company_id,
                'status' => 'aberto',
                'user_id' => Auth::id(),
                'inventory_date' => now()->setTimezone('America/Sao_Paulo')->toDateString()
            ]);
        }

        // 8. Ajustes e Histórico de Inventários Finalizados
        $adjustments = DB::table('inventory_adjustments')
            ->where('inventory_session_id', $session->id)
            ->get()
            ->groupBy('green_coffee_lot_id');

        $history = InventorySession::where('main_company_id', auth()->user()->main_company_id)
            ->where('status', 'finalizado')
            ->orderBy('id', 'desc') 
            ->get()
            ->toArray(); 

        return view('green-coffee.index', compact(
            'lotes', 'perfis', 'mapData', 'lastSubtractions', 'session', 'adjustments', 'history', 'ultimasSaidas', 'mapView'
        ));
    }

    public function startInventory(Request $request)
    {
        $session = InventorySession::where('main_company_id', auth()->user()->main_company_id)
            ->where('status', 'aberto')
            ->first();

        if (!$session) return response()->json(['error' => 'Sessão não encontrada.'], 422);
        
        try {
            DB::transaction(function () use ($session) {
                $mainCompanyId = auth()->user()->main_company_id;
                $lotesComEstoque = GreenCoffeeLot::with('locations')->where('total_weight', '>', 0)->get();

                foreach ($lotesComEstoque as $lote) {
                    if ($lote->locations->count() > 0) {
                        $primeiraLinha = true;
                        foreach ($lote->locations as $loc) {
                            $pesoNaLocalizacao = ($loc->quantity_bags * ($lote->unit_weight ?: 60)) + $loc->remainder_weight;
                            $expectativaSistema = $primeiraLinha ? $lote->total_weight : 0;
                            
                            DB::table('inventory_adjustments')->insert([
                                'main_company_id'      => $mainCompanyId,
                                'inventory_session_id' => $session->id,
                                'green_coffee_lot_id'  => $lote->id,
                                'new_address_row'      => strtoupper($loc->address_row),
                                'new_address_pos'      => $loc->address_pos,
                                'closed_bags'          => $loc->quantity_bags,
                                'bag_weight'           => $lote->unit_weight ?: 60,
                                'remainder_weight'     => $loc->remainder_weight,
                                'physical_weight'      => $pesoNaLocalizacao,
                                'system_weight'        => $expectativaSistema,
                                'difference_kg'        => $pesoNaLocalizacao - $expectativaSistema,
                                'created_at'           => now(),
                                'updated_at'           => now()
                            ]);
                            $primeiraLinha = false; 
                        }
                    }
                }
                $session->update(['status' => 'iniciado', 'inventory_date' => now()->setTimezone('America/Sao_Paulo')->toDateString()]);
            });
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar: ' . $e->getMessage()], 500);
        }
    }

    public function storeInventoryAdjustment(Request $request)
    {
        $mainCompanyId = auth()->user()->main_company_id;
        $session = InventorySession::where('main_company_id', $mainCompanyId)
            ->where('status', 'iniciado')
            ->first();

        if (!$session) return response()->json(['error' => 'Inicie o inventário.'], 403);

        DB::transaction(function () use ($request, $session, $mainCompanyId) {
            DB::table('inventory_adjustments')
                ->where('inventory_session_id', $session->id)
                ->where('green_coffee_lot_id', $request->lot_id)
                ->delete();

            $primeiraLinha = true;
            foreach ($request->locations as $loc) {
                $fisicoLinha = (float)$loc['total_line'];
                $sistemaLinha = $primeiraLinha ? (float)$request->system_weight : 0;

                DB::table('inventory_adjustments')->insert([
                    'main_company_id'      => $mainCompanyId,
                    'inventory_session_id' => $session->id,
                    'green_coffee_lot_id'  => $request->lot_id,
                    'new_address_row'      => strtoupper($loc['row']),
                    'new_address_pos'      => $loc['pos'], // CORREÇÃO: Removido o '0' intruso
                    'closed_bags'          => $loc['closed_bags'] ?? 0,
                    'bag_weight'           => $loc['bag_weight'] ?? 60,
                    'remainder_weight'     => $loc['remainder_weight'] ?? 0,
                    'physical_weight'      => $fisicoLinha,
                    'system_weight'        => $sistemaLinha,
                    'difference_kg'        => $fisicoLinha - $sistemaLinha, 
                    'reason'               => $request->reason,
                    'created_at'           => now(),
                    'updated_at'           => now()
                ]);
                $primeiraLinha = false;
            }
        });

        return response()->json(['success' => true]);
    }

    public function registrarSaida(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $lote = GreenCoffeeLot::findOrFail($request->green_coffee_lot_id);
            $lote->decrement('total_weight', $request->weight);

            $lote->movements()->create([
                'main_company_id' => auth()->user()->main_company_id,
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
        $session = InventorySession::where('main_company_id', auth()->user()->main_company_id)
            ->where('status', 'iniciado')
            ->first();

        if (!$session) return response()->json(['error' => 'Sessão não encontrada.'], 422);

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

                    $lote->total_weight = $totalPhysicalLot;
                    $unitWeight = ($lote->unit_weight > 0) ? $lote->unit_weight : 60;
                    $lote->quantity_bags = floor($totalPhysicalLot / $unitWeight);
                    $lote->save();

                    foreach ($adjs as $adj) {
                        DB::table('inventory_adjustments')
                            ->where('id', $adj->id)
                            ->update(['difference_kg' => $adj->physical_weight - $adj->system_weight]);
                    }

                    $lote->locations()->delete();

                    foreach ($adjs as $adj) {
                        if (!empty($adj->new_address_row) && !empty($adj->new_address_pos)) {
                            $lote->locations()->create([
                                'main_company_id' => auth()->user()->main_company_id,
                                'address_row' => strtoupper($adj->new_address_row),
                                'address_pos' => $adj->new_address_pos,
                                'quantity_bags' => floor($adj->physical_weight / $unitWeight),
                                'remainder_weight' => fmod($adj->physical_weight, $unitWeight)
                            ]);
                        }
                    }
                    $totalSessionDiff += $lotDiff;
                }
                $session->update(['status' => 'finalizado', 'total_diff_kg' => $totalSessionDiff]);
            });
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar banco: ' . $e->getMessage()], 500);
        }
    }

    public function getInventoryDetail($id)
    {
        $session = InventorySession::where('main_company_id', auth()->user()->main_company_id)
            ->with('user')
            ->findOrFail($id);
        
        $adjustments = DB::table('inventory_adjustments')
            ->join('green_coffee_lots', 'inventory_adjustments.green_coffee_lot_id', '=', 'green_coffee_lots.id')
            ->where('inventory_session_id', $id)
            ->select(
                'green_coffee_lots.reference',
                DB::raw('SUM(inventory_adjustments.system_weight) as system_weight'),
                DB::raw('SUM(inventory_adjustments.physical_weight) as physical_weight'),
                DB::raw('SUM(inventory_adjustments.physical_weight - inventory_adjustments.system_weight) as difference_kg'),
                DB::raw('MAX(inventory_adjustments.reason) as reason')
            )
            ->groupBy('green_coffee_lots.id', 'green_coffee_lots.reference')
            ->get();

        return response()->json(['session' => $session, 'adjustments' => $adjustments]);
    }

    public function store(Request $request) { 
        $request->validate(['reference' => 'required', 'origin' => 'required', 'sensory_profile_id' => 'required', 'unit_weight' => 'required', 'locations' => 'required', 'quantity_bags' => 'required', 'total_weight' => 'required']); 
        $lote = new GreenCoffeeLot(); 
        $lote->main_company_id = auth()->user()->main_company_id;
        $lote->user_id = Auth::id(); 
        $lote->reference = $request->reference; 
        $lote->origin = $request->origin; 
        $lote->origin_id = $request->origin_id; 
        $lote->origin_type = $request->origin_type; 
        $lote->sensory_profile_id = $request->sensory_profile_id; 
        $lote->unit_weight = $request->unit_weight; 
        $lote->quantity_bags = $request->quantity_bags; 
        $lote->total_weight = $request->total_weight; 
        $lote->save(); 
        foreach ($request->locations as $loc) { 
            if (!empty($loc['row'])) { 
                $lote->locations()->create([
                    'main_company_id' => auth()->user()->main_company_id,
                    'address_row' => strtoupper($loc['row']), 
                    'address_pos' => $loc['pos'], 
                    'quantity_bags' => (int)$loc['bags'], 
                    'remainder_weight' => $loc['remainder'] ?? 0
                ]); 
            } 
        } 
        return redirect()->route('estoque.index')->with('success', 'Lote registrado!'); 
    }

    public function getHistoryData(Request $request)
    {
        try {
            $tipo = $request->get('tipo', 'entrada');
            $start = $request->get('start_date');
            $end = $request->get('end_date');

            if ($tipo == 'entrada') {
                $query = GreenCoffeeLot::with(['sensoryProfile', 'locations']);
                if ($start && $end) {
                    $query->whereBetween('created_at', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()]);
                }
                $dados = $query->latest()->get()->map(function($lote) {
                    return [
                        'data' => $lote->created_at->subHours(3)->format('d/m/Y H:i'),
                        'perfil' => $lote->sensoryProfile->name ?? 'N/A',
                        'cor' => $lote->sensoryProfile->color_hex ?? '#ccc',
                        'endereco' => $lote->locations->map(fn($l) => $l->address_row . $l->address_pos)->join(', '),
                        'peso' => (float)$lote->total_weight,
                        'ref' => $lote->reference,
                        'tipo' => 'entrada'
                    ];
                });
            } else {
                $query = StockMovement::with(['lote.sensoryProfile', 'location']);
                if ($start && $end) {
                    $query->whereBetween('created_at', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()]);
                }
                $dados = $query->where('type', 'out')->latest()->get()->map(function($mov) {
                    return [
                        'data' => $mov->created_at->subHours(3)->format('d/m/Y H:i'),
                        'perfil' => $mov->lote->sensoryProfile->name ?? 'N/A',
                        'cor' => $mov->lote->sensoryProfile->color_hex ?? '#ccc',
                        'endereco' => $mov->location ? ($mov->location->address_row . $mov->location->address_pos) : 'N/A',
                        'peso' => (float)$mov->weight,
                        'ref' => $mov->lote->reference ?? 'Lote N/A',
                        'tipo' => 'saida'
                    ];
                });
            }
            return response()->json($dados);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $lote = GreenCoffeeLot::where('main_company_id', auth()->user()->main_company_id)->with(['attachments', 'locations', 'roastDemands.batches'])->findOrFail($id);
        try {
            DB::transaction(function () use ($lote, $id) {
                DB::table('inventory_adjustments')->where('green_coffee_lot_id', $id)->delete();
                $lote->locations()->delete();
                foreach ($lote->roastDemands as $demand) { $demand->batches()->delete(); $demand->delete(); }
                foreach ($lote->attachments as $at) { if (Storage::disk('public')->exists($at->file_path)) { Storage::disk('public')->delete($at->file_path); } }
                $lote->attachments()->delete();
                $lote->delete();
            });
            return redirect()->route('estoque.index')->with('success', 'Lote excluído!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeAttachment(Request $request, $id) { 
        $request->validate(['file' => 'required|file|max:2048']); 
        $lote = GreenCoffeeLot::where('main_company_id', auth()->user()->main_company_id)->findOrFail($id); 
        if ($request->hasFile('file')) { 
            $path = $request->file('file')->store('attachments', 'public'); 
            $lote->attachments()->create(['main_company_id' => auth()->user()->main_company_id, 'file_name' => $request->file('file')->getClientOriginalName(), 'file_path' => $path]); 
        } 
        return back()->with('success', 'Anexo salvo!'); 
    }

    public function destroyAttachment($id) { 
        $attachment = LotAttachment::findOrFail($id); 
        if (Storage::disk('public')->exists($attachment->file_path)) Storage::disk('public')->delete($attachment->file_path); 
        $attachment->delete(); 
        return redirect()->back()->with('success', 'Anexo removido!'); 
    }

    public function capture($id) { 
        $lote = GreenCoffeeLot::where('main_company_id', auth()->user()->main_company_id)->findOrFail($id); 
        session(['captured_lot' => $lote->id, 'captured_lot_name' => $lote->reference]); 
        return redirect()->back()->with('success', 'Lote capturado!'); 
    }

    public function clearCapture() { 
        session()->forget(['captured_lot', 'captured_lot_name']); 
        return redirect()->back()->with('info', 'Lote solto.'); 
    }

    public function show($id) { return back(); }
}