<?php

namespace App\Http\Controllers;

use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\CuppingProfile;
use App\Models\Cup;
use App\Models\Visitor;
use App\Models\Sample; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->main_company_id;

        // 1. Inicia a Query básica das sessões
        $query = CuppingSession::query()
            ->with(['items.sample', 'items.evaluations', 'profile'])
            ->withCount([
                'items', 
                'users', 
                'evaluations as participants_count' => function ($query) {
                    $query->select(DB::raw('count(distinct(user_id))'));
                }
            ]);

        // --- APLICAÇÃO DE FILTROS ---
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', "%{$request->title}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('cupping_profile_id')) {
            $query->where('cupping_profile_id', $request->cupping_profile_id);
        }
        if ($request->filled('cup_nickname')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('nickname', 'LIKE', "%{$request->cup_nickname}%");
            });
        }

        // --- LÓGICA DE PERFIS (Mais utilizado como padrão) ---
        $profiles = CuppingProfile::where('main_company_id', $companyId)
            ->withCount(['sessions' => function($q) use ($companyId) {
                $q->where('main_company_id', $companyId);
            }])
            ->orderBy('sessions_count', 'desc')
            ->get();

        $defaultProfileId = $profiles->first()?->id;

        // 2. Executa a busca das sessões
        $sessions = $query->latest()->get();
        
        // 3. KPIs Dinâmicos
        $totalSessions = $sessions->count();
        $totalCups = CuppingItem::whereIn('cupping_session_id', $sessions->pluck('id'))->count();
        
        $maxScore = Cup::where('main_company_id', $companyId)
            ->max('final_score') ?? 0;

        // 4. Lógica do Gráfico (AJUSTADO: Removido offset de fuso horário manual)
        $filter = $request->get('chart_filter', 'M'); 
        $now = now(); // Usa o tempo padrão do sistema/config
        $stats = [];
        
        if ($filter == 'D') {
            for ($i = 23; $i >= 0; $i--) {
                $time = $now->copy()->subHours($i);
                $hourStr = $time->format('Y-m-d H');
                $stats[] = [
                    'label' => $time->format('H:00'),
                    'sessions_count' => CuppingSession::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H') = ?", [$hourStr])->count(),
                    'cups_count' => CuppingItem::whereHas('session', function($q) use ($hourStr) { 
                        $q->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H') = ?", [$hourStr]); 
                    })->count()
                ];
            }
        } else {
            $days = ($filter == 'S') ? 7 : 30;
            for ($i = ($days - 1); $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $stats[] = [
                    'label' => $date->format('d/m'),
                    'sessions_count' => CuppingSession::whereDate('created_at', $date->toDateString())->count(),
                    'cups_count' => CuppingItem::whereHas('session', function($q) use ($date) { 
                        $q->whereDate('created_at', $date->toDateString()); 
                    })->count()
                ];
            }
        }

        return view('laboratory.index', compact(
            'sessions', 'totalSessions', 'totalCups', 
            'maxScore', 'stats', 'filter', 'profiles', 'defaultProfileId'
        ));
    }

    public function searchSamples(Request $request)
    {
        $search = $request->get('search');
        $samples = Sample::with('origin')
            ->where('is_active', 1)
            ->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('variety', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_batch', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $samples->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'variety' => $s->variety,
                'supplier_batch' => $s->supplier_batch,
                'drying_process' => $s->drying_process,
                'fermentation' => $s->fermentation,
                'humidity' => $s->humidity,
                'screen' => $s->screen,
                'altitude' => $s->altitude,
                'crop' => $s->crop,
                'origin_name' => $s->origin ? $s->origin->name : 'Não informado'
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'cupping_profile_id' => 'required|exists:cupping_profiles,id',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $companyId = Auth::user()->main_company_id;

                $session = CuppingSession::create([
                    'main_company_id'    => $companyId,
                    'cupping_profile_id' => $request->cupping_profile_id,
                    'title'              => $request->title,
                    'session_date'       => now(), 
                    'created_by'         => Auth::id(), 
                    'status'             => 'aberto',
                    'is_blind'           => $request->has('is_blind'),
                    'started_at'         => now(),
                ]);

                foreach ($request->items as $itemData) {
                    $cupNotes = !empty($itemData['notes']) ? $itemData['notes'] : ($itemData['pre_roast_obs'] ?? null);

                    $session->items()->create([
                        'main_company_id' => $companyId,
                        'position'       => $itemData['position'],
                        'is_avulso'      => ($itemData['is_avulso'] == "1"),
                        'sample_id'      => $itemData['sample_id'] ?? null,
                        'nickname'       => $itemData['nickname'] ?? null,
                        'variety'        => $itemData['variety'] ?? null,
                        'drying_process' => $itemData['drying_process'] ?? null,
                        'fermentation'   => $itemData['fermentation'] ?? null,
                        'humidity'       => $itemData['humidity'] ?? null,
                        'screen'         => $itemData['screen'] ?? null,
                        'lot_id'         => $itemData['lot_id'] ?? null,
                        'altitude'       => $itemData['altitude'] ?? null,
                        'crop'           => $itemData['crop'] ?? null,
                        'origin_id'      => $itemData['origin_id'] ?? null,
                        'origin_type'    => $itemData['origin_type'] ?? null,
                        'notes'          => !empty($cupNotes) ? trim($cupNotes) : null,
                    ]);
                }
            });

            return redirect()->route('laboratorio')->with('success', 'Mesa iniciada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar mesa: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $session = CuppingSession::findOrFail($id);
            foreach ($session->items as $item) {
                $item->evaluations()->delete();
                $item->delete();
            }
            $session->delete();
        });
        return back()->with('success', 'Mesa removida com sucesso.');
    }

    public function getTechInfo($index) 
    { 
        return view('laboratory.partials.modal_cup_tech_info', compact('index')); 
    }

    public function getSessionInfo($id) 
    { 
        $session = CuppingSession::with(['items.sample', 'evaluations.user', 'profile', 'visitors'])->findOrFail($id); 
        return view('laboratory.partials.modal_cupping_session_info', compact('session')); 
    }

    public function getCuppingInfo($id) 
    { 
        $item = CuppingItem::with(['sample', 'evaluations.user', 'evaluations.tags', 'session.profile'])->findOrFail($id); 
        
        $isMasked = $item->session->is_blind && 
                    $item->session->status !== 'finalizado' && 
                    Auth::id() !== $item->session->created_by;

        return view('laboratory.partials.modal_cupping_info', compact('item', 'isMasked')); 
    }

public function generatePdf(Request $request, $id)
{
    $target = $request->query('target');
    if (!$target) return abort(400, "Provador não selecionado.");

    $parts = explode('_', $target);
    $type = $parts[0]; 
    $evaluatorId = $parts[1];

    // 1. Busca a sessão com perfil e itens
    $mesa = \App\Models\CuppingSession::with(['profile', 'items.sample'])->findOrFail($id);
    $profile = $mesa->profile;
    
    // 2. Identifica o provador e busca as notas (Cups)
    if ($type === 'user') {
        $evaluator = \App\Models\User::findOrFail($evaluatorId);
        $query = \App\Models\Cup::where('user_id', $evaluatorId);
    } else {
        $evaluator = \App\Models\CuppingVisitor::findOrFail($evaluatorId);
        $query = \App\Models\Cup::where('cupping_visitor_id', $evaluatorId);
    }

    $evaluations = $query->whereHas('item', function($q) use ($id) {
            $q->where('cupping_session_id', $id);
        })
        ->with(['item.sample', 'tags'])
        ->get();

    // 3. Lógica "Desmarcado = 10" [cite: 18-24]
    $map = [
        'eval_fragrance' => 'fragrance_aroma',
        'eval_flavor'    => 'flavor',
        'eval_aftertaste'=> 'aftertaste',
        'eval_acidity'   => 'acidity',
        'eval_body'      => 'body',
        'eval_balance'   => 'balance',
        'eval_uniformity'=> 'uniformity',
        'eval_clean_cup' => 'clean_cup',
        'eval_sweetness' => 'sweetness',
        'eval_overall'   => 'overall',
    ];

    foreach ($evaluations as $cup) {
        foreach ($map as $profileKey => $cupKey) {
            if (isset($profile->$profileKey) && !$profile->$profileKey) {
                $cup->$cupKey = 10.0; // Atributo desmarcado no perfil ganha nota máxima [cite: 18]
            }
        }
    }

    // 4. Gera o PDF passando a variável 'evaluations' (sem o 'ByRound' para evitar o erro)
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sca_form', [
        'mesa' => $mesa,
        'user' => $evaluator,
        'evaluations' => $evaluations, // NOME CORRIGIDO AQUI
    ])->setPaper('a4', 'landscape');

    return $pdf->stream("Relatorio_SCA_Mesa_{$id}.pdf");
}
}