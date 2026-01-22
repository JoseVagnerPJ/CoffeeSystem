@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg" style="width: 100%; max-width: 400px; border-radius: 15px;">
        <div class="card-body p-4 text-center">
            <div class="mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Q-Grader Logo" style="width: 80px; margin-bottom: 10px;">
                <h3 class="fw-bold" style="color: var(--coffee-brown); letter-spacing: -1px;">Q-Grader</h3>
                <hr class="opacity-10">
            </div>

            <div class="mb-4">
                <h5 class="fw-bold" style="color: var(--coffee-text);">Identificação</h5>
                <p class="text-muted small">Informe o CNPJ da empresa para acessar o terminal</p>
            </div>

            <form method="POST" action="{{ route('identify') }}" id="identify_form">
                @csrf
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj_input" 
                           class="form-control bg-light border-0 text-center fw-bold" 
                           style="height: 50px; font-size: 1.1rem;"
                           placeholder="00.000.000/0000-00" 
                           maxlength="18" required autofocus>
                    
                    {{-- CONTAINER PARA CNPJS RECENTES --}}
                    <div id="recent_container" class="mt-2 d-none">
                        <small class="text-muted fw-bold" style="font-size: 0.6rem; text-transform: uppercase;">Acesso Rápido:</small>
                        <div id="recent_list" class="d-flex flex-wrap gap-1 mt-1"></div>
                    </div>

                    @error('cnpj') <div class="text-danger small fw-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-3 shadow mb-3" style="background-color: var(--coffee-brown); border-radius: 10px;">
                    CONTINUAR
                </button>
            </form>

            {{-- BOTÃO DISCRETO PARA POLÍTICA DE PRIVACIDADE --}}
            <div class="mt-2 text-center">
                <a href="{{ route('privacy') }}" target="_blank" class="text-muted small text-decoration-none opacity-50" style="font-size: 0.7rem;">
                    <i class="bi bi-shield-lock me-1"></i>Política de Privacidade
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    const cnpjInput = document.getElementById('cnpj_input');
    const identifyForm = document.getElementById('identify_form');
    const recentContainer = document.getElementById('recent_container');
    const recentList = document.getElementById('recent_list');

    // 1. Máscara simples para CNPJ
    cnpjInput.addEventListener('input', function (e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
        e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + '.' + x[3] + '/' + x[4] + (x[5] ? '-' + x[5] : '');
    });

    // 2. Carregar CNPJs recentes (até 6) ao abrir a página
    document.addEventListener('DOMContentLoaded', function() {
        const saved = JSON.parse(localStorage.getItem('qgrader_recent_cnpjs') || '[]');
        if (saved.length > 0) {
            recentContainer.classList.remove('d-none');
            saved.forEach(cnpj => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-secondary bg-opacity-10 text-dark border-0 p-2';
                badge.style.cursor = 'pointer';
                badge.style.fontSize = '0.7rem';
                badge.innerHTML = `<i class="bi bi-clock-history me-1"></i> ${cnpj}`;
                badge.onclick = function() {
                    cnpjInput.value = cnpj;
                    cnpjInput.dispatchEvent(new Event('input'));
                };
                recentList.appendChild(badge);
            });
        }
    });

    // 3. Salvar o CNPJ no LocalStorage (Limite de 6)
    identifyForm.addEventListener('submit', function() {
        const currentCnpj = cnpjInput.value;
        // Verifica se o CNPJ está completo antes de salvar
        if (currentCnpj.length === 18) {
            let saved = JSON.parse(localStorage.getItem('qgrader_recent_cnpjs') || '[]');
            
            // Remove duplicata se já existir para que o novo suba para o topo
            saved = saved.filter(item => item !== currentCnpj);
            
            // Adiciona no início da array
            saved.unshift(currentCnpj);
            
            // Limita aos 6 primeiros itens (os mais recentes)
            localStorage.setItem('qgrader_recent_cnpjs', JSON.stringify(saved.slice(0, 6)));
        }
    });
</script>
@endsection