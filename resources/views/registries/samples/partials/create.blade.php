<div class="modal fade" id="modalCreateSample" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Nova Amostra de Café</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Rota atualizada para registries.samples.store --}}
            <form action="{{ route('registries.samples.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Nome e Data --}}
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Nome (Apelido para busca futura)</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ex: Lote Especial Geisha" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Data de Recebimento</label>
                            <input type="date" name="receipt_date" class="form-control" required value="{{ \Carbon\Carbon::now('America/Sao_Paulo')->format('Y-m-d') }}" style="border-radius: 8px; height: 45px;">
                        </div>

                        {{-- BUSCA DINÂMICA DE FORNECEDOR --}}
                        <div class="col-12 position-relative" id="origin-search-container">
                            <label class="form-label fw-bold small text-uppercase" style="color: var(--coffee-brown);">Fornecedor / Produtor</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px; height: 45px;">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="search-origin" class="form-control border-start-0" 
                                       placeholder="Procurar fornecedor cadastrado..." autocomplete="off"
                                       style="border-radius: 0 8px 8px 0; height: 45px;">
                            </div>
                            
                            {{-- Campos Ocultos --}}
                            <input type="hidden" name="origin_id" id="origin_id" required>
                            <input type="hidden" name="origin_type" id="origin_type" required>

                            {{-- Painel flutuante de resultados --}}
                            <div id="search-results" class="list-group position-absolute w-100 shadow-lg d-none">
                                {{-- Injetado via JS --}}
                            </div>
                        </div>

                        {{-- Identificação Técnica --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">ID Lote Fornecedor</label>
                            <input type="text" name="supplier_batch" class="form-control" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Variedade</label>
                            <input type="text" name="variety" class="form-control" required style="border-radius: 8px; height: 45px;">
                        </div>

                        {{-- Processamento --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Processo de Secagem</label>
                            <input type="text" name="drying_process" class="form-control" required placeholder="Ex: Natural" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Fermentação</label>
                            <input type="text" name="fermentation" class="form-control" placeholder="Ex: Anaeróbica" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Umidade (%)</label>
                            <input type="number" step="0.01" name="humidity" class="form-control" placeholder="Opcional" style="border-radius: 8px; height: 45px;">
                        </div>

                        {{-- Dados Opcionais --}}
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Peneira</label>
                            <input type="text" name="screen" class="form-control" placeholder="Opcional" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Altitude (m)</label>
                            <input type="number" name="altitude" class="form-control" placeholder="Opcional" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Safra</label>
                            <input type="text" name="crop" class="form-control" placeholder="Opcional" style="border-radius: 8px; height: 45px;">
                        </div>

                        {{-- Observações --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Observações</label>
                            <textarea name="observations" class="form-control" rows="3" placeholder="Informações adicionais..." style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancelar</button>
                    <button type="submit" class="btn text-white px-4" style="background-color: #3d2b1f; border-radius: 8px;">Salvar Amostra</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #search-results {
        z-index: 9999 !important;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        max-height: 250px;
        overflow-y: auto;
        top: 100%;
        left: 0;
    }
    .select-result {
        cursor: pointer;
        transition: background 0.2s;
        text-align: left;
        width: 100%;
    }
    .select-result:hover {
        background-color: #f8f9fa !important;
    }
</style>

<script>
$(document).ready(function() {
    let searchTimeout;

    $('#search-origin').on('input', function() {
        let query = $(this).val();
        let resultsContainer = $('#search-results');

        if (query.length === 0) {
            $('#origin_id').val('');
            $('#origin_type').val('');
        }

        clearTimeout(searchTimeout);
        
        if (query.length < 2) { 
            resultsContainer.addClass('d-none'); 
            return; 
        }

        searchTimeout = setTimeout(function() {
            {{-- Rota de busca atualizada --}}
            $.get("{{ route('registries.samples.search-origin') }}", { q: query }, function(data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        html += `
                            <button type="button" class="list-group-item list-group-item-action border-0 py-2 select-result" 
                                    data-id="${item.id}" data-type="${item.type}" data-name="${item.name}">
                                <div class="fw-bold text-dark">${item.name}</div>
                                <small class="text-muted text-uppercase" style="font-size: 0.65rem;">${item.label}</small>
                            </button>`;
                    });
                    resultsContainer.html(html).removeClass('d-none');
                } else {
                    resultsContainer.html('<div class="list-group-item text-muted">Nenhum fornecedor encontrado.</div>').removeClass('d-none');
                }
            });
        }, 300);
    });

    $(document).on('click', '.select-result', function(e) {
        e.preventDefault();
        $('#search-origin').val($(this).data('name'));
        $('#origin_id').val($(this).data('id'));
        $('#origin_type').val($(this).data('type'));
        $('#search-results').addClass('d-none');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#origin-search-container').length) {
            $('#search-results').addClass('d-none');
        }
    });
});
</script>