@extends('layouts.app')

@section('title', 'Estoque Verde')

@section('content')
<div class="fade-in px-3">
    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Gestão de Estoque Verde</h2>
        <div>
            @if(session('captured_lot'))
                <span class="badge bg-warning text-dark p-2 me-2 shadow-sm border">
                    <i class="bi bi-hand-index-fill me-1"></i> Capturado: {{ session('captured_lot_name') }}
                    <a href="{{ route('estoque.clear-capture') }}" class="text-dark ms-2"><i class="bi bi-x-circle-fill"></i></a>
                </span>
            @endif
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Voltar ao Painel</a>
        </div>
    </div>

    {{-- Seção de Dashboards e Ações Clicáveis --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 text-white h-100" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75" style="color: var(--coffee-gold); font-size: 0.7rem; font-weight: bold;">Sacas em Estoque</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">{{ $lotes->sum('quantity_bags') }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 text-white h-100" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75" style="color: var(--coffee-gold); font-size: 0.7rem; font-weight: bold;">Peso Total</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">{{ number_format($lotes->sum('total_weight'), 2, ',', '.') }}kg</div>
                    </div>
                </div>

                {{-- Botões de Ação Dinâmicos --}}
                <div class="col-sm-4">
                    <button class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-success w-100 h-100" 
                         style="cursor: pointer; background: white;" data-bs-toggle="modal" data-bs-target="#modalRecebimento">
                        <i class="bi bi-plus-circle-fill fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Recebimento</span>
                    </button>
                </div>
                <div class="col-sm-4">
                    <a href="#" class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-primary w-100 h-100 text-decoration-none" style="background: white;">
                        <i class="bi bi-pencil-square fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Inventário</span>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="#" class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-danger w-100 h-100 text-decoration-none" style="background: white;">
                        <i class="bi bi-file-earmark-bar-graph-fill fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Relatórios</span>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a href="#" class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-muted w-100 h-100 text-decoration-none" style="background: white;">
                        <i class="bi bi-search fs-2"></i>
                        <span class="small fw-bold mt-1 text-uppercase">Filtrar</span>
                    </a>
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
                        <th>Qtd Sacas</th>
                        <th>Peso Total</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lotes as $lote)
                    <tr>
                        <td><span class="badge bg-secondary px-3" style="font-size: 0.8rem;">{{ $lote->address_row }}{{ $lote->address_pos }}</span></td>
                        <td><strong>{{ $lote->reference }}</strong></td>
                        <td>
                            <i class="bi bi-search small text-muted me-1"></i> {{ $lote->origin }}
                        </td>
                        <td><span class="badge border text-dark fw-normal bg-light">{{ $lote->process }}</span></td>
                        <td>{{ $lote->quantity_bags }} sacas</td>
                        <td class="fw-bold">{{ number_format($lote->total_weight, 2, ',', '.') }}kg</td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                <button class="btn btn-sm btn-white text-primary" title="Criar Amostra"><i class="bi bi-flask"></i></button>
                                <a href="{{ route('estoque.capture', $lote->id) }}" 
                                   class="btn btn-sm {{ session('captured_lot') == $lote->id ? 'btn-warning active' : 'btn-white' }}" 
                                   title="Capturar"><i class="bi bi-hand-index-fill"></i></a>
                                <button class="btn btn-sm btn-white text-success" title="Vincular"><i class="bi bi-link-45deg"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted small">Nenhum lote em estoque.</td></tr>
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
            <div class="modal-header border-0 bg-light rounded-top">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);"><i class="bi bi-box-seam me-2"></i>Novo Recebimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('estoque.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">REFERÊNCIA</label>
                            <input type="text" name="reference" class="form-control" placeholder="Ex: Lote Especial #001" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">LOCAL (C3, B4...)</label>
                            <div class="input-group">
                                <input type="text" name="address_row" class="form-control text-center" placeholder="A" maxlength="1" required>
                                <input type="number" name="address_pos" class="form-control text-center" placeholder="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">FORNECEDOR / PRODUTOR</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search opacity-50"></i></span>
                                <input type="text" name="origin" class="form-control border-start-0" placeholder="Pesquisar fornecedor..." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">PROCESSO</label>
                            <input type="text" name="process" class="form-control" placeholder="Ex: Natural / Honey / Lavado" required>
                        </div>

                        <div class="col-12"><hr class="my-2 opacity-10"></div>

                        {{-- CALCULADORA COM 2 DECIMAIS --}}
                        <div class="col-md-4 text-center">
                            <label class="form-label small fw-bold text-success">QTD SACAS</label>
                            <input type="number" id="calc_sacas" name="quantity_bags" class="form-control form-control-lg text-center fw-bold border-success shadow-sm" required>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="form-label small fw-bold text-success">PESO UNIT. (KG)</label>
                            <input type="number" id="calc_peso_unit" name="unit_weight" class="form-control form-control-lg text-center fw-bold border-success shadow-sm" step="0.01" value="60.00" required>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="form-label small fw-bold text-primary">PESO TOTAL (KG)</label>
                            <input type="number" id="calc_peso_total" name="total_weight" class="form-control form-control-lg text-center fw-bold border-primary bg-light" step="0.01" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4" style="background: var(--coffee-brown);">SALVAR LOTE</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT DA CALCULADORA COM 2 DECIMAIS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputSacas = document.getElementById('calc_sacas');
        const inputPesoUnit = document.getElementById('calc_peso_unit');
        const inputPesoTotal = document.getElementById('calc_peso_total');

        function calcularEstoque() {
            const sacas = parseFloat(inputSacas.value) || 0;
            const peso = parseFloat(inputPesoUnit.value) || 0;
            // Garantindo 2 casas decimais na lógica do JS
            inputPesoTotal.value = (sacas * peso).toFixed(2);
        }

        inputSacas.addEventListener('input', calcularEstoque);
        inputPesoUnit.addEventListener('input', calcularEstoque);
    });
</script>
@endsection