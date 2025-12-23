@extends('layouts.app')

@section('title', 'Cadastro')

@section('content')
<div class="d-flex align-items-center justify-content-center animate-fade-in" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg" style="width: 100%; max-width: 450px; border-radius: 15px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h4 class="fw-bold" style="color: var(--coffee-brown);">Novo Usuário</h4>
                <p class="text-muted small">Cadastre-se para acessar o sistema</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase">Nome</label>
                    <input type="text" name="name" class="form-control bg-light border-0" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase">E-mail</label>
                    <input type="email" name="email" class="form-control bg-light border-0" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase">Senha</label>
                    <input type="password" name="password" class="form-control bg-light border-0" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" class="form-control bg-light border-0" required>
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow" style="background-color: var(--coffee-brown); border: none;">
                    CADASTRAR
                </button>
            </form>
        </div>
    </div>
</div>
@endsection