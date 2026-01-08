<style>
    /* 1. FIXAÇÃO RÍGIDA DO TAMANHO DO MODAL */
    #modalCreateCupping .modal-dialog {
        max-width: 950px;
        margin: 1.75rem auto;
    }

    #modalCreateCupping .modal-content {
        height: 92vh;
        min-height: 92vh;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border-radius: 20px;
        border: none;
    }

    #formCreateCupping {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }

    /* 2. CORPO ROLÁVEL COM BARRA ESCONDIDA */
    #modalCreateCupping .modal-body {
        flex: 1;
        overflow-y: scroll;
        padding: 0 1.5rem 1.5rem 1.5rem; /* Ajuste no padding para o header sticky */
        background-color: #fcfcfc;
        scrollbar-width: none; 
        -ms-overflow-style: none; 
    }

    #modalCreateCupping .modal-body::-webkit-scrollbar {
        display: none;
    }

    /* 3. HEADER INTERNO STICKY (Fixa o Nome e os botões + / - no topo do scroll) */
    .sticky-controls {
        position: sticky;
        top: 0;
        background-color: #fcfcfc;
        z-index: 100;
        padding: 1.5rem 0;
        border-bottom: 1px solid #eee;
        margin-bottom: 1.5rem;
    }

    /* 4. RODAPÉ FIXO NA BASE */
    #modalCreateCupping .modal-footer {
        flex-shrink: 0;
        background: #fff;
        border-top: 1px solid #eee !important;
        padding: 1.5rem !important;
    }

    /* ESTILOS DOS CARDS (Lógica DigiGrão) */
    .cup-card { border: 1px solid #dee2e6; border-radius: 12px; margin-bottom: 1rem; transition: all 0.3s; background: #fff; }
    .cup-card:hover { border-color: var(--coffee-gold); }
    .cup-header { padding: 15px; display: flex; align-items: center; justify-content: space-between; }
    .cup-number { width: 32px; height: 32px; background: var(--coffee-brown); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem; margin-right: 12px; }
    
    .is-avulso-badge { background: #fd7e14; color: white; font-size: 0.65rem; padding: 2px 8px; border-radius: 4px; font-weight: bold; display: none; }
    .avulso-active .is-avulso-badge { display: inline-block; }
    .avulso-active .cup-number { background: #fd7e14; }
    
    .sample-result-list { position: absolute; width: 100%; z-index: 1000; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; display: none; }
    .sample-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 0.85rem; }
    .sample-item:hover { background: #f0f0f0; }

    .field-locked { background-color: #f8f9fa !important; color: #6c757d; cursor: not-allowed; }
    .input-lock-icon { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #adb5bd; font-size: 0.8rem; }
</style>

<div class="modal fade" id="modalCreateCupping" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 bg-light py-3">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">
                    <i class="bi bi-plus-circle-fill me-2"></i>Iniciar Nova Mesa de Prova
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formCreateCupping" action="{{ route('laboratory.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="sticky-controls">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-muted small text-uppercase">Nome/Título da Mesa</label>
                                <input type="text" name="title" class="form-control shadow-sm" style="border: 1px solid #ddd !important;" placeholder="Ex: Prova Mensal de Janeiro" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Controle de Xícaras</label>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-3 fw-bold" onclick="removeLastCup()">-</button>
                                    <div class="flex-grow-1 text-center bg-white border rounded-pill py-2 fw-bold" id="cup-counter">0 Xícaras</div>
                                    <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-bold" onclick="addNewCup()">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="cups-wrapper"></div>

                    <div id="empty-state" class="text-center py-5">
                        <i class="bi bi-box-seam text-muted opacity-25" style="font-size: 4rem;"></i>
                        <p class="text-muted">Adicione todas as xícaras desejadas para começar a editar.</p>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-sm" id="btnSubmitMesa" disabled>
                        CRIAR MESA E INICIAR PROVA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCupTechInfo" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; height: auto; min-height: auto; max-height: 90vh;">
            <div class="modal-header border-0 bg-light">
                <h6 class="fw-bold mb-0">Informações Técnicas</h6>
                <div class="d-flex align-items-center gap-2 ms-auto me-3">
                    <span class="small fw-bold text-muted">AVULSA</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="modal-avulso-toggle" onchange="toggleModalFields(this.checked)">
                    </div>
                </div>
                <button type="button" class="btn-close" onclick="closeTechModal()"></button>
            </div>
            <div class="modal-body p-4" id="tech-modal-fields" style="height: auto; overflow-y: auto;">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="small fw-bold text-muted">VARIEDADE</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="variety">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted">PROCESSO DE SECA</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="drying_process">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted">FERMENTAÇÃO</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="fermentation">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted">UMIDADE (%)</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="humidity">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted">PENEIRA</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="screen">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted">ID LOTE FORNECEDOR</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="lot_id">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted">ALTITUDE</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="altitude">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted">SAFRA</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="crop">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted">ORIGEM</label>
                        <div class="position-relative">
                            <input type="text" class="form-control tech-field" data-field="origin">
                            <i class="bi bi-lock-fill input-lock-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-dark w-100 rounded-pill fw-bold" onclick="saveTechInfo()">SALVAR</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cupCount = 0;
    let currentEditingIndex = null;
    const techModal = new bootstrap.Modal(document.getElementById('modalCupTechInfo'));

    function addNewCup() {
        if(cupCount >= 50) return;
        const index = cupCount;
        cupCount++;
        document.getElementById('empty-state').style.display = 'none';
        document.getElementById('btnSubmitMesa').disabled = false;
        updateCounter();

        const html = `
            <div class="cup-card shadow-sm" id="cup-row-${index}">
                <div class="cup-header">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="cup-number">${cupCount}</div>
                        <div class="flex-grow-1">
                            <input type="text" name="items[${index}][nickname]" class="form-control form-control-sm border-0 fw-bold p-0 bg-transparent" placeholder="Apelido da Xícara..." style="box-shadow:none;">
                            <span class="is-avulso-badge">AVULSA</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle" onclick="openTechInfo(${index})">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3 pt-0">
                    <div class="row g-2">
                        <div class="col-md-6 position-relative">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-0 bg-light js-sample-search" placeholder="Associar Amostra..." onkeyup="searchSample(this, ${index})">
                            </div>
                            <input type="hidden" name="items[${index}][sample_id]" class="js-sample-id">
                            <div class="sample-result-list shadow-lg" id="results-${index}"></div>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="items[${index}][notes]" class="form-control form-control-sm border-0 bg-light" placeholder="Observação pré-prova...">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="items[${index}][is_avulso]" class="js-is-avulso" value="0">
                <input type="hidden" name="items[${index}][variety]" class="js-variety">
                <input type="hidden" name="items[${index}][drying_process]" class="js-drying_process">
                <input type="hidden" name="items[${index}][fermentation]" class="js-fermentation">
                <input type="hidden" name="items[${index}][humidity]" class="js-humidity">
                <input type="hidden" name="items[${index}][screen]" class="js-screen">
                <input type="hidden" name="items[${index}][lot_id]" class="js-lot_id">
                <input type="hidden" name="items[${index}][altitude]" class="js-altitude">
                <input type="hidden" name="items[${index}][crop]" class="js-crop">
                <input type="hidden" name="items[${index}][origin]" class="js-origin">
                <input type="hidden" name="items[${index}][position]" value="${cupCount}">
            </div>
        `;
        document.getElementById('cups-wrapper').insertAdjacentHTML('beforeend', html);
        
        // AUTO-SCROLL REMOVIDO PARA MANTER A TELA FIXA EM CIMA
    }

    function openTechInfo(index) {
        currentEditingIndex = index;
        const row = document.getElementById(`cup-row-${index}`);
        const isAvulso = row.querySelector('.js-is-avulso').value == "1";
        
        document.querySelectorAll('.tech-field').forEach(field => {
            const fieldName = field.getAttribute('data-field');
            field.value = row.querySelector(`.js-${fieldName}`).value;
        });

        document.getElementById('modal-avulso-toggle').checked = isAvulso;
        toggleModalFields(isAvulso);
        techModal.show();
    }

    function toggleModalFields(isAvulso) {
        const fields = document.querySelectorAll('.tech-field');
        const icons = document.querySelectorAll('.input-lock-icon');
        
        fields.forEach(f => {
            f.readOnly = !isAvulso;
            isAvulso ? f.classList.remove('field-locked') : f.classList.add('field-locked');
        });

        icons.forEach(i => {
            isAvulso ? i.classList.replace('bi-lock-fill', 'bi-pencil-square') : i.classList.replace('bi-pencil-square', 'bi-lock-fill');
        });
    }

    function saveTechInfo() {
        const row = document.getElementById(`cup-row-${currentEditingIndex}`);
        const isAvulso = document.getElementById('modal-avulso-toggle').checked;

        document.querySelectorAll('.tech-field').forEach(field => {
            const fieldName = field.getAttribute('data-field');
            row.querySelector(`.js-${fieldName}`).value = field.value;
        });

        row.querySelector('.js-is-avulso').value = isAvulso ? "1" : "0";
        
        if(isAvulso) {
            row.classList.add('avulso-active');
            row.querySelector('.js-sample-search').disabled = true;
            row.querySelector('.js-sample-search').value = "AMOSTRA AVULSA";
            row.querySelector('.js-sample-id').value = "";
        } else {
            row.classList.remove('avulso-active');
            row.querySelector('.js-sample-search').disabled = false;
        }

        techModal.hide();
    }

    function searchSample(input, index) {
        const query = input.value;
        const resultsBox = document.getElementById(`results-${index}`);
        if(query.length < 2) { resultsBox.style.display = 'none'; return; }

        fetch(`{{ route('registries.samples.search') }}?search=${query}`)
            .then(r => r.json())
            .then(data => {
                let html = '';
                data.forEach(s => {
                    html += `<div class="sample-item" onclick="selectSample(${index}, ${JSON.stringify(s).replace(/"/g, '&quot;')})">
                        <b>${s.name}</b> <small class="text-muted">| ${s.variety || 'N/A'}</small>
                    </div>`;
                });
                resultsBox.innerHTML = html;
                resultsBox.style.display = 'block';
            });
    }

    function selectSample(index, sample) {
        const row = document.getElementById(`cup-row-${index}`);
        row.querySelector('.js-sample-id').value = sample.id;
        row.querySelector('.js-sample-search').value = sample.name;
        
        row.querySelector('.js-variety').value = sample.variety || '';
        row.querySelector('.js-drying_process').value = sample.drying_process || '';
        row.querySelector('.js-fermentation').value = sample.fermentation || '';
        row.querySelector('.js-humidity').value = sample.humidity || '';
        row.querySelector('.js-screen').value = sample.screen || '';
        row.querySelector('.js-lot_id').value = sample.lot_id || '';
        row.querySelector('.js-altitude').value = sample.altitude || '';
        row.querySelector('.js-crop').value = sample.crop || '';
        row.querySelector('.js-origin').value = sample.origin || '';

        document.getElementById(`results-${index}`).style.display = 'none';
    }

    function closeTechModal() { techModal.hide(); }
    
    function removeLastCup() {
        if(cupCount <= 0) return;
        document.getElementById(`cup-row-${cupCount-1}`).remove();
        cupCount--;
        updateCounter();
        if(cupCount === 0) {
            document.getElementById('empty-state').style.display = 'block';
            document.getElementById('btnSubmitMesa').disabled = true;
        }
    }
    
    function updateCounter() { document.getElementById('cup-counter').innerText = `${cupCount} Xícaras`; }
</script>