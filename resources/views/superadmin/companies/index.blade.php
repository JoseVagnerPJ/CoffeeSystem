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
                            <td class="ps-4 text-center text-muted fw-bold">
                                #{{ str_pad($company->id, 2, '0', STR_PAD_LEFT) }}
                            </td>

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

                            <td>
                                <div class="fw-bold" style="color: var(--coffee-brown);">{{ $company->name }}</div>
                                <small class="text-muted">
                                    {{ preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $company->cnpj) }}
                                </small>
                            </td>

                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                    <i class="bi bi-people-fill me-1 text-primary"></i> 
                                    {{ $company->users_count ?? 0 }}
                                </span>
                            </td>

                            {{-- Data e Horário (AJUSTADO: Removido o addHours) --}}
                            <td class="text-center">
                                <div class="d-flex flex-column">
                                    <span class="text-muted small fw-bold">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $company->created_at->format('d/m/Y') }}
                                    </span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i> {{ $company->created_at->format('H:i:s') }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-danger' }} opacity-75 px-3">
                                    {{ $company->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>

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

                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" 
                                            class="btn btn-sm btn-success px-3 shadow-sm" 
                                            onclick="openPaymentModal('{{ route('master.companies.payment', $company->id) }}', '{{ $company->name }}')"
                                            title="Registrar Pagamento">
                                        <i class="bi bi-currency-dollar"></i>
                                    </button>

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

@include('superadmin.companies.partials.modal_create_main_company')
@include('superadmin.companies.partials.modal_payment')
@include('superadmin.companies.partials.modal_view_main_company')

@endsection

@push('scripts')
<script>
// Função auxiliar (CORRIGIDA: Removida a adição de 3 horas para baixar o tempo)
function add3Hours(dateString) {
    if (!dateString || dateString === '--/--') return dateString;
    try {
        let isoFormat = dateString.includes(' ') ? dateString.replace(' ', 'T') : dateString;
        let date = new Date(isoFormat);
        if (isNaN(date.getTime())) return dateString;
        
        // Removida a linha: date.setHours(date.getHours() + 3);

        let d = String(date.getDate()).padStart(2, '0');
        let m = String(date.getMonth() + 1).padStart(2, '0');
        let y = date.getFullYear();
        let h = String(date.getHours()).padStart(2, '0');
        let min = String(date.getMinutes()).padStart(2, '0');
        let sec = String(date.getSeconds()).padStart(2, '0');

        return `${d}/${m}/${y} ${h}:${min}:${sec}`;
    } catch (e) { return dateString; }
}

async function loadAndOpenViewModal(id) {
    try {
        let url = "{{ route('master.companies.json', ':id') }}".replace(':id', id);
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) throw new Error('Empresa não encontrada');
        const data = await response.json();
        openViewModal(data);
    } catch (error) {
        console.error('Erro ao carregar:', error);
        alert('Erro ao carregar dados da empresa.');
    }
}

function openViewModal(data) {
    disableEditMode();
    const baseUrl = "{{ route('master.companies.update', ':id') }}";
    document.getElementById('formUpdateCompany').action = baseUrl.replace(':id', data.id);
    document.getElementById('viewCompanyNameTitle').innerText = data.name;
    document.getElementById('viewNameInput').value = data.name;
    document.getElementById('viewId').innerText = `#${String(data.id).padStart(2, '0')}`;
    document.getElementById('viewCnpj').innerText = data.cnpj;
    
    // Agora exibe o tempo original do banco formatado
    document.getElementById('viewCreatedAt').innerText = add3Hours(data.created_at);
    
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

    const modules = ['lab', 'stock', 'roast', 'production'];
    modules.forEach(m => {
        const el = document.getElementById('view_mod_' + m);
        if(el) el.checked = data.modules_active.includes(m);
    });

    const historyBody = document.getElementById('viewPaymentHistory');
    historyBody.innerHTML = '';
    if (data.payments && data.payments.length > 0) {
        data.payments.forEach(p => {
            historyBody.innerHTML += `
                <tr>
                    <td class="ps-3 py-2 text-muted fw-medium border-0">${add3Hours(p.date)}</td>
                    <td class="text-end pe-3 py-2 fw-bold text-success border-0">R$ ${p.amount}</td>
                </tr>`;
        });
    } else {
        historyBody.innerHTML = '<tr><td colspan="2" class="text-center py-4 text-muted border-0">Sem registros.</td></tr>';
    }

    const modalEl = document.getElementById('modalViewCompany');
    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInstance.show();
}

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