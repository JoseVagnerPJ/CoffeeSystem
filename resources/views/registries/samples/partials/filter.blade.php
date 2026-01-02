<div class="modal fade" id="modalFilterSample" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">
                    <i class="bi bi-sliders me-2"></i>Filtros Avançados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Rota atualizada para registries.samples.index --}}
            <form action="{{ route('registries.samples.index') }}" method="GET">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Período --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Recebido a partir de:</label>
                            <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Até:</label>
                            <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}">
                        </div>

                        {{-- Atributos Técnicos --}}
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Variedade:</label>
                            <input type="text" name="filter_variety" class="form-control" placeholder="Ex: Bourbon" value="{{ request('filter_variety') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Fermentação:</label>
                            <input type="text" name="filter_fermentation" class="form-control" placeholder="Ex: Anaeróbica" value="{{ request('filter_fermentation') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Drying Process:</label>
                            <select name="filter_drying" class="form-select">
                                <option value="">Todos</option>
                                <option value="Natural" {{ request('filter_drying') == 'Natural' ? 'selected' : '' }}>Natural</option>
                                <option value="Cereja Descascado" {{ request('filter_drying') == 'Cereja Descascado' ? 'selected' : '' }}>Cereja Descascado</option>
                                <option value="Fully Washed" {{ request('filter_drying') == 'Fully Washed' ? 'selected' : '' }}>Fully Washed</option>
                                <option value="Honey" {{ request('filter_drying') == 'Honey' ? 'selected' : '' }}>Honey</option>
                            </select>
                        </div>

                        {{-- Busca Sensorial --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Notas Sensoriais / Observações:</label>
                            <input type="text" name="filter_sensory" class="form-control" placeholder="Ex: Frutado, Chocolate, Acidez alta..." value="{{ request('filter_sensory') }}">
                        </div>

                        {{-- Fornecedor (Busca dinâmica) --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Filtrar por Fornecedor (Busca):</label>
                            <div class="position-relative" id="filter-origin-container">
                                <input type="text" id="filter_origin_name" class="form-control" placeholder="Digite o nome para buscar..." autocomplete="off">
                                <input type="hidden" name="filter_origin_id" id="filter_origin_id" value="{{ request('filter_origin_id') }}">
                                <input type="hidden" name="filter_origin_type" id="filter_origin_type" value="{{ request('filter_origin_type') }}">
                                <div id="filter_origin_results" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 1050;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <a href="{{ route('registries.samples.index') }}" class="btn btn-light px-4">Limpar Tudo</a>
                    <button type="submit" class="btn btn-dark px-4 fw-bold">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#filter_origin_name').on('input', function() {
            let q = $(this).val();
            let results = $('#filter_origin_results');
            if (q.length < 2) { results.addClass('d-none'); return; }

            {{-- Rota de busca atualizada --}}
            $.get("{{ route('registries.samples.search-origin') }}", { q: q }, function(data) {
                let html = '';
                data.forEach(item => {
                    html += `<button type="button" class="list-group-item list-group-item-action select-filter-origin" 
                        data-id="${item.id}" data-type="${item.type}" data-name="${item.name}">
                        <strong>${item.name}</strong> <small class="text-muted">(${item.label})</small>
                    </button>`;
                });
                results.html(html).removeClass('d-none');
            });
        });

        $(document).on('click', '.select-filter-origin', function() {
            $('#filter_origin_name').val($(this).data('name'));
            $('#filter_origin_id').val($(this).data('id'));
            $('#filter_origin_type').val($(this).data('type'));
            $('#filter_origin_results').addClass('d-none');
        });
    });
</script>