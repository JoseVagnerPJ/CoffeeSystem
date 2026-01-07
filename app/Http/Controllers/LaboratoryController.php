<?php

namespace App\Http\Controllers;

use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\Cup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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