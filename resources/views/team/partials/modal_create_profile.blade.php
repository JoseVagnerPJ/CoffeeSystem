{{-- MODAL: CRIAR PERFIL DE ACESSO COM AUTOMAÇÃO --}}
<div class="modal fade" id="modalCreateProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Configurar Nível de Acesso Detalhado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('team.storeProfile') }}" method="POST" id="formCreateProfile">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="info-label">Nome do Cargo / Nível</label>
                        <input type="text" name="name" class="form-control bg-light border-0 p-3" 
                               placeholder="Ex: Gerente de Qualidade, Operador de Torra..." required style="border-radius: 10px;">
                    </div>

                    {{-- MASTER CONTROL --}}
                    <div class="alert d-flex align-items-center mb-4" style="background-color: #fcf8e3; border: 1px solid #faebcc; border-radius: 12px;">
                        <div class="form-check form-switch">
                            <input class="form-check-input master-switch" type="checkbox" name="permissions[all_access]" value="1" id="perm_all">
                            <label class="form-check-label fw-bold text-dark ms-2" for="perm_all">
                                <i class="bi bi-star-fill text-warning me-1"></i> CONTROLE TOTAL (Ativa todas as permissões do sistema)
                            </label>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- MÓDULO: ESTOQUE VERDE --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-white module-box">
                                <h6 class="fw-bold small text-uppercase text-primary mb-3"><i class="bi bi-box-seam me-2"></i>Estoque Verde</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input parent-switch" type="checkbox" name="permissions[green_coffee_full]" value="1" id="gc_full">
                                    <label class="form-check-label small fw-bold" for="gc_full">Recebimento & Operação</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Permite dar entrada em novos lotes e movimentar estoque.</div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input child-switch" type="checkbox" name="permissions[green_coffee_view]" value="1" id="gc_view">
                                    <label class="form-check-label small" for="gc_view">Consultar Saldo</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Apenas visualiza quantidades e lotes existentes.</div>
                                </div>
                            </div>
                        </div>

                        {{-- MÓDULO: LABORATÓRIO --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-white module-box">
                                <h6 class="fw-bold small text-uppercase text-info mb-3"><i class="bi bi-microscope me-2"></i>Laboratório</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input parent-switch" type="checkbox" name="permissions[lab_full]" value="1" id="lb_full">
                                    <label class="form-check-label small fw-bold" for="lb_full">Abrir Mesas de Prova</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Permite criar sessões de cupping e registrar notas.</div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input child-switch" type="checkbox" name="permissions[lab_view]" value="1" id="lb_view">
                                    <label class="form-check-label small" for="lb_view">Ver Resultados</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Apenas consulta laudos e históricos de provas.</div>
                                </div>
                            </div>
                        </div>

                        {{-- MÓDULO: TORREFAÇÃO --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-white module-box">
                                <h6 class="fw-bold small text-uppercase text-warning mb-3"><i class="bi bi-fire me-2"></i>Torrefação</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input parent-switch" type="checkbox" name="permissions[roast_full]" value="1" id="rt_full">
                                    <label class="form-check-label small fw-bold" for="rt_full">Gerenciar Demandas</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Permite criar ordens de torra e planejar produção.</div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input child-switch" type="checkbox" name="permissions[roast_view]" value="1" id="rt_view">
                                    <label class="form-check-label small" for="rt_view">Acompanhar Torras</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Apenas vê o que está sendo produzido no momento.</div>
                                </div>
                            </div>
                        </div>

                        {{-- BLOCO: FORNECEDORES --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-light module-box">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3"><i class="bi bi-truck me-2"></i>Fornecedores</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input parent-switch" type="checkbox" name="permissions[supplier_edit]" value="1" id="sp_edit">
                                    <label class="form-check-label small fw-bold" for="sp_edit">Cadastrar</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Pode adicionar fazendas, produtores e empresas.</div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input danger-switch" type="checkbox" name="permissions[supplier_delete]" value="1" id="sp_del">
                                    <label class="form-check-label small text-danger fw-bold" for="sp_del">Excluir Fornecedores</label>
                                </div>
                            </div>
                        </div>

                        {{-- BLOCO: AMOSTRAS --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-light module-box">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3"><i class="bi bi-clipboard-pulse me-2"></i>Amostras</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input parent-switch" type="checkbox" name="permissions[sample_edit]" value="1" id="sm_edit">
                                    <label class="form-check-label small fw-bold" for="sm_edit">Registrar Amostras</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Pode dar entrada em novas amostras de café.</div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input danger-switch" type="checkbox" name="permissions[sample_delete]" value="1" id="sm_del">
                                    <label class="form-check-label small text-danger fw-bold" for="sm_del">Excluir Amostras</label>
                                </div>
                            </div>
                        </div>

                        {{-- BLOCO: PERFIS SENSORIAIS --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-light module-box">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3"><i class="bi bi-activity me-2"></i>Perfis Sensoriais</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input parent-switch" type="checkbox" name="permissions[profile_edit]" value="1" id="pf_edit">
                                    <label class="form-check-label small fw-bold" for="pf_edit">Criar Perfis/Blends</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Pode definir as notas e composição dos cafés.</div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input danger-switch" type="checkbox" name="permissions[profile_delete]" value="1" id="pf_del">
                                    <label class="form-check-label small text-danger fw-bold" for="pf_del">Excluir Perfis</label>
                                </div>
                            </div>
                        </div>

                        {{-- BLOCO: MÁQUINAS --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100 bg-light module-box">
                                <h6 class="fw-bold small text-uppercase text-dark mb-3"><i class="bi bi-nut me-2"></i>Equipamentos</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input parent-switch" type="checkbox" name="permissions[machine_edit]" value="1" id="mq_edit">
                                    <label class="form-check-label small fw-bold" for="mq_edit">Gestão de Máquinas</label>
                                    <div class="text-muted" style="font-size: 0.65rem;">Cadastra máquinas e registra manutenções.</div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input danger-switch" type="checkbox" name="permissions[machine_delete]" value="1" id="mq_del">
                                    <label class="form-check-label small text-danger fw-bold" for="mq_del">Remover Equipamento</label>
                                </div>
                            </div>
                        </div>
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
    // 1. Lógica MASTER: Se marcar 'all_access', marca TODOS os outros switches do modal
    $('.master-switch').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('#formCreateProfile .form-check-input:not(.master-switch)').prop('checked', isChecked);
    });

    // 2. Lógica de Módulo: Se marcar a opção de "Operação/Full", marca a de "Visualização" automaticamente
    $('.parent-switch').on('change', function() {
        if ($(this).is(':checked')) {
            // Encontra o próximo switch dentro do mesmo box de módulo
            $(this).closest('.module-box').find('.child-switch').prop('checked', true);
        }
    });

    // 3. Lógica Inversa: Se desmarcar o acesso total, desmarca a master (prevenção visual)
    $('#formCreateProfile .form-check-input:not(.master-switch)').on('change', function() {
        if (!$(this).is(':checked')) {
            $('.master-switch').prop('checked', false);
        }
    });
});
</script>