<style>
    /* Estilo do pegador circular (handle) */
    .drag-handle {
        width: 24px;
        height: 24px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: grab;
        color: #adb5bd;
        transition: all 0.2s ease;
        margin-top: 8px;
    }

    .drag-handle:hover {
        background-color: var(--coffee-brown);
        color: white;
        border-color: var(--coffee-brown);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    /* Estilo das observações (Post-it) */
    .notes-box {
        background-color: #fff9db;
        border-left: 3px solid #fcc419;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
        color: #444;
        margin-top: 10px;
    }

    .sortable-ghost {
        opacity: 0.3;
        border: 2px dashed var(--coffee-gold) !important;
        background: #fdfaf5 !important;
    }

    .sortable-chosen {
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>

<div class="row g-3">
    {{-- COLUNA: A TORRAR --}}
    <div class="kanban-col">
        <h6 class="fw-bold mb-3 text-uppercase opacity-50 small">Fila: A Torrar</h6>
        <div id="col-pending">
            @foreach($demands->where('status', 'pending') as $demand)
            <div class="card card-roast border-0 shadow-sm mb-3 demand-card" 
                 data-id="{{ $demand->id }}" 
                 data-machine="{{ $demand->machine_id }}" 
                 data-weight="{{ $demand->total_weight_requested }}"
                 style="border-left-color: {{ $demand->sensoryProfile->color_hex ?? '#FFD600' }} !important;">
                
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong class="text-dark d-block">
                                <i class="bi bi-chevron-down small me-1" data-bs-toggle="collapse" data-bs-target="#exp_{{ $demand->id }}" style="cursor:pointer"></i>
                                {{ $demand->lot->reference }}
                            </strong>
                            {{-- CAMPO LOCAL --}}
                            <small class="text-danger fw-bold" style="font-size: 0.65rem;">
                                <i class="bi bi-geo-alt-fill"></i> LOCAL: 
                                {{ $demand->lot->locations->map(fn($l) => $l->address_row . $l->address_pos)->join(', ') ?: 'N/A' }}
                            </small>
                        </div>
                        <span class="badge bg-light text-dark border small" style="font-size: 0.55rem; border-radius: 4px;">{{ $demand->roast_level }}</span>
                    </div>
                    
                    <div class="mb-2 d-flex align-items-start gap-2">
                        <div class="d-flex flex-column align-items-center">
                            @if($demand->sensoryProfile)
                                <div style="width:12px; height:12px; border-radius:3px; background-color:{{ $demand->sensoryProfile->color_hex }}; border:1px solid rgba(0,0,0,0.1);"></div>
                            @else
                                <div style="width:12px; height:12px; border-radius:3px; background-color:#ccc; border:1px solid rgba(0,0,0,0.1);"></div>
                            @endif
                            
                            <div class="drag-handle" title="Arraste para reordenar">
                                <i class="bi bi-arrows-move" style="font-size: 0.7rem;"></i>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            @if($demand->sensoryProfile)
                                <span class="fw-bold text-dark d-block" style="font-size: 0.7rem;">{{ $demand->sensoryProfile->name }}</span>
                            @else
                                <span class="text-muted small d-block" style="font-size: 0.65rem;">Sem perfil atribuído</span>
                            @endif
                            <span class="badge badge-machine mt-1" style="font-size: 0.6rem;">{{ $demand->machine->name }}</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="small text-muted" style="font-size: 0.65rem;">
                            <i class="bi bi-person-badge me-1"></i>Mestre: <b>{{ $demand->mestre->name ?? 'N/A' }}</b>
                        </div>
                        <div class="small text-muted" style="font-size: 0.65rem;">
                            <i class="bi bi-calendar3 me-1"></i>Criado em: <b>{{ $demand->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</b>
                        </div>
                    </div>

                    <div class="row g-0 text-center border rounded py-2 bg-light mt-2" style="border-style: dashed !important;">
                        <div class="col-4 border-end">
                            <small class="text-muted d-block" style="font-size: 0.5rem; font-weight: 800;">TOTAL</small>
                            <span class="fw-bold small" style="color: var(--coffee-brown);">{{ number_format($demand->total_weight_requested, 1, ',', '.') }}kg</span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted d-block" style="font-size: 0.5rem; font-weight: 800;">CICLO</small>
                            <span class="fw-bold small">{{ number_format($demand->partial_weight, 1, ',', '.') }}kg</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block" style="font-size: 0.5rem; font-weight: 800;">CICLOS</small>
                            <span class="badge bg-dark text-warning rounded-pill" style="font-size: 0.65rem;">{{ $demand->batches->count() }}x</span>
                        </div>
                    </div>

                    {{-- CAMPO OBSERVAÇÕES --}}
                    @if($demand->notes)
                        <div class="notes-box">
                            <strong>OBS:</strong> {{ $demand->notes }}
                        </div>
                    @endif
                </div>
                
                <div class="collapse bg-white p-2 border-top" id="exp_{{ $demand->id }}" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    @foreach($demand->batches as $batch)
                    <div class="px-2 small py-1 border-bottom d-flex justify-content-between align-items-center">
                        <span class="text-muted">Torra #{{ $batch->batch_number }}</span>
                        <span class="fw-bold">{{ number_format($batch->weight_in, 1, ',', '.') }}kg</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- COLUNA: NO TORRADOR (ATIVO) --}}
    <div class="kanban-col" style="background: rgba(192, 161, 107, 0.15); border: 1px solid var(--coffee-gold);">
        <h6 class="fw-bold mb-3 text-uppercase text-dark small">No Torrador (Ativo)</h6>
        <div id="col-active">
            @foreach($demands->where('status', 'roasting') as $demand)
            <div class="card card-roast border-0 shadow mb-3 demand-card" 
                 data-machine="{{ $demand->machine_id }}"
                 style="border-left-color: {{ $demand->sensoryProfile->color_hex ?? '#4E342E' }} !important;">
                <div class="p-3 text-center">
                    <small class="text-danger fw-bold text-uppercase d-block mb-1 anim-pulse" style="font-size: 0.6rem;">
                        <i class="bi bi-record-circle-fill"></i> Torrando...
                    </small>
                    <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">{{ $demand->lot->reference }}</h5>
                    <small class="text-danger fw-bold d-block mb-1" style="font-size: 0.6rem;">
                        LOCAL: {{ $demand->lot->locations->map(fn($l) => $l->address_row . $l->address_pos)->join(', ') ?: 'N/A' }}
                    </small>
                    <div class="fw-bold text-dark mt-1" style="font-size: 0.8rem;">{{ $demand->sensoryProfile->name ?? 'Sem Perfil' }}</div>
                    <div class="bg-white border rounded p-2 my-2">
                        <div class="row g-0">
                            <div class="col-6 border-end"><small class="d-block text-muted" style="font-size: 0.5rem">TOTAL</small><b>{{ number_format($demand->total_weight_requested, 1, ',', '.') }}kg</b></div>
                            <div class="col-6"><small class="d-block text-muted" style="font-size: 0.5rem">CICLO</small><b>{{ number_format($demand->partial_weight, 1, ',', '.') }}kg</b></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-dark">{{ $demand->machine->name }}</span>
                        <span class="badge border text-dark bg-white">{{ $demand->roast_level }}</span>
                    </div>
                    @if($demand->notes)
                        <div class="notes-box text-start">
                            <strong>OBS:</strong> {{ $demand->notes }}
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- COLUNA: FINALIZADO --}}
    <div class="kanban-col">
        <h6 class="fw-bold mb-3 text-uppercase text-success small">Finalizado (Hoje)</h6>
        <div id="col-finished">
            @foreach($demands->where('status', 'finished')->where('updated_at', '>=', now()->setTimezone('America/Sao_Paulo')->startOfDay()) as $demand)
            <div class="card card-roast border-0 shadow-sm mb-3 demand-card" 
                 style="border-left-color: {{ $demand->sensoryProfile->color_hex ?? '#9C27B0' }} !important;">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <strong class="text-muted d-block">{{ $demand->lot->reference }}</strong>
                            <small class="text-muted" style="font-size: 0.6rem;">
                                LOCAL: {{ $demand->lot->locations->map(fn($l) => $l->address_row . $l->address_pos)->join(', ') ?: 'N/A' }}
                            </small>
                        </div>
                        <div class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> OK</div>
                    </div>
                    <div class="mb-2 fw-bold text-muted small">Perfil: {{ $demand->sensoryProfile->name ?? 'N/A' }}</div>
                    <div class="bg-light rounded p-2 text-center text-success h5 mb-0 fw-bold">
                        {{ number_format($demand->total_weight_requested, 1, ',', '.') }}kg
                    </div>
                    @if($demand->notes)
                        <div class="mt-2 small text-muted italic">
                            <strong>Obs:</strong> {{ $demand->notes }}
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pendingCol = document.getElementById('col-pending');

        if (pendingCol) {
            new Sortable(pendingCol, {
                handle: '.drag-handle',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function (evt) {
                    const order = Array.from(pendingCol.querySelectorAll('.demand-card')).map((card, index) => {
                        return {
                            id: card.dataset.id,
                            position: index + 1
                        };
                    });
                    console.log('Nova Ordem Salva Localmente:', order);
                }
            });
        }
    });
</script>