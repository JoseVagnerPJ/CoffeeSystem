<div class="modal fade" id="modalHistoricoMovimentacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-light border-0 py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="fw-bold mb-0">Histórico de Movimentação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Filtros --}}
                <div class="row g-2 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">DATA INICIAL</label>
                        <input type="date" id="hist_start_date" class="form-control form-control-sm border-0 bg-light">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">DATA FINAL</label>
                        <input type="date" id="hist_end_date" class="form-control form-control-sm border-0 bg-light">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">ATALHO</label>
                        <select class="form-select form-select-sm border-0 bg-light fw-bold" id="hist_quick_filter" onchange="applyQuickFilterHist(this.value)">
                            <option value="last_week">Última Semana</option>
                            <option value="curr_month">Mês Atual</option>
                            <option value="last_month">Último Mês</option>
                            <option value="all">Tudo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button onclick="refreshHistorico()" class="btn btn-dark btn-sm w-100 fw-bold">APLICAR</button>
                    </div>
                </div>

                {{-- Alternador --}}
                <div class="btn-group w-100 mb-4 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <button id="btn-hist-entrada" onclick="changeHistType('entrada')" class="btn btn-success fw-bold">📈 RECEBIMENTOS</button>
                    <button id="btn-hist-saida" onclick="changeHistType('saida')" class="btn btn-outline-danger fw-bold">📉 SAÍDAS</button>
                </div>

                {{-- Tabela --}}
                <div style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                    <table class="table table-sm align-middle table-borderless">
                        <thead class="sticky-top bg-white">
                            <tr class="text-muted small text-uppercase" style="font-size: 0.65rem; border-bottom: 2px solid #f1f1f1;">
                                <th>Data/Horário</th>
                                <th>Lote / Perfil</th>
                                <th>Local</th>
                                <th class="text-end">Quantidade</th>
                            </tr>
                        </thead>
                        <tbody id="hist-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>