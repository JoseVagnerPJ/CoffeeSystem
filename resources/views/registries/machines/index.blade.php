@extends('layouts.app')

@section('content')
<style>
    .btn-action { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0; transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-2px); }
    .progress { height: 8px; border-radius: 10px; background-color: #eee; }
    
    /* Estilo da Linha do Tempo (Timeline) */
    .timeline-item { border-start: 2px solid #0dcaf2; position: relative; padding-left: 20px; padding-bottom: 20px; }
    .timeline-marker { position: absolute; width: 12px; height: 12px; background: #0dcaf2; border-radius: 50%; left: -7px; top: 0; border: 2px solid #fff; }
</style>

<div class="fade-in px-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Gestão de Máquinas</h2>
            <p class="text-muted small mb-0">Controle de torradores e manutenção técnica.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('registries.index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Voltar</a>
            
            {{-- PERMISSÃO: APENAS MACHINE_FULL PODE CRIAR --}}
            @if(auth()->user()->hasPermission('machine_full'))
                <button class="btn btn-sm btn-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCreateMachine">+ Nova Máquina</button>
            @else
                <button class="btn btn-sm btn-dark rounded-pill px-3 opacity-50" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                    <i class="bi bi-lock-fill"></i> + Nova Máquina
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm py-2 mb-4 small animate-fade-in">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="small text-muted text-uppercase">
                        <th>Máquina / Modelo</th>
                        <th>Capacidade</th>
                        <th>Status de Limpeza</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machines as $m)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $m->name }}</div>
                            <div class="small text-muted">{{ $m->brand }} {{ $m->model }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border fw-normal">{{ number_format($m->min_capacity, 1) }}kg - {{ number_format($m->max_capacity, 1) }}kg</span></td>
                        <td style="width: 200px;">
                            @php 
                                $percent = $m->cleaning_threshold > 0 ? ($m->current_roasts / $m->cleaning_threshold) * 100 : 0;
                                $color = $percent > 80 ? 'bg-danger' : ($percent > 50 ? 'bg-warning' : 'bg-success');
                            @endphp
                            <div class="progress shadow-sm"><div class="progress-bar {{ $color }}" style="width: {{ $percent }}%"></div></div>
                            <small class="text-muted" style="font-size: 0.65rem;">{{ $m->current_roasts }} / {{ $m->cleaning_threshold }} torras</small>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                {{-- PERMISSÃO: FULL OU VIEW PODEM VER DETALHES --}}
                                @if(auth()->user()->hasPermission('machine_full') || auth()->user()->hasPermission('machine_view'))
                                    <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInfoMachine{{ $m->id }}"><i class="bi bi-info-circle-fill"></i></button>
                                @else
                                    <button class="btn btn-sm btn-white border btn-action text-muted shadow-sm" onclick="alert('Você não tem permissão para acessar esse módulo!')"><i class="bi bi-lock-fill"></i></button>
                                @endif

                                {{-- PERMISSÃO: APENAS MACHINE_FULL PODE REGISTRAR MANUTENÇÃO --}}
                                @if(auth()->user()->hasPermission('machine_full'))
                                    <button class="btn btn-sm btn-white border btn-action text-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalMaintenanceMachine{{ $m->id }}"><i class="bi bi-tools"></i></button>
                                @else
                                    <button class="btn btn-sm btn-white border btn-action text-muted shadow-sm" onclick="alert('Você não tem permissão para registrar manutenção!')"><i class="bi bi-lock-fill"></i></button>
                                @endif

                                {{-- PERMISSÃO: APENAS MACHINE_FULL PODE INATIVAR --}}
                                @if(auth()->user()->hasPermission('machine_full'))
                                    <form action="{{ route('registries.machines.inactivate', $m->id) }}" method="POST" onsubmit="return confirm('Deseja inativar?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-white border btn-action text-danger shadow-sm"><i class="bi bi-trash3"></i></button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-white border btn-action text-muted shadow-sm" onclick="alert('Você não tem permissão para excluir máquinas!')"><i class="bi bi-lock-fill"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">Nenhuma máquina cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAIS --}}
@include('registries.machines.partials.modal_create')

@foreach($machines as $m)
    @include('registries.machines.partials.modal_info', ['m' => $m])
    @include('registries.machines.partials.modal_maintenance', ['m' => $m])
@endforeach

@endsection