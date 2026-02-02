@extends('layouts.app')

@section('title', 'Laboratório de Qualidade')

@section('content')
<style>
@media print {
    /* 1. Forçar Orientação Paisagem para comportar os nomes menos abreviados */
    @page { size: landscape; margin: 10mm; }

    /* 1. Gerenciamento de Visibilidade */
    body * { visibility: hidden; }
    #print-report, #print-report * { visibility: visible; }
    #print-report { 
        position: absolute; 
        left: 0; 
        top: 0; 
        width: 100%; 
        color: #000; 
        background: white !important;
        font-family: Arial, sans-serif;
    }

    /* 2. Estrutura da Mesa (Bordas contínuas) */
    .print-session-wrapper {
        border-left: 1.5pt solid #333;
        border-right: 1.5pt solid #333;
        margin-bottom: 30px;
        page-break-inside: avoid; 
    }

    .print-session-header {
        border-top: 1.5pt solid #333;
        border-bottom: 1.2pt solid #333;
        padding: 15px;
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
    }

    .print-session-footer {
        border-bottom: 1.5pt solid #333;
        width: 100%;
        height: 1px;
    }

    .print-cup-container {
        padding-left: 15px; 
        padding-right: 15px;
    }

    .print-cup-row {
        border-bottom: 0.5pt solid #eee;
        padding: 6px 0;
        display: flex;
        align-items: center;
        page-break-inside: avoid; 
        font-size: 7.5pt; /* Leve aumento para melhorar leitura */
    }

    /* Ajuste de larguras para Landscape */
    .col-print-pos   { width: 35px; text-align: left; color: #666; }
    .col-print-info  { width: 160px; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .col-print-attr  { flex: 1; text-align: center; font-weight: 600; min-width: 45px; }
    .col-print-final { width: 70px; text-align: right; font-weight: bold; }

    .fw-bold { font-weight: bold !important; }
    .no-print { display: none !important; }
}

    /* --- PADRONIZAÇÃO VISUAL --- */
    .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border-radius: 8px; border: 1px solid #dee2e6; background: white; cursor: pointer; color: #333; }
    .btn-action:hover { transform: scale(1.1); color: var(--coffee-brown); }
    .op-block { border: 2px solid #dee2e6; border-radius: 15px; padding: 0.8rem; background: #fff; margin-bottom: 0.5rem; }
    .stat-card-kpi { height: 105px; border-radius: 12px; display: flex; flex-direction: column; justify-content: center; padding: 1.2rem !important; border: none !important; }
    .btn-main-action { height: 105px; border-radius: 12px; font-weight: 800; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; border: none; text-decoration: none; color: white; transition: 0.2s; }
    .btn-main-action:hover { filter: brightness(0.9); transform: translateY(-2px); color: white; }

    /* --- GRÁFICO --- */
    .chart-container { background: #fff; border: 2px solid #dee2e6; border-radius: 20px; padding: 15px; height: 220px; width: 100%; position: relative; }
    .btn-filter-chart { font-size: 0.6rem; font-weight: 800; padding: 3px 8px; border-radius: 5px; border: 1px solid #dee2e6; background: white; color: #666; text-decoration: none; }
    .btn-filter-chart.active { background: var(--coffee-brown); color: white !important; }

    /* --- TABELAS --- */
    .table-responsive { width: 100%; border-radius: 12px; overflow: hidden; }
    #mesasTable { width: 100% !important; table-layout: auto; }

    .table-responsive-custom { 
        width: 100%; 
        border-radius: 12px; 
        background: #fff;
        overflow-x: auto; 
    }

    /* Tabela interna agora suporta min-width para não espremer nomes longos */
    .inner-table { 
        width: 100% !important; 
        min-width: 1000px !important; 
        table-layout: fixed !important; 
        border-collapse: separate; 
        border-spacing: 0 5px; 
    } 

    .inner-table th { 
        padding: 10px 2px; 
        background: #f8f9fa; 
        border-bottom: 2px solid #dee2e6; 
        font-size: 0.65rem; 
        text-align: center;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: visible;
    }

    .inner-table td { 
        padding: 10px 2px; 
        vertical-align: middle; 
        font-size: 0.8rem; 
        text-align: center;
    }

    .col-web-info { width: 200px; text-align: left !important; padding-left: 15px !important; }
    .col-web-sca  { width: 90px; }
    .col-web-btn  { width: 50px; }

    .rotate-icon { transition: transform 0.3s; }
    .btn[aria-expanded="true"] .rotate-icon { transform: rotate(180deg); }
    .sca-pill { font-weight: 800; color: var(--coffee-brown); border-left: 4px solid var(--coffee-gold); padding: 2px 0 2px 10px; font-size: 1rem; }

    /* --- MODAIS --- */
    #modalCupTechInfo .modal-dialog, #modalGeneralInfo .modal-dialog, #modalFilter .modal-dialog { max-width: 1000px !important; }
    #modalCupTechInfo .modal-content { background-color: #fdfaf5 !important; border: 2px solid #e3d5ca !important; height: auto !important; }
    
    .input-lock-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 0.9rem; color: #a67c52; pointer-events: none; transition: all 0.2s; }
    .hide-lock { display: none !important; } 
    .field-locked { background-color: rgba(233, 236, 239, 0.6) !important; color: #6c757d; cursor: not-allowed; border-color: #dee2e6 !important; }
    
    .nickname-container { flex-grow: 1; background: #fff; border: 2px solid #e9ecef; border-radius: 8px; padding: 4px 12px; margin-right: 10px; transition: border-color 0.2s; }
    .nickname-container:focus-within { border-color: var(--coffee-gold); }
    .nickname-label { font-size: 0.65rem; font-weight: 800; color: #adb5bd; text-transform: uppercase; margin-bottom: -2px; display: block; }
    .nickname-input { border: none !important; padding: 0 !important; font-weight: bold; color: var(--coffee-brown); width: 100%; outline: none; background: transparent; }

    .sample-result-list { position: absolute; width: 100%; z-index: 1050; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; display: none; box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .sample-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 0.85rem; }
    .sample-item:hover { background: #f8f9fa; }

    .is-avulso-badge { background: #fd7e14; color: white; font-size: 0.65rem; padding: 2px 8px; border-radius: 4px; font-weight: bold; display: none; }
    .avulso-active .is-avulso-badge { display: inline-block; }
    .avulso-active .cup-number { background: #fd7e14; }

    #footer-error-msg { font-size: 0.75rem; color: #dc3545; font-weight: 800; text-transform: uppercase; display: none; margin-bottom: 10px; text-align: center; width: 100%; }
</style>

@php
    /**
     * MAPEAMENTO DINÂMICO DE ATRIBUTOS SCA
     */
    $scaAttributesMap = [
        'eval_fragrance' => ['label' => 'Aroma',      'short' => 'Aroma',   'field' => 'fragrance_aroma'],
        'eval_flavor'    => ['label' => 'Sabor',      'short' => 'Sabor',   'field' => 'flavor'],
        'eval_aftertaste'=> ['label' => 'Retrogosto', 'short' => 'Retro.',  'field' => 'aftertaste'],
        'eval_acidity'   => ['label' => 'Acidez',     'short' => 'Acidez',  'field' => 'acidity'],
        'eval_body'      => ['label' => 'Corpo',      'short' => 'Corpo',   'field' => 'body'],
        'eval_balance'   => ['label' => 'Equilíbrio',  'short' => 'Equil.',  'field' => 'balance'],
        'eval_uniformity'=> ['label' => 'Uniformid.', 'short' => 'Unif.',   'field' => 'uniformity'],
        'eval_clean_cup' => ['label' => 'Limpeza',    'short' => 'Limpeza', 'field' => 'clean_cup'],
        'eval_sweetness' => ['label' => 'Doçura',     'short' => 'Doçura',  'field' => 'sweetness'],
        'eval_overall'   => ['label' => 'Geral',      'short' => 'Geral',   'field' => 'overall'],
        'eval_defects'   => ['label' => 'Defeitos',   'short' => 'Def.',    'field' => 'defects'],
    ];
@endphp

<div class="fade-in px-3 pt-3">

    {{-- AVISOS DE SUCESSO --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div><strong>Sucesso!</strong> {{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header e KPIs --}}
    <div class="row g-3 align-items-start no-print">
        <div class="col-lg-8">
            <div class="op-block mb-2 py-2 shadow-sm">
                <h2 class="h5 fw-bold mb-0" style="color: var(--coffee-brown);">Laboratório de Qualidade</h2>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <div class="card stat-card-kpi shadow-sm text-white" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Mesas Totais</small>
                        <div class="h3 fw-bold mb-0">{{ $totalSessions }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card-kpi shadow-sm text-white" style="background: var(--coffee-brown);">
                        <small class="text-uppercase opacity-75 fw-bold" style="font-size: 0.6rem; color: var(--coffee-gold);">Nota Máxima SCA</small>
                        <div class="h3 fw-bold mb-0">{{ number_format($maxScore, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    @if(auth()->user()->hasPermission('lab_full'))
                        <button type="button" class="btn btn-main-action w-100 shadow-sm" style="background: #198754;" data-bs-toggle="modal" data-bs-target="#modalCreateCupping">
                            <i class="bi bi-plus-circle-fill fs-3"></i><span class="small">INICIAR MESA</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-main-action w-100 shadow-sm opacity-50" style="background: #198754;" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                            <i class="bi bi-lock-fill fs-3"></i><span class="small">INICIAR MESA</span>
                        </button>
                    @endif
                </div>
            </div>
            
            {{-- LINHA DE BUSCA E FILTROS --}}
            <div class="op-block shadow-sm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <input type="text" id="mesaSearch" class="form-control form-control-sm border-0 bg-light py-2" placeholder="Pesquisar mesa..." readonly onfocus="this.removeAttribute('readonly');" autocomplete="chrome-off">
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-sm fw-bold border-0 w-100 py-2" style="background: var(--coffee-gold); color: var(--coffee-brown);" data-bs-toggle="modal" data-bs-target="#modalFilter">
                            <i class="bi bi-funnel-fill me-1"></i> FILTRAR
                        </button>
                    </div>
                    <div class="col-md-2 text-end">
                        <button onclick="window.print()" class="btn btn-sm btn-dark w-100 py-2 fw-bold">
                            <i class="bi bi-printer me-1"></i> RELATÓRIO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-container shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div>
                        <span class="fw-bold text-uppercase d-block" style="font-size: 0.6rem; color: var(--coffee-brown);">Fluxo de Trabalho</span>
                        <small class="text-muted" style="font-size: 0.55rem;">
                            @if($filter == 'D') Últimas 24h @elseif($filter == 'S') Últimos 7 dias @else Últimos 30 dias @endif
                        </small>
                    </div>
                    <div class="btn-group gap-1">
                        <a href="?chart_filter=D" class="btn-filter-chart {{ $filter == 'D' ? 'active' : '' }}">D</a>
                        <a href="?chart_filter=S" class="btn-filter-chart {{ $filter == 'S' ? 'active' : '' }}">S</a>
                        <a href="?chart_filter=M" class="btn-filter-chart {{ $filter == 'M' ? 'active' : '' }}">M</a>
                    </div>
                </div>
                <div style="height: 160px;"><canvas id="labChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- HISTÓRICO --}}
    <div id="printable-table" class="card shadow-sm border-0 p-3 p-md-4 mt-2" style="border-radius: 15px;">
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
                    @php
                        // Identifica atributos ativos no perfil desta sessão específica
                        $activeAttributes = collect($scaAttributesMap)->filter(function($attr, $key) use ($session) {
                            return $session->profile->$key ?? false;
                        });
                    @endphp
                    <tr class="mesa-row border-bottom">
                        <td><button class="btn btn-sm btn-light border-0" data-bs-toggle="collapse" data-bs-target="#mesa-{{ $session->id }}"><i class="bi bi-chevron-down rotate-icon"></i></button></td>
                        <td class="text-muted small">#{{ $session->id }}</td>
                        <td class="fw-bold">{{ $session->title }} <br><small class="text-muted fw-normal">Perfil: {{ $session->profile->name }}</small></td>
                        <td class="text-center fw-bold text-dark">{{ $session->items_count }}</td>
                        <td class="text-center fw-bold {{ $session->status == 'aberto' ? 'text-success' : '' }}">
                            {{ $session->status == 'aberto' ? ($session->users_count ?? 0) : $session->participants_count }}
                        </td>
                        <td class="small">{{ ($session->started_at ?? $session->created_at)->format('d/m/Y H:i') }}</td>
                        <td><span class="badge {{ $session->status == 'finalizado' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} fw-bold" style="font-size: 0.6rem;">{{ strtoupper($session->status) }}</span></td>
                        <td class="text-center no-print">
                            <div class="d-flex gap-1 justify-content-center">
                                @if(auth()->user()->hasPermission('lab_full') || auth()->user()->hasPermission('lab_view'))
                                    <button type="button" class="btn-action text-info" onclick="openSessionInfo({{ $session->id }})"><i class="bi bi-info-circle-fill"></i></button>
                                @else
                                    <button type="button" class="btn-action text-muted" onclick="alert('Você não tem permissão para acessar esse módulo!')"><i class="bi bi-lock-fill"></i></button>
                                @endif
                                
                                @if(auth()->user()->hasPermission('lab_full'))
                                    <form action="{{ route('laboratory.destroy', $session->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action text-danger border-0 bg-transparent" title="Excluir Mesa" onclick="return confirm('Deseja realmente excluir a mesa {{ addslashes($session->title) }}?')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
                    {{-- DETALHES DAS XÍCARAS (DINÂMICO CONFORME O PERFIL) --}}
                    <tr id="mesa-{{ $session->id }}" class="collapse bg-light">
                        <td colspan="8" class="p-2 p-md-3">
                            <div class="table-responsive-custom op-block border-0 shadow-sm p-0 bg-white">
                                <table class="table table-hover mb-0 inner-table">
                                    <thead class="bg-dark text-white small">
                                        <tr>
                                            <th class="col-web-info">Amostra / Identificação</th>
                                            {{-- CABEÇALHOS DINÂMICOS (RESUMIDOS) --}}
                                            @foreach($activeAttributes as $attr)
                                                <th>{{ $attr['short'] }}</th>
                                            @endforeach
                                            <th class="col-web-sca">SCA (MÉDIA)</th>
                                            <th class="col-web-btn">Info</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($session->items as $item)
                                        @php $finalScaAvg = $item->evaluations->avg('final_score') ?? 0; @endphp
                                        <tr class="border-bottom">
                                            <td class="col-web-info">
                                                <div class="fw-bold" style="color: var(--coffee-brown);">#{{ $item->position }} - {{ $item->nickname }}</div>
                                                <div class="small text-muted">{{ $item->is_avulso ? 'Avulsa' : optional($item->sample)->name }}</div>
                                            </td>
                                            
                                            {{-- VALORES DINÂMICOS --}}
                                            @foreach($activeAttributes as $key => $attr)
                                                <td>
                                                    @php $avgVal = $item->evaluations->avg($attr['field']) ?? 0; @endphp
                                                    <span class="{{ $avgVal > 0 ? 'fw-bold text-dark' : 'text-muted' }}">
                                                        {{ number_format($avgVal, 2) }}
                                                    </span>
                                                </td>
                                            @endforeach

                                            <td class="col-web-sca"><span class="sca-pill">{{ number_format($finalScaAvg, 2) }}</span></td>
                                            <td class="col-web-btn">
                                                <button type="button" class="btn btn-sm text-primary border-0" onclick="openCupInfo({{ $item->id }})"><i class="bi bi-eye-fill"></i></button>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. GRÁFICO (Mantido Original)
    const statsData = {!! json_encode($stats) !!};
    const ctx = document.getElementById('labChart').getContext('2d');
    const gradientGold = ctx.createLinearGradient(0, 0, 0, 160);
    gradientGold.addColorStop(0, 'rgba(198, 166, 100, 0.4)');
    gradientGold.addColorStop(1, 'rgba(198, 166, 100, 0)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: statsData.map(s => s.label),
            datasets: [{ label: 'Mesas', data: statsData.map(s => s.sessions_count), borderColor: '#c6a664', backgroundColor: gradientGold, borderWidth: 3, fill: true, tension: 0.4 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { minRotation: 45, maxRotation: 45, font: { size: 9 } } }, y: { beginAtZero: true } } }
    });

    // 2. LÓGICA DE CRIAÇÃO
    let cupCount = 0;
    let currentEditingIndex = null;
    let searchTimeout;
    let currentSessionId = null;

    function addNewCup() {
        if(cupCount >= 50) return;
        const index = cupCount++;
        const emptyState = document.getElementById('empty-state');
        if(emptyState) emptyState.style.display = 'none';

        const html = `
            <div class="cup-card shadow-sm" id="cup-row-${index}">
                <div class="cup-header border-bottom">
                    <div class="cup-number">${cupCount}</div>
                    <div class="nickname-container">
                        <label class="nickname-label">Apelido da Xícara *</label>
                        <input type="text" name="items[${index}][nickname]" class="nickname-input js-nickname" placeholder="Ex: Lote A..." autocomplete="off">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="is-avulso-badge">AVULSA</span>
                        <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle" onclick="openTechInfo(${index})">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3 bg-white">
                    <div class="row g-2">
                        <div class="col-12 mb-2 position-relative">
                            <div class="input-group input-group-sm border rounded-pill overflow-hidden bg-light">
                                <span class="input-group-text border-0 bg-transparent ps-3"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent js-sample-search" placeholder="Associar Amostra..." onkeyup="searchSample(this, ${index})">
                            </div>
                            <input type="hidden" name="items[${index}][sample_id]" class="js-sample-id">
                            <div class="sample-result-list shadow-lg" id="results-${index}"></div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-sm border rounded-pill overflow-hidden bg-light">
                                <span class="input-group-text border-0 bg-transparent ps-3 text-muted"><i class="bi bi-chat-left-text"></i></span>
                                <input type="text" name="items[${index}][pre_roast_obs]" class="form-control border-0 bg-transparent" placeholder="Observação Pré-Prova...">
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="items[${index}][is_avulso]" class="js-is-avulso" value="0">
                <input type="hidden" name="items[${index}][variety]" class="js-variety">
                <input type="hidden" name="items[${index}][drying_process]" class="js-drying_process">
                <input type="hidden" name="items[${index}][fermentation]" class="js-fermentation">
                <input type="hidden" name="items[${index}][humidity]" class="js-humidity">
                <input type="hidden" name="items[${index}][screen]" class="js-screen">
                <input type="hidden" name="items[${index}][lot_id]" class="js-lot_id">
                <input type="hidden" name="items[${index}][altitude]" class="js-altitude">
                <input type="hidden" name="items[${index}][crop]" class="js-crop">
                <input type="hidden" name="items[${index}][origin]" class="js-origin">
                <input type="hidden" name="items[${index}][notes]" class="js-notes"> 
                <input type="hidden" name="items[${index}][position]" value="${cupCount}">
            </div>`;
        document.getElementById('cups-wrapper').insertAdjacentHTML('beforeend', html);
        updateCounter();
        toggleSubmitButton();
    }

    function removeLastCup() {
        if(cupCount <= 0) return;
        document.getElementById(`cup-row-${--cupCount}`).remove();
        updateCounter();
        if(cupCount === 0) document.getElementById('empty-state').style.display = 'block';
        validateForm();
    }

    function updateCounter() { document.getElementById('cup-counter').innerText = `${cupCount} Xícaras`; }

    function searchSample(input, index) {
        const query = input.value;
        const resultsBox = document.getElementById(`results-${index}`);
        if(query.length < 2) { resultsBox.style.display = 'none'; return; }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch(`/registries/samples/search?search=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    let html = '';
                    data.forEach(s => {
                        html += `<div class="sample-item" onclick='selectSample(${index}, ${JSON.stringify(s).replace(/'/g, "&#39;")})'><b>${s.name}</b> <small class="text-muted">| ${s.variety || 'N/A'}</small></div>`;
                    });
                    resultsBox.innerHTML = html || '<div class="p-2 small">Nenhuma amostra</div>';
                    resultsBox.style.display = 'block';
                });
        }, 300);
    }

    function selectSample(index, sample) {
        const row = document.getElementById(`cup-row-${index}`);
        if (!row) return;
        row.querySelector('.js-sample-id').value = sample.id;
        row.querySelector('.js-sample-search').value = sample.name;
        const mapping = { 'variety': sample.variety, 'drying_process': sample.drying_process, 'fermentation': sample.fermentation, 'humidity': sample.humidity, 'screen': sample.screen, 'lot_id': sample.supplier_batch, 'altitude': sample.altitude, 'crop': sample.crop, 'origin': sample.origin_name };
        Object.keys(mapping).forEach(key => { const el = row.querySelector(`.js-${key}`); if(el) el.value = mapping[key] || ''; });
        document.getElementById(`results-${index}`).style.display = 'none';
        validateForm();
    }

    function openSessionInfo(sessionId) {
        currentSessionId = sessionId;
        const modalContent = document.getElementById('general-modal-content');
        const modalElement = document.getElementById('modalGeneralInfo');
        
        modalContent.innerHTML = `
            <div class="modal-content p-5 text-center border-0">
                <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
                <p class="text-muted fw-bold">Carregando detalhes da mesa...</p>
            </div>
        `;
        
        const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modalInstance.show();
        
        fetch("{{ url('laboratory/session-info') }}/" + sessionId)
            .then(response => response.text())
            .then(html => {
                modalContent.innerHTML = html;
                const qrElement = document.getElementById("qrcode");
                const linkInput = document.getElementById("linkVisitante");
                
                if (qrElement && linkInput) {
                    qrElement.innerHTML = ""; 
                    new QRCode(qrElement, {
                        text: linkInput.value,
                        width: 140,
                        height: 140,
                        colorDark : "#4b3621",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                    });
                }
            });
    }

    function downloadQRCode() {
        const sourceCanvas = document.querySelector('#qrcode canvas');
        if (!sourceCanvas) { alert("O QR Code ainda não foi gerado."); return; }

        let titleText = "Mesa de Prova";
        const modalTitleElement = document.querySelector('#modalGeneralInfo h4.fw-bold');
        if (modalTitleElement) titleText = modalTitleElement.innerText.trim();

        const brandText = "qgrader.site";
        const subTitleText = `ID #${currentSessionId || '--'}`;

        const padding = 30; quietZone = 15; borderThickness = 6; headerHeight = 110; borderColor = "#4b3621";
        const qrSize = sourceCanvas.width;
        const canvasWidth = qrSize + (padding * 2) + (borderThickness * 2) + (quietZone * 2);
        const canvasHeight = headerHeight + qrSize + (padding * 2) + (borderThickness * 2) + (quietZone * 2);

        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = canvasWidth; finalCanvas.height = canvasHeight;
        const ctx = finalCanvas.getContext('2d');

        ctx.fillStyle = "#ffffff"; ctx.fillRect(0, 0, canvasWidth, canvasHeight);
        ctx.textAlign = 'center';
        ctx.font = '14px Arial'; ctx.fillStyle = "#999999"; ctx.fillText(brandText, canvasWidth / 2, 35);
        ctx.font = 'bold 22px Arial'; ctx.fillStyle = borderColor; ctx.fillText(titleText, canvasWidth / 2, 65);
        ctx.font = '16px Arial'; ctx.fillStyle = "#666666"; ctx.fillText(subTitleText, canvasWidth / 2, 90);

        const borderX = padding; borderY = headerHeight + padding;
        const borderBoxSize = qrSize + (borderThickness * 2) + (quietZone * 2);
        ctx.fillStyle = borderColor; ctx.fillRect(borderX, borderY, borderBoxSize, borderBoxSize);
        ctx.fillStyle = "#ffffff"; ctx.fillRect(borderX + borderThickness, borderY + borderThickness, qrSize + (quietZone * 2), qrSize + (quietZone * 2));
        ctx.drawImage(sourceCanvas, borderX + borderThickness + quietZone, borderY + borderThickness + quietZone);

        const link = document.createElement('a');
        const safeTitle = titleText.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        link.download = `QR_Mesa_${currentSessionId}_${safeTitle}.png`;
        link.href = finalCanvas.toDataURL("image/png");
        link.click();
    }

    function openCupInfo(cupItemId) {
        const modalContent = document.getElementById('general-modal-content');
        modalContent.innerHTML = '<div class="modal-content p-5 text-center"><div class="spinner-border text-primary mx-auto"></div></div>';
        new bootstrap.Modal(document.getElementById('modalGeneralInfo')).show();
        fetch("{{ url('laboratory/cupping-info') }}/" + cupItemId)
            .then(r => r.text()).then(html => { modalContent.innerHTML = html; });
    }

    function openTechInfo(index) {
        currentEditingIndex = index;
        const modalContent = document.getElementById('tech-modal-content');
        modalContent.innerHTML = '<div class="modal-content p-5 text-center"><div class="spinner-border text-primary mx-auto"></div></div>';
        new bootstrap.Modal(document.getElementById('modalCupTechInfo')).show();
        fetch(`/laboratory/cup-tech-info/${index}`).then(r => r.text()).then(html => {
            modalContent.innerHTML = html;
            const mainRow = document.getElementById(`cup-row-${index}`);
            document.querySelectorAll('.tech-field').forEach(f => { const hf = mainRow.querySelector(`.js-${f.dataset.field}`); if(hf) f.value = hf.value; });
            const toggle = document.getElementById('modal-avulso-toggle');
            const isAvulso = mainRow.querySelector('.js-is-avulso').value === "1";
            const hasSample = mainRow.querySelector('.js-sample-id').value !== "";
            toggle.checked = isAvulso;
            if(hasSample) { toggle.disabled = true; toggleModalFields(false); } else { toggle.disabled = false; toggleModalFields(isAvulso); }
        });
    }

    function toggleModalFields(isAvulso) {
        const fields = document.querySelectorAll('.tech-field');
        const icons = document.querySelectorAll('.input-lock-icon');
        fields.forEach(f => { f.readOnly = !isAvulso; f.classList.toggle('field-locked', !isAvulso); });
        icons.forEach(i => { if (isAvulso) { i.classList.add('hide-lock'); } else { i.classList.remove('hide-lock'); } });
        const originContainer = document.getElementById('container-origin-field');
        if (originContainer) originContainer.style.display = isAvulso ? 'none' : 'block';
    }

    function saveTechInfo(index) {
        const mainRow = document.getElementById(`cup-row-${index}`);
        const isAvulso = document.getElementById('modal-avulso-toggle').checked;
        document.querySelectorAll('.tech-field').forEach(f => { const hf = mainRow.querySelector(`.js-${f.dataset.field}`); if(hf) hf.value = f.value; });
        mainRow.querySelector('.js-is-avulso').value = isAvulso ? "1" : "0";
        mainRow.classList.toggle('avulso-active', isAvulso);
        if(isAvulso) { mainRow.querySelector('.js-sample-search').value = "AMOSTRA AVULSA"; mainRow.querySelector('.js-sample-id').value = ""; }
        bootstrap.Modal.getInstance(document.getElementById('modalCupTechInfo')).hide();
        validateForm();
    }

    function validateForm() {
        const title = document.getElementById('mesa-title').value.trim();
        const btnSubmit = document.getElementById('btnSubmitMesa');
        const errorMsg = document.getElementById('footer-error-msg');
        let allValid = (title !== "" && cupCount > 0);
        for (let i = 0; i < cupCount; i++) {
            const row = document.getElementById(`cup-row-${i}`);
            if (!row) continue;
            const nick = row.querySelector('.js-nickname').value.trim();
            const sid = row.querySelector('.js-sample-id').value;
            const sName = row.querySelector('.js-sample-search').value.trim();
            const avulso = row.querySelector('.js-is-avulso').value === "1";
            if (nick === "" || (!sid && !avulso && sName === "")) { allValid = false; break; }
        }
        if(allValid) { btnSubmit.disabled = false; btnSubmit.classList.replace('btn-secondary', 'btn-success'); if(errorMsg) errorMsg.style.display = 'none'; } 
        else { btnSubmit.disabled = true; btnSubmit.classList.replace('btn-success', 'btn-secondary'); if(cupCount > 0 && errorMsg) errorMsg.style.display = 'block'; }
    }

    function attemptSubmit() { if (!document.getElementById('btnSubmitMesa').disabled) document.getElementById('formCreateCupping').submit(); else document.getElementById('footer-error-msg').style.display = 'block'; }

    document.getElementById('mesaSearch').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.mesa-row').forEach(row => { row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none'; });
    });
</script>

{{-- SEÇÃO DE IMPRESSÃO DINÂMICA --}}
<div id="print-report" class="d-none d-print-block p-4">
    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold mb-0">Laudo Técnico de Qualidade</h2>
            <p class="mb-0 text-muted small">Relatório gerado em: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold" style="color: var(--coffee-brown);">UNIQUE CAFÉS</h4>
        </div>
    </div>

    @foreach($sessions as $session)
        @php
            $printActiveAttributes = collect($scaAttributesMap)->filter(function($attr, $key) use ($session) {
                return $session->profile->$key ?? false;
            });
        @endphp
        <div class="print-session-wrapper">
            <div class="print-session-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small fw-bold">MESA #{{ $session->id }}</span>
                        <h4 class="fw-bold mb-0">{{ $session->title }}</h4>
                        <span class="small">Protocolo: {{ $session->profile->name }} | Provadores: {{ $session->participants_count }}</span>
                    </div>
                    <div class="text-end">
                        <div class="text-uppercase fw-bold" style="font-size: 0.7rem;">Status: {{ $session->status }}</div>
                        <div class="small">Data: {{ ($session->started_at ?? $session->created_at)->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="print-cup-container">
                <div class="print-cup-row fw-bold text-muted text-uppercase" style="border-bottom: 1.5pt solid #333; background: #f9f9f9;">
                    <div class="col-print-pos">#</div>
                    <div class="col-print-info">Amostra / Xícara</div>
                    @foreach($printActiveAttributes as $attr)
                        <div class="col-print-attr">{{ $attr['short'] }}</div>
                    @endforeach
                    <div class="col-print-final">SCA</div>
                </div>

                @foreach($session->items as $item)
                    <div class="print-cup-row">
                        <div class="col-print-pos">{{ $item->position }}</div>
                        <div class="col-print-info">
                            <span class="fw-bold d-block">{{ $item->nickname }}</span>
                            <span class="text-muted" style="font-size: 6pt;">{{ $item->is_avulso ? 'Avulsa' : optional($item->sample)->name }}</span>
                        </div>
                        @foreach($printActiveAttributes as $key => $attr)
                            <div class="col-print-attr">
                                {{ number_format($item->evaluations->avg($attr['field']) ?? 0, 1) }}
                            </div>
                        @endforeach
                        <div class="col-print-final">{{ number_format($item->evaluations->avg('final_score') ?? 0, 2) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="print-session-footer"></div>
        </div>
    @endforeach
</div>

{{-- MODAIS --}}
<div class="modal fade" id="modalCupTechInfo" tabindex="-1" aria-hidden="true" style="z-index: 1100;"><div class="modal-dialog modal-dialog-centered" id="tech-modal-content"></div></div>
<div class="modal fade" id="modalGeneralInfo" tabindex="-1" aria-hidden="true" style="z-index: 1110;"><div class="modal-dialog modal-dialog-centered" id="general-modal-content"></div></div>

@include('laboratory.partials.modal_create_cupping')
@include('laboratory.partials.modal_filter')

@push('scripts')
<script>
    // Definimos a função no escopo global (window) no arquivo principal
    window.gerarRelatorioPdf = function() {
        console.log("Botão disparado com sucesso!");

        // Buscamos o select dentro do modal aberto
        const select = document.getElementById('selectProvadorPdf');
        
        if (!select) {
            alert("Erro: Campo de seleção não encontrado. Tente fechar e abrir o modal novamente.");
            return;
        }

        const val = select.value;
        if (!val || val === "") {
            alert("Por favor, selecione um provador na lista.");
            return;
        }

        // Pegamos o ID da sessão que está guardado no select (via data-attribute ou variável)
        // Como o ID muda por mesa, vamos buscar o valor dinâmico
        const sessionId = select.getAttribute('data-session-id');
        
        // Construímos a URL. 
        // Nota: O caminho '/laboratory/cupping-pdf/' deve bater com seu web.php
        const finalUrl = `/laboratory/cupping-pdf/${sessionId}?target=${val}`;

        console.log("Abrindo PDF:", finalUrl);
        window.open(finalUrl, '_blank');
    };
</script>
@endpush
@endsection