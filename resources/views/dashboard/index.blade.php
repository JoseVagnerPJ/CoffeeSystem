@extends('layouts.app')

@section('title', 'Início')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="fade-in">
    {{-- Header Personalizado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold mb-0" style="color: var(--coffee-brown);">Bem-vindo, José Vagner! 👋</h2>
            <p class="text-muted small mb-0">Painel de Controle Real-Time | {{ Auth::user()->mainCompany->name }}</p>
        </div>
        
        {{-- Filtro de Período Suspenso --}}
        <div class="d-flex gap-2">
            <form action="{{ route('inicio') }}" method="GET" id="filterForm">
                <select name="periodo" class="form-select form-select-sm border-0 shadow-sm fw-bold" onchange="this.form.submit()" style="border-radius: 8px; color: var(--coffee-brown);">
                    <option value="dia" {{ $periodo == 'dia' ? 'selected' : '' }}>Hoje</option>
                    <option value="semana" {{ $periodo == 'semana' ? 'selected' : '' }}>Esta Semana</option>
                    <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Este Mês</option>
                    <option value="trimestre" {{ $periodo == 'trimestre' ? 'selected' : '' }}>Trimestre</option>
                    <option value="semestre" {{ $periodo == 'semestre' ? 'selected' : '' }}>Semestre</option>
                    <option value="ano" {{ $periodo == 'ano' ? 'selected' : '' }}>Anual</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Seção 1: Estoque por Perfil Sensorial --}}
    <h6 class="fw-bold text-uppercase mb-3" style="color: var(--coffee-gold); font-size: 0.75rem; letter-spacing: 1px;">Armazenamento por Perfil Sensorial</h6>
    <div class="row g-3 mb-4">
        @foreach($estoquePorPerfil as $perfil)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid {{ $perfil->color }} !important;">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="fw-bold small" style="color: {{ $perfil->color }}">{{ $perfil->name }}</span>
                        <span class="badge bg-light text-muted small">Lote #{{ $perfil->lote_id }}</span>
                    </div>
                    <h4 class="fw-bold mb-0" style="color: var(--coffee-brown);">{{ $perfil->total_kg }} <small class="fs-6">kg</small></h4>
                    <div class="text-muted small fw-bold">{{ $perfil->total_sacas }} sacas</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Seção 2: Gráficos --}}
    <div class="row">
        {{-- Gráfico de Recebimento --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="background: white; border-radius: 12px;">
                <h6 class="mb-4 fw-bold text-uppercase" style="color: var(--coffee-gold); letter-spacing: 1px;">Recebimentos (KG)</h6>
                <canvas id="chartRecebimento" height="200"></canvas>
            </div>
        </div>

        {{-- Gráfico de Laboratório --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="background: white; border-radius: 12px;">
                <h6 class="mb-4 fw-bold text-uppercase" style="color: var(--coffee-gold); letter-spacing: 1px;">Atividade de Laboratório</h6>
                <canvas id="chartLaboratorio" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Gráfico de Recebimentos
    const ctxRec = document.getElementById('chartRecebimento').getContext('2d');
    new Chart(ctxRec, {
        type: 'line',
        data: {
            labels: {!! json_encode($recebimentos->pluck('data')) !!},
            datasets: [{
                label: 'Peso Recebido (kg)',
                data: {!! json_encode($recebimentos->pluck('peso')) !!},
                borderColor: '#c0a16b',
                backgroundColor: 'rgba(192, 161, 107, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Gráfico de Laboratório (Mesas vs Xícaras)
    const ctxLab = document.getElementById('chartLaboratorio').getContext('2d');
    new Chart(ctxLab, {
        type: 'bar',
        data: {
            labels: {!! json_encode($cuppingData->pluck('data')) !!},
            datasets: [
                {
                    label: 'Mesas de Prova',
                    data: {!! json_encode($cuppingData->pluck('total_mesas')) !!},
                    backgroundColor: '#3e2723',
                    borderRadius: 5,
                },
                {
                    label: 'Xícaras Provadas',
                    data: {!! json_encode($cuppingData->pluck('total_xicaras')) !!},
                    backgroundColor: '#c0a16b',
                    borderRadius: 5,
                }
            ]
        },
        options: { 
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection