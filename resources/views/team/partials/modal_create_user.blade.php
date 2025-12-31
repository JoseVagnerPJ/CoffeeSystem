<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: var(--coffee-brown);">Novo Colaborador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('team.storeUser') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Upload de Foto --}}
                        <div class="col-12 text-center mb-2">
                            <label class="info-label d-block text-center mb-2">Foto do Perfil</label>
                            <label for="new_user_photo" class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 80px; height: 80px; cursor: pointer; border: 2px dashed #ddd;">
                                <i class="bi bi-person-plus text-muted fs-3" id="photo-preview-icon"></i>
                                <img id="photo-preview" class="rounded-circle w-100 h-100 d-none" style="object-fit: cover;">
                                <input type="file" name="photo" id="new_user_photo" class="d-none" onchange="previewImage(this)">
                            </label>
                            <small class="text-muted" style="font-size: 0.6rem;">Opcional (Máx 2MB)</small>
                        </div>

                        <div class="col-12">
                            <label class="info-label">Nome Completo</label>
                            <input type="text" name="name" class="form-control bg-light border-0 p-3" placeholder="Nome do funcionário" required style="border-radius: 10px;">
                        </div>

                        <div class="col-md-6">
                            <label class="info-label">Usuário (Login)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted">@</span>
                                <input type="text" name="username" class="form-control bg-light border-0 p-3" placeholder="joao.silva" required style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="info-label">Senha Inicial</label>
                            <input type="password" name="password" class="form-control bg-light border-0 p-3" required style="border-radius: 10px;">
                        </div>

                        <div class="col-12">
                            <label class="info-label">Perfil de Acesso</label>
                            <select name="profile_id" class="form-select bg-light border-0 p-3" required style="border-radius: 10px;">
                                <option value="" selected disabled>Selecione um nível...</option>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold px-4" style="background-color: var(--coffee-brown); border-radius: 10px;">Salvar Colaborador</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview').src = e.target.result;
                document.getElementById('photo-preview').classList.remove('d-none');
                document.getElementById('photo-preview-icon').classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>