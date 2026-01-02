@extends('layouts.app')

@section('title', 'Gestão de Fornecedores')

@section('content')
@php
    // Define qual aba deve iniciar aberta (padrão: producers)
    $activeTab = request('tab', 'producers');
@endphp

<style>
    .btn-action { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0; transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-2px); }
    .btn-group-custom { display: flex; gap: 5px; justify-content: flex-end; }
    .info-label { font-size: 0.65rem; font-weight: bold; text-transform: uppercase; color: #6c757d; display: block; margin-bottom: 2px; }
</style>

<div class="fade-in px-3">
    {{-- Cabeçalho Padronizado --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-lg-3">
            <h2 class="h4 fw-bold mb-0" style="color: var(--coffee-brown);">Gestão de Fornecedores</h2>
            <p class="text-muted small mb-0">Rastreabilidade e vínculos de origem.</p>
        </div>
        
        {{-- Barra de Busca Centralizada --}}
        <div class="col-lg-6">
            {{-- Rota atualizada para registries.suppliers.index --}}
            <form action="{{ route('registries.suppliers.index') }}" method="GET" id="formSearchSuppliers">
                {{-- IMPORTANTE: Classe 'input-tab-control' permite que o JS sincronize a aba --}}
                <input type="hidden" name="tab" class="input-tab-control" value="{{ $activeTab }}">
                
                <div class="input-group shadow-sm mx-auto" style="max-width: 700px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 py-2" 
                           placeholder="Nome, documento ou cidade..." value="{{ request('search') }}">
                    
                    <button type="button" class="btn btn-outline-dark px-3 d-flex align-items-center gap-2" 
                            data-bs-toggle="modal" data-bs-target="#modalFilterSupplier">
                        <i class="bi bi-sliders"></i>
                        <span class="d-none d-md-inline">Filtros</span>
                    </button>
                    
                    <button type="submit" class="btn btn-dark px-4 fw-bold">Buscar</button>
                </div>
            </form>
        </div>

        <div class="col-lg-3 text-lg-end">
            {{-- Rota atualizada para o hub de cadastros --}}
            <a href="{{ route('registries.index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Voltar</a>
        </div>
    </div>

    {{-- Alerta de Item Capturado (🖐️) --}}
    @if(session('captured_name'))
    <div class="alert bg-white border-start border-warning border-4 shadow-sm d-flex justify-content-between align-items-center mb-4 py-2" style="border-radius: 8px;">
        <div class="d-flex align-items-center">
            <div class="bg-warning-subtle p-2 rounded-circle me-3">
                <i class="bi bi-hand-index-thumb text-warning fs-5"></i>
            </div>
            <div>
                <span class="d-block small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Item na Mão</span>
                <strong class="text-dark">{{ session('captured_name') }}</strong>
            </div>
        </div>
        {{-- Rota atualizada --}}
        <a href="{{ route('registries.suppliers.clear') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">Soltar</a>
    </div>
    @endif

    {{-- Alertas de Sucesso --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm py-2 mb-4 small animate-fade-in">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Navegação por Abas --}}
    <ul class="nav nav-pills mb-4 shadow-sm p-2 bg-white rounded-pill" id="pills-tab" role="tablist" style="width: fit-content;">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab == 'producers' ? 'active' : '' }} rounded-pill px-4" 
                    data-bs-toggle="pill" data-bs-target="#producers" type="button" onclick="updateTabParam('producers')">Produtores</button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab == 'farms' ? 'active' : '' }} rounded-pill px-4" 
                    data-bs-toggle="pill" data-bs-target="#farms" type="button" onclick="updateTabParam('farms')">Fazendas</button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab == 'companies' ? 'active' : '' }} rounded-pill px-4" 
                    data-bs-toggle="pill" data-bs-target="#companies" type="button" onclick="updateTabParam('companies')">Empresas</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        
        {{-- ABA: PRODUTORES --}}
        <div class="tab-pane fade {{ $activeTab == 'producers' ? 'show active' : '' }}" id="producers" role="tabpanel">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Produtores Cadastrados</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalProducer">+ Novo Produtor</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light"><tr class="small text-muted text-uppercase"><th>Nome</th><th>CPF</th><th>Contato</th><th class="text-end pe-4">Ações</th></tr></thead>
                        <tbody>
                            @forelse($producers as $p)
                            <tr>
                                <td class="fw-bold ps-3">{{ $p->name }}</td>
                                <td>{{ $p->cpf ?? '---' }}</td>
                                <td>{{ $p->contact ?? '---' }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group-custom">
                                        <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInfoProducer{{ $p->id }}"><i class="bi bi-info-circle-fill"></i></button>
                                        {{-- Rota capturar atualizada --}}
                                        <a href="{{ route('registries.suppliers.capture', ['producer', $p->id]) }}" class="btn btn-sm btn-white border btn-action shadow-sm {{ session('captured_producer_id') == $p->id ? 'bg-warning border-warning' : '' }}"><i class="bi bi-hand-index-thumb"></i></a>
                                        {{-- Rota inativar atualizada --}}
                                        <form action="{{ route('registries.suppliers.inactivate', ['producer', $p->id]) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="tab" value="producers">
                                            <button type="submit" class="btn btn-sm btn-white border btn-action text-danger shadow-sm" onclick="return confirm('Excluir?')"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- Include atualizado para nova pasta registries.suppliers.partials --}}
                            @include('registries.suppliers.partials.modal_info_producer', ['p' => $p])
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Nenhum produtor encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ABA: FAZENDAS --}}
        <div class="tab-pane fade {{ $activeTab == 'farms' ? 'show active' : '' }}" id="farms" role="tabpanel">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Fazendas Cadastradas</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalFarm">+ Nova Fazenda</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light"><tr class="small text-muted text-uppercase"><th>Fazenda</th><th>Altitude</th><th>Localização</th><th class="text-end pe-4">Ações</th></tr></thead>
                        <tbody>
                            @forelse($farms as $f)
                            <tr>
                                <td class="fw-bold ps-3">{{ $f->name }}</td>
                                <td>{{ $f->altitude }}m</td>
                                <td>{{ $f->city }}/{{ $f->state }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group-custom">
                                        <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInfoFarm{{ $f->id }}"><i class="bi bi-info-circle-fill"></i></button>
                                        
                                        {{-- Rota link atualizada --}}
                                        @if(session('captured_producer_id'))
                                            <a href="{{ route('registries.suppliers.link', ['farm', $f->id]) }}" class="btn btn-sm btn-success btn-action shadow-sm text-white" title="Vincular Produtor a esta Fazenda">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                        @endif

                                        {{-- Rota capture atualizada --}}
                                        <a href="{{ route('registries.suppliers.capture', ['farm', $f->id]) }}" class="btn btn-sm btn-white border btn-action shadow-sm {{ session('captured_farm_id') == $f->id ? 'bg-warning border-warning' : '' }}"><i class="bi bi-hand-index-thumb"></i></a>
                                        {{-- Rota inactivate atualizada --}}
                                        <form action="{{ route('registries.suppliers.inactivate', ['farm', $f->id]) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="tab" value="farms">
                                            <button type="submit" class="btn btn-sm btn-white border btn-action text-danger shadow-sm" onclick="return confirm('Excluir?')"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- Include atualizado para nova pasta --}}
                            @include('registries.suppliers.partials.modal_info_farm', ['f' => $f])
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Nenhuma fazenda encontrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ABA: EMPRESAS --}}
        <div class="tab-pane fade {{ $activeTab == 'companies' ? 'show active' : '' }}" id="companies" role="tabpanel">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Empresas / Cooperativas</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCompany">+ Nova Empresa</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light"><tr class="small text-muted text-uppercase"><th>Nome</th><th>Tipo</th><th>CNPJ</th><th class="text-end pe-4">Ações</th></tr></thead>
                        <tbody>
                            @forelse($companies as $c)
                            <tr>
                                <td class="fw-bold ps-3">{{ $c->name }}</td>
                                <td>{{ $c->action_type }}</td>
                                <td>{{ $c->cnpj ?? '---' }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group-custom">
                                        <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInfoCompany{{ $c->id }}"><i class="bi bi-info-circle-fill"></i></button>
                                        
                                        {{-- Rota link atualizada --}}
                                        @if(session('captured_producer_id') || session('captured_farm_id'))
                                            <a href="{{ route('registries.suppliers.link', ['company', $c->id]) }}" class="btn btn-sm btn-success btn-action shadow-sm text-white" title="Vincular Item a esta Empresa">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                        @endif

                                        {{-- Rota inactivate atualizada --}}
                                        <form action="{{ route('registries.suppliers.inactivate', ['company', $c->id]) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="tab" value="companies">
                                            <button type="submit" class="btn btn-sm btn-white border btn-action text-danger shadow-sm" onclick="return confirm('Excluir?')"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- Include atualizado para nova pasta --}}
                            @include('registries.suppliers.partials.modal_info_company', ['c' => $c])
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Nenhuma empresa encontrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Inclusão dos Partials atualizada para nova estrutura registries.suppliers.partials --}}
@include('registries.suppliers.partials.modals_create')
@include('registries.suppliers.partials.filter')

<script>
    /**
     * Sincroniza todos os formulários da página com a aba atual
     */
    function updateTabParam(tabName) {
        // Atualiza todos os inputs hidden que controlam a aba ativa (Busca e Filtro)
        const inputs = document.querySelectorAll('.input-tab-control');
        inputs.forEach(el => el.value = tabName);

        // Atualiza a URL visualmente para o usuário
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    }
</script>
@endsection