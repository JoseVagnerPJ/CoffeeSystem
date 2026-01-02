@extends('layouts.app')

@section('title', 'Gestão da Equipe')

@section('content')
<div class="fade-in px-3">
    {{-- Cabeçalho Limpo (Sem os botões superiores) --}}
    <div class="mb-5">
        <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Configurações da Unidade</h2>
        <p class="text-muted small mb-0">Gerencie os dados da empresa, níveis de acesso e colaboradores.</p>
    </div>

    {{-- Navegação por Abas --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-3">
            <ul class="nav nav-pills card-header-pills custom-pills" id="teamTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button">
                        <i class="bi bi-buildings me-2"></i>Minha Empresa
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="profiles-tab" data-bs-toggle="tab" data-bs-target="#profiles" type="button">
                        <i class="bi bi-shield-shaded me-2"></i>Perfis de Acesso
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button">
                        <i class="bi bi-people me-2"></i>Usuários
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="teamTabsContent">
                {{-- Aba 1: Dados da Empresa --}}
                <div class="tab-pane fade show active" id="company" role="tabpanel">
                    @include('registries.team.partials.company_info')
                </div>

                {{-- Aba 2: Perfis de Acesso --}}
                <div class="tab-pane fade" id="profiles" role="tabpanel">
                    @include('registries.team.partials.profiles_list')
                </div>

                {{-- Aba 3: Membros da Equipe --}}
                <div class="tab-pane fade" id="members" role="tabpanel">
                    @include('registries.team.partials.users_list')
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modais de Criação --}}
@include('registries.team.partials.modal_create_user')
@include('registries.team.partials.modal_create_profile')

<style>
    /* Estilização das Abas (Pills) no padrão Unique Cafés */
    .custom-pills .nav-link {
        color: var(--coffee-brown);
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .custom-pills .nav-link.active {
        background-color: var(--coffee-brown) !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(61, 43, 31, 0.2);
    }
    .custom-pills .nav-link:hover:not(.active) {
        background-color: var(--coffee-cream);
    }

    /* Utilitários de Texto */
    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #adb5bd;
        display: block;
        margin-bottom: 3px;
    }
</style>
@endsection