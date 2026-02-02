<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogisticOrder;
use App\Models\LogisticRule;
use App\Models\LogisticOrderItem;
use App\Models\LogisticOrderDepartment;
use App\Services\OmieService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogisticsController extends Controller
{
    protected $omieService;

    public function __construct(OmieService $omieService)
    {
        $this->omieService = $omieService;
    }

    public function index()
    {
        $orders = LogisticOrder::where('main_company_id', auth()->user()->main_company_id)
            ->whereIn('status', ['importado', 'pending', 'approved']) 
            ->orderBy('order_number', 'desc')
            ->get();

        return view('logistics.index', compact('orders'));
    }

    public function syncOrders()
    {
        set_time_limit(300); 
        $registrosPorPagina = 50;

        $res = $this->omieService->listOrders(1, $registrosPorPagina); 
        if (!$res || !isset($res['total_de_paginas'])) {
            return back()->with('error', 'Sem resposta do Omie.');
        }

        $ultimaPagina = $res['total_de_paginas'];
        $allRecent = [];

        $paginasParaBuscar = array_unique(array_filter([$ultimaPagina, $ultimaPagina - 1], fn($p) => $p > 0));

        foreach ($paginasParaBuscar as $pg) {
            $data = $this->omieService->listOrders($pg, $registrosPorPagina);
            if (isset($data['pedido_venda_produto'])) {
                $allRecent = array_merge($allRecent, $data['pedido_venda_produto']);
            }
        }

        // MAPEAMENTO SOLICITADO
        $etapasMap = [
            '00' => 'PEDIDOS DE VENDA + ORÇAMENTOS',
            '10' => 'PEDIDOS DE VENDA + ORÇAMENTOS',
            '20' => 'Separar Estoque (B2B)',
            '50' => 'Pronto para Faturamento',
            '80' => 'COTANDO FRETE', // <--- NOVA ETAPA
            '60' => 'Faturado',
            '70' => 'Pedido Cancelado'
        ];

        $clientCache = [];
        $importedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($allRecent as $omieOrder) {
                $cabecalho = $omieOrder['cabecalho'];
                
                // Filtro: Pula apenas faturados (60) ou cancelados (70)
                if ($cabecalho['etapa'] == '60' || $cabecalho['etapa'] == '70' || ($omieOrder['infoCadastro']['cancelado'] ?? 'N') == 'S') {
                    continue;
                }

                $customerId = $cabecalho['codigo_cliente'];

                if (!isset($clientCache[$customerId])) {
                    $c = $this->omieService->getClient($customerId);
                    $clientCache[$customerId] = [
                        'fantasia' => $c['nome_fantasia'] ?? 'N/D',
                        'razao'    => $c['razao_social'] ?? 'N/D',
                        'cnpj'     => $c['cnpj_cpf'] ?? '',
                        'cep'      => $c['cep'] ?? '',      
                        'cidade'   => $c['cidade'] ?? '',
                        'uf'       => $c['estado'] ?? ''
                    ];
                }

                $entrega = $omieOrder['entrega'] ?? [];
                $nomeEtapa = $etapasMap[$cabecalho['etapa']] ?? 'Etapa ' . $cabecalho['etapa'];

                $order = LogisticOrder::updateOrCreate(
                    ['omie_order_id' => $cabecalho['codigo_pedido'], 'main_company_id' => auth()->user()->main_company_id],
                    [
                        'order_number'      => $cabecalho['numero_pedido'],
                        'omie_stage'        => $nomeEtapa,
                        'client_name'       => $clientCache[$customerId]['fantasia'],
                        'client_full_name'  => $clientCache[$customerId]['razao'],
                        'client_cnpj'       => $clientCache[$customerId]['cnpj'],
                        'client_cep'        => $entrega['cep']    ?? $clientCache[$customerId]['cep'],
                        'client_city'       => $entrega['cidade'] ?? $clientCache[$customerId]['cidade'],
                        'client_uf'         => $entrega['estado'] ?? $clientCache[$customerId]['uf'],
                        'merchandise_total' => $omieOrder['total_pedido']['valor_mercadorias'] ?? 0,
                        'order_total'       => $omieOrder['total_pedido']['valor_total_pedido'] ?? 0,
                        'total_weight'      => $omieOrder['frete']['peso_bruto'] ?? 0,
                        'shipping_type'     => ($omieOrder['frete']['modalidade'] ?? '9') == '0' ? 'CIF' : 'FOB',
                        'shipping_value'    => $omieOrder['frete']['valor_frete'] ?? 0,
                        'omie_obs'          => $omieOrder['observacoes']['obs_venda'] ?? '',
                        'detected_rule'     => $this->detectRule($omieOrder['observacoes']['obs_venda'] ?? ''),
                        'status'            => 'importado'
                    ]
                );

                $order->items()->delete();
                foreach ($omieOrder['det'] as $item) {
                    $order->items()->create([
                        'product_code' => $item['produto']['codigo'],
                        'description'  => $item['produto']['descricao'],
                        'quantity'     => $item['produto']['quantidade'],
                        'unit_value'   => $item['produto']['valor_unitario'],
                        'total_value'  => $item['produto']['valor_total'],
                    ]);
                }
                $importedCount++;
            }

            DB::commit();
            return back()->with('success', "Sync ok! {$importedCount} pedidos no Kanban.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    private function detectRule($obs) {
        $obs = strtoupper($obs);
        $rules = LogisticRule::where('main_company_id', auth()->user()->main_company_id)->get();
        foreach ($rules as $rule) {
            if (str_contains($obs, strtoupper($rule->term))) return $rule->term;
        }
        if (str_contains($obs, 'F.C')) return 'F.C';
        if (str_contains($obs, 'F.U')) return 'F.U';
        return null;
    }
}