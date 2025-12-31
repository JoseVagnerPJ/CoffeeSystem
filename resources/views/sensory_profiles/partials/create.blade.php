<div class="modal fade" id="modalCreateProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Criar Perfil Sensorial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('sensory-profiles.store') }}" method="POST" id="formCreateProfile">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Cabeçalho do Perfil --}}
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted">Nome do Perfil</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ex: Frutado Especial" style="height: 45px; border-radius: 8px;">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Cor</label>
                            <input type="color" name="color_hex" class="form-control form-control-color w-100" value="#3d2b1f" style="height: 45px; border-radius: 8px;">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Blend?</label>
                            <div class="form-check form-switch mt-2 ms-2">
                                <input class="form-check-input" type="checkbox" name="is_blend" id="is_blend_switch" value="1" style="width: 50px; height: 25px;">
                            </div>
                        </div>
                        <div class="col-md-3">
                             <label class="form-label fw-bold small text-muted">Torra</label>
                            <select name="roast_intensity" class="form-select" style="height: 45px; border-radius: 8px;">
                                <option value="Clara">Clara</option>
                                <option value="Média-Clara">Média-Clara</option>
                                <option value="Média">Média</option>
                                <option value="Média-Escura">Média-Escura</option>
                                <option value="Escura">Escura</option>
                            </select>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        {{-- COMPOSIÇÃO (AMOSTRAS) --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase mb-3" style="color: var(--coffee-brown);">Composição (Amostras)</label>
                            <div id="components-container" class="bg-light p-3 rounded-3 border">
                                {{-- Linha de Amostra --}}
                                <div class="row g-2 mb-2 component-row align-items-center">
                                    <div class="col-auto action-container" style="min-width: 50px;"></div>
                                    <div class="col position-relative">
                                        <input type="text" class="form-control search-sample-input bg-white" placeholder="Buscar amostra..." autocomplete="off" style="height: 45px; border-radius: 8px;">
                                        <input type="hidden" name="sample_id[]" class="sample-id-hidden" required>
                                        <div class="list-group position-absolute w-100 shadow-lg d-none search-results-container" style="z-index: 9999; top: 100%; background: white;"></div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="input-group">
                                            <input type="number" name="percentage[]" class="form-control percentage-input bg-white text-center fw-bold" value="100" step="0.01" min="0.01" max="100" required style="height: 45px;">
                                            <span class="input-group-text bg-white border-start-0">%</span>
                                        </div>
                                    </div>
                                    <div class="col-auto add-btn-container">
                                        <button type="button" class="btn btn-success btn-add-row d-none" style="height: 45px; width: 45px; border-radius: 8px;"><i class="bi bi-plus-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="blend-error" class="text-danger fw-bold small mt-2 d-none"></div>
                        </div>

                        {{-- ATRIBUTOS SENSORIAIS --}}
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted mb-4">Definição Sensorial (Intensidade 1 a 5)</label>
                            <div class="row text-center g-4 px-3 p-4 rounded-3 border" style="background-color: #f8f9fa;">
                                @foreach(['sweetness' => 'Doçura', 'acidity' => 'Acidez', 'body' => 'Corpo', 'bitterness' => 'Amargor'] as $field => $label)
                                <div class="col-md-3 col-6 mb-2">
                                    <label class="d-block fw-bold text-dark mb-3">{{ $label }}</label>
                                    <div class="px-2">
                                        <input type="range" name="{{ $field }}" class="form-range custom-coffee-range" min="1" max="5" step="1" value="3">
                                        <div class="d-flex justify-content-between mt-2 fw-bold text-muted small">
                                            <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-4">
                             <label class="form-label fw-bold small text-muted mb-3">Notas (Tags)</label>
                             <textarea name="notes" class="form-control" rows="8" placeholder="Ex: Frutado, Caramelo, Nozes, Floral..." style="border-radius: 8px; resize: none;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light btn-lg px-5" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-lg text-white px-5 shadow-sm" style="background-color: #3d2b1f; border-radius: 10px;">
                        <i class="bi bi-check-lg me-2"></i> Salvar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Estilo do Autocomplete */
    .search-results-container { max-height: 250px; overflow-y: auto; border: 1px solid #ddd; z-index: 9999 !important; border-radius: 0 0 8px 8px; }
    .select-sample-item { cursor: pointer; border-bottom: 1px solid #eee; }
    .select-sample-item:hover { background-color: #f8f9fa; }

    /* Estilo do Slider (Range) Personalizado */
    .custom-coffee-range {
        -webkit-appearance: none;
        width: 100%;
        height: 8px; /* Altura da linha um pouco menor para precisão */
        border-radius: 5px;
        background: #dee2e6;
        outline: none;
        margin: 15px 0;
    }

    /* Linha que atravessa o grão (Chrome/Safari/Edge) */
    .custom-coffee-range::-webkit-slider-runnable-track {
        height: 8px;
        background: #dee2e6;
        border-radius: 5px;
    }

    /* O Grão de Café (Thumb) */
    .custom-coffee-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 32px;
        height: 32px;
        background-image: url("{{ asset('images/coffee_bean_icon.png') }}");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        background-color: transparent;
        cursor: pointer;
        border: none;
        /* Cálculo: (Track Height / 2) - (Thumb Height / 2) */
        /* (8 / 2) - (32 / 2) = 4 - 16 = -12px */
        margin-top: -12px; 
        transition: transform 0.2s;
    }

    .custom-coffee-range:active::-webkit-slider-thumb {
        transform: scale(1.1);
    }

    /* Suporte para Firefox */
    .custom-coffee-range::-moz-range-thumb {
        width: 32px;
        height: 32px;
        background-image: url("{{ asset('images/coffee_bean_icon.png') }}");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        background-color: transparent;
        cursor: pointer;
        border: none;
    }
    
    .custom-coffee-range::-moz-range-track {
        height: 8px;
        background: #dee2e6;
        border-radius: 5px;
    }
</style>

<script>
$(document).ready(function() {
    // 1. Alternar modo Blend
    $('#is_blend_switch').change(function() {
        const isChecked = $(this).is(':checked');
        $('.btn-add-row').toggleClass('d-none', !isChecked);
        const firstInput = $('.percentage-input').first();
        if (isChecked) { 
            firstInput.val('').prop('readonly', false); 
        } else { 
            $('#components-container .component-row:not(:first)').remove(); 
            firstInput.val('100').prop('readonly', true); 
            $('#blend-error').addClass('d-none');
        }
    });

    // 2. Adicionar Nova Linha
    $(document).on('click', '.btn-add-row', function() {
        let newRow = $('.component-row:first').clone();
        newRow.find('input').val(''); 
        newRow.find('.search-results-container').addClass('d-none').html('');
        newRow.find('.add-btn-container').empty(); 
        newRow.find('.action-container').html('<button type="button" class="btn btn-outline-danger w-100 btn-remove-row" style="height: 45px; border-radius: 8px;"><i class="bi bi-trash-fill"></i></button>');
        $('#components-container').append(newRow);
    });

    $(document).on('click', '.btn-remove-row', function() { $(this).closest('.row').remove(); });

    // 3. Busca Dinâmica de Amostras
    let searchTimeout;
    $(document).on('input', '.search-sample-input', function() {
        let input = $(this); 
        let query = input.val(); 
        let container = input.siblings('.search-results-container');
        
        clearTimeout(searchTimeout);
        if (query.length < 2) { container.addClass('d-none'); return; }

        searchTimeout = setTimeout(() => {
            $.get("{{ route('sensory-profiles.search-samples') }}", { q: query }, function(data) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        html += `
                        <div class="list-group-item list-group-item-action select-sample-item p-3" data-id="${item.id}" data-name="${item.name}">
                            <div class="fw-bold">#${item.id} - ${item.name}</div>
                            <small class="text-muted">${item.variety} | Lote: ${item.supplier_batch ?? 'N/A'}</small>
                        </div>`;
                    });
                } else { html = '<div class="list-group-item text-muted small text-center">Nenhuma amostra ativa encontrada.</div>'; }
                container.html(html).removeClass('d-none');
            });
        }, 300);
    });

    // 4. Seleção da Amostra
    $(document).on('click', '.select-sample-item', function() {
        let row = $(this).closest('.component-row');
        row.find('.search-sample-input').val($(this).data('name'));
        row.find('.sample-id-hidden').val($(this).data('id')); 
        $(this).closest('.search-results-container').addClass('d-none');
    });

    // Fechar resultados ao clicar fora
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('.search-results-container').addClass('d-none');
        }
    });

    // 5. Validação da soma (Blend)
    $('#formCreateProfile').submit(function(e) {
        if ($('#is_blend_switch').is(':checked')) {
            let total = 0;
            $('.percentage-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            if (Math.abs(total - 100) > 0.01) {
                e.preventDefault();
                $('#blend-error').removeClass('d-none').html(`<i class="bi bi-exclamation-triangle-fill me-1"></i> A soma deve ser exatamente 100% (Atual: ${total.toFixed(2)}%)`);
                return false;
            }
        }
    });
});
</script>