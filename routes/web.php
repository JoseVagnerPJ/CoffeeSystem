<?php

use Illuminate\Support\Facades\Route;
// ESSA LINHA ABAIXO É VITAL: Ela importa o Controller para que o Laravel o encontre
use App\Http\Controllers\DashboardController;

// Rota para o Dashboard (Página Inicial)
Route::get('/', [DashboardController::class, 'index']);

// Rota para o Estoque Verde
Route::get('/estoque', [DashboardController::class, 'estoque']);

// Rota para o Laboratório Sensorial
Route::get('/laboratorio', [DashboardController::class, 'laboratorio']);