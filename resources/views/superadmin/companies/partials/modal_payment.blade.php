<div class="modal fade" id="modalRegisterPayment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 ps-4 pt-4">
                <h5 class="modal-title fw-bold text-success">
                    <i class="bi bi-currency-dollar me-1"></i> Registrar Pagamento
                </h5>
                <button type="button" class="btn-close me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- O action será preenchido pelo JavaScript --}}
            <form id="formPayment" action="" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="modal-body px-4">
                    <p class="small text-muted mb-4">
                        Confirmando recebimento para: <strong id="paymentCompanyName" class="text-dark"></strong>
                    </p>
                    
                    <div class="row g-3">
                        {{-- DATA DO PAGAMENTO --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Data do Pagamento</label>
                            <input type="date" class="form-control border-0 bg-light" name="payment_date" required value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- VALOR / QUANTIDADE --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Valor Recebido (R$)</label>
                            <input type="number" step="0.01" class="form-control border-0 bg-light" name="amount" required placeholder="0,00">
                        </div>

                        {{-- FORMA DE PAGAMENTO --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold">Forma de Pagamento</label>
                            <select class="form-select border-0 bg-light" name="payment_method" required>
                                <option value="" selected disabled>Selecione uma opção...</option>
                                <option value="pix">Pix</option>
                                <option value="boleto">Boleto</option>
                                <option value="transferencia">Transferência Bancária</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                                <option value="cartao_debito">Cartão de Débito</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="outros">Outros...</option>
                            </select>
                        </div>

                        {{-- OBSERVAÇÕES --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold">Observações Internas</label>
                            <textarea class="form-control border-0 bg-light" name="notes" rows="3" placeholder="Ex: Referente à mensalidade de Janeiro/2026..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                        Confirmar Recebimento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPaymentModal(url, name) {
        const form = document.getElementById('formPayment');
        const nameSpan = document.getElementById('paymentCompanyName');
        
        // Agora o 'url' já vem completo do servidor (ex: http://127.0.0.1:8000/master-panel/...)
        form.action = url;
        nameSpan.innerText = name;
        
        const modal = new bootstrap.Modal(document.getElementById('modalRegisterPayment'));
        modal.show();
    }
</script>