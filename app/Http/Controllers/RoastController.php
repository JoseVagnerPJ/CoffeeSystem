<?php

namespace App\Http\Controllers;

// IMPORTAÇÕES OBRIGATÓRIAS
use App\Models\RoastDemand;
use App\Models\RoastingBatch;
use App\Models\GreenCoffeeLot;
use App\Models\Machine;
use App\Models\User;
use App\Models\SensoryProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // <--- Certifique-se de que esta linha está aqui!

class RoastController extends Controller
{
    /**
     * Tela Principal (Kanban)
     */
    public function index() {
        $machines = Machine::where('is_active', true)->get();
        
        // Demandas que ainda não foram finalizadas para o Kanban
        $demands = RoastDemand::with(['lot', 'machine', 'mestre', 'batches', 'sensoryProfile'])
                    ->where('status', '!=', 'finished')
                    ->orderBy('created_at', 'asc')
                    ->get();

        return view('roasting.index', compact('machines', 'demands'));
    }

    /**
     * Retorna estatísticas filtradas por Máquina e Período (D/S/M) via AJAX
     */
    public function getStats(Request $request) {
        $machineId = $request->machine_id;
        $period = $request->period; // D, S, M

        // Definição da data de início com fallback seguro
        if ($period == 'D') {
            $startDate = Carbon::now()->startOfDay();
        } elseif ($period == 'S') {
            $startDate = Carbon::now()->startOfWeek();
        } else {
            $startDate = Carbon::now()->startOfMonth();
        }

        // 1. Peso Global (Todas as máquinas) no período selecionado
        $globalWeight = RoastDemand::where('status', 'finished')
            ->where('updated_at', '>=', $startDate)
            ->sum('total_weight_requested');

        // 2. Peso Individual da Máquina no período selecionado
        $machineWeight = RoastDemand::where('machine_id', $machineId)
            ->where('status', 'finished')
            ->where('updated_at', '>=', $startDate)
            ->sum('total_weight_requested');

        // 3. Construção dos dados do gráfico
        $chartData = $this->buildChartData($machineId, $period, $startDate);

        return response()->json([
            'global_weight' => number_format($globalWeight, 1, ',', '.'),
            'machine_weight' => number_format($machineWeight, 1, ',', '.'),
            'chart' => $chartData
        ]);
    }

    /**
     * Auxiliar para montar dados do Chart.js
     */
    private function buildChartData($machineId, $period, $startDate) {
        $queryGlobal = RoastDemand::select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('SUM(total_weight_requested) as total')
            )
            ->where('status', 'finished')
            ->where('updated_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $queryMachine = RoastDemand::select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('SUM(total_weight_requested) as total')
            )
            ->where('machine_id', $machineId)
            ->where('status', 'finished')
            ->where('updated_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $labels = [];
        $globalData = [];
        $machineData = [];

        $days = ($period == 'D') ? 1 : (($period == 'S') ? 7 : 30);
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d/m');
            $globalData[] = $queryGlobal->get($date, 0);
            $machineData[] = $queryMachine->get($date, 0);
        }

        return [
            'labels' => $labels,
            'global' => $globalData,
            'machine' => $machineData
        ];
    }

    // --- BUSCAS DINÂMICAS PARA O MODAL ---
    public function searchLots(Request $request) {
        return GreenCoffeeLot::where('reference', 'like', "%{$request->q}%")
            ->where('total_weight', '>', 0)
            ->get(['id', 'reference', 'total_weight', 'origin']);
    }

    public function searchProfiles(Request $request) {
        return SensoryProfile::where('name', 'like', "%{$request->q}%")->get();
    }

    public function searchMestres(Request $request) {
        return User::where('name', 'like', "%{$request->q}%")->get(['id', 'name']);
    }

    public function getLotInfo($id) {
        return GreenCoffeeLot::with('sensoryProfile')->find($id);
    }

    public function updateStatus(Request $request) {
        $demand = RoastDemand::findOrFail($request->id);
        $demand->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    // --- LÓGICA DE ARMAZENAMENTO INTELIGENTE ---
    public function store(Request $request) {
        return DB::transaction(function () use ($request) {
            $lot = GreenCoffeeLot::lockForUpdate()->find($request->green_coffee_lot_id);

            $demand = RoastDemand::create([
                'user_id' => auth()->id(),
                'green_coffee_lot_id' => $request->green_coffee_lot_id,
                'sensory_profile_id' => $request->sensory_profile_id,
                'machine_id' => $request->machine_id,
                'mestre_user_id' => $request->mestre_user_id,
                'reference' => 'TR-' . strtoupper(uniqid()),
                'total_weight_requested' => $request->total_weight,
                'partial_weight' => $request->partial_weight,
                'roast_level' => $request->roast_level,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            $total = $request->total_weight;
            $partial = $request->partial_weight;
            $count = floor($total / $partial);
            $remainder = fmod($total, $partial);

            for ($i = 1; $i <= $count; $i++) {
                $demand->batches()->create([
                    'batch_number' => $i,
                    'weight_in' => $partial,
                    'mestre_user_id' => $request->mestre_user_id,
                    'status' => 'pending'
                ]);
            }

            if ($remainder > 0) {
                $demand->batches()->create([
                    'batch_number' => $count + 1,
                    'weight_in' => $remainder,
                    'mestre_user_id' => $request->mestre_user_id,
                    'status' => 'pending'
                ]);
            }

            $lot->decrement('total_weight', $total);

            return redirect()->route('roasting.index')->with('success', 'Demanda e ciclos gerados!');
        });
    }
}