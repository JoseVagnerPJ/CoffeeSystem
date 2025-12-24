<div class="modal fade" id="modalInfo{{ $lote->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            
            {{-- Cabeçalho com Cor de Café --}}
            <div class="modal-header border-0 p-4" style="background-color: var(--coffee-brown); border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-info-circle-fill text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Lote: {{ $lote->reference }}</h5>
                        <small style="color: var(--coffee-gold);">ID do Sistema: #{{ $lote->id }} | Localização: {{ $lote->address_row }}{{ $lote->address_pos }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    
                    {{-- Coluna da Esquerda: Dados e Histórico --}}
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--coffee-brown);">Dados de Recebimento</h6>
                        
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Data de Entrada:</span>
                                <span class="small fw-bold">{{ $lote->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Responsável:</span>
                                <span class="small fw-bold">{{ $lote->user->name ?? 'Sistema' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Origem:</span>
                                <span class="small fw-bold">{{ $lote->origin }}</span>
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--coffee-brown);">Ações de Qualidade</h6>
                        <div class="d-grid gap-2">
                            {{-- Botão Criar Amostra --}}
                            <button class="btn btn-outline-primary btn-sm text-start py-2">
                                <i class="bi bi-flask me-2"></i> Criar Amostra de Laboratório
                            </button>
                            {{-- Botão Torrefação --}}
                            <button class="btn btn-outline-secondary btn-sm text-start py-2" disabled title="Aguardando Vínculo">
                                <i class="bi bi-fire me-2"></i> Demandas de Torra Vinculadas
                            </button>
                        </div>
                    </div>

                    {{-- Coluna da Direita: Vínculos e Anexos --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--coffee-brown);">Documentos e Anexos</h6>
                        
                        {{-- Área de Anexos (Estilo Pastas que você sugeriu) --}}
                        <div class="list-group list-group-flush mb-4">
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                                <span><i class="bi bi-folder2-open me-2 text-warning"></i> Curvas Artisan</span>
                                <span class="badge rounded-pill bg-light text-dark border">0</span>
                            </div>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                                <span><i class="bi bi-folder2-open me-2 text-warning"></i> Laudos de Prova</span>
                                <span class="badge rounded-pill bg-light text-dark border">0</span>
                            </div>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                <span><i class="bi bi-folder2-open me-2 text-warning"></i> Notas Fiscais / Fotos</span>
                                <span class="badge rounded-pill bg-light text-dark border">0</span>
                            </div>
                        </div>

                        <div class="alert alert-light border shadow-sm small py-2">
                            <i class="bi bi-info-circle me-1"></i> Use o botão de <strong>clipe (📎)</strong> na tabela para vincular arquivos a este lote.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rodapé: Exclusão Segura e Fechar --}}
            <div class="modal-footer border-0 p-4 bg-light rounded-bottom d-flex justify-content-between">
                {{-- Botão de Excluir escondido aqui para evitar acidentes --}}
                <form action="{{ route('estoque.destroy', $lote->id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Isso excluirá o lote e todos os seus vínculos permanentemente. Confirma?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger text-decoration-none small p-0 fw-bold">
                        <i class="bi bi-trash3 me-1"></i> EXCLUIR LOTE
                    </button>
                </form>

                <button type="button" class="btn btn-dark px-4 rounded-pill fw-bold" data-bs-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>
</div>