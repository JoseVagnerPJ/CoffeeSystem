<div class="modal fade" id="modalInventario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <div>
                    <h4 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2"></i>Auditoria de Inventário #{{ $session->id }}</h4>
                    <small class="opacity-75 text-uppercase js-session-date">
                        {{ $session->status == 'iniciado' ? 'Snapshot: ' . date('d/m/Y', strtotime($session->inventory_date)) : 'Aguardando Início' }}
                    </small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body bg-light p-0">
                {{-- BLOCO DE BLINDAGEM CONTRA AUTOFILL (ENGANA O EDGE/CHROME) --}}
                <div style="position: absolute; top: -9999px; left: -9999px;">
                    <input type="text" name="fake_user_name">
                    <input type="password" name="fake_password">
                </div>

                <div class="container-fluid py-4">
                    <div class="row g-4">
                        {{-- COLUNA ESQUERDA --}}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-outline-dark w-100 rounded-pill fw-bold py-2 mb-4" data-bs-toggle="modal" data-bs-target="#modalMapaArmazem">
                                <i class="bi bi-map me-2"></i>MAPA DE ENDEREÇOS
                            </button>

                            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
                                <h6 class="fw-bold text-uppercase text-muted small mb-3">Resumo da Sessão</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Divergência:</span>
                                    <span id="total-inventory-diff" class="fw-bold text-dark">0.00 kg</span>
                                </div>
                                <div id="actions-container">
                                    @if($session->status == 'aberto')
                                        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mt-2 js-btn-start">INICIAR INVENTÁRIO</button>
                                    @else
                                        <button type="button" class="btn btn-success w-100 rounded-pill fw-bold py-2 mt-2 js-btn-finish">FINALIZAR INVENTÁRIO</button>
                                    @endif
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: white !important;">
                                <h6 class="fw-bold text-uppercase text-muted small mb-3">Histórico <span class="badge bg-light text-muted border ms-1">{{ count($history) }}</span></h6>
                                <div id="debug-history-container">
                                    @php $firstSix = array_slice($history, 0, 6); @endphp
                                    @forelse($firstSix as $h)
                                        <div style="background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #6f4e37;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div><b>ID #{{ $h['id'] }}</b><br><small>{{ date('d/m/Y', strtotime($h['inventory_date'])) }}</small></div>
                                                <div class="text-end"><b>{{ number_format($h['total_diff_kg'], 1) }}kg</b><br><a href="javascript:void(0)" onclick="viewInventoryDetail({{ $h['id'] }})" class="small text-decoration-none fw-bold">VER</a></div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted small italic text-center py-3">Nenhum histórico.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- COLUNA DIREITA --}}
                        <div class="col-lg-10">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden js-table-container {{ $session->status == 'aberto' ? 'opacity-50' : '' }}">
                                <table class="table align-middle mb-0">
                                    <thead class="table-dark small text-uppercase" style="font-size: 0.65rem;">
                                        <tr>
                                            <th rowspan="2" class="ps-4 align-middle" style="width: 120px;">Lote</th>
                                            <th rowspan="2" class="text-center align-middle" style="width: 45px;">+</th>
                                            <th colspan="3" class="text-center border-bottom border-secondary py-2">Localização / Contagem Real</th>
                                            <th rowspan="2" class="text-center align-middle" style="width: 100px;">Apurado</th>
                                            <th rowspan="2" class="text-center align-middle" style="width: 100px;">Sistema</th>
                                            <th rowspan="2" class="text-center align-middle" style="width: 90px;">Diverg..</th>
                                            <th rowspan="2" class="pe-4 align-middle" style="width: 150px;">Observações</th>
                                        </tr>
                                        <tr class="bg-dark text-muted">
                                            <th class="text-center py-1" style="width: 130px; letter-spacing: 1px;">Endereço (Rua/Num)</th>
                                            <th class="text-center py-1" style="letter-spacing: 1px;">Qtd Sacas x Peso Unit. + Sobra (kg)</th>
                                            <th class="text-center py-1" style="width: 85px; letter-spacing: 1px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lotes as $lote)
                                            <tr id="parent-lote-{{ $lote->id }}" class="border-bottom">
                                                <td class="ps-4 align-top pt-3">
                                                    <div class="fw-bold text-nowrap small">{{ $lote->reference }}</div>
                                                    <input type="hidden" class="js-system-weight-val" value="{{ $lote->total_weight }}">
                                                </td>
                                                
                                                <td class="text-center align-top pt-3">
                                                    <button type="button" class="btn btn-sm btn-primary rounded-circle js-add-loc-line" 
                                                            data-id="{{ $lote->id }}" style="width: 26px; height: 26px; padding: 0;"
                                                            {{ $session->status == 'aberto' ? 'disabled' : '' }}>
                                                        <i class="bi bi-plus-lg small"></i>
                                                    </button>
                                                </td>

                                                <td class="p-0" colspan="3">
                                                    <div class="js-loc-lines-container" data-id="{{ $lote->id }}">
                                                        @php 
                                                            $lotAdjustments = $adjustments[$lote->id] ?? collect();
                                                            if($lotAdjustments->isEmpty()) {
                                                                $lotAdjustments->push((object)[
                                                                    'new_address_row' => '', 'new_address_pos' => '',
                                                                    'closed_bags' => '', 'bag_weight' => 60,
                                                                    'remainder_weight' => '', 'physical_weight' => 0
                                                                ]);
                                                            }
                                                        @endphp
                                                        @foreach($lotAdjustments as $adj)
                                                        <div class="d-flex align-items-center gap-2 p-2 border-bottom js-loc-line">
                                                            <div class="input-group input-group-sm" style="width: 130px;">
                                                                <input type="text" class="form-control text-center fw-bold js-row" placeholder="Rua" value="{{ $adj->new_address_row }}" maxlength="1">
                                                                <input type="text" class="form-control text-center fw-bold js-pos" placeholder="Nº" value="{{ $adj->new_address_pos }}" {{ empty($adj->new_address_row) ? 'disabled' : '' }}>
                                                            </div>
                                                            <div class="input-group input-group-sm flex-grow-1">
                                                                <input type="number" class="form-control text-center js-bags" placeholder="Sacas" value="{{ $adj->closed_bags }}" min="0" step="1">
                                                                <span class="input-group-text bg-light">x</span>
                                                                <input type="number" class="form-control text-center js-bw" value="{{ $adj->bag_weight }}" min="0" step="1">
                                                                <span class="input-group-text bg-light">+</span>
                                                                <input type="number" class="form-control text-center js-rem" placeholder="Sobra" value="{{ $adj->remainder_weight }}" min="0" step="any">
                                                            </div>
                                                            <div style="width: 85px;">
                                                                <input type="text" class="form-control form-control-sm text-center fw-bold bg-light border-0 js-line-total" value="{{ number_format($adj->physical_weight, 1, ',', '.') }}" readonly>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-link text-danger p-0 js-remove-line" {{ $session->status == 'aberto' ? 'disabled' : '' }}><i class="bi bi-x-circle-fill"></i></button>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </td>

                                                <td class="text-center align-top pt-3">
                                                    <span class="fw-bold js-lot-total-physical small">0,0 kg</span>
                                                </td>

                                                <td class="text-center align-top pt-3 text-muted small">
                                                    {{ number_format($lote->total_weight, 1, ',', '.') }}
                                                </td>

                                                <td class="text-center align-top pt-3">
                                                    <span class="js-diff-badge badge rounded-pill bg-light text-muted small" style="font-size: 0.7rem;">0,0 kg</span>
                                                </td>

                                                <td class="pe-4 align-top pt-3">
                                                    <input type="text" 
                                                           class="form-control form-control-sm js-reason" 
                                                           placeholder="..." 
                                                           value="{{ $lotAdjustments->first()->reason ?? '' }}"
                                                           autocomplete="one-time-code"
                                                           spellcheck="false">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DO MAPA --}}
