@extends('layouts.app')

@section('title', 'Criar Conta')

@section('content')
<div class="d-flex align-items-center justify-content-center animate-fade-in" style="min-height: 85vh;">
    <div class="card border-0 shadow-lg" style="width: 100%; max-width: 450px; border-radius: 15px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="fw-bold" style="color: var(--coffee-brown);">Novo Usuário</h4>
                <p class="text-muted small">Crie seu acesso administrativo</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm small py-2 mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-bold">NOME COMPLETO</label>
                    <input type="text" name="name" 
                           class="form-control bg-light border-0 @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback small fw-bold">Por favor, insira seu nome.</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">E-MAIL</label>
                    <input type="email" name="email" 
                           class="form-control bg-light border-0 @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback small fw-bold">Este e-mail já está em uso ou é inválido.</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">SENHA</label>
                        <input type="password" name="password" 
                               class="form-control bg-light border-0 @error('password') is-invalid @enderror" 
                               required>
                        @error('password')
                            <div class="invalid-feedback small fw-bold">Mínimo 8 caracteres.</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">CONFIRMAR</label>
                        <input type="password" name="password_confirmation" 
                               class="form-control bg-light border-0" 
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow mt-2" 
                        style="background-color: var(--coffee-brown); border: none; border-radius: 8px;">
                    CRIAR MINHA CONTA
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="small text-decoration-none" style="color: var(--coffee-gold);">
                        Já tem conta? Faça Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection