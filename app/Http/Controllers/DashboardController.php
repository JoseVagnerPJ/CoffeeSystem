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
    // Definimos 'D' como padrão para mostrar as últimas 24h com precisão
    $periodo = $request->get('periodo', 'D');

    // Ajuste inicial de fuso: -3 horas
    $dataInicio = Carbon::now()->subHours(3);
    
    // Define a granularidade do gráfico inicial
    if ($periodo == 'D') {
        $dataInicio = $dataInicio->subHours(24);
        $formatoSql = '%H:%i:%s'; // Precisão de segundos para o gráfico não "ziguezaguear"
    } else {
        $formatoSql = '%d/%m';
        switch ($periodo) {
            case 'S': $dataInicio = $dataInicio->subDays(7); break;
            case 'M': $dataInicio = $dataInicio->startOfMonth(); break;
            case 'T': $dataInicio = $dataInicio->subMonths(3); break;
            case 'Se': $dataInicio = $dataInicio->subMonths(6); break;
            case 'A': $dataInicio = $dataInicio->startOfYear(); break;
        }
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
        ->reverse()
        ->values()
        ->map(function($lote) {
            return [
                'perfil_id' => $lote->sensory_profile_id,
                'peso' => $lote->total_weight,
                'data' => $lote->created_at->subHours(3)->format('d/m H:i:s')
            ];
        });

    // 3. LABORATÓRIO (Lógica Unificada para evitar linhas no zero)
    // Buscamos as Mesas e somamos as xícaras (items_count) no mesmo agrupamento de tempo
    $dadosLab = CuppingSession::where('created_at', '>=', $dataInicio)
        ->select(
            DB::raw("DATE_FORMAT(DATE_SUB(created_at, INTERVAL 3 HOUR), '$formatoSql') as data"), 
            DB::raw('COUNT(id) as total_mesas'),
            DB::raw('SUM(items_count) as total_xicaras')
        )
        ->groupBy('data')
        ->orderBy('data', 'ASC')
        ->get();

    // Caso você ainda use as variáveis separadas na View, extraímos elas aqui:
    $mesasData = $dadosLab->map(fn($item) => ['data' => $item->data, 'total' => $item->total_mesas]);
    $xicarasData = $dadosLab->map(fn($item) => ['data' => $item->data, 'total' => $item->total_xicaras]);

    return view('dashboard.index', compact(
        'user', 
        'estoquePorPerfil', 
        'ultimosRecebimentos', 
        'dadosLab', 
        'mesasData', 
        'xicarasData', 
        'periodo'
    ));
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
        $periodo = $request->get('periodo', 'D');
        $dataInicio = Carbon::now()->subHours(3);
        $formatoSql = ($periodo == 'D') ? '%H:%i:%s' : '%d/%m';

        if ($periodo == 'D') {
            $dataInicio = $dataInicio->subHours(24);
        } else {
            switch ($periodo) {
                case 'S': $dataInicio = $dataInicio->subDays(7); break;
                case 'T': $dataInicio = $dataInicio->subMonths(3); break;
                case 'Se': $dataInicio = $dataInicio->subMonths(6); break;
                case 'A': $dataInicio = $dataInicio->subYear(); break;
                default: $dataInicio = $dataInicio->startOfMonth(); break;
            }
        }

        // Busca Unificada: Xícaras e Mesas amarradas no mesmo timestamp
        $dados = CuppingSession::where('created_at', '>=', $dataInicio)
            ->select(
                DB::raw("DATE_FORMAT(DATE_SUB(created_at, INTERVAL 3 HOUR), '$formatoSql') as tempo"),
                DB::raw('COUNT(id) as total_mesas'),
                DB::raw('SUM(items_count) as total_xicaras')
            )
            ->groupBy('tempo')
            ->orderBy('tempo', 'ASC')
            ->get();

        return response()->json([
            'labels' => $dados->pluck('tempo'),
            'mesas' => $dados->pluck('total_mesas'),
            'xicaras' => $dados->pluck('total_xicaras')
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