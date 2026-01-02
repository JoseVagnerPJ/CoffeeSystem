<div class="modal fade" id="modalFilterSensory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">
                    <i class="bi bi-sliders me-2"></i>Filtros de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('registries.sensory-profiles.index') }}" method="GET">
                {{-- Mantém a busca textual principal ativa --}}
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Tipo de Perfil --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Tipo:</label>
                            <select name="filter_type" class="form-select">
                                <option value="">Todos</option>
                                <option value="1" {{ request('filter_type') == '1' ? 'selected' : '' }}>Blend</option>
                                <option value="0" {{ request('filter_type') == '0' ? 'selected' : '' }}>Único (Varietal)</option>
                            </select>
                        </div>

                        {{-- Intensidade de Torra --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Torra:</label>
                            <select name="filter_roast" class="form-select">
                                <option value="">Todas</option>
                                <option value="Clara" {{ request('filter_roast') == 'Clara' ? 'selected' : '' }}>Clara</option>
                                <option value="Média-Clara" {{ request('filter_roast') == 'Média-Clara' ? 'selected' : '' }}>Média-Clara</option>
                                <option value="Média" {{ request('filter_roast') == 'Média' ? 'selected' : '' }}>Média</option>
                                <option value="Média-Escura" {{ request('filter_roast') == 'Média-Escura' ? 'selected' : '' }}>Média-Escura</option>
                                <option value="Escura" {{ request('filter_roast') == 'Escura' ? 'selected' : '' }}>Escura</option>
                            </select>
                        </div>

                        {{-- NOVO: Variedade do Café --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Variedade do Grão (Composição):</label>
                            <input type="text" name="filter_variety" class="form-control" placeholder="Ex: Catuaí, Bourbon..." value="{{ request('filter_variety') }}">
                        </div>

                        {{-- Notas Sensoriais --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Notas Sensoriais (#tags):</label>
                            <input type="text" name="filter_notes" class="form-control" placeholder="Ex: #frutado, #doce..." value="{{ request('filter_notes') }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <a href="{{ route('registries.sensory-profiles.index') }}" class="btn btn-light px-4">Limpar Filtros</a>
                    <button type="submit" class="btn btn-dark px-4 fw-bold">Aplicar</button>
                </div>
            </form>
        </div>
    </div>
</div>