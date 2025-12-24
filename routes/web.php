<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GreenCoffeeLotController;
use App\Http\Controllers\SupplierController;

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
    // Deletar Lote (Delete)
    Route::delete('/estoque/{id}', [GreenCoffeeLotController::class, 'destroy'])->name('estoque.destroy');
    // Capturar Lote (Sessão)
    Route::get('/estoque/capture/{id}', [GreenCoffeeLotController::class, 'capture'])->name('estoque.capture');
    // Soltar Lote (Limpar Sessão)
    Route::get('/estoque/clear-capture', [GreenCoffeeLotController::class, 'clearCapture'])->name('estoque.clear-capture');


    // --- DEMAIS MÓDULOS ---
    Route::get('/laboratorio', [DashboardController::class, 'laboratorio'])->name('laboratorio');
    Route::get('/torrefacao', [DashboardController::class, 'torrefacao'])->name('torrefacao');
    Route::get('/cadastros', [DashboardController::class, 'cadastros'])->name('cadastros');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');    
    Route::get('/suppliers/capture/{type}/{id}', [SupplierController::class, 'capture'])->name('suppliers.capture');
    Route::get('/suppliers/clear', [SupplierController::class, 'clearCapture'])->name('suppliers.clear');
    Route::get('/suppliers/link/{type}/{id}', [SupplierController::class, 'link'])->name('suppliers.link');
    Route::post('/suppliers/producer', [SupplierController::class, 'storeProducer'])->name('suppliers.storeProducer');
    Route::post('/suppliers/farm', [SupplierController::class, 'storeFarm'])->name('suppliers.storeFarm');
    Route::post('/suppliers/company', [SupplierController::class, 'storeCompany'])->name('suppliers.storeCompany');
    Route::get('/configuracoes', [DashboardController::class, 'configuracoes'])->name('configuracoes');

});
// Rotas de Captura e Vínculo de Entidades