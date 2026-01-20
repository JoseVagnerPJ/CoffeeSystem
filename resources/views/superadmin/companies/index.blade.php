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
                            <th class="py-3 text-center">Data Cadastro</th>
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
        // 1. Busca os dados via JSON
        let url = "{{ route('master.companies.json', ':id') }}".replace(':id', id);
        
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Empresa não encontrada');

        const data = await response.json();
        
        // 2. Preenche e Abre o Modal
        openViewModal(data);

    } catch (error) {
        console.error('Erro ao carregar:', error);
        alert('Erro ao carregar dados da empresa.');
    }
}

function openViewModal(data) {
    // 1. Reseta o estado de edição
    disableEditMode();

    // 2. Configura a rota de Update no Form
    const baseUrl = "{{ route('master.companies.update', ':id') }}";
    document.getElementById('formUpdateCompany').action = baseUrl.replace(':id', data.id);

    // 3. Preenche os campos de texto
    document.getElementById('viewCompanyNameTitle').innerText = data.name;
    document.getElementById('viewNameInput').value = data.name;
    document.getElementById('viewId').innerText = `#${String(data.id).padStart(2, '0')}`;
    document.getElementById('viewCnpj').innerText = data.cnpj;
    document.getElementById('viewCreatedAt').innerText = data.created_at;
    
    // 4. Lógica da Logo / Ícone
    const img = document.getElementById('viewLogoPreview');
    const icon = document.getElementById('viewLogoIcon');
    if (data.logo) {
        img.src = '/storage/' + data.logo;
        img.style.display = 'block';
        icon.style.display = 'none';
    } else {
        img.style.display = 'none';
        icon.style.display = 'block';
    }

    // 5. Switches de Módulos
    const modules = ['lab', 'stock', 'roast', 'production'];
    modules.forEach(m => {
        const el = document.getElementById('view_mod_' + m);
        if(el) el.checked = data.modules_active.includes(m);
    });

    // 6. Histórico Financeiro
    const historyBody = document.getElementById('viewPaymentHistory');
    historyBody.innerHTML = '';
    if (data.payments && data.payments.length > 0) {
        data.payments.forEach(p => {
            historyBody.innerHTML += `
                <tr>
                    <td class="ps-3 py-2 text-muted fw-medium border-0">${p.date}</td>
                    <td class="text-end pe-3 py-2 fw-bold text-success border-0">R$ ${p.amount}</td>
                </tr>`;
        });
    } else {
        historyBody.innerHTML = '<tr><td colspan="2" class="text-center py-4 text-muted border-0">Sem registros.</td></tr>';
    }

    // --- CORREÇÃO DO ERRO DA TELA CINZA ---
    const modalEl = document.getElementById('modalViewCompany');
    // Em vez de 'new', pegamos a instância existente ou criamos uma única
    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInstance.show();
}

// Funções Auxiliares (Fora das funções principais para evitar duplicação)
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('viewLogoPreview');
            const icon = document.getElementById('viewLogoIcon');
            img.src = e.target.result;
            img.style.display = 'block';
            icon.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function enableEditMode() {
    document.getElementById('viewNameInput').readOnly = false;
    document.getElementById('viewNameInput').classList.replace('bg-light', 'bg-white');
    document.querySelectorAll('.module-switch').forEach(el => el.disabled = false);
    document.getElementById('btnEditCompany').classList.add('d-none');
    document.getElementById('btnSaveCompany').classList.remove('d-none');
    document.querySelectorAll('.edit-field').forEach(el => el.classList.remove('d-none'));
}

function disableEditMode() {
    const nameInput = document.getElementById('viewNameInput');
    if(nameInput) {
        nameInput.readOnly = true;
        nameInput.classList.replace('bg-white', 'bg-light');
    }
    document.querySelectorAll('.module-switch').forEach(el => el.disabled = true);
    document.getElementById('btnEditCompany').classList.remove('d-none');
    document.getElementById('btnSaveCompany').classList.add('d-none');
    document.querySelectorAll('.edit-field').forEach(el => el.classList.add('d-none'));
}
</script>
@endpush