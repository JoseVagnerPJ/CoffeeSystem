<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GreenCoffeeLot;
use App\Models\Machine;
use App\Models\CuppingSession; // Assumindo que este é o nome do seu modelo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request) 
    {
        $companyId = Auth::user()->main_company_id;
        $periodo = $request->get('periodo', 'mes'); // Padrão: mensal

        // 1. Estatísticas Básicas
        $stats = [
            'maquinas' => Machine::where('main_company_id', $companyId)->count(),
            'lotes'    => GreenCoffeeLot::where('main_company_id', $companyId)->count(),
        ];

        // 2. Estoque por Perfil Sensorial (Sacas e KG)
        // Assume-se que GreenCoffeeLot tem sensory_profile_id e peso_atual
        $estoquePorPerfil = DB::table('green_coffee_lots')
            ->join('sensory_profiles', 'green_coffee_lots.sensory_profile_id', '=', 'sensory_profiles.id')
            ->where('green_coffee_lots.main_company_id', $companyId)
            ->select(
                'sensory_profiles.name',
                'sensory_profiles.color',
                'green_coffee_lots.id as lote_id',
                DB::raw('SUM(peso_atual) as total_kg')
            )
            ->groupBy('sensory_profiles.id', 'sensory_profiles.name', 'sensory_profiles.color', 'green_coffee_lots.id')
            ->get()
            ->map(function($item) {
                $item->total_sacas = number_format($item->total_kg / 60, 1); // 60kg por saca
                return $item;
            });

        // 3. Dados para o Gráfico de Recebimento
        $recebimentos = GreenCoffeeLot::where('main_company_id', $companyId)
            ->select(DB::raw('DATE(created_at) as data'), DB::raw('SUM(peso_inicial) as peso'))
            ->groupBy('data')
            ->orderBy('data', 'ASC')
            ->get();

        // 4. Dados para o Gráfico de Laboratório (Mesas e Xícaras)
        $cuppingData = DB::table('cupping_sessions')
            ->where('main_company_id', $companyId)
            ->select(
                DB::raw('DATE(created_at) as data'),
                DB::raw('COUNT(id) as total_mesas'),
                DB::raw('SUM(items_count) as total_xicaras') // Assume que tem contador de amostras
            )
            ->groupBy('data')
            ->get();

        return view('dashboard.index', compact('stats', 'estoquePorPerfil', 'recebimentos', 'cuppingData', 'periodo'));
    }
}