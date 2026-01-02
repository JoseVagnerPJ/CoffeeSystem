@extends('layouts.app')

@section('title', 'Perfis Sensoriais')

@section('content')
<div class="px-3">
    {{-- Header Padronizado --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-lg-3">
            <h2 class="h4 fw-bold mb-0" style="color: var(--coffee-brown);">Perfis Sensoriais</h2>
            <p class="text-muted small mb-0">Gestão de identidades sensoriais e Blends.</p>
        </div>
        
        {{-- Busca Centralizada --}}
        <div class="col-lg-6">
            {{-- Rota atualizada --}}
            <form action="{{ route('registries.sensory-profiles.index') }}" method="GET">
                <div class="input-group shadow-sm mx-auto" style="max-width: 700px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 py-2" 
                           placeholder="Pesquisar nome do perfil ou notas..." value="{{ request('search') }}">
                    
                    <button type="button" class="btn btn-outline-dark px-3 d-flex align-items-center gap-2" 
                            data-bs-toggle="modal" data-bs-target="#modalFilterSensory">
                        <i class="bi bi-sliders"></i>
                        <span class="d-none d-md-inline">Filtros</span>
                    </button>
                    
                    <button type="submit" class="btn btn-dark px-4 fw-bold">Buscar</button>
                </div>
            </form>
        </div>

        <div class="col-lg-3 text-lg-end">
            <button type="button" class="btn btn-dark shadow-sm d-inline-flex align-items-center px-4 py-2" 
                    data-bs-toggle="modal" data-bs-target="#modalCreateProfile">
                <i class="bi bi-plus-lg me-2"></i> Novo Perfil
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Cor</th>
                        <th>Nome do Perfil</th>
                        <th>Tipo</th>
                        <th>Torra</th>
                        <th>Notas</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                        <tr>
                            <td class="ps-4">
                                <div style="width: 25px; height: 25px; border-radius: 6px; background-color: {{ $profile->color_hex }}; border: 1px solid #ddd;"></div>
                            </td>
                            <td class="fw-bold text-dark">{{ $profile->name }}</td>
                            <td>
                                <span class="badge {{ $profile->is_blend ? 'bg-primary' : 'bg-secondary' }} rounded-pill" style="font-size: 0.7rem;">
                                    {{ $profile->is_blend ? 'BLEND' : 'ÚNICO' }}
                                </span>
                            </td>
                            <td>{{ $profile->roast_intensity }}</td>
                            <td class="text-muted small">{{ $profile->notes }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0 btn-view-profile" 
                                            data-id="{{ $profile->id }}" title="Ver Ficha">
                                        <i class="bi bi-eye-fill fs-5"></i>
                                    </button>
                                    {{-- Rota atualizada --}}
                                    <form action="{{ route('registries.sensory-profiles.destroy', $profile->id) }}" method="POST" onsubmit="return confirm('Excluir perfil?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Excluir">
                                            <i class="bi bi-trash-fill fs-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr style="pointer-events: none; background: transparent !important;">
                            <td colspan="6" class="text-center py-5 text-muted">
                                Nenhum perfil encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modais --}}
<div class="modal fade" id="modalViewProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="modalViewProfileContent" style="border-radius: 16px;"></div>
    </div>
</div>

{{-- Includes com caminhos atualizados --}}
@include('registries.sensory-profiles.partials.create')
@include('registries.sensory-profiles.partials.filter')

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-view-profile', function() {
            var profileId = $(this).data('id');
            var modalContent = $('#modalViewProfileContent');
            modalContent.html('<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>');
            new bootstrap.Modal('#modalViewProfile').show();
            // URL AJAX atualizada para o novo prefixo registries
            $.get('/registries/sensory-profiles/' + profileId, function(data) {
                modalContent.html(data);
            });
        });
    });
</script>
@endpush
@endsection