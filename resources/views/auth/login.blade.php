@extends('layouts.app')
@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg" style="width: 100%; max-width: 400px; border-radius: 15px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="fw-bold" style="color: var(--coffee-brown);">{{ session('login_company_name') }}</h4>
                <p class="text-muted small">Acesse sua conta</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">USUÁRIO</label>
                    <input type="text" name="username" class="form-control bg-light border-0" required autofocus>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">SENHA</label>
                    <input type="password" name="password" class="form-control bg-light border-0" required>
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow" style="background-color: var(--coffee-brown);">
                    ENTRAR
                </button>
                
                <div class="text-center mt-3">
                    <a href="{{ route('identify') }}" class="small text-muted text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Trocar Empresa
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection