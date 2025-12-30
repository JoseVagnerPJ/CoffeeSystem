<div class="modal-header border-0 pb-0">
    <h5 class="modal-title fw-bold d-flex align-items-center" style="color: var(--coffee-brown); font-size: 1.1rem;">
        <i class="bi bi-file-earmark-text-fill me-2"></i>
        Ficha Técnica da Amostra
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    <div class="row g-4">
        {{-- COLUNA DA ESQUERDA: Dados Principais e Técnicos --}}
        <div class="col-md-8 border-end">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="fw-bold mb-0" style="color: #2c2c2c; font-size: 1.5rem;">{{ $sample->name }}</h2>
                    <p class="text-muted fw-bold mb-0" style="font-size: 0.85rem;">Lote Interno #{{ $sample->id }}</p>
                </div>
                <div class="text-end">
                    <small class="text-muted fw-bold d-block small-label">RECEBIMENTO</small>
                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($sample->receipt_date)->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <small class="text-muted fw-bold d-block small-label">ORIGEM / FORNECEDOR</small>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold fs-6" style="color: #2c2c2c;">{{ $sample->origin->name ?? 'N/D' }}</span>
                        <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.55rem;">
                            {{ $sample->origin_type == 'App\Models\Farm' ? 'Fazenda' : ($sample->origin_type == 'App\Models\Producer' ? 'Produtor' : 'Empresa') }}
                        </span>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">ID Lote Forn: {{ $sample->supplier_batch }}</small>
                </div>
                <div class="col-md-3">
                    <small class="text-muted fw-bold d-block small-label">SAFRA</small>
                    <span class="fw-bold text-dark">{{ $sample->crop ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted fw-bold d-block small-label">ALTITUDE</small>
                    <span class="fw-bold text-dark">@if($sample->altitude) {{ $sample->altitude }}m @else - @endif</span>
                </div>
            </div>

            {{-- Grid Técnico Compacto --}}
            <div class="p-3 rounded-4" style="background-color: #f9f9f9; border: 1px solid #efefef;">
                <div class="row text-center g-2">
                    <div class="col border-end">
                        <small class="text-muted fw-bold d-block small-label">VARIEDADE</small>
                        <span class="fw-bold text-dark small">{{ $sample->variety }}</span>
                    </div>
                    <div class="col border-end">
                        <small class="text-muted fw-bold d-block small-label">PROCESSO</small>
                        <span class="badge rounded-pill text-dark fw-normal border px-2" style="background-color: var(--coffee-cream); font-size: 0.7rem;">
                            {{ $sample->drying_process }}
                        </span>
                    </div>
                    <div class="col border-end">
                        <small class="text-muted fw-bold d-block small-label">FERMENTAÇÃO</small>
                        <span class="text-dark small fw-bold">{{ $sample->fermentation ?? '-' }}</span>
                    </div>
                    <div class="col border-end">
                        <small class="text-muted fw-bold d-block small-label">UMIDADE</small>
                        <span class="fw-bold text-dark small">@if($sample->humidity) {{ number_format($sample->humidity, 1, ',', '.') }}% @else - @endif</span>
                    </div>
                    <div class="col">
                        <small class="text-muted fw-bold d-block small-label">PENEIRA</small>
                        <span class="fw-bold text-dark small">{{ $sample->screen ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Seção de Observações --}}
            <div class="mt-4">
                <small class="text-muted fw-bold d-block small-label mb-2">OBSERVAÇÕES</small>
                <div class="p-3 rounded-3 border bg-light shadow-sm" style="min-height: 80px;">
                    <p class="mb-0 text-dark" style="font-size: 0.85rem; line-height: 1.4;">
                        {{ $sample->observations ?? 'Nenhuma observação registrada para esta amostra.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- COLUNA DA DIREITA: Vínculos e Ações --}}
        <div class="col-md-4">
            <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 1px; font-size: 0.65rem;">Vínculos e Rastreabilidade</h6>
            
            <div class="mb-3">
                <small class="fw-bold text-muted small-label d-block mb-2">CLASSIFICAÇÕES (SCA)</small>
                @if(isset($sample->cuppingResults) && $sample->cuppingResults->count() > 0)
                    @foreach($sample->cuppingResults as $cupping)
                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border bg-white mb-1 shadow-sm">
                            <span class="fw-bold small" style="color: var(--coffee-brown);">Nota: {{ $cupping->score }}</span>
                            <small class="text-muted" style="font-size: 0.65rem;">{{ $cupping->created_at->format('d/m/Y') }}</small>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-2 border border-dashed rounded-3">
                        <p class="text-muted small mb-0" style="font-size: 0.7rem;">Nenhum cupping</p>
                    </div>
                @endif
            </div>

            {{-- NOVA SEÇÃO: PERFIS SENSORIAIS VINCULADOS --}}
            <div class="mb-3">
                <small class="fw-bold text-muted small-label d-block mb-2 text-uppercase">Perfis Sensoriais / Blends</small>
                @if($sample->sensoryProfileComponents->count() > 0)
                    @foreach($sample->sensoryProfileComponents as $component)
                        <div class="d-flex align-items-center p-2 rounded-3 border bg-white mb-1 shadow-sm border-start border-4" style="border-left-color: {{ $component->profile->color_hex }} !important;">
                            <div class="flex-grow-1">
                                <span class="fw-bold text-dark d-block" style="font-size: 0.75rem; line-height: 1;">{{ $component->profile->name }}</span>
                                <small class="text-muted" style="font-size: 0.6rem;">Uso: {{ number_format($component->percentage, 0) }}% do perfil</small>
                            </div>
                            <span class="badge {{ $component->profile->is_blend ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 0.5rem;">
                                {{ $component->profile->is_blend ? 'BLEND' : 'VARIETAL' }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-2 border border-dashed rounded-3">
                        <p class="text-muted small mb-0" style="font-size: 0.7rem;">Nenhum perfil vinculado</p>
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <small class="fw-bold text-muted small-label d-block mb-2">PERFIS DE TORRA (ARTISAN)</small>
                <div class="text-center py-2 border border-dashed rounded-3">
                    <p class="text-muted small mb-0" style="font-size: 0.7rem;">Aguardando vínculo</p>
                </div>
            </div>

            <div>
                <small class="fw-bold text-muted small-label d-block mb-2">LOTES GERADOS</small>
                <div class="text-center py-2 border border-dashed rounded-3">
                    <p class="text-muted small mb-0" style="font-size: 0.7rem;">Nenhum lote gerado</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer border-0 p-4 pt-0">
    <button type="button" class="btn w-100 py-2 fw-bold text-white shadow-sm" data-bs-dismiss="modal" style="background-color: #2c2c2c; border-radius: 10px; font-size: 0.9rem;">
        Fechar Visualização
    </button>
</div>

<style>
    /* Ajuste para telas de notebook */
    @media (min-width: 992px) {
        #modalViewSample .modal-dialog {
            max-width: 900px; /* Largura XL customizada para 15" */
        }
    }
    
    .small-label {
        font-size: 0.6rem;
        letter-spacing: 0.5px;
        color: #888 !important;
        margin-bottom: 2px;
    }

    #modalViewSample .modal-content {
        border-radius: 20px;
        overflow: hidden;
    }

    .border-dashed {
        border-style: dashed !important;
        border-width: 1px !important;
        background-color: #fafafa;
    }
</style>