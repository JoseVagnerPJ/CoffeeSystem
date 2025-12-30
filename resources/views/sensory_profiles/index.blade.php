@extends('layouts.app')

@section('title', 'Perfis Sensoriais')

@section('content')
<div class="px-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Perfis Sensoriais</h2>
            <p class="text-muted small mb-0">Gestão de identidades sensoriais e Blends do laboratório.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-dark shadow-sm d-flex align-items-center" 
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
                                    <form action="{{ route('sensory-profiles.destroy', $profile->id) }}" method="POST" onsubmit="return confirm('Excluir perfil?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash-fill fs-5"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr style="pointer-events: none; background: transparent !important;">
                            <td colspan="6" class="text-center py-5 text-muted">Nenhum perfil cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalViewProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="modalViewProfileContent" style="border-radius: 16px;"></div>
    </div>
</div>

@include('sensory_profiles.partials.create')

@push('scripts')
<script>
    $(document).ready(function() {
        console.log("Sistema de Perfis carregado.");

        $(document).on('click', '.btn-view-profile', function() {
            var profileId = $(this).data('id');
            var modalContent = $('#modalViewProfileContent');
            var modalElement = document.getElementById('modalViewProfile');

            // Feedback visual de carregamento
            modalContent.html('<div class="p-5 text-center"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Buscando ficha técnica...</p></div>');
            
            // Abre o modal
            var bsModal = new bootstrap.Modal(modalElement);
            bsModal.show();

            // Busca os dados via AJAX
            $.get('/sensory-profiles/' + profileId, function(data) {
                modalContent.html(data);
            }).fail(function() {
                modalContent.html('<div class="p-4 text-center text-danger">Erro ao buscar dados do perfil sensorial.</div>');
            });
        });
    });
</script>
@endpush
@endsection