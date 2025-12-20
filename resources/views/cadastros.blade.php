@extends('layouts.app')

@section('title', 'Central de Cadastros')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h2 class="h4 fw-bold text-dark">Central de Cadastros</h2>
        <p class="text-muted small">Gerencie as bases técnicas e operacionais da Unique Cafés.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 transition-hover" style="cursor: pointer; border-radius: 15px;">
                <div class="card-body p-4 text-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Fornecedores</h5>
                    <p class="text-muted small mb-3">Gestão de Empresas e Produtores para rastreabilidade de origem.</p>
                    <div class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        {{ $cadastrosStats['fornecedores'] }} Registros
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-4 text-center">
                    <button class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">GERENCIAR</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 transition-hover" style="cursor: pointer; border-radius: 15px;">
                <div class="card-body p-4 text-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-tags fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Cafés & Variedades</h5>
                    <p class="text-muted small mb-3">Catálogo de espécies e tipos para laboratório e estoque.</p>
                    <div class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        {{ $cadastrosStats['variedades'] }} Tipos
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-4 text-center">
                    <button class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">GERENCIAR</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 transition-hover" style="cursor: pointer; border-radius: 15px;">
                <div class="card-body p-4 text-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-gear-wide-connected fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Maquinário</h5>
                    <p class="text-muted small mb-3">Configuração de capacidades e manutenção de torradores.</p>
                    <div class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        {{ $cadastrosStats['maquinas'] }} Máquinas
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-4 text-center">
                    <button class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">GERENCIAR</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 transition-hover" style="cursor: pointer; border-radius: 15px;">
                <div class="card-body p-4 text-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Equipe Técnica</h5>
                    <p class="text-muted small mb-3">Gestão de Mestres de Torra e Q-Graders do sistema.</p>
                    <div class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        {{ $cadastrosStats['equipe'] }} Membros
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-4 text-center">
                    <button class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">GERENCIAR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .transition-hover:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; 
    }
</style>
@endsection