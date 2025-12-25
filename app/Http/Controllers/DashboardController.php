<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retorna as estatísticas globais usadas no menu lateral (app.blade.php)
     */
    private function getStats()
    {
        return [
            'empresas' => Company::count(),
            'usuarios' => DB::table('users')->count(),
            'lotes'    => DB::table('green_coffee_lots')->count(),
            'maquinas' => DB::table('machines')->count(),
        ];
    }

    /**
     * View: Dashboard (Página Inicial)
     */
    public function index() 
{
    $stats = $this->getStats();
    return view('inicio', compact('stats'));
}

    /**
     * View: Gestão de Estoque Verde
     */
    public function estoque() 
    {
        $stats = $this->getStats();
        return view('estoque', compact('stats'));
    }

    /**
     * View: Laboratório Sensorial
     */
    public function laboratorio() 
    {
        $stats = $this->getStats();
        
        // Dados simulados para os KPIs do Laboratório
        $labStats = [
            'total_mesas' => 42,
            'total_xicaras' => 156,
            'maior_nota' => 91.5,
        ];

        return view('laboratorio', compact('stats', 'labStats'));
    }

    /**
     * View: Controle de Torrefação (Kanban)
     */
    public function torrefacao() 
    {
        $stats = $this->getStats();
        // Se no futuro a tela de torrefação precisar de KPIs próprios,
        // você pode criá-los aqui como fizemos no laboratório.
        return view('torrefacao', compact('stats'));
    }

    /**
     * View: Hub de Cadastros
     */
    public function cadastros()
    {
        $stats = $this->getStats();
        
        // Dados para o resumo dos cards na tela de cadastros
        $cadastrosStats = [
            'variedades'   => 12, // Ex: Bourbon, Catuaí...
            'fornecedores' => 28, // Soma de empresas e produtores
            'maquinas'     => 2,  // Probat e CarmoMaq
            'equipe'       => 5   // Mestres e Provadores
        ];

        return view('cadastros', compact('stats', 'cadastrosStats'));
    }

    /**
     * View: Configurações do Sistema
     */
    public function configuracoes() 
    {
        $stats = $this->getStats();
        return view('configuracoes', compact('stats'));
    }
}