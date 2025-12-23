{{-- Removemos qualquer menção a <x-guest-layout> --}}
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg p-4" style="width: 400px; border-radius: 15px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold" style="color: var(--coffee-brown);">Unique Cafés</h4>
            <p class="text-muted small">Acesse o sistema</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">E-mail</label>
                <input type="email" name="email" class="form-control bg-light border-0" required autofocus>
            </div>

            <div class="mb-4 text-start">
                <label class="form-label small fw-bold">Senha</label>
                <input type="password" name="password" class="form-control bg-light border-0" required>
            </div>

            <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow" style="background-color: var(--coffee-brown); border: none;">
                ENTRAR
            </button>
            
            <div class="text-center mt-3">
                <a href="{{ route('register') }}" class="small text-decoration-none" style="color: var(--coffee-gold);">Criar nova conta</a>
            </div>
        </form>
    </div>
</div>
@endsection