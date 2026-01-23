<?php

namespace App\Http\Controllers;

use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Cup;
use App\Models\Sample; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Iniciamos a Query básica
        $query = CuppingSession::query()
            ->with(['items.sample', 'items.evaluations'])
            ->withCount(['items', 'evaluations as participants_count' => function ($query) {
                $query->select(DB::raw('count(distinct(user_id))'));
            }]);

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

        if ($request->filled('cup_nickname')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('nickname', 'LIKE', "%{$request->cup_nickname}%");
            });
        }

        if ($request->filled('min_participants')) {
            $query->whereRaw('(SELECT COUNT(DISTINCT user_id) FROM cups 
                               INNER JOIN cupping_items ON cups.cupping_item_id = cupping_items.id 
                               WHERE cupping_items.cupping_session_id = cupping_sessions.id) >= ?', 
                               [$request->min_participants]);
        }

        if ($request->filled('min_score') || $request->filled('max_score')) {
            $min = (float) ($request->min_score ?? 0);
            $max = (float) ($request->max_score ?? 100);

            $query->whereDoesntHave('items', function ($q) use ($min, $max) {
                $q->select('cupping_items.id') 
                  ->join('cups', 'cupping_items.id', '=', 'cups.cupping_item_id')
                  ->groupBy('cupping_items.id')
                  ->havingRaw('60 + AVG(cups.sweetness + cups.acidity + cups.body + cups.aftertaste) < ?', [$min])
                  ->orHavingRaw('60 + AVG(cups.sweetness + cups.acidity + cups.body + cups.aftertaste) > ?', [$max]);
            });

            $query->whereHas('items.evaluations');
        }

        // 2. Executa a busca
        $sessions = $query->latest()->get();
        
        // 3. KPIs Dinâmicos
        $totalSessions = $sessions->count();
        $totalCups = CuppingItem::whereIn('cupping_session_id', $sessions->pluck('id'))->count();
        
        $maxScore = DB::table('cups')
            ->select(DB::raw('MAX(acidity + body + sweetness + aftertaste + 60) as sca'))
            ->value('sca') ?? 0;

        // 4. Lógica do Gráfico
        $filter = $request->get('chart_filter', 'M'); 
        $now = \Carbon\Carbon::now('America/Sao_Paulo');
        $stats = [];
        if ($filter == 'D') {
            for ($i = 23; $i >= 0; $i--) {
                $time = $now->copy()->subHours($i);
                $hourStr = $time->format('Y-m-d H');
                $stats[] = [
                    'label' => $time->format('H:00'),
                    'sessions_count' => CuppingSession::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H') = ?", [$hourStr])->count(),
                    'cups_count' => CuppingItem::whereHas('session', function($q) use ($hourStr) { $q->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H') = ?", [$hourStr]); })->count()
                ];
            }
        } else {
            $days = ($filter == 'S') ? 7 : 30;
            for ($i = ($days - 1); $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $stats[] = [
                    'label' => $date->format('d/m'),
                    'sessions_count' => CuppingSession::whereDate('created_at', $date->toDateString())->count(),
                    'cups_count' => CuppingItem::whereHas('session', function($q) use ($date) { $q->whereDate('created_at', $date->toDateString()); })->count()
                ];
            }
        }

        return view('laboratory.index', compact('sessions', 'totalSessions', 'totalCups', 'maxScore', 'stats', 'filter'));
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
    dd($request->all());
    
    $request->validate([
        'title' => 'required|string|max:255',
        'items' => 'required|array|min:1',
    ]);

    try {
        DB::transaction(function () use ($request) {
            $companyId = Auth::user()->main_company_id;

            $session = CuppingSession::create([
                'main_company_id' => $companyId,
                'title'           => $request->title,
                'session_date'    => now(), 
                'created_by'      => Auth::id(), 
                'status'          => 'aberto',
                'started_at'      => now(),
            ]);

            foreach ($request->items as $itemData) {
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
                    'altitude'       => $itemData['altitude'] ?? null,
                    'crop'           => $itemData['crop'] ?? null,
                    'origin_id'      => $itemData['origin_id'] ?? null,
                    'origin_type'    => $itemData['origin_type'] ?? null,
                    // O trim remove espaços vazios e garante que salve null se nada for digitado
                    'notes'          => !empty($itemData['notes']) ? trim($itemData['notes']) : null,
                ]);
            }
        });

        return redirect()->route('laboratorio')->with('success', 'Mesa iniciada com sucesso!');
    } catch (\Exception $e) {
        \Log::error("Erro ao salvar mesa Web: " . $e->getMessage());
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

    public function getTechInfo($index) { return view('laboratory.partials.modal_cup_tech_info', compact('index')); }
    public function getSessionInfo($id) { $session = CuppingSession::with(['items', 'evaluations.user'])->findOrFail($id); return view('laboratory.partials.modal_cupping_session_info', compact('session')); }
    public function getCuppingInfo($id) { $item = CuppingItem::with(['sample', 'evaluations.user', 'evaluations.tags'])->findOrFail($id); return view('laboratory.partials.modal_cupping_info', compact('item')); }
}