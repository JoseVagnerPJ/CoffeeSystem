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
    {{-- Coluna de Movimentação --}}
<div class="col-lg-6">
    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark" id="tituloMovimentacao">RECEBIMENTOS</h6>
            
            <div class="btn-group shadow-sm">
                <button onclick="toggleMovimentacao('entrada')" id="btn-mov-entrada" class="btn btn-sm btn-success text-white">📈 Entradas</button>
                <button onclick="toggleMovimentacao('saida')" id="btn-mov-saida" class="btn btn-sm btn-outline-danger">📉 Saídas</button>
            </div>
        </div>

        <div style="height: 200px;">
            <canvas id="chartUltimos"></canvas>
        </div>

        {{-- FILTROS DE DATA --}}
        <div class="mt-3 pt-3 border-top">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">DATA INICIAL</label>
                    <input type="date" id="filter_start_date" class="form-control form-control-sm border-0 bg-light" onchange="refreshMovimentacao()">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">DATA FINAL</label>
                    <input type="date" id="filter_end_date" class="form-control form-control-sm border-0 bg-light" onchange="refreshMovimentacao()">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted">FILTRO RÁPIDO</label>
                    <select id="quick_filter" class="form-select form-select-sm border-0 bg-light fw-bold" onchange="applyQuickFilter(this.value)">
                        <option value="last_week">Última Semana</option>
                        <option value="curr_month">Mês Atual</option>
                        <option value="last_month">Último Mês</option>
                        <option value="this_year">Este Ano</option>
                        <option value="last_year">Último Ano</option>
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
    .active-filter { box-shadow: 0 0 15px rgba(0,0,0,0.2) !important; background: #fffceb; }
    .inactive-filter { opacity: 0.5; }

    .sensory-scroll-container::-webkit-scrollbar { height: 17px; }
    .sensory-scroll-container::-webkit-scrollbar-thumb { background: #c0a16b; border-radius: 17px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let currentTipo = 'entrada';
    let activeProfileId = null;
    let chartLab, chartUltimos, chartEstoque;

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
                start = new Date(2020, 0, 1); 
                break;
        }

        startInput.value = start.toISOString().split('T')[0];
        endInput.value = end.toISOString().split('T')[0];
        
        refreshMovimentacao();
    }
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
    const dataRecebimentoInicial = {!! json_encode($ultimosRecebimentos) !!};
    let activeProfileId = null;
    let chartLab, chartUltimos, chartEstoque;

    async function refreshMovimentacao() {
        const start = document.getElementById('filter_start_date').value;
        const end = document.getElementById('filter_end_date').value;
        const perfil = activeProfileId;

        try {
            const url = `/dashboard/movimentacao-data?tipo=${currentTipo}&perfil_id=${perfil || ''}&start_date=${start}&end_date=${end}`;
            const response = await fetch(url);
            const result = await response.json();

            if (chartUltimos) {
                const isEntrada = currentTipo === 'entrada';
                const color = isEntrada ? '#c0a16b' : '#dc3545';
                const bgColor = isEntrada ? 'rgba(192, 161, 107, 0.1)' : 'rgba(220, 53, 69, 0.1)';
                
                chartUltimos.data.labels = result.dados.map(d => d.data);
                chartUltimos.data.datasets[0].data = result.dados.map(d => d.peso);
                chartUltimos.data.datasets[0].label = result.label;
                chartUltimos.data.datasets[0].borderColor = color;
                chartUltimos.data.datasets[0].backgroundColor = bgColor;
                chartUltimos.update(); 

                document.getElementById('tituloMovimentacao').innerText = result.titulo;
            }
        } catch (error) {
            console.error("Erro ao carregar movimentação:", error);
        }
    }

    /**
     * AJAX: Alternar entre Entradas (Recebimentos) e Saídas (Torras)
     */
    function toggleMovimentacao(tipo) {
        currentTipo = tipo;
        
        // Estilo dos botões
        const btnE = document.getElementById('btn-mov-entrada');
        const btnS = document.getElementById('btn-mov-saida');
        
        if(tipo === 'entrada') {
            btnE.className = 'btn btn-sm btn-success text-white';
            btnS.className = 'btn btn-sm btn-outline-danger';
        } else {
            btnE.className = 'btn btn-sm btn-outline-success';
            btnS.className = 'btn btn-sm btn-danger text-white';
        }

        refreshMovimentacao();
    }

    /**
     * AJAX: Atualizar Gráfico do Laboratório (D, S, M, T, Se, A)
     */
    async function updateChartLab(periodo) {
    try {
        const response = await fetch(`{{ route('dashboard.laboratorio_data') }}?periodo=${periodo}`);
        const res = await response.json();
        
        if (chartLab) {
            chartLab.data.labels = res.labels.map(l => add3Hours(l));
            chartLab.data.datasets[0].data = res.mesas; // Deve bater com o JSON: 'mesas'
            chartLab.data.datasets[1].data = res.xicaras; // Deve bater com o JSON: 'xicaras'
            chartLab.update();
        }

            // Atualização visual dos botões de filtro
            document.querySelectorAll('.btn-lab-filter').forEach(b => b.classList.replace('btn-dark', 'btn-outline-dark'));
            const btnAtivo = document.getElementById(`btn-lab-${periodo}`);
            if (btnAtivo) btnAtivo.classList.replace('btn-outline-dark', 'btn-dark');
        } catch (e) {
            console.error("Erro ao atualizar dados do laboratório:", e);
        }
    }

    /**
     * Inicialização de todos os gráficos da página
     */
function initCharts() {
        const elLab = document.getElementById('chartLab');
        if (elLab) {
            chartLab = new Chart(elLab, {
                type: 'line',
                data: {
                    // Pega as labels do mesasData (que é o dadosLab mapeado)
                    labels: {!! json_encode($mesasData->pluck('data')) !!}.map(l => add3Hours(l)),
                    datasets: [
                        { 
                            label: 'Mesas', 
                            data: {!! json_encode($mesasData->pluck('total')) !!}, 
                            borderColor: '#3d2b1f', 
                            backgroundColor: 'rgba(61, 43, 31, 0.1)', 
                            fill: true, tension: 0.4 
                        },
                        { 
                            label: 'Xícaras', 
                            data: {!! json_encode($xicarasData->pluck('total')) !!}, 
                            borderColor: '#c0a16b', 
                            backgroundColor: 'rgba(192, 161, 107, 0.1)', 
                            fill: true, tension: 0.4 
                        }
                    ]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    scales: { 
                        x: { ticks: { autoSkip: true, maxRotation: 0 } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // 2. Gráfico de Últimos Recebimentos / Movimentações (Linha)
        const elUltimos = document.getElementById('chartUltimos');
        if (elUltimos) {
            chartUltimos = new Chart(elUltimos, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'kg',
                        data: [],
                        borderColor: '#c0a16b',
                        backgroundColor: 'rgba(192, 161, 107, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    }

        // 3. Gráfico de Mapa de Estoque por Perfil (Barra)
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
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } },
                    scales: { x: { display: false } } 
                }
            });
        }
    }

    /**
     * Filtro rápido ao clicar nos cards sensoriais do armazém
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
     * Helper: Atualiza apenas os dados do gráfico de movimentação
     */
    function updateChartUltimos(data) {
        if (chartUltimos) {
            chartUltimos.data.labels = data.map(d => add3Hours(d.data));
            chartUltimos.data.datasets[0].data = data.map(d => d.peso);
            chartUltimos.update();
        }
    }

    /**
     * Habilita rolagem horizontal do grid de perfis com a rodinha do mouse
     */
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

    // Inicialização ao carregar a janela
    window.onload = () => {
        initCharts();
        applyQuickFilter('last_week'); // Já carrega os dados iniciais
        enableHorizontalScroll();
        updateChartLab('D');
    };
</script>
@endsection