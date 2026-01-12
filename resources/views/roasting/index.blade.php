@extends('layouts.app')

@section('title', 'Gestão de Torrefação')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root { --p-yellow: #FFD600; --p-brown: #4E342E; --p-purple: #9C27B0; }
    
    /* Kanban e Cards */
    .card-roast { border-left: 10px solid transparent !important; transition: 0.3s; background: white; border-radius: 12px; }
    .border-pending { border-left-color: var(--p-yellow) !important; }
    .border-roasting { border-left-color: var(--p-brown) !important; }
    .border-completed { border-left-color: var(--p-purple) !important; }

    /* KPIs */
    .kpi-mini { padding: 0.5rem 0.8rem; border-radius: 10px; background: #fff; border: 1px solid #eee; height: 75px; display: flex; flex-direction: column; justify-content: center; transition: all 0.3s; }
    .kpi-mini small { font-size: 0.55rem; font-weight: 800; color: #888; text-transform: uppercase; }
    .kpi-mini .val { font-size: 1.1rem; font-weight: 800; color: var(--coffee-brown); line-height: 1.2; }

    /* Botão de Ação Estilizado como KPI */
    .btn-add-kpi { 
        height: 75px; border-radius: 10px; border: none; background: #198754; color: white; 
        display: flex; align-items: center; justify-content: center; gap: 8px; 
        font-weight: 800; font-size: 0.75rem; text-transform: uppercase; transition: 0.3s;
    }
    .btn-add-kpi:hover { background: #157347; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

    /* Seletor de Máquinas Corrigido */
    .machine-nav-container { 
        position: relative; 
        display: flex; 
        align-items: center; 
        background: #f8f9fa; 
        border-radius: 15px; 
        padding: 8px 40px; 
        border: 1px solid #eee; 
        overflow: hidden;
    }
    
    .machine-track { 
        display: flex; 
        gap: 8px; 
        width: 100%; 
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
    }

    .btn-machine-select { 
        flex: 0 0 calc(33.333% - 6px); 
        background: #fff; border: 2px solid transparent; border-radius: 10px; padding: 10px;
        text-align: center; transition: 0.3s; cursor: pointer;
        display: flex; align-items: center; justify-content: center; height: 50px;
        white-space: nowrap;
    }
    
    .btn-machine-select.active { border-color: var(--coffee-gold); background: var(--coffee-brown); color: #fff; }
    
    .nav-arrow { 
        position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;
        background: var(--coffee-brown); color: white; border: none; border-radius: 50%; 
        width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; 
        cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    #prevBtn { left: 8px; }
    #nextBtn { right: 8px; }
    .nav-arrow:disabled { background: #ccc; opacity: 0.5; cursor: not-allowed; }

    /* Botões de Filtro D/S/M */
    .filter-group { display: flex; gap: 2px; background: #eee; padding: 2px; border-radius: 8px; }
    .btn-filter { border: none; background: transparent; font-size: 0.6rem; font-weight: 800; padding: 4px 8px; border-radius: 6px; color: #666; transition: 0.2s; }
    .btn-filter.active { background: #fff; color: var(--coffee-brown); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

    .chart-card { background: #fff; border-radius: 15px; border: 1px solid #eee; height: 100%; padding: 12px; }
    .kanban-wrapper { display: flex; overflow-x: auto; gap: 15px; padding-top: 10px; }
    .kanban-col { min-width: 320px; flex: 1; background: #EBE7E0; border-radius: 15px; padding: 15px; min-height: 500px; }

    .text-danger-clean { color: #dc3545 !important; animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0.3; } }
</style>

<div class="fade-in px-2">
    <div class="row g-3 mb-2">
        <div class="col-lg-8">
            <div class="mb-3">
                <h2 class="h5 fw-bold mb-0">Monitor de Torrefação</h2>
            </div>

            <div class="row g-2">
                <div class="col-md-3">
                    <div class="kpi-mini shadow-sm">
                        <small>Total Torrado <span class="label-period">Mês</span></small>
                        <div class="val" id="kpi-global-weight">0,0 kg</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-mini shadow-sm">
                        <small id="kpi-machine-name">Torrado na Máquina</small>
                        <div class="val" id="kpi-machine-weight">0,0 kg</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-mini shadow-sm" id="clean-status-card">
                        <small>Status de Limpeza</small>
                        <div class="val">
                            <span id="clean-current">0</span> 
                            <small class="fs-6 text-muted">/ <span id="clean-threshold">0</span> torras</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn-add-kpi shadow-sm w-100" data-bs-toggle="modal" data-bs-target="#modalCreateRoast">
                        <i class="bi bi-plus-circle-fill fs-5"></i>
                        Nova Demanda
                    </button>
                </div>
            </div>

            <div class="machine-nav-container shadow-sm mt-3">
                <button class="nav-arrow" onclick="scrollMachines(-1)" id="prevBtn"><i class="bi bi-chevron-left"></i></button>
                <div class="machine-track" id="machineTrack">
                    @foreach($machines as $machine)
                    <div class="btn-machine-select shadow-sm @if($loop->first) active @endif" 
                         onclick="selectMachine(this, {
                            id: {{ $machine->id }}, 
                            name: '{{ $machine->name }}', 
                            current: {{ $machine->current_roasts }}, 
                            threshold: {{ $machine->cleaning_threshold }}
                         })" 
                         data-machine-id="{{ $machine->id }}">
                        <div class="fw-bold text-uppercase mb-0" style="font-size: 0.8rem;">{{ $machine->name }}</div>
                    </div>
                    @endforeach
                </div>
                <button class="nav-arrow" onclick="scrollMachines(1)" id="nextBtn"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">Produção Realizada</small>
                    <div class="filter-group">
                        <button class="btn-filter" onclick="setPeriod('D', this)">D</button>
                        <button class="btn-filter" onclick="setPeriod('S', this)">S</button>
                        <button class="btn-filter active" onclick="setPeriod('M', this)">M</button>
                    </div>
                </div>
                <div style="height: 140px;">
                    <canvas id="roastChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="kanban-wrapper">
       @include('roasting.partials.modal_kanban_content')
    </div>
</div>

<script>
    let selectedMachine = null;
    let currentPeriod = 'M';
    let currentPos = 0;
    const itemsToShow = 3;

    const chartData = {
        'D': { labels: ['08h', '10h', '12h', '14h', '16h', '18h'], global: [5, 12, 8, 20, 15, 5], machine: [2, 5, 3, 10, 4, 1] },
        'S': { labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'], global: [120, 150, 180, 90, 200, 50], machine: [40, 50, 60, 30, 70, 10] },
        'M': { labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'], global: [800, 1200, 950, 1100], machine: [300, 400, 250, 350] }
    };

    const ctx = document.getElementById('roastChart').getContext('2d');
    const roastChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData['M'].labels,
            datasets: [
                { label: 'Global', data: chartData['M'].global, borderColor: '#c0a16b', borderWidth: 3, tension: 0.4, pointRadius: 0 },
                { label: 'Máquina', data: chartData['M'].machine, borderColor: '#3e2723', borderDash: [5, 5], borderWidth: 2, tension: 0.4, pointRadius: 0 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { display: false }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } }
        }
    });

    function setPeriod(period, btn) {
        currentPeriod = period;
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const labelMap = { 'D': 'Hoje', 'S': 'Semana', 'M': 'Mês' };
        document.querySelectorAll('.label-period').forEach(el => el.innerText = labelMap[period]);
        refreshAllData();
    }

    function selectMachine(btn, data) {
        selectedMachine = data;
        document.querySelectorAll('.btn-machine-select').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.demand-card').forEach(card => {
            card.style.display = (card.dataset.machine == data.id) ? 'block' : 'none';
        });
        refreshAllData();
    }

    function refreshAllData() {
        if (!selectedMachine) return;
        let factor = (currentPeriod === 'D') ? 1 : (currentPeriod === 'S' ? 7 : 30);
        let globalSum = 250 * factor; 
        document.getElementById('kpi-global-weight').innerText = globalSum.toLocaleString('pt-BR') + " kg";
        let machineSum = 80 * factor;
        document.getElementById('kpi-machine-name').innerText = "Volume " + selectedMachine.name;
        document.getElementById('kpi-machine-weight').innerText = machineSum.toLocaleString('pt-BR') + " kg";
        document.getElementById('clean-current').innerText = selectedMachine.current;
        document.getElementById('clean-threshold').innerText = selectedMachine.threshold;
        updateCleaningUI(selectedMachine.current, selectedMachine.threshold);
        roastChart.data.labels = chartData[currentPeriod].labels;
        roastChart.data.datasets[0].data = chartData[currentPeriod].global;
        roastChart.data.datasets[1].data = chartData[currentPeriod].machine;
        roastChart.update();
    }

    function updateCleaningUI(current, threshold) {
        const valText = document.getElementById('clean-current');
        if (current >= threshold) {
            valText.className = 'text-danger-clean';
        } else if (current >= threshold * 0.9) {
            valText.className = 'text-warning';
        } else {
            valText.className = '';
        }
    }

    function scrollMachines(dir) {
        const track = document.getElementById('machineTrack');
        const btns = document.querySelectorAll('.btn-machine-select');
        if (btns.length <= itemsToShow) return;

        const itemWidth = btns[0].offsetWidth + 8; 
        currentPos += dir;
        
        if (currentPos < 0) currentPos = 0;
        if (currentPos > btns.length - itemsToShow) currentPos = btns.length - itemsToShow;

        track.style.transform = `translateX(-${currentPos * itemWidth}px)`;
        updateArrowStates(btns.length);
    }

    function updateArrowStates(totalItems) {
        document.getElementById('prevBtn').disabled = (currentPos === 0);
        document.getElementById('nextBtn').disabled = (currentPos >= totalItems - itemsToShow);
        const arrowDisplay = totalItems > itemsToShow ? 'flex' : 'none';
        document.getElementById('prevBtn').style.display = arrowDisplay;
        document.getElementById('nextBtn').style.display = arrowDisplay;
    }

    window.onload = () => {
        const first = document.querySelector('.btn-machine-select.active');
        if(first) first.click();
        setTimeout(() => {
            updateArrowStates(document.querySelectorAll('.btn-machine-select').length);
        }, 100);
    };
</script>

@include('roasting.partials.modal_create_roast_demand')
@endsection