<div class="modal fade" id="modalCreateRoast" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-fire me-2 text-warning"></i>Nova Demanda de Torra</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('roasting.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Coluna Inputs --}}
                        <div class="col-lg-8">
                            <div class="row g-3">
                                {{-- Busca Lote --}}
                                <div class="col-md-9 position-relative">
                                    <label class="form-label small fw-bold text-muted">LOTE DE CAFÉ VERDE</label>
                                    <input type="text" id="ajax-lot-search" class="form-control" placeholder="Digite para buscar lote..." autocomplete="off">
                                    <input type="hidden" name="green_coffee_lot_id" id="lot_id_val">
                                    <div id="lot-res" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index:1000"></div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <label class="form-label small fw-bold text-muted">ESTOQUE</label>
                                    <div class="p-2 border rounded bg-light position-relative">
                                        <span id="display-stock" class="fw-bold">0.0</span> kg
                                        <small id="display-delta" class="text-danger d-block fw-bold d-none" style="font-size:0.6rem">-0.00</small>
                                    </div>
                                </div>

                                {{-- Perfil e Cor --}}
                                <div class="col-md-6 position-relative">
                                    <label class="form-label small fw-bold text-muted">PERFIL SENSORIAL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><div id="color-indicator" style="width:18px; height:18px; border-radius:4px; background:#eee; border: 1px solid #ddd;"></div></span>
                                        <input type="text" id="ajax-profile-search" class="form-control" placeholder="Buscar perfil..." autocomplete="off">
                                    </div>
                                    <input type="hidden" name="sensory_profile_id" id="profile_id_val">
                                    <div id="profile-res" class="list-group position-absolute w-100 shadow d-none" style="z-index:1000"></div>
                                </div>

                                {{-- Mestre --}}
                                <div class="col-md-6 position-relative">
                                    <label class="form-label small fw-bold text-muted">MESTRE DE TORRA</label>
                                    <input type="text" id="ajax-mestre-search" class="form-control" placeholder="Pesquisar..." autocomplete="off">
                                    <input type="hidden" name="mestre_user_id" id="mestre_id_val">
                                    <div id="mestre-res" class="list-group position-absolute w-100 shadow d-none" style="z-index:1000"></div>
                                </div>

                                {{-- Máquina e Capacidade --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">TORRADOR</label>
                                    <select name="machine_id" id="select-machine" class="form-select" required>
                                        <option value="" selected disabled>Escolha a máquina...</option>
                                        @foreach($machines as $m)
                                        <option value="{{ $m->id }}" data-min="{{ $m->min_capacity }}" data-max="{{ $m->max_capacity }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">PONTO DE COR</label>
                                    <select name="roast_level" class="form-select">
                                        <option>Torra Clara</option>
                                        <option>Torra Média-Clara</option>
                                        <option selected>Torra Média</option>
                                        <option>Torra Média-Escura</option>
                                        <option>Torra Escura</option>
                                    </select>
                                </div>

                                {{-- Pesos --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-primary">PESO TOTAL (KG)</label>
                                    <input type="number" name="total_weight" id="in-total" step="0.01" class="form-control fw-bold border-primary">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-primary">PESO POR CICLO PADRÃO (KG)</label>
                                    <input type="number" name="partial_weight" id="in-partial" step="0.01" class="form-control fw-bold border-primary" disabled>
                                </div>
                            </div>
                        </div>

                        {{-- Coluna Plano Produção --}}
                        <div class="col-lg-4">
                            <div class="h-100 bg-light p-3 rounded-3 border">
                                <h6 class="small fw-bold text-uppercase opacity-50 mb-3">Assistente de Produção</h6>
                                <div id="calc-summary" class="text-center py-4 text-muted small">Defina máquina e pesos para calcular.</div>
                                <div id="calc-details" class="d-none">
                                    <div class="p-3 bg-white shadow-sm rounded border-start border-4 border-primary mb-2">
                                        <div class="h3 fw-bold mb-0" id="res-ciclos">0</div>
                                        <div class="small fw-bold text-uppercase opacity-50">Ciclos Totais</div>
                                    </div>

                                    {{-- Info de Torra Quebrada --}}
                                    <div id="box-quebrada" class="p-2 mb-2 border rounded bg-white d-none">
                                        <small class="text-muted fw-bold d-block" style="font-size: 0.6rem;">TORRA PARCIAL (FINAL)</small>
                                        <span id="val-quebrada" class="fw-bold text-dark">0.00 kg</span>
                                    </div>

                                    <div id="alert-machine" class="alert alert-danger p-2 small d-none">Ciclo padrão fora do limite!</div>
                                    <div id="alert-remainder" class="alert alert-danger p-2 small d-none">A torra parcial está fora do limite da máquina!</div>
                                    <div id="suggest-machine" class="alert alert-info p-2 small d-none"></div>
                                </div>
                                <textarea name="notes" class="form-control mt-3" rows="3" placeholder="Observações..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="submit" id="btn-submit" class="btn btn-success w-100 rounded-pill fw-bold" disabled>LANÇAR DEMANDA NO KANBAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentLotWeight = 0;

    // 1. Busca AJAX Lotes
    $('#ajax-lot-search').on('input', function() {
        let q = $(this).val();
        if(q.length < 2) return $('#lot-res').addClass('d-none');
        $.get("{{ route('roasting.search.lots') }}", { q }, function(data) {
            let h = '';
            data.forEach(l => h += `<button type="button" class="list-group-item list-group-item-action sel-lot" data-id="${l.id}" data-weight="${l.total_weight}">${l.reference} - ${l.origin}</button>`);
            $('#lot-res').html(h).removeClass('d-none');
        });
    });

    $(document).on('click', '.sel-lot', function() {
        currentLotWeight = $(this).data('weight');
        $('#ajax-lot-search').val($(this).text());
        $('#lot_id_val').val($(this).data('id'));
        $('#display-stock').text(currentLotWeight);
        $('#lot-res').addClass('d-none');
        calc();
    });

    // 2. Busca AJAX Perfil
    $('#ajax-profile-search').on('input', function() {
        let q = $(this).val();
        if(q.length < 1) return $('#profile-res').addClass('d-none');
        $.get("{{ route('roasting.search.profiles') }}", { q }, function(data) {
            let h = '';
            data.forEach(p => {
                h += `
                <button type="button" class="list-group-item list-group-item-action sel-prof d-flex align-items-center" data-id="${p.id}" data-color="${p.color_hex}">
                    <div style="width:14px; height:14px; border-radius:4px; background-color:${p.color_hex}; margin-right:10px; border:1px solid #ddd;"></div>
                    ${p.name}
                </button>`;
            });
            $('#profile-res').html(h).removeClass('d-none');
        });
    });

    $(document).on('click', '.sel-prof', function() {
        $('#ajax-profile-search').val($.trim($(this).text()));
        $('#profile_id_val').val($(this).data('id'));
        $('#color-indicator').css('background-color', $(this).data('color'));
        $('#profile-res').addClass('d-none');
    });

    // 3. Busca AJAX Mestre de Torra
    $('#ajax-mestre-search').on('input', function() {
        let q = $(this).val();
        if(q.length < 2) return $('#mestre-res').addClass('d-none');
        $.get("{{ route('roasting.search.mestres') }}", { q }, function(data) {
            let h = '';
            data.forEach(m => h += `<button type="button" class="list-group-item list-group-item-action sel-mestre" data-id="${m.id}">${m.name}</button>`);
            $('#mestre-res').html(h).removeClass('d-none');
        });
    });

    $(document).on('click', '.sel-mestre', function() {
        $('#ajax-mestre-search').val($(this).text());
        $('#mestre_id_val').val($(this).data('id'));
        $('#mestre-res').addClass('d-none');
    });

    // 4. Lógica de Pesos e Máquinas
    $('#select-machine').change(function() { 
        $('#in-partial').prop('disabled', false); 
        calc();
    });

    $('#in-total, #in-partial').on('input', calc);

    function calc() {
        const total = parseFloat($('#in-total').val()) || 0;
        const partial = parseFloat($('#in-partial').val()) || 0;
        const machine = $('#select-machine option:selected');
        const min = parseFloat(machine.data('min'));
        const max = parseFloat(machine.data('max'));

        if(total > 0) {
            $('#display-delta').text(`-${total.toFixed(2)}`).removeClass('d-none');
        } else {
            $('#display-delta').addClass('d-none');
        }

        if(total > 0 && partial > 0) {
            $('#calc-summary').addClass('d-none');
            $('#calc-details').removeClass('d-none');
            
            let ciclosInteiros = Math.floor(total / partial);
            let sobra = fmod(total, partial);
            
            // Total de batidas no torrador
            $('#res-ciclos').text(sobra > 0.01 ? ciclosInteiros + 1 : ciclosInteiros);

            // Exibe box de torra quebrada se houver sobra significativa
            if(sobra > 0.01) {
                $('#box-quebrada').removeClass('d-none');
                $('#val-quebrada').text(sobra.toFixed(2) + ' kg');
            } else {
                $('#box-quebrada').addClass('d-none');
            }

            const hasRemainder = sobra > 0.01;
            const machineLimitError = (partial < min || partial > max);
            // Agora validamos se a SOBRA também cabe na máquina
            const remainderLimitError = hasRemainder && (sobra < min || sobra > max);
            const stockError = (total > currentLotWeight);

            // Controle de alertas
            if (machineLimitError) $('#alert-machine').removeClass('d-none'); else $('#alert-machine').addClass('d-none');
            if (remainderLimitError) $('#alert-remainder').removeClass('d-none'); else $('#alert-remainder').addClass('d-none');

            // Lógica de bloqueio: 
            // Permite se (Não tem erro de máquina no ciclo padrão) E (Não tem erro de máquina na sobra ou não tem sobra) E (Tem estoque)
            if(machineLimitError || remainderLimitError || stockError) {
                $('#btn-submit').prop('disabled', true);
            } else {
                $('#btn-submit').prop('disabled', false);
            }

        } else {
            $('#calc-summary').removeClass('d-none');
            $('#calc-details').addClass('d-none');
            $('#btn-submit').prop('disabled', true);
        }
    }

    function fmod(a, b) { return Number((a - (Math.floor(a / b) * b)).toPrecision(8)); }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('.list-group').addClass('d-none');
        }
    });
});
</script>