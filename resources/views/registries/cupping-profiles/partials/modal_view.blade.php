<div class="modal fade" id="modalViewProfile{{ $profile->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 ps-4 pt-4">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">
                    <i class="bi bi-search me-2"></i>Detalhes do Protocolo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body px-4">
                <div class="mb-4">
                    <h6 class="text-uppercase small fw-bold text-muted mb-1">Nome do Perfil</h6>
                    <p class="h5 fw-bold text-dark">{{ $profile->name }}</p>
                    @if($profile->description)
                        <p class="text-muted small mb-0">{{ $profile->description }}</p>
                    @endif
                </div>

                <div class="row g-3">
                    {{-- Coluna Atributos SCA --}}
                    <div class="col-md-12">
                        <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary small">
                            <i class="bi bi-check2-square me-2"></i>Atributos Ativos
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $fields = [
                                    'eval_fragrance' => 'Aroma', 'eval_flavor' => 'Sabor',
                                    'eval_aftertaste' => 'Retrogosto', 'eval_acidity' => 'Acidez',
                                    'eval_body' => 'Corpo', 'eval_balance' => 'Equilíbrio',
                                    'eval_uniformity' => 'Uniformidade', 'eval_clean_cup' => 'Limpeza',
                                    'eval_sweetness' => 'Doçura', 'eval_overall' => 'Geral',
                                    'eval_defects' => 'Defeitos'
                                ];
                            @endphp

                            @foreach($fields as $key => $label)
                                <div class="badge {{ $profile->$key ? 'bg-success-subtle text-success border-success' : 'bg-light text-muted border-secondary-subtle' }} border px-3 py-2 fw-medium" style="font-size: 0.75rem;">
                                    <i class="bi {{ $profile->$key ? 'bi-check-lg' : 'bi-x' }} me-1"></i>
                                    {{ $label }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Coluna Configurações Técnicas --}}
                    <div class="col-md-12 mt-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2 text-success small">
                            <i class="bi bi-sliders me-2"></i>Configurações de Escala
                        </h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                Nível de Torra
                                <i class="bi {{ $profile->show_roast_level ? 'bi-toggle-on text-success fs-5' : 'bi-toggle-off text-muted fs-5' }}"></i>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                Réguas de Aroma (Seco/Quebra)
                                <i class="bi {{ $profile->show_fragrance_scales ? 'bi-toggle-on text-success fs-5' : 'bi-toggle-off text-muted fs-5' }}"></i>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                Intensidade de Acidez/Corpo
                                <i class="bi {{ $profile->show_intensity_scales ? 'bi-toggle-on text-success fs-5' : 'bi-toggle-off text-muted fs-5' }}"></i>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                Detalhamento de Defeitos
                                <i class="bi {{ $profile->show_defect_details ? 'bi-toggle-on text-success fs-5' : 'bi-toggle-off text-muted fs-5' }}"></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-secondary fw-bold w-100 py-2" style="border-radius: 10px;" data-bs-dismiss="modal">Fechar Visualização</button>
            </div>
        </div>
    </div>
</div>