@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
        <h2 class="h4 fw-bold">Painel de Controle Real-Time</h2>
        <div class="input-group" style="width: 250px;">
            <input type="text" class="form-control form-control-sm" placeholder="Buscar ID (Rastrear)...">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm d-flex flex-row overflow-hidden" style="border-radius: 12px; background: white;">
                <div class="d-flex align-items-center justify-content-center" style="width: 70px; background-color: var(--coffee-gold); color: var(--coffee-brown);">
                    <i class="bi bi-gear-wide-connected fs-2"></i>
                </div>
                <div class="p-3 flex-grow-1">
                    <div class="text-muted small text-uppercase">Torradores Ativos</div>
                    <h3 class="fw-bold mb-0" style="color: var(--coffee-brown);">{{ $stats['maquinas'] }} <small class="fs-6 text-muted">/ 2</small></h3>
                    <span class="badge bg-success" style="font-size: 0.6rem;">SISTEMA ONLINE</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm d-flex flex-row overflow-hidden" style="border-radius: 12px; background: white;">
                <div class="d-flex align-items-center justify-content-center" style="width: 70px; background-color: var(--coffee-brown); color: var(--coffee-gold);">
                    <i class="bi bi-fire fs-2"></i>
                </div>
                <div class="p-3 flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <div class="text-muted small text-uppercase">Produção Total</div>
                        <div class="btn-group">
                            <button class="btn btn-outline-warning btn-sm py-0 px-2 active" style="font-size: 0.6rem;">D</button>
                            <button class="btn btn-outline-warning btn-sm py-0 px-2" style="font-size: 0.6rem;">M</button>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0" style="color: var(--coffee-brown);">120.5 <small class="fs-6">kg</small></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm d-flex flex-row overflow-hidden border-start border-danger border-4" style="border-radius: 12px; background: white;">
                <div class="d-flex align-items-center justify-content-center text-danger" style="width: 70px;">
                    <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                </div>
                <div class="p-3 flex-grow-1">
                    <div class="text-muted small text-uppercase">Próxima Limpeza</div>
                    <h3 class="fw-bold mb-0 text-danger">38 <small class="fs-6 text-muted">/ 40 torras</small></h3>
                    <span class="text-danger fw-bold" style="font-size: 0.7rem;">Carmomaq: Limpeza pendente!</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="background: white; border-radius: 12px;">
                <h6 class="mb-4 fw-bold text-uppercase" style="color: var(--coffee-gold); letter-spacing: 1px;">Fila de Produção Hoje</h6>
                <div class="row g-3">
                    <div class="col-4">
                        <div class="p-2 rounded bg-light">
                            <div class="text-muted mb-3 fw-bold small">A TORRAR (2)</div>
                            <div class="bg-white p-2 rounded shadow-sm mb-2 border-start border-3 border-warning small">#502 - Bourbon Amarelo</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded" style="background: rgba(192, 161, 107, 0.1);">
                            <div class="text-warning mb-3 fw-bold small">NO TORRADOR (1)</div>
                            <div class="bg-white p-2 rounded shadow-sm mb-2 border-start border-3 border-dark small"><strong>PROBAT L25</strong></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-light">
                            <div class="text-success mb-3 fw-bold small">FINALIZADO (8)</div>
                            <div class="bg-white p-2 rounded shadow-sm mb-2 border-start border-3 border-success small">#499 - Geisha</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="background: white; border-radius: 12px;">
                <h6 class="mb-4 fw-bold text-uppercase" style="color: var(--coffee-gold); letter-spacing: 1px;">Últimas Provas</h6>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 border-0 mb-2">
                        <div class="d-flex justify-content-between"><small class="text-muted">Hoje, 14:20</small><span class="badge bg-dark text-warning">87.5 pts</span></div>
                        <p class="mb-0 small fw-bold">Bourbon Amarelo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection