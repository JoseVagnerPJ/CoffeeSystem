@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f1ee; min-height: 100vh;">
    {{-- Saudação --}}
    <div class="mb-4">
        <h2 class="h3 fw-bold mb-0" style="color: #3d2b1f;">Bem vindo, {{ $user->name }}! 👋</h2>
    </div>

    {{-- PARTE INFERIOR: MAPA DE ESTOQUE TOTAL --}}
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

    {{-- GRID DE PERFIS (LINHA ÚNICA COLORIDA COM PELÍCULA) --}}
    <h6 class="fw-bold text-uppercase mb-3" style="color: #c0a16b; font-size: 0.75rem; letter-spacing: 1px;">Estoque por Perfil (Filtragem Rápida)</h6>
    <div class="sensory-scroll-container mb-4">
        <div class="sensory-single-row">
            @forelse($estoquePorPerfil as $item)
            
            <div class="card profile-card border-0 shadow-sm" 
                id="profile-{{ $item->sensory_profile_id }}"
                onclick="toggleProfileFilter({{ $item->sensory_profile_id }})"
                style="background-color: {{ $item->sensoryProfile->color_hex ?? '#3d2b1f' }}; min-width: 260px;">
                
                {{-- Película Transparente Interna --}}
                <div class="glass-film p-2 d-flex flex-column justify-content-center h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- Nome do perfil e referência --}}
                        <span class="fw-bold profile-name text-black text-uppercase" style="font-size: 1.1rem; color: #000 !important;">
                            {{ $item->sensoryProfile->name }}
                        </span>
                        <span class="fw-bold" style="font-size: 0.65rem; color: #000 !important; opacity: 0.8;">#{{ $item->reference }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-1">
                        <h4 class="fw-bold mb-0 text-black" style="font-size: 1.3rem; color: #000 !important;">
                            {{ number_format($item->total_kg, 1) }}<small style="font-size: 0.7rem;">kg</small>
                        </h4>
                        <span class="fw-bold text-black" style="font-size: 0.85rem; color: #000 !important;">
                            {{ $item->total_sacas }} SACAS
                        </span>
                    </div>
                </div>
            </div>
            @empty
                <div class="w-100 text-center py-4 bg-white shadow-sm" style="border-radius: 15px;">
                    <span class="text-muted">Não existem dados a exibir</span>
                </div>
            @endforelse
        </div>
    </div>

{{-- PARTE SUPERIOR: DOIS GRÁFICOS ALINHADOS --}}
<div class="row g-4 mb-4">
    
    {{-- Coluna do Laboratório (7/12 avos da tela) --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0" style="color: #3d2b1f;">PRODUTIVIDADE LABORATÓRIO</h6>
                <div class="btn-group btn-group-sm shadow-sm">
                    @foreach(['D','S','M','T','Se','A'] as $p)
                        <button type="button" onclick="updateChartLab('{{$p}}')" 
                                class="btn btn-{{ $periodo == $p ? 'dark' : 'outline-dark' }} btn-lab-filter" 
                                id="btn-lab-{{$p}}">{{$p}}</button>
                    @endforeach
                </div>
            </div>
            <div style="height: 250px;">
                @if($mesasData->isEmpty() && $xicarasData->isEmpty())
                    <div class="h-100 d-flex align-items-center justify-content-center text-muted">Não existem dados a exibir</div>
                @else
                    <canvas id="chartLab"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Coluna de Movimentação (5/12 avos da tela) --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark" id="tituloMovimentacao">ÚLTIMOS RECEBIMENTOS</h6>
                
                <div class="btn-group shadow-sm">
                    <button id="btn-tipo-entrada" onclick="toggleMovimentacao('entrada')" class="btn btn-sm btn-success text-white" title="Ver Recebimentos">
                        📈 Entradas
                    </button>
                    <button id="btn-tipo-saida" onclick="toggleMovimentacao('saida')" class="btn btn-sm btn-outline-danger" title="Ver Saídas">
                        📉 Saídas
                    </button>
                </div>
            </div>
            <div style="height: 250px;">
                <canvas id="chartUltimos"></canvas>
            </div>

            {{-- NOVOS FILTROS DE DATA ABAIXO DO GRÁFICO --}}
            <div class="mt-3 pt-3 border-top">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1" style="font-size: 0.65rem;">DATA INICIAL</label>
                        <input type="date" id="filter_start_date" class="form-control form-control-sm border-0 bg-light" onchange="refreshMovimentacao()">
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1" style="font-size: 0.65rem;">DATA FINAL</label>
                        <input type="date" id="filter_end_date" class="form-control form-control-sm border-0 bg-light" onchange="refreshMovimentacao()">
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1" style="font-size: 0.65rem;">FILTRO RÁPIDO</label>
                        <select class="form-select form-select-sm border-0 bg-light fw-bold" style="font-size: 0.75rem;" id="quick_filter" onchange="applyQuickFilter(this.value)">
                            <option value="last_week">Última semana</option>
                            <option value="curr_month">Mês atual</option>
                            <option value="last_month">Último mês</option>
                            <option value="this_year">Esse ano</option>
                            <option value="last_year">Último ano</option>
                            <option value="all">Geral</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<style>
    /* Estilo do Grid Horizontal de 3 Linhas */
    .sensory-scroll-container { 
        overflow-x: auto; 
        padding-bottom: 15px;
        white-space: nowrap;
    }
    .sensory-single-row {
        display: flex;
        gap: 12px;
        height: 100px; /* Altura fixa para manter os cards alinhados */
    }
    .profile-card { 
        cursor: pointer; 
        border-radius: 12px; 
        overflow: hidden; /* Garante que a película não saia das bordas */
        transition: transform 0.2s ease-in-out;
        flex-shrink: 0;
    }
    .glass-film {
        background: rgba(255, 255, 255, 0.23); /* Película branca semi-transparente */
        width: 100%;
        height: 100%;
    }
    .profile-card:hover { 
        transform: scale(1.03); 
    }
    .text-black {
        color: #000000 !important;
    }
    .active-filter { 
        box-shadow: 0 0 0 3px #3d2b1f !important;
        transform: scale(1.05);
    }
    .inactive-filter { opacity: 0.4; filter: grayscale(0.5); }

    .sensory-scroll-container::-webkit-scrollbar { height: 17px; }
    .sensory-scroll-container::-webkit-scrollbar-thumb { background: #c0a16b; border-radius: 17px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Função auxiliar para somar 3 horas aos rótulos de tempo
    function add3Hours(label) {
        if (!label || typeof label !== 'string' || !label.includes(':')) return label;
        let parts = label.split(' ');
        let timePart = parts.length > 1 ? parts[1] : parts[0];
        let datePart = parts.length > 1 ? parts[0] : null;
        let [h, m, s] = timePart.split(':').map(Number);
        h = (h + 3) % 24;
        let newTime = h.toString().padStart(2, '0') + ":" + (m || 0).toString().padStart(2, '0') + (s !== undefined ? ":" + s.toString().padStart(2, '0') : "");
        return datePart ? datePart + " " + newTime : newTime;
    }

    // Variáveis Globais para manter o estado e acesso aos objetos Chart.js
    let activeProfileId = null;
    let currentTipo = 'entrada';
    let chartLab, chartUltimos, chartEstoque;

    /**
     * Atalhos de Data: Preenche os inputs e dispara o refresh centralizado
     */
    function applyQuickFilter(value) {
        const startInput = document.getElementById('filter_start_date');
        const endInput = document.getElementById('filter_end_date');
        const now = new Date();
        let start = new Date();
        let end = new Date();

        switch(value) {
            case 'last_week':
                start.setDate(now.getDate() - 7);
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
                start = new Date(2025, 0, 1); // Marco inicial do sistema
                break;
        }

        startInput.value = start.toISOString().split('T')[0];
        endInput.value = end.toISOString().split('T')[0];
        
        refreshMovimentacao();
    }

    /**
     * Função Centralizada para buscar dados com TODOS os filtros (Tipo + Perfil + Datas)
     */
    async function refreshMovimentacao() {
        const start = document.getElementById('filter_start_date').value;
        const end = document.getElementById('filter_end_date').value;
        const perfil = activeProfileId;

        try {
            const response = await fetch(`/dashboard/movimentacao-data?tipo=${currentTipo}&perfil_id=${perfil || ''}&start_date=${start}&end_date=${end}`);
            const result = await response.json();

            const tituloElem = document.getElementById('tituloMovimentacao');
            if(tituloElem) tituloElem.innerText = result.titulo;

            if (chartUltimos) {
                const color = currentTipo === 'entrada' ? '#c0a16b' : '#dc3545';
                const bgColor = currentTipo === 'entrada' ? 'rgba(192, 161, 107, 0.1)' : 'rgba(220, 53, 69, 0.1)';
                
                chartUltimos.data.labels = result.dados.map(d => add3Hours(d.data));
                chartUltimos.data.datasets[0].data = result.dados.map(d => d.peso);
                chartUltimos.data.datasets[0].label = currentTipo === 'entrada' ? 'kg Recebido' : 'kg Torrado';
                chartUltimos.data.datasets[0].borderColor = color;
                chartUltimos.data.datasets[0].backgroundColor = bgColor;
                chartUltimos.update(); 
            }
        } catch (error) {
            console.error("Erro ao carregar movimentação:", error);
        }
    }

    /**
     * Alternar entre Entradas e Saídas (Mantém Perfil e Data)
     */
    function toggleMovimentacao(tipo) {
        currentTipo = tipo;
        
        // Ajuste visual dos botões
        const btnEntrada = document.getElementById('btn-tipo-entrada');
        const btnSaida = document.getElementById('btn-tipo-saida');
        
        if (tipo === 'entrada') {
            btnEntrada.classList.replace('btn-outline-success', 'btn-success');
            btnEntrada.classList.add('text-white');
            btnSaida.classList.replace('btn-danger', 'btn-outline-danger');
            btnSaida.classList.remove('text-white');
        } else {
            btnSaida.classList.replace('btn-outline-danger', 'btn-danger');
            btnSaida.classList.add('text-white');
            btnEntrada.classList.replace('btn-success', 'btn-outline-success');
            btnEntrada.classList.remove('text-white');
        }

        refreshMovimentacao();
    }

    /**
     * Filtro por Perfil Sensorial (Mantém Tipo e Data)
     */
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
        
        refreshMovimentacao();
    }

    /**
     * AJAX: Atualizar Gráfico do Laboratório
     */
    async function updateChartLab(periodo) {
        try {
            const response = await fetch(`{{ route('dashboard.laboratorio_data') }}?periodo=${periodo}`);
            const res = await response.json();
            
            if (chartLab) {
                chartLab.data.labels = res.labels.map(l => add3Hours(l));
                chartLab.data.datasets[0].data = res.mesas;
                chartLab.data.datasets[1].data = res.xicaras;
                chartLab.update();
            }

            document.querySelectorAll('.btn-lab-filter').forEach(b => b.classList.replace('btn-dark', 'btn-outline-dark'));
            const btnAtivo = document.getElementById(`btn-lab-${periodo}`);
            if (btnAtivo) btnAtivo.classList.replace('btn-outline-dark', 'btn-dark');
        } catch (e) {
            console.error("Erro ao atualizar dados do laboratório:", e);
        }
    }

    /**
     * Inicialização de todos os gráficos
     */
    function initCharts() {
        // 1. Gráfico Lab
        const elLab = document.getElementById('chartLab');
        if (elLab) {
            chartLab = new Chart(elLab, {
                type: 'line',
                data: {
                    labels: {!! json_encode($mesasData->pluck('data')) !!}.map(l => add3Hours(l)),
                    datasets: [
                        { label: 'Mesas', data: {!! json_encode($mesasData->pluck('total')) !!}, borderColor: '#3d2b1f', backgroundColor: 'rgba(61, 43, 31, 0.1)', fill: true, tension: 0.4 },
                        { label: 'Xícaras', data: {!! json_encode($xicarasData->pluck('total')) !!}, borderColor: '#c0a16b', backgroundColor: 'rgba(192, 161, 107, 0.1)', fill: true, tension: 0.4 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }

        // 2. Gráfico Movimentação (Inicia vazio, carregado pelo applyQuickFilter)
        const elUltimos = document.getElementById('chartUltimos');
        if (elUltimos) {
            chartUltimos = new Chart(elUltimos, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{ label: 'kg', data: [], borderColor: '#c0a16b', backgroundColor: 'rgba(192, 161, 107, 0.1)', fill: true, tension: 0.4, borderWidth: 3 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // 3. Gráfico Mapa de Estoque
        const elEstoque = document.getElementById('chartEstoqueTotal');
        if (elEstoque) {
            chartEstoque = new Chart(elEstoque, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.name')) !!},
                    datasets: [{
                        data: {!! json_encode($estoquePorPerfil->pluck('total_kg')) !!},
                        backgroundColor: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.color_hex')) !!},
                        borderRadius: 5
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false } } }
            });
        }
    }

    function enableHorizontalScroll() {
        const container = document.querySelector('.sensory-scroll-container');
        if (container) {
            container.addEventListener('wheel', (evt) => {
                if (evt.deltaY !== 0) {
                    evt.preventDefault();
                    container.scrollLeft += evt.deltaY;
                }
            });
        }
    }

    window.onload = () => {
        initCharts();
        enableHorizontalScroll();
        // Dispara o filtro padrão (Última semana) que já preenche datas e carrega o gráfico
        applyQuickFilter('last_week'); 
        updateChartLab('D');
    };
</script>
@endsection