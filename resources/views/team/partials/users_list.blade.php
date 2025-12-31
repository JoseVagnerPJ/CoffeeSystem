<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-0 text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Colaboradores da Unidade</h6>
    <button class="btn btn-sm text-white fw-bold rounded-pill px-3" style="background-color: var(--coffee-brown);" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
        <i class="bi bi-person-plus-fill me-1"></i> Novo Colaborador
    </button>
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
            <tr class="{{ !$user->is_active ? 'bg-light opacity-75' : '' }}">
                <td class="px-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 45px; height: 45px; border: 2px solid {{ $user->is_active ? 'var(--coffee-cream)' : '#dee2e6' }}; overflow: hidden;">
                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" class="w-100 h-100" style="object-fit: cover; filter: {{ !$user->is_active ? 'grayscale(100%)' : 'none' }};">
                            @else
                                <i class="bi bi-person text-dark fs-5"></i>
                            @endif
                        </div>
                        <div>
                            <div class="fw-bold {{ !$user->is_active ? 'text-muted text-decoration-line-through' : 'text-dark' }}">
                                {{ $user->name }}
                            </div>
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
                    @if($user->is_active)
                        <span class="badge bg-success rounded-pill px-3" style="font-size: 0.65rem;">ATIVO</span>
                    @else
                        <span class="badge bg-danger rounded-pill px-3" style="font-size: 0.65rem;">INATIVO</span>
                        <div class="d-block mt-1" style="font-size: 0.55rem; color: var(--bs-danger); font-weight: bold; text-transform: uppercase;">
                            <i class="bi bi-exclamation-triangle-fill"></i> Acesso Bloqueado
                        </div>
                    @endif
                </td>
                <td class="text-end px-3">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-light rounded-circle shadow-sm" 
                                data-bs-toggle="modal" data-bs-target="#modalViewUser{{ $user->id }}" 
                                title="Ver Detalhes">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                        </button>

                        @if($user->id !== Auth::id())
                            <form action="{{ route('team.inactivateUser', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" 
                                        onclick="return confirm('Deseja {{ $user->is_active ? 'inativar' : 'reativar' }} este colaborador?')" 
                                        title="{{ $user->is_active ? 'Inativar' : 'Reativar' }}">
                                    @if($user->is_active)
                                        <i class="bi bi-person-x-fill text-danger"></i>
                                    @else
                                        <i class="bi bi-person-check-fill text-success"></i>
                                    @endif
                                </button>
                            </form>
                        @else
                            <span class="badge bg-light text-muted border fw-normal d-flex align-items-center px-3">Você</span>
                        @endif
                    </div>
                </td>
            </tr>

            @include('team.partials.view_user', ['user' => $user])

            @endforeach
        </tbody>
    </table>
</div>