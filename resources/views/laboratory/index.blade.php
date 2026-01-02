@extends('layouts.app')

@section('title', 'Laboratório de Qualidade')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold">Laboratório de Qualidade</h2>
        <div class="btn-group shadow-sm">
            <button class="btn btn-dark active">Mesas de Prova</button>
            <button class="btn btn-outline-dark">Amostras Verdes</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 4px solid var(--coffee-gold) !important;">
                        <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Mesas Realizadas</small>
                        <div class="h3 fw-bold mb-0">42</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 bg-white">
                        <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Xícaras Pontuadas</small>
                        <div class="h3 fw-bold mb-0">156</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 text-white" style="background: var(--coffee-brown);">
                        <small class="text-uppercase fw-bold opacity-75" style="color: var(--coffee-gold); font-size: 0.65rem;">Maior Pontuação</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">91.5 <small class="fs-6">SCA</small></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-white shadow-sm w-100 fw-bold border-0" style="height: 50px; color: var(--coffee-brown);">
                        <i class="bi bi-funnel-fill me-2 text-warning"></i> FILTRAR
                    </button>
                </div>
                <div class="col-sm-5">
                    <button class="btn text-white shadow-sm w-100 fw-bold border-0" style="height: 50px; background: #2e7d32;">
                        <i class="bi bi-plus-circle me-2"></i> NOVA MESA DE PROVA
                    </button>
                </div>
                <div class="col-sm-4">
                    <button class="btn shadow-sm w-100 fw-bold border-0" style="height: 50px; background: var(--coffee-gold); color: var(--coffee-brown);">
                        <i class="bi bi-magic me-2"></i> BLEND BUILDER
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100 d-flex align-items-center justify-content-center text-center" style="background: white; border: 2px dashed var(--coffee-gold) !important;">
                <i class="bi bi-clipboard-pulse fs-1 opacity-25"></i>
                <p class="small fw-bold mt-2 mb-0 opacity-50">HISTÓRICO SENSORIAL GLOBAL</p>
                <button class="btn btn-link btn-sm text-decoration-none p-0 mt-2" style="color: var(--coffee-brown); font-size: 0.7rem;">GERAR RELATÓRIO PDF</button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background: white;">
        <h5 class="fw-bold mb-4">Últimas Mesas de Prova</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="small text-uppercase text-muted border-bottom">
                        <th style="width: 40px;"></th>
                        <th>Nome da Mesa</th>
                        <th>Xícaras</th>
                        <th>Data/Hora</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="shadow-sm" style="background: #fdfdfd;">
                        <td>
                            <button class="btn btn-sm text-dark" data-bs-toggle="collapse" data-bs-target="#collapseMesa1">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </td>
                        <td class="fw-bold">Cafés pro Clube de Janeiro</td>
                        <td><span class="badge bg-light text-dark border">2 Xícaras</span></td>
                        <td class="small">18/12/2025 - 14:30</td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">Ativo</span></td>
                        <td class="text-center text-nowrap">
                            <i class="bi bi-info-circle text-primary me-2" style="cursor:pointer"></i>
                            <i class="bi bi-trash3 text-danger" style="cursor:pointer"></i>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="p-0 border-0">
                            <div id="collapseMesa1" class="collapse show px-4 py-3" style="background: #fafafa; border-top: 1px solid #eee;">
                                <h6 class="fw-bold small text-muted text-uppercase mb-3">Detalhamento das Xícaras:</h6>
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <thead class="small opacity-75 border-bottom">
                                        <tr>
                                            <th class="text-center" style="width: 40px;">Nº</th>
                                            <th>Variedade</th>
                                            <th>Torra</th>
                                            <th class="text-center">Nota SCA</th>
                                            <th class="text-center">Acidez</th>
                                            <th class="text-center">Doçura</th>
                                            <th class="text-center">Corpo</th>
                                            <th>Sensorial / Tags</th>
                                            <th class="text-center">Anexos</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <tr class="border-bottom">
                                            <td class="text-center fw-bold text-muted">1</td>
                                            <td><strong>Bourbon Amarelo</strong></td>
                                            <td><span class="badge bg-warning-subtle text-dark" style="font-size: 0.65rem;">Média-Clara</span></td>
                                            <td class="text-center fw-bold text-dark">87.50</td>
                                            <td class="text-center">8.5</td>
                                            <td class="text-center">9.0</td>
                                            <td class="text-center">8.0</td>
                                            <td><span class="text-warning fw-bold">#mel #laranja #floral</span></td>
                                            <td class="text-center"><i class="bi bi-file-earmark-text text-primary"></i></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-bold text-muted">2</td>
                                            <td><strong>Catuaí Vermelho</strong></td>
                                            <td><span class="badge bg-dark-subtle text-dark" style="font-size: 0.65rem;">Média</span></td>
                                            <td class="text-center fw-bold text-dark">84.25</td>
                                            <td class="text-center">7.5</td>
                                            <td class="text-center">8.0</td>
                                            <td class="text-center">7.5</td>
                                            <td><span class="text-warning fw-bold">#chocolate #corpo-denso</span></td>
                                            <td class="text-center"><i class="bi bi-file-earmark-text text-primary"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection