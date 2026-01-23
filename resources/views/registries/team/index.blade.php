@extends('layouts.app')

@section('title', 'Gestão da Equipe')

@section('content')
@php
    // Definição das permissões específicas
    $canEditSettings = auth()->user()->hasPermission('settings_edit'); // Único que acessa aba Empresa
    $canManageProfiles = auth()->user()->hasPermission('team_full');    // Acessa aba Perfis
    $canManageUsers = auth()->user()->hasPermission('team_full') || auth()->user()->hasPermission('team_user_only'); // Acessa aba Usuários

    // Lógica para definir qual aba inicia aberta (prioridade: Empresa > Perfis > Usuários)
    if ($canEditSettings) {
        $defaultTab = 'company';
    } elseif ($canManageProfiles) {
        $defaultTab = 'profiles';
    } else {
        $defaultTab = 'members';
    }
@endphp

<div class="fade-in px-3">
    {{-- Cabeçalho Limpo --}}
    <div class="mb-5">
        <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Configurações da Unidade</h2>
        <p class="text-muted small mb-0">Gerencie os dados da empresa, níveis de acesso e colaboradores.</p>
    </div>

    {{-- Navegação por Abas --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-3">
            <ul class="nav nav-pills card-header-pills custom-pills" id="teamTabs" role="tablist">
                
                {{-- ABA: MINHA EMPRESA (Restrito a Settings) --}}
                <li class="nav-item">
                    @if($canEditSettings)
                        <button class="nav-link {{ $defaultTab == 'company' ? 'active' : '' }}" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button">
                            <i class="bi bi-buildings me-2"></i>Minha Empresa
                        </button>
                    @else
                        <button class="nav-link opacity-50" onclick="alert('Acesso Negado: Somente usuários com permissão de Configurações podem gerenciar dados da empresa.')">
                            <i class="bi bi-buildings me-2"></i>Minha Empresa <i class="bi bi-lock-fill ms-2"></i>
                        </button>
                    @endif
                </li>

                {{-- ABA: PERFIS DE ACESSO (Restrito a Team Full) --}}
                <li class="nav-item">
                    @if($canManageProfiles)
                        <button class="nav-link {{ $defaultTab == 'profiles' ? 'active' : '' }}" id="profiles-tab" data-bs-toggle="tab" data-bs-target="#profiles" type="button">
                            <i class="bi bi-shield-shaded me-2"></i>Perfis de Acesso
                        </button>
                    @else
                        <button class="nav-link opacity-50" onclick="alert('Você não tem permissão para gerenciar níveis de acesso!')">
                            <i class="bi bi-shield-shaded me-2"></i>Perfis de Acesso <i class="bi bi-lock-fill ms-2"></i>
                        </button>
                    @endif
                </li>

                {{-- ABA: USUÁRIOS (Team Full ou User Only) --}}
                <li class="nav-item">
                    @if($canManageUsers)
                        <button class="nav-link {{ $defaultTab == 'members' ? 'active' : '' }}" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button">
                            <i class="bi bi-people me-2"></i>Usuários
                        </button>
                    @else
                        <button class="nav-link opacity-50" onclick="alert('Você não tem permissão para gerenciar usuários!')">
                            <i class="bi bi-people me-2"></i>Usuários <i class="bi bi-lock-fill ms-2"></i>
                        </button>
                    @endif
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="teamTabsContent">
                {{-- Conteúdo Aba 1: Dados da Empresa --}}
                @if($canEditSettings)
                    <div class="tab-pane fade {{ $defaultTab == 'company' ? 'show active' : '' }}" id="company" role="tabpanel">
                        @include('registries.team.partials.company_info')
                    </div>
                @endif

                {{-- Conteúdo Aba 2: Perfis de Acesso --}}
                @if($canManageProfiles)
                    <div class="tab-pane fade {{ $defaultTab == 'profiles' ? 'show active' : '' }}" id="profiles" role="tabpanel">
                        @include('registries.team.partials.profiles_list')
                    </div>
                @endif

                {{-- Conteúdo Aba 3: Membros da Equipe --}}
                @if($canManageUsers)
                    <div class="tab-pane fade {{ $defaultTab == 'members' ? 'show active' : '' }}" id="members" role="tabpanel">
                        @include('registries.team.partials.users_list')
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modais de Criação (Ficam fora do loop e das abas por segurança de z-index) --}}
@include('registries.team.partials.modal_create_user')
@include('registries.team.partials.modal_create_profile')

<style>
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