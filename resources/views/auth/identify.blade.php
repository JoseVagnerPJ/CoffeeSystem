@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg" style="width: 100%; max-width: 400px; border-radius: 15px;">
        <div class="card-body p-4 text-center">
            <div class="mb-4">
                <h4 class="fw-bold" style="color: var(--coffee-brown);">Identificação</h4>
                <p class="text-muted small">Informe o CNPJ da empresa para acessar o terminal</p>
            </div>

            <form method="POST" action="{{ route('identify') }}">
                @csrf
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj_input" 
                           class="form-control bg-light border-0 text-center fw-bold" 
                           placeholder="00.000.000/0000-00" 
                           maxlength="18" required autofocus>
                    @error('cnpj') <div class="text-danger small fw-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow" style="background-color: var(--coffee-brown);">
                    CONTINUAR
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Máscara simples para CNPJ
    document.getElementById('cnpj_input').addEventListener('input', function (e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
        e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + '.' + x[3] + '/' + x[4] + (x[5] ? '-' + x[5] : '');
    });
</script>
@endsection