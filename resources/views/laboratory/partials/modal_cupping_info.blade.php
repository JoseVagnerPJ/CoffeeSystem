<div class="modal-content border-0 shadow-lg" style="background: #fdfaf5; min-height: 80vh;"> {{-- Aumentado o comprimento mínimo --}}
    <div class="modal-header bg-dark text-white py-3">
        <div class="d-flex flex-column">
            <h4 class="modal-title fw-bold">Xícara #{{ $item->position }} — {{ $item->nickname }}</h4>
            <span class="opacity-75 fs-6">
                <i class="bi bi-info-circle me-1"></i>
                {{ $item->is_avulso ? ($item->variety ?? 'Variedade não informada') : (optional($item->sample)->variety ?? 'N/A') }}
            </span>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body p-4">
        @php 
            $avgSweetness = $item->evaluations->avg('sweetness') ?? 0;
            $avgAcidity = $item->evaluations->avg('acidity') ?? 0;
            $avgBody = $item->evaluations->avg('body') ?? 0;
            $avgAftertaste = $item->evaluations->avg('aftertaste') ?? 0;
            $finalSca = 60 + ($avgSweetness + $avgAcidity + $avgBody + $avgAftertaste); 
        @endphp

        {{-- 1. DASHBOARD DE MÉDIAS (TOP) --}}
        <div class="row g-3 mb-5">
            <div class="col-md-12">
                <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing: 1px;">Desempenho Médio na Mesa</h6>
                <div class="row g-2">
                    @foreach(['Doçura' => $avgSweetness, 'Acidez' => $avgAcidity, 'Corpo' => $avgBody, 'Retrogosto' => $avgAftertaste] as $label => $val)
                        <div class="col">
                            <div class="card border-0 shadow-sm text-center p-3" style="background: white; border-bottom: 4px solid var(--coffee-gold) !important;">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">{{ $label }}</small>
                                <span class="h4 fw-bold mb-0" style="color: var(--coffee-brown);">{{ number_format($val, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center p-3 text-white" style="background: var(--coffee-brown);">
                            <small class="text-uppercase fw-bold" style="font-size: 0.65rem; color: var(--coffee-gold);">Pontuação SCA Média</small>
                            <span class="h3 fw-bold mb-0">{{ number_format($finalSca, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            {{-- 2. FICHA TÉCNICA (ESQUERDA) --}}
            <div class="col-lg-7">
                <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing: 1px;">Ficha Técnica da Amostra</h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="card card-body p-3 border-0 shadow-sm bg-white">
                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.6rem;">Origem / Fornecedor</small>
                            <span class="fw-bold text-dark fs-5">
                                <i class="bi bi-geo-alt me-1 text-primary"></i>
                                @if($item->is_avulso)
                                    Avulsa (Informação Manual)
                                @else
                                    {{ $item->sample->origin->name ?? 'Não vinculado' }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @php 
                        $specs = [
                            'Processo'    => $item->drying_process,
                            'Fermentação' => $item->fermentation ?: 'Natural',
                            'Altitude'    => $item->altitude ? $item->altitude . 'm' : 'N/A',
                            'Safra'       => $item->crop,
                            'Umidade'     => $item->humidity ? $item->humidity . '%' : 'N/A',
                            'Peneira'     => $item->screen
                        ];
                    @endphp
                    @foreach($specs as $label => $val)
                        <div class="col-6 col-md-4">
                            <div class="card card-body p-3 border-0 shadow-sm bg-white">
                                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.6rem;">{{ $label }}</small>
                                <span class="fw-bold text-dark">{{ $val ?: '—' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- OBSERVAÇÕES PRÉ-PROVA --}}
                <div class="card border-0 shadow-sm p-4 mt-3 bg-white" style="border-radius: 15px;">
                    <h6 class="fw-bold text-muted small text-uppercase mb-2">
                        <i class="bi bi-journal-text me-2"></i>Observações do Laboratório (Pré-Prova)
                    </h6>
                    <p class="mb-0 text-dark fs-6 italic">
                        "{{ $item->notes ?: 'Nenhuma observação técnica registrada para esta amostra.' }}"
                    </p>
                </div>
            </div>

            {{-- 3. NOTAS SENSORIAIS CONSOLIDADAS (DIREITA) --}}
            <div class="col-lg-5">
                <h6 class="fw-bold text-muted small text-uppercase mb-3" style="letter-spacing: 1px;">Resumo Sensorial da Equipe</h6>
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 15px;">
                    <div class="d-flex flex-wrap gap-2">
                        @php $allTags = $item->evaluations->flatMap->tags->unique('id'); @endphp
                        @forelse($allTags as $tag)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
                                <i class="bi bi-tag-fill me-1"></i>{{ $tag->name }}
                            </span>
                        @empty
                            <div class="text-center w-100 py-4">
                                <i class="bi bi-tags text-muted fs-2"></i>
                                <p class="text-muted small mt-2">Nenhum descritor sensorial marcado.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. AVALIAÇÕES INDIVIDUAIS (ESTENDIDAS ATÉ O FINAL) --}}
        <div class="mt-5">
            <h6 class="fw-bold text-muted small text-uppercase mb-4" style="letter-spacing: 1px;">Relatório Detalhado por Provador</h6>
            <div class="row g-4">
                @foreach($item->evaluations as $eval)
                    <div class="col-12"> {{-- Card agora ocupa a largura toda para dar espaço às observações --}}
                        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle fs-4 text-muted me-2"></i>
                                    <span class="fw-bold text-dark fs-5">{{ $eval->user->name }}</span>
                                </div>
                                <span class="badge rounded-pill px-4 py-2 fs-6" style="background: var(--coffee-brown); color: var(--coffee-gold);">
                                    {{ number_format(60 + $eval->acidity + $eval->body + $eval->sweetness + $eval->aftertaste, 2) }} SCA
                                </span>
                            </div>
                            <div class="card-body bg-white pt-0">
                                <div class="row align-items-start">
                                    <div class="col-md-5">
                                        <div class="row g-2 mb-3 text-center">
                                            @foreach(['Acidez' => 'acidity', 'Corpo' => 'body', 'Doçura' => 'sweetness', 'Retrogosto' => 'aftertaste'] as $label => $attr)
                                                <div class="col-3">
                                                    <div class="p-2 rounded border bg-light">
                                                        <small class="text-muted d-block fw-bold" style="font-size: 0.55rem;">{{ strtoupper($label) }}</small>
                                                        <span class="fw-bold text-primary">{{ number_format($eval->$attr, 1) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 0.6rem;">Descritores:</small>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($eval->tags as $tag)
                                                <span class="badge bg-light text-dark border border-secondary-subtle fw-normal">#{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    {{-- CAMPO DE OBSERVAÇÕES TÉCNICAS DA PROVA --}}
                                    <div class="col-md-7">
                                        <div class="p-3 rounded-3 h-100" style="background: #fdfaf5; border-left: 4px solid #e3d5ca;">
                                            <small class="d-block fw-bold text-muted text-uppercase mb-2" style="font-size: 0.55rem;">Observações Técnicas (Durante a Prova):</small>
                                            <p class="text-dark mb-0" style="font-size: 0.95rem; line-height: 1.5;">
                                                <i class="bi bi-chat-left-quote text-muted me-2"></i>
                                                {{ $eval->observations ?: 'Nenhuma observação adicional registrada por este provador.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilo para garantir que o modal XL seja realmente largo em telas grandes */
    @media (min-width: 1200px) {
        .modal-xl { max-width: 1200px; }
    }
    .italic { font-style: italic; }
</style>