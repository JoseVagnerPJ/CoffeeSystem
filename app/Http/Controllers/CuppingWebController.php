<?php

namespace App\Http\Controllers;

use App\Models\CuppingSession;
use App\Models\Cup;
use Illuminate\Http\Request;

class CuppingWebController extends Controller
{
    public function index()
    {
        // 1. Busca as mesas com os relacionamentos para a tabela
        $sessions = CuppingSession::with(['items.sample', 'items.evaluations.tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Calcula as estatísticas para os cards do topo
        $totalSessions = CuppingSession::count();
        $totalCups = Cup::count();
        $maxScore = Cup::max('final_score') ?? 0;

        // 3. Passa todas as variáveis para a view
        return view('laboratory.index', compact(
            'sessions', 
            'totalSessions', 
            'totalCups', 
            'maxScore'
        ));
    }
}