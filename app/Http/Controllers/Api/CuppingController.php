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
    return response()->json(
        CuppingSession::withCount('users as users_count')
            ->withCount('items') 
            ->with([
                'profile', // <--- ADICIONE ESTA LINHA AQUI
                'items.sample.origin', 
                'items.origin',
                'items.evaluations.user',
                'users' => function($q) {
                    $q->select('users.id', 'users.name')->withPivot('has_finished');
                }
            ])
            ->orderBy('id', 'desc') 
            ->get()
    );
}

// No método show($id)
public function show($id)
{
    $session = CuppingSession::with([
            'profile', // <--- ADICIONE ESTA LINHA AQUI TAMBÉM
            'items.sample.origin',
            'items.origin',
            'items.evaluations.user', 
            'items.evaluations.tags'
        ])
        ->withCount([
            'items',
            'users', 
            'users as finished_users_count' => function ($query) {
                $query->where('cupping_session_user.has_finished', true);
            }
        ])
        ->findOrFail($id);
        
    return response()->json($session);
}

public function generatePdf($id, Request $request) {
    // Busca a mesa com todos os relacionamentos necessários
    $mesa = CuppingSession::with(['items.evaluations.user', 'items.evaluations.tags', 'profile'])
            ->findOrFail($id);

    // Filtra para pegar apenas as provas do usuário logado (ou todas, se preferir)
    $userId = $request->user()->id;

    $pdf = \PDF::loadView('pdf.sca_form', compact('mesa', 'userId'))->setPaper('a4', 'landscape');
    return $pdf->download("Mesa_{$id}.pdf");
}

