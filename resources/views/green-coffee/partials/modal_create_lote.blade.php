<style>
    /* Ajustes finos de altura para evitar overflow */
    #modalRecebimento .form-control, #modalRecebimento .form-select, #modalRecebimento .input-group-text {
        height: 32px !important;
        padding-top: 2px !important;
        padding-bottom: 2px !important;
        font-size: 0.85rem !important;
    }
    #modalRecebimento .form-label { margin-bottom: 2px; font-size: 0.75rem; }
    .verified-badge { color: #0095f6; font-size: 0.9rem; vertical-align: middle; }
    .color-preview { width: 24px; height: 24px; border-radius: 6px; border: 1px solid #ddd; display: inline-block; vertical-align: middle; }
    
    /* Cursor para indicar interatividade no mapa */
    .slot-group { cursor: copy !important; outline: none !important; }
    .slot-group:hover .pallet-rect { stroke: #0095f6 !important; stroke-width: 10 !important; }

    /* Estilo para evitar qualquer seleção visual de texto ou foco azul */
    #interactive-map-container svg {
        user-select: none !important;
        -webkit-tap-highlight-color: transparent !important;
        outline: none !important;
    }

    /* Estilo para os resultados da pesquisa */
    .supplier-type-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        margin-top: -2px;
    }
</style>

<div class="modal fade" id="modalRecebimento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-light py-2">
                <h6 class="fw-bold mb-0" style="color: var(--coffee-brown);">
                    <i class="bi bi-box-seam me-2"></i>Novo Recebimento de Lote
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('estoque.store') }}" method="POST" id="formCreateLote">
                @csrf
                <div class="modal-body p-3">
                    <div class="row g-3">
                        {{-- Identificação --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted text-uppercase">Referência do Lote</label>
                            <input type="text" name="reference" class="form-control shadow-sm" placeholder="Nome para busca futura..." required>
                        </div>

                        {{-- Coluna Esquerda: Dados --}}
                        <div class="col-lg-7">
                            <div class="row g-2">
                                {{-- Busca de Fornecedor com Tipo --}}
                                <div class="col-md-6 position-relative">
                                    <label class="form-label fw-bold text-muted text-uppercase">Fornecedor</label>
                                    <input type="text" id="search-supplier" name="origin" class="form-control shadow-none" placeholder="Pesquisar..." autocomplete="off" required>
                                    <input type="hidden" name="origin_id" id="supplier_id_modal">
                                    <input type="hidden" name="origin_type" id="supplier_type_modal">
                                    <div id="supplier-results" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 9999;"></div>
                                </div>

                                {{-- Perfil com Preview de Cor --}}
                                <div class="col-md-6 position-relative">
                                    <label class="form-label fw-bold text-muted text-uppercase">Perfil Sensorial</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0" id="profile-color-indicator">
                                            <div class="color-preview" style="background-color: #eee;"></div>
                                        </span>
                                        <input type="text" id="search-sensory-profile" class="form-control border-start-0 shadow-none" placeholder="Digite o nome..." autocomplete="off" required>
                                    </div>
                                    <input type="hidden" name="sensory_profile_id" id="sensory_profile_id_modal">
                                    <div id="sensory-results" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 9999;"></div>
                                </div>

                                {{-- Endereçamento --}}
                                <div class="col-12 bg-light p-2 rounded border mt-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="small fw-bold text-muted text-uppercase mb-0">Localização (Clique no Mapa ou digite)</label>
                                        <button type="button" class="btn btn-xs btn-dark rounded-pill px-2 py-0" id="add-location-btn" style="font-size: 0.7rem;">+ Pilha</button>
                                    </div>
                                    <div id="locations-container">
                                        <div class="row g-1 mb-1 location-row align-items-end">
                                            <div class="col-md-2">
                                                <input type="text" name="locations[0][row]" class="form-control text-center fw-bold js-loc-row" placeholder="Fila" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="locations[0][pos]" class="form-control text-center fw-bold js-loc-pos" placeholder="Nº" min="1" required disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="locations[0][bags]" class="form-control text-center js-loc-bags" placeholder="Sacas" step="1" min="0" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="locations[0][remainder]" class="form-control text-center js-loc-remainder" placeholder="Sobra kg" inputmode="decimal">
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Calculadora --}}
                                <div class="col-md-4 mt-2">
                                    <label class="form-label small fw-bold text-success">TOTAL SACAS</label>
                                    <input type="number" id="js_sacas_total_modal" name="quantity_bags" class="form-control fw-bold border-success bg-light" readonly>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label class="form-label small fw-bold text-success">KG/SACA</label>
                                    <input type="text" id="js_peso_unit_modal" name="unit_weight" class="form-control fw-bold border-success shadow-none js-loc-remainder" value="60.00" inputmode="decimal">
                                </div>
                                <div class="col-md-4 mt-2">
                                    <label class="form-label small fw-bold text-primary">PESO TOTAL</label>
                                    <input type="number" id="js_peso_total_modal" name="total_weight" class="form-control fw-bold border-primary bg-light" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- COLUNA DIREITA: MAPA DINÂMICO --}}
                        <div class="col-lg-5">
                            <div class="p-2 border rounded-4 bg-white h-100 d-flex flex-column align-items-center">
                                <h6 class="small fw-bold text-muted text-uppercase mb-2">Selecione no Mapa</h6>
                                <div style="width: 100%; aspect-ratio: 1780/1885;" id="interactive-map-container">
                                    {{-- CORREÇÃO AQUI: Agora carrega da nova pasta 'maps' baseada na variável dinâmica --}}
                                    @include('green-coffee.maps.' . $mapView)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm w-100">FINALIZAR ENTRADA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const validSpots = { 'A': 5, 'B': 2, 'C': 3, 'D': 4, 'E': 3, 'F': 7 };
    let locIdx = 1;

    function resetMapSelection() {
        $('.pallet-rect').css({
            'stroke': '#402318',
            'stroke-width': '4'
        }).removeClass('pallet-highlight');
    }

    $('#modalRecebimento').on('hidden.bs.modal', function () {
        resetMapSelection();
        $('#formCreateLote')[0].reset();
        $('#profile-color-indicator .color-preview').css('background-color', '#eee');
        $('.location-row:not(:first)').remove();
        $('.js-loc-pos').first().prop('disabled', true);
    });

    $(document).on('input', '.js-loc-remainder', function() {
        let cursorPosition = this.selectionStart;
        let originalValue = this.value;
        let cleanedValue = originalValue.replace(/,/g, '.').replace(/[^0-9.]/g, '');
        let parts = cleanedValue.split('.');
        if (parts.length > 2) cleanedValue = parts[0] + '.' + parts.slice(1).join('');

        if (originalValue !== cleanedValue) {
            this.value = cleanedValue;
            this.setSelectionRange(cursorPosition, cursorPosition);
        }
        updateMapSelection();
    });

    $(document).on('input', '.js-loc-bags', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        updateMapSelection();
    });

    $('#add-location-btn').on('click', function() {
        let rowHtml = `
            <div class="row g-1 mb-1 location-row align-items-end">
                <div class="col-md-2"><input type="text" name="locations[${locIdx}][row]" class="form-control text-center fw-bold js-loc-row" placeholder="Fila" required></div>
                <div class="col-md-2"><input type="number" name="locations[${locIdx}][pos]" class="form-control text-center fw-bold js-loc-pos" placeholder="Nº" min="1" required disabled></div>
                <div class="col-md-3"><input type="number" name="locations[${locIdx}][bags]" class="form-control text-center js-loc-bags" placeholder="Sacas" step="1" min="0" required></div>
                <div class="col-md-3"><input type="text" name="locations[${locIdx}][remainder]" class="form-control text-center js-loc-remainder" placeholder="Sobra kg" inputmode="decimal"></div>
                <div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 remove-location"><i class="bi bi-trash"></i></button></div>
            </div>`;
        $('#locations-container').append(rowHtml);
        locIdx++;
    });

    $(document).on('click', '.remove-location', function() {
        $(this).closest('.location-row').remove();
        updateMapSelection();
    });

    $(document).on('input', '.js-loc-row', function() {
        this.value = this.value.toUpperCase().replace(/[^A-F]/g, '');
        const posInput = $(this).closest('.location-row').find('.js-loc-pos');
        if (this.value.length > 0) {
            posInput.prop('disabled', false);
        } else {
            posInput.prop('disabled', true).val('');
        }
        updateMapSelection();
    });

    $(document).on('input', '.js-loc-pos', function() {
        const row = $(this).closest('.location-row').find('.js-loc-row').val();
        const max = validSpots[row] || 99;
        if (parseInt(this.value) > max) this.value = max;
        if (parseInt(this.value) < 1) this.value = '';
        updateMapSelection();
    });

    $(document).on('click', '.slot-group', function() {
        const id = $(this).attr('id').replace('pallet-', '');
        const row = id.charAt(0);
        const pos = id.substring(1);
        let targetRow = null;

        $('.location-row').each(function() {
            if (!$(this).find('.js-loc-row').val()) {
                targetRow = $(this);
                return false;
            }
        });

        if (!targetRow) {
            $('#add-location-btn').click();
            targetRow = $('.location-row').last();
        }

        targetRow.find('.js-loc-row').val(row);
        targetRow.find('.js-loc-pos').prop('disabled', false).val(pos);
        updateMapSelection();
    });

    function updateMapSelection() {
        resetMapSelection();
        let totalSacas = 0, totalPeso = 0;
        const padrao = parseFloat($('#js_peso_unit_modal').val()) || 0;

        $('.location-row').each(function() {
            const r = $(this).find('.js-loc-row').val();
            const p = $(this).find('.js-loc-pos').val();
            const bags = parseInt($(this).find('.js-loc-bags').val()) || 0;
            const rem = parseFloat($(this).find('.js-loc-remainder').val()) || 0;

            if (r && p) {
                $(`#pallet-${r}${p} .pallet-rect`).css({
                    'stroke': '#ffc107',
                    'stroke-width': '12'
                });
            }
            totalSacas += bags;
            totalPeso += (bags * padrao) + rem;
        });

        $('#js_sacas_total_modal').val(totalSacas);
        $('#js_peso_total_modal').val(totalPeso.toFixed(2));
    }

    $(document).on('input', '.js-loc-bags, .js-loc-remainder, #js_peso_unit_modal', updateMapSelection);

    function setupSearch(inputId, resId, url, type) {
        $(`#${inputId}`).on('input', function() {
            let q = $(this).val();
            if(q.length < 2) return $(`#${resId}`).addClass('d-none');
            
            $.get(url, { search: q, format: 'json' }, function(data) {
                let html = '';
                data.forEach(i => {
                    if (type === 'sup') {
                        let label = 'Produtor';
                        const tech = (i.type || '').toLowerCase();
                        if (tech.includes('company')) label = 'Empresa';
                        else if (tech.includes('farm')) label = 'Fazenda';

                        html += `
                            <button type="button" class="list-group-item list-group-item-action sel-sup" data-id="${i.id}" data-name="${i.name}" data-type="${i.type}">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold"><i class="bi bi-patch-check-fill verified-badge me-1"></i> ${i.name}</span>
                                    <span class="supplier-type-label">${label}</span>
                                </div>
                            </button>`;
                    } else {
                        html += `<button type="button" class="list-group-item list-group-item-action sel-prof" data-id="${i.id}" data-name="${i.name}" data-color="${i.color_hex}"><span class="color-preview me-2" style="background-color: ${i.color_hex}"></span> ${i.name}</button>`;
                    }
                });
                $(`#${resId}`).html(html).removeClass('d-none');
            });
        });
    }

    setupSearch('search-supplier', 'supplier-results', "{{ route('registries.suppliers.search') }}", 'sup');
    setupSearch('search-sensory-profile', 'sensory-results', "{{ route('registries.sensory-profiles.index') }}", 'prof');

    $(document).on('click', '.sel-sup', function() {
        $('#search-supplier').val($(this).data('name'));
        $('#supplier_id_modal').val($(this).data('id'));
        $('#supplier_type_modal').val($(this).data('type'));
        $('#supplier-results').addClass('d-none');
    });

    $(document).on('click', '.sel-prof', function() {
        const color = $(this).data('color');
        $('#search-sensory-profile').val($(this).data('name'));
        $('#sensory_profile_id_modal').val($(this).data('id'));
        $('#profile-color-indicator .color-preview').css('background-color', color);
        $('#sensory-results').addClass('d-none');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('.list-group').addClass('d-none');
        }
    });
});
</script>