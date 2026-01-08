@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg" style="width: 100%; max-width: 400px; border-radius: 15px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 60px; margin-bottom: 10px;">
                <h5 class="fw-bold mb-0" style="color: var(--coffee-brown);">Ctrl + Coffee</h5>
                <hr class="opacity-10">
                
                <h4 class="fw-bold mt-3" style="color: var(--coffee-gold);">{{ session('login_company_name') }}</h4>
                <p class="text-muted small">Acesse sua conta</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">USUÁRIO</label>
                    <input type="text" name="username" class="form-control bg-light border-0" style="height: 45px;" required autofocus>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">SENHA</label>
                    <input type="password" name="password" class="form-control bg-light border-0" style="height: 45px;" required>
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-3 shadow" style="background-color: var(--coffee-brown); border-radius: 10px;">
                    ENTRAR
                </button>
                
                <div class="text-center mt-4">
                    <a href="{{ route('identify') }}" class="small text-muted text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Trocar Empresa
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection