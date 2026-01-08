@extends('layouts.app')

@section('title', 'Laboratório de Qualidade')

@section('content')
<style>
    /* PADRONIZAÇÃO */
    .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border-radius: 8px; border: 1px solid #dee2e6; background: white; cursor: pointer; color: #333; }
    .btn-action:hover { transform: scale(1.1); color: var(--coffee-brown); }
    .op-block { border: 2px solid #dee2e6; border-radius: 15px; padding: 0.8rem; background: #fff; margin-bottom: 0.5rem; }
    .stat-card-kpi { height: 105px; border-radius: 12px; display: flex; flex-direction: column; justify-content: center; padding: 1.2rem !important; border: none !important; }
    .btn-main-action { height: 105px; border-radius: 12px; font-weight: 800; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; border: none; text-decoration: none; color: white; transition: 0.2s; }
    .btn-main-action:hover { filter: brightness(0.9); transform: translateY(-2px); color: white; }

    /* GRÁFICO */
    .chart-container { background: #fff; border: 2px solid #dee2e6; border-radius: 20px; padding: 15px; height: 220px; width: 100%; }
    .btn-filter-chart { font-size: 0.6rem; font-weight: 800; padding: 3px 8px; border-radius: 5px; border: 1px solid #dee2e6; background: white; color: #666; text-decoration: none; }
    .btn-filter-chart.active { background: var(--coffee-brown); color: white !important; }

    /* TABELA E EXPANSÃO */
    .table-responsive-custom { width: 100%; overflow-x: auto; border-radius: 12px; }
    .inner-table { min-width: 1100px; table-layout: fixed; }
    .inner-table th, .inner-table td { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .rotate-icon { transition: transform 0.3s; }
    .btn[aria-expanded="true"] .rotate-icon { transform: rotate(180deg); }

    .sca-pill { font-weight: 800; color: var(--coffee-brown); border-left: 4px solid var(--coffee-gold); padding: 2px 0 2px 10px; }
</style>

<div class="fade-in px-3 pt-3">
    <div class="row g-3 align-items-start no-print">
        <div class="col-lg-8">
            <div class="op-block mb-2 py-2 shadow-sm">
                <h2 class="h5 fw-bold mb-0" style="color: var(--coffee-brown);">Laboratório de Qualidade</h2>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <div class="card stat-card-kpi shadow-sm text-white" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Mesas</small>
                        <div class="h3 fw-bold mb-0">{{ $totalSessions }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card-kpi shadow-sm text-white" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Xícaras</small>
                        <div class="h3 fw-bold mb-0">{{ $totalCups }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-main-action w-100 shadow-sm" style="background: #198754;" data-bs-toggle="modal" data-bs-target="#modalCreateCupping">
                        <i class="bi bi-plus-circle-fill fs-3"></i><span class="small">INICIAR MESA</span>
                    </button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-main-action w-100 shadow-sm" style="background: var(--coffee-gold); color: var(--coffee-brown);">
                        <i class="bi bi-layers-fill fs-3"></i><span class="small">BLEND BUILDER</span>
                    </button>
                </div>
            </div>

            {{-- BARRA DE PESQUISA ATUALIZADA --}}
            <div class="op-block shadow-sm">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" id="mesaSearch" class="form-control form-control-sm border-0 bg-light" placeholder="Pesquisar mesa...">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-sm fw-bold border-0 w-100 h-100" style="background: var(--coffee-gold); color: var(--coffee-brown);">
                            <i class="bi bi-funnel-fill me-1"></i> FILTRAR
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button onclick="window.print()" class="btn btn-sm btn-dark w-100 h-100 fw-bold">
                            <i class="bi bi-printer me-1"></i> RELATÓRIO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-container shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-uppercase" style="font-size: 0.6rem; color: #999;">Atividade Recente</span>
                    <div class="btn-group gap-1">
                        <a href="?chart_filter=D" class="btn-filter-chart {{ $filter == 'D' ? 'active' : '' }}">D</a>
                        <a href="?chart_filter=S" class="btn-filter-chart {{ $filter == 'S' ? 'active' : '' }}">S</a>
                        <a href="?chart_filter=M" class="btn-filter-chart {{ $filter == 'M' ? 'active' : '' }}">M</a>
                    </div>
                </div>
                <canvas id="labChart"></canvas>
            </div>
        </div>
    </div>

    <div id="printable-table" class="card shadow-sm border-0 p-4 mt-2" style="border-radius: 15px;">
        <h5 class="fw-bold mb-4" style="color: var(--coffee-brown); font-size: 1rem;">Histórico de Mesas</h5>
        <div class="table-responsive">
            <table class="table align-middle table-no-hover" id="mesasTable">
                <thead class="table-light">
                    <tr class="small text-uppercase text-muted">
                        <th style="width: 50px;"></th><th>ID</th><th>Título da Mesa</th><th class="text-center">Xícaras</th><th class="text-center">Participantes</th><th>Data Início</th><th>Status</th><th class="text-center no-print">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($sessions as $session)
                    <tr class="mesa-row border-bottom">
                        <td><button class="btn btn-sm btn-light border-0" data-bs-toggle="collapse" data-bs-target="#mesa-{{ $session->id }}"><i class="bi bi-chevron-down rotate-icon"></i></button></td>
                        <td class="text-muted small">#{{ $session->id }}</td>
                        <td class="fw-bold">{{ $session->title }}</td>
                        <td class="text-center fw-bold text-dark">{{ $session->items_count }}</td>
                        <td class="text-center">{{ $session->participants_count }}</td>
                        <td class="small">{{ date('d/m/Y H:i', strtotime($session->started_at ?? $session->created_at)) }}</td>
                        <td><span class="badge {{ $session->status == 'finalizado' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} fw-bold" style="font-size: 0.6rem;">{{ strtoupper($session->status) }}</span></td>
                        <td class="text-center no-print">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn-action text-info"><i class="bi bi-info-circle-fill"></i></button>
                                <form action="{{ route('laboratory.destroy', $session->id) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="btn-action text-danger"><i class="bi bi-trash-fill"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    <tr id="mesa-{{ $session->id }}" class="collapse bg-light">
                        <td colspan="8" class="p-3">
                            <div class="table-responsive-custom op-block border-0 shadow-sm p-0">
                                <table class="table table-sm table-hover mb-0 inner-table">
                                    <thead class="bg-dark text-white small">
                                        <tr>
                                            <th style="width: 50px;" class="ps-3">#</th>
                                            <th style="width: 200px;">Xícara</th>
                                            <th style="width: 150px;">Amostra</th>
                                            <th class="text-center">Doçura</th>
                                            <th class="text-center">Acidez</th>
                                            <th class="text-center">Corpo</th>
                                            <th class="text-center">Retro.</th>
                                            <th class="text-center" style="width: 120px;">SCA FINAL</th>
                                            <th style="width: 200px;">Observações</th>
                                            <th class="text-center" style="width: 60px;">Info</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small bg-white">
                                    @foreach($session->items as $item)
                                        @php
                                            $m = ['s' => $item->evaluations->avg('sweetness') ?? 0, 'a' => $item->evaluations->avg('acidity') ?? 0, 'b' => $item->evaluations->avg('body') ?? 0, 'r' => $item->evaluations->avg('aftertaste') ?? 0];
                                            $final = 60 + array_sum($m);
                                        @endphp
                                        <tr class="border-bottom">
                                            <td class="ps-3 text-muted fw-bold">{{ $item->position }}</td>
                                            <td class="fw-bold">{{ $item->nickname ?? 'Xícara '.$item->position }}</td>
                                            <td>{{ $item->is_avulso ? 'AVULSA' : optional($item->sample)->variety }}</td>
                                            <td class="text-center">{{ number_format($m['s'], 1) }}</td>
                                            <td class="text-center">{{ number_format($m['a'], 1) }}</td>
                                            <td class="text-center">{{ number_format($m['b'], 1) }}</td>
                                            <td class="text-center">{{ number_format($m['r'], 1) }}</td>
                                            <td class="text-center"><span class="sca-pill">{{ number_format($final, 2) }}</span></td>
                                            <td class="text-muted small italic">{{ $item->notes ?: '-' }}</td>
                                            <td class="text-center"><button class="btn-action btn-sm mx-auto"><i class="bi bi-info"></i></button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statsData = {!! json_encode($stats) !!};
    const ctx = document.getElementById('labChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: statsData.map(s => s.label),
            datasets: [
                { label: 'Mesas', data: statsData.map(s => s.sessions_count), borderColor: '#c6a664', backgroundColor: '#c6a66422', borderWidth: 2, fill: true, tension: 0.4 },
                { label: 'Xícaras', data: statsData.map(s => s.cups_count), borderColor: '#3e2723', borderDash: [5,5], borderWidth: 1, pointRadius: 0, tension: 0.4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } } }, 
                x: { ticks: { font: { size: 9 } } } 
            }
        }
    });

    document.getElementById('mesaSearch').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.mesa-row').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
        });
    });
</script>
@include('laboratory.partials.modal_create_cupping')
@include('laboratory.partials.modal_cup_tech_info')
@endsection