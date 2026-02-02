@extends('layouts.app')

@section('title', 'Central de Cadastros')

@section('content')
<style>
    /* Estilo para os cards superiores */
    .card-modulo {
        transition: all 0.3s ease;
        border: 2px solid transparent !important;
        cursor: pointer;
        min-height: 200px;
        position: relative;
    }
    .card-modulo:not(.locked):hover {
        transform: translateY(-10px);
        border-color: var(--coffee-gold) !important;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    /* Estilo para cards travados */
    .card-modulo.locked {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #f8f9fa;
    }

    /* Estilo para o botão de Equipe (horizontal) */
    .btn-equipe-horizontal {
        transition: all 0.3s ease;
        border: 2px solid transparent !important;
        text-decoration: none;
        display: flex;
        align-items: center;
        background-color: white;
    }

    .btn-equipe-horizontal:not(.locked):hover {
        border-color: var(--coffee-gold) !important;
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .btn-equipe-horizontal.locked {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .icon-box {
        width: 70px;
        height: 70px;
        background-color: var(--coffee-cream);
        color: var(--coffee-brown);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    .card-modulo:not(.locked):hover .icon-box {
        background-color: var(--coffee-brown);
        color: var(--coffee-gold);
    }

    .lock-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        color: var(--coffee-brown);
        font-size: 1.2rem;
    }
</style>

<div class="fade-in px-3">
    <div class="mb-5">
        <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Central de Cadastros</h2>
        <p class="text-muted">Gerencie os ativos, parceiros e a estrutura técnica da Unique Cafés.</p>
    </div>

    <div class="row g-4">
        {{-- Módulo: Fornecedores --}}
        <div class="col-md-6 col-lg-3">
            @if(auth()->user()->hasPermission('supplier_full') || auth()->user()->hasPermission('supplier_view'))
                <a href="{{ route('registries.suppliers.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
            @else
                <div class="card card-modulo locked shadow-sm p-4 text-center h-100" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                    <i class="bi bi-lock-fill lock-badge"></i>
            @endif
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Fornecedores</h5>
                <p class="small text-muted mb-0">Fazendas, produtores e parceiros de café verde.</p>
            @if(auth()->user()->hasPermission('supplier_full') || auth()->user()->hasPermission('supplier_view'))
                </a>
            @else
                </div>
            @endif
        </div>

        {{-- Módulo: Cadastro de Amostras --}}
        <div class="col-md-6 col-lg-3">
            @if(auth()->user()->hasPermission('sample_full') || auth()->user()->hasPermission('sample_view'))
                <a href="{{ route('registries.samples.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
            @else
                <div class="card card-modulo locked shadow-sm p-4 text-center h-100" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                    <i class="bi bi-lock-fill lock-badge"></i>
            @endif
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-clipboard-pulse fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Cadastro de Amostras</h5>
                <p class="small text-muted mb-0">Registro, classificação e controle de amostras de café.</p>
            @if(auth()->user()->hasPermission('sample_full') || auth()->user()->hasPermission('sample_view'))
                </a>
            @else
                </div>
            @endif
        </div>

        {{-- Módulo: Máquinas --}}
        <div class="col-md-6 col-lg-3">
            @if(auth()->user()->hasPermission('machine_full') || auth()->user()->hasPermission('machine_view'))
                <a href="{{ route('registries.machines.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
            @else
                <div class="card card-modulo locked shadow-sm p-4 text-center h-100" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                    <i class="bi bi-lock-fill lock-badge"></i>
            @endif
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-nut fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Máquinas</h5>
                <p class="small text-muted mb-0">Cadastro de Máquinas e equipamentos.</p>
            @if(auth()->user()->hasPermission('machine_full') || auth()->user()->hasPermission('machine_view'))
                </a>
            @else
                </div>
            @endif
        </div>

        {{-- Módulo: Perfil Sensorial --}}
        <div class="col-md-6 col-lg-3">
            @if(auth()->user()->hasPermission('sensory_full') || auth()->user()->hasPermission('sensory_view'))
                <a href="{{ route('registries.sensory-profiles.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
            @else
                <div class="card card-modulo locked shadow-sm p-4 text-center h-100" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                    <i class="bi bi-lock-fill lock-badge"></i>
            @endif
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-activity fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Perfil Sensorial</h5>
                <p class="small text-muted mb-0">Notas, aromas e características técnicas dos cafés.</p>
            @if(auth()->user()->hasPermission('sensory_full') || auth()->user()->hasPermission('sensory_view'))
                </a>
            @else
                </div>
            @endif
        </div>

        {{-- NOVO Módulo: Perfil de Prova SCA --}}
        <div class="col-md-6 col-lg-3">
            @if(auth()->user()->hasPermission('cupping_profile_full') || auth()->user()->hasPermission('cupping_profile_view'))
                <a href="{{ route('registries.cupping-profiles.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
            @else
                <div class="card card-modulo locked shadow-sm p-4 text-center h-100" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                    <i class="bi bi-lock-fill lock-badge"></i>
            @endif
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-award fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Perfil de Prova SCA</h5>
                <p class="small text-muted mb-0">Configuração de protocolos e atributos de avaliação.</p>
            @if(auth()->user()->hasPermission('cupping_profile_full') || auth()->user()->hasPermission('cupping_profile_view'))
                </a>
            @else
                </div>
            @endif
        </div>
    </div>

    {{-- Seção secundária --}}
    <div class="mt-5">
        <h5 class="fw-bold mb-4 opacity-50" style="color: var(--coffee-brown);">Gestão Administrativa</h5>
        <div class="row g-3">
            {{-- Módulo: Equipe --}}
            <div class="col-md-4">
                @if(auth()->user()->hasPermission('team_full') || auth()->user()->hasPermission('team_user_only'))
                    <a href="{{ route('registries.team.index') }}" class="btn-equipe-horizontal p-3 rounded shadow-sm">
                @else
                    <div class="btn-equipe-horizontal locked p-3 rounded shadow-sm" onclick="alert('Você não tem permissão para acessar esse módulo!')">
                @endif
                    <i class="bi bi-people-fill me-3 fs-4 text-muted"></i>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Equipe</h6>
                        <small class="text-muted">Gestão de colaboradores e acessos.</small>
                    </div>
                    @if(auth()->user()->hasPermission('team_full') || auth()->user()->hasPermission('team_user_only'))
                        <i class="bi bi-arrow-right-short fs-4 ms-auto text-dark"></i>
                        </a>
                    @else
                        <i class="bi bi-lock-fill fs-5 ms-auto text-muted"></i>
                        </div>
                    @endif
            </div>
        </div>
    </div>
</div>
@endsection