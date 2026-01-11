<div class="kanban-col">
    <h6 class="fw-bold mb-3 text-uppercase opacity-50 small">Fila: A Torrar</h6>
    <div id="col-pending">
        @foreach($demands->where('status', 'pending') as $demand)
        <div class="card card-roast border-0 shadow-sm mb-3 border-pending demand-card" data-machine="{{ $demand->machine_id }}" data-weight="{{ $demand->total_weight_requested }}">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <strong class="text-dark">
                        <i class="bi bi-chevron-down small me-1" data-bs-toggle="collapse" data-bs-target="#exp_{{ $demand->id }}" style="cursor:pointer"></i>
                        {{ $demand->lot->reference }}
                    </strong>
                    <span class="badge bg-light text-dark border small" style="font-size: 0.55rem; border-radius: 4px;">{{ $demand->roast_level }}</span>
                </div>
                
                <div class="mt-2 d-flex align-items-center gap-2">
                    @if($demand->sensoryProfile)
                    <div style="width:12px; height:12px; border-radius:3px; background-color:{{ $demand->sensoryProfile->color_hex }}; border:1px solid rgba(0,0,0,0.1);"></div>
                    @endif
                    <span class="badge badge-machine" style="font-size: 0.6rem;">{{ $demand->machine->name }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-end mt-3">
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size: 0.55rem;">DEMANDA</small>
                        <div class="fw-bold" style="color: var(--coffee-brown);">{{ number_format($demand->total_weight_requested, 1, ',', '.') }}kg</div>
                    </div>
                    <div class="bg-dark text-warning d-flex align-items-center justify-content-center rounded-3 fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.8rem;" title="Total de Ciclos">
                        {{ $demand->batches->count() }}
                    </div>
                </div>
            </div>
            
            <div class="collapse bg-white p-2 border-top" id="exp_{{ $demand->id }}" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                @foreach($demand->batches as $batch)
                <div class="px-2 small py-1 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted">Ciclo #{{ $batch->batch_number }}</span>
                    <span class="fw-bold">{{ number_format($batch->weight_in, 1, ',', '.') }}kg</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="kanban-col" style="background: rgba(192, 161, 107, 0.15); border: 1px solid var(--coffee-gold);">
    <h6 class="fw-bold mb-3 text-uppercase text-dark small">No Torrador (Ativo)</h6>
    <div id="col-active">
        @foreach($demands->where('status', 'roasting') as $demand)
        <div class="card card-roast border-0 shadow mb-3 border-roasting demand-card" data-machine="{{ $demand->machine_id }}">
            <div class="p-3 text-center">
                <small class="text-danger fw-bold text-uppercase d-block mb-2 anim-pulse" style="font-size: 0.6rem;">
                    <i class="bi bi-record-circle-fill"></i> Torrando...
                </small>
                <h5 class="fw-bold mb-1" style="color: var(--coffee-brown);">{{ $demand->lot->reference }}</h5>
                <div class="d-flex justify-content-center gap-2 mt-2">
                    <span class="badge bg-dark">{{ $demand->machine->name }}</span>
                    <span class="badge border text-dark bg-white">{{ $demand->roast_level }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="kanban-col">
    <h6 class="fw-bold mb-3 text-uppercase text-success small">Finalizado (Hoje)</h6>
    <div id="col-finished">
        @foreach($demands->where('status', 'finished')->where('updated_at', '>=', now()->startOfDay()) as $demand)
        <div class="card card-roast border-0 shadow-sm mb-3 border-completed demand-card" data-machine="{{ $demand->machine_id }}" data-weight="{{ $demand->total_weight_requested }}">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="text-muted">{{ $demand->lot->reference }}</strong>
                    <div class="text-success d-flex align-items-center gap-1 small fw-bold">
                        <i class="bi bi-check-circle-fill"></i> CONCLUÍDO
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    Total: <b>{{ number_format($demand->total_weight_requested, 1, ',', '.') }}kg</b>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>