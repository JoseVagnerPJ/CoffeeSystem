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
                    <button onclick="toggleMovimentacao('entrada')" class="btn btn-sm btn-outline-success" title="Ver Recebimentos">
                        📈 Entradas
                    </button>
                    <button onclick="toggleMovimentacao('saida')" class="btn btn-sm btn-outline-danger" title="Ver Saídas">
                        📉 Saídas
                    </button>
                </div>
            </div>
            <div style="height: 250px;">
                @if($ultimosRecebimentos->isEmpty())
                    <div class="h-100 d-flex align-items-center justify-content-center text-muted">Não existem dados a exibir</div>
                @else
                    <canvas id="chartUltimos"></canvas>
                @endif
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
    // Variáveis Globais para manter o estado e acesso ao gráfico
    const dataRecebimentoInicial = {!! json_encode($ultimosRecebimentos) !!};
    let activeProfileId = null;
    let chartLab, chartUltimos, chartEstoque;

    // FUNÇÃO GLOBAL: Agora os botões conseguem "enxergar" esta função
    async function toggleMovimentacao(tipo) {
        try {
            const response = await fetch(`/dashboard/movimentacao-data?tipo=${tipo}`);
            const result = await response.json();

            // 1. Atualiza o Título do Card
            const tituloElem = document.getElementById('tituloMovimentacao');
            if(tituloElem) tituloElem.innerText = result.titulo;

            // 2. Define cores: Ouro/Verde para entrada, Vermelho para saída
            const color = tipo === 'entrada' ? '#c0a16b' : '#dc3545';
            const bgColor = tipo === 'entrada' ? 'rgba(192, 161, 107, 0.1)' : 'rgba(220, 53, 69, 0.1)';

            // 3. Atualiza o objeto Chart.js global
            if (chartUltimos) {
                chartUltimos.data.labels = result.dados.map(d => d.data);
                chartUltimos.data.datasets[0].data = result.dados.map(d => d.peso);
                chartUltimos.data.datasets[0].label = tipo === 'entrada' ? 'kg Recebido' : 'kg Torrado';
                chartUltimos.data.datasets[0].borderColor = color;
                chartUltimos.data.datasets[0].backgroundColor = bgColor;
                chartUltimos.update(); 
            }
        } catch (error) {
            console.error("Erro ao alternar gráfico:", error);
        }
    }

    function initCharts() {
        // 1. Gráfico Lab
        const elLab = document.getElementById('chartLab');
        if (elLab) {
            new Chart(elLab, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($mesasData->pluck('data')) !!},
                    datasets: [
                        { label: 'Mesas', data: {!! json_encode($mesasData->pluck('total')) !!}, backgroundColor: '#3d2b1f', borderRadius: 5 },
                        { label: 'Xícaras', data: {!! json_encode($xicarasData->pluck('total')) !!}, backgroundColor: '#c0a16b', borderRadius: 5 }
                    ]
                },
                options: { maintainAspectRatio: false }
            });
        }

        // 2. Últimos Recebimentos (Inicialização)
        const elUltimos = document.getElementById('chartUltimos');
        if (elUltimos) {
            chartUltimos = new Chart(elUltimos, {
                type: 'line',
                data: {
                    labels: dataRecebimentoInicial.map(d => d.data),
                    datasets: [{
                        label: 'kg',
                        data: dataRecebimentoInicial.map(d => d.peso),
                        borderColor: '#c0a16b',
                        backgroundColor: 'rgba(192, 161, 107, 0.1)',
                        fill: true, tension: 0.4
                    }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // 3. Estoque Total
        const elEstoque = document.getElementById('chartEstoqueTotal');
        if (elEstoque) {
            new Chart(elEstoque, {
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
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } },
                    scales: { x: { display: false } }
                }
            });
        }
    }

    async function toggleMovimentacao(tipo) {
        try {
            const response = await fetch(`{{ route('dashboard.movimentacao_data') }}?tipo=${tipo}`);
            const res = await response.json();
            document.getElementById('tituloMovimentacao').innerText = res.titulo;
            if (chartUltimos) {
                chartUltimos.data.labels = res.dados.map(d => d.data);
                chartUltimos.data.datasets[0].data = res.dados.map(d => d.peso);
                chartUltimos.data.datasets[0].borderColor = (tipo === 'entrada') ? '#c0a16b' : '#dc3545';
                chartUltimos.update();
            }
        } catch (e) { console.error(e); }
    }

    function initCharts() {
        // Lab
        const elLab = document.getElementById('chartLab');
        if (elLab) {
            chartLab = new Chart(elLab, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($mesasData->pluck('data')) !!},
                    datasets: [
                        { label: 'Mesas', data: {!! json_encode($mesasData->pluck('total')) !!}, backgroundColor: '#3d2b1f' },
                        { label: 'Xícaras', data: {!! json_encode($xicarasData->pluck('total')) !!}, backgroundColor: '#c0a16b' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // Movimentação
        const elUltimos = document.getElementById('chartUltimos');
        if (elUltimos) {
            chartUltimos = new Chart(elUltimos, {
                type: 'line',
                data: {
                    labels: dataRecebimentoInicial.map(d => d.data),
                    datasets: [{
                        label: 'kg',
                        data: dataRecebimentoInicial.map(d => d.peso),
                        borderColor: '#c0a16b', fill: true, tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // Estoque Total
        const elEstoque = document.getElementById('chartEstoqueTotal');
        if (elEstoque) {
            new Chart(elEstoque, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.name')) !!},
                    datasets: [{
                        data: {!! json_encode($estoquePorPerfil->pluck('total_kg')) !!},
                        backgroundColor: {!! json_encode($estoquePorPerfil->pluck('sensoryProfile.color_hex')) !!}
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: false } } }
            });
        }
    }

    // Scroll Horizontal com Mouse
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

    // 1. Inicialização do Gráfico do Laboratório
    const elLab = document.getElementById('chartLab');
    if (elLab) {
        chartLab = new Chart(elLab, {
            type: 'line', // Alterado de 'bar' para 'line'
            data: {
                labels: [], // Inicia vazio, o AJAX preenche
                datasets: [
                    { 
                        label: 'Mesas', 
                        data: [], 
                        borderColor: '#3d2b1f', 
                        backgroundColor: 'rgba(61, 43, 31, 0.1)',
                        fill: true, 
                        tension: 0.4, // Linha suave
                        borderWidth: 3
                    },
                    { 
                        label: 'Xícaras', 
                        data: [], 
                        borderColor: '#c0a16b', 
                        backgroundColor: 'rgba(192, 161, 107, 0.1)',
                        fill: true, 
                        tension: 0.4, // Linha suave
                        borderWidth: 3
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } } 
            }
        });
    }

    // 2. Função de atualização AJAX
    async function updateChartLab(periodo) {
        try {
            const response = await fetch(`{{ route('dashboard.laboratorio_data') }}?periodo=${periodo}`);
            const res = await response.json();

            if (chartLab) {
                chartLab.data.labels = res.labels;
                chartLab.data.datasets[0].data = res.mesas;
                chartLab.data.datasets[1].data = res.xicaras;
                chartLab.update(); // Atualiza as linhas com animação
            }

            // Estilo dos botões (opcional)
            document.querySelectorAll('.btn-lab-filter').forEach(b => b.classList.replace('btn-dark', 'btn-outline-dark'));
            const btn = document.getElementById(`btn-lab-${periodo}`);
            if (btn) btn.classList.replace('btn-outline-dark', 'btn-dark');
        } catch (e) { console.error("Erro no Lab:", e); }
    }

    // Filtro dos Cards Sensoriais
    function toggleProfileFilter(profileId) {
        const cards = document.querySelectorAll('.profile-card');
        if (activeProfileId === profileId) {
            activeProfileId = null;
            cards.forEach(c => c.classList.remove('active-filter', 'inactive-filter'));
            updateRecebimentos(dataRecebimentoInicial);
        } else {
            activeProfileId = profileId;
            cards.forEach(c => {
                c.classList.toggle('active-filter', c.id === `profile-${profileId}`);
                c.classList.toggle('inactive-filter', c.id !== `profile-${profileId}`);
            });
            const filtered = dataRecebimentoInicial.filter(d => d.perfil_id == profileId);
            updateRecebimentos(filtered);
        }
    }

    function updateRecebimentos(data) {
        if (chartUltimos) {
            chartUltimos.data.labels = data.map(d => d.data);
            chartUltimos.data.datasets[0].data = data.map(d => d.weight ?? d.peso);
            chartUltimos.update();
        }
    }

    window.onload = () => {
        initCharts();
        enableHorizontalScroll();
    };
</script>
@endsection