<div class="modal fade" id="modalMapaArmazem" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-2">
                <span class="small fw-bold text-uppercase"><i class="bi bi-map me-2"></i>Consulta de Endereços</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size: 0.7rem;"></button>
            </div>
            <div class="modal-body p-2 bg-white text-center">
                <div class="warehouse-map-mini" style="background: #fdfdfd; border-radius: 8px; pointer-events: none;">
                    {{-- AJUSTE DINÂMICO AQUI --}}
                    @include('green-coffee.maps.' . $mapView)
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const validSpots = { 'A': 5, 'B': 2, 'C': 3, 'D': 4, 'E': 3, 'F': 7 };

    // --- REGRAS DE VALIDAÇÃO ---
    
    // Regra 1: Apenas números positivos em campos numéricos
    $(document).on('input', 'input[type="number"]', function() {
        if (parseFloat($(this).val()) < 0) {
            $(this).val(0);
        }
    });

    // Regra 2: Somente números inteiros (sem pontos/vírgulas) para Sacas e Peso Unitário
    $(document).on('input', '.js-bags, .js-bw', function() {
        this.value = this.value.replace(/[^\d]/g, '');
    });

    // --- LÓGICA DE ENDEREÇAMENTO ---

    $(document).on('input', '.js-row', function() {
        this.value = this.value.toUpperCase().replace(/[^A-F]/g, '');
        const posInput = $(this).closest('.js-loc-line').find('.js-pos');
        if (this.value.length >= 1) {
            posInput.prop('disabled', false);
            posInput.focus();
        } else {
            posInput.prop('disabled', true).val('');
        }
        $(this).closest('.js-loc-line').find('.js-pos').trigger('input');
    });

    $(document).on('input', '.js-pos', function() {
        const row = $(this).prev('.js-row').val();
        const maxVal = validSpots[row] || 99;
        let val = parseInt(this.value);
        if (row && validSpots[row]) {
            if (val > maxVal) this.value = maxVal;
            if (val < 1) this.value = '';
        }
        if (this.value.length >= 1) {
            $(this).closest('.js-loc-line').find('.js-bags').focus();
        }
    });

    $(document).on('click', '.js-btn-start', function() {
        $.post("{{ route('estoque.inventory.start') }}", { _token: "{{ csrf_token() }}" }, function() { 
            localStorage.setItem('reopenInventoryModal', 'true');
            location.reload(); 
        });
    });

    $(document).on('click', '.js-btn-finish', function() {
        const btn = $(this);
        if(!confirm('Deseja finalizar o inventário? Isso atualizará o peso de todos os lotes no sistema.')) return;

        // Feedback visual para evitar cliques duplos
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> FINALIZANDO...');

        $.post("{{ route('estoque.inventory.finish') }}", { _token: "{{ csrf_token() }}" })
            .done(function(res) {
                localStorage.removeItem('reopenInventoryModal');
                location.reload(); 
            })
            .fail(function(xhr) {
                // Se o controller retornar erro 422 ou 500, o alerta abaixo dirá o porquê
                const msg = xhr.responseJSON ? xhr.responseJSON.error : 'Erro ao finalizar inventário.';
                alert('Atenção: ' + msg);
                btn.prop('disabled', false).text('FINALIZAR INVENTÁRIO');
            });
    });

    if (localStorage.getItem('reopenInventoryModal') === 'true') {
        var modalEl = document.getElementById('modalInventario');
        if (modalEl) {
            var myModal = new bootstrap.Modal(modalEl);
            myModal.show();
        }
    }

    $(document).on('click', '.js-add-loc-line', function() {
        const lotId = $(this).data('id');
        const container = $(`.js-loc-lines-container[data-id="${lotId}"]`);
        const newLine = `
            <div class="d-flex align-items-center gap-2 p-2 border-bottom js-loc-line">
                <div class="input-group input-group-sm" style="width: 130px;">
                    <input type="text" class="form-control text-center fw-bold js-row" placeholder="Rua" maxlength="1">
                    <input type="text" class="form-control text-center fw-bold js-pos" placeholder="Nº" disabled>
                </div>
                <div class="input-group input-group-sm flex-grow-1">
                    <input type="number" class="form-control text-center js-bags" placeholder="Sacas" min="0" step="1">
                    <span class="input-group-text bg-light">x</span>
                    <input type="number" class="form-control text-center js-bw" value="60" min="0" step="1">
                    <span class="input-group-text bg-light">+</span>
                    <input type="number" class="form-control text-center js-rem" placeholder="Sobra" min="0" step="any">
                </div>
                <div style="width: 85px;"><input type="text" class="form-control form-control-sm text-center fw-bold bg-light border-0 js-line-total" value="0,0" readonly></div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0 js-remove-line"><i class="bi bi-x-circle-fill"></i></button>
            </div>`;
        container.append(newLine);
    });

    $(document).on('click', '.js-remove-line', function() {
        const parentTr = $(this).closest('tr');
        const lotId = parentTr.attr('id').replace('parent-lote-', '');
        $(this).closest('.js-loc-line').remove();
        calculateLot(lotId);
    });

    $(document).on('input', '.js-bags, .js-bw, .js-rem, .js-row, .js-pos, .js-reason', function() {
        const tr = $(this).closest('tr');
        if (tr.attr('id')) {
            const lotId = tr.attr('id').replace('parent-lote-', '');
            calculateLot(lotId);
        }
    });

    function calculateLot(id) {
        const parent = $(`#parent-lote-${id}`);
        if(!parent.length) return;
        
        let lotTotal = 0;
        const locations = [];

        parent.find('.js-loc-line').each(function() {
            const bags = parseFloat($(this).find('.js-bags').val()) || 0;
            const bw = parseFloat($(this).find('.js-bw').val()) || 0;
            const rem = parseFloat($(this).find('.js-rem').val()) || 0;
            const lineTotal = (bags * bw) + rem;
            
            $(this).find('.js-line-total').val(lineTotal.toFixed(1).replace('.', ','));
            lotTotal += lineTotal;

            locations.push({
                row: $(this).find('.js-row').val(),
                pos: $(this).find('.js-pos').val(),
                closed_bags: bags,
                bag_weight: bw,
                remainder_weight: rem,
                total_line: lineTotal
            });
        });

        const system = parseFloat(parent.find('.js-system-weight-val').val());
        const diff = lotTotal - system;

        parent.find('.js-lot-total-physical').text(lotTotal.toFixed(1).replace('.', ',') + ' kg');
        const badge = parent.find('.js-diff-badge');
        badge.text((diff > 0 ? '+' : '') + diff.toFixed(1).replace('.', ',') + ' kg')
             .removeClass('bg-light bg-danger bg-primary bg-success text-muted')
             .addClass(diff < 0 ? 'bg-danger' : (diff > 0 ? 'bg-primary' : 'bg-success'));

        $.post("{{ route('estoque.inventory.store') }}", {
            _token: "{{ csrf_token() }}",
            lot_id: id,
            system_weight: system,
            locations: locations,
            reason: parent.find('.js-reason').val()
        });
        
        calculateGlobal();
    }

    function calculateGlobal() {
        let total = 0;
        $('.js-diff-badge').each(function() { 
            total += parseFloat($(this).text().replace(' kg', '').replace(',', '.').replace('+', '')) || 0;
        });
        $('#total-inventory-diff').text((total > 0 ? '+' : '') + total.toFixed(2).replace('.', ',') + ' kg')
            .removeClass('text-danger text-primary').addClass(total < 0 ? 'text-danger' : (total > 0 ? 'text-primary' : ''));
    }
    
    $('tbody tr').each(function() { 
        const id = $(this).attr('id');
        if(id) calculateLot(id.replace('parent-lote-', '')); 
    });
});
</script>