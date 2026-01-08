<?php

namespace App\Http\Controllers;

use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Cup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Listagem Principal
        $sessions = CuppingSession::with(['items.sample', 'items.evaluations'])
            ->withCount(['items', 'evaluations as participants_count' => function ($query) {
                $query->select(DB::raw('count(distinct(user_id))'));
            }])
            ->orderBy('id', 'desc')
            ->get();

        // 2. KPIs
        $totalSessions = $sessions->count();
        $totalCups = Cup::count();
        $maxScore = DB::table('cups')
            ->select(DB::raw('MAX(acidity + body + sweetness + aftertaste + 60) as sca'))
            ->value('sca') ?? 0;

        // 3. Lógica do Gráfico (D / S / M)
        $filter = $request->get('chart_filter', 'M'); 
        $now = Carbon::now('America/Sao_Paulo');

        $query = DB::table('cupping_sessions')
            ->leftJoin('cupping_items', 'cupping_sessions.id', '=', 'cupping_items.cupping_session_id');

        if ($filter == 'D') {
            $query->select(
                DB::raw("DATE_FORMAT(cupping_sessions.created_at, '%H:00') as label"),
                DB::raw('COUNT(DISTINCT cupping_sessions.id) as sessions_count'),
                DB::raw('COUNT(cupping_items.id) as cups_count'),
                DB::raw('MIN(cupping_sessions.created_at) as sort_key')
            )
            ->whereDate('cupping_sessions.created_at', $now->toDateString());
        } else {
            $days = $filter == 'S' ? 7 : 30;
            $query->select(
                DB::raw("DATE_FORMAT(cupping_sessions.created_at, '%d/%m') as label"),
                DB::raw('COUNT(DISTINCT cupping_sessions.id) as sessions_count'),
                DB::raw('COUNT(cupping_items.id) as cups_count'),
                DB::raw('MIN(cupping_sessions.created_at) as sort_key')
            )
            ->where('cupping_sessions.created_at', '>=', $now->copy()->subDays($days));
        }

        $stats = $query->groupBy('label')->orderBy('sort_key', 'ASC')->get();

        return view('laboratory.index', compact('sessions', 'totalSessions', 'totalCups', 'maxScore', 'stats', 'filter'));
    }

    /**
     * Armazena uma nova mesa de prova criada via Web
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Cria a Sessão (Mesa)
                $session = CuppingSession::create([
                    'title' => $request->title,
                    'user_id' => Auth::id(), // Dono da mesa
                    'status' => 'aberto',
                    'started_at' => now(),
                ]);

                // 2. Cria cada Xícara (Item)
                foreach ($request->items as $itemData) {
                    $session->items()->create([
                        'position'       => $itemData['position'],
                        'is_avulso'      => isset($itemData['is_avulso']) ? true : false,
                        'sample_id'      => $itemData['sample_id'] ?? null,
                        'nickname'       => $itemData['nickname'] ?? null,
                        'variety'        => $itemData['variety'] ?? null,
                        'drying_process' => $itemData['drying_process'] ?? null,
                        'fermentation'   => $itemData['fermentation'] ?? null,
                        'humidity'       => $itemData['humidity'] ?? null,
                        'screen'         => $itemData['screen'] ?? null,
                        'lot_id'         => $itemData['lot_id'] ?? null,
                        'altitude'       => $itemData['altitude'] ?? null,
                        'origin'         => $itemData['origin'] ?? null,
                        'crop'           => $itemData['crop'] ?? null,
                        'notes'          => $itemData['notes'] ?? null,
                    ]);
                }
            });

            return redirect()->route('laboratorio')->with('success', 'Mesa iniciada com sucesso! Os provadores já podem visualizá-la no App.');
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
}