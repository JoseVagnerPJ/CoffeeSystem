<?php

namespace App\Http\Controllers;

use App\Models\RoastDemand;
use App\Models\RoastingBatch;
use App\Models\GreenCoffeeLot;
use App\Models\Machine;
use App\Models\User;
use App\Models\SensoryProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoastController extends Controller
{
    /**
     * Tela Principal (Kanban)
     */
    public function index() {
        $machines = Machine::where('is_active', true)->get();

        $demands = RoastDemand::with(['lot.locations', 'machine', 'mestre', 'batches', 'sensoryProfile'])
                    ->orderBy('position', 'asc')
                    ->get();

        return view('roasting.index', compact('machines', 'demands'));
    }

    /**
     * Retorna estatísticas filtradas por Máquina e Período (D/S/M)
     */
    public function getStats(Request $request) {
        $machineId = $request->machine_id;
        $period = $request->period; 

        if ($period == 'D') {
            $startDate = Carbon::now('America/Sao_Paulo')->startOfDay();
        } elseif ($period == 'S') {
            $startDate = Carbon::now('America/Sao_Paulo')->startOfWeek();
        } else {
            $startDate = Carbon::now('America/Sao_Paulo')->startOfMonth();
        }

        $globalWeight = RoastDemand::where('status', 'finished')
            ->where('updated_at', '>=', $startDate)
            ->sum('total_weight_requested');

        $machineWeight = RoastDemand::where('machine_id', $machineId)
            ->where('status', 'finished')
            ->where('updated_at', '>=', $startDate)
            ->sum('total_weight_requested');

        $chartData = $this->buildChartData($machineId, $period, $startDate);

        return response()->json([
            'global_weight' => number_format($globalWeight, 2, ',', '.'),
            'machine_weight' => number_format($machineWeight, 2, ',', '.'),
            'chart' => $chartData
        ]);
    }

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
            $date = Carbon::now('America/Sao_Paulo')->subDays($i)->format('Y-m-d');
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

    public function store(Request $request) {
        return DB::transaction(function () use ($request) {
            $lot = GreenCoffeeLot::lockForUpdate()->find($request->green_coffee_lot_id);

            $maxPosition = RoastDemand::where('machine_id', $request->machine_id)
                                      ->where('status', 'pending')
                                      ->max('position') ?? 0;

            $demand = RoastDemand::create([
                'user_id'                => auth()->id(),
                'green_coffee_lot_id'    => $request->green_coffee_lot_id,
                'sensory_profile_id'     => $request->sensory_profile_id,
                'machine_id'             => $request->machine_id,
                'mestre_user_id'         => $request->mestre_user_id,
                'reference'              => 'TR-' . strtoupper(uniqid()),
                'total_weight_requested' => $request->total_weight,
                'partial_weight'         => $request->partial_weight,
                'roast_level'            => $request->roast_level,
                'notes'                  => $request->notes,
                'status'                 => 'pending',
                'position'               => $maxPosition + 1
            ]);

            $total = $request->total_weight;
            $partial = $request->partial_weight;
            $count = floor($total / $partial);
            $remainder = fmod($total, $partial);

            for ($i = 1; $i <= $count; $i++) {
                $demand->batches()->create([
                    'batch_number'   => $i,
                    'weight_in'      => $partial,
                    'mestre_user_id' => $request->mestre_user_id,
                    'status'         => 'pending'
                ]);
            }

            if ($remainder > 0) {
                $demand->batches()->create([
                    'batch_number'   => $count + 1,
                    'weight_in'      => $remainder,
                    'mestre_user_id' => $request->mestre_user_id,
                    'status'         => 'pending'
                ]);
            }

            $lot->decrement('total_weight', $total);

            return redirect()->route('roasting.index')->with('success', 'Demanda enviada para o final da fila!');
        });
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        DB::transaction(function () use ($order) {
            foreach ($order as $item) {
                RoastDemand::where('id', $item['id'])
                    ->update(['position' => $item['position']]);
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Ordem atualizada!']);
    }

    public function apiQueue() {
        try {
            $user = auth()->user();
            $machines = Machine::where('is_active', true)->get();

            $demands = RoastDemand::with(['lot.locations', 'sensoryProfile', 'batches', 'machine'])
                ->where('mestre_user_id', $user->id)
                ->where(function($query) {
                    $query->where('status', '!=', 'finished')
                          ->orWhere('status', 'finished');
                })
                ->orderBy('position', 'asc') 
                ->get();

            return response()->json([
                'mestre_name' => $user->name,
                'machines' => $machines,
                'demands' => $demands
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

public function apiStartBatch($id) {
        $batch = RoastingBatch::findOrFail($id);
        
        $batch->update([
            'status' => 'roasting',
            'started_at' => Carbon::now('America/Sao_Paulo'), // Força Brasília
        ]);

        $batch->demand->update(['status' => 'roasting']);

        return response()->json([
            'success' => true,
            'started_at' => $batch->started_at->timestamp // Enviamos o timestamp puro para o JS
        ]);
    }

    public function apiFinishBatch(Request $request, $id) {
        try {
            $batch = RoastingBatch::findOrFail($id);

            // Converte o horário que vem do App para o fuso correto
            $startTime = $request->start_time ? Carbon::parse($request->start_time)->timezone('America/Sao_Paulo') : $batch->started_at;

            $batch->update([
                'status'          => 'finished',
                'weight_out'      => (float) str_replace(',', '.', $request->weight_out),
                'notes'           => $request->notes ?? '',
                'is_special_tour' => filter_var($request->is_special, FILTER_VALIDATE_BOOLEAN),
                'started_at'      => $startTime, 
                'finished_at'     => Carbon::now('America/Sao_Paulo'),
            ]);

            if ($batch->roast_demand_id) {
                $demand = RoastDemand::with('batches')->find($batch->roast_demand_id);
                if ($demand && $demand->machine_id) {
                    DB::table('machines')->where('id', $demand->machine_id)->increment('current_roasts');
                }
                $isComplete = $demand->batches->every(function($b) {
                    return in_array($b->status, ['finished', 'discarded']) && ($b->status !== 'finished' || $b->weight_out > 0);
                });
                if ($isComplete) {
                    $demand->update(['status' => 'finished']);
                }
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function apiUpdateBatch(Request $request, $id) {
        $batch = RoastingBatch::findOrFail($id);
        
        $batch->update([
            'is_special_tour' => filter_var($request->is_special, FILTER_VALIDATE_BOOLEAN),
            'notes' => $request->notes ?? $batch->notes
        ]);

        return response()->json(['success' => true]);
    }

    public function apiDiscardBatch($id) {
        $batch = RoastingBatch::findOrFail($id);
        $batch->update(['status' => 'discarded']);

        return response()->json(['success' => true, 'message' => 'Torra inutilizada']);
    }

    public function webRequestSplit(Request $request) {
        $demand = RoastDemand::findOrFail($request->demand_id);
        $demand->update(['status' => 'awaiting_split_confirmation']);

        return response()->json(['success' => true, 'message' => 'Solicitação enviada ao mestre de torra.']);
    }

    public function apiConfirmSplit(Request $request, $id) {
        return DB::transaction(function () use ($request, $id) {
            $originalDemand = RoastDemand::with('batches')->findOrFail($id);
            $keepCount = $request->num_remaining; 

            $pendingBatches = $originalDemand->batches()
                ->where('status', 'pending')
                ->orderBy('batch_number', 'asc')
                ->get();

            $batchesToMove = $pendingBatches->slice($keepCount);

            if ($batchesToMove->isNotEmpty()) {
                $movedWeight = $batchesToMove->sum('weight_in');

                $newDemand = RoastDemand::create([
                    'user_id' => $originalDemand->user_id,
                    'green_coffee_lot_id' => $originalDemand->green_coffee_lot_id,
                    'sensory_profile_id' => $originalDemand->sensory_profile_id,
                    'machine_id' => $originalDemand->machine_id,
                    'mestre_user_id' => $originalDemand->mestre_user_id,
                    'reference' => $originalDemand->reference . '-PART2',
                    'total_weight_requested' => $movedWeight,
                    'partial_weight' => $originalDemand->partial_weight,
                    'roast_level' => $originalDemand->roast_level,
                    'notes' => '(Dividido) ' . $originalDemand->notes,
                    'status' => 'pending',
                    'position' => $originalDemand->position + 1
                ]);

                foreach ($batchesToMove as $batch) {
                    $batch->update(['roast_demand_id' => $newDemand->id]);
                }

                $originalDemand->decrement('total_weight_requested', $movedWeight);
            }

            $originalDemand->update(['status' => 'roasting']);

            return response()->json(['success' => true, 'message' => 'Demanda reorganizada com sucesso.']);
        });
    }

    public function apiRegisterCleaning(Request $request, $id)
{
    try {
        return DB::transaction(function () use ($id, $request) {
            $machine = Machine::findOrFail($id);
            $user = auth()->user();

            // 1. Cria o log de limpeza
            $machine->cleaningLogs()->create([
                'user_id' => $user->id,
                'cleaned_at' => Carbon::now('America/Sao_Paulo'),
                'notes' => $request->notes ?? 'Limpeza de rotina realizada pelo App',
            ]);

            // 2. Reseta o contador de torras da máquina
            $machine->update([
                'current_roasts' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Limpeza registrada e contador resetado!',
                'current_roasts' => 0
            ]);
        });
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
}