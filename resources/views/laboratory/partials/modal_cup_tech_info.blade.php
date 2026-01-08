<div class="modal fade" id="modalCupTechInfo" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-light py-2">
                <h6 class="fw-bold mb-0" style="color: var(--coffee-brown);">
                    <i class="bi bi-info-circle-fill me-2"></i>Informações Técnicas
                </h6>
                <div class="ms-auto d-flex align-items-center gap-2 me-3">
                    <span class="small fw-bold text-muted" style="font-size: 0.7rem;">AVULSA</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="modal-avulso-toggle" onchange="toggleModalFields(this.checked)">
                    </div>
                </div>
                <button type="button" class="btn-close" onclick="closeTechModal()" style="font-size: 0.8rem;"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Variedade</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="variety" placeholder="Ex: Catuaí Vermelho">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>

                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Processo de Seca</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="drying_process" placeholder="Ex: Natural">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Fermentação</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="fermentation" placeholder="Ex: Anaeróbica">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>

                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Umidade (%)</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="humidity" placeholder="0.00">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Peneira</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="screen" placeholder="Ex: 16up">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">ID Lote Fornecedor</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="lot_id" placeholder="Cód. Origem">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Altitude (m)</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="altitude" placeholder="Ex: 1100">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Safra</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="crop" placeholder="24/25">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Origem/Região</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="origin" placeholder="Ex: Mantiqueira de Minas">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-dark w-100 rounded-pill fw-bold py-2" onclick="saveTechInfo()">
                    CONCLUIR E SALVAR
                </button>
            </div>
        </div>
    </div>
</div>