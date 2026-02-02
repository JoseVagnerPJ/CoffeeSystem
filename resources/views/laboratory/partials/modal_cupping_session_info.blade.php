<div class="modal-content border-0 shadow-lg">
    <div class="modal-header text-white" style="background: var(--coffee-brown);">
        <h4 class="fw-bold mb-0">{{ $session->title }}</h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        {{-- KPIs SUPERIORES --}}
        <div class="row mb-4 text-center">
            <div class="col-md-3 border-end">
                <small class="text-muted d-block text-uppercase">Iniciado em</small>
                <span class="fw-bold text-success">{{ $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('H:i:s') : '--:--' }}</span>
                <div class="small text-muted">{{ $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('d/m/Y') : '' }}</div>
            </div>
            <div class="col-md-3 border-end">
                <small class="text-muted d-block text-uppercase">Finalizado em</small>
                <span class="fw-bold text-danger">{{ $session->finished_at ? \Carbon\Carbon::parse($session->finished_at)->format('H:i:s') : 'Em curso' }}</span>
                <div class="small text-muted">{{ $session->finished_at ? \Carbon\Carbon::parse($session->finished_at)->format('d/m/Y') : '' }}</div>
            </div>
            <div class="col-md-3 border-end">
                <small class="text-muted d-block text-uppercase">Protocolo</small>
                <span class="fw-bold text-dark">{{ $session->profile->name }}</span>
                <div class="small text-muted">SCA</div>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block text-uppercase">Total Xícaras</small>
                <span class="fw-bold h4">{{ $session->items->count() }}</span>
            </div>
        </div>

        {{-- SEÇÃO DE EXPORTAÇÃO PDF --}}
        <div class="card border-primary mb-4" style="border-radius: 12px; background-color: #f0f7ff;">
            <div class="card-body">
                <h6 class="fw-bold small text-uppercase mb-3 text-primary"><i class="bi bi-file-pdf-fill me-1"></i> Exportar Relatório de Prova</h6>
                <div class="row g-2">
                    <div class="col-md-8">
                        {{-- ADICIONADO data-session-id PARA O JS GLOBAL --}}
                        <select id="selectProvadorPdf" data-session-id="{{ $session->id }}" class="form-select border-primary">
                            <option value="" disabled selected hidden>Selecione um provador...</option>
                            
                            @php
                                $internalTasters = $session->evaluations->whereNotNull('user_id')->groupBy('user_id');
                            @endphp

                            @if($internalTasters->count() > 0)
                                <optgroup label="Provadores Internos">
                                    @foreach($internalTasters as $userId => $evals)
                                        <option value="user_{{ $userId }}">{{ $evals->first()->user->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if($session->visitors->count() > 0)
                                <optgroup label="Visitantes">
                                    @foreach($session->visitors as $visitor)
                                        <option value="visitor_{{ $visitor->id }}">{{ $visitor->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100 fw-bold" onclick="window.gerarRelatorioPdf()">
                            <i class="bi bi-download me-1"></i> Gerar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- QR CODE / LINK --}}
        @if($session->status !== 'finalizado')
        <div class="card bg-light border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body">
                <div class="row align-items-center text-center text-md-start">
                    <div class="col-md-5 border-end text-center mb-3 mb-md-0">
                        <h6 class="fw-bold small text-uppercase mb-3 text-muted">Acesso via QR-Code</h6>
                        <div id="qrcode-container" class="bg-white p-3 d-inline-block shadow-sm mb-2" style="border-radius: 10px;">
                            <div id="qrcode"></div>
                        </div>
                    </div>
                    <div class="col-md-7 ps-md-4">
                        <h6 class="fw-bold small text-uppercase mb-2 text-muted">Link da Mesa</h6>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control border-0 bg-white fw-bold" readonly value="{{ url('/prova/v1/' . $session->uuid) }}" id="linkVisitante">
                            <button class="btn btn-dark" onclick="copyLink()"><i class="bi bi-clipboard"></i></button>
                        </div>
                        <p class="text-muted small mb-0">Inicie a prova no celular.</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-secondary border-0 mb-4 py-3" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-lock-fill fs-4 me-3 text-muted"></i>
                <div>
                    <h6 class="mb-0 fw-bold">Mesa Finalizada</h6>
                    <small>Acesso desativado em {{ \Carbon\Carbon::parse($session->finished_at)->format('d/m/Y H:i') }}.</small>
                </div>
            </div>
        </div>
        @endif

        {{-- LISTAS DE PROVADORES (VISUALIZAÇÃO RÁPIDA) --}}
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold text-dark border-bottom pb-2">Internos</h6>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @forelse($internalTasters as $userId => $evals)
                        <span class="badge bg-white text-dark border px-2 py-1 fw-normal">{{ $evals->first()->user->name }}</span>
                    @empty
                        <small class="text-muted">Nenhum registro.</small>
                    @endforelse
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-dark border-bottom pb-2">Visitantes</h6>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @forelse($session->visitors as $visitor)
                        <span class="badge bg-white text-dark border px-2 py-1 fw-normal" style="border-left: 3px solid var(--coffee-gold) !important;">{{ $visitor->name }}</span>
                    @empty
                        <small class="text-muted">Nenhum visitante.</small>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    // Inicialização do QR Code (Isso funciona aqui pois é auto-executável na injeção)
    (function() {
        @if($session->status !== 'finalizado')
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ url('/prova/v1/' . $session->uuid) }}",
                width: 120,
                height: 120,
                colorDark : "#4b3621",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        @endif
    })();

    function copyLink() {
        var copyText = document.getElementById("linkVisitante");
        copyText.select();
        navigator.clipboard.writeText(copyText.value);
        alert("Link copiado!");
    }
</script>