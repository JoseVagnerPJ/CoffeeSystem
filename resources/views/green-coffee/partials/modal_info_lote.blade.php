<div class="modal fade" id="modalInfo{{ $lote->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            
            <div class="modal-header border-0 p-4 text-white" style="background: linear-gradient(135deg, #2c1e12 0%, #1a120b 100%);">
                <div class="d-flex align-items-center">
                    <div class="bg-white p-1 rounded-circle me-3 shadow-sm">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: {{ $lote->sensoryProfile->color_hex ?? '#eee' }}; border: 2px solid #ddd;"></div>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Ficha Técnica de Qualidade</h5>
                        <small class="opacity-75">#{{ str_pad($lote->id, 4, '0', STR_PAD_LEFT) }} | Ref: {{ $lote->reference }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0 bg-light">
                <div class="row g-0">
                    {{-- Coluna Esquerda --}}
                    <div class="col-md-7 p-4 border-end">
                        <h6 class="text-uppercase fw-bold text-muted mb-4 small"><i class="bi bi-info-circle me-1"></i> Especificações</h6>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Produtor / Origem</small>
                                <span class="h5 fw-bold text-dark">{{ $lote->origin }}</span>
                            </div>

                            <div class="col-12">
                                <small class="text-muted d-block mb-2">Localização no Armazém</small>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($lote->locations as $loc)
                                        <div class="bg-dark text-white px-3 py-2 rounded shadow-sm border border-secondary">
                                            <i class="bi bi-geo-alt me-1 text-warning"></i>
                                            <strong>{{ $loc->address_row }}{{ $loc->address_pos }}</strong> 
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 rounded-4 bg-white shadow-sm border">
                                    <small class="text-muted d-block mb-2">Volume Atual Consolidado</small>
                                    <div class="d-flex align-items-end gap-2">
                                        <h2 class="fw-bold mb-0 text-success">{{ $lote->quantity_bags }} <small class="fs-6 text-muted">Sacas</small></h2>
                                        <span class="h3 fw-bold mb-0 text-muted ms-auto">{{ number_format($lote->total_weight, 1, ',', '.') }}kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- HISTÓRICO DE SAÍDAS COM AJUSTE DE DATA/HORA --}}
                        <h6 class="text-uppercase fw-bold text-muted mb-3 small"><i class="bi bi-box-arrow-up me-1"></i> Histórico de Saídas (7 dias)</h6>
                        <div class="bg-white rounded-3 border shadow-sm overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0 align-middle" style="font-size: 0.72rem;">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-3 py-2">DATA - HORA</th>
                                            <th>QTD</th>
                                            <th>END.</th>
                                            <th>JUSTIFICATIVA</th>
                                            <th class="text-center">OBS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lote->movements->where('type', 'out')->where('created_at', '>=', now()->subDays(7))->sortByDesc('created_at') as $saida)
                                            <tr>
                                                {{-- FORMATO: DATA - HORA:MIN:SEG --}}
                                                <td class="ps-3 fw-bold text-nowrap">{{ $saida->created_at->copy()->subHours(3)->format('d/m - H:i:s') }}</td>
                                                <td class="fw-bold text-danger text-nowrap">-{{ number_format($saida->weight, 1, ',', '.') }}kg</td>
                                                <td><span class="badge bg-secondary">{{ $saida->location->address_row ?? '' }}{{ $saida->location->address_pos ?? '' }}</span></td>
                                                <td class="text-truncate" style="max-width: 100px;">{{ $saida->reason }}</td>
                                                <td class="text-center">
                                                    @if($saida->notes)
                                                        <i class="bi bi-chat-left-text-fill text-primary" 
                                                           style="cursor: help; font-size: 1.1rem;"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-html="true"
                                                           title="<strong>Observação:</strong><br>{{ $saida->notes }}"></i>
                                                    @else
                                                        <span class="text-muted opacity-50">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center py-4 text-muted italic">Nenhuma saída recente.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Coluna Direita --}}
                    <div class="col-md-5 p-4 bg-white">
                        <h6 class="text-uppercase fw-bold text-muted mb-3 small"><i class="bi bi-cup-hot me-1"></i> Perfil Sensorial</h6>
                        @if($lote->sensoryProfile)
                            <div class="p-3 rounded-3 d-flex align-items-center gap-3 mb-5" style="background-color: {{ $lote->sensoryProfile->color_hex }}15; border: 1px solid {{ $lote->sensoryProfile->color_hex }}30;">
                                <div style="width: 10px; height: 35px; border-radius: 5px; background-color: {{ $lote->sensoryProfile->color_hex }};"></div>
                                <span class="fw-bold text-dark">{{ $lote->sensoryProfile->name }}</span>
                            </div>
                        @endif

                        <h6 class="text-uppercase fw-bold text-muted mb-3 small"><i class="bi bi-paperclip me-1"></i> Arquivos & Laudos</h6>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden shadow-sm">
                            @forelse($lote->attachments as $file)
                                <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                                    <div class="text-truncate me-2">
                                        <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                        <small class="fw-bold text-dark">{{ $file->file_name }}</small>
                                    </div>
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-light border p-1"><i class="bi bi-eye text-info"></i></a>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted bg-light small">Sem documentos.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-dark w-100 py-3 rounded-pill fw-bold" data-bs-dismiss="modal">FECHAR FICHA</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('modalInfo{{ $lote->id }}').addEventListener('shown.bs.modal', function () {
        var tooltipTriggerList = [].slice.call(this.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    })
</script>