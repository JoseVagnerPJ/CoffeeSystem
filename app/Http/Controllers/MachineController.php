<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\MachineMaintenance;

class MachineController extends Controller
{
    /**
     * Lista todas as máquinas ativas e seus históricos de manutenção.
     */
    public function index()
    {
        // Carregamos as máquinas ativas ordenadas por nome
        // O "with" traz as manutenções ordenadas pela data mais recente (Timeline)
        $machines = Machine::with(['maintenances' => function ($query) {
            $query->orderBy('maintenance_date', 'desc');
        }])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

        // CAMINHO ATUALIZADO PARA A NOVA ESTRUTURA DE PASTAS
        return view('registries.machines.index', compact('machines'));
    }

    /**
     * Salva o cadastro de uma nova máquina.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'max_capacity' => 'required|numeric',
            'min_capacity' => 'required|numeric',
            'cleaning_threshold' => 'required|integer',
        ]);

        Machine::create([
            'name'               => $request->name,
            'brand'              => $request->brand,
            'model'              => $request->model,
            'year'               => $request->year,
            'serial_number'      => $request->serial_number,
            'max_capacity'       => $request->max_capacity,
            'min_capacity'       => $request->min_capacity,
            'cleaning_threshold' => $request->cleaning_threshold,
            'current_roasts'     => 0, // Inicia zerada
            'is_active'          => true,
        ]);

        return redirect()->back()->with('success', 'Máquina cadastrada com sucesso!');
    }

    /**
     * Inativa a máquina (Exclusão Lógica).
     * Mantemos os dados no banco para rastreabilidade de torras antigas.
     */
    public function inactivate($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Máquina inativada do sistema.');
    }

    /**
     * Registra uma nova manutenção técnica no histórico da máquina.
     */
    public function storeMaintenance(Request $request, $id)
    {
        $request->validate([
            'maintenance_date' => 'required|date',
            'provider'         => 'required|string|max:255',
            'description'      => 'required|string',
        ]);

        MachineMaintenance::create([
            'machine_id'             => $id,
            'maintenance_date'       => $request->maintenance_date,
            'provider'               => $request->provider,
            'description'            => $request->description,
            'next_maintenance_date'  => $request->next_maintenance_date,
        ]);

        return redirect()->back()->with('success', 'Manutenção técnica registrada no histórico!');
    }
}