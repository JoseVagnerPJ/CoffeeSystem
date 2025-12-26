<div class="modal fade" id="modalInfoProducer{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-light">
                <h5 class="fw-bold mb-0 text-info"><i class="bi bi-person-badge me-2"></i>Ficha do Produtor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <span class="info-label">Nome Completo</span>
                    <h5 class="fw-bold text-dark">{{ $p->name }}</h5>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <span class="info-label">CPF</span>
                        <p class="fw-bold mb-0">{{ $p->cpf ?? '---' }}</p>
                    </div>
                    <div class="col-6">
                        <span class="info-label">Contato</span>
                        <p class="fw-bold mb-0">{{ $p->contact ?? '---' }}</p>
                    </div>
                </div>
                
                {{-- Seção de Fazendas --}}
                <h6 class="fw-bold small border-bottom pb-2 mb-3 text-uppercase text-secondary">Fazendas Vinculadas</h6>
                <div class="list-group list-group-flush mb-4">
                    @forelse($p->farms as $farm)
                        <div class="list-group-item px-0 py-2 d-flex align-items-center border-0">
                            <i class="bi bi-patch-check-fill text-success me-2"></i>
                            <span class="small fw-bold">{{ $farm->name }}</span>
                            <span class="ms-auto badge bg-light text-dark border fw-normal">{{ $farm->city }}/{{ $farm->state }}</span>
                        </div>
                    @empty
                        <p class="small text-muted italic">Nenhuma fazenda vinculada.</p>
                    @endforelse
                </div>

                {{-- NOVA SEÇÃO: Empresas Vinculadas Diretamente --}}
                <h6 class="fw-bold small border-bottom pb-2 mb-3 text-uppercase text-secondary">Empresas Vinculadas</h6>
                <div class="list-group list-group-flush">
                    @forelse($p->companies as $company)
                        <div class="list-group-item px-0 py-2 d-flex align-items-center border-0">
                            <i class="bi bi-buildings text-primary me-2"></i>
                            <span class="small fw-bold">{{ $company->name }}</span>
                            <span class="ms-auto badge bg-primary bg-opacity-10 text-primary border-0 fw-bold" style="font-size: 0.6rem;">{{ $company->action_type }}</span>
                        </div>
                    @empty
                        <p class="small text-muted italic">Nenhum vínculo com empresas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>