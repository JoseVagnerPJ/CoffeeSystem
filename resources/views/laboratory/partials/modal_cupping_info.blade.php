<div class="modal-content border-0 shadow-lg" style="background: #f8f9fa;">
    <div class="modal-header bg-dark text-white">
        <div class="d-flex flex-column">
            <h5 class="modal-title fw-bold">Xícara #{{ $item->position }} - {{ $item->nickname }}</h5>
            <small class="opacity-75">{{ $item->is_avulso ? ($item->variety ?? 'Variedade não informada') : (optional($item->sample)->variety ?? 'N/A') }}</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        {{-- FICHA TÉCNICA (GRID ESTILO APP) --}}
        <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing: 1px;">Ficha Técnica (Snapshot)</h6>
        <div class="row g-2 mb-4 text-center">
            @php 
                $specs = [
                    'Processo' => $item->is_avulso ? $item->drying_process : optional($item->sample)->drying_process,
                    'Fermentação' => $item->fermentation,
                    'Altitude' => $item->altitude,
                    'Safra' => $item->crop,
                    'Umidade' => $item->humidity,
                    'Peneira' => $item->screen
                ];
            @endphp
            @foreach($specs as $label => $val)
                <div class="col-4 col-md-2">
                    <div class="card card-body p-2 border-0 shadow-sm h-100">
                        <small class="text-muted d-block" style="font-size: 0.6rem;">{{ strtoupper($label) }}</small>
                        <span class="fw-bold small">{{ $val ?: 'N/A' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- AVALIAÇÕES INDIVIDUAIS --}}
        <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing: 1px;">Relatório de Notas dos Provadores</h6>
        
        @foreach($item->evaluations as $eval)
            <div class="card border-0 shadow-sm mb-3 overflow-hidden" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-person-circle text-muted me-2"></i>
                        <span class="fw-bold text-dark">{{ $eval->user->name }}</span>
                    </div>
                    <span class="badge bg-primary fs-6 px-3">
                        {{ number_format(60 + $eval->acidity + $eval->body + $eval->sweetness + $eval->aftertaste, 2) }} SCA
                    </span>
                </div>
                <div class="card-body bg-white pt-0">
                    <div class="row g-2 mb-3 text-center">
                        @foreach(['Acidez' => 'acidity', 'Corpo' => 'body', 'Doçura' => 'sweetness', 'Retrogosto' => 'aftertaste'] as $label => $attr)
                            <div class="col-3">
                                <div class="p-2 rounded" style="background: #fdfdfd; border: 1px solid #f1f1f1;">
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">{{ $label }}</small>
                                    <span class="fw-bold text-primary">{{ number_format($eval->$attr, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- SENSORIAL --}}
                    <div class="mb-2">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem;">Sensorial:</small>
                        @foreach($eval->tags as $tag)
                            <span class="badge bg-light text-dark border-secondary-subtle border fw-normal me-1" style="font-size: 0.7rem;">#{{ $tag->name }}</span>
                        @endforeach
                        @if($eval->tags->isEmpty()) <span class="text-muted small">Nenhuma nota informada.</span> @endif
                    </div>

                    {{-- OBSERVAÇÕES --}}
                    @if($eval->observations)
                        <div class="mt-3 p-3 rounded-3 small" style="background: #f8f9fa; border-left: 3px solid #dee2e6;">
                            <i class="bi bi-chat-left-text text-muted me-2"></i>
                            <span class="text-dark italic">{{ $eval->observations }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>