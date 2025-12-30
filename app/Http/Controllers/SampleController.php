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
     * Lista todas as amostras e prepara os dados para o modal de criação.
     */
    public function index()
    {
        $samples = Sample::with('origin')->latest()->get();
        
        // Padronizado para 'is_active' conforme solicitado
        $companies = Company::where('is_active', 1)->orderBy('name')->get();
        $producers = Producer::where('is_active', 1)->orderBy('name')->get();
        $farms = Farm::where('is_active', 1)->orderBy('name')->get();

        return view('samples.index', compact('samples', 'companies', 'producers', 'farms'));
    }

    /**
     * Retorna a Ficha Técnica da Amostra via AJAX.
     * Agora carrega também os Perfis Sensoriais vinculados.
     */
    public function info($id) 
    {
        // Ponto de alteração: Carregando a origem e os componentes de perfis sensoriais vinculados
        $sample = Sample::with(['origin', 'sensoryProfileComponents.profile'])->findOrFail($id);
        
        return view('samples.partials.info', compact('sample'));
    }

    /**
     * Salva uma nova amostra no banco de dados.
     */
    public function store(Request $request)
    {
        // VALIDAÇÃO: Impede o erro de "Integrity constraint violation"
        $request->validate([
            'origin_id' => 'required',
            'origin_type' => 'required',
            'name' => 'required'
        ]);

        $sample = new Sample();
        $sample->name = $request->name;
        $sample->receipt_date = $request->receipt_date;
        $sample->origin_id = $request->origin_id; 
        $sample->origin_type = $request->origin_type;
        $sample->supplier_batch = $request->supplier_batch;
        $sample->variety = $request->variety;
        $sample->drying_process = $request->drying_process;
        $sample->fermentation = $request->fermentation;
        $sample->humidity = $request->humidity;
        $sample->screen = $request->screen;
        $sample->altitude = $request->altitude;
        $sample->crop = $request->crop;
        $sample->observations = $request->observations;
        $sample->save();

        return redirect()->route('samples.index')->with('success', 'Amostra cadastrada!');
    }

    /**
     * Busca dinâmica para o autocomplete de origens (Empresas, Produtores, Fazendas).
     */
    public function searchOrigin(Request $request)
    {
        $q = $request->get('q');
        $results = [];

        // Filtra Empresas ativas
        $companies = Company::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($companies as $c) {
            $results[] = ['id' => $c->id, 'type' => 'App\Models\Company', 'name' => $c->name, 'label' => 'Empresa'];
        }

        // Filtra Produtores ativos
        $producers = Producer::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($producers as $p) {
            $results[] = ['id' => $p->id, 'type' => 'App\Models\Producer', 'name' => $p->name, 'label' => 'Produtor'];
        }

        // Filtra Fazendas ativas
        $farms = Farm::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($farms as $f) {
            $results[] = ['id' => $f->id, 'type' => 'App\Models\Farm', 'name' => $f->name, 'label' => 'Fazenda'];
        }

        return response()->json($results);
    }

    /**
     * Exclui uma amostra.
     */
    public function destroy($id)
    {
        $sample = Sample::findOrFail($id);
        $sample->delete();
        
        return redirect()->route('samples.index')->with('success', 'Amostra excluída!');
    }
}