<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GreenCoffeeLot;
use App\Models\CuppingSession;
use App\Models\Cup;
use App\Models\Company;
use App\Models\Farm;
use App\Models\Producer;
use App\Models\RoastingBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard Principal
     */
    public function index(Request $request) 
    {
        $user = Auth::user();
        $periodo = $request->get('periodo', 'M');

        // Ajuste de fuso: -3 horas para o filtro de início
        $dataInicio = Carbon::now()->subHours(3)->startOfMonth();
        switch ($periodo) {
            case 'D': $dataInicio = Carbon::now()->subHours(3)->startOfDay(); break;
            case 'S': $dataInicio = Carbon::now()->subHours(3)->startOfWeek(); break;
            case 'T': $dataInicio = Carbon::now()->subHours(3)->subMonths(3); break;
            case 'Se': $dataInicio = Carbon::now()->subHours(3)->subMonths(6); break;
            case 'A': $dataInicio = Carbon::now()->subHours(3)->startOfYear(); break;
        }

        // 1. ESTOQUE POR PERFIL (Cards e Mapa)
        $estoquePorPerfil = GreenCoffeeLot::with('sensoryProfile')
            ->select('sensory_profile_id', 'reference',
                DB::raw('SUM(total_weight) as total_kg'),
                DB::raw('SUM(quantity_bags) as total_sacas'))
            ->groupBy('sensory_profile_id', 'reference')
            ->get();

        // 2. ÚLTIMOS RECEBIMENTOS (Ordenado do antigo -> novo e -3h)
        $ultimosRecebimentos = GreenCoffeeLot::orderBy('created_at', 'DESC')
            ->limit(6)
            ->get()
            ->reverse() // Inverte para o antigo ficar na esquerda
            ->values()
            ->map(function($lote) {
                return [
                    'perfil_id' => $lote->sensory_profile_id,
                    'peso' => $lote->total_weight,
                    'data' => $lote->created_at->subHours(3)->format('d/m H:i:s')
                ];
            });

        // 3. LABORATÓRIO (Ordenado ASC e ajuste de -3h no Agrupamento)
        $mesasData = CuppingSession::where('session_date', '>=', $dataInicio)
            ->select(
                DB::raw('DATE(DATE_SUB(created_at, INTERVAL 3 HOUR)) as data'), 
                DB::raw('COUNT(id) as total')
            )
            ->groupBy('data')
            ->orderBy('data', 'ASC')
            ->get();

        $xicarasData = Cup::where('created_at', '>=', $dataInicio)
            ->select(
                DB::raw('DATE(DATE_SUB(created_at, INTERVAL 3 HOUR)) as data'), 
                DB::raw('COUNT(id) as total')
            )
            ->groupBy('data')
            ->orderBy('data', 'ASC')
            ->get();

        return view('dashboard.index', compact('user', 'estoquePorPerfil', 'ultimosRecebimentos', 'mesasData', 'xicarasData', 'periodo'));
    }

    /**
     * AJAX: Alternância entre Entradas (Recebimentos) e Saídas (Torras)
     */
    public function getMovimentacaoData(Request $request) 
{
    $tipo = $request->get('tipo', 'entrada');
    
    if ($tipo == 'entrada') {
        // Busca os últimos 6 lotes de café verde (Entradas)
        $dados = \App\Models\GreenCoffeeLot::orderBy('created_at', 'DESC')
            ->limit(6)->get()->reverse()->values()
            ->map(function($lote) {
                return [
                    'peso' => $lote->total_weight,
                    'data' => $lote->created_at->subHours(3)->format('d/m H:i:s')
                ];
            });
        $titulo = "ÚLTIMOS RECEBIMENTOS";
    } else {
        // Busca as últimas 6 torras finalizadas (Saídas)
        $dados = \App\Models\RoastingBatch::whereNotNull('finished_at')
            ->orderBy('finished_at', 'DESC')
            ->limit(6)->get()->reverse()->values()
            ->map(function($batch) {
                return [
                    'peso' => $batch->weight_out,
                    'data' => $batch->finished_at->subHours(3)->format('d/m H:i:s')
                ];
            });
        $titulo = "ÚLTIMAS SAÍDAS";
    }

    return response()->json(['dados' => $dados, 'titulo' => $titulo]);
}
    
public function getLaboratorioData(Request $request) 
{
    $periodo = $request->get('periodo', 'M');

    // Ajuste de fuso: -3 horas para o filtro de início
    $dataInicio = Carbon::now()->subHours(3)->startOfMonth();
    switch ($periodo) {
        case 'D': $dataInicio = Carbon::now()->subHours(3)->startOfDay(); break;
        case 'S': $dataInicio = Carbon::now()->subHours(3)->startOfWeek(); break;
        case 'T': $dataInicio = Carbon::now()->subHours(3)->subMonths(3); break;
        case 'Se': $dataInicio = Carbon::now()->subHours(3)->subMonths(6); break;
        case 'A': $dataInicio = Carbon::now()->subHours(3)->startOfYear(); break;
    }

    // Busca Mesas (CuppingSession)
    $mesas = \App\Models\CuppingSession::where('created_at', '>=', $dataInicio)
        ->select(DB::raw('DATE(DATE_SUB(created_at, INTERVAL 3 HOUR)) as data'), DB::raw('COUNT(id) as total'))
        ->groupBy('data')->orderBy('data', 'ASC')->get();

    // Busca Xícaras (Cup)
    $xicaras = \App\Models\Cup::where('created_at', '>=', $dataInicio)
        ->select(DB::raw('DATE(DATE_SUB(created_at, INTERVAL 3 HOUR)) as data'), DB::raw('COUNT(id) as total'))
        ->groupBy('data')->orderBy('data', 'ASC')->get();

    // Organiza labels únicos (datas)
    $labels = $mesas->pluck('data')->merge($xicaras->pluck('data'))->unique()->sort()->values();

    return response()->json([
        'labels' => $labels,
        'mesas' => $mesas->pluck('total'),
        'xicaras' => $xicaras->pluck('total')
    ]);
}

    /**
     * Módulo de Cadastros (Registries)
     */
    public function cadastros()
    {
        $cadastrosStats = [
            'variedades'   => 12, 
            'fornecedores' => Company::count() + Farm::count() + Producer::count(),
            'equipe'       => User::count()
        ];
        
        return view('registries.index', compact('cadastrosStats'));
    }

    public function laboratorio() 
    {
        return view('laboratory.index');
    }

    public function torrefacao() 
    {
        return view('roasting.index');
    }

    public function configuracoes() 
    {
        return view('settings.index');
    }
}