<div class="modal fade" id="modalViewUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            @php
                $authUser = auth()->user();
                $isAuthDev = $authUser->hasPermission('dev_access');
                
                // Identifica se o usuário desta ficha é um Desenvolvedor
                $targetIsDev = ($user->profile && ($user->profile->name === 'Desenvolvedor' || (isset($user->profile->permissions['dev_access']) && $user->profile->permissions['dev_access'])));
                
                // REGRA SOLICITADA: Se eu sou dev, altero qualquer um. Se não sou dev, não altero um dev.
                $canEditThisUser = $isAuthDev || !$targetIsDev;
            @endphp

            <div class="modal-header border-0 bg-light rounded-top p-4">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">
                    <i class="bi bi-person-badge me-2"></i>Ficha do Colaborador
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('registries.team.updateUser', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Lado Esquerdo: Foto e Status --}}
                        <div class="col-md-4 border-end text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 130px; height: 130px; border: 3px solid white; overflow: hidden;">
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" class="w-100 h-100" style="object-fit: cover;" id="preview_{{ $user->id }}">
                                    @else
                                        <i class="bi bi-person-fill text-muted" style="font-size: 4rem;" id="icon_{{ $user->id }}"></i>
                                        <img src="" class="w-100 h-100 d-none" style="object-fit: cover;" id="preview_{{ $user->id }}">
                                    @endif
                                </div>
                                
                                {{-- Só mostra o botão de trocar foto se tiver permissão de editar --}}
                                @if($canEditThisUser)
                                    <label for="photo_input_{{ $user->id }}" class="btn btn-sm btn-dark rounded-circle position-absolute bottom-0 end-0 d-none edit-mode-{{ $user->id }}" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;">
                                        <i class="bi bi-camera-fill"></i>
                                        <input type="file" name="photo" id="photo_input_{{ $user->id }}" class="d-none" accept="image/*" onchange="previewUserPhoto(this, '{{ $user->id }}')">
                                    </label>
                                @endif
                            </div>
                            
                            <div class="mb-2">
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $user->is_active ? 'text-success' : 'text-danger' }} border-0 px-3 py-2 fw-bold">
                                    {{ $user->is_active ? 'Colaborador Ativo' : 'Acesso Suspenso' }}
                                </span>
                            </div>
                            <small class="text-muted d-block mt-2">Membro desde: <br><strong>{{ $user->created_at->format('d/m/Y') }}</strong></small>
                        </div>

                        {{-- Lado Direito: Dados Editáveis --}}
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="info-label mb-0">Configurações de Acesso</h6>
                                
                                @if($canEditThisUser)
                                    <button type="button" class="btn btn-sm btn-outline-warning border-0 fw-bold unlock-btn" onclick="enableUserEdit('{{ $user->id }}', this)">
                                        <i class="bi bi-pencil-square me-1"></i> Alterar Dados
                                    </button>
                                @else
                                    <span class="badge bg-light text-muted border py-2 px-3 fw-normal">
                                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> Perfil Protegido
                                    </span>
                                @endif
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="info-label">Nome de Exibição</label>
                                    <input type="text" name="name" class="form-control border-0 bg-light p-3 input-field-{{ $user->id }}" 
                                           value="{{ $user->name }}" required disabled style="border-radius: 10px;">
                                </div>

                                <div class="col-12">
                                    <label class="info-label">Nível de Acesso (Perfil)</label>
                                    <select name="profile_id" class="form-select border-0 bg-light p-3 input-field-{{ $user->id }}" disabled required style="border-radius: 10px;">
                                        @foreach($profiles as $profile)
                                            @php
                                                $loopProfileIsDev = ($profile->name === 'Desenvolvedor' || (isset($profile->permissions['dev_access']) && $profile->permissions['dev_access']));
                                                
                                                $canAssign = true;
                                                // Se não sou dev e o perfil do loop for dev, não posso atribuir
                                                if (!$isAuthDev && $loopProfileIsDev) {
                                                    $canAssign = false;
                                                }
                                                // Validação de Hierarquia padrão
                                                elseif (!$isAuthDev && !$authUser->hasPermission('all_access')) {
                                                    if ($profile->permissions) {
                                                        foreach ($profile->permissions as $permissionKey => $isEnabled) {
                                                            if ($isEnabled && !$authUser->hasPermission($permissionKey)) {
                                                                $canAssign = false;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }

                                                $isCurrentProfile = ($user->profile_id == $profile->id);
                                            @endphp

                                            @if($isCurrentProfile || $canAssign)
                                                <option value="{{ $profile->id }}" {{ $isCurrentProfile ? 'selected' : '' }}>
                                                    {{ $profile->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="info-label">Usuário (Login)</label>
                                    <input type="text" class="form-control border-0 bg-white p-3 fw-bold text-muted" 
                                           value="@ {{ $user->username }}" readonly style="cursor: not-allowed; border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="info-label">Nova Senha</label>
                                    <input type="password" name="password" class="form-control border-0 bg-light p-3 input-field-{{ $user->id }}" 
                                           placeholder="Manter senha atual" disabled style="border-radius: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- O rodapé com o botão salvar só existe se o usuário tiver permissão de editar --}}
                @if($canEditThisUser)
                    <div class="modal-footer border-0 p-4 pt-0 d-none edit-mode-{{ $user->id }}">
                        <button type="button" class="btn btn-light fw-bold px-4" onclick="location.reload()">Cancelar</button>
                        <button type="submit" class="btn text-white fw-bold px-5 shadow-sm" style="background-color: var(--coffee-brown); border-radius: 10px;">
                            <i class="bi bi-check-lg me-2"></i>Salvar Alterações
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
    if (typeof enableUserEdit !== 'function') {
        function enableUserEdit(userId, btn) {
            const inputs = document.querySelectorAll('.input-field-' + userId);
            inputs.forEach(input => {
                input.disabled = false;
                input.classList.replace('bg-light', 'bg-white');
                input.style.border = '1px solid #ced4da';
            });

            const editElements = document.querySelectorAll('.edit-mode-' + userId);
            editElements.forEach(el => {
                el.classList.remove('d-none');
            });

            btn.classList.add('d-none');
        }
    }

    if (typeof previewUserPhoto !== 'function') {
        function previewUserPhoto(input, userId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('preview_' + userId);
                    const icon = document.getElementById('icon_' + userId);
                    if(img) {
                        img.src = e.target.result;
                        img.classList.remove('d-none');
                    }
                    if (icon) icon.classList.add('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    }
</script>