<div class="modal fade" id="modalViewUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
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
                                
                                <label for="photo_input_{{ $user->id }}" class="btn btn-sm btn-dark rounded-circle position-absolute bottom-0 end-0 d-none edit-mode-{{ $user->id }}" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;">
                                    <i class="bi bi-camera-fill"></i>
                                    <input type="file" name="photo" id="photo_input_{{ $user->id }}" class="d-none" accept="image/*" onchange="previewUserPhoto(this, '{{ $user->id }}')">
                                </label>
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
                                <button type="button" class="btn btn-sm btn-outline-warning border-0 fw-bold unlock-btn" onclick="enableUserEdit('{{ $user->id }}', this)">
                                    <i class="bi bi-pencil-square me-1"></i> Alterar Dados
                                </button>
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
                                            <option value="{{ $profile->id }}" {{ $user->profile_id == $profile->id ? 'selected' : '' }}>
                                                {{ $profile->name }}
                                            </option>
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

                <div class="modal-footer border-0 p-4 pt-0 d-none edit-mode-{{ $user->id }}">
                    <button type="button" class="btn btn-light fw-bold px-4" onclick="location.reload()">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold px-5 shadow-sm" style="background-color: var(--coffee-brown); border-radius: 10px;">
                        <i class="bi bi-check-lg me-2"></i>Salvar Alterações
                    </button>
                </div>
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
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                    if (icon) icon.classList.add('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    }
</script>