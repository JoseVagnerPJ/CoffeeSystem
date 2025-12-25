<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GreenCoffeeLotController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MachineController;

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
    Route::get('/estoque', [GreenCoffeeLotController::class, 'index'])->name('estoque');
    Route::post('/estoque', [GreenCoffeeLotController::class, 'store'])->name('estoque.store');
    Route::delete('/estoque/{id}', [GreenCoffeeLotController::class, 'destroy'])->name('estoque.destroy');
    Route::get('/estoque/capture/{id}', [GreenCoffeeLotController::class, 'capture'])->name('estoque.capture');
    Route::get('/estoque/clear-capture', [GreenCoffeeLotController::class, 'clearCapture'])->name('estoque.clear-capture');

    // --- FORNECEDORES ---
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');    
    Route::get('/suppliers/capture/{type}/{id}', [SupplierController::class, 'capture'])->name('suppliers.capture');
    Route::get('/suppliers/clear', [SupplierController::class, 'clearCapture'])->name('suppliers.clear');
    Route::get('/suppliers/link/{type}/{id}', [SupplierController::class, 'link'])->name('suppliers.link');
    Route::post('/suppliers/producer', [SupplierController::class, 'storeProducer'])->name('suppliers.storeProducer');
    Route::post('/suppliers/farm', [SupplierController::class, 'storeFarm'])->name('suppliers.storeFarm');
    Route::post('/suppliers/company', [SupplierController::class, 'storeCompany'])->name('suppliers.storeCompany');
    Route::get('/configuracoes', [DashboardController::class, 'configuracoes'])->name('configuracoes');

    // --- LABORATÓRIO ---
    Route::get('/laboratorio', [DashboardController::class, 'laboratorio'])->name('laboratorio');

    // --- TORREFAÇÃO ---
    Route::get('/torrefacao', [DashboardController::class, 'torrefacao'])->name('torrefacao');

    // --- CADASTROS ---
    Route::get('/cadastros', [DashboardController::class, 'cadastros'])->name('cadastros');
    
    // --- MÁQUINAS ---
    Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');
    Route::post('/machines', [MachineController::class, 'store'])->name('machines.store');
    Route::patch('/machines/{id}/inactivate', [MachineController::class, 'inactivate'])->name('machines.inactivate');
    Route::post('/machines/{id}/maintenance', [MachineController::class, 'storeMaintenance'])->name('machines.storeMaintenance');


});