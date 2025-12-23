<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GreenCoffeeLot;
use Illuminate\Support\Facades\Auth;

class GreenCoffeeLotController extends Controller
{
    /**
     * Exibe a listagem do estoque verde.
     */
    public function index()
    {
        // Puxa os lotes ordenados pelo mais recente
        $lotes = GreenCoffeeLot::orderBy('created_at', 'desc')->get();
        
        return view('estoque', compact('lotes'));
    }

    /**
     * Salva um novo lote no banco de dados.
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados
        $validated = $request->validate([
            'reference'     => 'required|string|max:255',
            'origin'        => 'required|string|max:255',
            'process'       => 'required|string',
            'address_row'   => 'required|string|max:1',
            'address_pos'   => 'required|integer',
            'quantity_bags' => 'required|integer',
            'unit_weight'   => 'required|numeric',
            'total_weight'  => 'required|numeric',
        ]);

        // 2. Criação do registro vinculado ao usuário logado
        $lote = new GreenCoffeeLot();
        $lote->user_id       = Auth::id();
        $lote->reference     = $request->reference;
        $lote->origin        = $request->origin;
        $lote->process       = $request->process;
        $lote->address_row   = strtoupper($request->address_row); // Garante letra maiúscula
        $lote->address_pos   = $request->address_pos;
        $lote->quantity_bags = $request->quantity_bags;
        $lote->unit_weight   = $request->unit_weight;
        $lote->total_weight  = $request->total_weight;
        $lote->save();

        return redirect()->route('estoque')->with('success', 'Lote cadastrado com sucesso!');
    }

    /**
     * "Captura" um lote na sessão para uso posterior.
     */
    public function capture($id)
    {
        $lote = GreenCoffeeLot::findOrFail($id);
        
        // Armazena na sessão do navegador
        session([
            'captured_lot' => $lote->id,
            'captured_lot_name' => $lote->reference
        ]);

        return redirect()->back()->with('success', 'Lote ' . $lote->reference . ' capturado!');
    }

    /**
     * Limpa o lote capturado da sessão.
     */
    public function clearCapture()
    {
        session()->forget(['captured_lot', 'captured_lot_name']);
        
        return redirect()->back()->with('info', 'Lote solto com sucesso.');
    }
}