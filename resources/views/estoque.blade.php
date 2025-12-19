@extends('layouts.app')

@section('title', 'Estoque Verde')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold">Gestão de Estoque Verde</h2>
        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Voltar ao Painel</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 text-white" style="background: var(--coffee-brown); min-height: 110px;">
                        <small class="text-uppercase opacity-75" style="color: var(--coffee-gold); font-size: 0.7rem; font-weight: bold;">Sacas em Estoque</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">842</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 text-white" style="background: var(--coffee-brown); min-height: 110px;">
                        <small class="text-uppercase opacity-75" style="color: var(--coffee-gold); font-size: 0.7rem; font-weight: bold;">Peso Total</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">50.520kg</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-success" style="min-height: 110px; cursor: pointer; background: white;">
                        <i class="bi bi-plus-circle-fill fs-2"></i>
                        <span class="small fw-bold mt-1">RECEBIMENTO</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-primary" style="min-height: 110px; cursor: pointer; background: white;">
                        <i class="bi bi-pencil-square fs-2"></i>
                        <span class="small fw-bold mt-1">INVENTÁRIO</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-danger" style="min-height: 110px; cursor: pointer; background: white;">
                        <i class="bi bi-file-earmark-bar-graph-fill fs-2"></i>
                        <span class="small fw-bold mt-1">RELATÓRIOS</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 d-flex flex-column align-items-center justify-content-center text-muted" style="min-height: 110px; cursor: pointer; background: white;">
                        <i class="bi bi-search fs-2"></i>
                        <span class="small fw-bold mt-1">FILTRAR</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="d-flex align-items-center justify-content-center text-center p-4 h-100" style="background: white; border: 2px dashed var(--coffee-gold); border-radius: 12px; min-height: 235px;">
                <div>
                    <i class="bi bi-map fs-1 opacity-25 d-block mb-2"></i>
                    <p class="mb-0 fw-bold opacity-50">Mapa do Armazém</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4 shadow-sm border-0 p-4" style="border-radius: 12px; background: white;">
        <h5 class="fw-bold mb-4" style="color: var(--coffee-brown);">Lotes Armazenados</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr class="small text-uppercase text-muted">
                        <th>ID Lote</th>
                        <th>Produtor</th>
                        <th>Variedade</th>
                        <th>Peso</th>
                        <th>Local</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>#2024-001</strong></td>
                        <td>Fazenda Santa Maria</td>
                        <td>Bourbon Amarelo</td>
                        <td>6.000kg</td>
                        <td><span class="badge bg-secondary">Setor A</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold" style="font-size: 0.7rem;">RETIRAR AMOSTRA</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection