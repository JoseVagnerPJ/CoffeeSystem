<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/monolith.min.css"/>

<style>
    /* Estilização para o seletor Pickr ocupar o espaço corretamente */
    .pickr { width: 100%; }
    .pickr .pcr-button {
        width: 100% !important;
        height: 45px !important;
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    /* Ajustes do Modal para o layout XL */
    #modalCreateProfile .modal-dialog { max-width: 950px; }
    
    /* Estilo Personalizado do Slider (Original Mantido) */
    .custom-coffee-range {
        -webkit-appearance: none;
        width: 100%;
        height: 10px;
        border-radius: 5px;
        background: #dee2e6; 
        outline: none; 
    }
    .custom-coffee-range::-webkit-slider-runnable-track {
        height: 10px;
        border-radius: 5px;
        background: #3d2b1f; 
    }
    .custom-coffee-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 30px; 
        height: 30px; 
        background-image: url("{{ asset('images/coffee_bean_icon.png') }}");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        background-color: transparent;
        cursor: pointer;
        margin-top: -10px; 
        border: none;
    }
    .custom-coffee-range::-moz-range-thumb {
        width: 30px;
        height: 30px;
        background-image: url("{{ asset('images/coffee_bean_icon.png') }}");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        background-color: transparent;
        cursor: pointer;
        border: none;
    }
    .custom-coffee-range:focus {
        outline: none;
        box-shadow: none;
    }
    .custom-coffee-range::-moz-focus-outer {
        border: 0;
    }
</style>

<div class="modal fade" id="modalCreateProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Criar Perfil Sensorial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('registries.sensory-profiles.store') }}" method="POST" id="formCreateProfile">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Cabeçalho do Perfil --}}
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted">Nome do Perfil</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ex: Frutado..." style="height: 45px; border-radius: 8px;">
                        </div>

                        {{-- SELETOR DE COR --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Cor Principal</label>
                            <div id="color-picker-container"></div>
                            <input type="hidden" name="color_hex" id="color_hex_hidden" value="#3d2b1f">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Blend?</label>
                            <div class="form-check form-switch mt-2 ms-2">
                                <input class="form-check-input" type="checkbox" name="is_blend" id="is_blend_switch" value="1" style="width: 50px; height: 25px;">
                            </div>
                        </div>
                        <div class="col-md-3">
                             <label class="form-label fw-bold small text-muted">Torra Padrão</label>
                            <select name="roast_intensity" class="form-select" style="height: 45px; border-radius: 8px;">
                                <option value="Clara">Clara</option>
                                <option value="Média-Clara">Média-Clara</option>
                                <option value="Média" selected>Média</option>
                                <option value="Média-Escura">Média-Escura</option>
                                <option value="Escura">Escura</option>
                            </select>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        {{-- COMPOSIÇÃO (AMOSTRAS) --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase mb-3" style="color: var(--coffee-brown); letter-spacing: 1px;">Composição do Perfil (Amostras)</label>
                            
                            <div id="components-container" class="bg-light p-3 rounded-3 border">
                                <div class="row g-2 mb-2 component-row align-items-center">
                                    <div class="col-auto action-container" style="min-width: 50px;">
                                    </div>

                                    <div class="col position-relative">
                                        <input type="text" class="form-control search-sample-input bg-white" placeholder="Buscar amostra (Nome ou Variedade)..." style="height: 45px; border-radius: 8px;">
                                        <input type="hidden" name="sample_id[]" class="sample-id-hidden" required>
                                        <div class="list-group position-absolute w-100 shadow-lg d-none search-results-container" style="z-index: 2000; top: 100%;"></div>
                                    </div>

                                    <div class="col-3 col-md-2">
                                        <div class="input-group">
                                            <input type="number" name="percentage[]" class="form-control percentage-input bg-white text-center fw-bold" value="100" step="0.01" min="0.01" max="100" required style="height: 45px;">
                                            <span class="input-group-text bg-white border-start-0">%</span>
                                        </div>
                                    </div>

                                    <div class="col-auto add-btn-container">
                                        <button type="button" class="btn btn-success btn-add-row d-none shadow-sm" style="height: 45px; width: 45px; border-radius: 8px;">
                                            <i class="bi bi-plus-lg fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="blend-error" class="text-danger fw-bold small mt-2 d-none"></div>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        {{-- ATRIBUTOS E NOTAS --}}
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted mb-4">Definição Sensorial (Intensidade 1 a 5)</label>
                            
                            <div class="row text-center g-4 px-3 p-4 rounded-3 border" style="background-color: #f8f9fa;">
                                @foreach(['sweetness' => 'Doçura', 'acidity' => 'Acidez', 'body' => 'Corpo', 'bitterness' => 'Amargor'] as $field => $label)
                                <div class="col-md-3 col-6 mb-2">
                                    <label for="range_{{ $field }}" class="d-block fw-bold text-dark mb-3" style="font-size: 0.9rem;">{{ $label }}</label>
                                    
                                    <div class="position-relative px-2">
                                        <input type="range" name="{{ $field }}" id="range_{{ $field }}" 
                                                class="form-range custom-coffee-range" 
                                                min="1" max="5" step="1" value="3">
                                        
                                        <div class="d-flex justify-content-between mt-2 fw-bold text-muted" style="font-size: 0.8rem; padding: 0 5px;">
                                            <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-4">
                             <label class="form-label fw-bold small text-muted mb-3">Notas Sensoriais (Tags)</label>
                             <textarea name="notes" class="form-control" rows="8" placeholder="Ex: Frutado, Caramelo, Nozes, Floral..." style="border-radius: 8px; resize: none;"></textarea>
                             <small class="text-muted">Separe por vírgulas.</small>
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

<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>

<script>
$(document).ready(function() {
    // 0. Inicialização do Seletor de Cor Corrigida
    const pickr = Pickr.create({
        el: '#color-picker-container',
        theme: 'monolith',
        default: '#3d2b1f',
        swatches: [
            '#3d2b1f', '#6f4e37', '#b87333', '#d2691e', '#8b4513', '#a0522d',
            '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF'
        ],
        components: {
            preview: true,
            opacity: false,
            hue: true,
            interaction: {
                hex: true,
                input: true,
                save: true
            }
        },
        i18n: { 'ui:save': 'Confirmar Cor' }
    });

    // Função central para atualizar cor (Ajustado para não fechar sozinho)
    function updateColor(color) {
        const hex = color.toHEXA().toString();
        $('#color_hex_hidden').val(hex);
        // pickr.applyColor() removido daqui para evitar fechar no 'change'
    }

    // Eventos do Pickr
    pickr.on('change', (color) => updateColor(color));
    pickr.on('swatchselect', (color) => updateColor(color));
    pickr.on('save', (color) => {
        if (color) updateColor(color);
        pickr.hide(); // Só fecha agora quando apertar o botão de salvar/confirmar
    });

    // 1. Alternar modo Blend
    $('#is_blend_switch').change(function() {
        if ($(this).is(':checked')) {
            $('.btn-add-row').removeClass('d-none');
            $('.percentage-input').val('').prop('readonly', false);
        } else {
            $('#components-container .component-row:not(:first)').remove();
            $('.btn-add-row').addClass('d-none');
            $('.percentage-input').val('100').prop('readonly', true);
            $('#blend-error').addClass('d-none');
        }
    });

    // 2. Adicionar Nova Linha
    $(document).on('click', '.btn-add-row', function() {
        let newRow = $('.component-row:first').clone();
        newRow.find('input').val('');
        newRow.find('.sample-id-hidden').val('');
        newRow.find('.add-btn-container').empty();

        let deleteBtnHtml = `
            <button type="button" class="btn btn-outline-danger w-100 btn-remove-row shadow-sm" style="height: 45px; border-radius: 8px;">
                <i class="bi bi-trash-fill"></i>
            </button>`;
        newRow.find('.action-container').html(deleteBtnHtml);

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
            $.get("{{ route('registries.sensory-profiles.search-samples') }}", { q: query }, function(data) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        html += `<button type="button" class="list-group-item list-group-item-action select-sample-item border-0 border-bottom py-2" 
                                    data-id="${item.id}" data-name="${item.name}">
                                    <div class="fw-bold text-dark">#${item.id} - ${item.name}</div>
                                    <small class="text-muted">${item.variety}</small>
                                 </button>`;
                    });
                } else {
                    html = '<div class="list-group-item text-muted small">Nenhuma amostra encontrada.</div>';
                }
                container.html(html).removeClass('d-none');
            });
        }, 300);
    });

    $(document).on('click', '.select-sample-item', function(e) {
        e.preventDefault();
        let row = $(this).closest('.component-row');
        row.find('.search-sample-input').val($(this).data('name'));
        row.find('.sample-id-hidden').val($(this).data('id'));
        $(this).closest('.search-results-container').addClass('d-none');
    });

    // 4. Validação da soma 100%
    $('#formCreateProfile').submit(function(e) {
        let total = 0;
        $('.percentage-input').each(function() {
            let val = parseFloat($(this).val());
            total += isNaN(val) ? 0 : val;
        });
        
        if (Math.abs(total - 100) > 0.01) {
            e.preventDefault();
            $('#blend-error').removeClass('d-none').html(`<i class="bi bi-exclamation-triangle-fill me-1"></i> Soma: <strong>${total.toFixed(2)}%</strong>. O total deve ser 100%.`);
        }
    });
});
</script>