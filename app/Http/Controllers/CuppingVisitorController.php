<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuppingSession;
use App\Models\CuppingVisitor;
use App\Models\CuppingItem;
use App\Models\Cup;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CuppingVisitorController extends Controller
{
    /**
     * Tela de boas-vindas: Solicita Nome e CPF.
     */
    public function landing($uuid)
    {
        $session = CuppingSession::where('uuid', $uuid)->firstOrFail();

        if ($session->status === 'finalizado') {
            return view('visitor.closed', compact('session'));
        }

        return view('visitor.landing', compact('session'));
    }

    /**
     * Registra o visitante na sessão e o identifica via Session do navegador.
     */
    public function join(Request $request, $uuid)
    {
        $session = CuppingSession::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|min:11',
        ]);

        // Cria ou recupera o visitante para esta mesa específica
        $visitor = CuppingVisitor::firstOrCreate(
            [
                'cupping_session_id' => $session->id,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf)
            ],
            ['name' => $request->name]
        );

        // Armazena a identificação na sessão do navegador
        Session::put('visitor_id', $visitor->id);
        Session::put('visitor_name', $visitor->name);
        Session::put('visitor_cpf', $visitor->cpf);

        return redirect()->route('visitor.evaluation', $uuid);
    }

    /**
     * Exibe a interface de prova (O clone do App Flutter).
     */
    public function evaluation($uuid)
    {
        $session = CuppingSession::with(['items.sample.origin', 'profile'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Verifica se o visitante passou pela landing page
        if (!Session::has('visitor_id')) {
            return redirect()->route('visitor.landing', $uuid);
        }

        // Carrega as tags para o autocomplete do site
        $availableTags = Tag::where('main_company_id', $session->main_company_id)
            ->pluck('name');

        return view('visitor.evaluation', compact('session', 'availableTags'));
    }

    /**
     * Processa o envio das notas (SCA).
     */
    public function submit(Request $request, $uuid)
    {
        $session = CuppingSession::where('uuid', $uuid)->firstOrFail();
        
        if (!Session::has('visitor_id')) {
            return response()->json(['error' => 'Sessão expirada'], 403);
        }

        try {
            DB::transaction(function () use ($request, $session) {
                foreach ($request->evaluations as $eval) {
                    
                    $cup = Cup::updateOrCreate(
                        [
                            'cupping_item_id' => $eval['cupping_item_id'],
                            'cupping_visitor_id' => Session::get('visitor_id'),
                        ],
                        [
                            'main_company_id'   => $session->main_company_id,
                            'visitor_name'      => Session::get('visitor_name'),
                            'visitor_cpf'       => Session::get('visitor_cpf'),
                            
                            // Atributos SCA
                            'fragrance_aroma'   => $eval['fragrance_aroma'] ?? 6,
                            'flavor'            => $eval['flavor'] ?? 6,
                            'aftertaste'        => $eval['aftertaste'] ?? 6,
                            'acidity'           => $eval['acidity'] ?? 6,
                            'body'              => $eval['body'] ?? 6,
                            'balance'           => $eval['balance'] ?? 6,
                            'uniformity'        => $eval['uniformity'] ?? 10,
                            'clean_cup'         => $eval['clean_cup'] ?? 10,
                            'sweetness'         => $eval['sweetness'] ?? 10,
                            'overall'           => $eval['overall'] ?? 6,
                            
                            // Intensidades
                            'roast_level'       => $eval['roast_level'] ?? 1,
                            'fragrance_dry'     => $eval['fragrance_dry'] ?? 0,
                            'fragrance_break'   => $eval['fragrance_break'] ?? 0,
                            'acidity_intensity' => $eval['acidity_intensity'] ?? 0,
                            'body_level'        => $eval['body_level'] ?? 0,
                            
                            // Defeitos
                            'defect_cups'       => $eval['defects_cups'] ?? 0,
                            'defect_intensity'  => $eval['defects_intensity'] ?? 0,
                            
                            'observations'      => $eval['observations'] ?? null,
                        ]
                    );

                    // Sincronização de Tags (Notas Sensoriais)
                    if (!empty($eval['tags'])) {
                        $tagNames = array_map('trim', explode(',', $eval['tags']));
                        $tagIds = [];
                        foreach ($tagNames as $name) {
                            $tag = Tag::firstOrCreate(
                                ['name' => $name, 'main_company_id' => $session->main_company_id]
                            );
                            $tagIds[$tag->id] = ['main_company_id' => $session->main_company_id];
                        }
                        $cup->tags()->sync($tagIds);
                    }
                }
            });

            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Tela de sucesso após finalizar a prova.
     */
    public function success($uuid)
    {
        // Limpa a sessão do visitante para permitir novas entradas ou segurança
        Session::forget(['visitor_id', 'visitor_name', 'visitor_cpf']);
        return view('visitor.success');
    }
}