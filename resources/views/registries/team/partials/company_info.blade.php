{{-- Aba: Minha Empresa --}}
<div class="row">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Dados Cadastrais</h6>
            {{-- Botão para destravar edição --}}
            <button type="button" id="btn-unlock-company" class="btn btn-sm btn-outline-warning border-0 fw-bold">
                <i class="bi bi-pencil-square me-1"></i> Alterar Informações
            </button>
        </div>

        {{-- Importante: enctype para permitir upload de arquivos --}}
        <form action="{{ route('registries.team.updateCompany') }}" method="POST" id="form-company" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="info-label">Nome da Unidade / Razão Social</label>
                    <input type="text" name="name" id="input-company-name" class="form-control border-0 bg-light p-3 company-input" 
                           value="{{ $company->name }}" required disabled style="border-radius: 10px;">
                </div>

                <div class="col-md-6">
                    <label class="info-label">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj_edit" class="form-control border-0 bg-light p-3 company-input" 
                           value="{{ $company->cnpj }}" required disabled style="border-radius: 10px;">
                </div>

                <div class="col-md-6">
                    <label class="info-label">ID do Terminal (Sistema)</label>
                    <input type="text" class="form-control border-0 bg-white p-3 fw-bold text-muted" 
                           value="#{{ str_pad($company->id, 4, '0', STR_PAD_LEFT) }}" readonly style="cursor: not-allowed;">
                </div>

                {{-- Botão de salvar (oculto por padrão) --}}
                <div class="col-12 mt-4 text-end d-none" id="save-company-container">
                    <button type="button" id="btn-cancel-edit" class="btn btn-light px-4 py-2 me-2 fw-bold" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-dark px-5 py-2 fw-bold" style="border-radius: 10px; background-color: var(--coffee-brown);">
                        <i class="bi bi-check2-all me-2"></i>Salvar Alterações
                    </button>
                </div>
            </div>
    </div>

    {{-- Lado Direito: Logo com cantos arredondados --}}
    <div class="col-lg-4 border-start ps-lg-5 mt-4 mt-lg-0">
        <div class="text-center">
            <label class="info-label mb-3">Logo da Unidade</label>
            
            <div class="bg-white rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3 position-relative shadow-sm" 
                 style="width: 150px; height: 150px; border: 2px dashed #ddd; overflow: hidden;">
                
                {{-- ID Adicionado para o Preview --}}
                <div id="logo-preview-container" class="w-100 h-100 d-flex align-items-center justify-content-center">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" id="img-company-logo" class="img-fluid w-100 h-100" style="object-fit: contain;" alt="Logo">
                    @else
                        <i class="bi bi-image text-muted fs-1" id="icon-no-logo"></i>
                    @endif
                </div>
                
                {{-- Botão de câmera para upload --}}
                <label for="logo_input" class="btn btn-sm btn-dark rounded-circle position-absolute bottom-0 end-0 d-none m-2 shadow" 
                       id="label-logo-input" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;">
                    <i class="bi bi-camera-fill"></i>
                    <input type="file" name="logo" id="logo_input" class="d-none" accept="image/*">
                </label>
            </div>
            <p class="small text-muted d-none" id="logo-hint">Toque na câmera para alterar</p>
        </div>
    </div>
    </form>
</div>

<script>
    // 1. Lógica para destravar e travar a edição
    document.getElementById('btn-unlock-company').addEventListener('click', function() {
        document.querySelectorAll('.company-input').forEach(input => {
            input.disabled = false;
            input.classList.replace('bg-light', 'bg-white');
            input.style.border = '1px solid #dee2e6';
        });

        document.getElementById('save-company-container').classList.remove('d-none');
        document.getElementById('label-logo-input').classList.remove('d-none');
        document.getElementById('logo-hint').classList.remove('d-none');
        this.classList.add('d-none');
        document.getElementById('input-company-name').focus();
    });

    // 2. LÓGICA DE PREVIEW DA LOGO (CORREÇÃO)
    document.getElementById('logo_input').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const container = document.getElementById('logo-preview-container');
            const icon = document.getElementById('icon-no-logo');
            let img = document.getElementById('img-company-logo');

            // Se não existia imagem antes (era apenas o ícone), cria a tag img
            if (!img) {
                if(icon) icon.remove();
                img = document.createElement('img');
                img.id = 'img-company-logo';
                img.className = 'img-fluid w-100 h-100';
                img.style.objectFit = 'contain';
                container.appendChild(img);
            }

            img.src = URL.createObjectURL(file);
        }
    });

    // 3. Botão Cancelar
    document.getElementById('btn-cancel-edit').addEventListener('click', function() { 
        location.reload(); 
    });

    // 4. Máscara de CNPJ
    document.getElementById('cnpj_edit').addEventListener('input', function (e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
        e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + '.' + x[3] + '/' + x[4] + (x[5] ? '-' + x[5] : '');
    });
</script>