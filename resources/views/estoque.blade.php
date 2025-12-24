@extends('layouts.app')

@section('title', 'Estoque Verde')

@section('content')
<style>
    /* Padronização dos botões de ação na tabela */
    .btn-action {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.2s;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
    .btn-group-custom {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
    /* Efeito de clique nos cards superiores */
    .card-action {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none !important;
    }
    .card-action:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>

<div class="fade-in px-3">
    {{-- Cabeçalho e Lote Capturado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Gestão de Estoque Verde</h2>
        <div>
            @if(session('captured_lot'))
                <div class="badge bg-white text-dark p-2 me-2 shadow-sm border border-warning animate-fade-in" style="border-left: 4px solid #ffc107 !important;">
                    <i class="bi bi-hand-index-fill text-warning me-1"></i> 
                    <small class="text-muted fw-normal">Capturado:</small> {{ session('captured_lot_name') }}
                    <a href="{{ route('estoque.clear-capture') }}" class="text-danger ms-2" title="Soltar Lote"><i class="bi bi-x-circle-fill"></i></a>
                </div>
            @endif
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Voltar ao Painel</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm animate-fade-in py-2 small d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Seção de Dashboards e Ações --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm text-white h-100 border-0" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75" style="color: var(--coffee-gold); font-size: 0.7rem; font-weight: bold;">Sacas em Estoque</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">{{ $lotes->sum('quantity_bags') }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm text-white h-100 border-0" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75" style="color: var(--coffee-gold); font-size: 0.7rem; font-weight: bold;">Peso Total</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">{{ number_format($lotes->sum('total_weight'), 2, ',', '.') }}<small style="font-size: 0.5em;">kg</small></div>
                    </div>
                </div>

                {{-- Botões de Ação --}}
                <div class="col-sm-4">
                    <button class="card p-3 shadow-sm d-flex flex-column align-items-center justify-content-center text-success w-100 h-100 card-action" 
                         style="background: white;" data-bs-toggle="modal" data-bs-target="#modalRecebimento">
                        <i class="bi bi-plus-circle-fill fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Recebimento</span>
                    </button>
                </div>
                <div class="col-sm-4">
                    <button class="card p-3 shadow-sm d-flex flex-column align-items-center justify-content-center text-primary w-100 h-100 card-action" style="background: white;">
                        <i class="bi bi-pencil-square fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Inventário</span>
                    </button>
                </div>
                <div class="col-sm-4">
                    <button class="card p-3 shadow-sm d-flex flex-column align-items-center justify-content-center text-danger w-100 h-100 card-action" style="background: white;">
                        <i class="bi bi-file-earmark-bar-graph-fill fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Relatórios</span>
                    </button>
                </div>
                <div class="col-sm-4">
                    <button class="card p-3 shadow-sm d-flex flex-column align-items-center justify-content-center text-muted w-100 h-100 card-action" style="background: white;">
                        <i class="bi bi-search fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Filtrar</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="d-flex align-items-center justify-content-center text-center p-4 h-100" 
                 style="background: white; border: 2px dashed var(--coffee-gold); border-radius: 12px; min-height: 235px;">
                <div>
                    <i class="bi bi-map fs-1 opacity-25 d-block mb-2"></i>
                    <p class="mb-0 fw-bold opacity-50">Mapa do Armazém</p>
                    <small class="text-muted">Interação visual em breve</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabela de Lotes --}}
    <div class="card mt-4 shadow-sm border-0 p-4" style="border-radius: 12px; background: white;">
        <h5 class="fw-bold mb-4" style="color: var(--coffee-brown);">Lotes Armazenados</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr class="small text-uppercase text-muted">
                        <th>Local</th>
                        <th>Referência</th>
                        <th>Fornecedor/Produtor</th>
                        <th>Processo</th>
                        <th>Sacas</th>
                        <th>Peso Total</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($lotes as $lote)
                <tr>
                    <td><span class="badge bg-secondary px-3" style="font-size: 0.8rem;">{{ $lote->address_row }}{{ $lote->address_pos }}</span></td>
                    <td><strong>{{ $lote->reference }}</strong></td>
                    <td><i class="bi bi-search small text-muted me-1"></i> {{ $lote->origin }}</td>
                    <td><span class="badge border text-dark fw-normal bg-light">{{ $lote->process }}</span></td>
                    <td>{{ $lote->quantity_bags }} sacas</td>
                    <td class="fw-bold">{{ number_format($lote->total_weight, 2, ',', '.') }}kg</td>
                    <td class="text-center">
                        <div class="btn-group-custom">
                            {{-- Botão Informações (Onde ficará tudo o que removemos da frente) --}}
                            <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" 
                                    data-bs-toggle="modal" data-bs-target="#modalInfo{{ $lote->id }}" title="Ver Detalhes">
                                <i class="bi bi-info-circle-fill"></i>
                            </button>
                            
                            {{-- Capturar (Mão Aberta 🖐️) --}}
                            <a href="{{ route('estoque.capture', $lote->id) }}" 
                            class="btn btn-sm btn-action border shadow-sm {{ session('captured_lot') == $lote->id ? 'btn-warning active border-warning' : 'btn-white' }}" 
                            title="Capturar Lote">
                                <i class="bi bi-hand-index-thumb"></i> {{-- Mão pronta para pegar --}}
                            </a>

                            {{-- Vincular (Clip 📎) --}}
                            <button class="btn btn-sm btn-white border btn-action text-success shadow-sm" title="Vincular">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- MODAL DE DETALHES DO LOTE (Oculto, abre no "i") --}}
                @include('partials.modal_info_lote', ['lote' => $lote])

                @empty
                <tr><td colspan="7" class="text-center py-5 text-muted small">Nenhum lote em estoque.</td></tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DE RECEBIMENTO --}}
<div class="modal fade" id="modalRecebimento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-light rounded-top py-3">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);"><i class="bi bi-box-seam me-2"></i>Novo Recebimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('estoque.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">REFERÊNCIA</label>
                            <input type="text" name="reference" class="form-control shadow-sm" placeholder="Ex: Catuaí Vermelho Lote 10" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">LOCAL NO ARMAZÉM</label>
                            <div class="input-group shadow-sm">
                                <input type="text" name="address_row" class="form-control text-center fw-bold" placeholder="Fila" maxlength="1" required>
                                <input type="number" name="address_pos" class="form-control text-center fw-bold" placeholder="Nº" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">FORNECEDOR / PRODUTOR</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search opacity-50"></i></span>
                                <input type="text" name="origin" class="form-control border-start-0" placeholder="Pesquisar..." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">PROCESSO</label>
                            <input type="text" name="process" class="form-control shadow-sm" placeholder="Ex: Natural / Cereja Descascado" required>
                        </div>

                        <div class="col-12"><hr class="my-2 opacity-10"></div>

                        {{-- CALCULADORA --}}
                        <div class="col-md-4 text-center">
                            <label class="form-label small fw-bold text-success">QUANTIDADE (SACAS)</label>
                            <input type="number" id="js_sacas" name="quantity_bags" class="form-control form-control-lg text-center fw-bold border-success shadow-sm" required>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="form-label small fw-bold text-success">PESO UNITÁRIO (KG)</label>
                            <input type="number" id="js_peso_unit" name="unit_weight" class="form-control form-control-lg text-center fw-bold border-success shadow-sm" step="0.01" value="60.00" required>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="form-label small fw-bold text-primary">PESO TOTAL CALCULADO</label>
                            <input type="number" id="js_peso_total" name="total_weight" class="form-control form-control-lg text-center fw-bold border-primary bg-light" step="0.01" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 shadow" style="background: var(--coffee-brown);">CONFIRMAR ENTRADA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inSacas = document.getElementById('js_sacas');
        const inUnit = document.getElementById('js_peso_unit');
        const inTotal = document.getElementById('js_peso_total');

        function atualizarCalculo() {
            const total = (parseFloat(inSacas.value) || 0) * (parseFloat(inUnit.value) || 0);
            inTotal.value = total.toFixed(2); // Duas casas decimais no cálculo
        }

        inSacas.addEventListener('input', atualizarCalculo);
        inUnit.addEventListener('input', atualizarCalculo);
    });
</script>
@endsection