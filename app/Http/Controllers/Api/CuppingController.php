<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cup;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuppingController extends Controller
{
    /**
     * Lista todas as mesas de cupping abertas.
     */
    public function index()
    {
        return response()->json(
            CuppingSession::where('status', 'aberto')
                ->withCount('items') 
                ->with(['items.sample', 'items.evaluations.user'])
                ->orderBy('session_date', 'desc')
                ->get()
        );
    }

    /**
     * Inicia uma nova mesa. Suporta amostras vinculadas ou avulsas.
     */
    public function startSession(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.position' => 'required|integer',
            'items.*.is_avulso' => 'required|boolean',
            // Se não for avulso, o sample_id é obrigatório
            'items.*.sample_id' => 'required_if:items.*.is_avulso,false|nullable|exists:samples,id',
            // Campos técnicos (opcionais no request, pois podem vir da amostra)
            'items.*.variety' => 'nullable|string',
            'items.*.altitude' => 'nullable|integer',
            'items.*.origin_id' => 'nullable|integer',
            'items.*.origin_type' => 'nullable|string',
        ]);

        $session = DB::transaction(function () use ($data, $request) {
            $session = CuppingSession::create([
                'title' => $data['title'],
                'session_date' => now(),
                'created_by' => $request->user()->id,
                'status' => 'aberto',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                $attributes = [
                    'position' => $itemData['position'],
                    'is_avulso' => $itemData['is_avulso'],
                    'notes' => $itemData['notes'] ?? null,
                ];

                // LÓGICA DE SNAPSHOT: Se houver amostra, puxa os dados dela automaticamente
                if (!$itemData['is_avulso']) {
                    $sample = Sample::find($itemData['sample_id']);
                    $attributes = array_merge($attributes, [
                        'sample_id' => $sample->id,
                        'variety' => $sample->variety,
                        'drying_process' => $sample->drying_process,
                        'fermentation' => $sample->fermentation,
                        'humidity' => $sample->humidity,
                        'screen' => $sample->screen,
                        'altitude' => $sample->altitude,
                        'crop' => $sample->crop,
                        'origin_id' => $sample->origin_id,
                        'origin_type' => $sample->origin_type,
                    ]);
                } else {
                    // Se for AVULSO, salva o que veio do formulário
                    $attributes = array_merge($attributes, [
                        'variety' => $itemData['variety'] ?? null,
                        'altitude' => $itemData['altitude'] ?? null,
                        'origin_id' => $itemData['origin_id'] ?? null,
                        'origin_type' => $itemData['origin_type'] ?? null,
                        // Adicione outros campos técnicos manuais aqui se necessário
                    ]);
                }

                $session->items()->create($attributes);
            }

            return $session;
        });

        return response()->json($session->load('items.sample'), 201);
    }

    /**
     * Salva a avaliação individual de um provador para uma ou mais xícaras.
     */
    public function finishEvaluations(Request $request)
    {
        $data = $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.cupping_item_id' => 'required|exists:cupping_items,id',
            'evaluations.*.acidity' => 'required|numeric|min:0|max:10',
            'evaluations.*.body' => 'required|numeric|min:0|max:10',
            'evaluations.*.sweetness' => 'required|numeric|min:0|max:10',
            'evaluations.*.aftertaste' => 'required|numeric|min:0|max:10',
            'evaluations.*.is_invalid' => 'nullable|boolean',
            'evaluations.*.invalid_reason' => 'nullable|string',
            'evaluations.*.observations' => 'nullable|string',
            'evaluations.*.tags' => 'nullable|array', // IDs das notas sensoriais
        ]);

        DB::transaction(function () use ($data, $request) {
            foreach ($data['evaluations'] as $eval) {
                // O cálculo do final_score (60 + pilares) é feito automaticamente pelo Model Cup no método booted()
                $cup = Cup::create([
                    'cupping_item_id' => $eval['cupping_item_id'],
                    'user_id' => $request->user()->id,
                    'acidity' => $eval['acidity'],
                    'body' => $eval['body'],
                    'sweetness' => $eval['sweetness'],
                    'aftertaste' => $eval['aftertaste'],
                    'is_invalid' => $eval['is_invalid'] ?? false,
                    'invalid_reason' => $eval['invalid_reason'] ?? null,
                    'observations' => $eval['observations'] ?? null,
                ]);

                if (!empty($eval['tags'])) {
                    $cup->tags()->sync($eval['tags']);
                }
            }
        });

        return response()->json(['message' => 'Avaliações salvas com sucesso!'], 201);
    }
}