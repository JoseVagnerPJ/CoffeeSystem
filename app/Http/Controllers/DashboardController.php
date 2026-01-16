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
        $dataInicio = Carbon::now()->addHours()->startOfMonth();
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
    $dataInicio = Carbon::now()->subHours();
    
    // Define o formato da data e o agrupamento SQL baseado no botão clicado
    $groupByFormat = '%Y-%m-%d';
    $displayFormat = 'd/m';

    switch ($periodo) {
        case 'D': 
            $dataInicio = $dataInicio->startOfDay();
            $groupByFormat = '%Y-%m-%d %H:00'; // Agrupa por hora
            $displayFormat = 'H:00';
            break;
        case 'S': $dataInicio = $dataInicio->subDays(7); break;
        case 'T': $dataInicio = $dataInicio->subMonths(3); break;
        case 'Se': $dataInicio = $dataInicio->subMonths(6); break;
        case 'A': $dataInicio = $dataInicio->subYear(); break;
        default: $dataInicio = $dataInicio->startOfMonth(); break;
    }

    // Consulta Mesas (Mesa = CuppingSession)
    $mesas = \App\Models\CuppingSession::where(DB::raw("DATE_ADD(created_at, INTERVAL 3 HOUR)"), '>=', $dataInicio)
    ->select(
        DB::raw("DATE_FORMAT(DATE_ADD(created_at, INTERVAL 3 HOUR), '$groupByFormat') as tempo"),
        DB::raw('COUNT(id) as total')
    )
    ->groupBy('tempo')->orderBy('tempo', 'ASC')->get();

    // Consulta Xícaras (Cup)
    $xicaras = \App\Models\Cup::where('created_at', '>=', $dataInicio)
        ->select(
            DB::raw("DATE_FORMAT(DATE_SUB(created_at, INTERVAL 3 HOUR), '$groupByFormat') as tempo"),
            DB::raw('COUNT(id) as total')
        )
        ->groupBy('tempo')->orderBy('tempo', 'ASC')->get();

    // Sincroniza labels (tempo) para as duas linhas
    $labels = $mesas->pluck('tempo')->merge($xicaras->pluck('tempo'))->unique()->sort()->values();
    
    // Formata os labels para exibição (ex: de 2023-10-25 para 25/10 ou 14:00)
    $labelsFormatados = $labels->map(function($t) use ($displayFormat) {
        return Carbon::parse($t)->format($displayFormat);
    });

    return response()->json([
        'labels' => $labelsFormatados,
        'mesas' => $labels->map(fn($l) => $mesas->where('tempo', $l)->first()->total ?? 0),
        'xicaras' => $labels->map(fn($l) => $xicaras->where('tempo', $l)->first()->total ?? 0)
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