<div class="modal fade" id="modalSaida" tabindex="-1" aria-labelledby="modalSaidaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalSaidaLabel" style="color: var(--coffee-brown);">
                    <i class="bi bi-box-arrow-up me-2 text-danger"></i>Registrar Saída
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('estoque.registrar-saida') }}" method="POST" id="formRegistrarSaida">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <select name="reason" id="saida_reason" class="form-select shadow-none border-2 fw-bold" style="border-radius: 8px; height: 45px;">
                            <option value="Demanda de torra">MOTIVO: Demanda de Torra</option>
                            <option value="Outros">MOTIVO: Outros (Justifique abaixo)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <select name="green_coffee_lot_id" id="saida_lot_id" class="form-select shadow-none border-2 fw-bold" style="border-radius: 8px; height: 45px;" required>
                            <option value="">SELECIONE O LOTE DE ORIGEM...</option>
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}" 
                                        data-weight="{{ $lote->total_weight }}"
                                        data-locations="{{ $lote->locations->map(fn($l) => ['id' => $l->id, 'label' => $l->address_row . $l->address_pos])->toJson() }}">
                                    LOTE: {{ $lote->reference }} (Disp: {{ number_format($lote->total_weight, 1, ',', '.') }}kg)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="wrapper_saida_address" style="display: none;">
                        <select name="location_id" id="saida_location_id" class="form-select shadow-none border-2 fw-bold text-primary" style="border-radius: 8px; height: 45px;" required disabled>
                            <option value="">SELECIONE A POSIÇÃO/ENDEREÇO...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Quantidade a retirar (kg)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-2 border-end-0"><i class="bi bi-hash"></i></span>
                            <input type="number" name="weight" id="saida_weight" step="0.01" min="0.01" class="form-control border-2 shadow-none fw-bold" placeholder="0,00" required>
                        </div>
                        <div id="weight_error" class="text-danger small mt-1 fw-bold" style="display: none;">
                            <i class="bi bi-exclamation-triangle-fill"></i> Saldo insuficiente!
                        </div>
                    </div>

                    <div class="mb-2">
                        <textarea name="notes" id="saida_notes" class="form-control border-2 shadow-none" rows="2" placeholder="Observações..." style="border-radius: 8px;"></textarea>
                        <div id="notes_error" class="text-danger fw-bold mt-1" style="display: none; font-size: 0.7rem;">
                             Justificativa obrigatória para o motivo "Outros"!
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold text-muted" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" id="btnSubmitSaida" class="btn btn-danger fw-bold px-4 shadow-sm" style="border-radius: 8px;">
                        CONFIRMAR SAÍDA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#saida_lot_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const weight = selectedOption.data('weight');
        const locations = selectedOption.data('locations');
        
        const $addressWrapper = $('#wrapper_saida_address');
        const $addressSelect = $('#saida_location_id');

        $addressSelect.html('<option value="">SELECIONE A POSIÇÃO/ENDEREÇO...</option>');

        if (locations && locations.length > 0) {
            locations.forEach(loc => {
                $addressSelect.append(`<option value="${loc.id}">ENDEREÇO: ${loc.label}</option>`);
            });
            $addressWrapper.fadeIn();
            $addressSelect.prop('disabled', false).prop('required', true);
        } else {
            $addressWrapper.hide();
            $addressSelect.prop('disabled', true).prop('required', false);
        }

        if(weight) $('#saida_weight').attr('max', weight);
        validateSaida();
    });

    // Validar também ao mudar o motivo ou digitar nas observações
    $('#saida_weight, #saida_notes').on('input', validateSaida);
    $('#saida_reason').on('change', validateSaida);

    function validateSaida() {
        const max = parseFloat($('#saida_weight').attr('max')) || 0;
        const current = parseFloat($('#saida_weight').val()) || 0;
        const reason = $('#saida_reason').val();
        const notes = $('#saida_notes').val().trim();

        const exceeds = current > max;
        const isWeightEmpty = current <= 0;
        
        // Validação da Observação para o motivo "Outros"
        const isOthers = reason === 'Outros';
        const isNotesEmpty = notes === "";
        const notesInvalid = isOthers && isNotesEmpty;

        // Feedback visual do peso
        if (exceeds) {
            $('#weight_error').show();
            $('#saida_weight').addClass('is-invalid');
        } else {
            $('#weight_error').hide();
            $('#saida_weight').removeClass('is-invalid');
        }

        // Feedback visual das observações
        if (notesInvalid) {
            $('#saida_notes').addClass('is-invalid');
            $('#notes_error').show();
        } else {
            $('#saida_notes').removeClass('is-invalid');
            $('#notes_error').hide();
        }

        // Desabilita botão se houver erro de peso ou se "Outros" estiver sem nota
        $('#btnSubmitSaida').prop('disabled', exceeds || isWeightEmpty || notesInvalid);
    }
});
</script>