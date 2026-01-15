{{-- MODAL: CRIAR PERFIL DE ACESSO COM TRAVA DE HIERARQUIA --}}
<div class="modal fade" id="modalCreateProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Configurar Nível de Acesso Detalhado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('registries.team.storeProfile') }}" method="POST" id="formCreateProfile">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="info-label">Nome do Cargo / Nível</label>
                        <input type="text" name="name" class="form-control bg-light border-0 p-3" 
                               placeholder="Ex: Mestre de Torra, Auxiliar de Laboratório..." required style="border-radius: 10px;">
                    </div>

                    {{-- CONTROLES ESPECIAIS --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="alert d-flex align-items-center h-100 mb-0 shadow-sm" style="background-color: #f0f4f8; border: 1px solid #d1d9e0; border-radius: 12px;">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="permissions[dev_access]" value="1" id="perm_dev" disabled>
                                    <label class="form-check-label fw-bold text-secondary ms-2" for="perm_dev">
                                        <i class="bi bi-code-slash me-1"></i> DESENVOLVEDOR (Bloqueado)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- APENAS QUEM JÁ É MASTER PODE CRIAR OUTRO MASTER --}}
                            @if(auth()->user()->hasPermission('all_access'))
                                <div class="alert d-flex align-items-center h-100 mb-0 shadow-sm" style="background-color: #fcf8e3; border: 1px solid #faebcc; border-radius: 12px;">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input master-switch" type="checkbox" name="permissions[all_access]" value="1" id="perm_all">
                                        <label class="form-check-label fw-bold text-dark ms-2" for="perm_all">
                                            <i class="bi bi-star-fill text-warning me-1"></i> CONTROLE TOTAL
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- MÓDULO: ESTOQUE VERDE --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-white module-box shadow-sm">
                                <h6 class="fw-bold small text-uppercase text-primary mb-3"><i class="bi bi-box-seam me-2"></i>Estoque Verde</h6>
                                @if(auth()->user()->hasPermission('green_coffee_full'))
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input parent-switch" type="checkbox" name="permissions[green_coffee_full]" value="1" id="gc_full">
                                        <label class="form-check-label small fw-bold" for="gc_full">Recebimento e Operação (Total)</label>
                                    </div>
                                @endif
                                @if(auth()->user()->hasPermission('green_coffee_view'))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input child-switch" type="checkbox" name="permissions[green_coffee_view]" value="1" id="gc_view">
                                        <label class="form-check-label small" for="gc_view">Somente Visualização</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- MÓDULO: LABORATÓRIO --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-white module-box shadow-sm">
                                <h6 class="fw-bold small text-uppercase text-info mb-3"><i class="bi bi-microscope me-2"></i>Laboratório</h6>
                                @if(auth()->user()->hasPermission('lab_full'))
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input parent-switch" type="checkbox" name="permissions[lab_full]" value="1" id="lb_full">
                                        <label class="form-check-label small fw-bold" for="lb_full">Cientista (Acesso Total)</label>
                                    </div>
                                @endif
                                @if(auth()->user()->hasPermission('lab_view'))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input child-switch" type="checkbox" name="permissions[lab_view]" value="1" id="lb_view">
                                        <label class="form-check-label small" for="lb_view">Visualização (Sem exclusão)</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- MÓDULO: EQUIPE --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-white module-box shadow-sm">
                                <h6 class="fw-bold small text-uppercase text-danger mb-3"><i class="bi bi-people me-2"></i>Gestão de Equipe</h6>
                                @if(auth()->user()->hasPermission('team_full'))
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input parent-switch" type="checkbox" name="permissions[team_full]" value="1" id="tm_full">
                                        <label class="form-check-label small fw-bold" for="tm_full">Acesso Total (Admin)</label>
                                    </div>
                                @endif
                                @if(auth()->user()->hasPermission('team_user_only') || auth()->user()->hasPermission('team_full'))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input child-switch" type="checkbox" name="permissions[team_user_only]" value="1" id="tm_user">
                                        <label class="form-check-label small" for="tm_user">Apenas Criação de Usuários</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12"><hr class="opacity-10"></div>
                        
                        {{-- SEÇÃO: CADASTROS BÁSICOS --}}
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light module-box shadow-sm">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3">Fornecedores</h6>
                                @if(auth()->user()->hasPermission('supplier_full'))
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input parent-switch" type="checkbox" name="permissions[supplier_full]" value="1" id="sup_full">
                                        <label class="form-check-label small fw-bold" for="sup_full">Editor Total</label>
                                    </div>
                                @endif
                                @if(auth()->user()->hasPermission('supplier_view'))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input child-switch" type="checkbox" name="permissions[supplier_view]" value="1" id="sup_view">
                                        <label class="form-check-label small" for="sup_view">Visualização</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- REPETIR A MESMA LÓGICA PARA AMOSTRAS, EQUIPAMENTOS E SENSOREAIS --}}
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light module-box shadow-sm">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3">Amostras</h6>
                                @if(auth()->user()->hasPermission('sample_full'))
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input parent-switch" type="checkbox" name="permissions[sample_full]" value="1" id="sam_full">
                                        <label class="form-check-label small fw-bold" for="sam_full">Editor Total</label>
                                    </div>
                                @endif
                                @if(auth()->user()->hasPermission('sample_view'))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input child-switch" type="checkbox" name="permissions[sample_view]" value="1" id="sam_view">
                                        <label class="form-check-label small" for="sam_view">Visualização</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light module-box shadow-sm">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3">Equipamentos</h6>
                                @if(auth()->user()->hasPermission('machine_full'))
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input parent-switch" type="checkbox" name="permissions[machine_full]" value="1" id="mac_full">
                                        <label class="form-check-label small fw-bold" for="mac_full">Editor Total</label>
                                    </div>
                                @endif
                                @if(auth()->user()->hasPermission('machine_view'))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input child-switch" type="checkbox" name="permissions[machine_view]" value="1" id="mac_view">
                                        <label class="form-check-label small" for="mac_view">Visualização</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border bg-light module-box shadow-sm">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3">Perfis Sensoriais</h6>
                                @if(auth()->user()->hasPermission('sensory_full'))
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input parent-switch" type="checkbox" name="permissions[sensory_full]" value="1" id="sen_full">
                                        <label class="form-check-label small fw-bold" for="sen_full">Editor Total</label>
                                    </div>
                                @endif
                                @if(auth()->user()->hasPermission('sensory_view'))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input child-switch" type="checkbox" name="permissions[sensory_view]" value="1" id="sen_view">
                                        <label class="form-check-label small" for="sen_view">Visualização</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- CONFIGURAÇÕES --}}
                        @if(auth()->user()->hasPermission('settings_edit'))
                        <div class="col-md-12">
                            <div class="p-3 rounded-3 border shadow-sm" style="background-color: #fdfdfd;">
                                <div class="form-check form-switch d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="permissions[settings_edit]" value="1" id="st_edit">
                                    <label class="form-check-label fw-bold text-dark ms-2" for="st_edit">
                                        <i class="bi bi-gear-fill me-1"></i> MÓDULO CONFIGURAÇÕES: Editar informações da empresa e parâmetros globais
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold px-4" style="background-color: var(--coffee-brown); border-radius: 10px;">
                        Salvar Nível de Acesso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. Lógica MASTER: Marca tudo que estiver VISÍVEL (permissões que o criador possui)
    $('.master-switch').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('#formCreateProfile .form-check-input:not(.master-switch):not(#perm_dev)').prop('checked', isChecked);
    });

    // 2. Lógica de Módulo: Se marcar "Acesso Total", marca "Visualização"
    $('.parent-switch').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.module-box').find('.child-switch').prop('checked', true);
        }
    });

    // 3. Lógica Inversa: Se desmarcar qualquer uma, remove o check do Controle Total
    $('#formCreateProfile .form-check-input:not(.master-switch)').on('change', function() {
        if (!$(this).is(':checked')) {
            $('.master-switch').prop('checked', false);
        }
    });
});
</script>