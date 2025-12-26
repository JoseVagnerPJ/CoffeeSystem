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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 text-start">
                {{-- Dados da Empresa --}}
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <span class="info-label">CNPJ / Identificação</span>
                        <p class="fw-bold mb-0 text-dark">{{ $c->cnpj ?? '---' }}</p>
                    </div>
                    <div class="col-6">
                        <span class="info-label">Contato</span>
                        <p class="fw-bold mb-0 text-dark">{{ $c->contact ?? '---' }}</p>
                    </div>
                </div>

                <hr class="opacity-10">

                {{-- Fazendas Vinculadas --}}
                <div class="mb-4">
                    <label class="info-label mb-2 text-uppercase" style="font-size: 0.7rem;">Fazendas Vinculadas</label>
                    <div class="list-group list-group-flush">
                        @forelse($c->farms as $farm)
                            <div class="list-group-item px-0 py-2 d-flex align-items-center border-0 border-bottom">
                                <i class="bi bi-link-45deg text-info me-2"></i>
                                <span class="small fw-bold">{{ $farm->name }}</span>
                                <span class="ms-auto small text-muted">{{ $farm->city }}/{{ $farm->state }}</span>
                            </div>
                        @empty
                            <p class="small text-muted italic">Nenhuma fazenda vinculada.</p>
                        @endforelse
                    </div>
                </div>

                {{-- NOVA SEÇÃO: Produtores Vinculados Diretamente --}}
                <div>
                    <label class="info-label mb-2 text-uppercase" style="font-size: 0.7rem;">Produtores Vinculados</label>
                    <div class="list-group list-group-flush">
                        @forelse($c->producers as $producer)
                            <div class="list-group-item px-0 py-2 d-flex align-items-center border-0 border-bottom">
                                <i class="bi bi-person-check-fill text-success me-2"></i>
                                <span class="small fw-bold">{{ $producer->name }}</span>
                                <span class="ms-auto small text-muted">{{ $producer->contact ?? 'Sem contato' }}</span>
                            </div>
                        @empty
                            <p class="small text-muted italic">Nenhum produtor vinculado diretamente.</p>
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