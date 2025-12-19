<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Função privada para buscar os dados globais do menu lateral.
     * Assim não precisamos repetir esse código em cada página.
     */
    private function getStats()
    {
        return [
            'empresas' => Company::count(),
            'usuarios' => DB::table('users')->count(),
            'lotes'    => DB::table('green_coffee_lots')->count(),
            'maquinas' => DB::table('roasting_machines')->count(),
        ];
    }

    /**
     * Exibe a Página Inicial (Dashboard)
     */
    public function index()
    {
        $stats = $this->getStats();
        return view('welcome', compact('stats'));
    }

    /**
     * Exibe a Página de Estoque Verde
     */
    public function estoque()
    {
        $stats = $this->getStats();
        return view('estoque', compact('stats'));
    }

    /**
     * Exibe a Página do Laboratório Sensorial
     */
    public function laboratorio()
    {
        $stats = $this->getStats();

        // Dados temporários para os KPIs do Laboratório
        // No futuro, buscaremos isso das tabelas 'cupping_sessions' e 'cups'
        $labStats = [
            'total_mesas'   => 42,
            'total_xicaras' => 156,
            'maior_nota'    => 91.5,
        ];

        return view('laboratorio', compact('stats', 'labStats'));
    }
}