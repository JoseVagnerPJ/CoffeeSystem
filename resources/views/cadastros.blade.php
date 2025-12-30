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
    }
    .card-modulo:hover {
        transform: translateY(-10px);
        border-color: var(--coffee-gold) !important;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    /* Estilo para o botão de Equipe (horizontal) */
    .btn-equipe-horizontal {
        transition: all 0.3s ease;
        border: 2px solid transparent !important; /* Começa transparente */
        text-decoration: none;
        display: flex;
        align-items: center;
        background-color: white;
    }

    .btn-equipe-horizontal:hover {
        border-color: var(--coffee-gold) !important; /* Dourado apenas no hover */
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
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
    .card-modulo:hover .icon-box {
        background-color: var(--coffee-brown);
        color: var(--coffee-gold);
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
            <a href="{{ route('suppliers.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Fornecedores</h5>
                <p class="small text-muted mb-0">Fazendas, produtores e parceiros de café verde.</p>
            </a>
        </div>

        {{-- Módulo: Cadastro de Amostras --}}
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('samples.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-clipboard-pulse fs-1"></i> {{-- Ícone de Pacotinho --}}
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Cadastro de Amostras</h5>
                <p class="small text-muted mb-0">Registro, classificação e controle de amostras de café.</p>
            </a>
        </div>

        {{-- Módulo: Máquinas --}}
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('machines.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-nut fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Máquinas</h5>
                <p class="small text-muted mb-0">Cadastro de Máquinas e equipamentos.</p>
            </a>
        </div>

        {{-- Módulo: Perfil Sensorial --}}
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('sensory-profiles.index') }}" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-activity fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Perfil Sensorial</h5>
                <p class="small text-muted mb-0">Notas, aromas e características técnicas dos cafés.</p>
            </a>
        </div>
    </div>

    {{-- Seção secundária --}}
    <div class="mt-5">
        <h5 class="fw-bold mb-4 opacity-50" style="color: var(--coffee-brown);">Gestão Administrativa</h5>
        <div class="row g-3">
            {{-- Módulo: Equipe --}}
            <div class="col-md-4">
                <a href="#" class="btn-equipe-horizontal p-3 rounded shadow-sm">
                    <i class="bi bi-people-fill me-3 fs-4 text-muted"></i>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Equipe</h6>
                        <small class="text-muted">Gestão de colaboradores e acessos.</small>
                    </div>
                    <i class="bi bi-arrow-right-short fs-4 ms-auto text-dark"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection