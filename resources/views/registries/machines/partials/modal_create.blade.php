<div class="modal fade" id="modalCreateMachine" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        {{-- ROTA ATUALIZADA --}}
        <form action="{{ route('registries.machines.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @csrf
            <div class="modal-header border-0 bg-light rounded-top">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);"><i class="bi bi-nut me-2"></i>Cadastrar Novo Torrador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    {{-- Identificação --}}
                    <div class="col-md-12">
                        <label class="info-label">Nome de Identificação (Apelido)</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Ex: Probat, CarmoMaq..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">Marca</label>
                        <input type="text" name="brand" class="form-control shadow-sm" placeholder="Ex: CarmoMaq" required>
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">Modelo</label>
                        <input type="text" name="model" class="form-control shadow-sm" placeholder="Ex: Caloratto" required>
                    </div>

                    {{-- Opcionais Técnicos --}}
                    <div class="col-md-6">
                        <label class="info-label">Ano de Fabricação</label>
                        <input type="number" name="year" class="form-control shadow-sm" placeholder="(Opcional)">
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">Número de Série </label>
                        <input type="text" name="serial_number" class="form-control shadow-sm" placeholder="(Opcional)">
                    </div>

                    <div class="col-12"><hr class="my-2 opacity-10"></div>

                    {{-- Capacidades --}}
                    <div class="col-md-4">
                        <label class="info-label">Capacidade Máxima (kg)</label>
                        <div class="input-group shadow-sm">
                            <input type="number" step="0.1" name="max_capacity" class="form-control" placeholder="25.0" required>
                            <span class="input-group-text bg-white">kg</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="info-label">Capacidade Mínima (kg)</label>
                        <div class="input-group shadow-sm">
                            <input type="number" step="0.1" name="min_capacity" class="form-control" placeholder="15.0" required>
                            <span class="input-group-text bg-white">kg</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="info-label text-primary">Alerta de Limpeza</label>
                        <div class="input-group shadow-sm">
                            <input type="number" name="cleaning_threshold" class="form-control" placeholder="40" required>
                            <span class="input-group-text bg-white">torras</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.6rem;">Avisar após X torras.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold" style="background: var(--coffee-brown);">SALVAR MÁQUINA</button>
            </div>
        </form>
    </div>
</div>