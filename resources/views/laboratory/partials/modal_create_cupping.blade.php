<style>
    /* 1. FIXAÇÃO RÍGIDA DO TAMANHO DO MODAL */
    #modalCreateCupping .modal-dialog { max-width: 950px; margin: 1.75rem auto; }
    #modalCreateCupping .modal-content { height: 92vh; display: flex; flex-direction: column; overflow: hidden; border-radius: 20px; border: none; }
    #formCreateCupping { display: flex; flex-direction: column; height: 100%; overflow: hidden; }
    #modalCreateCupping .modal-body { flex: 1; overflow-y: scroll; padding: 0 1.5rem; background-color: #fcfcfc; scrollbar-width: none; }
    #modalCreateCupping .modal-body::-webkit-scrollbar { display: none; }

    /* CONTROLES STICKY ATUALIZADOS */
    .sticky-controls { 
        position: sticky; 
        top: 0; 
        background-color: #fcfcfc; 
        z-index: 100; 
        padding: 1.5rem 0; 
        border-bottom: 1px solid #eee; 
        margin-bottom: 1.5rem; 
    }
    #modalCreateCupping .modal-footer { flex-shrink: 0; background: #fff; border-top: 1px solid #eee !important; padding: 1.5rem !important; }

    /* ESTILO DO SELECT DE PERFIL */
    .profile-box {
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 12px;
        padding: 5px 12px;
        transition: all 0.2s;
    }
    .profile-box:focus-within { border-color: var(--coffee-brown); box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1); }
    .label-mini { font-size: 0.65rem; font-weight: 800; color: #adb5bd; text-transform: uppercase; margin-bottom: -2px; display: block; }
    .select-custom { border: none !important; padding: 0 !important; font-weight: bold; color: var(--coffee-brown); width: 100%; outline: none; background: transparent; cursor: pointer; }

    /* ESTILO DOS CARDS E COMPONENTES EXISTENTES */
    .cup-card { border: 1px solid #dee2e6; border-radius: 12px; margin-bottom: 1rem; transition: all 0.2s; background: #fff; position: relative; }
    .cup-header { padding: 15px; display: flex; align-items: center; justify-content: space-between; }
    .cup-number { width: 32px; height: 32px; background: var(--coffee-brown); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem; margin-right: 12px; flex-shrink: 0; }
    
    .nickname-container { flex-grow: 1; background: #ffffff; border: 1px solid #ced4da; border-radius: 8px; padding: 4px 12px; margin-right: 10px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05); }
    .nickname-label { font-size: 0.65rem; font-weight: 800; color: #adb5bd; text-transform: uppercase; margin-bottom: -2px; display: block; }
    .nickname-input { border: none !important; padding: 0 !important; font-weight: bold; color: var(--coffee-brown); width: 100%; outline: none; background: transparent; }

    .is-avulso-badge { background: #fd7e14; color: white; font-size: 0.65rem; padding: 2px 8px; border-radius: 4px; font-weight: bold; display: none; }
    .avulso-active .is-avulso-badge { display: inline-block; }
    .avulso-active .cup-number { background: #fd7e14; }
    
    .sample-result-list { position: absolute; width: 100%; z-index: 1000; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .sample-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 0.85rem; }
    .sample-item:hover { background: #f0f0f0; }

    .is-invalid-card { border: 2px solid #dc3545 !important; background-color: #fff8f8 !important; }
    #validation-error-msg { display: none; font-weight: bold; font-size: 0.85rem; color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
</style>

<div class="modal fade" id="modalCreateCupping" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 bg-light py-3">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);"><i class="bi bi-plus-circle-fill me-2"></i>Iniciar Mesa Técnica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formCreateCupping" action="{{ route('laboratory.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="sticky-controls">
                        <div id="validation-error-msg"></div>
                        
                        {{-- LINHA 1: TÍTULO DA MESA --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small text-uppercase">Título da Mesa *</label>
                                <input type="text" id="mesa-title" name="title" class="form-control shadow-sm border-0 bg-white py-2 px-3 fw-bold" placeholder="Ex: Seleção de Safra 2024..." required style="border-radius: 10px; color: var(--coffee-brown);">
                            </div>
                        </div>

                        {{-- LINHA 2: PERFIL, MESA ÀS CEGAS E CONTADOR --}}
                        <div class="row g-3 align-items-end">
                            {{-- Seleção de Perfil --}}
                            <div class="col-md-5">
                                <div class="profile-box shadow-sm">
                                    <label class="label-mini">Perfil de Atributos (Protocolo) *</label>
                                    <select name="cupping_profile_id" id="cupping_profile_id" class="select-custom" required>
                                        @foreach($profiles as $profile)
                                            <option value="{{ $profile->id }}" {{ $profile->id == $defaultProfileId ? 'selected' : '' }}>
                                                {{ $profile->name }} @if($profile->id == $defaultProfileId) (Padrão) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Mesa às Cegas --}}
                            <div class="col-md-3 text-center mb-1">
                                <label class="label-mini mb-1">Mesa às Cegas?</label>
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" name="is_blind" id="is_blind" value="1" style="width: 2.8em; height: 1.4em; cursor: pointer;">
                                </div>
                            </div>

                            {{-- Contador de Xícaras --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase text-center d-block">Quantidade de Xícaras</label>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-3 fw-bold" onclick="removeLastCup()">-</button>
                                    <div class="flex-grow-1 text-center bg-white border rounded-pill py-2 fw-bold shadow-sm" id="cup-counter" style="min-width: 80px;">0 Xícaras</div>
                                    <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-bold" onclick="addNewCup()">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="cups-wrapper"></div>
                    <div id="empty-state" class="text-center py-5">
                        <i class="bi bi-box-seam text-muted opacity-25" style="font-size: 4rem;"></i>
                        <p class="text-muted">Adicione xícaras para começar a configurar a mesa.</p>
                    </div>
                </div>

                <div class="modal-footer border-0 d-flex flex-column align-items-center">
                    <div id="footer-error-msg"></div>
                    <button type="button" onclick="attemptSubmit()" class="btn btn-secondary w-100 rounded-pill py-3 fw-bold shadow-sm" id="btnSubmitMesa" disabled>
                        CRIAR MESA E GERAR ACESSO QR-CODE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>