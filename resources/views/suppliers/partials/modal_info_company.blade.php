<div class="modal fade" id="modalInfoCompany{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-light p-4" style="border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-buildings-fill text-info fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color: var(--coffee-brown);">{{ $c->name }}</h5>
                        <small class="badge bg-white text-dark border fw-normal">{{ $c->action_type }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-start">
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <span class="info-label">CNPJ / Identificação</span>
                        <p class="fw-bold mb-0 text-dark">{{ $c->cnpj ?? 'Não informado' }}</p>
                    </div>
                    <div class="col-6">
                        <span class="info-label">Contato</span>
                        <p class="fw-bold mb-0 text-dark">{{ $c->contact ?? 'Não informado' }}</p>
                    </div>
                    <div class="col-12">
                        <span class="info-label">Sede / Localização</span>
                        <p class="fw-bold mb-0 text-dark">
                            @if($c->is_international)
                                <i class="bi bi-globe me-1 text-primary"></i> {{ $c->country }}
                            @else
                                <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $c->city }} / {{ $c->state }}
                            @endif
                        </p>
                    </div>
                </div>

                <hr class="opacity-10">

                <div class="mb-2">
                    <label class="info-label mb-2 text-uppercase" style="font-size: 0.7rem;">Fazendas que entregam para esta Empresa</label>
                    <div class="list-group list-group-flush">
                        @forelse($c->farms as $farm)
                            <div class="list-group-item px-0 py-2 d-flex align-items-center border-0 border-bottom">
                                <i class="bi bi-link-45deg text-info me-2"></i>
                                <span class="small fw-bold">{{ $farm->name }}</span>
                                <span class="ms-auto small text-muted">{{ $farm->city }}/{{ $farm->state }}</span>
                            </div>
                        @empty
                            <div class="bg-light p-3 rounded-3 text-center">
                                <p class="small text-muted mb-0 italic">Nenhuma fazenda vinculada a esta cooperativa.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 bg-light rounded-bottom">
                <button type="button" class="btn btn-dark px-4 rounded-pill fw-bold w-100" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>