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

    .feed-title { font-size: 0.65rem; font-weight: 800; color: #6c757d; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px; }
    .feed-item { font-size: 0.7rem; padding: 3px 0; border-bottom: 1px solid #f1f1f1; display: flex; justify-content: space-between; }

    .table-no-hover tbody tr:hover { background-color: rgba(191, 163, 126, 0.05) !important; }
    .pallet-highlight { stroke: #ffc107 !important; stroke-width: 15 !important; filter: drop-shadow(0 0 15px gold); }

    /* REGRAS DE IMPRESSÃO - CORRIGIDAS PARA SUPORTAR DOIS TIPOS DE RELATÓRIOS */
    @media print {
        .no-print, .btn-close, .modal-header .btn { display: none !important; }
        body * { visibility: hidden; }
        
        /* Se o modal de detalhe estiver aberto, imprime ele */
        body.modal-open #modalHistoricoDetalhe, 
        body.modal-open #modalHistoricoDetalhe * { 
            visibility: visible; 
        }
        body.modal-open #modalHistoricoDetalhe {
            position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0;
        }

        /* Se NÃO houver modal aberto, imprime a tabela de lotes geral */
        body:not(.modal-open) #printable-table, 
        body:not(.modal-open) #printable-table * { 
            visibility: visible; 
        }
        body:not(.modal-open) #printable-table { 
            position: absolute; left: 0; top: 0; width: 100%; 
        }

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

            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <div class="card stat-card-kpi shadow-sm text-white border-0" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Sacas</small>
                        <div class="h3 fw-bold mb-0">{{ $lotes->sum('quantity_bags') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-kpi shadow-sm text-white border-0" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Peso Total</small>
                        <div class="h3 fw-bold mb-0">{{ number_format($lotes->sum('total_weight'), 0, ',', '.') }}<small class="h6">kg</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-main-receive w-100 card-action shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRecebimento">
                        <i class="bi bi-plus-circle-fill fs-3"></i>
                        <span class="small">RECEBIMENTO</span>
                    </button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-main-inventory w-100 card-action shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInventario">
                        <i class="bi bi-clipboard-check-fill fs-3"></i>
                        <span class="small">INVENTÁRIO</span>
                    </button>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <div class="op-block shadow-sm h-100" style="min-height: 90px;">
                        <div class="feed-title"><i class="bi bi-arrow-down-circle-fill text-success"></i> Últimos Recebimentos</div>
                        @foreach($lotes->take(2) as $recente)
                            <div class="feed-item">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <span class="text-truncate me-2">{{ $recente->reference }}</span>
                                    <small class="text-muted" style="font-size: 0.6rem;">{{ $recente->created_at->format('d/m/Y') }}</small>
                                </div>
                                <span class="fw-bold text-nowrap">+{{ number_format($recente->total_weight, 1, ',', '.') }}kg</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="op-block shadow-sm h-100" style="min-height: 90px;">
                        <div class="feed-title"><i class="bi bi-arrow-up-circle-fill text-danger"></i> Últimas Saídas</div>
                        <div class="feed-item">
                            <span>Torra Diária #01</span>
                            <span class="fw-bold text-danger">-120kg</span>
                        </div>
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
                @include('green-coffee.partials.warehouse_map', ['mapData' => $mapData])
            </div>
        </div>
    </div>

    {{-- TABELA DE LOTES GERAL (REGRAS GLOBAIS DO APP.BLADE APLICADAS) --}}
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
                                <form action="{{ route('estoque.destroy', $lote->id) }}" method="POST" onsubmit="return confirm('Excluir este lote permanentemente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action text-danger" title="Excluir Lote">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
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
                
                // CORREÇÃO: Formata a data YYYY-MM-DD para DD/MM/YYYY sem usar o objeto Date (evita fuso horário)
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
</script>

@include('green-coffee.partials.modal_create_lote')
@foreach($lotes as $lote)
    @include('green-coffee.partials.modal_anexos_lote', ['lote' => $lote])     
    @include('green-coffee.partials.modal_info_lote', ['lote' => $lote])
@endforeach

{{-- MODAIS GLOBAIS - FORA DO LOOP --}}
@include('green-coffee.partials.modal_inventario')
@include('green-coffee.partials.modal_detalhe_inventario')

@endsection