@extends('layouts.app')

@section('content')
<style>
    /* Padronização dos botões de ação */
    .btn-action {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.2s;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
    .btn-group-custom {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
    }
    .info-label {
        font-size: 0.65rem;
        font-weight: bold;
        text-transform: uppercase;
        color: #6c757d;
        display: block;
        margin-bottom: 2px;
    }
</style>

<div class="fade-in px-3">
    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Gestão de Fornecedores</h2>
            <p class="text-muted small mb-0">Rastreabilidade e vínculos de origem da Unique Cafés.</p>
        </div>
        <a href="{{ route('cadastros') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Voltar</a>
    </div>

    {{-- Alerta de Entidade Capturada (🖐️) --}}
    @if(session('captured_name'))
    <div class="alert bg-white border-start border-warning border-4 shadow-sm d-flex justify-content-between align-items-center mb-4 py-2 animate-fade-in" style="border-radius: 8px;">
        <div class="d-flex align-items-center">
            <div class="bg-warning-subtle p-2 rounded-circle me-3">
                <i class="bi bi-hand-index-thumb text-warning fs-5"></i>
            </div>
            <div>
                <span class="d-block small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Item na Mão</span>
                <strong class="text-dark">{{ session('captured_name') }}</strong>
            </div>
        </div>
        <a href="{{ route('suppliers.clear') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">Soltar</a>
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
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="tab-producers" data-bs-toggle="pill" data-bs-target="#producers" type="button">Produtores</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="tab-farms" data-bs-toggle="pill" data-bs-target="#farms" type="button">Fazendas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="tab-companies" data-bs-toggle="pill" data-bs-target="#companies" type="button">Empresas / Cooperativas</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        
        {{-- ABA PRODUTORES --}}
        <div class="tab-pane fade show active" id="producers" role="tabpanel">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Produtores Cadastrados</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalProducer">+ Novo Produtor</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr class="small text-muted text-uppercase"><th>Nome</th><th>CPF</th><th>Contato</th><th class="text-end">Ações</th></tr></thead>
                        <tbody>
                            @forelse($producers as $p)
                            <tr>
                                <td class="fw-bold">{{ $p->name }}</td>
                                <td>{{ $p->cpf ?? '---' }}</td>
                                <td>{{ $p->contact ?? '---' }}</td>
                                <td class="text-end">
                                    <div class="btn-group-custom">
                                        {{-- Botão Info aponta para modalInfoProducer --}}
                                        <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInfoProducer{{ $p->id }}"><i class="bi bi-info-circle-fill"></i></button>
                                        <a href="{{ route('suppliers.capture', ['producer', $p->id]) }}" class="btn btn-sm btn-white border btn-action shadow-sm {{ session('captured_producer_id') == $p->id ? 'bg-warning border-warning' : '' }}"><i class="bi bi-hand-index-thumb"></i></a>
                                        <button class="btn btn-sm btn-white border btn-action shadow-sm text-muted"><i class="bi bi-pencil"></i></button>
                                    </div>
                                </td>
                            </tr>
                            {{-- CHAMA O ARQUIVO EXTERNO CORRETO --}}
                            @include('suppliers.partials.modal_info_producer', ['p' => $p])

                            @empty
                            <tr><td colspan="4" class="text-center py-4">Nenhum produtor cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ABA FAZENDAS --}}
        <div class="tab-pane fade" id="farms" role="tabpanel">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Fazendas Cadastradas</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalFarm">+ Nova Fazenda</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr class="small text-muted text-uppercase"><th>Fazenda</th><th>Altitude</th><th>Localização</th><th class="text-end">Ações</th></tr></thead>
                        <tbody>
                            @forelse($farms as $f)
                            <tr>
                                <td class="fw-bold">{{ $f->name }}</td>
                                <td>{{ $f->altitude }}m</td>
                                <td>{{ $f->is_international ? $f->country : $f->city.'/'.$f->state }}</td>
                                <td class="text-end">
                                    <div class="btn-group-custom">
                                        {{-- Botão Info aponta para modalInfoFarm --}}
                                        <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInfoFarm{{ $f->id }}"><i class="bi bi-info-circle-fill"></i></button>
                                        @if(session('captured_producer_id'))
                                            <a href="{{ route('suppliers.link', ['farm', $f->id]) }}" class="btn btn-sm btn-success btn-action shadow-sm text-white"><i class="bi bi-link-45deg"></i></a>
                                        @endif
                                        <a href="{{ route('suppliers.capture', ['farm', $f->id]) }}" class="btn btn-sm btn-white border btn-action shadow-sm {{ session('captured_farm_id') == $f->id ? 'bg-warning border-warning' : '' }}"><i class="bi bi-hand-index-thumb"></i></a>
                                        <button class="btn btn-sm btn-white border btn-action shadow-sm text-muted"><i class="bi bi-pencil"></i></button>
                                    </div>
                                </td>
                            </tr>
                            {{-- CHAMA O ARQUIVO EXTERNO CORRETO --}}
                            @include('suppliers.partials.modal_info_farm', ['f' => $f])

                            @empty
                            <tr><td colspan="4" class="text-center py-4">Nenhuma fazenda cadastrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ABA EMPRESAS --}}
        <div class="tab-pane fade" id="companies" role="tabpanel">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Empresas / Cooperativas</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCompany">+ Nova Empresa</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr class="small text-muted text-uppercase"><th>Nome</th><th>Tipo</th><th>CNPJ</th><th class="text-end">Ações</th></tr></thead>
                        <tbody>
                            @forelse($companies as $c)
                            <tr>
                                <td class="fw-bold">{{ $c->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $c->action_type }}</span></td>
                                <td>{{ $c->cnpj ?? '---' }}</td>
                                <td class="text-end">
                                    <div class="btn-group-custom">
                                        {{-- Botão Info aponta para modalInfoCompany --}}
                                        <button class="btn btn-sm btn-white border btn-action text-info shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInfoCompany{{ $c->id }}"><i class="bi bi-info-circle-fill"></i></button>
                                        @if(session('captured_farm_id'))
                                            <a href="{{ route('suppliers.link', ['company', $c->id]) }}" class="btn btn-sm btn-success btn-action shadow-sm text-white"><i class="bi bi-link-45deg"></i></a>
                                        @endif
                                        <button class="btn btn-sm btn-white border btn-action shadow-sm text-muted"><i class="bi bi-pencil"></i></button>
                                    </div>
                                </td>
                            </tr>
                            {{-- CHAMA O ARQUIVO EXTERNO CORRETO --}}
                            @include('suppliers.partials.modal_info_company', ['c' => $c])

                            @empty
                            <tr><td colspan="4" class="text-center py-4">Nenhuma empresa cadastrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAIS DE CADASTRO --}}
@include('suppliers.partials.modals_create')

<script>
    // Lógica Internacional/Brasil (Fazenda)
    document.getElementById('checkInternacionalFarm')?.addEventListener('change', function() {
        document.getElementById('camposBrasilFarm').classList.toggle('d-none', this.checked);
        document.getElementById('camposExteriorFarm').classList.toggle('d-none', !this.checked);
    });
    // Lógica Internacional/Brasil (Empresa)
    document.getElementById('checkInternacionalCompany')?.addEventListener('change', function() {
        document.getElementById('camposBrasilCompany').classList.toggle('d-none', this.checked);
        document.getElementById('camposExteriorCompany').classList.toggle('d-none', !this.checked);
    });
</script>
@endsection