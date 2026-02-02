<div class="modal fade" id="modalAddProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 ps-4 pt-4">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">
                    <i class="bi bi-award me-2"></i>Configurar Novo Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('registries.cupping-profiles.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">NOME DO PROTOCOLO *</label>
                            <input type="text" name="name" class="form-control bg-light border-0 py-2 fw-bold" placeholder="Ex: Protocolo SCA Completo" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">DESCRIÇÃO BREVE</label>
                            <textarea name="description" class="form-control bg-light border-0" rows="2" placeholder="Explique para que serve este perfil..."></textarea>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">
                        <i class="bi bi-check2-square me-2"></i>Atributos SCA para Avaliação
                    </h6>
                    <div class="row g-3 mb-4">
                        @php
                            $fields = [
                                'eval_fragrance' => 'Fragrância/Aroma', 'eval_flavor' => 'Sabor',
                                'eval_aftertaste' => 'Retrogosto', 'eval_acidity' => 'Acidez',
                                'eval_body' => 'Corpo', 'eval_balance' => 'Equilíbrio',
                                'eval_uniformity' => 'Uniformidade', 'eval_clean_cup' => 'Xícara Limpa',
                                'eval_sweetness' => 'Doçura', 'eval_overall' => 'Geral',
                                'eval_defects' => 'Defeitos (Negativo)'
                            ];
                        @endphp
                        @foreach($fields as $key => $label)
                        <div class="col-md-4 col-6">
                            <div class="form-check form-switch p-2 border rounded bg-white shadow-xs">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="{{ $key }}" id="{{ $key }}" value="1" checked>
                                <label class="form-check-label small fw-medium" for="{{ $key }}">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-success">
                        <i class="bi bi-sliders me-2"></i>Detalhes Técnicos (Escalas Visuais)
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch p-2 border rounded bg-white shadow-xs">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="show_roast_level" id="show_roast_level" value="1" checked>
                                <label class="form-check-label small fw-medium" for="show_roast_level">Habilitar Nível de Torra</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch p-2 border rounded bg-white shadow-xs">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="show_fragrance_scales" id="show_fragrance_scales" value="1" checked>
                                <label class="form-check-label small fw-medium" for="show_fragrance_scales">Réguas de Aroma (Seco/Quebra)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch p-2 border rounded bg-white shadow-xs">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="show_intensity_scales" id="show_intensity_scales" value="1" checked>
                                <label class="form-check-label small fw-medium" for="show_intensity_scales">Réguas de Intensidade (Acidez/Corpo)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch p-2 border rounded bg-white shadow-xs">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="show_defect_details" id="show_defect_details" value="1" checked>
                                <label class="form-check-label small fw-medium" for="show_defect_details">Cálculo de Defeitos Detalhado</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 bg-light-subtle" style="border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-outline-dark fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm" style="background-color: var(--coffee-brown);">
                        <i class="bi bi-save me-2"></i>Gravar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>