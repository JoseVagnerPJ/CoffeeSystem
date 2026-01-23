<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-0 text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Colaboradores da Unidade</h6>
    {{-- Qualquer um com permissão de time pode ver o botão de "Novo" --}}
    @if(auth()->user()->hasPermission('team_full') || auth()->user()->hasPermission('team_user_only'))
        <button class="btn btn-sm text-white fw-bold rounded-pill px-3" style="background-color: var(--coffee-brown);" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
            <i class="bi bi-person-plus-fill me-1"></i> Novo Colaborador
        </button>
    @endif
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th class="border-0 px-3 py-3 small text-muted text-uppercase fw-bold">Colaborador</th>
                <th class="border-0 py-3 small text-muted text-uppercase fw-bold">Perfil</th>
                <th class="border-0 py-3 small text-muted text-uppercase fw-bold text-center">Status</th>
                <th class="border-0 py-3 text-end small text-muted text-uppercase fw-bold px-3">Gestão</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                @php
                    $authUser = auth()->user();
                    $isMaster = $authUser->hasPermission('all_access');
                    $isDev = $authUser->hasPermission('dev_access');
                    $canManageTeam = $authUser->hasPermission('team_full');
                    
                    // Identifica o usuário da linha
                    $rowUserIsDev = ($user->profile && ($user->profile->name === 'Desenvolvedor' || (isset($user->profile->permissions['dev_access']) && $user->profile->permissions['dev_access'])));
                    $rowUserIsAdmin = $user->hasPermission('all_access');

                    $canSeeUser = false;
                    if ($user->id === $authUser->id) { $canSeeUser = true; }
                    elseif ($rowUserIsDev && !$isDev) { $canSeeUser = false; }
                    elseif ($isMaster) { $canSeeUser = true; }
                    else {
                        $hasSuperiorPermission = false;
                        if ($user->profile && $user->profile->permissions) {
                            foreach ($user->profile->permissions as $permKey => $enabled) {
                                if ($enabled && !$authUser->hasPermission($permKey)) { $hasSuperiorPermission = true; break; }
                            }
                        }
                        if (!$hasSuperiorPermission) { $canSeeUser = true; }
                    }
                @endphp

                @if($canSeeUser)
                <tr class="{{ !$user->is_active ? 'bg-light opacity-75' : '' }}">
                    <td class="px-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 45px; height: 45px; border: 2px solid {{ $user->is_active ? 'var(--coffee-cream)' : '#dee2e6' }}; overflow: hidden;">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" class="w-100 h-100" style="object-fit: cover;">
                                @else
                                    <i class="bi bi-person text-dark fs-5"></i>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold {{ !$user->is_active ? 'text-muted text-decoration-line-through' : 'text-dark' }}">{{ $user->name }}</div>
                                <small class="text-muted">@<span>{{ $user->username }}</span></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ !$user->is_active ? 'bg-secondary' : 'bg-primary' }} bg-opacity-10 {{ !$user->is_active ? 'text-secondary' : 'text-primary' }} border-0 fw-bold">
                            {{ $user->profile->name ?? 'Sem Perfil' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} rounded-pill px-3" style="font-size: 0.65rem;">
                            {{ $user->is_active ? 'ATIVO' : 'INATIVO' }}
                        </span>
                    </td>
                    <td class="text-end px-3">
                        <div class="d-flex justify-content-end gap-2">
                            
                            {{-- LÓGICA DE GESTÃO (EDIÇÃO/VISUALIZAÇÃO) --}}
                            @if($canManageTeam && !$rowUserIsDev && (!$rowUserIsAdmin || $isMaster))
                                {{-- Pode editar: Lápis azul --}}
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#modalViewUser{{ $user->id }}" title="Editar Colaborador">
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                </button>
                            @else
                                {{-- Apenas visualiza: Olho cinza (Para Devs, Admins editados por não-admins ou usuários sem permissão de gestão) --}}
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#modalViewUser{{ $user->id }}" title="Visualizar Ficha">
                                    <i class="bi bi-eye-fill text-muted"></i>
                                </button>
                            @endif

                            @if($user->id !== $authUser->id)
                                {{-- BLOQUEIO DE STATUS PARA DESENVOLVEDOR E ADMIN --}}
                                @if($rowUserIsDev || ($rowUserIsAdmin && !$isMaster))
                                    <span class="badge bg-light text-muted border fw-normal d-flex align-items-center px-3" title="Usuário Vitalício ou Superior">
                                        <i class="bi bi-shield-fill-check text-warning me-1"></i> Protegido
                                    </span>
                                @elseif($canManageTeam)
                                    <form action="{{ route('registries.team.inactivateUser', $user->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="return confirm('Confirmar alteração de status?')">
                                            <i class="bi bi-person-{{ $user->is_active ? 'x-fill text-danger' : 'check-fill text-success' }}"></i>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="badge bg-light text-muted border fw-normal d-flex align-items-center px-3">Você</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @include('registries.team.partials.view_user', ['user' => $user])
                @endif
            @endforeach
        </tbody>
    </table>
</div>