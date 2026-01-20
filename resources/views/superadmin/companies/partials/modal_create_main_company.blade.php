<div class="modal fade" id="modalCreateCompany" tabindex="-1" aria-labelledby="modalCreateCompanyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 ps-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalCreateCompanyLabel" style="color: var(--coffee-brown);">
                    <i class="bi bi-plus-circle me-2"></i> Cadastrar Nova Empresa
                </h5>
                <button type="button" class="btn-close me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('master.companies.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="modal-body px-4">
                    {{-- RAZÃO SOCIAL --}}
                    <div class="mb-3">
                        <label for="name" class="form-label small fw-bold">Razão Social / Nome Fantasia <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-0 bg-light py-2 shadow-sm" id="name" name="name" required placeholder="Ex: Cafés do Brasil LTDA">
                    </div>

                    {{-- CNPJ COM MÁSCARA --}}
                    <div class="mb-3">
                        <label for="cnpj_mask" class="form-label small fw-bold">CNPJ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-0 bg-light py-2 shadow-sm" id="cnpj_mask" name="cnpj" required 
                               placeholder="00.000.000/0000-00" maxlength="18">
                        <div class="form-text small">Apenas números ou com máscara.</div>
                    </div>

                    {{-- LOGO --}}
                    <div class="mb-4">
                        <label for="logo" class="form-label small fw-bold">Logo da Empresa</label>
                        <div class="input-group">
                            <input type="file" class="form-control border-0 bg-light shadow-sm" id="logo" name="logo" accept="image/*">
                        </div>
                        <div class="form-text small">Tamanho máximo recomendado: 2MB.</div>
                    </div>

                    <hr class="opacity-10 my-4">

                    {{-- MÓDULOS HABILITADOS --}}
                    <label class="form-label small fw-bold mb-3 d-flex align-items-center" style="color: var(--coffee-brown);">
                        <i class="bi bi-cpu-fill me-2 text-primary"></i> Módulos Iniciais Contratados
                    </label>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-check form-switch p-2 border rounded shadow-xs bg-white ms-0 ps-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="mod_lab" name="module_lab" checked>
                                <label class="form-check-label small fw-medium" for="mod_lab">Laboratório</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch p-2 border rounded shadow-xs bg-white ms-0 ps-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="mod_stock" name="module_stock" checked>
                                <label class="form-check-label small fw-medium" for="mod_stock">Estoque Verde</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch p-2 border rounded shadow-xs bg-white ms-0 ps-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="mod_roast" name="module_roast">
                                <label class="form-check-label small fw-medium" for="mod_roast">Torrefação</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch p-2 border rounded shadow-xs bg-white ms-0 ps-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="mod_prod" name="module_production">
                                <label class="form-check-label small fw-medium" for="mod_prod">Produção</label>
                            </div>
                        </div>
                    </div>

                    {{-- AVISO SOBRE USUÁRIO --}}
                    <div class="mt-4 p-2 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded text-center">
                        <small class="text-dark" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle-fill me-1"></i> 
                            O usuário <strong>zevagner</strong> será criado automaticamente para esta empresa.
                        </small>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4 mt-2 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-4 shadow-sm" data-bs-dismiss="modal" style="border-radius: 8px;">Cancelar</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm" style="background-color: var(--coffee-brown); border-color: var(--coffee-gold); border-radius: 8px;">
                        <i class="bi bi-check-lg me-1"></i> Finalizar Cadastro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT DA MÁSCARA DO CNPJ OTIMIZADO --}}
<script>
document.getElementById('cnpj_mask').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
    
    if (value.length > 14) value = value.slice(0, 14); // Limita a 14 dígitos

    // Aplica a máscara dinamicamente
    if (value.length <= 2) {
        e.target.value = value;
    } else if (value.length <= 5) {
        e.target.value = value.replace(/^(\d{2})(\d)/, '$1.$2');
    } else if (value.length <= 8) {
        e.target.value = value.replace(/^(\d{2})(\d{3})(\d)/, '$1.$2.$3');
    } else if (value.length <= 12) {
        e.target.value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d)/, '$1.$2.$3/$4');
    } else {
        e.target.value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d)/, '$1.$2.$3/$4-$5');
    }
});
</script>