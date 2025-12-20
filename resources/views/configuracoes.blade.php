@extends('layouts.app')

@section('title', 'Configurações de Sistema')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h2 class="h4 fw-bold text-dark">Configurações de Sistema</h2>
        <p class="text-muted small">Gerencie o comportamento, acessos e a identidade visual do seu ERP.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="list-group shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <a href="#aparencia" class="list-group-item list-group-item-action active p-3 border-0" data-bs-toggle="list">
                    <i class="bi bi-palette me-2"></i> Interface & Tema
                </a>
                <a href="#fluxo-trabalho" class="list-group-item list-group-item-action p-3 border-0" data-bs-toggle="list">
                    <i class="bi bi-diagram-3 me-2"></i> Fluxo de Trabalho
                </a>
                <a href="#seguranca-acesso" class="list-group-item list-group-item-action p-3 border-0" data-bs-toggle="list">
                    <i class="bi bi-shield-lock me-2"></i> Segurança & Acessos
                </a>
                <a href="#sistema" class="list-group-item list-group-item-action p-3 border-0" data-bs-toggle="list">
                    <i class="bi bi-cpu me-2"></i> Performance & Sistema
                </a>
            </div>
            
            <div class="mt-4 p-3 bg-white shadow-sm rounded-3 border-start border-4 border-gold">
                <small class="text-muted d-block fw-bold mb-1">DICA DE PRODUTIVIDADE</small>
                <p class="small mb-0 opacity-75">Configurações de fluxo de trabalho aplicam-se a todos os terminais de torra e laboratório em tempo real.</p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="aparencia">
                    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-4">Personalização de Interface</h5>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Tema do Sistema</label>
                            <div class="d-flex gap-3">
                                <div class="form-check border rounded p-3 flex-grow-1">
                                    <input class="form-check-input" type="radio" name="theme" id="light" checked>
                                    <label class="form-check-label" for="light">
                                        <i class="bi bi-sun me-2"></i> Light (Unique Cream)
                                    </label>
                                </div>
                                <div class="form-check border rounded p-3 flex-grow-1 opacity-50">
                                    <input class="form-check-input" type="radio" name="theme" id="dark" disabled>
                                    <label class="form-check-label" for="dark">
                                        <i class="bi bi-moon-stars me-2"></i> Dark Mode (Beta)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small">Densidade da Interface</label>
                            <select class="form-select border-0 bg-light">
                                <option>Confortável (Padrão)</option>
                                <option>Compacto (Para telas menores/Laboratório)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="fluxo-trabalho">
                    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-4">Regras de Produção</h5>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Autonomia do Mestre de Torra</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="editFila" checked>
                                <label class="form-check-label fw-bold" for="editFila">Permitir edição da Fila de Torra</label>
                                <p class="text-muted" style="font-size: 0.75rem;">Mestres podem reordenar a sequência de torras dentro da coluna "A Torrar".</p>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small">Validação de Tempo</label>
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <span class="d-block fw-bold" style="font-size: 0.9rem;">Tempo mínimo para conclusão (Sinalização)</span>
                                    <small class="text-muted">Bloqueia a finalização da torra antes de X minutos para evitar erros de clique.</small>
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control border-0 bg-light" value="8">
                                        <span class="input-group-text border-0 bg-light">min</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="seguranca-acesso">
                    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-4">Segurança e Hierarquia</h5>
                        
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 mb-4">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="supervisorPass" checked>
                                <label class="form-check-label fw-bold" for="supervisorPass">Substituir Bloqueio por Senha de Supervisor</label>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Em vez de proibir ações, o sistema solicitará um PIN de supervisor para prosseguir em módulos restritos.</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tempo de Sessão Inativa</label>
                            <select class="form-select border-0 bg-light">
                                <option>Nunca deslogar automático</option>
                                <option>Deslogar após 2 horas de inatividade</option>
                                <option>Deslogar após 8 horas</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="sistema">
                    <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                        <h5 class="fw-bold mb-4">Inteligência & Sincronização</h5>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Atualização do Dashboard Real-Time</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="range" class="form-range flex-grow-1" min="5" max="60" step="5" value="10">
                                <span class="badge bg-dark">10 segundos</span>
                            </div>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cache" checked>
                            <label class="form-check-label fw-bold" for="cache">Habilitar Cache de Imagens de Cafés</label>
                            <p class="text-muted small">Acelera o carregamento do Kanban em conexões lentas.</p>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="mt-4 text-end">
                <button class="btn btn-outline-secondary rounded-pill px-4 me-2">Descartar</button>
                <button class="btn btn-dark rounded-pill px-5 fw-bold shadow">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item { color: var(--coffee-text); transition: 0.2s; border-radius: 0 !important; }
    .list-group-item.active { background-color: var(--coffee-brown); border-color: var(--coffee-brown); font-weight: bold; }
    .list-group-item:not(.active):hover { background-color: #f8f9fa; color: var(--coffee-gold); }
    .border-gold { border-left-color: var(--coffee-gold) !important; }
</style>
@endsection