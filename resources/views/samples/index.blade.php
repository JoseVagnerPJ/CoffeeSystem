@extends('layouts.app')

@section('title', 'Gestão de Amostras')

@section('content')
<div class="px-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 fw-bold" style="color: var(--coffee-brown);">Cadastro de Amostras</h2>
            <p class="text-muted small mb-0">Controle e rastreabilidade técnica de cafés recebidos.</p>
        </div>
        
        <div class="d-flex gap-2">
            <form action="{{ route('samples.index') }}" method="GET" class="d-flex">
                <div class="input-group shadow-sm" style="min-width: 300px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                           placeholder="Pesquisar nome, lote ou origem..." value="{{ request('search') }}">
                </div>
            </form>

            <button type="button" class="btn btn-dark shadow-sm d-flex align-items-center" 
                    data-bs-toggle="modal" data-bs-target="#modalCreateSample">
                <i class="bi bi-plus-lg me-2"></i> Nova Amostra
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Lote Interno</th>
                        <th>Nome / Apelido</th>
                        <th>Origem (Fornecedor)</th>
                        <th>Data</th>
                        <th>Variedade</th>
                        <th>Processo</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($samples as $sample)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#{{ str_pad($sample->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $sample->name }}</span>
                                <div class="small text-muted">Lote Forn: {{ $sample->supplier_batch }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $sample->origin->name ?? 'N/D' }}</div>
                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;">
                                    {{ $sample->origin_type == 'App\Models\Farm' ? 'Fazenda' : ($sample->origin_type == 'App\Models\Producer' ? 'Produtor' : 'Empresa') }}
                                </small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($sample->receipt_date)->format('d/m/Y') }}</td>
                            <td>{{ $sample->variety }}</td>
                            <td>
                                <span class="badge rounded-pill border text-dark fw-normal" style="background-color: var(--coffee-cream);">
                                    {{ $sample->drying_process }}
                                </span>
                            </td>
                            <td class="text-end pe-4" style="white-space: nowrap;">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Botão INFO (Olho) --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary border-0 btn-view-sample" 
                                            data-sample-id="{{ $sample->id }}" 
                                            title="Informações Técnicas">
                                        <i class="bi bi-eye-fill fs-5"></i>
                                    </button>

                                    {{-- Botão EXCLUIR (Lixeira) --}}
                                    <form action="{{ route('samples.destroy', $sample->id) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Excluir amostra #{{ str_pad($sample->id, 4, '0', STR_PAD_LEFT) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Excluir">
                                            <i class="bi bi-trash-fill fs-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Nenhuma amostra encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL PARA VISUALIZAÇÃO (O POPUP "INFO") --}}
<div class="modal fade" id="modalViewSample" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="modalViewSampleContent" style="border-radius: 16px;">
            {{-- Conteúdo carregado via AJAX --}}
        </div>
    </div>
</div>

@include('samples.partials.create')

{{-- Script colocado direto aqui para garantir execução caso o @stack falhe --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Seleciona todos os botões com a classe btn-view-sample
        const buttons = document.querySelectorAll('.btn-view-sample');
        const modalElement = document.getElementById('modalViewSample');
        const modalContent = document.getElementById('modalViewSampleContent');
        
        // Inicializa o modal do Bootstrap
        const bsModal = new bootstrap.Modal(modalElement);

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const sampleId = this.getAttribute('data-sample-id');
                
                // Limpa e coloca o loading
                modalContent.innerHTML = `
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Carregando dados da amostra...</p>
                    </div>`;
                
                bsModal.show();

                // Faz a requisição para buscar o info.blade.php
                fetch(`/samples/${sampleId}/info`)
                    .then(response => {
                        if (!response.ok) throw new Error('Erro na requisição');
                        return response.text();
                    })
                    .then(html => {
                        modalContent.innerHTML = html;
                    })
                    .catch(error => {
                        modalContent.innerHTML = `
                            <div class="modal-body text-center py-4">
                                <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                <p class="text-danger fw-bold">Erro ao carregar informações.</p>
                            </div>`;
                        console.error(error);
                    });
            });
        });
    });
</script>

@endsection