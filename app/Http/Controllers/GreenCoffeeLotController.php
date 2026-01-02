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
        $query = GreenCoffeeLot::with(['sensoryProfile', 'attachments', 'roastingBatches.machine', 'locations'])
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

        $lastSubtractions = RoastingBatch::with('lot')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($batch) {
                return [
                    'description' => "Torra: " . ($batch->lot->reference ?? 'Lote Excluído'),
                    'value' => number_format($batch->input_weight, 1, ',', '.') . "kg",
                    'type' => 'danger'
                ];
            });

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

        // BUSCA AJUSTES AGRUPADOS POR LOTE
        $adjustments = DB::table('inventory_adjustments')
            ->where('inventory_session_id', $session->id)
            ->get()
            ->groupBy('green_coffee_lot_id');

        $history = InventorySession::where('status', 'finalizado')
            ->orderBy('id', 'desc') 
            ->get()
            ->toArray(); 

        return view('green-coffee.index', compact(
            'lotes', 'perfis', 'mapData', 'lastSubtractions', 'session', 'adjustments', 'history'
        ));
    }

    public function startInventory(Request $request)
    {
        $session = InventorySession::where('status', 'aberto')->first();
        if (!$session) return response()->json(['error' => 'Sessão não encontrada.'], 422);
        
        $session->update([
            'status' => 'iniciado',
            'inventory_date' => now()->setTimezone('America/Sao_Paulo')->toDateString()
        ]);

        return response()->json(['success' => true, 'date' => date('d/m/Y')]);
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

    public function finishInventory(Request $request)
    {
        $session = InventorySession::where('status', 'iniciado')->first();
        if (!$session) return response()->json(['error' => 'Sessão não encontrada.'], 422);

        $adjustments = DB::table('inventory_adjustments')->where('inventory_session_id', $session->id)->get();

        DB::transaction(function () use ($session, $adjustments) {
            $totalSessionDiff = 0;
            $lotsToProcess = $adjustments->groupBy('green_coffee_lot_id');

            foreach ($lotsToProcess as $lotId => $adjs) {
                $lote = GreenCoffeeLot::find($lotId);
                if (!$lote) continue;

                $totalPhysicalLot = $adjs->sum('physical_weight');
                $lotDiff = $totalPhysicalLot - $lote->total_weight;

                // 1. Atualiza o Peso do Lote
                $lote->total_weight = $totalPhysicalLot;
                $unitWeight = ($lote->unit_weight > 0) ? $lote->unit_weight : 60;
                $lote->quantity_bags = floor($totalPhysicalLot / $unitWeight);
                $lote->save();

                // 2. Atualiza Múltiplos Endereços
                $lote->locations()->delete();
                foreach ($adjs as $adj) {
                    if ($adj->new_address_row && $adj->new_address_pos) {
                        $lote->locations()->create([
                            'address_row' => $adj->new_address_row,
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

        return response()->json(['success' => true, 'message' => 'Inventário finalizado e mapa atualizado!']);
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
    public function destroy($id) { $lote = GreenCoffeeLot::with(['attachments', 'locations'])->findOrFail($id); try { DB::transaction(function () use ($lote, $id) { DB::table('inventory_adjustments')->where('green_coffee_lot_id', $id)->delete(); $lote->locations()->delete(); foreach ($lote->attachments as $at) { if (Storage::disk('public')->exists($at->file_path)) Storage::disk('public')->delete($at->file_path); } $lote->attachments()->delete(); $lote->delete(); }); return redirect()->route('estoque.index')->with('success', 'Lote excluído!'); } catch (\Exception $e) { return redirect()->back()->with('error', 'Erro ao excluir.'); } }
    public function storeAttachment(Request $request, $id) { $request->validate(['file' => 'required|file|max:2048']); $lote = GreenCoffeeLot::findOrFail($id); if ($request->hasFile('file')) { $path = $request->file('file')->store('attachments', 'public'); $lote->attachments()->create(['file_name' => $request->file('file')->getClientOriginalName(), 'file_path' => $path]); } return back()->with('success', 'Anexo salvo!'); }
    public function destroyAttachment($id) { $attachment = LotAttachment::findOrFail($id); if (Storage::disk('public')->exists($attachment->file_path)) Storage::disk('public')->delete($attachment->file_path); $attachment->delete(); return redirect()->back()->with('success', 'Anexo removido!'); }
    public function capture($id) { $lote = GreenCoffeeLot::findOrFail($id); session(['captured_lot' => $lote->id, 'captured_lot_name' => $lote->reference]); return redirect()->back()->with('success', 'Lote capturado!'); }
    public function clearCapture() { session()->forget(['captured_lot', 'captured_lot_name']); return redirect()->back()->with('info', 'Lote solto.'); }
    public function show($id) { return back(); }
}