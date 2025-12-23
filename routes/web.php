<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GreenCoffeeLotController;

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard Principal (Início)
    Route::get('/', [DashboardController::class, 'index'])->name('inicio');

    // --- MÓDULO: ESTOQUE VERDE ---
    // Listagem de Lotes
    Route::get('/estoque', [GreenCoffeeLotController::class, 'index'])->name('estoque');
    // Salvar Novo Lote (POST)
    Route::post('/estoque', [GreenCoffeeLotController::class, 'store'])->name('estoque.store');
    // Capturar Lote (Sessão)
    Route::get('/estoque/capture/{id}', [GreenCoffeeLotController::class, 'capture'])->name('estoque.capture');
    // Soltar Lote (Limpar Sessão)
    Route::get('/estoque/clear-capture', [GreenCoffeeLotController::class, 'clearCapture'])->name('estoque.clear-capture');


    // --- DEMAIS MÓDULOS ---
    Route::get('/laboratorio', [DashboardController::class, 'laboratorio'])->name('laboratorio');
    Route::get('/torrefacao', [DashboardController::class, 'torrefacao'])->name('torrefacao');
    Route::get('/cadastros', [DashboardController::class, 'cadastros'])->name('cadastros');
    Route::get('/configuracoes', [DashboardController::class, 'configuracoes'])->name('configuracoes');

});