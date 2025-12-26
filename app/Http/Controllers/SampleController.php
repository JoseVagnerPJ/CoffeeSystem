<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sample;    // IMPORTANTE: Corrige o erro de "Class not found"
use App\Models\Company;   // Sua tabela de empresas
use App\Models\Producer;  // Sua tabela de produtores
use App\Models\Farm;      // Sua tabela de fazendas


class SampleController extends Controller
{
    public function index()
{
    $samples = Sample::with('origin')->latest()->get();
    
    // Substitua 'ativo' pelo nome real da sua coluna (ex: 'active', 'situacao', etc)
    $companies = Company::where('is_active', 1)->orderBy('name')->get();
    $producers = Producer::where('is_active', 1)->orderBy('name')->get();
    $farms = Farm::where('is_active', 1)->orderBy('name')->get();

    return view('samples.index', compact('samples', 'companies', 'producers', 'farms'));
}

    public function store(Request $request)
    {
        // O valor vem como "1|App\Models\Farm" do select
        $originData = explode('|', $request->origin_combined);

        $sample = new Sample($request->except('origin_combined'));
        $sample->origin_id = $originData[0];
        $sample->origin_type = $originData[1];
        $sample->save();

        return redirect()->back()->with('success', 'Amostra cadastrada com sucesso!');
    }

public function info($id)
{
    $sample = Sample::with('origin')->findOrFail($id);
    
    // Certifique-se de que o caminho 'samples.partials.info' está correto
    return view('samples.partials.info', compact('sample'));
}

public function destroy($id)
{
    $sample = Sample::findOrFail($id);
    $sample->delete();
    return redirect()->route('samples.index')->with('success', 'Amostra excluída!');
}
}