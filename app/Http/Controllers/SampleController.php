<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sample;
use App\Models\Company;
use App\Models\Producer;
use App\Models\Farm;

class SampleController extends Controller
{
    /**
     * Lista as amostras cadastradas.
     * Corrigido para retornar a view de amostras e não a de fornecedores.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Busca as amostras filtrando por nome, variedade ou lote do fornecedor
        $samples = Sample::with('origin')
            ->where('is_active', true)
            ->when($search, function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('variety', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_batch', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('registries.samples.index', compact('samples'));
    }

    /**
     * Inativa uma amostra (Soft Delete manual).
     * Ajustado para a tabela 'samples'.
     */
    public function inactivate($id)
    {
        $sample = Sample::findOrFail($id);
        $sample->is_active = 0;
        $sample->save();

        // Nome da rota atualizado para refletir o grupo 'registries'
        return redirect()->route('registries.samples.index')->with('success', 'Amostra removida com sucesso!');
    }

    /**
     * Exibe detalhes de uma amostra específica.
     */
    public function info($id) {
        $sample = Sample::with(['origin', 'sensoryProfileComponents.profile'])->findOrFail($id);
        return view('registries.samples.partials.info', compact('sample'));
    }

    /**
     * Salva uma nova amostra.
     */
    public function store(Request $request) {
        $request->validate([
            'origin_id' => 'required', 
            'origin_type' => 'required', 
            'name' => 'required',
            'receipt_date' => 'required|date'
        ]);

        Sample::create($request->all());

        // Nome da rota atualizado para refletir o grupo 'registries'
        return redirect()->route('registries.samples.index')->with('success', 'Amostra cadastrada!');
    }

    /**
     * Busca a origem (Produtor, Fazenda ou Empresa) para o autocomplete do cadastro de amostras.
     */
    public function searchOrigin(Request $request) {
        $q = $request->get('q');
        $results = [];

        // Empresas
        $companies = Company::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($companies as $c) { 
            $results[] = ['id' => $c->id, 'type' => 'App\Models\Company', 'name' => $c->name, 'label' => 'Empresa']; 
        }

        // Produtores
        $producers = Producer::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($producers as $p) { 
            $results[] = ['id' => $p->id, 'type' => 'App\Models\Producer', 'name' => $p->name, 'label' => 'Produtor']; 
        }

        // Fazendas
        $farms = Farm::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($farms as $f) { 
            $results[] = ['id' => $f->id, 'type' => 'App\Models\Farm', 'name' => $f->name, 'label' => 'Fazenda']; 
        }

        return response()->json($results);
    }

    /**
     * Exclui permanentemente uma amostra.
     */
    public function destroy($id) {
        Sample::findOrFail($id)->delete();
        
        // Nome da rota atualizado para refletir o grupo 'registries'
        return redirect()->route('registries.samples.index')->with('success', 'Amostra excluída permanentemente!');
    }
}