<div class="modal fade" id="modalViewCompany" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 ps-4 pt-4">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">
                    <i class="bi bi-eye me-2"></i> Detalhes do Terminal: <span id="viewCompanyNameTitle"></span>
                </h5>
                <button type="button" class="btn-close me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formUpdateCompany" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 pb-4">
                    <div class="row g-0">
                        
                        {{-- COLUNA DA ESQUERDA: LOGO E DADOS --}}
                        <div class="col-md-4 text-center border-md-end pe-md-4 mb-4 mb-md-0">
                            <div class="position-relative d-inline-block mb-3">
                                {{-- Preview da Logo --}}
                                <div id="logoContainer" class="rounded-circle border shadow-sm d-flex align-items-center justify-content-center bg-white" style="width: 120px; height: 120px; overflow: hidden;">
                                    <img id="viewLogoPreview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                    <i id="viewLogoIcon" class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                                </div>
                                
                                {{-- Input de Logo --}}
                                <div class="mt-2 d-none edit-field">
                                    <label class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 0.75rem; border-radius: 20px; cursor: pointer;">
                                        <i class="bi bi-camera me-1"></i> Alterar Logo 
                                        <input type="file" name="logo" id="viewLogoInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3 p-2 bg-light rounded-3">
                                <label class="small fw-bold d-block text-muted mb-1">ID do Terminal</label>
                                <span class="badge bg-dark px-3" id="viewId" style="font-size: 0.9rem;"></span>
                            </div>

                            <div class="mb-3 text-start ps-2">
                                <label class="small fw-bold d-block text-muted">CNPJ</label>
                                <span class="fw-bold text-secondary" id="viewCnpj"></span>
                            </div>

                            <div class="mb-0 text-start ps-2">
                                <label class="small fw-bold d-block text-muted">Data de Criação</label>
                                <span class="text-secondary" id="viewCreatedAt"></span>
                            </div>
                        </div>

                        {{-- COLUNA DA DIREITA: EDIÇÃO E MÓDULOS --}}
                        <div class="col-md-8 ps-md-4">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Razão Social</label>
                                <input type="text" name="name" id="viewNameInput" 
                                       class="form-control border-0 bg-light fw-bold py-2" 
                                       style="font-size: 1.1rem; color: var(--coffee-brown);" readonly>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold mb-3 text-primary">
                                    <i class="bi bi-cpu-fill me-1"></i> Módulos do Sistema
                                </label>
                                <div class="row g-2">
                                    @php $modules = ['lab' => 'Laboratório', 'stock' => 'Estoque Verde', 'roast' => 'Torrefação', 'production' => 'Produção']; @endphp
                                    @foreach($modules as $key => $label)
                                    <div class="col-6">
                                        <div class="form-check form-switch p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between shadow-xs">
                                            <label class="form-check-label small fw-medium ms-2" for="view_mod_{{ $key }}">{{ $label }}</label>
                                            <input class="form-check-input module-switch me-1" type="checkbox" role="switch" 
                                                   id="view_mod_{{ $key }}" name="module_{{ $key }}" disabled>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold mb-2 text-success">
                                    <i class="bi bi-clock-history me-1"></i> Histórico de Recebimentos
                                </label>
                                <div class="border rounded-3 bg-light p-0" style="max-height: 160px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0 small">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="ps-3 border-0 py-2">Data</th>
                                                <th class="text-end pe-3 border-0 py-2">Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody id="viewPaymentHistory">
                                            {{-- Preenchido via JS --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-between bg-light-subtle" style="border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-outline-dark fw-bold px-4" data-bs-dismiss="modal">Fechar</button>
                    
                    <div id="actionButtons">
                        <button type="button" id="btnEditCompany" class="btn btn-dark fw-bold px-4 shadow-sm" 
                                style="background-color: var(--coffee-brown); border-color: var(--coffee-gold);" onclick="enableEditMode()">
                            <i class="bi bi-pencil-square me-2"></i> Editar Dados
                        </button>
                        <button type="submit" id="btnSaveCompany" class="btn btn-success fw-bold px-4 d-none shadow-sm">
                            <i class="bi bi-check-lg me-2"></i> Gravar Alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Função auxiliar para adicionar 3 horas a uma string de data
    function add3Hours(dateString) {
        if (!dateString || dateString === '--/--') return dateString;
        try {
            // Tenta converter para objeto Date
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString; // Se não for data válida, retorna original
            
            date.setHours(date.getHours() + 3);
            
            // Formata de volta para padrão brasileiro amigável
            return date.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }

    function openViewModal(data) {
        disableEditMode();
        
        document.getElementById('viewCompanyNameTitle').innerText = data.name;
        document.getElementById('viewNameInput').value = data.name;
        document.getElementById('viewId').innerText = `#${String(data.id).padStart(2, '0')}`;
        document.getElementById('viewCnpj').innerText = data.cnpj;
        
        // ADICIONADO +3 HORAS NA CRIAÇÃO
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
                // ADICIONADO +3 HORAS NO HISTÓRICO
                historyBody.innerHTML += `
                    <tr>
                        <td class="ps-3 py-2 text-muted fw-medium border-0">${add3Hours(p.date)}</td>
                        <td class="text-end pe-3 py-2 fw-bold text-success border-0">R$ ${p.amount}</td>
                    </tr>`;
            });
        } else {
            historyBody.innerHTML = '<tr><td colspan="2" class="text-center py-4 text-muted border-0">Sem registros.</td></tr>';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalViewCompany'));
        modal.show();
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