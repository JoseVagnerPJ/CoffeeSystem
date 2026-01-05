@extends('layouts.app')

@section('title', 'Laboratório de Qualidade')

@section('content')
<div class="fade-in">
    {{-- CABEÇALHO - Escondido na impressão --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2 class="h4 fw-bold">Laboratório de Qualidade</h2>
    </div>

    {{-- KPIs - Escondidos na impressão --}}
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 bg-white h-100" style="border-left: 4px solid var(--coffee-gold) !important;">
                <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Mesas Realizadas</small>
                <div class="h3 fw-bold mb-0">{{ $totalSessions ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 bg-white h-100">
                <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Xícaras Pontuadas</small>
                <div class="h3 fw-bold mb-0">{{ $totalCups ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 text-white h-100" style="background: var(--coffee-brown);">
                <small class="text-uppercase fw-bold opacity-75" style="color: var(--coffee-gold); font-size: 0.65rem;">Maior Pontuação</small>
                <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">{{ number_format($maxScore ?? 0, 1) }} <small class="fs-6">SCA</small></div>
            </div>
        </div>
    </div>

    {{-- BARRA DE PESQUISA E AÇÕES - no-print --}}
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-6">
            <div class="input-group shadow-sm">
                <input type="text" id="mesaSearch" class="form-control border-0" placeholder="Pesquisar por nome da mesa..." style="height: 50px;">
                <button class="btn fw-bold border-0 px-4" style="background: var(--coffee-gold); color: var(--coffee-brown);">
                    <i class="bi bi-funnel-fill me-2"></i> FILTRAR
                </button>
            </div>
        </div>
        <div class="col-md-3">
            <button class="btn btn-dark shadow-sm w-100 fw-bold border-0" style="height: 50px;" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> GERAR RELATÓRIO
            </button>
        </div>
        <div class="col-md-3">
            <button class="btn shadow-sm w-100 fw-bold border-0" style="height: 50px; background: var(--coffee-gold); color: var(--coffee-brown);">
                <i class="bi bi-magic me-2"></i> BLEND BUILDER
            </button>
        </div>
    </div>

    {{-- ÁREA DO RELATÓRIO --}}
    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background: white;">
        <h5 class="fw-bold mb-4">Relatório Sensorial de Mesas de Prova</h5>
        <div class="table-responsive">
            <table class="table align-middle" id="mesasTable">
                <thead>
                    <tr class="small text-uppercase text-muted border-bottom">
                        <th style="width: 40px;" class="no-print"></th>
                        <th>Mesa</th>
                        <th>Qtd Xícaras</th>
                        <th>Data Realização</th>
                        <th>Status</th>
                        <th class="text-center no-print">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr class="mesa-row">
                        <td class="no-print">
                            <button class="btn btn-sm text-dark" data-bs-toggle="collapse" data-bs-target="#collapseMesa{{ $session->id }}">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </td>
                        <td class="fw-bold mesa-title">{{ $session->title }}</td>
                        <td>{{ $session->items->count() }} xícaras</td>
                        <td class="small">{{ date('d/m/Y - H:i', strtotime($session->created_at)) }}</td>
                        <td>{{ ucfirst($session->status) }}</td>
                        <td class="text-center no-print">
                            <i class="bi bi-info-circle text-primary"></i>
                        </td>
                    </tr>
                    {{-- DETALHAMENTO DAS XÍCARAS --}}
                    <tr class="mesa-details-row">
                        <td colspan="6" class="p-0 border-0">
                            <div id="collapseMesa{{ $session->id }}" class="collapse print-expand px-4 py-3" style="background: #fafafa; border-top: 1px solid #eee;">
                                <h6 class="fw-bold small text-muted text-uppercase mb-3">Detalhamento Técnico das Xícaras:</h6>
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="small opacity-75 border-bottom">
                                        <tr class="table-light">
                                            <th class="text-center" style="width: 40px;">#</th>
                                            <th>Variedade</th>
                                            <th>Torra</th>
                                            <th class="text-center">Acidez</th>
                                            <th class="text-center">Doçura</th>
                                            <th class="text-center">Corpo</th>
                                            <th class="text-center">SCA Final</th>
                                            <th>Notas Sensoriais (Principais Atributos)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @foreach($session->items as $item)
                                        <tr class="border-bottom">
                                            <td class="text-center fw-bold">{{ $item->position }}</td>
                                            <td><strong>{{ $item->sample->variety }}</strong></td>
                                            <td>{{ $item->sample->roast_type ?? 'Média' }}</td>
                                            <td class="text-center">{{ number_format($item->evaluations->avg('acidity'), 1) }}</td>
                                            <td class="text-center">{{ number_format($item->evaluations->avg('sweetness'), 1) }}</td>
                                            <td class="text-center">{{ number_format($item->evaluations->avg('body'), 1) }}</td>
                                            <td class="text-center fw-bold text-dark">
                                                {{ number_format($item->evaluations->avg('final_score'), 2) }}
                                            </td>
                                            <td>
                                                <span class="text-dark">
                                                    @foreach($item->evaluations->flatMap->tags->unique('id')->take(5) as $tag)
                                                        #{{ $tag->name }} 
                                                    @endforeach
                                                </span>
                                            </td>
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

<style>
@media print {
    /* 1. Esconde TUDO o que é interface do sistema */
    body * { visibility: hidden !important; background: transparent !important; }
    .no-print, .main-sidebar, .main-header, .btn, .input-group, nav, aside { display: none !important; }

    /* 2. Isola apenas a área do relatório e expande para 100% da folha */
    .printable-report, .printable-report * { visibility: visible !important; }
    .printable-report {
        position: absolute !important; left: 0 !important; top: 0 !important;
        width: 100% !important; margin: 0 !important; padding: 0 !important;
        background: white !important; /* Corrige o erro da imagem 69e5a5.png */
    }

    /* 3. Força a expansão das tabelas escondidas no collapse */
    .collapse { display: block !important; height: auto !important; opacity: 1 !important; }
    table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; }
    th { background-color: #eee !important; border: 1px solid #000 !important; color: #000 !important; }
    td { border: 1px solid #000 !important; padding: 5px !important; }
}
</style>

{{-- SCRIPT DE PESQUISA EM TEMPO REAL --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('mesaSearch');
    const tableRows = document.querySelectorAll('.mesa-row');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        tableRows.forEach(row => {
            const title = row.querySelector('.mesa-title').textContent.toLowerCase();
            const detailsRow = row.nextElementSibling;

            if (title.includes(searchTerm)) {
                row.style.display = '';
                if (detailsRow) detailsRow.style.display = '';
            } else {
                row.style.display = 'none';
                if (detailsRow) detailsRow.style.display = 'none';
            }
        });
    });
});
</script>
@endsection