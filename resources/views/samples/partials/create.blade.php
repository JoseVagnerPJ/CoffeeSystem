<div class="modal fade" id="modalCreateSample" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Nova Amostra de Café</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('samples.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Nome/Apelido da Amostra</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ex: Lote Especial Geisha" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Data de Recebimento</label>
                            <input type="date" name="receipt_date" class="form-control" required value="{{ date('Y-m-d') }}" style="border-radius: 8px; height: 45px;">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase" style="color: var(--coffee-brown);">Fornecedor / Produtor</label>
                            <select name="origin_combined" class="select2-unique" required style="width: 100%">
                                <option value=""></option>
                                <optgroup label="Empresas Ativas">
                                    @foreach($companies as $c)
                                        <option value="{{ $c->id }}|App\Models\Company">{{ $c->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Produtores Ativos">
                                    @foreach($producers as $p)
                                        <option value="{{ $p->id }}|App\Models\Producer">{{ $p->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Fazendas Ativas">
                                    @foreach($farms as $f)
                                        <option value="{{ $f->id }}|App\Models\Farm">{{ $f->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">ID Lote Fornecedor</label>
                            <input type="text" name="supplier_batch" class="form-control" required style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Variedade</label>
                            <input type="text" name="variety" class="form-control" required style="border-radius: 8px; height: 45px;">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted">Processo de secagem</label>
                            <input type="text" name="drying_process" class="form-control" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Fermentação</label>
                            <input type="text" name="fermentation" class="form-control" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Umidade (%)</label>
                            <input type="number" step="0.01" name="humidity" class="form-control" style="border-radius: 8px; height: 45px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancelar</button>
                    <button type="submit" class="btn text-white px-4" style="background-color: #3d2b1f; border-radius: 8px;">Salvar Amostra</button>
                </div>
            </form>
        </div>
    </div>
</div>