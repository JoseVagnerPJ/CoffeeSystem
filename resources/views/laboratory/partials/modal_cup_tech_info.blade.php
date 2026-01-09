<div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: #fdfaf5;"> {{-- Cor levemente creme --}}
    <div class="modal-header border-0 bg-light py-3" style="border-bottom: 1px solid #e3d5ca !important;">
        <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">
            <i class="bi bi-gear-wide-connected me-2"></i>Setup Técnico: Xícara {{ $index + 1 }}
        </h5>
        <div class="ms-auto d-flex align-items-center gap-3 me-3">
            <div class="text-end">
                <span class="d-block small fw-bold text-muted" style="font-size: 0.6rem; line-height: 1;">ENTRADA</span>
                <span id="status-label" class="small fw-bold text-primary">SINC. SISTEMA</span>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="modal-avulso-toggle" 
                       style="width: 45px; height: 22px; cursor: pointer;"
                       onchange="toggleModalFields(this.checked)">
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    
    <div class="modal-body p-4">
        <div class="row g-3">
            {{-- Coluna Esquerda --}}
            <div class="col-md-7">
                <label class="small fw-bold text-muted text-uppercase">Variedade do Grão</label>
                <div class="position-relative mb-3">
                    <input type="text" class="form-control tech-field" data-field="variety">
                    <i class="bi bi-lock-fill input-lock-icon"></i>
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted text-uppercase">Processo de Seca</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="drying_process">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted text-uppercase">Fermentação</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="fermentation">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coluna Direita --}}
            <div class="col-md-5">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase">Umidade (%)</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="humidity">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase">Peneira</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="screen">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted text-uppercase">Lote Fornecedor</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="lot_id">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-2 opacity-25">

            <div class="col-md-4">
                <label class="small fw-bold text-muted text-uppercase">Altitude</label>
                <div class="position-relative">
                    <input type="text" class="form-control tech-field" data-field="altitude">
                    <i class="bi bi-lock-fill input-lock-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold text-muted text-uppercase">Safra</label>
                <div class="position-relative">
                    <input type="text" class="form-control tech-field" data-field="crop">
                    <i class="bi bi-lock-fill input-lock-icon"></i>
                </div>
            </div>

            <div class="col-md-4" id="container-origin-field">
                <label class="small fw-bold text-muted text-uppercase">Origem/Região</label>
                <div class="position-relative">
                    <input type="text" class="form-control tech-field" data-field="origin">
                    <i class="bi bi-lock-fill input-lock-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer border-0 p-4 pt-0">
        <button type="button" class="btn btn-dark w-100 rounded-pill fw-bold py-3 shadow" onclick="saveTechInfo({{ $index }})">
            SALVAR CONFIGURAÇÃO DA XÍCARA
        </button>
    </div>
</div>