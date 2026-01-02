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
                    {{-- Coluna Esquerda: Dados e Torra --}}
                    <div class="col-md-7 p-4 border-end">
                        <h6 class="text-uppercase fw-bold text-muted mb-4 small"><i class="bi bi-info-circle me-1"></i> Especificações</h6>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Produtor / Origem</small>
                                <span class="h5 fw-bold text-dark">{{ $lote->origin }}</span>
                            </div>

                            {{-- LISTA DE LOCAIS MULTI-ENDEREÇO COM DETALHES DE QUEBRA --}}
                            <div class="col-12">
                                <small class="text-muted d-block mb-2">Localização no Armazém</small>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($lote->locations as $loc)
                                        <div class="bg-dark text-white px-3 py-2 rounded shadow-sm border border-secondary">
                                            <i class="bi bi-geo-alt me-1 text-warning"></i>
                                            <strong>{{ $loc->address_row }}{{ $loc->address_pos }}</strong> 
                                            <div class="small opacity-75 mt-1">
                                                <span class="badge bg-light text-dark">{{ $loc->quantity_bags }} sacas cheias</span>
                                                @if($loc->remainder_weight > 0)
                                                    <span class="badge bg-danger text-white">+ 1 quebra ({{ number_format($loc->remainder_weight, 1, ',', '.') }}kg)</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Data de Entrada</small>
                                <span class="fw-bold text-dark">{{ $lote->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <div class="col-12">
                                <div class="p-3 rounded-4 bg-white shadow-sm border">
                                    <small class="text-muted d-block mb-2">Volume Atual Consolidado</small>
                                    <div class="d-flex align-items-end gap-2">
                                        <h2 class="fw-bold mb-0 text-success">{{ $lote->quantity_bags }} <small class="fs-6 text-muted">Sacas (Total)</small></h2>
                                        <span class="h3 fw-bold mb-0 text-muted ms-auto">{{ number_format($lote->total_weight, 1, ',', '.') }}kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="text-uppercase fw-bold text-muted mb-3 small"><i class="bi bi-fire me-1"></i> Histórico de Torra (7 dias)</h6>
                        <div class="bg-white rounded-3 border p-2 shadow-sm overflow-hidden">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 0.75rem;">
                                <thead class="border-bottom text-muted"><tr><th>DATA</th><th>MÁQUINA</th><th class="text-end">PESO</th></tr></thead>
                                <tbody>
                                    @forelse($lote->roastingBatches->where('created_at', '>=', now()->subDays(7)) as $torra)
                                        <tr><td class="fw-bold">{{ $torra->created_at->format('d/m H:i') }}</td><td>{{ $torra->machine->name ?? 'N/A' }}</td><td class="text-end fw-bold text-danger">-{{ number_format($torra->input_weight, 1, ',', '.') }}kg</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-3 text-muted italic">Sem torras identificadas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Coluna Direita: Perfil e ANEXOS --}}
                    <div class="col-md-5 p-4 bg-white">
                        <h6 class="text-uppercase fw-bold text-muted mb-3 small"><i class="bi bi-cup-hot me-1"></i> Perfil Sensorial</h6>
                        @if($lote->sensoryProfile)
                            <div class="p-3 rounded-3 d-flex align-items-center gap-3 mb-5" style="background-color: {{ $lote->sensoryProfile->color_hex }}15; border: 1px solid {{ $lote->sensoryProfile->color_hex }}30;">
                                <div style="width: 10px; height: 35px; border-radius: 5px; background-color: {{ $lote->sensoryProfile->color_hex }};"></div>
                                <span class="fw-bold text-dark">{{ $lote->sensoryProfile->name }}</span>
                            </div>
                        @endif

                        <h6 class="text-uppercase fw-bold text-muted mb-3 small"><i class="bi bi-paperclip me-1"></i> Arquivos & Laudos</h6>
                        {{-- O style flex !important garante visibilidade contra interferências de scripts --}}
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden shadow-sm" style="display: flex !important;">
                            @forelse($lote->attachments as $file)
                                <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                                    <div class="text-truncate me-2" style="max-width: 120px;">
                                        <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                        <small class="fw-bold text-dark">{{ $file->file_name }}</small>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-light border p-1" title="Visualizar"><i class="bi bi-eye text-info"></i></a>
                                        <form action="{{ route('estoque.attachment.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Excluir este arquivo?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border p-1" title="Remover"><i class="bi bi-trash text-danger"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted bg-light small">Sem documentos vinculados.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-dark w-100 py-3 rounded-pill fw-bold" data-bs-dismiss="modal">CONCLUIR CONSULTA</button>
            </div>
        </div>
    </div>
</div>