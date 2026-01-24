@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f1ee; min-height: 100vh;">
    {{-- Saudação --}}
    <div class="mb-4">
        <h2 class="h3 fw-bold mb-0" style="color: #3d2b1f;">Bem vindo, {{ $user->name }}! 👋</h2>
    </div>

    {{-- MAPA DE ESTOQUE TOTAL --}}
    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
        <h6 class="fw-bold mb-4 text-dark">MAPA DE ESTOQUE POR PERFIL (TOTAL KG)</h6>
        @if($estoquePorPerfil->isEmpty())
            <div class="py-5 text-center text-muted">Não existem dados a exibir</div>
        @else
            <div style="height: 200px;"><canvas id="chartEstoqueTotal"></canvas></div>
            <div class="d-flex flex-wrap justify-content-center mt-4 gap-3">
                @foreach($estoquePorPerfil as $item)
                    <div class="d-flex align-items-center">
                        <div style="width: 15px; height: 15px; border-radius: 3px; background-color: {{ $item->sensoryProfile->color_hex }}; margin-right: 6px;"></div>
                        <span style="font-size: 0.75rem; color: #000; font-weight: 900;">{{ strtoupper($item->sensoryProfile->name) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- GRID DE PERFIS --}}
    <h6 class="fw-bold text-uppercase mb-3" style="color: #c0a16b; font-size: 0.75rem; letter-spacing: 1px;">Filtragem por Perfil Sensorial</h6>
    <div class="sensory-scroll-container mb-4">
        <div class="sensory-single-row">
            @forelse($estoquePorPerfil as $item)
            <div class="card profile-card border-0 shadow-sm" 
                id="profile-{{ $item->sensory_profile_id }}"
                onclick="toggleProfileFilter({{ $item->sensory_profile_id }})"
                style="background-color: {{ $item->sensoryProfile->color_hex ?? '#3d2b1f' }}; min-width: 260px;">
                <div class="glass-film p-2 d-flex flex-column justify-content-center h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold profile-name text-black text-uppercase" style="font-size: 1.1rem; color: #000 !important;">{{ $item->sensoryProfile->name }}</span>
                        <span class="fw-bold" style="font-size: 0.65rem; color: #000 !important; opacity: 0.8;">#{{ $item->reference }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-1">
                        <h4 class="fw-bold mb-0 text-black" style="font-size: 1.3rem; color: #000 !important;">{{ number_format($item->total_kg, 1) }}<small style="font-size: 0.7rem;">kg</small></h4>
                        <span class="fw-bold text-black" style="font-size: 0.85rem; color: #000 !important;">{{ $item->total_sacas }} SACAS</span>
                    </div>
                </div>
            </div>
            @empty
                <div class="w-100 text-center py-4 bg-white shadow-sm" style="border-radius: 15px;"><span class="text-muted">Não existem dados</span></div>
            @endforelse
        </div>
    </div>

    {{-- PAINEL DE FILTROS GLOBAL --}}
    <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius: 20px; background: #fff;">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="fw-bold text-muted mb-1 small">DATA INICIAL</label>
                <input type="date" id="filter_start_date" class="form-control border-0 bg-light">
            </div>
            <div class="col-md-3">
                <label class="fw-bold text-muted mb-1 small">DATA FINAL</label>
                <input type="date" id="filter_end_date" class="form-control border-0 bg-light">
            </div>
            <div class="col-md-3">
                <label class="fw-bold text-muted mb-1 small">ATALHO RÁPIDO</label>
                <select class="form-select border-0 bg-light fw-bold" id="quick_filter" onchange="applyQuickFilter(this.value)">
                    <option value="last_week">Última Semana</option>
                    <option value="curr_month">Mês Atual</option>
                    <option value="last_month">Último Mês</option>
                    <option value="this_year">Este Ano</option>
                    <option value="last_year">Último Ano</option>
                    <option value="all">Todos (Geral)</option>
                </select>
            </div>
            <div class="col-md-3">
                <button onclick="refreshAllData()" class="btn btn-dark w-100 fw-bold" style="height: 45px;">
                    <i class="bi bi-filter-circle-fill me-2"></i> APLICAR FILTROS
                </button>
            </div>
        </div>
    </div>

    {{-- GRÁFICOS --}}
    <div class="row g-4 mb-4">
        {{-- Produtividade Laboratório --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4" style="color: #3d2b1f;">PRODUTIVIDADE LABORATÓRIO</h6>
                <div style="height: 300px;"><canvas id="chartLab"></canvas></div>
            </div>
        </div>

        {{-- Movimentação --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-dark" id="tituloMovimentacao">MOVIMENTAÇÕES</h6>
                    <div class="btn-group shadow-sm">
                        <button id="btn-tipo-entrada" onclick="toggleMovimentacao('entrada')" class="btn btn-sm btn-success text-white">📈 Entradas</button>
                        <button id="btn-tipo-saida" onclick="toggleMovimentacao('saida')" class="btn btn-sm btn-outline-danger">📉 Saídas</button>
                    </div>
                </div>
                <div style="height: 300px;"><canvas id="chartUltimos"></canvas></div>
            </div>
        </div>
    </div>
</div>

<style>
    .sensory-scroll-container { overflow-x: auto; padding-bottom: 15px; white-space: nowrap; }
    .sensory-single-row { display: flex; gap: 12px; height: 100px; }
    .profile-card { cursor: pointer; border-radius: 12px; overflow: hidden; transition: transform 0.2s; flex-shrink: 0; }
    .glass-film { background: rgba(255, 255, 255, 0.23); width: 100%; height: 100%; }
    .profile-card:hover { transform: scale(1.03); }
    .active-filter { box-shadow: 0 0 0 4px #3d2b1f !important; transform: scale(1.05); z-index: 10; }
    .inactive-filter { opacity: 0.4; filter: grayscale(0.6); }
    .sensory-scroll-container::-webkit-scrollbar { height: 8px; }
    .sensory-scroll-container::-webkit-scrollbar-thumb { background: #c0a16b; border-radius: 10px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let activeProfileId = null;
    let currentTipo = 'entrada';
    let chartLab, chartUltimos, chartEstoque;
    let selectedRangeType = 'last_week';

    /**
     * Lógica de Atalhos Rápidos de Data
     */
    function applyQuickFilter(value) {
        selectedRangeType = value;
        const startInput = document.getElementById('filter_start_date');
        const endInput = document.getElementById('filter_end_date');
        const now = new Date();
        let start = new Date();
        let end = new Date();

        switch(value) {
            case 'last_week':
                start.setDate(now.getDate() - 6);
                break;
            case 'curr_month':
                start = new Date(now.getFullYear(), now.getMonth(), 1);
                break;
            case 'last_month':
                start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                end = new Date(now.getFullYear(), now.getMonth(), 0);
                break;
            case 'this_year':
                start = new Date(now.getFullYear(), 0, 1);
                break;
            case 'last_year':
                start = new Date(now.getFullYear() - 1, 0, 1);
                end = new Date(now.getFullYear() - 1, 11, 31);
                break;
            case 'all':
                start = new Date(2025, 0, 1);
                break;
        }

        startInput.value = start.toISOString().split('T')[0];
        endInput.value = end.toISOString().split('T')[0];
    }

    /**
     * Gera os labels do gráfico e preenche com zero onde não há dados
     */
    function processChartData(apiData, type) {
        const start = new Date(document.getElementById('filter_start_date').value + 'T00:00:00');
        const end = new Date(document.getElementById('filter_end_date').value + 'T23:59:59');
        
        let labels = [];
        let valuesMesas = [];
        let valuesXicaras = [];
        let valuesMov = [];

        // Para o filtro "Todos/Geral", usamos os pontos crus da API
        if (selectedRangeType === 'all') {
            return {
                labels: apiData.map(d => d.data),
                mesas: apiData.map(d => d.mesas || 0),
                xicaras: apiData.map(d => d.xicaras || 0),
                mov: apiData.map(d => d.peso || 0)
            };
        }

        // Para os outros filtros, iteramos dia a dia para garantir a "linha no chão"
        let current = new Date(start);
        const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        while (current <= end) {
            let labelStr = "";
            let dataKey = ""; // Chave para buscar no JSON da API

            const dd = String(current.getDate()).padStart(2, '0');
            const mm = String(current.getMonth() + 1).padStart(2, '0');
            
            if (selectedRangeType === 'last_week') {
                // Top: Dia da semana | Bottom: Data
                labelStr = [diasSemana[current.getDay()], `${dd}/${mm}`];
                dataKey = `${dd}/${mm}`;
            } else {
                labelStr = dd; // Apenas o dia para meses/anos
                dataKey = dd;
            }

            labels.push(labelStr);

            // Busca o valor correspondente no retorno da API (comparando com o campo 'tempo' ou 'data')
            const match = apiData.find(d => (d.tempo || d.data).includes(dd + '/' + mm) || (d.tempo || d.data) == dd);
            
            if (type === 'lab') {
                valuesMesas.push(match ? match.total_mesas : 0);
                valuesXicaras.push(match ? match.total_xicaras : 0);
            } else {
                valuesMov.push(match ? match.peso : 0);
            }

            current.setDate(current.getDate() + 1);
        }

        return { labels, mesas: valuesMesas, xicaras: valuesXicaras, mov: valuesMov };
    }

    /**
     * Refresh Geral de todos os gráficos
     */
    async function refreshAllData() {
        const start = document.getElementById('filter_start_date').value;
        const end = document.getElementById('filter_end_date').value;
        const perfil = activeProfileId;

        // 1. Atualizar Laboratório
        try {
            const resLab = await fetch(`/dashboard/laboratorio-data?start_date=${start}&end_date=${end}`);
            const dataLab = await resLab.json();
            const processedLab = processChartData(dataLab.raw_data, 'lab');
            
            chartLab.data.labels = processedLab.labels;
            chartLab.data.datasets[0].data = processedLab.mesas;
            chartLab.data.datasets[1].data = processedLab.xicaras;
            chartLab.update();
        } catch(e) { console.error(e); }

        // 2. Atualizar Movimentação
        try {
            const resMov = await fetch(`/dashboard/movimentacao-data?tipo=${currentTipo}&perfil_id=${perfil || ''}&start_date=${start}&end_date=${end}`);
            const dataMov = await resMov.json();
            const processedMov = processChartData(dataMov.dados, 'mov');

            document.getElementById('tituloMovimentacao').innerText = dataMov.titulo;
            chartUltimos.data.labels = processedMov.labels;
            chartUltimos.data.datasets[0].data = processedMov.mov;
            chartUltimos.data.datasets[0].label = dataMov.label;
            chartUltimos.update();
        } catch(e) { console.error(e); }
    }

    function toggleMovimentacao(tipo) {
        currentTipo = tipo;
        const btnE = document.getElementById('btn-tipo-entrada');
        const btnS = document.getElementById('btn-tipo-saida');
        btnE.className = tipo === 'entrada' ? 'btn btn-sm btn-success text-white' : 'btn btn-sm btn-outline-success';
        btnS.className = tipo === 'saida' ? 'btn btn-sm btn-danger text-white' : 'btn btn-sm btn-outline-danger';
        refreshAllData();
    }

    function toggleProfileFilter(profileId) {
        const cards = document.querySelectorAll('.profile-card');
        if (activeProfileId === profileId) {
            activeProfileId = null;
            cards.forEach(c => c.classList.remove('active-filter', 'inactive-filter'));
        } else {
            activeProfileId = profileId;
            cards.forEach(c => {
                c.classList.toggle('active-filter', c.id === `profile-${profileId}`);
                c.classList.toggle('inactive-filter', c.id !== `profile-${profileId}`);
            });
        }
        refreshAllData();
    }

    function initCharts() {
        // Lab Chart
        chartLab = new Chart(document.getElementById('chartLab'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Mesas', data: [], borderColor: '#3d2b1f', backgroundColor: 'rgba(61, 43, 31, 0.05)', fill: true, tension: 0.3 },
                    { label: 'Xícaras', data: [], borderColor: '#c0a16b', backgroundColor: 'rgba(192, 161, 107, 0.05)', fill: true, tension: 0.3 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { ticks: { font: { size: 10 } } }, y: { beginAtZero: true } } }
        });

        // Movement Chart
        chartUltimos = new Chart(document.getElementById('chartUltimos'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{ label: 'kg', data: [], borderColor: '#c0a16b', backgroundColor: 'rgba(192, 161, 107, 0.1)', fill: true, tension: 0.3, borderWidth: 3 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { font: { size: 10 } } } } }
        });

        // Estoque Total (Barra fixa)
        chartEstoque = new Chart(document.getElementById('chartEstoqueTotal'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.name')) !!},
                datasets: [{ data: {!! json_encode($estoquePorPerfil->pluck('total_kg')) !!}, backgroundColor: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.color_hex')) !!}, borderRadius: 5 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false } } }
        });
    }

    function enableHorizontalScroll() {
        const container = document.querySelector('.sensory-scroll-container');
        if (container) {
            container.addEventListener('wheel', (evt) => {
                if (evt.deltaY !== 0) { evt.preventDefault(); container.scrollLeft += evt.deltaY; }
            });
        }
    }

    window.onload = () => {
        initCharts();
        enableHorizontalScroll();
        applyQuickFilter('last_week');
        refreshAllData();
    };
</script>
@endsection