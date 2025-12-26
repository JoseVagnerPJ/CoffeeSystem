<div class="modal-header border-0 pb-0">
    <h5 class="modal-title fw-bold d-flex align-items-center" style="color: var(--coffee-brown); font-size: 1.25rem;">
        <i class="bi bi-cup-hot me-2"></i>
        Dados da Amostra
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    {{-- Seção Principal: Informações do Cadastro --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1" style="color: #2c2c2c; font-size: 1.5rem;">
            {{ $sample->name }}
        </h2>
        <p class="text-muted mb-4" style="font-weight: 500;">Lote Interno #{{ str_pad($sample->id, 4, '0', STR_PAD_LEFT) }}</p>

        <div class="row g-3 mb-3">
            {{-- Origem --}}
            <div class="col-md-7">
                <small class="text-muted fw-bold d-block small-label">ORIGEM / FORNECEDOR</small>
                <div class="d-flex align-items-center">
                    <span class="fw-bold me-2 fs-5" style="color: #2c2c2c;">{{ $sample->origin->name ?? 'Não identificado' }}</span>
                    <small class="text-muted text-uppercase badge bg-light text-dark border" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                        @if($sample->origin_type == 'App\Models\Farm') FAZENDA
                        @elseif($sample->origin_type == 'App\Models\Producer') PRODUTOR
                        @else EMPRESA @endif
                    </small>
                </div>
                <small class="text-muted" style="font-size: 0.8rem;">ID Lote Forn: {{ $sample->supplier_batch }}</small>
            </div>
            {{-- Data e Safra --}}
            <div class="col-md-5">
                 <div class="row">
                    <div class="col-6">
                        <small class="text-muted fw-bold d-block small-label">RECEBIMENTO</small>
                        <span class="fw-bold" style="color: #2c2c2c;">
                            {{ \Carbon\Carbon::parse($sample->receipt_date)->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted fw-bold d-block small-label">SAFRA</small>
                        <span class="fw-bold" style="color: #2c2c2c;">{{ $sample->crop ?? '-' }}</span>
                    </div>
                 </div>
            </div>
        </div>

        {{-- Dados Técnicos --}}
        <div class="p-3 rounded-3 bg-light" style="border: 1px solid #eee;">
            <div class="row g-3">
                <div class="col-md-4 col-6">
                    <small class="text-muted fw-bold d-block small-label">VARIEDADE</small>
                    <span class="fw-bold text-dark">{{ $sample->variety }}</span>
                </div>
                <div class="col-md-4 col-6">
                    <small class="text-muted fw-bold d-block small-label">PROCESSO</small>
                    <span class="badge rounded-pill text-dark fw-normal border px-3" style="background-color: var(--coffee-cream);">
                        {{ $sample->drying_process }}
                    </span>
                </div>
                <div class="col-md-4 col-6">
                    <small class="text-muted fw-bold d-block small-label">FERMENTAÇÃO</small>
                    <span class="text-dark">{{ $sample->fermentation ?? '-' }}</span>
                </div>
                <div class="col-md-4 col-6">
                    <small class="text-muted fw-bold d-block small-label">UMIDADE</small>
                    <span class="fw-bold text-dark">
                        @if($sample->humidity) {{ number_format($sample->humidity, 1, ',', '.') }}% @else - @endif
                    </span>
                </div>
                 <div class="col-md-4 col-6">
                    <small class="text-muted fw-bold d-block small-label">PENEIRA</small>
                    <span class="text-dark">{{ $sample->screen ?? '-' }}</span>
                </div>
                <div class="col-md-4 col-6">
                    <small class="text-muted fw-bold d-block small-label">ALTITUDE</small>
                    <span class="text-dark">@if($sample->altitude) {{ $sample->altitude }}m @else - @endif</span>
                </div>
            </div>
        </div>
        
        @if($sample->observations)
        <div class="mt-3 ps-2 border-start border-3" style="border-color: var(--coffee-cream) !important;">
             <small class="text-muted fw-bold d-block small-label">OBSERVAÇÕES</small>
             <p class="mb-0 small fst-italic text-muted">{{ $sample->observations }}</p>
        </div>
        @endif
    </div>

    {{-- Seção Secundária: Vínculos Registrados --}}
    <div>
        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-3" style="letter-spacing: 1px; font-size: 0.75rem;">
            VÍNCULOS REGISTRADOS
        </h6>

        {{-- Classificações (Exemplo de como ficará) --}}
        <div class="mb-3">
            <small class="text-muted fw-bold d-block small-label mb-2">CLASSIFICAÇÕES (SCA)</small>
            {{-- Substitua este bloco pelo @forelse real quando tiver a relação --}}
            @if(isset($sample->cuppingResults) && $sample->cuppingResults->count() > 0)
                @foreach($sample->cuppingResults as $cupping)
                    <div class="bg-light rounded-3 p-2 mb-1 d-flex justify-content-between align-items-center border">
                        <div>
                            <i class="bi bi-cup-hot-fill me-2" style="color: var(--coffee-brown);"></i>
                            <span class="fw-bold">Pontuação: {{ $cupping->score }} pontos</span>
                        </div>
                        <small class="text-muted">{{ $cupping->created_at->format('d/m/Y') }}</small>
                    </div>
                @endforeach
            @else
                <p class="text-muted small fst-italic ps-1 mb-0">Nenhuma classificação registrada.</p>
            @endif
        </div>

        {{-- Perfis de Torra (Exemplo) --}}
        <div class="mb-3">
            <small class="text-muted fw-bold d-block small-label mb-2">PERFIS DE TORRA (ARTISAN)</small>
             @if(isset($sample->roastProfiles) && $sample->roastProfiles->count() > 0)
                @foreach($sample->roastProfiles as $profile)
                    <div class="bg-light rounded-3 p-2 mb-1 border">
                        <i class="bi bi-fire me-2" style="color: var(--coffee-brown);"></i>
                        Perfil: <strong>{{ $profile->name }}</strong>
                    </div>
                @endforeach
            @else
                <p class="text-muted small fst-italic ps-1 mb-0">Nenhum perfil de torra vinculado.</p>
            @endif
        </div>

        {{-- Lotes Verdes (Exemplo) --}}
        <div>
            <small class="text-muted fw-bold d-block small-label mb-2">LOTES VERDES GERADOS</small>
             @if(isset($sample->greenLots) && $sample->greenLots->count() > 0)
                @foreach($sample->greenLots as $lot)
                    <div class="bg-light rounded-3 p-2 mb-1 border">
                        <i class="bi bi-box-seam-fill me-2" style="color: var(--coffee-brown);"></i>
                        Lote: <strong>{{ $lot->lot_number }}</strong>
                    </div>
                @endforeach
            @else
                <p class="text-muted small fst-italic ps-1 mb-0">Nenhum lote verde gerado a partir desta amostra.</p>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer border-0 p-4 pt-0">
    <button type="button" class="btn w-100 py-2 fw-bold text-white" data-bs-dismiss="modal" style="background-color: #2c2c2c; border-radius: 8px; font-size: 1rem;">
        Fechar
    </button>
</div>

<style>
    .small-label {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
        color: #6c757d; /* text-muted bootstrap color */
    }
    /* Garante que o modal tenha bordas arredondadas como na referência */
    #modalViewSample .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }
    .modal-header .btn-close {
        padding: 0.5rem;
        margin: -0.5rem -0.5rem -0.5rem auto;
    }
</style>