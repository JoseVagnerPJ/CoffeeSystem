<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cup;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Sample;
use App\Models\Tag; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CuppingController extends Controller
{
    public function index()
{
    // Usamos select('*') dentro do eager loading para não haver dúvida
    return response()->json(
        CuppingSession::withCount('users as users_count')
            ->withCount('items') 
            ->with([
                'items' => function($query) {
                    $query->select('*'); // Força carregar notes, nickname, etc.
                },
                'items.sample', 
                'items.evaluations.user'
            ])
            ->orderBy('id', 'desc') 
            ->get()
    );
}

    public function show($id)
    {
        $session = CuppingSession::with(['items.sample', 'items.evaluations.user', 'items.evaluations.tags'])
            ->withCount('items')
            ->findOrFail($id);
            
        return response()->json($session);
    }

    /**
     * Inicia uma nova mesa. Registra o momento exato de início (Fuso SP).
     */
    public function startSession(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.position' => 'required|integer',
            'items.*.is_avulso' => 'required|boolean',
            'items.*.sample_id' => 'required_if:items.*.is_avulso,false|nullable|exists:samples,id',
            'items.*.nickname' => 'nullable|string',
            'items.*.variety' => 'nullable|string',
            'items.*.drying_process' => 'nullable|string',
            'items.*.fermentation' => 'nullable|string',
            'items.*.humidity' => 'nullable|string',
            'items.*.screen' => 'nullable|string',
            'items.*.altitude' => 'nullable|string',
            'items.*.crop' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
            'items.*.origin_id' => 'nullable|integer',
            'items.*.origin_type' => 'nullable|string',
        ]);

        $session = DB::transaction(function () use ($data, $request) {
            $agoraSp = Carbon::now('America/Sao_Paulo');
            $user = $request->user();

            $session = CuppingSession::create([
                'main_company_id' => $user->main_company_id, // Injeção manual de segurança
                'title' => $data['title'],
                'session_date' => $agoraSp->toDateString(),
                'started_at' => $agoraSp,
                'created_by' => $user->id,
                'status' => 'aberto',
                'notes' => $data['notes'] ?? null,
            ]);

            // Registro de vínculo do usuário na mesa
            DB::table('cupping_session_user')->insert([
                'cupping_session_id' => $session->id,
                'user_id' => $user->id,
                'has_finished' => false,
                'created_at' => $agoraSp,
                'updated_at' => $agoraSp,
            ]);

            foreach ($data['items'] as $itemData) {
                $attributes = [
                    'main_company_id' => $user->main_company_id, // Itens também precisam do ID da empresa
                    'position' => $itemData['position'],
                    'is_avulso' => $itemData['is_avulso'],
                    'nickname' => $itemData['nickname'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ];

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
                    $attributes = array_merge($attributes, [
                        'variety' => $itemData['variety'] ?? null,
                        'drying_process' => $itemData['drying_process'] ?? null,
                        'fermentation' => $itemData['fermentation'] ?? null,
                        'humidity' => $itemData['humidity'] ?? null,
                        'screen' => $itemData['screen'] ?? null,
                        'altitude' => $itemData['altitude'] ?? null,
                        'crop' => $itemData['crop'] ?? null,
                        'origin_id' => $itemData['origin_id'] ?? null,
                        'origin_type' => $itemData['origin_type'] ?? null,
                    ]);
                }

                $session->items()->create($attributes);
            }

            return $session;
        });

        return response()->json($session->load('items.sample'), 201);
    }

    /**
     * Registra entrada do usuário na mesa.
     */
    public function joinSession(Request $request, $id)
    {
        $session = CuppingSession::findOrFail($id);
        
        if ($session->status === 'aberto') {
            DB::table('cupping_session_user')->updateOrInsert(
                ['cupping_session_id' => $id, 'user_id' => $request->user()->id],
                ['has_finished' => false, 'updated_at' => Carbon::now('America/Sao_Paulo')]
            );
        }
        
        return response()->json(['message' => 'Usuário entrou na mesa.']);
    }

    /**
     * Salva a avaliação e encerra a mesa se todos concluíram.
     */
    public function finishEvaluations(Request $request)
    {
        try {
            $data = $request->validate([
                'evaluations' => 'required|array',
                'evaluations.*.cupping_item_id' => 'required|exists:cupping_items,id',
                'evaluations.*.acidity' => 'required|numeric|min:0|max:10',
                'evaluations.*.body' => 'required|numeric|min:0|max:10',
                'evaluations.*.sweetness' => 'required|numeric|min:0|max:10',
                'evaluations.*.aftertaste' => 'required|numeric|min:0|max:10',
                'evaluations.*.observations' => 'nullable|string',
                'evaluations.*.tags' => 'nullable|string',
            ]);

            DB::transaction(function () use ($data, $request) {
                $agoraSp = Carbon::now('America/Sao_Paulo');
                $user = $request->user();

                foreach ($data['evaluations'] as $eval) {
                    // 1. Criamos a avaliação vinculada à empresa
                    $cup = Cup::create([
                        'main_company_id' => $user->main_company_id,
                        'cupping_item_id' => $eval['cupping_item_id'],
                        'user_id'         => $user->id,
                        'acidity'         => $eval['acidity'],
                        'body'            => $eval['body'],
                        'sweetness'       => $eval['sweetness'],
                        'aftertaste'      => $eval['aftertaste'],
                        'observations'    => $eval['observations'] ?? null,
                    ]);

                    // 2. Lógica de Tags (Notas Sensoriais)
                    if (!empty($eval['tags'])) {
                        $tagNames = explode(',', $eval['tags']);
                        $tagIds = [];

                        foreach ($tagNames as $name) {
                            $trimmedName = trim(mb_strtolower($name));
                            if ($trimmedName !== '') {
                                // Busca segura considerando a empresa atual
                                $tag = Tag::where('name', $trimmedName)
                                          ->where('main_company_id', $user->main_company_id)
                                          ->first();

                                if (!$tag) {
                                    $tag = Tag::create([
                                        'name' => $trimmedName,
                                        'main_company_id' => $user->main_company_id
                                    ]);
                                }
                                $tagIds[] = $tag->id;
                            }
                        }

                        if (!empty($tagIds)) {
                            // CORREÇÃO CRÍTICA: Injetando main_company_id na tabela pivô cup_tag
                            $syncData = [];
                            foreach ($tagIds as $id) {
                                $syncData[$id] = ['main_company_id' => $user->main_company_id];
                            }
                            $cup->tags()->sync($syncData);
                        }
                    }
                }

                // 3. Identifica a sessão e marca conclusão do usuário
                $firstItemId = $data['evaluations'][0]['cupping_item_id'];
                $sessionId = DB::table('cupping_items')->where('id', $firstItemId)->value('cupping_session_id');

                DB::table('cupping_session_user')
                    ->where('cupping_session_id', $sessionId)
                    ->where('user_id', $user->id)
                    ->update(['has_finished' => true, 'updated_at' => $agoraSp]);

                // Lógica de fechamento automático da mesa
                $participantes = DB::table('cupping_session_user')->where('cupping_session_id', $sessionId)->count();
                $finalizados = DB::table('cupping_session_user')->where('cupping_session_id', $sessionId)->where('has_finished', true)->count();

                if ($participantes > 0 && $participantes === $finalizados) {
                    CuppingSession::where('id', $sessionId)->update([
                        'status' => 'finalizado',
                        'finished_at' => $agoraSp
                    ]);
                }
            });

            return response()->json(['message' => 'Avaliações salvas com sucesso!'], 201);
            
        } catch (\Exception $e) {
            Log::error('Erro ao finalizar prova no Q-App: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno ao processar avaliações.', 'error' => $e->getMessage()], 500);
        }
    }

    public function closeSession($id)
    {
        $session = CuppingSession::findOrFail($id);
        
        // Verifica se a sessão pertence à empresa do usuário
        if ($session->main_company_id !== auth()->user()->main_company_id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $session->update([
            'status' => 'finalizado',
            'finished_at' => Carbon::now('America/Sao_Paulo')
        ]);

        return response()->json(['message' => 'Mesa finalizada com sucesso!']);
    }
}