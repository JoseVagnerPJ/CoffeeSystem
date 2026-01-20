@extends('layouts.app')

@section('title', 'Gestão Master - Empresas')

@section('content')
<div class="container-fluid">

    {{-- AVISO DE SUCESSO / ERRO NO TOPO --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div>
                    <strong>Sucesso!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Cabeçalho com Título e Botão de Ação --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: var(--coffee-brown);">
                <i class="bi bi-building-gear me-2"></i> Empresas Cadastradas
            </h2>
            <p class="text-muted small mb-0">Controle de terminais e unidades ativas no sistema.</p>
        </div>
        
        <button type="button" class="btn btn-dark shadow-sm px-4 fw-bold" 
                style="background-color: var(--coffee-brown); border-color: var(--coffee-gold); border-radius: 10px;"
                data-bs-toggle="modal" data-bs-target="#modalCreateCompany">
            <i class="bi bi-plus-circle me-2"></i> Nova Empresa
        </button>
    </div>

    {{-- Tabela de Gestão Master --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: var(--coffee-gold-light);">
                        <tr>
                            <th class="ps-4 py-3 text-center" style="width: 70px;">ID</th>
                            <th class="py-3" style="width: 80px;">Logo</th>
                            <th class="py-3">Razão Social / CNPJ</th>
                            <th class="py-3 text-center">Usuários</th>
                            <th class="py-3 text-center">Data Cadastro (-3h)</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Último Pagamento</th>
                            <th class="py-3 text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                        <tr>
                            {{-- ID do Terminal --}}
                            <td class="ps-4 text-center text-muted fw-bold">
                                #{{ str_pad($company->id, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            {{-- Coluna da Logo --}}
                            <td>
                                <div class="rounded-circle border d-flex align-items-center justify-content-center shadow-sm" 
                                     style="width: 48px; height: 48px; background-color: white; overflow: hidden;">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" class="w-100 h-100" style="object-fit: cover;">
                                    @else
                                        <i class="bi bi-building text-muted fs-4"></i>
                                    @endif
                                </div>
                            </td>

                            {{-- Informações da Empresa com Máscara de CNPJ --}}
                            <td>
                                <div class="fw-bold" style="color: var(--coffee-brown);">{{ $company->name }}</div>
                                <small class="text-muted">
                                    {{-- APLICAÇÃO DA MÁSCARA: xx.xxx.xxx/xxxx-xx --}}
                                    {{ preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $company->cnpj) }}
                                </small>
                            </td>

                            {{-- Contagem de Usuários --}}
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                    <i class="bi bi-people-fill me-1 text-primary"></i> 
                                    {{ $company->users_count ?? 0 }}
                                </span>
                            </td>

                            {{-- Data e Horário de Criação (Ajustado -3h) --}}
                            <td class="text-center">
                                <div class="d-flex flex-column">
                                    <span class="text-muted small fw-bold">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $company->created_at->subHours(3)->format('d/m/Y') }}
                                    </span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i> {{ $company->created_at->subHours(3)->format('H:i:s') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Badge de Status --}}
                            <td class="text-center">
                                <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-danger' }} opacity-75 px-3">
                                    {{ $company->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>

                            {{-- Coluna Último Pagamento --}}
                            <td class="text-center">
                                @if(isset($company->last_payment_date))
                                    <span class="text-success small fw-bold">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        {{ \Carbon\Carbon::parse($company->last_payment_date)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted small italic">---</span>
                                @endif
                            </td>

                            {{-- Ações Rápidas --}}
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    
                                    {{-- Botão Financeiro --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-success px-3 shadow-sm" 
                                            onclick="openPaymentModal('{{ route('master.companies.payment', $company->id) }}', '{{ $company->name }}')"
                                            title="Registrar Pagamento">
                                        <i class="bi bi-currency-dollar"></i>
                                    </button>

                                    {{-- Botão de Ativação/Suspensão --}}
                                    <form action="{{ route('master.companies.toggle', $company->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Tem certeza que deseja {{ $company->is_active ? 'BLOQUEAR' : 'LIBERAR' }} o acesso da empresa {{ $company->name }}?')">
                                        @csrf 
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $company->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} px-3 shadow-sm" 
                                                title="{{ $company->is_active ? 'Suspender Acesso' : 'Liberar Acesso' }}">
                                            <i class="bi bi-{{ $company->is_active ? 'lock-fill' : 'unlock-fill' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Botão Visualizar (Olhinho) --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary px-3 shadow-sm" 
                                            onclick="loadAndOpenViewModal('{{ $company->id }}')" 
                                            title="Visualizar Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- IMPORTAÇÃO DOS MODAIS --}}
@include('superadmin.companies.partials.modal_create_main_company')
@include('superadmin.companies.partials.modal_payment')
@include('superadmin.companies.partials.modal_view_main_company')

@endsection

@push('scripts')
<script>
async function loadAndOpenViewModal(id) {
    try {
        // Usamos a rota do Laravel injetada no JS, trocando o placeholder pelo ID real
        let url = "{{ route('master.companies.json', ':id') }}".replace(':id', id);
        
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Empresa não encontrada ou erro no servidor');
        }

        const data = await response.json();
        openViewModal(data);

    } catch (error) {
        console.error('Erro ao carregar:', error);
        alert('Erro ao carregar dados da empresa. Verifique se a rota existe no web.php');
    }

    function openViewModal(data) {
    // 1. Desabilita o modo de edição (volta para visualização)
    disableEditMode();

    // 2. CONSTRÓI A URL CORRETA PARA EVITAR O 404
    // Usamos o helper do Laravel para pegar a base da rota e concatenamos o ID
    const baseUrl = "{{ route('master.companies.update', ':id') }}";
    document.getElementById('formUpdateCompany').action = baseUrl.replace(':id', data.id);

    // 3. Preenche os campos
    document.getElementById('viewCompanyNameTitle').innerText = data.name;
    document.getElementById('viewNameInput').value = data.name;
    document.getElementById('viewId').innerText = `#${String(data.id).padStart(2, '0')}`;
    document.getElementById('viewCnpj').innerText = data.cnpj;
    document.getElementById('viewCreatedAt').innerText = data.created_at;
    
    // Preview da Logo
    const preview = document.getElementById('viewLogoPreview');
    preview.src = data.logo ? `/storage/${data.logo}` : '/images/default-company.png';

    // Checkboxes dos módulos
    const modules = ['lab', 'stock', 'roast', 'production'];
    modules.forEach(m => {
        const el = document.getElementById(`view_mod_${m}`);
        if(el) el.checked = data.modules_active.includes(m);
    });

    const modal = new bootstrap.Modal(document.getElementById('modalViewCompany'));
    modal.show();
}

    const modal = new bootstrap.Modal(document.getElementById('modalViewCompany'));
    modal.show();
}

// Função para mostrar a imagem assim que o usuário seleciona o arquivo
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('viewLogoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

</script>
@endpush