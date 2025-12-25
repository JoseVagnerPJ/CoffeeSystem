@extends('layouts.app')

@section('title', 'Central de Cadastros')

@section('content')
<style>
    /* Estilo para os cards de módulo */
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
        <p class="text-muted">Gerencie os ativos, parceiros e a estrutura da Unique Cafés.</p>
    </div>

    {{-- AQUI ESTÁ O SEGREDO: Abrir a DIV ROW --}}
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

        {{-- Módulo: Equipe --}}
        <div class="col-md-6 col-lg-3">
            <a href="#" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Equipe</h5>
                <p class="small text-muted mb-0">Gestão de colaboradores e permissões de acesso.</p>
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

        {{-- Módulo: Armazéns --}}
        <div class="col-md-6 col-lg-3">
            <a href="#" class="card card-modulo shadow-sm p-4 text-decoration-none text-center h-100">
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi bi-buildings fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark">Armazéns</h5>
                <p class="small text-muted mb-0">Estrutura física, silos e setores de estocagem.</p>
            </a>
        </div>

    </div> {{-- FIM DA ROW --}}

    {{-- Seção secundária --}}
    <div class="mt-5">
        <h5 class="fw-bold mb-4 opacity-50" style="color: var(--coffee-brown);">Configurações Técnicas</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="bg-white p-3 rounded shadow-sm d-flex align-items-center border-start border-4" style="border-color: var(--coffee-gold) !important;">
                    <i class="bi bi-tag-fill me-3 fs-4 text-muted"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Variedades</h6>
                        <small class="text-muted">Bourbon, Catuaí, Arábica...</small>
                    </div>
                    <a href="#" class="ms-auto text-dark"><i class="bi bi-arrow-right-short fs-4"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection