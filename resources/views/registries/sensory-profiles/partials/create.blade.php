<style>
    /* 1. ESTILIZAÇÃO DO CONTAINER E DO POPUP */
    .range-wrapper {
        position: relative;
        padding: 25px 0 10px 0; /* Espaço para o popup aparecer em cima */
    }

    .range-value-popup {
        position: absolute;
        top: -10px;
        background: #3d2b1f;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
        transform: translateX(-50%);
        pointer-events: none;
        display: none; /* Escondido por padrão, aparece no drag */
        z-index: 100;
    }

    /* Triângulo do popup */
    .range-value-popup::after {
        content: "";
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid #3d2b1f;
    }

    /* 2. ESTILIZAÇÃO DO SLIDER */
    .custom-coffee-range {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        background: transparent;
        cursor: pointer;
        height: 40px;
        display: block;
        margin: 0;
    }

    .custom-coffee-range:focus { outline: none; }

    /* TRILHA (TRACK) - Linha fina com bolinhas alinhadas */
    .custom-coffee-range::-webkit-slider-runnable-track {
        width: 100%;
        height: 2px;
        cursor: pointer;
        background: #adb5bd;
        background-image: radial-gradient(circle, #adb5bd 5px, transparent 5px);
        background-size: 25% 100%; /* 25% para 5 pontos exatos */
        background-repeat: repeat-x;
        background-position: 0% center;
        border-radius: 2px;
    }

    /* GRÃO DE CAFÉ (THUMB) */
    .custom-coffee-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 30px;
        width: 30px;
        background: url('/images/coffee_bean_icon.png') no-repeat center;
        background-size: contain;
        cursor: pointer;
        margin-top: -17px; /* Alinhamento vertical central */
        border: none;
        transition: transform 0.1s ease;
        z-index: 10;
        position: relative;
    }

    .custom-coffee-range:active::-webkit-slider-thumb { transform: scale(1.15); }

    /* COMPENSAÇÃO PARA FIREFOX */
    .custom-coffee-range::-moz-range-track {
        height: 2px;
        background: #adb5bd;
        background-image: radial-gradient(circle, #adb5bd 5px, transparent 5px);
        background-size: 25% 100%;
        background-repeat: repeat-x;
    }
    .custom-coffee-range::-moz-range-thumb {
        height: 36px;
        width: 36px;
        background: url('/images/coffee-bean-icon.png') no-repeat center;
        background-size: contain;
        border: none;
    }

    /* 3. ALINHAMENTO DOS NÚMEROS (Labels) */
    .range-labels {
        padding: 0 18px; /* Metade da largura do grão (36px/2) */
        margin-top: -8px;
        display: flex;
        justify-content: space-between;
    }
    
    .range-labels span {
        font-weight: bold;
        color: #6c757d;
        font-size: 14px;
        transition: color 0.2s;
    }

    .range-labels span.active { color: #3d2b1f; }
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
                            <label class="form-label fw-bold small text-muted mb-4">Definição Sensorial</label>
                            <div class="row text-center g-4 px-3 p-4 rounded-3 border" style="background-color: #f8f9fa;">
                                @foreach(['sweetness' => 'Doçura', 'acidity' => 'Acidez', 'body' => 'Corpo', 'bitterness' => 'Amargor'] as $field => $label)
                                <div class="col-md-3 col-6 mb-2">
                                    <label class="d-block fw-bold text-dark mb-0 small">{{ $label }}</label>
                                    <div class="range-wrapper">
                                        <div class="range-value-popup" id="popup-{{ $field }}">3</div>
                                        <input type="range" name="{{ $field }}" class="custom-coffee-range" min="1" max="5" step="1" value="3" 
                                               oninput="updateRangePopup(this, '{{ $field }}')" 
                                               onmousedown="showPopup('{{ $field }}')" 
                                               onmouseup="hidePopup('{{ $field }}')"
                                               ontouchstart="showPopup('{{ $field }}')"
                                               ontouchend="hidePopup('{{ $field }}')">
                                        <div class="range-labels" id="labels-{{ $field }}">
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

<script>
    // LÓGICA DO POPUP E DESTAQUE DOS NÚMEROS
    function updateRangePopup(input, field) {
        const popup = document.getElementById('popup-' + field);
        const value = input.value;
        popup.innerText = value;

        // Cálculo da posição do popup acompanhando o grão de café
        const min = input.min ? input.min : 1;
        const max = input.max ? input.max : 5;
        const newVal = Number(((value - min) * 100) / (max - min));
        
        // Ajuste fino para o popup não "vazar" nas pontas (36px é o tamanho do grão)
        popup.style.left = `calc(${newVal}% + (${18 - newVal * 0.36}px))`;

        // Destaca o número correspondente embaixo
        const labels = document.querySelectorAll(`#labels-${field} span`);
        labels.forEach((span, index) => {
            span.classList.toggle('active', (index + 1) == value);
        });
    }

    function showPopup(field) { document.getElementById('popup-' + field).style.display = 'block'; }
    function hidePopup(field) { document.getElementById('popup-' + field).style.display = 'none'; }

    $(document).ready(function() {
        // Inicializa o destaque dos números (valor 3 padrão)
        $('.custom-coffee-range').each(function() {
            updateRangePopup(this, $(this).attr('name'));
        });

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
                $.get("{{ route('registries.sensory-profiles.search-samples') }}", { q: query }, function(data) {
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

<div class="modal fade" id="modalCreateProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Criar Perfil Sensorial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Rota atualizada --}}
            <form action="{{ route('registries.sensory-profiles.store') }}" method="POST" id="formCreateProfile">
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
            // Rota AJAX atualizada
            $.get("{{ route('registries.sensory-profiles.search-samples') }}", { q: query }, function(data) {
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