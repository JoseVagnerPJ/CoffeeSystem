@extends('layouts.app')

@section('title', 'Gestão de Torrefação')

@section('content')
<style>
    /* Estilização dos Cards com Cores Fortes e Bordas de 12px */
    .card-roast { border-left: 12px solid transparent !important; transition: 0.3s; background: white; border-radius: 8px; }
    
    .border-yellow { border-left-color: #FFD600 !important; background: rgba(255, 214, 0, 0.1); }
    .border-brown { border-left-color: #4E342E !important; background: rgba(78, 52, 46, 0.1); }
    .border-purple { border-left-color: #9C27B0 !important; background: rgba(156, 39, 176, 0.1); }

    .chart-container { position: relative; height: 180px; width: 100%; }
    .mestre-label { font-size: 0.75rem; font-weight: 600; color: var(--coffee-text); opacity: 0.8; }
    .badge-machine { background: #212121; color: white; font-weight: 600; }
</style>

<div class="fade-in">
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold">Controle de Produção (Kanban)</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-success fw-bold shadow-sm px-3"><i class="bi bi-plus-lg me-1"></i> NOVA DEMANDA</button>
                    <button class="btn btn-primary fw-bold shadow-sm px-3"><i class="bi bi-bell-fill me-1"></i> NOTIFICAR MESTRE</button>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="card p-3 shadow-sm border-0 bg-white">
                        <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Torras Realizadas (Mês)</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-brown);">
                            342 <span class="fs-6 fw-normal">torras</span> / 8.550 <span class="fs-6 fw-normal">kg</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card p-3 shadow-sm border-0 bg-white border-start border-4 border-warning">
                        <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Status de Limpeza (Probat)</small>
                        <div class="h3 fw-bold mb-0 text-warning">34 <small class="fs-6 text-muted">/ 40 torras</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="small fw-bold mb-0">PRODUÇÃO POR VARIEDADE (KG)</h6>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary btn-sm py-0 px-2 active" style="font-size: 0.6rem;">D</button>
                        <button class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.6rem;">M</button>
                    </div>
                </div>
                <div class="chart-container d-flex align-items-center justify-content-center">
                    <svg viewBox="0 0 400 150" class="w-100">
                        <path d="M0,100 L100,80 L200,120 L300,40 L400,60" fill="none" stroke="#FFD600" stroke-width="4" />
                        <path d="M0,120 L100,110 L200,50 L300,90 L400,30" fill="none" stroke="#4E342E" stroke-width="4" />
                        <path d="M0,60 L100,130 L200,90 L300,110 L400,10" fill="none" stroke="#9C27B0" stroke-width="4" />
                        <circle cx="300" cy="40" r="5" fill="#FFD600" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="p-3 rounded-3 shadow-inner" style="background: #EBE7E0; min-height: 600px;">
                <h6 class="fw-bold mb-3 text-uppercase opacity-50 small">Fila: A Torrar</h6>
                
                <div class="card card-roast border-0 shadow-sm mb-3 border-yellow">
                    <div class="p-3">
                        <div class="d-flex justify-content-between">
                            <strong class="fs-5" style="color: var(--coffee-brown);">
                                <i class="bi bi-chevron-down small me-2" data-bs-toggle="collapse" data-bs-target="#expA_1" style="cursor:pointer"></i>
                                Bourbon Amarelo
                            </strong>
                            <i class="bi bi-pencil-square text-muted" style="cursor: pointer;"></i>
                        </div>
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <span class="badge bg-white text-dark border border-warning">Clara</span>
                            <span class="badge badge-machine"><i class="bi bi-gear-fill me-1"></i>Probat</span>
                            <span class="mestre-label ms-auto">Mestre: Carlos</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-end mt-3">
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 0.65rem;">TOTAL / CARGA</small>
                                <div class="h5 fw-bold mb-0">100kg <span class="text-muted fw-normal fs-6">/ 25kg</span></div>
                            </div>
                            <div class="bg-dark text-warning d-flex align-items-center justify-content-center rounded-3 fw-bold shadow" style="width: 40px; height: 40px; font-size: 1.2rem;">4</div>
                        </div>
                    </div>
                    <div class="collapse bg-white p-2 border-top" id="expA_1">
                        <div class="px-2 small py-1 border-bottom d-flex justify-content-between"><span>Torra #1</span> <span class="fw-bold">25kg</span></div>
                        <div class="px-2 small py-1 d-flex justify-content-between"><span>Torra #2</span> <span class="fw-bold">25kg</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background: rgba(192, 161, 107, 0.15); min-height: 600px; border: 1px solid var(--coffee-gold);">
                <h6 class="fw-bold mb-3 text-uppercase text-dark small">No Torrador (Ativo)</h6>
                
                <div class="card card-roast border-0 shadow mb-3 border-brown">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong class="fs-5">Blend Unique</strong>
                            <div class="text-danger small fw-bold anim-pulse"><i class="bi bi-record-circle-fill me-1"></i>TORRANDO</div>
                        </div>
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white">Média</span>
                            <span class="badge badge-machine"><i class="bi bi-gear-fill me-1"></i>Probat</span>
                            <span class="mestre-label ms-auto">Mestre: Carlos</span>
                        </div>
                        
                        <div class="mt-3 p-3 rounded-3 shadow-sm border bg-white">
                            <div class="row align-items-center">
                                <div class="col-5 border-end">
                                    <small class="text-muted d-block fw-bold" style="font-size: 0.6rem;">CARGA ATUAL</small>
                                    <span class="h4 fw-bold">20 kg</span>
                                </div>
                                <div class="col-7 text-end">
                                    <small class="text-muted d-block fw-bold" style="font-size: 0.6rem;">HORA INÍCIO / TEMPO CORRIDO</small>
                                    <span class="h5 fw-bold text-danger">14:20 <span class="mx-1">|</span> 14 mins</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 text-center small opacity-75">Ciclo 1 de 1 da demanda</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-3 shadow-inner" style="background: #EBE7E0; min-height: 600px;">
                <h6 class="fw-bold mb-3 text-uppercase text-success small">Finalizado (Hoje)</h6>
                
                <div class="card card-roast border-0 shadow-sm mb-3 border-purple">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="fs-5" style="color: var(--coffee-brown);">
                                <i class="bi bi-chevron-down small me-2" data-bs-toggle="collapse" data-bs-target="#expF_1" style="cursor:pointer"></i>
                                Catuaí Vermelho
                            </strong>
                            <span class="text-success small fw-bold">CONCLUÍDO 14:20</span>
                        </div>
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <span class="badge bg-white text-dark border border-secondary">Média</span>
                            <span class="badge badge-machine"><i class="bi bi-gear-fill me-1"></i>Probat</span>
                            <span class="mestre-label ms-auto">Mestre: Carlos</span>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">ID: #TR-992</span>
                            <div class="h5 fw-bold text-success mb-0">Total: 45kg</div>
                        </div>
                    </div>
                    <div class="collapse bg-white p-2 border-top" id="expF_1">
                        <div class="px-2 small py-1 border-bottom d-flex justify-content-between">
                            <span>Torra #1 (13:50)</span> <span class="fw-bold">22.5kg</span>
                        </div>
                        <div class="px-2 small py-1 d-flex justify-content-between">
                            <span>Torra #2 (14:15)</span> <span class="fw-bold">22.5kg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
.anim-pulse { animation: pulse 1.5s infinite; }
</style>
@endsection