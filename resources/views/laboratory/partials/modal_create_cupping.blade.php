<style>
    /* 1. FIXAÇÃO RÍGIDA DO TAMANHO DO MODAL */
    #modalCreateCupping .modal-dialog { max-width: 950px; margin: 1.75rem auto; }
    #modalCreateCupping .modal-content { height: 92vh; display: flex; flex-direction: column; overflow: hidden; border-radius: 20px; border: none; }
    #formCreateCupping { display: flex; flex-direction: column; height: 100%; overflow: hidden; }
    #modalCreateCupping .modal-body { flex: 1; overflow-y: scroll; padding: 0 1.5rem; background-color: #fcfcfc; scrollbar-width: none; }
    #modalCreateCupping .modal-body::-webkit-scrollbar { display: none; }

    /* CONTROLES STICKY */
    .sticky-controls { position: sticky; top: 0; background-color: #fcfcfc; z-index: 100; padding: 1.5rem 0; border-bottom: 1px solid #eee; margin-bottom: 1.5rem; }
    #modalCreateCupping .modal-footer { flex-shrink: 0; background: #fff; border-top: 1px solid #eee !important; padding: 1.5rem !important; }

    /* ESTILO DOS CARDS */
    .cup-card { border: 1px solid #dee2e6; border-radius: 12px; margin-bottom: 1rem; transition: all 0.2s; background: #fff; position: relative; }
    .cup-header { padding: 15px; display: flex; align-items: center; justify-content: space-between; }
    .cup-number { width: 32px; height: 32px; background: var(--coffee-brown); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem; margin-right: 12px; flex-shrink: 0; }
    
    /* CAMPO DE APELIDO COM FUNDO BRANCO DESTACADO */
    .nickname-container {
        flex-grow: 1;
        background: #ffffff; /* Fundo branco puro */
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 4px 12px;
        margin-right: 10px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    .nickname-label { font-size: 0.65rem; font-weight: 800; color: #adb5bd; text-transform: uppercase; margin-bottom: -2px; display: block; }
    .nickname-input { border: none !important; padding: 0 !important; font-weight: bold; color: var(--coffee-brown); width: 100%; outline: none; background: transparent; }

    .is-avulso-badge { background: #fd7e14; color: white; font-size: 0.65rem; padding: 2px 8px; border-radius: 4px; font-weight: bold; display: none; }
    .avulso-active .is-avulso-badge { display: inline-block; }
    .avulso-active .cup-number { background: #fd7e14; }
    
    /* LISTA DE RESULTADOS DA BUSCA */
    .sample-result-list { position: absolute; width: 100%; z-index: 1000; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .sample-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 0.85rem; }
    .sample-item:hover { background: #f0f0f0; }

    /* VALIDAÇÃO */
    .is-invalid-card { border: 2px solid #dc3545 !important; background-color: #fff8f8 !important; }
    #validation-error-msg { display: none; font-weight: bold; font-size: 0.85rem; color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
</style>

<div class="modal fade" id="modalCreateCupping" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 bg-light py-3">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);"><i class="bi bi-plus-circle-fill me-2"></i>Iniciar Mesa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formCreateCupping" action="{{ route('laboratory.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="sticky-controls">
                        <div id="validation-error-msg"></div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-muted small text-uppercase">Título da Mesa *</label>
                                <input type="text" id="mesa-title" name="title" class="form-control shadow-sm" placeholder="Ex: Prova de Safra 2024" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Xícaras</label>
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
                        <p class="text-muted">Adicione xícaras para começar.</p>
                    </div>
                </div>

                <div class="modal-footer border-0 d-flex flex-column align-items-center">
                    <div id="footer-error-msg"></div>
                    <button type="button" onclick="attemptSubmit()" class="btn btn-secondary w-100 rounded-pill py-3 fw-bold" id="btnSubmitMesa" disabled>
                        CRIAR MESA E INICIAR PROVA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCupTechInfo" tabindex="-1" aria-hidden="true" style="z-index: 1100;">
    <div class="modal-dialog modal-dialog-centered" id="tech-modal-content"></div>
</div>

<script>
    let cupCount = 0;

    function addNewCup() {
        if(cupCount >= 50) return;
        const index = cupCount++;
        
        const emptyState = document.getElementById('empty-state');
        if(emptyState) emptyState.style.display = 'none';

        const html = `
    <div class="cup-card shadow-sm" id="cup-row-${index}">
        <div class="cup-header">
            <div class="cup-number">${cupCount}</div>
            <div class="nickname-container">
                <label class="nickname-label">Apelido da Xícara *</label>
                <input type="text" name="items[${index}][nickname]" class="nickname-input js-nickname" placeholder="Ex: Xícara A..." autocomplete="off">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="is-avulso-badge">AVULSA</span>
                <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle" onclick="openTechInfo(${index})">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                </button>
            </div>
        </div>
        <div class="p-3 pt-0">
            <div class="row g-2">
                <div class="col-md-6 position-relative">
                    <input type="text" class="form-control form-control-sm border-0 bg-light js-sample-search" placeholder="Associar Amostra..." onkeyup="searchSample(this, ${index})">
                    <input type="hidden" name="items[${index}][sample_id]" class="js-sample-id">
                    <div class="sample-result-list shadow-lg" id="results-${index}"></div>
                </div>
                <div class="col-md-6">
                    <input type="text" name="items[${index}][notes]" class="form-control form-control-sm border-0 bg-light js-notes" placeholder="Obs...">
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
        <input type="hidden" name="items[${index}][origin_id]" class="js-origin_id">
        <input type="hidden" name="items[${index}][origin_type]" class="js-origin_type">
        <input type="hidden" name="items[${index}][position]" value="${cupCount}">
    </div>`;
        
        document.getElementById('cups-wrapper').insertAdjacentHTML('beforeend', html);
        updateCounter();
    }

    function removeLastCup() {
        if(cupCount <= 0) return;
        const lastRow = document.getElementById(`cup-row-${cupCount-1}`);
        if(lastRow) lastRow.remove();
        cupCount--;
        updateCounter();
        if(cupCount === 0) {
            document.getElementById('empty-state').style.display = 'block';
        }
    }

    function updateCounter() {
        const counter = document.getElementById('cup-counter');
        if(counter) counter.innerText = `${cupCount} Xícaras`;
    }

    function searchSample(input, index) {
        const query = input.value;
        const resultsBox = document.getElementById(`results-${index}`);
        if(query.length < 2) { resultsBox.style.display = 'none'; return; }

        fetch(`{{ route('registries.samples.search') }}?search=${query}`)
            .then(r => r.json())
            .then(data => {
                let html = '';
                if(data.length > 0) {
                    data.forEach(s => {
                        html += `<div class="sample-item" onclick='selectSample(${index}, ${JSON.stringify(s).replace(/'/g, "&#39;")})'>
                            <b>${s.name}</b> <small class="text-muted">| ${s.variety || 'N/A'}</small>
                        </div>`;
                    });
                } else {
                    html = `<div class="p-2 small text-muted">Nenhuma amostra encontrada</div>`;
                }
                resultsBox.innerHTML = html;
                resultsBox.style.display = 'block';
            });
    }

    function selectSample(index, sample) {
        const row = document.getElementById(`cup-row-${index}`);
        row.querySelector('.js-sample-id').value = sample.id;
        row.querySelector('.js-sample-search').value = sample.name;
        
        // Mapeamento automático de dados técnicos
        row.querySelector('.js-variety').value = sample.variety || '';
        row.querySelector('.js-drying_process').value = sample.drying_process || '';
        row.querySelector('.js-origin').value = sample.origin || '';
        row.querySelector('.js-fermentation').value = sample.fermentation || '';
        row.querySelector('.js-humidity').value = sample.humidity || '';
        row.querySelector('.js-screen').value = sample.screen || '';
        row.querySelector('.js-lot_id').value = sample.lot_id || '';
        row.querySelector('.js-altitude').value = sample.altitude || '';
        row.querySelector('.js-crop').value = sample.crop || '';

        document.getElementById(`results-${index}`).style.display = 'none';
    }

    function attemptSubmit() {
        const title = document.getElementById('mesa-title').value.trim();
        const errorDiv = document.getElementById('validation-error-msg');
        let errors = [];

        if (!title) errors.push("O título da mesa é obrigatório.");
        if (cupCount === 0) errors.push("Adicione pelo menos uma xícara.");

        for (let i = 0; i < cupCount; i++) {
            const row = document.getElementById(`cup-row-${i}`);
            if (!row) continue;
            const nick = row.querySelector('.js-nickname').value.trim();
            const sid = row.querySelector('.js-sample-id').value;
            const avulso = row.querySelector('.js-is-avulso').value === "1";

            if (!nick || (!sid && !avulso)) {
                row.classList.add('is-invalid-card');
                errors.push(`Xícara ${i+1} incompleta.`);
            } else {
                row.classList.remove('is-invalid-card');
            }
        }

        if (errors.length > 0) {
            errorDiv.innerHTML = `<i class="bi bi-exclamation-circle-fill me-2"></i> Preencha o título e os campos obrigatórios das xícaras.`;
            errorDiv.style.display = 'block';
            document.querySelector('.modal-body').scrollTop = 0;
            return;
        }

        document.getElementById('formCreateCupping').submit();
    }

    function openTechInfo(index) {
        const content = document.getElementById('tech-modal-content');
        content.innerHTML = '<div class="modal-content p-5 text-center"><div class="spinner-border text-primary mx-auto"></div></div>';
        
        const modalElement = document.getElementById('modalCupTechInfo');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        fetch(`/laboratory/cup-tech-info/${index}`)
            .then(r => r.text())
            .then(html => {
                content.innerHTML = html;
                const mainRow = document.getElementById(`cup-row-${index}`);
                document.querySelectorAll('.tech-field').forEach(f => {
                    f.value = mainRow.querySelector(`.js-${f.dataset.field}`).value;
                });
                const isAvulso = mainRow.querySelector('.js-is-avulso').value === "1";
                document.getElementById('modal-avulso-toggle').checked = isAvulso;
                toggleModalFields(isAvulso);
            });
    }

    function saveTechInfo(index) {
        const mainRow = document.getElementById(`cup-row-${index}`);
        const isAvulso = document.getElementById('modal-avulso-toggle').checked;
        document.querySelectorAll('.tech-field').forEach(f => {
            mainRow.querySelector(`.js-${f.dataset.field}`).value = f.value;
        });
        mainRow.querySelector('.js-is-avulso').value = isAvulso ? "1" : "0";
        mainRow.classList.toggle('avulso-active', isAvulso);
        if(isAvulso) {
            mainRow.querySelector('.js-sample-search').value = "AMOSTRA AVULSA";
            mainRow.querySelector('.js-sample-id').value = "";
        }
        
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalCupTechInfo'));
        modalInstance.hide();
    }

    function toggleModalFields(isAvulso) {
        const fields = document.querySelectorAll('.tech-field');
        const originContainer = document.getElementById('container-origin-field');
        fields.forEach(f => {
            f.readOnly = !isAvulso;
            f.classList.toggle('field-locked', !isAvulso);
        });
        if (originContainer) {
            originContainer.style.visibility = isAvulso ? 'hidden' : 'visible';
        }
    }

    function closeTechModal() {
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalCupTechInfo'));
        if(modalInstance) modalInstance.hide();
    }
</script>