<div class="modal fade" id="modalInfoFarm{{ $f->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-light">
                <h5 class="fw-bold mb-0 text-info"><i class="bi bi-map me-2"></i>Dados da Fazenda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <span class="info-label">Nome da Propriedade</span>
                    <h5 class="fw-bold text-dark">{{ $f->name }}</h5>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <span class="info-label">Altitude</span>
                        <h6 class="fw-bold"><i class="bi bi-reception-4 me-1 text-primary"></i>{{ $f->altitude }}m</h6>
                    </div>
                    <div class="col-6">
                        <span class="info-label">Localização</span>
                        <p class="fw-bold small mb-0">
                            @if($f->is_international)
                                <i class="bi bi-globe me-1 text-primary"></i>{{ $f->country }} ({{ $f->district }})
                            @else
                                <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $f->city }}/{{ $f->state }}
                            @endif
                        </p>
                    </div>
                    <div class="col-12">
                        <span class="info-label">Variedades Cultivadas</span>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @php $tags = explode(',', $f->varieties_tags); @endphp
                            @foreach($tags as $tag)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fw-normal">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold small border-bottom pb-2 mb-3 text-uppercase" style="color: var(--coffee-brown); font-size: 0.7rem;">Vínculos Registrados</h6>
                <div class="mb-3">
                    <span class="info-label">Produtores Vinculados</span>
                    @forelse($f->producers as $prod)
                        <div class="d-flex align-items-center mb-1 bg-light p-2 rounded">
                            <i class="bi bi-person-circle text-secondary me-2"></i>
                            <span class="small fw-bold">{{ $prod->name }}</span>
                        </div>
                    @empty
                        <span class="text-muted small italic">Nenhum produtor vinculado.</span>
                    @endforelse
                </div>

                <div class="mb-4">
                    <span class="info-label">Empresas Vinculadas</span>
                    @forelse($f->companies as $comp)
                        <div class="d-flex align-items-center mb-1 bg-light p-2 rounded">
                            <i class="bi bi-buildings text-secondary me-2"></i>
                            <span class="small fw-bold">{{ $comp->name }}</span>
                        </div>
                    @empty
                        <span class="text-muted small italic">Nenhuma empresa vinculada.</span>
                    @endforelse
                </div>

                <h6 class="fw-bold small border-bottom pb-2 mb-3 text-uppercase text-success" style="font-size: 0.7rem;">Amostras Registradas</h6>
                <div class="list-group list-group-flush">
                    @forelse($f->samples as $sample)
                        <div class="list-group-item px-0 py-2 d-flex align-items-center border-0 border-bottom">
                            <i class="bi bi-box-seam text-success me-2"></i>
                            <div>
                                <span class="d-block small fw-bold text-dark">{{ $sample->name }}</span>
                                <small class="text-muted" style="font-size: 0.65rem;">Lote: {{ $sample->supplier_batch }} | Var: {{ $sample->variety }}</small>
                            </div>
                            <span class="ms-auto badge bg-success bg-opacity-10 text-success border-0 small">#{{ $sample->id }}</span>
                        </div>
                    @empty
                        <p class="small text-muted italic">Nenhuma amostra vinculada a esta fazenda.</p>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light rounded-bottom">
                <button type="button" class="btn btn-dark px-4 rounded-pill fw-bold w-100" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>