public function getProfiles(Request $request)
{
    // Retorna todos os perfis cadastrados para a empresa do usuário
    $profiles = \App\Models\CuppingProfile::where('main_company_id', $request->user()->main_company_id)
        ->get(['id', 'name']); // Pegamos apenas o essencial para a lista

    return response()->json($profiles);
}

    /**
     * Inicia uma nova mesa. Registra o momento exato de início (Fuso SP).
     */
    public function startSession(Request $request)
{
    // 1. A validação precisa incluir o perfil, senão o Laravel bloqueia o envio do App
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'cupping_profile_id' => 'required|exists:cupping_profiles,id',
        'is_blind' => 'boolean',
        'notes' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.position' => 'required|integer',
        'items.*.is_avulso' => 'required|boolean',
        'items.*.sample_id' => 'nullable|exists:samples,id',
        'items.*.nickname' => 'nullable|string',
        'items.*.variety' => 'nullable|string',
        'items.*.drying_process' => 'nullable|string',
        'items.*.fermentation' => 'nullable|string',
        'items.*.humidity' => 'nullable|string',
        'items.*.screen' => 'nullable|string',
        'items.*.altitude' => 'nullable|string',
        'items.*.crop' => 'nullable|string',
        'items.*.notes' => 'nullable|string',
    ]);

    $session = DB::transaction(function () use ($data, $request) {
        $agoraSp = Carbon::now('America/Sao_Paulo');
        $user = $request->user();

        // 2. Criamos a sessão vinculando o perfil escolhido no App
        $session = CuppingSession::create([
            'main_company_id'    => $user->main_company_id,
            'cupping_profile_id' => $data['cupping_profile_id'], 
            'title'              => $data['title'],
            'session_date'       => $agoraSp->toDateString(),
            'started_at'         => $agoraSp,
            'created_by'         => $user->id,
            'is_blind'           => $data['is_blind'] ?? false,
            'status'             => 'aberto',
            'notes'              => $data['notes'] ?? null,
        ]);

        // Registro automático do criador como participante da mesa
        DB::table('cupping_session_user')->insert([
            'cupping_session_id' => $session->id,
            'user_id' => $user->id,
            'has_finished' => false,
            'created_at' => $agoraSp,
            'updated_at' => $agoraSp,
        ]);

        // 3. Salvando os itens da mesa com os dados técnicos enviados pelo App
        foreach ($data['items'] as $itemData) {
            $session->items()->create([
                'main_company_id' => $user->main_company_id,
                'position'        => $itemData['position'],
                'is_avulso'       => $itemData['is_avulso'],
                'sample_id'       => $itemData['sample_id'] ?? null,
                'nickname'        => $itemData['nickname'] ?? null,
                'variety'         => $itemData['variety'] ?? null,
                'drying_process'  => $itemData['drying_process'] ?? null,
                'fermentation'    => $itemData['fermentation'] ?? null,
                'humidity'        => $itemData['humidity'] ?? null,
                'screen'          => $itemData['screen'] ?? null,
                'altitude'        => $itemData['altitude'] ?? null,
                'crop'            => $itemData['crop'] ?? null,
                'notes'           => $itemData['notes'] ?? null,
            ]);
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
            'evaluations.*.fragrance_aroma' => 'nullable|numeric',
            'evaluations.*.flavor'          => 'nullable|numeric',
            'evaluations.*.aftertaste'      => 'nullable|numeric',
            'evaluations.*.acidity'         => 'nullable|numeric',
            'evaluations.*.body'            => 'nullable|numeric',
            'evaluations.*.balance'         => 'nullable|numeric',
            'evaluations.*.uniformity'      => 'nullable|numeric',
            'evaluations.*.clean_cup'       => 'nullable|numeric',
            'evaluations.*.sweetness'       => 'nullable|numeric',
            'evaluations.*.overall'         => 'nullable|numeric',
            'evaluations.*.roast_level'       => 'nullable|integer',
            'evaluations.*.fragrance_dry'     => 'nullable|numeric',
            'evaluations.*.fragrance_break'   => 'nullable|numeric',
            'evaluations.*.acidity_intensity' => 'nullable|numeric',
            'evaluations.*.body_level'        => 'nullable|numeric',
            'evaluations.*.defects_cups'      => 'nullable|integer', 
            'evaluations.*.defects_intensity' => 'nullable|integer', 
            'evaluations.*.observations'      => 'nullable|string',
            'evaluations.*.tags'              => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $request) {
            $user = $request->user();
            $agoraSp = \Carbon\Carbon::now('America/Sao_Paulo');

            foreach ($data['evaluations'] as $eval) {
                // Usamos updateOrCreate para evitar duplicidade se o usuário enviar duas vezes
                $cup = Cup::updateOrCreate(
                    [
                        'cupping_item_id' => $eval['cupping_item_id'],
                        'user_id'         => $user->id,
                    ],
                    [
                        'main_company_id'   => $user->main_company_id,
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
                        'roast_level'       => $eval['roast_level'] ?? 1,
                        'body_level'        => $eval['body_level'] ?? 0, 
                        'acidity_intensity' => (float)($eval['acidity_intensity'] ?? 0),
                        'fragrance_dry'     => (float)($eval['fragrance_dry'] ?? 0),
                        'fragrance_break'   => (float)($eval['fragrance_break'] ?? 0),
                        'defect_cups'       => $eval['defects_cups'] ?? 0,
                        'defect_intensity'  => $eval['defects_intensity'] ?? 0,
                        'observations'      => $eval['observations'] ?? null,
                    ]
                );

                // Lógica de Tags...
                if (!empty($eval['tags'])) {
                    $tagNames = array_map('trim', explode(',', $eval['tags']));
                    $tagIds = [];
                    foreach ($tagNames as $name) {
                        $tag = Tag::firstOrCreate(
                            ['name' => $name, 'main_company_id' => $user->main_company_id]
                        );
                        $tagIds[$tag->id] = ['main_company_id' => $user->main_company_id];
                    }
                    $cup->tags()->sync($tagIds);
                }
            }

            // Lógica de Finalização da Mesa...
            $firstItemId = $data['evaluations'][0]['cupping_item_id'];
            $sessionId = DB::table('cupping_items')->where('id', $firstItemId)->value('cupping_session_id');
            if ($sessionId) {
                DB::table('cupping_session_user')
                    ->where('cupping_session_id', $sessionId)
                    ->where('user_id', $user->id)
                    ->update(['has_finished' => true, 'updated_at' => $agoraSp]);
                
                // Verifica se todos terminaram para fechar a mesa
                $total = DB::table('cupping_session_user')->where('cupping_session_id', $sessionId)->count();
                $finished = DB::table('cupping_session_user')->where('cupping_session_id', $sessionId)->where('has_finished', true)->count();
                if ($total > 0 && $total === $finished) {
                    CuppingSession::where('id', $sessionId)->update(['status' => 'finalizado', 'finished_at' => $agoraSp]);
                }
            }
        });

        return response()->json(['message' => 'Sucesso!'], 201);
    } catch (\Exception $e) {
        Log::error("Erro ao finalizar cupping: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
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