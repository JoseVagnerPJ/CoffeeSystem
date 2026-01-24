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
        
        // Mapa de Estoque por Perfil (Barra fixa no topo)
        $estoquePorPerfil = GreenCoffeeLot::with('sensoryProfile')
            ->select('sensory_profile_id', 'reference',
                DB::raw('SUM(total_weight) as total_kg'),
                DB::raw('SUM(quantity_bags) as total_sacas'))
            ->groupBy('sensory_profile_id', 'reference')
            ->get();

        // Dados iniciais para o primeiro carregamento da página
        $ultimosRecebimentos = GreenCoffeeLot::orderBy('created_at', 'DESC')
            ->limit(6)->get()->reverse()->values()
            ->map(function($lote) {
                return [
                    'perfil_id' => $lote->sensory_profile_id,
                    'peso' => $lote->total_weight,
                    'data' => $lote->created_at->subHours(3)->format('d/m H:i')
                ];
            });

        // Variáveis padrão para evitar erros no blade no init
        $mesasData = collect();
        $xicarasData = collect();
        $periodo = 'S'; 

        return view('dashboard.index', compact('user', 'estoquePorPerfil', 'ultimosRecebimentos', 'mesasData', 'xicarasData', 'periodo'));
    }

    /**
     * AJAX: Dados de Movimentação (Recebimentos ou Saídas)
     */
    public function getMovimentacaoData(Request $request) 
    {
        $tipo = $request->get('tipo', 'entrada');
        $perfilId = $request->get('perfil_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $isGeral = ($request->get('range_type') === 'all');

        $query = ($tipo == 'entrada') 
            ? GreenCoffeeLot::query() 
            : StockMovement::where('type', 'out');

        // Filtro Sensorial
        if ($perfilId) {
            if ($tipo == 'entrada') {
                $query->where('sensory_profile_id', $perfilId);
            } else {
                $query->whereHas('lote', function($q) use ($perfilId) {
                    $q->where('sensory_profile_id', $perfilId);
                });
            }
        }

        // Filtro de Data
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(), 
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        if ($isGeral) {
            // Retorno bruto para alta densidade
            $dados = $query->orderBy('created_at', 'ASC')->get()->map(fn($item) => [
                'peso' => ($tipo == 'entrada') ? $item->total_weight : $item->weight,
                'data' => $item->created_at->subHours(3)->format('d/m H:i')
            ]);
        } else {
            // Agrupamento por dia para a "linha no chão" do JS
            $colPeso = ($tipo == 'entrada') ? 'total_weight' : 'weight';
            $dados = $query->select(
                DB::raw("DATE_FORMAT(DATE_SUB(created_at, INTERVAL 3 HOUR), '%d/%m') as data"),
                DB::raw("SUM($colPeso) as peso")
            )
            ->groupBy('data')
            ->orderBy('created_at', 'ASC')
            ->get();
        }

        return response()->json([
            'dados' => $dados,
            'titulo' => ($tipo == 'entrada') ? "RECEBIMENTOS" : "SAÍDAS (TORRA)",
            'label' => ($tipo == 'entrada') ? "kg Recebido" : "kg Torrado"
        ]);
    }

    /**
     * AJAX: Dados do Laboratório (Mesas e Xícaras)
     */
    public function getLaboratorioData(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $isGeral = ($request->get('range_type') === 'all');

        $query = CuppingSession::query();

        if ($startDate && $endDate) {
            $query->whereBetween('cupping_sessions.created_at', [
                Carbon::parse($startDate)->startOfDay(), 
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $query->leftJoin('cupping_items', 'cupping_sessions.id', '=', 'cupping_items.cupping_session_id')
              ->select(
                  DB::raw("DATE_FORMAT(DATE_SUB(cupping_sessions.created_at, INTERVAL 3 HOUR), '%d/%m') as tempo"),
                  DB::raw('COUNT(DISTINCT cupping_sessions.id) as total_mesas'),
                  DB::raw('COUNT(cupping_items.id) as total_xicaras')
              )
              ->groupBy('tempo')
              ->orderBy('cupping_sessions.created_at', 'ASC');

        $dados = $query->get();

        return response()->json([
            'raw_data' => $dados
        ]);
    }

    public function cadastros() { return view('registries.index'); }
    public function laboratorio() { return view('laboratory.index'); }
    public function torrefacao() { return view('roasting.index'); }
    public function configuracoes() { return view('settings.index'); }
}