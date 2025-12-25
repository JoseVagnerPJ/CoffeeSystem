<div class="modal fade" id="modalInfoMachine{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-light rounded-top p-4">
                <h5 class="fw-bold mb-0 text-info"><i class="bi bi-info-circle-fill me-2"></i>Ficha Técnica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Identificação do Equipamento --}}
                <div class="mb-4">
                    <span class="info-label">Equipamento</span>
                    <h5 class="fw-bold mb-0">{{ $m->name }}</h5>
                    <small class="text-muted">
                        {{ $m->brand }} - {{ $m->model }} 
                        @if($m->year) <span class="ms-1">({{ $m->year }})</span> @endif
                    </small>
                </div>

                {{-- Informações Técnicas em 3 Colunas --}}
                <div class="row g-3 mb-4 text-center">
                    <div class="col-4 border-end">
                        <span class="info-label">Número de Série</span>
                        <p class="small fw-bold mb-0 text-truncate" title="{{ $m->serial_number ?? 'Não informado' }}">
                            {{ $m->serial_number ?? '---' }}
                        </p>
                    </div>
                    <div class="col-4 border-end">
                        <span class="info-label">Capacidade Mín.</span>
                        <p class="small fw-bold mb-0 text-secondary">{{ number_format($m->min_capacity, 1) }} kg</p>
                    </div>
                    <div class="col-4">
                        <span class="info-label">Capacidade Máx.</span>
                        <p class="small fw-bold mb-0 text-primary">{{ number_format($m->max_capacity, 1) }} kg</p>
                    </div>
                </div>

                <hr class="opacity-10">
                
                {{-- Histórico de Manutenções --}}
                <h6 class="fw-bold small mb-3 text-uppercase" style="font-size: 0.7rem; color: var(--coffee-brown);">Histórico de Manutenções</h6>
                <div class="timeline-container px-2" style="max-height: 300px; overflow-y: auto;">
                    @forelse($m->maintenances as $mt)
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="small fw-bold">
                            {{-- Data de Realização --}}
                            {{ date('d/m/Y', strtotime($mt->maintenance_date)) }}
                
                            {{-- Data Sugerida com Traço --}}
                            @if($mt->next_maintenance_date)
                                <span class="text-primary mx-1">—</span> 
                                <span class="text-primary" title="Próxima manutenção sugerida">
                                    Próxima manutenção sugerida: {{ date('d/m/Y', strtotime($mt->next_maintenance_date)) }}
                                </span>
                            @endif
                        </div>
                        <div class="small text-muted fw-bold">{{ $mt->provider }}</div>
                        <p class="small text-muted mb-0">{{ $mt->description }}</p>
                    </div>
                    @empty
                        <p class="small text-muted italic text-center py-2">Nenhuma manutenção técnica registrada.</p>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light w-100 rounded-pill fw-bold border" data-bs-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>
</div>