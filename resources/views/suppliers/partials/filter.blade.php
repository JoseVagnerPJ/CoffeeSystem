<div class="modal fade" id="modalFilterSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">
                    <i class="bi bi-sliders me-2"></i>Filtros Avançados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('suppliers.index') }}" method="GET" id="filterForm">
                {{-- O campo 'tab' é atualizado dinamicamente via JS para manter a aba correta --}}
                <input type="hidden" name="tab" id="modal_active_tab" value="{{ $activeTab ?? 'producers' }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Seleção de Categoria Principal --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Exibir apenas na categoria:</label>
                            <select name="filter_type" id="select_filter_type_modal" class="form-select shadow-sm">
                                <option value="">Todas as categorias</option>
                                <option value="producer" {{ request('filter_type') == 'producer' ? 'selected' : '' }}>Produtores</option>
                                <option value="farm" {{ request('filter_type') == 'farm' ? 'selected' : '' }}>Fazendas</option>
                                <option value="company" {{ request('filter_type') == 'company' ? 'selected' : '' }}>Empresas / Cooperativas</option>
                            </select>
                        </div>

                        {{-- Filtros Comuns (Estado e Internacional) --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Estado (UF):</label>
                            <input type="text" name="filter_state" class="form-control shadow-sm" placeholder="Ex: MG" value="{{ request('filter_state') }}" maxlength="2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Internacional:</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="filter_intl" value="1" {{ request('filter_intl') ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted">Sim</label>
                            </div>
                        </div>

                        {{-- CAMPOS ESPECÍFICOS: FAZENDA --}}
                        <div id="group_farm_filters_modal" class="col-12 d-none">
                            <div class="row g-3 border-top pt-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Altitude Mínima:</label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" name="filter_altitude" class="form-control" value="{{ request('filter_altitude') }}">
                                        <span class="input-group-text small">m</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Variedade:</label>
                                    <input type="text" name="filter_variety" class="form-control shadow-sm" placeholder="Ex: Geisha" value="{{ request('filter_variety') }}">
                                </div>
                            </div>
                        </div>

                        {{-- CAMPOS ESPECÍFICOS: EMPRESA (Dropdown de tipos adicionado) --}}
                        <div id="group_company_filters_modal" class="col-12 d-none">
                            <div class="border-top pt-3">
                                <label class="form-label small fw-bold text-muted">Tipo de Atuação:</label>
                                <select name="filter_action" class="form-select shadow-sm">
                                    <option value="">Todos os tipos</option>
                                    <option value="Cooperativa" {{ request('filter_action') == 'Cooperativa' ? 'selected' : '' }}>Cooperativa</option>
                                    <option value="Exportadora" {{ request('filter_action') == 'Exportadora' ? 'selected' : '' }}>Exportadora</option>
                                    <option value="Distribuidora" {{ request('filter_action') == 'Distribuidora' ? 'selected' : '' }}>Distribuidora</option>
                                    <option value="Fazenda Independente" {{ request('filter_action') == 'Fazenda Independente' ? 'selected' : '' }}>Fazenda Independente</option>
                                    <option value="Outros" {{ request('filter_action') == 'Outros' ? 'selected' : '' }}>Outros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0 text-end">
                    <a href="{{ route('suppliers.index', ['tab' => $activeTab ?? 'producers']) }}" class="btn btn-light px-4 rounded-pill">Limpar</a>
                    <button type="submit" class="btn btn-dark px-4 rounded-pill fw-bold">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /**
     * Atualiza a interface do modal e o comportamento da aba ativa
     */
    function updateModalFilters() {
        const type = document.getElementById('select_filter_type_modal').value;
        const farmGroup = document.getElementById('group_farm_filters_modal');
        const companyGroup = document.getElementById('group_company_filters_modal');
        const tabInput = document.getElementById('modal_active_tab');

        // 1. Alterna visibilidade dos campos técnicos baseada na categoria
        farmGroup.classList.toggle('d-none', type !== 'farm');
        companyGroup.classList.toggle('d-none', type !== 'company');

        // 2. Mapeia a seleção para a aba plural correta para garantir o redirecionamento
        const mapping = {
            'producer': 'producers',
            'farm': 'farms',
            'company': 'companies'
        };

        if (type && mapping[type]) {
            tabInput.value = mapping[type];
        }
    }

    // Eventos de ativação do script
    document.getElementById('select_filter_type_modal').addEventListener('change', updateModalFilters);
    document.addEventListener('DOMContentLoaded', updateModalFilters);
</script>