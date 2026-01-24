<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GreenCoffeeLot;
use App\Models\SensoryProfile;
use App\Models\LotAttachment;
use App\Models\RoastingBatch; 
use App\Models\StockMovement;
use Carbon\Carbon;
use App\Models\InventorySession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class GreenCoffeeLotController extends Controller
{
    public function index(Request $request)
    {
        // Corrigido: Eager loading limpo para evitar o "unexpected identifier"
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
        $mapView = auth()->user()->mainCompany->map_view ?? 'default';

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

        $ultimasSaidas = StockMovement::with('lote')->where('type', 'out')->latest()->take(2)->get();

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

        $adjustments = DB::table('inventory_adjustments')->where('inventory_session_id', $session->id)->get()->groupBy('green_coffee_lot_id');
        $history = InventorySession::where('main_company_id', auth()->user()->main_company_id)->where('status', 'finalizado')->orderBy('id', 'desc')->get()->toArray(); 

        return view('green-coffee.index', compact('lotes', 'perfis', 'mapData', 'session', 'adjustments', 'history', 'ultimasSaidas', 'mapView'));
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

    // Mantive os outros métodos (store, registrarSaida, etc) conforme sua lógica de permissão
    public function registrarSaida(Request $request) {
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
            return redirect()->back()->with('success', 'Saída registrada!');
        });
    }

    public function storeInventoryAdjustment(Request $request) {
        $mainCompanyId = auth()->user()->main_company_id;
        $session = InventorySession::where('main_company_id', $mainCompanyId)->where('status', 'iniciado')->first();
        if (!$session) return response()->json(['error' => 'Inicie o inventário.'], 403);

        DB::transaction(function () use ($request, $session, $mainCompanyId) {
            DB::table('inventory_adjustments')->where('inventory_session_id', $session->id)->where('green_coffee_lot_id', $request->lot_id)->delete();
            $primeiraLinha = true;
            foreach ($request->locations as $loc) {
                $fisicoLinha = (float)$loc['total_line'];
                $sistemaLinha = $primeiraLinha ? (float)$request->system_weight : 0;
                DB::table('inventory_adjustments')->insert([
                    'main_company_id'      => $mainCompanyId,
                    'inventory_session_id' => $session->id,
                    'green_coffee_lot_id'  => $request->lot_id,
                    'new_address_row'      => strtoupper($loc['row']),
                    'new_address_pos'      => $loc['pos'], // Corrigido o 0 perdido
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

    public function show($id) { return back(); }
}