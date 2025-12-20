<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Rotas do Sistema Unique Cafés - ERP Torrefação
|--------------------------------------------------------------------------
*/

// --- MÓDULOS OPERACIONAIS ---

// Dashboard Principal
Route::get('/', [DashboardController::class, 'index']);

// Gestão de Estoque Verde
Route::get('/estoque', [DashboardController::class, 'estoque']);

// Laboratório de Qualidade (SCA / Amostras)
Route::get('/laboratorio', [DashboardController::class, 'laboratorio']);

// Controle de Torrefação (Kanban)
Route::get('/torrefacao', [DashboardController::class, 'torrefacao']);


// --- GESTÃO E CONFIGURAÇÕES ---

// Hub de Cadastros (Fornecedores, Cafés, Maquinário, Equipe)
Route::get('/cadastros', [DashboardController::class, 'cadastros']);

// Configurações do Sistema e Regras de Negócio
Route::get('/configuracoes', [DashboardController::class, 'configuracoes']);