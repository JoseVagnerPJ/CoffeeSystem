{{-- MODAL CADASTRO: PRODUTOR --}}
<div class="modal fade" id="modalProducer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('suppliers.storeProducer') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @csrf
            <div class="modal-header border-0 bg-light rounded-top">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">Novo Produtor (Pessoa)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="info-label">Nome Completo</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Ex: Carlos Oliveira" required>
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">CPF</label>
                        <input type="text" name="cpf" class="form-control shadow-sm" placeholder="000.000.000-00">
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">Contato / WhatsApp</label>
                        <input type="text" name="contact" class="form-control shadow-sm" placeholder="(35) 99999-0000">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold" style="background: var(--coffee-brown);">SALVAR PRODUTOR</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL CADASTRO: FAZENDA --}}
<div class="modal fade" id="modalFarm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('suppliers.storeFarm') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @csrf
            <div class="modal-header border-0 bg-light rounded-top">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">Nova Fazenda (Origem)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="info-label">Nome da Fazenda</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Ex: Fazenda Ondas da Mantiqueira" required>
                    </div>
                    <div class="col-md-4">
                        <label class="info-label">Altitude (m)</label>
                        <div class="input-group shadow-sm">
                            <input type="number" name="altitude" class="form-control" placeholder="1200" required>
                            <span class="input-group-text bg-white">m</span>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <label class="info-label text-primary">Variedades Cultivadas (Tags)</label>
                        <input type="text" name="varieties_tags" class="form-control shadow-sm" placeholder="Ex: Geisha, Bourbon Amarelo, Catuaí 144">
                        <small class="text-muted" style="font-size: 0.7rem;">Use vírgulas para separar as variedades.</small>
                    </div>

                    <div class="col-12"><hr class="my-2 opacity-10"></div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="checkInternacionalFarm" name="is_international" value="1">
                            <label class="form-check-label fw-bold" for="checkInternacionalFarm">Esta fazenda é fora do Brasil?</label>
                        </div>
                    </div>

                    {{-- Localização Brasil --}}
                    <div id="camposBrasilFarm" class="row g-3 m-0 p-0">
                        <div class="col-md-8">
                            <label class="info-label">Cidade</label>
                            <input type="text" name="city" class="form-control shadow-sm" placeholder="Ex: Carmo de Minas">
                        </div>
                        <div class="col-md-4">
                            <label class="info-label">Estado (UF)</label>
                            <input type="text" name="state" class="form-control shadow-sm" placeholder="MG" maxlength="2">
                        </div>
                    </div>

                    {{-- Localização Exterior --}}
                    <div id="camposExteriorFarm" class="row g-3 m-0 p-0 d-none">
                        <div class="col-md-4">
                            <label class="info-label">País</label>
                            <input type="text" name="country" class="form-control shadow-sm" placeholder="Ex: Etiópia">
                        </div>
                        <div class="col-md-4">
                            <label class="info-label">Distrito / Região</label>
                            <input type="text" name="district" class="form-control shadow-sm" placeholder="Ex: Sidamo">
                        </div>
                        <div class="col-md-4">
                            <label class="info-label">Cidade</label>
                            <input type="text" name="city_ext" class="form-control shadow-sm" placeholder="Ex: Yirgacheffe">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="submit" class="btn btn-dark px-4 rounded-pill fw-bold" style="background: var(--coffee-brown);">SALVAR FAZENDA</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL CADASTRO: EMPRESA --}}
<div class="modal fade" id="modalCompany" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('suppliers.storeCompany') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @csrf
            <div class="modal-header border-0 bg-light rounded-top">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">Nova Empresa / Cooperativa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="info-label">Razão Social / Nome</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Ex: Cooperativa Cocarive" required>
                    </div>
                    <div class="col-md-4">
                        <label class="info-label">Tipo de Atuação</label>
                        <select name="action_type" class="form-select shadow-sm">
                            <option value="Cooperativa">Cooperativa</option>
                            <option value="Exportadora">Exportadora</option>
                            <option value="Distribuidora">Distribuidora</option>
                            <option value="Fazenda Independente">Fazenda Independente</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">CNPJ (Opcional)</label>
                        <input type="text" name="cnpj" class="form-control shadow-sm" placeholder="00.000.000/0000-00">
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">Contato Principal</label>
                        <input type="text" name="contact" class="form-control shadow-sm" placeholder="Email ou Telefone">
                    </div>

                    <div class="col-12"><hr class="my-2 opacity-10"></div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="checkInternacionalCompany" name="is_international" value="1">
                            <label class="form-check-label fw-bold" for="checkInternacionalCompany">Empresa fora do Brasil?</label>
                        </div>
                    </div>

                    {{-- Endereço Empresa BR --}}
                    <div id="camposBrasilCompany" class="row g-3 m-0 p-0">
                        <div class="col-md-9">
                            <label class="info-label">Cidade</label>
                            <input type="text" name="city" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="info-label">UF</label>
                            <input type="text" name="state" class="form-control shadow-sm" maxlength="2">
                        </div>
                    </div>

                    {{-- Endereço Empresa Exterior --}}
                    <div id="camposExteriorCompany" class="row g-3 m-0 p-0 d-none">
                        <div class="col-md-6">
                            <label class="info-label">País</label>
                            <input type="text" name="country" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="info-label">Cidade (Texto Livre)</label>
                            <input type="text" name="city_ext" class="form-control shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="submit" class="btn btn-dark px-4 rounded-pill fw-bold" style="background: var(--coffee-brown);">SALVAR EMPRESA</button>
            </div>
        </form>
    </div>
</div>