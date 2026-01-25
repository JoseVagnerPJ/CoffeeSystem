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
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $samples = Sample::with('origin')
            ->where('is_active', true)
            ->when($search, function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('variety', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_batch', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            // Ajustado para usar o formatador consistente para o App
            return response()->json($this->formatSamplesForApi($samples));
        }

        return view('registries.samples.index', compact('samples'));
    }

    /**
     * NOVO: Busca amostras para o Autocomplete.
     * Ajustado para carregar origem e formatar JSON de forma consistente.
     */
    public function search(Request $request)
    {
        $q = $request->get('search') ?? $request->get('q');
        
        if (strlen($q) < 2) return response()->json([]);

        // Carregamos o relacionamento origin (morphTo) para pegar o nome da Fazenda/Produtor
        $samples = Sample::with('origin') 
            ->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('variety', 'LIKE', "%{$q}%")
                      ->orWhere('supplier_batch', 'LIKE', "%{$q}%");
            })
            ->limit(10)
            ->get();

        // Utiliza o método auxiliar para manter a consistência de dados com o Flutter
        return response()->json($this->formatSamplesForApi($samples));
    }

    /**
     * Função auxiliar para garantir que o JSON entregue ao App seja sempre igual.
     * Mapeia as colunas do banco para as propriedades esperadas pelo SampleModel no Flutter.
     */
    private function formatSamplesForApi($samples) {
        return $samples->map(function($s) {
            return [
                'id'             => $s->id,
                'name'           => $s->name,
                'variety'        => $s->variety,
                'supplier_batch' => $s->supplier_batch, // ID Lote Fornecedor
                'drying_process' => $s->drying_process,
                'fermentation'   => $s->fermentation,
                'humidity'       => $s->humidity,
                'screen'         => $s->screen,
                'altitude'       => $s->altitude,
                'crop'           => $s->crop,
                // Chave origin_name processada para facilitar a leitura no mobile
                'origin_name'    => $s->origin ? $s->origin->name : 'Não informado' 
            ];
        });
    }

    /**
     * Inativa uma amostra (Soft Delete manual).
     */
    public function inactivate($id)
    {
        $sample = Sample::findOrFail($id);
        $sample->is_active = 0;
        $sample->save();

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

        return redirect()->route('registries.samples.index')->with('success', 'Amostra cadastrada!');
    }

    /**
     * Busca a origem (Produtor, Fazenda ou Empresa) para o autocomplete.
     */
    public function searchOrigin(Request $request) {
        $q = $request->get('q');
        $results = [];

        $companies = Company::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($companies as $c) { 
            $results[] = ['id' => $c->id, 'type' => 'App\Models\Company', 'name' => $c->name, 'label' => 'Empresa']; 
        }

        $producers = Producer::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($producers as $p) { 
            $results[] = ['id' => $p->id, 'type' => 'App\Models\Producer', 'name' => $p->name, 'label' => 'Produtor']; 
        }

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
        return redirect()->route('registries.samples.index')->with('success', 'Amostra excluída permanentemente!');
    }
}