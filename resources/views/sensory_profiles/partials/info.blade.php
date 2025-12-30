<div class="modal-header border-0 pb-0">
    <h5 class="modal-title fw-bold d-flex align-items-center" style="color: var(--coffee-brown);">
        <i class="bi bi-file-earmark-bar-graph me-2"></i>
        Ficha Sensorial do Perfil
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    <div class="row g-4">
        {{-- COLUNA ESQUERDA: Identidade e Atributos --}}
        <div class="col-md-5 border-end pe-md-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div style="width: 70px; height: 70px; border-radius: 15px; background-color: {{ $profile->color_hex }}; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></div>
                <div>
                    <h2 class="fw-bold mb-0" style="color: #2c2c2c; font-size: 1.6rem;">{{ $profile->name }}</h2>
                    <span class="badge bg-dark text-uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">
                        {{ $profile->is_blend ? 'Composição Blend' : 'Varietal Pura' }}
                    </span>
                </div>
            </div>

            <div class="p-3 rounded-4 border" style="background-color: #f9f9f9;">
                <small class="text-muted fw-bold d-block mb-3 text-center text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Intensidade dos Atributos</small>
                
                @foreach(['sweetness' => 'Doçura', 'acidity' => 'Acidez', 'body' => 'Corpo', 'bitterness' => 'Amargor'] as $field => $label)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small fw-bold mb-1">
                        <span class="text-dark">{{ $label }}</span>
                        <span style="color: var(--coffee-brown);">{{ $profile->$field }}/5</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px; background-color: #e9ecef;">
                        <div class="progress-bar shadow-sm" 
                             role="progressbar" 
                             style="width: {{ ($profile->$field / 5) * 100 }}%; background-color: var(--coffee-brown);" 
                             aria-valuenow="{{ $profile->$field }}" 
                             aria-valuemin="0" 
                             aria-valuemax="5">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="row mt-4 text-center g-2">
                <div class="col-5 border-end">
                    <small class="text-muted fw-bold d-block small-label">TORRA PADRÃO</small>
                    <span class="fw-bold text-dark">{{ $profile->roast_intensity }}</span>
                </div>
                <div class="col-7">
                    <small class="text-muted fw-bold d-block small-label">NOTAS SENSORIAIS</small>
                    <span class="text-primary fw-bold" style="font-size: 0.9rem;">{{ $profile->notes ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA: Composição Técnica das Amostras --}}
        <div class="col-md-7 ps-md-4">
            <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing: 1px;">Amostras Vinculadas e Composição</h6>
            
            <div class="list-group shadow-sm overflow-auto" style="max-height: 380px; border-radius: 12px;">
                @foreach($profile->components as $component)
                    <div class="list-group-item border-0 bg-white mb-2 rounded-3 shadow-sm d-flex justify-content-between align-items-center p-3 border-start border-4" style="border-left-color: var(--coffee-brown) !important;">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark fs-6">{{ $component->sample->name }}</span>
                                <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.65rem;">ID #{{ $component->sample->id }}</span>
                            </div>
                            <div class="mt-1">
                                <small class="text-muted">
                                    <strong>Variedade:</strong> {{ $component->sample->variety }} | 
                                    <strong>Origem:</strong> {{ $component->sample->origin->name ?? 'N/D' }}
                                </small>
                            </div>
                        </div>
                        <div class="ms-3 text-end">
                            <div class="h4 mb-0 fw-bold" style="color: var(--coffee-brown);">{{ number_format($component->percentage, 0) }}%</div>
                            <small class="text-muted fw-bold" style="font-size: 0.6rem;">DA COMPOSIÇÃO</small>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($profile->is_blend)
                <div class="mt-3 p-2 bg-light rounded-3 text-center border">
                    <small class="text-muted">Este perfil é um <strong>Blend</strong> técnico balanceado no laboratório.</small>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer border-0 p-4 pt-0">
    <button type="button" class="btn w-100 py-2 fw-bold text-white shadow-sm" data-bs-dismiss="modal" style="background-color: #2c2c2c; border-radius: 10px; font-size: 0.9rem;">
        Fechar Visualização
    </button>
</div>

<style>
    .small-label {
        font-size: 0.65rem;
        letter-spacing: 0.5px;
        color: #888 !important;
        margin-bottom: 4px;
    }
</style>