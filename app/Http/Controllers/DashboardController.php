<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\GreenCoffeeLot;
use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function getStats()
    {
        $companyId = Auth::user()->main_company_id;
        return [
            'usuarios' => User::where('main_company_id', $companyId)->count(),
            'lotes'    => GreenCoffeeLot::where('user_id', Auth::id())->count(), // Ajustar se lote for por empresa
            'maquinas' => Machine::count(),
        ];
    }

    public function index() 
    {
        $stats = $this->getStats();
        return view('dashboard.index', compact('stats'));
    }

    public function laboratorio() 
    {
        $labStats = ['total_mesas' => 42, 'total_xicaras' => 156, 'maior_nota' => 91.5];
        return view('laboratory.index', compact('labStats'));
    }

    public function torrefacao() 
    {
        return view('roasting.index');
    }

    public function cadastros()
    {
        $cadastrosStats = ['variedades' => 12, 'fornecedores' => 28, 'maquinas' => 2, 'equipe' => 5];
        return view('registries.index', compact('cadastrosStats'));
    }

    public function configuracoes() 
    {
        return view('settings.index');
    }
}