@extends('layouts.app')

@section('title', 'Gestão de Torrefação')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    :root { 
        --p-yellow: #FFD600; 
        --p-brown: #4E342E; 
        --p-purple: #9C27B0; 
        --coffee-gold: #C0A16B;
    }
    
    /* --- BLOQUEIO DE SELEÇÃO AZUL --- */
    .dragging-active, .dragging-active * {
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
    }

    /* Layout Geral */
    .kpi-mini { padding: 0.5rem 0.8rem; border-radius: 10px; background: #fff; border: 1px solid #eee; height: 75px; display: flex; flex-direction: column; justify-content: center; transition: all 0.3s; overflow: hidden; }
    .kpi-mini .val { font-size: 1.1rem; font-weight: 800; color: var(--p-brown); line-height: 1.2; }
    .btn-add-kpi { height: 75px; border-radius: 10px; border: none; background: #198754; color: white; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; transition: 0.3s; }

    /* Navegação de Máquinas */
    .machine-nav-container { 
        position: relative; background: #fff; border-radius: 12px; padding: 0;
        border: 1px solid #e0e0e0; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex; align-items: center; height: 60px; overflow: hidden;
    }
    .machine-track-wrapper { overflow: hidden; width: 100%; padding: 0 45px; }
    .machine-track { display: flex; gap: 10px; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .btn-machine-select { 
        flex: 0 0 calc(33.333% - 7px); background: #f8f9fa; border: 1px solid #eee; 
        border-radius: 8px; padding: 5px 10px; cursor: pointer; height: 45px; 
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s ease; color: #666; white-space: nowrap;
    }
    .btn-machine-select.active { border-color: var(--p-brown); background: var(--p-brown); color: #fff; box-shadow: 0 4px 10px rgba(78, 52, 46, 0.2); }

    .nav-arrow { 
        position: absolute; top: 0; bottom: 0; width: 40px; background: #fdfdfd; 
        color: var(--p-brown); border: none; display: flex; align-items: center; 
        justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 1.3rem; z-index: 10;
    }
    .nav-arrow#prevBtn { left: 0; border-right: 1px solid #eee; }
    .nav-arrow#nextBtn { right: 0; border-left: 1px solid #eee; }

    /* Kanban e Cards */
    .kanban-col { background: #EBE7E0; border-radius: 15px; padding: 15px; min-height: 650px; }
    .card-roast { 
        border-left-width: 10px !important; border-left-style: solid !important; 
        background: white; border-radius: 12px; 
        transition: transform 0.2s ease, box-shadow 0.2s ease; 
        border-top:none; border-right:none; border-bottom:none; position: relative; 
    }
    
    .sortable-ghost { opacity: 0.4; background-color: #d1d1d1 !important; border: 2px dashed #666 !important; }
    .sortable-chosen { box-shadow: 0 15px 30px rgba(0,0,0,0.2) !important; }
    .sortable-drag { opacity: 0.95; transform: rotate(3deg) scale(1.02); cursor: grabbing !important; }

    .expand-trigger { position: absolute; top: 12px; right: 12px; cursor: pointer; color: #adb5bd; transition: 0.2s; z-index: 10; font-size: 1.1rem; }
    .drag-handle { width: 30px; height: 30px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: grab; color: #666; margin-right: 20px; transition: 0.2s; }
    .drag-handle:hover { background: var(--p-brown); color: white; }

    /* MONITOR */
    .monitor-foco { background: #fff; border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .monitor-header { background: #f8f9fa; padding: 5px 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .monitor-body { padding: 12px; display: flex; align-items: center; justify-content: center; gap: 20px; }
    .cycle-number-med { font-size: 1.8rem; font-weight: 900; color: var(--coffee-gold); line-height: 1; display: flex; flex-direction: column; align-items: center; }
    .cycle-number-med small { font-size: 0.55rem; color: #999; text-transform: uppercase; margin-top: -2px; }
    .timer-display-med { font-family: 'Courier New', monospace; font-size: 2.2rem; font-weight: 800; color: #333; letter-spacing: -1px; line-height: 1; }
    .monitor-footer { background: #fdfdfd; padding: 6px 12px; border-top: 1px dashed #eee; display: flex; justify-content: space-around; }

    /* Estilos de Ciclo */
    .batch-item-drag { background: #fff; border: 1px solid #eee; border-radius: 6px; padding: 5px 8px; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center; cursor: grab; font-size: 0.7rem; }
    .batch-item-done-mini { background: #f8fdf9; border: 1px solid #e1eee2; border-radius: 6px; padding: 8px; margin-bottom: 8px; font-size: 0.6rem; transition: 0.2s; }
    .done-time-row { display: flex; justify-content: space-between; color: #777; margin-bottom: 4px; padding-bottom: 4px; border-bottom: 1px solid #eef3ef; }
    
    .tooltip-inner { background-color: var(--p-brown) !important; font-size: 0.95rem !important; padding: 12px 18px !important; max-width: 350px !important; border: 1px solid var(--coffee-gold); }
    .special-tour-badge { background: linear-gradient(45deg, #FFD600, #C0A16B); color: #4E342E; font-size: 0.55rem; font-weight: 900; padding: 2px 8px; border-radius: 50px; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 3px; }
    .tech-label { font-size: 0.5rem; color: #aaa; text-transform: uppercase; font-weight: 800; display: block; }
    .tech-val { font-size: 0.7rem; font-weight: 700; color: #444; line-height: 1.2; }
    .anim-pulse { animation: pulse-green 2s infinite; }
    @keyframes pulse-green { 0% { transform: scale(0.95); opacity: 0.7; } 70% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(0.95); opacity: 0.7; } }
</style>

<div class="fade-in px-2">
    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="row g-2 mb-3">
                <div class="col-md-3"><div class="kpi-mini shadow-sm"><small>Total Hoje</small><div class="val" id="kpi-global-weight">0,00kg</div></div></div>
                <div class="col-md-3"><div class="kpi-mini shadow-sm"><small id="kpi-machine-name">Volume Máquina</small><div class="val" id="kpi-machine-weight">0,00kg</div></div></div>
                
                {{-- KPI DE LIMPEZA DIVIDIDO EM DOIS --}}
                <div class="col-md-3">
                    <div class="kpi-mini shadow-sm p-0">
                        <div class="row g-0 h-100">
                            <div class="col-6 border-end d-flex flex-column justify-content-center ps-3">
                                <small style="font-size: 0.65rem;">Limpeza</small>
                                <div class="val"><span id="clean-current">0</span><small class="fs-6 text-muted">/<span id="clean-threshold">0</span></small></div>
                            </div>
                            <div class="col-6 d-flex flex-column justify-content-center ps-3 bg-light bg-opacity-50">
                            <small style="font-size: 0.65rem;">Última em</small>

                            <div class="val" style="font-size: 0.85rem; color: #666;" id="clean-last">--/--</div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3"><button class="btn-add-kpi shadow-sm w-100" data-bs-toggle="modal" data-bs-target="#modalCreateRoast"><i class="bi bi-plus-circle-fill me-1"></i> Nova Demanda</button></div>
            </div>
            <div class="machine-nav-container shadow-sm">
                <button class="nav-arrow" id="prevBtn" onclick="scrollMachines(-1)"><i class="bi bi-chevron-left"></i></button>
                <div class="machine-track-wrapper">
                    <div class="machine-track" id="machineTrack">
                        @foreach($machines as $machine)
                        {{-- ATUALIZADO: Passando a data da última limpeza para a função JS --}}
                        <div class="btn-machine-select shadow-sm @if($loop->first) active @endif" 
                             onclick="selectMachine(this, {
                                id: {{ $machine->id }}, 
                                name: '{{ $machine->name }}', 
                                current: {{ $machine->current_roasts }}, 
                                threshold: {{ $machine->cleaning_threshold }},
                                last_cleaning: '{{ $machine->last_cleaning_at ? $machine->last_cleaning_at->format('d/m H:i') : '--/--' }}'
                             })" 
                             data-machine-id="{{ $machine->id }}">
                             <span class="fw-bold text-uppercase" style="font-size: 0.75rem;">{{ $machine->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button class="nav-arrow" id="nextBtn" onclick="scrollMachines(1)"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bg-white p-3 rounded shadow-sm border" style="height: 180px;"><canvas id="roastChart"></canvas></div>
        </div>
    </div>

    @foreach($machines as $machine)
    <div class="machine-kanban-board" id="board-machine-{{ $machine->id }}" style="display: none;">
        <div class="row g-3">
            
            {{-- COLUNA 1: FILA --}}
            <div class="col-md-4">
                <div class="kanban-col shadow-sm">
                    <h6 class="fw-bold mb-3 text-uppercase opacity-50 small">Fila: A Torrar</h6>
                    <div class="sortable-list" id="col-pending-{{ $machine->id }}">
                        @foreach($demands->where('status', 'pending')->where('machine_id', $machine->id)->sortBy('position') as $demand)
                        <div class="card card-roast shadow-sm mb-3 demand-card" data-id="{{ $demand->id }}" style="border-left-color: {{ $demand->sensoryProfile->color_hex ?? '#ccc' }} !important;">
                            <i class="bi bi-chevron-down expand-trigger" data-bs-toggle="collapse" data-bs-target="#exp_pending_{{ $demand->id }}"></i>
                            <div class="p-3">
                                <div class="mb-2">
                                    <span class="fw-black text-uppercase d-block" style="font-size: 1.1rem; color: var(--p-brown); line-height: 1.1;">{{ $demand->sensoryProfile->name ?? 'PADRÃO' }}</span>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <div>
                                            <strong class="text-muted" style="font-size: 0.75rem;">LOTE: {{ $demand->lot->reference }}</strong> 
                                            <span class="badge-roast-level ms-1">{{ $demand->roast_level }}</span>
                                            <div style="font-size: 0.65rem;" class="mt-1">
                                                <i class="bi bi-person-fill"></i> {{ $demand->mestre->name ?? 'Não definido' }} | 
                                                <i class="bi bi-gear-fill"></i> {{ $demand->machine->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="drag-handle"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-end text-end">
                                    <div>
                                        <span class="date-label-small">Criado em:</span><br>
                                        <span class="date-text" style="font-size: 0.7rem; font-weight: bold;">
                                            {{ $demand->created_at->addHours(3)->timezone('America/Sao_Paulo')->format('d/m/Y') }} {{ $demand->created_at->addHours(3)->timezone('America/Sao_Paulo')->format('H:i:s') }}
                                        </span>
                                    </div>
                                    <b class="text-dark fs-5">{{ number_format($demand->total_weight_requested, 2, ',', '.') }}kg</b>
                                </div>
                            </div>
                            @if($demand->notes)
                                <div class="mt-2 p-1 bg-light border-start border-warning" style="font-size: 0.7rem; color: #555; border-left-width: 3px !important;">
                                    <i class="bi bi-chat-left-dots-fill me-1 text-warning"></i> {{ $demand->notes }}
                                </div>
                            @endif
                            <div class="collapse bg-white p-2 border-top" id="exp_pending_{{ $demand->id }}">
                                @foreach($demand->batches as $batch)
                                <div class="px-2 small py-1 border-bottom d-flex justify-content-between align-items-center" @if($batch->notes) data-bs-toggle="tooltip" title="OBSERVAÇÃO: {{ $batch->notes }}" @endif>
                                    <span>
                                        #{{ $batch->batch_number }}
                                        @if($batch->notes) <i class="bi bi-exclamation-triangle-fill ms-1 text-warning"></i> @endif
                                    </span>
                                    <span class="weight-discrete">{{ number_format($batch->weight_in, 2, ',', '.') }}kg</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- COLUNA 2: NO TORRADOR --}}
            <div class="col-md-4">
                <div class="kanban-col shadow-sm" style="background: rgba(78, 52, 46, 0.08); border: 2px solid var(--p-brown);">
                    <h6 class="fw-bold mb-3 text-uppercase text-dark small"><i class="bi bi-fire me-2"></i>No Torrador</h6>
                    <div id="col-active-{{ $machine->id }}">
                        @foreach($demands->whereIn('status', ['roasting', 'awaiting_split_confirmation'])->where('machine_id', $machine->id) as $demand)
                        @php 
                            $focusBatch = $demand->batches->whereNotIn('status', ['finished', 'discarded'])->sortBy('batch_number')->first();
                            $remainingBatches = $demand->batches->where('status', 'pending')->where('id', '!=', $focusBatch->id ?? 0)->sortBy('batch_number');
                            $doneBatchesOfDemand = $demand->batches->where('status', 'finished')->sortByDesc('finished_at');
                        @endphp
                        <div class="card card-roast shadow mb-3" style="border-left-color: {{ $demand->sensoryProfile->color_hex ?? '#4E342E' }} !important;">
                            <div class="p-3">
                                <div class="text-center mb-3">
                                    <span class="fw-black text-uppercase d-block" style="font-size: 1.2rem; color: var(--p-brown); line-height: 1.1;">{{ $demand->sensoryProfile->name ?? 'PADRÃO' }}</span>
                                    <div class="fw-bold text-muted small" style="font-size: 0.65rem;">LOTE: {{ $demand->lot->reference }} <span class="badge-roast-level ms-1">{{ $demand->roast_level }}</span></div>
                                    <div style="font-size: 0.65rem;" class="mt-1 fw-bold text-dark">
                                        <i class="bi bi-person-fill"></i> {{ $demand->mestre->name ?? 'N/A' }} | <i class="bi bi-gear-fill"></i> {{ $demand->machine->name ?? 'N/A' }}
                                    </div>
                                </div>
                                @if($demand->notes)
                                    <div class="mt-1 mb-2 p-1 bg-white border rounded" style="font-size: 0.65rem; color: #666; border-style: dashed !important;">
                                        <strong>Nota:</strong> {{ $demand->notes }}
                                    </div>
                                @endif

                                <div class="monitor-foco mb-3" @if($focusBatch && $focusBatch->notes) data-bs-toggle="tooltip" title="OBSERVAÇÃO ATUAL: {{ $focusBatch->notes }}" @endif>
                                    <div class="monitor-header">
                                        <div class="d-flex align-items-center">
                                            @if($focusBatch && $focusBatch->status == 'roasting')
                                                <span class="status-dot bg-success anim-pulse me-1"></span><span class="status-text-subtle text-success" style="font-size: 0.5rem;">ATIVO</span>
                                            @else
                                                <span class="status-dot bg-warning me-1"></span><span class="status-text-subtle" style="font-size: 0.5rem;">AGUARDANDO</span>
                                            @endif
                                            @if($focusBatch && $focusBatch->is_special_tour) <span class="special-tour-badge ms-2"><i class="bi bi-stars"></i> Rota Especial</span> @endif
                                        </div>
                                        <span class="fw-bold text-muted" style="font-size: 0.55rem;">MONITOR</span>
                                    </div>
                                    <div class="monitor-body">
                                        <div class="cycle-number-med">#{{ $focusBatch->batch_number ?? '0' }}<small>ciclo</small></div>
                                        <div class="timer-display-med timer-display" data-start="{{ ($focusBatch && $focusBatch->status == 'roasting' && $focusBatch->started_at) ? ($focusBatch->started_at->timestamp + 21600) : '' }}">00:00:00</div>
                                    </div>
                                    <div class="monitor-footer">
                                        <div class="tech-item text-center">
                                            <span class="tech-label">Início</span>
                                            <div class="tech-val">
                                                @if($focusBatch && $focusBatch->started_at)
                                                    {{ $focusBatch->started_at->addHours(3)->timezone('America/Sao_Paulo')->format('d/m/Y') }}<br>
                                                    {{ $focusBatch->started_at->addHours(3)->timezone('America/Sao_Paulo')->format('H:i:s') }}
                                                @else --:--:-- @endif
                                            </div>
                                        </div>
                                        <div class="tech-item text-center"><span class="tech-label">Carga</span><div class="tech-val">{{ number_format($focusBatch->weight_in ?? 0, 2, ',', '.') }}kg</div></div>
                                    </div>
                                </div>

                                <div class="row g-2 mt-2">
                                    <div class="col-5 border-end pe-2">
                                        <span class="col-title-split">A Fazer ({{ $remainingBatches->count() }})</span>
                                        <div class="batch-sortable-area" data-demand-id="{{ $demand->id }}">
                                            @foreach($remainingBatches as $batch)
                                                <div class="batch-item-drag shadow-sm" style="{{ $batch->is_special_tour ? 'border: 2px solid var(--coffee-gold) !important;' : '' }}" data-batch-id="{{ $batch->id }}" data-type="batch" @if($batch->notes) data-bs-toggle="tooltip" title="OBSERVAÇÃO: {{ $batch->notes }}" @endif>
                                                    <span>
                                                        #{{ $batch->batch_number }}
                                                        @if($batch->notes) <i class="bi bi-exclamation-triangle-fill ms-1 text-warning"></i> @endif
                                                    </span>
                                                    <b>{{ number_format($batch->weight_in, 2, ',', '.') }}kg</b>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-7 ps-2">
                                        <span class="col-title-split">Já Feitos ({{ $doneBatchesOfDemand->count() }})</span>
                                        <div>
                                            @foreach($doneBatchesOfDemand as $batch)
                                                @php 
                                                    $bStart = \Carbon\Carbon::parse($batch->started_at)->addHours(3);
                                                    $bEnd = \Carbon\Carbon::parse($batch->finished_at)->addHours(3);
                                                    $diff = $bStart->diff($bEnd);
                                                    $dur = sprintf('%02d:%02d:%02d', ($diff->h + ($diff->days * 24)), $diff->i, $diff->s);
                                                @endphp
                                                <div class="batch-item-done-mini shadow-sm" style="{{ $batch->is_special_tour ? 'border: 2px solid var(--coffee-gold) !important;' : '' }}" @if($batch->notes) data-bs-toggle="tooltip" title="OBSERVAÇÃO: {{ $batch->notes }}" @endif>
                                                    <div class="d-flex justify-content-between fw-bold border-bottom mb-1 pb-1">
                                                        <span>
                                                            #{{ $batch->batch_number }} 
                                                            @if($batch->is_special_tour) <i class="bi bi-stars text-warning ms-1" title="Rota Especial"></i> @endif
                                                            @if($batch->notes) <i class="bi bi-exclamation-triangle-fill ms-1 text-warning"></i> @endif
                                                        </span>
                                                        <span class="text-success">{{ $dur }}</span>
                                                    </div>
                                                    <div class="done-time-row">
                                                        <div class="d-flex flex-column text-start">
                                                            <small style="font-size: 0.5rem;">INÍCIO:</small>
                                                            <span style="font-size: 0.55rem; font-weight: bold;">{{ $bStart->timezone('America/Sao_Paulo')->format('H:i:s') }}</span>
                                                        </div>
                                                        <div class="d-flex flex-column text-end">
                                                            <small style="font-size: 0.5rem;">FIM:</small>
                                                            <span style="font-size: 0.55rem; font-weight: bold;">{{ $bEnd->timezone('America/Sao_Paulo')->format('H:i:s') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="done-weight-row">
                                                        <span class="tech-val">IN: {{ number_format($batch->weight_in, 2, ',', '.') }}kg</span>
                                                        <span class="tech-val text-success">OUT: {{ number_format($batch->weight_out, 2, ',', '.') }}kg</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- COLUNA 3: CONCLUÍDOS --}}
            <div class="col-md-4">
                <div class="kanban-col shadow-sm">
                    <h6 class="fw-bold mb-3 text-uppercase text-success small">Concluído</h6>
                    <div id="col-finished-{{ $machine->id }}">
                        @foreach($demands->where('machine_id', $machine->id)->where('status', 'finished') as $demand)
                        @php 
                            $doneBatches = $demand->batches->where('status', 'finished')->sortBy('batch_number');
                            $firstBatch = $doneBatches->first();
                            $lastBatch = $doneBatches->last();
                            $totalIn = $doneBatches->sum('weight_in');
                            $totalOut = $doneBatches->sum('weight_out');
                            $shrinkage = $totalIn > 0 ? (($totalIn - $totalOut) / $totalIn) * 100 : 0;
                            
                            $firstStartAdj = \Carbon\Carbon::parse($firstBatch->started_at)->addHours(3);
                            $lastEndAdj = \Carbon\Carbon::parse($lastBatch->finished_at)->addHours(3);
                            $totalDiff = $firstStartAdj->diff($lastEndAdj);
                            $totalDurationStr = sprintf('%02d:%02d:%02d', ($totalDiff->h + ($totalDiff->days * 24)), $totalDiff->i, $totalDiff->s);
                        @endphp
                        <div class="card card-roast shadow-sm mb-3" style="border-left-color: {{ $demand->sensoryProfile->color_hex ?? '#198754' }} !important;">
                            <i class="bi bi-chevron-down expand-trigger" data-bs-toggle="collapse" data-bs-target="#exp_fin_{{ $demand->id }}"></i>
                            <div class="p-3">
                                <span class="fw-black text-uppercase d-block" style="font-size: 1.1rem; color: var(--p-brown); line-height: 1.1;">{{ $demand->sensoryProfile->name ?? 'PADRÃO' }}</span>
                                <div style="font-size: 0.65rem;" class="mb-2">
                                    <i class="bi bi-person-fill"></i> {{ $demand->mestre->name ?? 'N/A' }} | <i class="bi bi-gear-fill"></i> {{ $demand->machine->name ?? 'N/A' }}
                                </div>
                                <div class="mb-2 p-2 bg-light border rounded shadow-xs text-center">
                                    <small class="tech-label">Intervalo da Demanda</small>
                                    <div class="d-flex justify-content-between mt-1" style="font-size: 0.65rem; font-weight: bold; color: #555;">
                                        <span>{{ $firstStartAdj->timezone('America/Sao_Paulo')->format('d/m/Y') }}<br>{{ $firstStartAdj->timezone('America/Sao_Paulo')->format('H:i:s') }}</span>
                                        <span class="align-self-center">➔</span>
                                        <span>{{ $lastEndAdj->timezone('America/Sao_Paulo')->format('d/m/Y') }}<br>{{ $lastEndAdj->timezone('America/Sao_Paulo')->format('H:i:s') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-1" style="font-size: 0.65rem; font-weight: 900; color: var(--p-brown);">
                                        <span><i class="bi bi-hourglass-split me-1"></i> Duração Total:</span>
                                        <span>{{ $totalDurationStr }}</span>
                                    </div>
                                </div>
                                <div class="row text-center g-0 bg-white border rounded py-2 shadow-sm mb-2">
                                    <div class="col-4 border-end"><span class="tech-label">Peso Inicial</span><span class="tech-val">{{ number_format($totalIn, 2, ',', '.') }}kg</span></div>
                                    <div class="col-4 border-end"><span class="tech-label text-success">Peso Final</span><span class="tech-val text-success">{{ number_format($totalOut, 2, ',', '.') }}kg</span></div>
                                    <div class="col-4"><span class="tech-label text-danger">Quebra</span><span class="shrink-text">{{ number_format($shrinkage, 2, ',', '.') }}%</span></div>
                                </div>
                            </div>
                                @if($demand->notes)
                                    <div class="mb-2 p-1 border-start border-secondary" style="font-size: 0.65rem; color: #777; background: #fdfdfd;">
                                        <strong>Obs:</strong> {{ $demand->notes }}
                                    </div>
                                @endif

                            <div class="collapse bg-light p-2 border-top" id="exp_fin_{{ $demand->id }}">
                                @foreach($doneBatches as $batch)
                                @php
                                    $batchShrinkage = $batch->weight_in > 0 ? (($batch->weight_in - $batch->weight_out) / $batch->weight_in) * 100 : 0;
                                    $bS = \Carbon\Carbon::parse($batch->started_at)->addHours(3);
                                    $bE = \Carbon\Carbon::parse($batch->finished_at)->addHours(3);
                                    $bDiff = $bS->diff($bE);
                                    $bDuration = sprintf('%02d:%02d:%02d', $bDiff->h, $bDiff->i, $bDiff->s);
                                @endphp
                                <div class="batch-detail-card mb-2 bg-white p-2 border rounded shadow-xs" style="{{ $batch->is_special_tour ? 'border: 2px solid var(--coffee-gold) !important;' : '' }}" @if($batch->notes) data-bs-toggle="tooltip" title="OBSERVAÇÃO: {{ $batch->notes }}" @endif>
                                    <div class="d-flex justify-content-between fw-bold mb-1 small border-bottom pb-1">
                                        <span>
                                            #{{ $batch->batch_number }} 
                                            @if($batch->is_special_tour) <i class="bi bi-stars text-warning ms-1" title="Rota Especial"></i> @endif
                                            @if($batch->notes) <i class="bi bi-exclamation-triangle-fill ms-1 text-warning"></i> @endif
                                        </span>
                                        <span class="text-success" style="font-size: 0.6rem;"><i class="bi bi-stopwatch me-1"></i>{{ $bDuration }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1 text-center" style="font-size: 0.6rem; color: #777;">
                                        <div>{{ $bS->timezone('America/Sao_Paulo')->format('H:i:s') }}</div>
                                        <div>➔</div>
                                        <div>{{ $bE->timezone('America/Sao_Paulo')->format('H:i:s') }}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-1" style="font-size: 0.65rem;">
                                        <span><b>Inicial:</b> {{ number_format($batch->weight_in, 2, ',', '.') }}kg</span>
                                        <span><b>Final:</b> {{ number_format($batch->weight_out, 2, ',', '.') }}kg</span>
                                        <span class="text-danger"><b>-{{ number_format($batchShrinkage, 2, ',', '.') }}%</b></span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    const serverTimestampAtLoad = {{ now('America/Sao_Paulo')->addHours(3)->timestamp }};
    const clientTimestampAtLoad = Math.floor(Date.now() / 1000);
    const timeOffsetSeconds = serverTimestampAtLoad - clientTimestampAtLoad;

    function updateTimers() {
        document.querySelectorAll('.timer-display').forEach(timer => {
            const startTimestamp = timer.getAttribute('data-start');
            if (!startTimestamp || startTimestamp === "") return;
            const adjustedNow = Math.floor(Date.now() / 1000) + timeOffsetSeconds;
            const diffSeconds = adjustedNow - parseInt(startTimestamp);
            if (diffSeconds >= 0) {
                const h = Math.floor(diffSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((diffSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (diffSeconds % 60).toString().padStart(2, '0');
                timer.innerText = `${h}:${m}:${s}`;
            }
        });
    }
    setInterval(updateTimers, 1000);

    function selectMachine(btn, data) {
        document.querySelectorAll('.btn-machine-select').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.machine-kanban-board').forEach(b => b.style.display = 'none');
        const board = document.getElementById('board-machine-' + data.id);
        if(board) board.style.display = 'block';
        document.getElementById('kpi-machine-name').innerText = "Volume " + data.name;
        document.getElementById('clean-current').innerText = data.current;
        document.getElementById('clean-threshold').innerText = data.threshold;
        // ATUALIZADO: Atualiza o campo da última limpeza
        document.getElementById('clean-last').innerText = data.last_cleaning || '--/--';
    }

    let currentPos = 0;
    function scrollMachines(dir) {
        const track = document.getElementById('machineTrack');
        const btns = document.querySelectorAll('.btn-machine-select');
        if (btns.length <= 3) return;
        const itemWidth = btns[0].offsetWidth + 10;
        currentPos += dir;
        if (currentPos < 0) currentPos = 0;
        if (currentPos > btns.length - 3) currentPos = btns.length - 3;
        track.style.transform = `translateX(-${currentPos * itemWidth}px)`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tEl => new bootstrap.Tooltip(tEl));

        document.querySelectorAll('.sortable-list').forEach(el => {
            new Sortable(el, { 
                handle: '.drag-handle', 
                animation: 200,
                easing: "ease-out",
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                forceFallback: true,
                fallbackTolerance: 3,
                onStart: function() { document.documentElement.classList.add('dragging-active'); },
                onEnd: function() {
                    document.documentElement.classList.remove('dragging-active');
                    const order = Array.from(el.querySelectorAll('.demand-card')).map((card, index) => {
                        return { id: card.dataset.id, position: index + 1 };
                    });
                    fetch("{{ route('roasting.update-order') }}", { 
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, 
                        body: JSON.stringify({ order: order }) 
                    });
                }
            });
        });

        document.querySelectorAll('.batch-sortable-area').forEach(el => {
            new Sortable(el, { group: { name: 'batches', pull: true, put: false }, animation: 250 });
        });

        const first = document.querySelector('.btn-machine-select');
        if(first) first.click();
    });
</script>

@include('roasting.partials.modal_create_roast_demand')
@endsection