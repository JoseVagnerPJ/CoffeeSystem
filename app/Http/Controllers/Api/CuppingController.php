<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cup;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuppingController extends Controller
{
    /**
     * Lista todas as mesas de cupping abertas com a contagem de amostras.
     */
    public function index()
    {
        return response()->json(
            CuppingSession::where('status', 'aberto')
                ->withCount('items') // <--- ISSO GERA O items_count PARA O FLUTTER
                ->with(['items.sample'])
                ->orderBy('session_date', 'desc')
                ->get()
        );
    }

    /**
     * Inicia uma nova mesa (sessão) de cupping.
     */
    public function startSession(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.sample_id' => 'required|exists:samples,id',
            'items.*.position' => 'required|integer',
        ]);

        $session = DB::transaction(function () use ($data, $request) {
            $session = CuppingSession::create([
                'title' => $data['title'],
                'session_date' => now(),
                'created_by' => $request->user()->id,
                'status' => 'aberto'
            ]);

            foreach ($data['items'] as $item) {
                $session->items()->create([
                    'sample_id' => $item['sample_id'],
                    'position' => $item['position']
                ]);
            }

            return $session;
        });

        return response()->json($session->load('items.sample'), 201);
    }

    /**
     * Recebe o JSON final do App e salva todas as avaliações do provador.
     */
    public function finishEvaluations(Request $request)
    {
        $data = $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.cupping_item_id' => 'required|exists:cupping_items,id',
            'evaluations.*.acidity' => 'required|numeric',
            'evaluations.*.body' => 'required|numeric',
            'evaluations.*.sweetness' => 'required|numeric',
            'evaluations.*.aftertaste' => 'required|numeric',
        ]);

        DB::transaction(function () use ($data, $request) {
            foreach ($data['evaluations'] as $eval) {
                $score = 60 + $eval['acidity'] + $eval['body'] + $eval['sweetness'] + $eval['aftertaste'];

                $cup = Cup::create([
                    'cupping_item_id' => $eval['cupping_item_id'],
                    'user_id' => $request->user()->id,
                    'acidity' => $eval['acidity'],
                    'body' => $eval['body'],
                    'sweetness' => $eval['sweetness'],
                    'aftertaste' => $eval['aftertaste'],
                    'final_score' => $score,
                    'is_invalid' => $eval['is_invalid'] ?? false,
                    'invalid_reason' => $eval['invalid_reason'] ?? null,
                    'observations' => $eval['observations'] ?? null,
                ]);

                if (isset($eval['tags']) && is_array($eval['tags'])) {
                    $cup->tags()->sync($eval['tags']);
                }
            }
        });

        return response()->json(['message' => 'Avaliações enviadas com sucesso!'], 201);
    }
}