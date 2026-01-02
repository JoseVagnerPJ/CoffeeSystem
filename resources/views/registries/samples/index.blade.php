@extends('layouts.app')

@section('title', 'Gestão de Amostras')

@section('content')
<div class="px-3">
    {{-- Header Reorganizado --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-lg-3">
            <h2 class="h4 fw-bold mb-0" style="color: var(--coffee-brown);">Cadastro de Amostras</h2>
            <p class="text-muted small mb-0">Rastreabilidade técnica de cafés.</p>
        </div>
        
        {{-- Pesquisa Centralizada e Extendida --}}
        <div class="col-lg-6">
            {{-- Rota atualizada para registries.samples.index --}}
            <form action="{{ route('registries.samples.index') }}" method="GET" id="mainSearchForm">
                {{-- Mantém os filtros ativos na URL se houver --}}
                @foreach(request()->except('search') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                
                <div class="input-group shadow-sm mx-auto" style="max-width: 700px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 py-2" 
                           placeholder="Nome, lote ou variedade..." value="{{ request('search') }}">
                    
                    <button type="button" class="btn btn-outline-dark px-3 d-flex align-items-center gap-2" 
                            data-bs-toggle="modal" data-bs-target="#modalFilterSample">
                        <i class="bi bi-sliders"></i>
                        <span class="d-none d-md-inline">Filtros</span>
                    </button>
                    
                    <button type="submit" class="btn btn-dark px-4 fw-bold">Buscar</button>
                </div>
            </form>
        </div>

        <div class="col-lg-3 text-lg-end">
            {{-- Link de voltar atualizado para o Hub de Cadastros --}}
            <a href="{{ route('registries.index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-2">Voltar</a>
            
            <button type="button" class="btn btn-success shadow-sm d-inline-flex align-items-center px-4 py-2" 
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
                        <th>Fermentação</th>
                        <th>Peneira</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($samples as $sample)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#{{ $sample->id }}</td>
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
                            <td>{{ $sample->fermentation ?? '-' }}</td>
                            <td>{{ $sample->screen ?? '-' }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0 btn-view-sample" 
                                            data-sample-id="{{ $sample->id }}"><i class="bi bi-eye-fill fs-5"></i></button>
                                    
                                    {{-- Rota de destruição atualizada para registries.samples.destroy --}}
                                    <form action="{{ route('registries.samples.destroy', $sample->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash-fill fs-5"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        {{-- O modal de info é carregado dinamicamente via AJAX, não precisa de include no loop --}}
                    @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted">Nenhuma amostra encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modais --}}
<div class="modal fade" id="modalViewSample" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="modalViewSampleContent" style="border-radius: 16px;"></div>
    </div>
</div>

{{-- Includes atualizados para a nova estrutura de pastas --}}
@include('registries.samples.partials.create')
@include('registries.samples.partials.filter')

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-view-sample', function() {
            var sampleId = $(this).data('sample-id');
            var modalContent = $('#modalViewSampleContent');
            
            // Visual de carregamento
            modalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary"></div></div>');
            new bootstrap.Modal('#modalViewSample').show();
            
            // Ajuste da URL do AJAX para o novo prefixo de rota
            $.get('/registries/samples/' + sampleId + '/info', function(data) { 
                modalContent.html(data); 
            }).fail(function() {
                modalContent.html('<div class="modal-body text-center py-4 text-danger">Erro ao carregar informações.</div>');
            });
        });
    });
</script>
@endpush
@endsection