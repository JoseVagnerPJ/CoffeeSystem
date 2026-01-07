<div class="modal-content border-0 shadow-lg">
    <div class="modal-header text-white" style="background: var(--coffee-brown);">
        <h5 class="modal-title fw-bold">Detalhes da Mesa #{{ $session->id }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        <div class="row mb-4 text-center">
            <div class="col-md-4 border-end">
                <small class="text-muted d-block text-uppercase">Iniciado em</small>
                <span class="fw-bold text-success">{{ $session->started_at ? $session->started_at->format('H:i:s') : '--:--' }}</span>
                <div class="small text-muted">{{ $session->started_at ? $session->started_at->format('d/m/Y') : '' }}</div>
            </div>
            <div class="col-md-4 border-end">
                <small class="text-muted d-block text-uppercase">Finalizado em</small>
                <span class="fw-bold text-danger">{{ $session->finished_at ? $session->finished_at->format('H:i:s') : 'Em curso' }}</span>
                <div class="small text-muted">{{ $session->finished_at ? $session->finished_at->format('d/m/Y') : '' }}</div>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block text-uppercase">Total Xícaras</small>
                <span class="fw-bold h4">{{ $session->items->count() }}</span>
            </div>
        </div>

        <h6 class="fw-bold text-dark border-bottom pb-2">Provadores Participantes ({{ $session->evaluations->groupBy('user_id')->count() }})</h6>
        <div class="d-flex flex-wrap gap-2 mt-3 mb-4">
            @foreach($session->evaluations->groupBy('user_id') as $userId => $evals)
                <span class="badge bg-light text-dark border px-3 py-2 fw-normal">
                    <i class="bi bi-person-fill me-1 text-muted"></i> {{ $evals->first()->user->name }}
                </span>
            @endforeach
        </div>

        @if($session->notes)
            <div class="alert alert-secondary border-0 shadow-sm mb-0">
                <small class="fw-bold text-uppercase d-block mb-1">Observações Gerais:</small>
                <p class="mb-0 small italic">"{{ $session->notes }}"</p>
            </div>
        @endif
    </div>
</div>