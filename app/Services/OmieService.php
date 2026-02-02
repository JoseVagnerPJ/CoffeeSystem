<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OmieService
{
    protected $baseUrl = 'https://app.omie.com.br/api/v1/produtos/pedido/';
    protected $appKey;
    protected $appSecret;

    public function __construct()
    {
        $this->appKey = env('OMIE_APP_KEY');
        $this->appSecret = env('OMIE_APP_SECRET');
    }

    protected function call($call, array $param = [])
    {
        try {
            $response = Http::post($this->baseUrl, [
                'call'       => $call,
                'app_key'    => $this->appKey,
                'app_secret' => $this->appSecret,
                'param'      => [$param]
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Se falhar, registra o motivo exato no log
            Log::error("Erro Omie [{$call}]: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Falha de conexão Omie: " . $e->getMessage());
            return null;
        }
    }

public function listOrders($pagina = 1, $registros = 50)
{
    return $this->call('ListarPedidos', [
        'pagina'               => $pagina,
        'registros_por_pagina' => $registros,
        'apenas_importado_api' => 'N'
    ]);
}
    public function getClient($customerId)
    {
        $originalUrl = $this->baseUrl;
        $this->baseUrl = 'https://app.omie.com.br/api/v1/geral/clientes/';
        $result = $this->call('ConsultarCliente', ['codigo_cliente_omie' => $customerId]);
        $this->baseUrl = $originalUrl;
        return $result;
    }
}