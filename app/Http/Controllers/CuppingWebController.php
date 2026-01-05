<?php

namespace App\Http\Controllers;

use App\Models\CuppingSession;
use App\Models\Cup;
use App\Models\CuppingItem;
use Illuminate\Http\Request;

class CuppingWebController extends Controller
{
    /**
     * Dashboard do Laboratório.
     */
    public function index()
    {
        // 1. Busca as sessões com todos os dados técnicos e avaliações dos usuários
        $sessions = CuppingSession::with([
                'items.sample', 
                'items.evaluations.user', 
                'items.evaluations.tags',
                'creator'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Estatísticas Globais para os Cards
        $totalSessions = CuppingSession::count();
        $totalCups = Cup::where('is_invalid', false)->count(); // Apenas xícaras válidas
        $maxScore = Cup::where('is_invalid', false)->max('final_score') ?? 0;

        return view('laboratory.index', compact(
            'sessions', 
            'totalSessions', 
            'totalCups', 
            'maxScore'
        ));
    }

    /**
     * Fecha a mesa para novas avaliações.
     */
    public function closeSession($id)
    {
        $session = CuppingSession::findOrFail($id);
        $session->update(['status' => 'finalizado']);

        return back()->with('success', 'Mesa de prova finalizada com sucesso!');
    }
}