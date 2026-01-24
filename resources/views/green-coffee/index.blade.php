@extends('layouts.app')

@section('title', 'Estoque Verde')

@section('content')
<style>
    /* PADRONIZAÇÃO E ALINHAMENTO */
    .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border-radius: 8px; border: 1px solid #dee2e6; background: white; text-decoration: none; cursor: pointer; }
    .btn-action:hover { transform: scale(1.1); color: inherit; }
    .card-action { transition: transform 0.2s, box-shadow 0.2s; border: none !important; cursor: pointer; border-radius: 12px; }
    .card-action:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important; }
    
    .op-block { border: 2px solid #dee2e6; border-radius: 15px; padding: 0.8rem; background: #fff; margin-bottom: 0.5rem; }
    
    .map-container-side {
        background-color: #fff;
        border: 2px solid #dee2e6;
        border-radius: 20px;
        padding: 8px;
        aspect-ratio: 1 / 1; 
        width: 100%;
        max-width: 380px; 
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-left: auto;
        pointer-events: none;
    }

    .stat-card-kpi { height: 105px; border-radius: 12px; display: flex; flex-direction: column; justify-content: center; padding: 1rem !important; }
    
    .btn-main-receive { 
        background-color: #198754 !important; 
        color: white !important;
        height: 105px; border-radius: 12px; font-weight: 800; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; border: none;
    }

    .btn-main-inventory { 
        background-color: #ffc107 !important; 
        color: #212529 !important;
        height: 105px; border-radius: 12px; font-weight: 800; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; border: none;
    }

    /* ESTILO DO NOVO BOTÃO DE SAÍDA */
    .btn-main-exit { 
        background-color: #dc3545 !important; 
        color: white !important;
        height: 105px; border-radius: 12px; font-weight: 800; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; border: none;
    }

    .feed-title { font-size: 0.65rem; font-weight: 800; color: #6c757d; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px; }
    .feed-item { font-size: 0.7rem; padding: 3px 0; border-bottom: 1px solid #f1f1f1; display: flex; justify-content: space-between; align-items: center; }

    .table-no-hover tbody tr:hover { background-color: rgba(191, 163, 126, 0.05) !important; }
    .pallet-highlight { stroke: #ffc107 !important; stroke-width: 15 !important; filter: drop-shadow(0 0 15px gold); }

    /* REGRAS DE IMPRESSÃO */
    @media print {
        .no-print, .btn-close, .modal-header .btn { display: none !important; }
        body * { visibility: hidden; }
        body.modal-open #modalHistoricoDetalhe, 
        body.modal-open #modalHistoricoDetalhe * { visibility: visible; }
        body.modal-open #modalHistoricoDetalhe { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
        body:not(.modal-open) #printable-table, 
        body:not(.modal-open) #printable-table * { visibility: visible; }
        body:not(.modal-open) #printable-table { position: absolute; left: 0; top: 0; width: 100%; }
        .card { border: none !important; box-shadow: none !important; }
        .modal-content { border: none !important; box-shadow: none !important; }
        .modal-backdrop { display: none !important; }
    }
</style>

<div class="fade-in px-3 pt-3">
    <div class="row g-3 align-items-start no-print">
        <div class="col-lg-8">
            <div class="op-block mb-2 shadow-sm py-2">
                <h2 class="h5 fw-bold mb-0" style="color: var(--coffee-brown);">Gestão de Estoque Verde</h2>
            </div>

            {{-- LINHA DIVIDIDA EM 5 COLUNAS --}}
            <div class="row g-2 mb-2">
                <div class="col">
                    <div class="card stat-card-kpi shadow-sm text-white border-0" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Sacas</small>
                        <div class="h3 fw-bold mb-0">{{ $lotes->sum('quantity_bags') }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="card stat-card-kpi shadow-sm text-white border-0" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Peso Total</small>
                        <div class="h3 fw-bold mb-0">{{ number_format($lotes->sum('total_weight'), 0, ',', '.') }}<small class="h6">kg</small></div>
                    </div>
                </div>
                <div class="col">
                    @if(auth()->user()->hasPermission('green_coffee_full'))
                        <button class="btn btn-main-receive w-100 card-action shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRecebimento">
                            <i class="bi bi-plus-circle-fill fs-3"></i>
                            <span class="small">RECEBIMENTO</span>
                        </button>
                    @else
                        <button class="btn btn-main-receive w-100 card-action shadow-sm opacity-50" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                            <i class="bi bi-lock-fill fs-3"></i>
                            <span class="small">RECEBIMENTO</span>
                        </button>
                    @endif
                </div>
                <div class="col">
                    @if(auth()->user()->hasPermission('green_coffee_full'))
                        <button class="btn btn-main-inventory w-100 card-action shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInventario">
                            <i class="bi bi-clipboard-check-fill fs-3"></i>
                            <span class="small">INVENTÁRIO</span>
                        </button>
                    @else
                        <button class="btn btn-main-inventory w-100 card-action shadow-sm opacity-50" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                            <i class="bi bi-lock-fill fs-3"></i>
                            <span class="small">INVENTÁRIO</span>
                        </button>
                    @endif
                </div>
                {{-- NOVO BOTÃO: REGISTRAR SAÍDA --}}
                <div class="col">
                    @if(auth()->user()->hasPermission('green_coffee_full'))
                        <button class="btn btn-main-exit w-100 card-action shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSaida">
                            <i class="bi bi-box-arrow-up fs-3"></i>
                            <span class="small">REGISTRAR SAÍDA</span>
                        </button>
                    @else
                        <button class="btn btn-main-exit w-100 card-action shadow-sm opacity-50" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                            <i class="bi bi-lock-fill fs-3"></i>
                            <span class="small">REGISTRAR SAÍDA</span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="op-block shadow-sm h-100" style="min-height: 90px;">
                        <div class="feed-title" onclick="openMovimentacaoHistorico('entrada')" style="cursor: pointer;"><i class="bi bi-arrow-down-circle-fill text-success"></i> Últimos Recebimentos (Ver tudo)</div>
                        {{-- LOOP DE RECEBIMENTOS AJUSTADO --}}
                        @foreach($lotes->sortByDesc('created_at')->take(2) as $recente)
                            <div class="feed-item">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <span class="text-truncate me-2 fw-bold">{{ $recente->reference }}</span>
                                    <small class="text-muted" style="font-size: 0.6rem;">{{ $recente->created_at->copy()->subHours(3)->format('d/m/Y - H:i:s') }}</small>
                                </div>
                                <span class="fw-bold text-nowrap text-success">+{{ number_format($recente->total_weight, 1, ',', '.') }}kg</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="op-block shadow-sm h-100" style="min-height: 90px;">
                        <div class="feed-title" onclick="openMovimentacaoHistorico('saida')" style="cursor: pointer;"><i class="bi bi-arrow-up-circle-fill text-danger"></i> Últimas Saídas (Ver tudo)</div>
                        {{-- LOOP DE SAÍDAS AJUSTADO --}}
                        @isset($ultimasSaidas)
                            @foreach($ultimasSaidas as $saida)
                                <div class="feed-item">
                                    <div class="d-flex align-items-center overflow-hidden flex-grow-1">
                                        <span class="text-truncate me-2 fw-bold">{{ $saida->lote->reference ?? 'Lote' }}</span>
                                        <small class="text-muted me-2" style="font-size: 0.6rem;">{{ $saida->created_at->copy()->subHours(3)->format('d/m/Y - H:i:s') }}</small>
                                        
                                        {{-- AJUSTE AQUI: Aumentado o max-width e adicionado um title para ver o texto completo ao passar o mouse --}}
                                        <small class="text-truncate text-muted opacity-75" 
                                            style="font-size: 0.55rem; max-width: 130px;" 
                                            title="{{ $saida->reason }}">
                                            {{ $saida->reason }}
                                        </small>
                                    </div>
                                    <span class="fw-bold text-danger text-nowrap ms-2">-{{ number_format($saida->weight, 1, ',', '.') }}kg</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-2 small text-muted italic">Nenhuma saída recente registrada.</div>
                        @endisset
                    </div>
                </div>
            </div>

            <div class="op-block shadow-sm">
                <form action="{{ route('estoque.index') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Busca</label>
                        <input type="text" name="search" class="form-control form-control-sm shadow-none" placeholder="Fornecedor ou Lote..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Perfil</label>
                        <select name="profile" class="form-select form-select-sm shadow-none">
                            <option value="">Todos</option>
                            @foreach($perfis as $perfil)
                                <option value="{{ $perfil->id }}" {{ request('profile') == $perfil->id ? 'selected' : '' }}>{{ $perfil->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark flex-grow-1 fw-bold">APLICAR</button>
                        <button type="button" onclick="window.print()" class="btn btn-sm btn-danger fw-bold px-3">
                            <i class="bi bi-printer me-1"></i> RELATÓRIO
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="map-container-side shadow-sm">
                {{-- ALTERAÇÃO DINÂMICA: Busca o arquivo na pasta maps baseado no mapView da empresa --}}
                @include('green-coffee.maps.' . $mapView, ['mapData' => $mapData])
            </div>
        </div>
    </div>

    {{-- TABELA DE LOTES GERAL --}}
    <div id="printable-table" class="card shadow-sm border-0 p-4 mt-2" style="border-radius: 15px;">
        <h5 class="fw-bold mb-4" style="color: var(--coffee-brown); font-size: 1rem;">Lotes Armazenados - Cafés Especiais</h5>
        <div class="table-responsive">
            <table class="table align-middle table-no-hover">
                <thead class="table-light">
                    <tr class="small text-uppercase text-muted">
                        <th>Local</th><th>Referência</th><th>Fornecedor</th><th>Perfil</th><th>Peso Total</th><th class="text-center no-print">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($lotes as $lote)
                    @php $lotCoords = $lote->locations->map(fn($l) => strtoupper($l->address_row . $l->address_pos))->toJson(); @endphp
                    <tr onmouseenter="highlightOnMap({{ $lotCoords }})" onmouseleave="clearMapHighlight()">
                        <td>
                            @foreach($lote->locations as $loc)
                                <span class="badge bg-secondary mb-1" style="font-size: 0.7rem;">{{ $loc->address_row }}{{ $loc->address_pos }}</span>
                            @endforeach
                        </td>
                        <td class="fw-bold">{{ $lote->reference }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $lote->origin }}</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">{{ $lote->supplier_type ?? 'Produtor' }}</div>
                        </td>
                        <td>
                            @if($lote->sensoryProfile)
                                <span class="badge rounded-pill border text-dark fw-normal" style="background-color: {{ $lote->sensoryProfile->color_hex }}15; border-color: {{ $lote->sensoryProfile->color_hex }};">
                                    {{ $lote->sensoryProfile->name }}
                                </span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ number_format($lote->total_weight, 1, ',', '.') }}kg</td>
                        <td class="text-center no-print">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn-action text-info" data-bs-toggle="modal" data-bs-target="#modalInfo{{ $lote->id }}" title="Informações">
                                    <i class="bi bi-info-circle-fill"></i>
                                </button>
                                <button type="button" class="btn-action text-secondary" data-bs-toggle="modal" data-bs-target="#modalAnexos{{ $lote->id }}" title="Gerenciar Anexos">
                                    <i class="bi bi-paperclip"></i>
                                </button>
                                
                                @if(auth()->user()->hasPermission('green_coffee_full'))
                                    <form action="{{ route('estoque.destroy', $lote->id) }}" method="POST" onsubmit="return confirm('Excluir este lote permanentemente?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action text-danger" title="Excluir Lote">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn-action text-muted" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted small italic">Nenhum lote localizado no armazém.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DE HISTÓRICO DE MOVIMENTAÇÃO (NOVA LÓGICA) --}}
<div class="modal fade" id="modalHistoricoMovimentacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-light border-0 py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="fw-bold mb-0">Histórico de Movimentação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-2 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">DATA INICIAL</label>
                        <input type="date" id="hist_start_date" class="form-control form-control-sm border-0 bg-light">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">DATA FINAL</label>
                        <input type="date" id="hist_end_date" class="form-control form-control-sm border-0 bg-light">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">ATALHO</label>
                        <select class="form-select form-select-sm border-0 bg-light fw-bold" id="hist_quick_filter" onchange="applyQuickFilterHist(this.value)">
                            <option value="last_week">Última Semana</option>
                            <option value="curr_month">Mês Atual</option>
                            <option value="last_month">Último Mês</option>
                            <option value="all">Tudo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button onclick="refreshHistorico()" class="btn btn-dark btn-sm w-100 fw-bold">APLICAR</button>
                    </div>
                </div>

                <div class="btn-group w-100 mb-4 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <button id="btn-hist-entrada" onclick="changeHistType('entrada')" class="btn btn-success fw-bold">📈 RECEBIMENTOS</button>
                    <button id="btn-hist-saida" onclick="changeHistType('saida')" class="btn btn-outline-danger fw-bold">📉 SAÍDAS</button>
                </div>

                <div style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                    <table class="table table-sm align-middle table-borderless">
                        <thead class="sticky-top bg-white">
                            <tr class="text-muted small text-uppercase" style="font-size: 0.65rem; border-bottom: 2px solid #f1f1f1;">
                                <th>Data/Horário</th>
                                <th>Lote / Perfil</th>
                                <th>Local</th>
                                <th class="text-end">Quantidade</th>
                            </tr>
                        </thead>
                        <tbody id="hist-body">
                            {{-- Preenchido via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function highlightOnMap(coords) {
        coords.forEach(c => {
            const slot = document.querySelector(`#pallet-${c} .pallet-rect`);
            if (slot) slot.classList.add('pallet-highlight');
        });
    }
    function clearMapHighlight() {
        document.querySelectorAll('.pallet-rect').forEach(p => p.classList.remove('pallet-highlight'));
    }

    window.viewInventoryDetail = function(id) {
        $.ajax({
            url: `/green-coffee/inventory/history/${id}`,
            method: 'GET',
            success: function(data) {
                $('#det-id').text(data.session.id);
                $('#det-user').text(data.session.user ? data.session.user.name : 'Sistema');
                if(data.session.inventory_date) {
                    const partes = data.session.inventory_date.split('-');
                    $('#det-data').text(`${partes[2]}/${partes[1]}/${partes[0]}`);
                }
                let html = '';
                data.adjustments.forEach(adj => {
                    const diffCol = adj.difference_kg < 0 ? 'text-danger' : (adj.difference_kg > 0 ? 'text-primary' : 'text-success');
                    html += `
                        <tr>
                            <td><small class="fw-bold">${adj.reference}</small></td>
                            <td class="text-center small">${parseFloat(adj.system_weight).toFixed(1)}kg</td>
                            <td class="text-center small">${parseFloat(adj.physical_weight).toFixed(1)}kg</td>
                            <td class="text-center small fw-bold ${diffCol}">${parseFloat(adj.difference_kg).toFixed(1)}kg</td>
                            <td class="small text-muted italic">${adj.reason || '-'}</td>
                        </tr>`;
                });
                $('#det-body').html(html);
                var myModal = new bootstrap.Modal(document.getElementById('modalHistoricoDetalhe'));
                myModal.show();
            },
            error: function() {
                alert('Erro ao carregar detalhes.');
            }
        });
    };

    /* NOVA LÓGICA: HISTÓRICO DE MOVIMENTAÇÃO AJAX */
    let currentHistType = 'entrada';

    function openMovimentacaoHistorico(type) {
        currentHistType = type;
        applyQuickFilterHist('last_week'); 
        var myModal = new bootstrap.Modal(document.getElementById('modalHistoricoMovimentacao'));
        myModal.show();
        refreshHistorico();
    }

    function changeHistType(type) {
        currentHistType = type;
        const btnE = $('#btn-hist-entrada');
        const btnS = $('#btn-hist-saida');
        if(type === 'entrada') {
            btnE.removeClass('btn-outline-success').addClass('btn-success');
            btnS.removeClass('btn-danger').addClass('btn-outline-danger');
        } else {
            btnE.removeClass('btn-success').addClass('btn-outline-success');
            btnS.removeClass('btn-outline-danger').addClass('btn-danger');
        }
        refreshHistorico();
    }

    function applyQuickFilterHist(value) {
        const startInput = document.getElementById('hist_start_date');
        const endInput = document.getElementById('hist_end_date');
        const now = new Date();
        let start = new Date(), end = new Date();

        switch(value) {
            case 'last_week': start.setDate(now.getDate() - 7); break;
            case 'curr_month': start = new Date(now.getFullYear(), now.getMonth(), 1); break;
            case 'last_month': start = new Date(now.getFullYear(), now.getMonth() - 1, 1); end = new Date(now.getFullYear(), now.getMonth(), 0); break;
            case 'all': start = new Date(2020, 0, 1); break;
        }
        startInput.value = start.toISOString().split('T')[0];
        endInput.value = end.toISOString().split('T')[0];
    }

    async function refreshHistorico() {
        const start = $('#hist_start_date').val();
        const end = $('#hist_end_date').val();
        const body = $('#hist-body');
        body.html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border spinner-border-sm text-secondary"></div></td></tr>');
        try {
            const response = await fetch(`/green-coffee/movimentacao-historico?tipo=${currentHistType}&start_date=${start}&end_date=${end}`);
            const data = await response.json();
            let html = '';
            if(data.length === 0) {
                html = '<tr><td colspan="4" class="text-center py-5 text-muted small">Nenhuma movimentação encontrada.</td></tr>';
            } else {
                data.forEach(item => {
                    const isSaida = item.tipo === 'saida';
                    html += `
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td><div class="fw-bold" style="font-size: 0.75rem;">${item.data}</div></td>
                            <td>
                                <div class="fw-bold small">${item.ref}</div>
                                <span class="badge rounded-pill" style="font-size: 0.6rem; background-color: ${item.cor}15; color: ${item.cor}; border: 1px solid ${item.cor};">${item.perfil}</span>
                            </td>
                            <td><span class="badge bg-light text-dark border" style="font-size: 0.65rem;">${item.endereco}</span></td>
                            <td class="text-end fw-bold ${isSaida ? 'text-danger' : 'text-success'}" style="font-size: 0.85rem;">
                                ${isSaida ? '-' : '+'}${parseFloat(item.peso).toLocaleString('pt-BR', {minimumFractionDigits: 1})}kg
                            </td>
                        </tr>`;
                });
            }
            body.html(html);
        } catch(e) { body.html('<tr><td colspan="4" class="text-center py-5 text-danger small">Erro ao carregar dados.</td></tr>'); }
    }
</script>

@include('green-coffee.partials.modal_create_lote')
@foreach($lotes as $lote)
    @include('green-coffee.partials.modal_anexos_lote', ['lote' => $lote])     
    @include('green-coffee.partials.modal_info_lote', ['lote' => $lote])
@endforeach

{{-- MODAIS GLOBAIS --}}
@include('green-coffee.partials.modal_inventario')
@include('green-coffee.partials.modal_detalhe_inventario')
@include('green-coffee.partials.modal_saidas')

@endsection