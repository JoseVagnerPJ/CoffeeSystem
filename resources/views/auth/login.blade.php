@extends('layouts.app')

@section('title', 'Acesso ao Sistema')

@section('content')
<div class="d-flex align-items-center justify-content-center animate-fade-in" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg" style="width: 100%; max-width: 400px; border-radius: 15px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="fw-bold" style="color: var(--coffee-brown);">Unique Cafés</h4>
                <p class="text-muted small">Entre com suas credenciais</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm small py-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    @if($errors->has('email') || $errors->has('password'))
                        E-mail ou senha incorretos.
                    @else
                        Houve um problema ao processar seu login.
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">E-MAIL</label>
                    <input type="email" name="email" 
                           class="form-control bg-light border-0 @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback small fw-bold">Este e-mail não foi encontrado.</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">SENHA</label>
                    <input type="password" name="password" 
                           class="form-control bg-light border-0 @error('password') is-invalid @enderror" 
                           required>
                    @error('password')
                        <div class="invalid-feedback small fw-bold">Senha inválida.</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow" 
                        style="background-color: var(--coffee-brown); border: none; border-radius: 8px;">
                    ACESSAR SISTEMA
                </button>
                
                <div class="text-center mt-3">
                    <a href="{{ route('register') }}" class="small text-decoration-none" style="color: var(--coffee-gold);">
                        Ainda não tem acesso? Cadastre-se
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection