<div class="modal fade" id="modalMaintenanceMachine{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-light rounded-top p-4">
                <h5 class="fw-bold mb-0 text-secondary"><i class="bi bi-tools me-2"></i>Registrar Manutenção Técnica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('machines.storeMaintenance', $m->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4 text-uppercase fw-bold" style="font-size: 0.65rem;">Máquina: {{ $m->name }}</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="info-label">Data da Manutenção</label>
                            <input type="date" name="maintenance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label">Próxima Sugerida (Opcional)</label>
                            <input type="date" name="next_maintenance_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="info-label">Técnico / Empresa</label>
                            <input type="text" name="provider" class="form-control" placeholder="Ex: Assistência Técnica Probat" required>
                        </div>
                        <div class="col-12">
                            <label class="info-label">Descrição do Serviço</label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="O que foi feito?"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">REGISTRAR SERVIÇO</button>
                </div>
            </form>
        </div>
    </div>
</div>