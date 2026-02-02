@extends('layouts.app')

@section('title', 'Logística Unique - Kanban Omie')

@section('content')
<div class="container-fluid py-4">
    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--coffee-brown);">
                <i class="bi bi-truck me-2"></i>Logística Omie
            </h2>
            <p class="text-muted mb-0">Monitorando <strong>{{ $orders->count() }}</strong> documentos ativos no Kanban.</p>
        </div>

        <form action="{{ route('logistics.sync') }}" method="POST" id="formSync">
            @csrf
            <button type="submit" class="btn btn-dark fw-bold shadow-sm px-4" id="btnSync" style="background-color: var(--coffee-brown); border: none; border-radius: 10px;">
                <i class="bi bi-arrow-repeat me-1" id="iconSync"></i> Sincronizar Kanban
            </button>
        </form>
    </div>

    {{-- ALERTAS DE STATUS --}}
    @if(session('success')) 
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div> 
    @endif
    @if(session('error')) 
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        </div> 
    @endif

    {{-- TABELA DE PEDIDOS --}}
    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">PEDIDO / ID</th>
                            <th>CLIENTE / DOCUMENTO</th>
                            <th>DESTINO</th>
                            <th>VALORES (MERC. / TOTAL)</th>
                            <th class="text-center">PESO / GATILHO</th>
                            <th class="text-center">ETAPA KANBAN</th>
                            <th class="pe-4 text-end">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            {{-- Coluna Pedido --}}
                            <td class="ps-4">
                                <span class="badge bg-dark mb-1" style="font-size: 0.9rem;">#{{ $order->order_number }}</span>
                                <div class="text-muted" style="font-size: 0.65rem;">Ref: {{ $order->omie_order_id }}</div>
                            </td>

                            {{-- Coluna Cliente --}}
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="{{ $order->client_full_name }}">
                                    {{ $order->client_name }}
                                </div>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    <i class="bi bi-card-text me-1"></i>{{ $order->client_cnpj }}
                                </div>
                            </td>

                            {{-- Coluna Destino --}}
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-light text-dark border w-fit mb-1">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $order->client_cep ?: 'S/ CEP' }}
                                    </span>
                                    <div class="text-dark small fw-bold" style="font-size: 0.75rem;">
                                        {{ $order->client_city }} / {{ $order->client_uf }}
                                    </div>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ strtoupper($order->shipping_type) }}</span>
                                </div>
                            </td>

                            {{-- Coluna Financeiro --}}
                            <td>
                                <div class="small">
                                    <span class="text-muted">Merc:</span> R$ {{ number_format($order->merchandise_total, 2, ',', '.') }}<br>
                                    <span class="fw-bold" style="color: var(--coffee-brown);">Total: R$ {{ number_format($order->order_total, 2, ',', '.') }}</span>
                                </div>
                            </td>

                            {{-- Coluna Peso/Gatilho --}}
                            <td class="text-center">
                                <div class="fw-bold text-dark">{{ number_format($order->total_weight, 3, ',', '.') }} kg</div>
                                @php
                                    $ruleClass = match($order->detected_rule) {
                                        'F.U' => 'bg-warning text-dark',
                                        'F.C' => 'bg-primary text-white',
                                        'DEDICADO' => 'bg-info text-dark',
                                        default => 'bg-light text-muted border'
                                    };
                                @endphp
                                <span class="badge {{ $ruleClass }} px-2 py-1 mt-1 small" style="min-width: 70px;">
                                    {{ $order->detected_rule ?: 'NENHUM' }}
                                </span>
                            </td>

                            {{-- Coluna Etapa Kanban (Status Dinâmico) --}}
                            <td class="text-center">
                                @php
                                    // Determina a cor baseada no texto da etapa personalizada
                                    $stageColor = match(true) {
                                        str_contains($order->omie_stage, 'ORÇAMENTOS') => 'bg-info text-white',
                                        str_contains($order->omie_stage, 'Separar')    => 'bg-warning text-dark',
                                        str_contains($order->omie_stage, 'FRETE')      => 'bg-indigo text-white',
                                        str_contains($order->omie_stage, 'Faturado')   => 'bg-success text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge {{ $stageColor }} px-3 py-2 shadow-sm text-uppercase" style="border-radius: 8px; min-width: 160px; font-size: 0.65rem;">
                                    {{ $order->omie_stage }}
                                </span>
                            </td>

                            {{-- Coluna Ações --}}
                            <td class="pe-4 text-end">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-sm btn-white border" title="Ver Itens" 
                                            onclick="alert('Pedido #{{ $order->order_number }} possui {{ $order->items->count() }} itens registrados.')">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary fw-bold px-3" onclick="openValidationModal({{ $order->id }})">
                                        Validar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-cloud-download display-1 opacity-25"></i>
                                <p class="mt-3">Ainda não há pedidos sincronizados. Clique em <strong>Sincronizar Kanban</strong>.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- CSS ADICIONAL --}}
<style>
    .w-fit { width: fit-content; }
    .x-small { font-size: 0.65rem; }
    .btn-white { background: white; border: 1px solid #dee2e6; color: #666; }
    .btn-white:hover { background: #f8f9fa; color: #333; }
    .bg-indigo { background-color: #6610f2 !important; } {{-- Cor Roxa para Cotação --}}
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.015); transition: 0.2s; }
    .badge { font-weight: 700; letter-spacing: 0.3px; }
    .bi-arrow-repeat.spinning { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@push('scripts')
<script>
    // Gerenciamento do Loading no Botão
    document.getElementById('formSync').addEventListener('submit', function() {
        const btn = document.getElementById('btnSync');
        const icon = document.getElementById('iconSync');
        
        btn.classList.add('disabled');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sincronizando...';
        if(icon) icon.classList.add('spinning');
    });

    function openValidationModal(id) {
        console.log("Iniciando validação do registro local ID:", id);
        alert("Aguardando implementação do modal de validação para o ID " + id);
    }
</script>
@endpush
@endsection