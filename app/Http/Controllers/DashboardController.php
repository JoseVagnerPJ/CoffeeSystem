<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GreenCoffeeLot;
use App\Models\CuppingSession;
use App\Models\CuppingItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request) 
    {
        $user = Auth::user();
        
        $estoquePorPerfil = GreenCoffeeLot::with('sensoryProfile')
            ->select('sensory_profile_id', 'reference',
                DB::raw('SUM(total_weight) as total_kg'),
                DB::raw('SUM(quantity_bags) as total_sacas'))
            ->groupBy('sensory_profile_id', 'reference')
            ->get();

        // Variáveis vazias para o primeiro boot da página (o AJAX preencherá)
        $ultimosRecebimentos = collect();
        $mesasData = collect();
        $xicarasData = collect();
        $periodo = 'S'; 

        return view('dashboard.index', compact('user', 'estoquePorPerfil', 'ultimosRecebimentos', 'mesasData', 'xicarasData', 'periodo'));
    }

    public function getMovimentacaoData(Request $request) 
    {
        $tipo = $request->get('tipo', 'entrada');
        $perfilId = $request->get('perfil_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $rangeType = $request->get('range_type');

        $query = ($tipo == 'entrada') 
            ? GreenCoffeeLot::query() 
            : StockMovement::where('type', 'out');

        // --- FILTRO SENSORIAL ---
        if ($perfilId) {
            if ($tipo == 'entrada') {
                // Certifique-se que o nome da coluna na tabela green_coffee_lots é este:
                $query->where('sensory_profile_id', $perfilId);
            } else {
                // Certifique-se que na Model StockMovement o relacionamento se chama 'lote'
                $query->whereHas('lote', function($q) use ($perfilId) {
                    $q->where('sensory_profile_id', $perfilId);
                });
            }
        }

        // --- FILTRO DE DATA ---
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(), 
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        if ($rangeType === 'all') {
            // LÓGICA "TODOS": Mostra cada registro individual (como os antigos últimos 6)
            $dados = $query->orderBy('created_at', 'ASC')->get()->map(function($item) use ($tipo) {
                return [
                    'peso' => ($tipo == 'entrada') ? $item->total_weight : $item->weight,
                    'data' => $item->created_at->subHours(3)->format('d/m H:i')
                ];
            });
        } else {
            // LÓGICA AGRUPADA: Soma por dia para a "linha no chão"
            $colPeso = ($tipo == 'entrada') ? 'total_weight' : 'weight';
            $dados = $query->select(
                DB::raw("DATE_FORMAT(DATE_SUB(created_at, INTERVAL 3 HOUR), '%d/%m') as data"),
                DB::raw("SUM($colPeso) as peso"),
                DB::raw("MAX(created_at) as sort_date")
            )
            ->groupBy('data')
            ->orderBy('sort_date', 'ASC')
            ->get();
        }

        return response()->json([
            'dados' => $dados,
            'titulo' => ($tipo == 'entrada') ? "RECEBIMENTOS" : "SAÍDAS (TORRA)",
            'label' => ($tipo == 'entrada') ? "kg Recebido" : "kg Torrado"
        ]);
    }

    public function getLaboratorioData(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $rangeType = $request->get('range_type');

        $query = CuppingSession::query();

        if ($startDate && $endDate) {
            $query->whereBetween('cupping_sessions.created_at', [
                Carbon::parse($startDate)->startOfDay(), 
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        if ($rangeType === 'all') {
            // Laboratório "TODOS": Pontos individuais
            $dados = $query->leftJoin('cupping_items', 'cupping_sessions.id', '=', 'cupping_items.cupping_session_id')
                ->select(
                    DB::raw("DATE_FORMAT(DATE_SUB(cupping_sessions.created_at, INTERVAL 3 HOUR), '%d/%m %H:%i') as tempo"),
                    DB::raw('COUNT(DISTINCT cupping_sessions.id) as total_mesas'),
                    DB::raw('COUNT(cupping_items.id) as total_xicaras')
                )
                ->groupBy('tempo', 'cupping_sessions.created_at')
                ->orderBy('cupping_sessions.created_at', 'ASC')
                ->get();
        } else {
            // Laboratório "AGRUPADO"
            $dados = $query->leftJoin('cupping_items', 'cupping_sessions.id', '=', 'cupping_items.cupping_session_id')
                ->select(
                    DB::raw("DATE_FORMAT(DATE_SUB(cupping_sessions.created_at, INTERVAL 3 HOUR), '%d/%m') as tempo"),
                    DB::raw('COUNT(DISTINCT cupping_sessions.id) as total_mesas'),
                    DB::raw('COUNT(cupping_items.id) as total_xicaras'),
                    DB::raw('MAX(cupping_sessions.created_at) as sort_date')
                )
                ->groupBy('tempo')
                ->orderBy('sort_date', 'ASC')
                ->get();
        }

        return response()->json(['raw_data' => $dados]);
    }

    // --- MANTIDAS AS OUTRAS FUNÇÕES ---
    public function cadastros() { return view('registries.index'); }
    public function laboratorio() { return view('laboratory.index'); }
    public function torrefacao() { return view('roasting.index'); }
    public function configuracoes() { return view('settings.index'); }
}