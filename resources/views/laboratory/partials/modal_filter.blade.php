<div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">
                    <i class="bi bi-funnel me-2"></i>Filtros Avançados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('laboratorio') }}" method="GET">
                <div class="modal-body p-4">
                    {{-- Filtro por Nome e Amostra --}}
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase mb-1">Busca Geral</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="text" name="title" class="form-control form-control-sm border-light-subtle bg-light" 
                                       placeholder="Nome da Mesa" value="{{ request('title') }}">
                            </div>
                            <div class="col-6">
                                <input type="text" name="cup_nickname" class="form-control form-control-sm border-light-subtle bg-light" 
                                       placeholder="Apelido da Xícara" value="{{ request('cup_nickname') }}">
                            </div>
                        </div>
                    </div>

                    {{-- NOVO: Filtro por Perfil de Prova --}}
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase mb-1">Perfil de Prova (Protocolo)</label>
                        <select name="cupping_profile_id" class="form-select form-select-sm border-light-subtle bg-light">
                            <option value="">Todos os Perfis</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" {{ request('cupping_profile_id') == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="opacity-25">

                    {{-- Filtro por Data --}}
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase mb-1">Período da Mesa</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" name="start_date" class="form-control form-control-sm border-light-subtle bg-light" 
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-6">
                                <input type="date" name="end_date" class="form-control form-control-sm border-light-subtle bg-light" 
                                       value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Filtro por Status e Participantes --}}
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm border-light-subtle bg-light">
                                    <option value="">Todos</option>
                                    <option value="aberto" {{ request('status') == 'aberto' ? 'selected' : '' }}>Aberto</option>
                                    <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted text-uppercase mb-1">Mín. Provadores</label>
                                <input type="number" name="min_participants" class="form-control form-control-sm border-light-subtle bg-light" 
                                       placeholder="Ex: 2" value="{{ request('min_participants') }}">
                            </div>
                        </div>
                    </div>

                    <hr class="opacity-25">

                    {{-- Filtro por Pontuação SCA --}}
                    <div class="mb-2">
                        <label class="small fw-bold text-muted text-uppercase mb-1">Intervalo de Pontuação (Média SCA)</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <input type="number" step="0.01" name="min_score" class="form-control form-control-sm border-light-subtle bg-light text-center" 
                                       placeholder="Mín" value="{{ request('min_score') }}">
                            </div>
                            <div class="col-2 text-center text-muted small">até</div>
                            <div class="col-5">
                                <input type="number" step="0.01" name="max_score" class="form-control form-control-sm border-light-subtle bg-light text-center" 
                                       placeholder="Máx" value="{{ request('max_score') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <div class="row w-100 g-2">
                        <div class="col-4">
                            <a href="{{ route('laboratorio') }}" class="btn btn-light w-100 rounded-pill fw-bold py-2 small border">
                                LIMPAR
                            </a>
                        </div>
                        <div class="col-8">
                            <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2 shadow">
                                APLICAR FILTROS
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>