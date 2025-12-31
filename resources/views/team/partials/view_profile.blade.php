<div class="modal fade" id="modalViewProfile{{ $profile->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            {{-- Header com Design Unique Cafés --}}
            <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-shield-lock-fill" style="color: var(--coffee-brown); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color: var(--coffee-brown);">{{ $profile->name }}</h5>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">ID do Perfil: #{{ str_pad($profile->id, 3, '0', STR_PAD_LEFT) }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="row g-4">
                    {{-- Coluna 1: Mapa de Permissões --}}
                    <div class="col-lg-7">
                        <div class="p-3 bg-white border rounded-4 h-100 shadow-sm">
                            <h6 class="fw-bold mb-4 d-flex align-items-center" style="color: var(--coffee-brown);">
                                <i class="bi bi-ui-checks me-2 text-primary"></i> Atribuições do Nível
                            </h6>
                            
                            <div class="row g-3">
                                @php
                                    $categories = [
                                        'all_access' => ['label' => 'Acesso Mestre', 'icon' => 'bi-star-fill', 'color' => 'text-warning'],
                                        'green_coffee' => ['label' => 'Estoque Verde', 'icon' => 'bi-box-seam', 'color' => 'text-primary'],
                                        'lab' => ['label' => 'Laboratório', 'icon' => 'bi-microscope', 'color' => 'text-info'],
                                        'roast' => ['label' => 'Torrefação', 'icon' => 'bi-fire', 'color' => 'text-danger'],
                                        'supplier' => ['label' => 'Fornecedores', 'icon' => 'bi-truck', 'color' => 'text-dark'],
                                        'sample' => ['label' => 'Amostras', 'icon' => 'bi-clipboard-pulse', 'color' => 'text-success'],
                                        'profile' => ['label' => 'Sensorial', 'icon' => 'bi-activity', 'color' => 'text-secondary'],
                                        'machine' => ['label' => 'Máquinas', 'icon' => 'bi-nut', 'color' => 'text-dark'],
                                    ];

                                    $labels = [
                                        'all_access' => 'Controle Total do Sistema',
                                        'green_coffee_full' => 'Recebimento & Operação',
                                        'green_coffee_view' => 'Monitoramento de Saldo',
                                        'lab_full' => 'Gestão de Qualidade',
                                        'lab_view' => 'Consulta de Laudos',
                                        'roast_full' => 'Engenharia de Torra',
                                        'roast_view' => 'Acompanhamento de Lotes',
                                        'supplier_edit' => 'Cadastro & Edição',
                                        'supplier_delete' => 'Poder de Exclusão',
                                        'sample_edit' => 'Registro de Amostras',
                                        'sample_delete' => 'Exclusão de Amostras',
                                        'profile_edit' => 'Criação de Perfis/Blends',
                                        'profile_delete' => 'Exclusão de Perfis',
                                        'machine_edit' => 'Gestão de Equipamentos',
                                        'machine_delete' => 'Remoção de Máquinas'
                                    ];
                                @endphp

                                @if($profile->permissions)
                                    @foreach($categories as $prefix => $cat)
                                        @php
                                            $filtered = array_filter($profile->permissions, function($v, $k) use ($prefix) {
                                                return $v && strpos($k, $prefix) === 0;
                                            }, ARRAY_FILTER_USE_BOTH);
                                        @endphp

                                        @if(count($filtered) > 0)
                                            <div class="col-md-6">
                                                <div class="p-3 rounded-3 border bg-light h-100">
                                                    <small class="fw-bold d-block mb-2 {{ $cat['color'] }} text-uppercase" style="font-size: 0.65rem;">
                                                        <i class="bi {{ $cat['icon'] }} me-1"></i> {{ $cat['label'] }}
                                                    </small>
                                                    @foreach($filtered as $key => $value)
                                                        @if(isset($labels[$key]))
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="bi bi-check2 text-success me-2"></i>
                                                                <span class="small text-dark fw-medium">{{ $labels[$key] }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Coluna 2: Equipe Vinculada (Ativos e Inativos) --}}
                    <div class="col-lg-5">
                        <div class="p-3 bg-white border rounded-4 h-100 shadow-sm">
                            <h6 class="fw-bold mb-4 d-flex align-items-center" style="color: var(--coffee-brown);">
                                <i class="bi bi-people me-2 text-secondary"></i> Colaboradores Vinculados
                            </h6>
                            <div class="d-flex flex-column gap-3" style="max-height: 400px; overflow-y: auto;">
                                @forelse($profile->users as $user)
                                    <div class="d-flex align-items-center p-3 rounded-3 border bg-white transition-all {{ !$user->is_active ? 'bg-light opacity-75' : 'hover-shadow' }}">
                                        <div class="flex-shrink-0">
                                            @if($user->photo)
                                                <img src="{{ asset('storage/' . $user->photo) }}" 
                                                     class="rounded-circle border" 
                                                     style="width: 48px; height: 48px; object-fit: cover; filter: {{ !$user->is_active ? 'grayscale(100%)' : 'none' }};">
                                            @else
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border text-secondary" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-person-fill fs-4"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-bold {{ !$user->is_active ? 'text-muted text-decoration-line-through' : 'text-dark' }}" style="font-size: 0.9rem;">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                                                <i class="bi bi-at me-1"></i>{{ $user->username }}
                                                @if($user->is_active)
                                                    <span class="ms-2 badge bg-success bg-opacity-10 text-success p-1 px-2" style="font-size: 0.55rem; border: 1px solid rgba(25, 135, 84, 0.2);">Ativo</span>
                                                @else
                                                    <span class="ms-2 badge bg-danger bg-opacity-10 text-danger p-1 px-2" style="font-size: 0.55rem; border: 1px solid rgba(220, 53, 69, 0.2);">Inativo</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-2">
                                            <i class="bi bi-person-dash text-muted fs-3"></i>
                                        </div>
                                        <p class="text-muted small">Nenhum colaborador vinculado<br>a este perfil.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-dark w-100 fw-bold py-3" data-bs-dismiss="modal" style="border-radius: 12px; letter-spacing: 1px;">
                    FECHAR VISUALIZAÇÃO
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: var(--coffee-gold) !important;
    }
    .transition-all { transition: all 0.3s ease; }
</style>