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
    public function index(Request $request) 
    {
        $user = Auth::user();
        $periodo = $request->get('periodo', 'D');

        $dataInicio = Carbon::now()->subHours(3);
        if ($periodo == 'D') {
            $dataInicio = $dataInicio->subHours(24);
            $formatoSql = '%H:%i:%s';
        } else {
            $formatoSql = '%d/%m';
            switch ($periodo) {
                case 'S': $dataInicio = $dataInicio->subDays(7); break;
                case 'T': $dataInicio = $dataInicio->subMonths(3); break;
                case 'Se': $dataInicio = $dataInicio->subMonths(6); break;
                case 'A': $dataInicio = $dataInicio->subYears(2); break; // Para ver o Seeder
                default: $dataInicio = $dataInicio->startOfMonth(); break;
            }
        }

        $estoquePorPerfil = GreenCoffeeLot::with('sensoryProfile')
            ->select('sensory_profile_id', 'reference',
                DB::raw('SUM(total_weight) as total_kg'),
                DB::raw('SUM(quantity_bags) as total_sacas'))
            ->groupBy('sensory_profile_id', 'reference')
            ->get();

        $ultimosRecebimentos = GreenCoffeeLot::orderBy('created_at', 'DESC')
            ->limit(6)->get()->reverse()->values()
            ->map(function($lote) {
                return [
                    'perfil_id' => $lote->sensory_profile_id,
                    'peso' => $lote->total_weight,
                    'data' => $lote->created_at->subHours(3)->format('d/m H:i:s')
                ];
            });

        // Query Unificada Corrigida para SQL strict mode (orderBy pelo alias)
        $dadosLab = CuppingSession::where('cupping_sessions.created_at', '>=', $dataInicio)
            ->leftJoin('cupping_items', 'cupping_sessions.id', '=', 'cupping_items.cupping_session_id')
            ->select(
                DB::raw("DATE_FORMAT(DATE_SUB(cupping_sessions.created_at, INTERVAL 3 HOUR), '$formatoSql') as data"),
                DB::raw('COUNT(DISTINCT cupping_sessions.id) as total_mesas'),
                DB::raw('COUNT(cupping_items.id) as total_xicaras')
            )
            ->groupBy('data')
            ->orderBy('data', 'ASC')
            ->get();

        // Criamos as variáveis separadas para não dar erro na View
        $mesasData = $dadosLab->map(fn($item) => (object)['data' => $item->data, 'total' => $item->total_mesas]);
        $xicarasData = $dadosLab->map(fn($item) => (object)['data' => $item->data, 'total' => $item->total_xicaras]);

        return view('dashboard.index', compact('user', 'estoquePorPerfil', 'ultimosRecebimentos', 'dadosLab', 'mesasData', 'xicarasData', 'periodo'));
    }

    public function getMovimentacaoData(Request $request) 
    {
        $tipo = $request->get('tipo', 'entrada');
        if ($tipo == 'entrada') {
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
            $dados = \App\Models\StockMovement::with('lote')
    ->where('type', 'out')
    ->latest()
    ->limit(6)
    ->get()
    ->reverse()
    ->values()
    ->map(function($movimentacao) {
        return [
            // perfil_id extraído através do relacionamento 'lote'
            'perfil_id' => $movimentacao->lote->sensory_profile_id ?? null,
            // Peso retirado no modal
            'peso' => $movimentacao->weight,
            // Data formatada com ajuste de fuso horário
            'data' => $movimentacao->created_at->subHours(3)->format('d/m H:i:s')
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
                case 'A': $dataInicio = $dataInicio->subYears(2); break;
                default: $dataInicio = $dataInicio->startOfMonth(); break;
            }
        }

        $dados = CuppingSession::where('cupping_sessions.created_at', '>=', $dataInicio)
            ->leftJoin('cupping_items', 'cupping_sessions.id', '=', 'cupping_items.cupping_session_id')
            ->select(
                DB::raw("DATE_FORMAT(DATE_SUB(cupping_sessions.created_at, INTERVAL 3 HOUR), '$formatoSql') as tempo"),
                DB::raw('COUNT(DISTINCT cupping_sessions.id) as total_mesas'),
                DB::raw('COUNT(cupping_items.id) as total_xicaras')
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

    public function cadastros() { return view('registries.index'); }
    public function laboratorio() { return view('laboratory.index'); }
    public function torrefacao() { return view('roasting.index'); }
    public function configuracoes() { return view('settings.index'); }
}