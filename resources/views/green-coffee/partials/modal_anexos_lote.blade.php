<div class="modal fade" id="modalAnexos{{ $lote->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-light rounded-top p-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-paperclip me-2 text-info"></i>Gerenciar Anexos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                {{-- Lista Atual com Botão Excluir --}}
                <div class="mb-4">
                    <label class="info-label text-muted mb-3">Arquivos do Lote: {{ $lote->reference }}</label>
                    <div class="list-group">
                        @forelse($lote->attachments as $file)
                            <div class="list-group-item d-flex justify-content-between align-items-center border rounded-3 mb-2 shadow-sm">
                                <div class="text-truncate" style="max-width: 60%;">
                                    <i class="bi bi-file-check me-2 text-success"></i>
                                    <span class="small fw-bold">{{ $file->file_name }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info border-0">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    {{-- FORMULÁRIO DE EXCLUSÃO --}}
                                    <form action="{{ route('estoque.attachment.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Excluir este arquivo?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center border rounded-3 border-dashed">
                                <small class="text-muted italic">Nenhum arquivo submetido.</small>
                            </div>
                        @endforelse
                    </div>
                </div>

                <form action="{{ route('estoque.attach', $lote->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-3 rounded-3" style="background-color: #f8f9fa; border: 2px dashed #dee2e6;">
                        <label class="info-label fw-bold mb-2">Subir Novo Documento</label>
                        <input type="file" name="file" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-info text-white w-100 rounded-pill fw-bold mt-2">EFETUAR UPLOAD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>