@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f1ee; min-height: 100vh;">
    <div class="mb-4">
        <h2 class="h3 fw-bold mb-0" style="color: #3d2b1f;">Bem vindo, {{ $user->name }}! 👋</h2>
    </div>

    {{-- MAPA DE ESTOQUE --}}
    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
        <h6 class="fw-bold mb-4 text-dark text-uppercase" style="font-size: 0.8rem;">Mapa de Estoque por Perfil (Total KG)</h6>
        @if($estoquePorPerfil->isEmpty())
            <div class="py-5 text-center text-muted">Não existem dados a exibir</div>
        @else
            <div style="height: 200px;"><canvas id="chartEstoqueTotal"></canvas></div>
            <div class="d-flex flex-wrap justify-content-center mt-4 gap-3">
                @foreach($estoquePorPerfil as $item)
                    <div class="d-flex align-items-center">
                        <div style="width: 12px; height: 12px; border-radius: 3px; background-color: {{ $item->sensoryProfile->color_hex }}; margin-right: 6px;"></div>
                        <span style="font-size: 0.7rem; color: #000; font-weight: 800;">{{ strtoupper($item->sensoryProfile->name) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- FILTRO SENSORIAL --}}
    <h6 class="fw-bold text-uppercase mb-3" style="color: #c0a16b; font-size: 0.7rem; letter-spacing: 1px;">Filtragem por Perfil</h6>
    <div class="sensory-scroll-container mb-4">
        <div class="sensory-single-row">
            @foreach($estoquePorPerfil as $item)
            <div class="card profile-card border-0 shadow-sm" id="profile-{{ $item->sensory_profile_id }}" onclick="toggleProfileFilter({{ $item->sensory_profile_id }})" style="background-color: {{ $item->sensoryProfile->color_hex }}; min-width: 240px;">
                <div class="glass-film p-2 d-flex flex-column justify-content-center h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-black text-uppercase" style="font-size: 1rem;">{{ $item->sensoryProfile->name }}</span>
                        <span class="fw-bold small opacity-75">#{{ $item->reference }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-1">
                        <h4 class="fw-bold mb-0 text-black">{{ number_format($item->total_kg, 1) }}<small class="small">kg</small></h4>
                        <span class="fw-bold text-black small">{{ $item->total_sacas }} SACAS</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- PAINEL DE FILTROS --}}
    <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius: 20px;">
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
                <label class="fw-bold text-muted mb-1 small">ATALHO</label>
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
                <button onclick="refreshAllData()" class="btn btn-dark w-100 fw-bold" style="height: 40px;">APLICAR FILTROS</button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4" style="color: #3d2b1f;">PRODUTIVIDADE LABORATÓRIO</h6>
                <div style="height: 300px;"><canvas id="chartLab"></canvas></div>
            </div>
        </div>
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
    .sensory-scroll-container { overflow-x: auto; padding-bottom: 10px; }
    .sensory-single-row { display: flex; gap: 12px; height: 90px; }
    .profile-card { cursor: pointer; border-radius: 12px; overflow: hidden; transition: transform 0.2s; flex-shrink: 0; }
    .glass-film { background: rgba(255, 255, 255, 0.2); width: 100%; height: 100%; }
    .active-filter { box-shadow: 0 0 0 4px #3d2b1f !important; transform: scale(1.03); z-index: 5; }
    .inactive-filter { opacity: 0.4; filter: grayscale(0.7); }
    .text-black { color: #000 !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let activeProfileId = null, currentTipo = 'entrada', chartLab, chartUltimos, chartEstoque;
    let selectedRangeType = 'last_week';

    function applyQuickFilter(value) {
        selectedRangeType = value;
        const startInput = document.getElementById('filter_start_date');
        const endInput = document.getElementById('filter_end_date');
        const now = new Date();
        let start = new Date(), end = new Date();

        switch(value) {
            case 'last_week': start.setDate(now.getDate() - 6); break;
            case 'curr_month': start = new Date(now.getFullYear(), now.getMonth(), 1); break;
            case 'last_month': start = new Date(now.getFullYear(), now.getMonth() - 1, 1); end = new Date(now.getFullYear(), now.getMonth(), 0); break;
            case 'this_year': start = new Date(now.getFullYear(), 0, 1); break;
            case 'last_year': start = new Date(now.getFullYear() - 1, 0, 1); end = new Date(now.getFullYear() - 1, 11, 31); break;
            case 'all': start = new Date(2025, 0, 1); break;
        }
        startInput.value = start.toISOString().split('T')[0];
        endInput.value = end.toISOString().split('T')[0];
    }

    function processChartData(apiData, type) {
        if (selectedRangeType === 'all') {
            return {
                labels: apiData.map(d => d.tempo || d.data),
                mesas: apiData.map(d => d.total_mesas || 0),
                xicaras: apiData.map(d => d.total_xicaras || 0),
                mov: apiData.map(d => d.peso || 0)
            };
        }

        const start = new Date(document.getElementById('filter_start_date').value + 'T00:00:00');
        const end = new Date(document.getElementById('filter_end_date').value + 'T23:59:59');
        let labels = [], vMesas = [], vXicaras = [], vMov = [];
        let current = new Date(start);
        const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        while (current <= end) {
            const dd = String(current.getDate()).padStart(2, '0');
            const mm = String(current.getMonth() + 1).padStart(2, '0');
            const chave = `${dd}/${mm}`;
            labels.push(selectedRangeType === 'last_week' ? [diasSemana[current.getDay()], chave] : dd);
            const match = apiData.find(d => (d.tempo || d.data) === chave);
            if (type === 'lab') {
                vMesas.push(match ? match.total_mesas : 0);
                vXicaras.push(match ? match.total_xicaras : 0);
            } else {
                vMov.push(match ? match.peso : 0);
            }
            current.setDate(current.getDate() + 1);
        }
        return { labels, mesas: vMesas, xicaras: vXicaras, mov: vMov };
    }

    async function refreshAllData() {
        const start = document.getElementById('filter_start_date').value;
        const end = document.getElementById('filter_end_date').value;
        
        // Lab
        try {
            const res = await fetch(`/dashboard/laboratorio-data?start_date=${start}&end_date=${end}&range_type=${selectedRangeType}`);
            const json = await res.json();
            const proc = processChartData(json.raw_data, 'lab');
            chartLab.data.labels = proc.labels;
            chartLab.data.datasets[0].data = proc.mesas;
            chartLab.data.datasets[1].data = proc.xicaras;
            chartLab.update();
        } catch(e) {}

        // Movimentação
        try {
            const res = await fetch(`/dashboard/movimentacao-data?tipo=${currentTipo}&perfil_id=${activeProfileId || ''}&start_date=${start}&end_date=${end}&range_type=${selectedRangeType}`);
            const json = await res.json();
            const proc = processChartData(json.dados, 'mov');
            document.getElementById('tituloMovimentacao').innerText = json.titulo;
            chartUltimos.data.labels = proc.labels;
            chartUltimos.data.datasets[0].data = proc.mov;
            chartUltimos.data.datasets[0].label = json.label;
            chartUltimos.update();
        } catch(e) {}
    }

    function toggleMovimentacao(tipo) {
        currentTipo = tipo;
        document.getElementById('btn-tipo-entrada').className = tipo === 'entrada' ? 'btn btn-sm btn-success text-white' : 'btn btn-sm btn-outline-success';
        document.getElementById('btn-tipo-saida').className = tipo === 'saida' ? 'btn btn-sm btn-danger text-white' : 'btn btn-sm btn-outline-danger';
        refreshAllData();
    }

    function toggleProfileFilter(id) {
        activeProfileId = (activeProfileId === id) ? null : id;
        document.querySelectorAll('.profile-card').forEach(c => {
            c.classList.toggle('active-filter', activeProfileId === parseInt(c.id.split('-')[1]));
            c.classList.toggle('inactive-filter', activeProfileId !== null && activeProfileId !== parseInt(c.id.split('-')[1]));
        });
        refreshAllData();
    }

    function initCharts() {
        chartLab = new Chart(document.getElementById('chartLab'), {
            type: 'line',
            data: { labels: [], datasets: [
                { label: 'Mesas', data: [], borderColor: '#3d2b1f', backgroundColor: 'rgba(61, 43, 31, 0.05)', fill: true, tension: 0.3 },
                { label: 'Xícaras', data: [], borderColor: '#c0a16b', backgroundColor: 'rgba(192, 161, 107, 0.05)', fill: true, tension: 0.3 }
            ]},
            options: { responsive: true, maintainAspectRatio: false }
        });
        chartUltimos = new Chart(document.getElementById('chartUltimos'), {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'kg', data: [], borderColor: '#c0a16b', backgroundColor: 'rgba(192, 161, 107, 0.1)', fill: true, tension: 0.3, borderWidth: 3 }]},
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }}
        });
        chartEstoque = new Chart(document.getElementById('chartEstoqueTotal'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.name')) !!},
                datasets: [{ data: {!! json_encode($estoquePorPerfil->pluck('total_kg')) !!}, backgroundColor: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.color_hex')) !!}, borderRadius: 5 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false } }}
        });
    }

    window.onload = () => {
        initCharts();
        applyQuickFilter('last_week');
        refreshAllData();
    };
</script>
@endsection