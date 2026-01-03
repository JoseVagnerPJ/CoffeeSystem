@extends('layouts.app')

@section('title', 'Laboratório de Qualidade')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold">Laboratório de Qualidade</h2>
        <div class="btn-group shadow-sm">
            <button class="btn btn-dark active">Mesas de Prova</button>
            <button class="btn btn-outline-dark">Amostras Verdes</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 4px solid var(--coffee-gold) !important;">
                        <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Mesas Realizadas</small>
                        <div class="h3 fw-bold mb-0">{{ $totalSessions ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 bg-white">
                        <small class="text-uppercase fw-bold opacity-50" style="font-size: 0.65rem;">Xícaras Pontuadas</small>
                        <div class="h3 fw-bold mb-0">{{ $totalCups ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card p-3 shadow-sm border-0 text-white" style="background: var(--coffee-brown);">
                        <small class="text-uppercase fw-bold opacity-75" style="color: var(--coffee-gold); font-size: 0.65rem;">Maior Pontuação</small>
                        <div class="h3 fw-bold mb-0" style="color: var(--coffee-gold);">{{ number_format($maxScore ?? 0, 1) }} <small class="fs-6">SCA</small></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-white shadow-sm w-100 fw-bold border-0" style="height: 50px; color: var(--coffee-brown);">
                        <i class="bi bi-funnel-fill me-2 text-warning"></i> FILTRAR
                    </button>
                </div>
                <div class="col-sm-5">
                    <button class="btn text-white shadow-sm w-100 fw-bold border-0" style="height: 50px; background: #2e7d32;">
                        <i class="bi bi-plus-circle me-2"></i> NOVA MESA DE PROVA
                    </button>
                </div>
                <div class="col-sm-4">
                    <button class="btn shadow-sm w-100 fw-bold border-0" style="height: 50px; background: var(--coffee-gold); color: var(--coffee-brown);">
                        <i class="bi bi-magic me-2"></i> BLEND BUILDER
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 h-100 d-flex align-items-center justify-content-center text-center" style="background: white; border: 2px dashed var(--coffee-gold) !important;">
                <i class="bi bi-clipboard-pulse fs-1 opacity-25"></i>
                <p class="small fw-bold mt-2 mb-0 opacity-50">HISTÓRICO SENSORIAL GLOBAL</p>
                <button class="btn btn-link btn-sm text-decoration-none p-0 mt-2" style="color: var(--coffee-brown); font-size: 0.7rem;">GERAR RELATÓRIO PDF</button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background: white;">
        <h5 class="fw-bold mb-4">Últimas Mesas de Prova</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="small text-uppercase text-muted border-bottom">
                        <th style="width: 40px;"></th>
                        <th>Nome da Mesa</th>
                        <th>Xícaras</th>
                        <th>Data/Hora</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr class="shadow-sm" style="background: #fdfdfd;">
                        <td>
                            <button class="btn btn-sm text-dark" data-bs-toggle="collapse" data-bs-target="#collapseMesa{{ $session->id }}">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </td>
                        <td class="fw-bold">{{ $session->title }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $session->items->count() }} Xícaras</span></td>
                        <td class="small">{{ date('d/m/Y - H:i', strtotime($session->created_at)) }}</td>
                        <td>
                            <span class="badge bg-{{ $session->status == 'aberto' ? 'primary' : 'success' }}-subtle text-{{ $session->status == 'aberto' ? 'primary' : 'success' }} border border-{{ $session->status == 'aberto' ? 'primary' : 'success' }}-subtle">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td class="text-center text-nowrap">
                            <a href="{{ route('cupping.show', $session->id) }}"><i class="bi bi-info-circle text-primary me-2" style="cursor:pointer"></i></a>
                            <i class="bi bi-trash3 text-danger" style="cursor:pointer"></i>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="p-0 border-0">
                            <div id="collapseMesa{{ $session->id }}" class="collapse px-4 py-3" style="background: #fafafa; border-top: 1px solid #eee;">
                                <h6 class="fw-bold small text-muted text-uppercase mb-3">Detalhamento das Xícaras (Média da Mesa):</h6>
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <thead class="small opacity-75 border-bottom">
                                        <tr>
                                            <th class="text-center" style="width: 40px;">Nº</th>
                                            <th>Variedade</th>
                                            <th>Origem</th>
                                            <th class="text-center">Média SCA</th>
                                            <th class="text-center">Acidez</th>
                                            <th class="text-center">Doçura</th>
                                            <th class="text-center">Corpo</th>
                                            <th>Principais Tags</th>
                                            <th class="text-center">Obs</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @foreach($session->items as $item)
                                        <tr class="border-bottom">
                                            <td class="text-center fw-bold text-muted">{{ $item->position }}</td>
                                            <td><strong>{{ $item->sample->variety }}</strong></td>
                                            <td><span class="badge bg-light text-dark" style="font-size: 0.65rem;">{{ $item->sample->name }}</span></td>
                                            <td class="text-center fw-bold text-dark">
                                                {{ number_format($item->evaluations->avg('final_score'), 2) }}
                                            </td>
                                            <td class="text-center">{{ number_format($item->evaluations->avg('acidity'), 1) }}</td>
                                            <td class="text-center">{{ number_format($item->evaluations->avg('sweetness'), 1) }}</td>
                                            <td class="text-center">{{ number_format($item->evaluations->avg('body'), 1) }}</td>
                                            <td>
                                                <span class="text-warning fw-bold">
                                                    @foreach($item->evaluations->flatMap->tags->unique('id')->take(3) as $tag)
                                                        #{{ $tag->name }} 
                                                    @endforeach
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($item->evaluations->where('observations', '!=', null)->count() > 0)
                                                    <i class="bi bi-file-earmark-text text-primary" title="Possui observações"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection