@extends('layouts.app')

@section('title', 'Perfis de Prova SCA')

@section('content')
<div class="fade-in px-3">
    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Perfis de Prova SCA</h2>
            <p class="text-muted">Configure quais atributos e escalas estarão disponíveis nas mesas de prova.</p>
        </div>
        <button class="btn btn-dark fw-bold px-4 shadow-sm" style="background-color: var(--coffee-brown); border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#modalAddProfile">
            <i class="bi bi-plus-lg me-2"></i> Novo Perfil
        </button>
    </div>

    {{-- Listagem de Perfis --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Nome do Perfil</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase">Descrição</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase text-center">Configuração</th>
                        <th class="py-3 text-muted small fw-bold text-uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark">{{ $profile->name }}</span>
                        </td>
                        <td>
                            @if($profile->description)
                                <small class="text-muted">{{ Str::limit($profile->description, 60) }}</small>
                            @else
                                <small class="text-muted fst-italic">Sem descrição informada</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                // Conta dinamicamente quantos atributos 'eval_' estão como true
                                $activeCount = collect($profile->getAttributes())
                                    ->filter(fn($val, $key) => str_starts_with($key, 'eval_') && $val == true)
                                    ->count();
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 shadow-sm" 
                                  style="background-color: var(--coffee-brown); color: var(--coffee-gold); border: 1px solid var(--coffee-gold); font-size: 0.85rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ $activeCount }}/11 Atributos
                            </span>
                        </td>
                        <td class="text-center">
                            {{-- Botão Visualizar Detalhes --}}
                            <button class="btn btn-sm btn-outline-primary border-0 me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalViewProfile{{ $profile->id }}"
                                    title="Visualizar Configurações">
                                <i class="bi bi-eye-fill fs-5"></i>
                            </button>

                            {{-- Botão Excluir --}}
                            <form action="{{ route('registries.cupping-profiles.destroy', $profile->id) }}" method="POST" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0" 
                                        onclick="return confirm('Tem certeza que deseja excluir este perfil permanentemente?')"
                                        title="Excluir Perfil">
                                    <i class="bi bi-trash3-fill fs-5"></i>
                                </button>
                            </form>

                            {{-- Incluindo o Modal de Visualização (específico para cada linha) --}}
                            @include('registries.cupping-profiles.partials.modal_view', ['profile' => $profile])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-clipboard-x text-muted opacity-25" style="font-size: 4rem;"></i>
                                <p class="text-muted fw-bold mt-3">Nenhum perfil de prova cadastrado até o momento.</p>
                                <small class="text-muted d-block">Clique em "Novo Perfil" para definir seu primeiro protocolo.</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Incluindo o Modal de Criação (fora do loop) --}}
@include('registries.cupping-profiles.partials.modal_create')

@endsection