<?php

namespace App\Http\Controllers;

use App\Models\{RoastDemand, RoastingBatch, GreenCoffeeLot, Machine, User, SensoryProfile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoastController extends Controller
{
    public function index() {
        $machines = Machine::where('is_active', true)->get();
        $demands = RoastDemand::with(['lot', 'machine', 'mestre', 'batches', 'sensoryProfile'])
                    ->where('status', '!=', 'finished')->orderBy('created_at', 'asc')->get();

        return view('roasting.index', compact('machines', 'demands'));
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

    // --- LÓGICA DE ARMAZENAMENTO INTELIGENTE ---
    public function store(Request $request) {
        return DB::transaction(function () use ($request) {
            $lot = GreenCoffeeLot::lockForUpdate()->find($request->green_coffee_lot_id);

            // 1. Criar Demanda Central
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

            // 2. Cálculo de Ciclos e Criação Automática das Parciais (Batches)
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

            // 3. Reserva de Estoque
            $lot->decrement('total_weight', $total);

            return redirect()->route('roasting.index')->with('success', 'Demanda e ciclos gerados!');
        });
    }
}