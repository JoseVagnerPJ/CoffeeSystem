<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-0 text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Níveis de Acesso</h6>
    <button class="btn btn-sm btn-outline-dark fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCreateProfile">
        <i class="bi bi-plus-lg me-1"></i> Novo Perfil
    </button>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th class="border-0 px-3 py-3 small text-muted text-uppercase fw-bold" style="width: 35%;">Nome do Perfil</th>
                <th class="border-0 py-3 small text-muted text-uppercase fw-bold">Resumo de Acesso</th>
                <th class="border-0 py-3 text-center small text-muted text-uppercase fw-bold" style="width: 150px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($profiles as $profile)
            @php
                // Identifica se este perfil é o de Desenvolvedor
                $isRowDevProfile = ($profile->name === 'Desenvolvedor' || (isset($profile->permissions['dev_access']) && $profile->permissions['dev_access']));
                // Verifica se o usuário logado tem poder para excluir (Master ou Dev)
                $canUserDeleteProfiles = (auth()->user()->hasPermission('all_access') || auth()->user()->hasPermission('dev_access'));
            @endphp
            <tr>
                <td class="px-3">
                    <div class="fw-bold text-dark">{{ $profile->name }}</div>
                    <small class="text-muted">ID: #{{ str_pad($profile->id, 3, '0', STR_PAD_LEFT) }}</small>
                </td>
                <td>
                    @if($profile->permissions)
                        @php $count = count(array_filter($profile->permissions)); @endphp
                        <span class="badge bg-dark bg-opacity-10 text-dark border-0 fw-bold">
                            {{ $count }} Permissões Ativas
                        </span>
                    @else
                        <span class="text-muted small">Nenhum acesso definido.</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        {{-- Detalhes sempre visível --}}
                        <button class="btn btn-sm btn-light rounded-circle shadow-sm" 
                                data-bs-toggle="modal" data-bs-target="#modalViewProfile{{ $profile->id }}" 
                                title="Ver Detalhes">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                        </button>

                        {{-- LÓGICA DE EXCLUSÃO --}}
                        @if($isRowDevProfile)
                            {{-- Perfil Dev é intocável --}}
                            <span class="badge bg-light text-muted border fw-normal d-flex align-items-center px-2" title="Perfil Protegido pelo Sistema">
                                <i class="bi bi-shield-lock-fill me-1 text-warning"></i> Sistema
                            </span>
                        @elseif($canUserDeleteProfiles)
                            {{-- Só Admin Total ou Dev excluem outros perfis --}}
                            <form action="{{ route('registries.team.destroyProfile', $profile->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" 
                                        onclick="return confirm('ATENÇÃO: Deseja excluir este perfil? Usuários vinculados ficarão sem permissões.')"
                                        title="Excluir Perfil">
                                    <i class="bi bi-trash-fill text-danger"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @include('registries.team.partials.view_profile', ['profile' => $profile])
            @empty
            <tr><td colspan="3" class="text-center py-5 text-muted">Nenhum perfil encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>