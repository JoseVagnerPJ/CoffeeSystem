<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GreenCoffeeLotController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\SensoryProfileController;
use App\Http\Controllers\TeamController; // Importado o novo Controller

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
    Route::get('/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');    
    Route::get('/suppliers/capture/{type}/{id}', [SupplierController::class, 'capture'])->name('suppliers.capture');
    Route::get('/suppliers/clear', [SupplierController::class, 'clearCapture'])->name('suppliers.clear');
    Route::get('/suppliers/link/{type}/{id}', [SupplierController::class, 'link'])->name('suppliers.link');
    Route::post('/suppliers/producer', [SupplierController::class, 'storeProducer'])->name('suppliers.storeProducer');
    Route::post('/suppliers/farm', [SupplierController::class, 'storeFarm'])->name('suppliers.storeFarm');
    Route::post('/suppliers/company', [SupplierController::class, 'storeCompany'])->name('suppliers.storeCompany');
    Route::patch('/suppliers/inactivate/{type}/{id}', [SupplierController::class, 'inactivate'])->name('suppliers.inactivate');

    // --- CONFIGURAÇÕES / LABORATÓRIO / TORREFAÇÃO ---
    Route::get('/configuracoes', [DashboardController::class, 'configuracoes'])->name('configuracoes');
    Route::get('/laboratorio', [DashboardController::class, 'laboratorio'])->name('laboratorio');
    Route::get('/torrefacao', [DashboardController::class, 'torrefacao'])->name('torrefacao');
    Route::get('/cadastros', [DashboardController::class, 'cadastros'])->name('cadastros');
    
    // --- MÁQUINAS ---
    Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');
    Route::post('/machines', [MachineController::class, 'store'])->name('machines.store');
    Route::patch('/machines/{id}/inactivate', [MachineController::class, 'inactivate'])->name('machines.inactivate');
    Route::post('/machines/{id}/maintenance', [MachineController::class, 'storeMaintenance'])->name('machines.storeMaintenance');

    // --- PERFIS SENSORIAIS ---
    Route::get('/sensory-profiles/search-samples', [SensoryProfileController::class, 'searchSamples'])->name('sensory-profiles.search-samples');
    Route::resource('sensory-profiles', SensoryProfileController::class);
    
    // --- AMOSTRAS ---
    Route::get('/samples/search-origin', [SampleController::class, 'searchOrigin'])->name('samples.search-origin');
    Route::get('/samples/{id}/info', [SampleController::class, 'info'])->name('samples.info');
    Route::resource('samples', SampleController::class);

    // --- MÓDULO: EQUIPE (GESTÃO DA UNIDADE) ---
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::patch('/team/company', [TeamController::class, 'updateCompany'])->name('team.updateCompany');
    Route::post('/team/profile', [TeamController::class, 'storeProfile'])->name('team.storeProfile');
    Route::post('/team/user', [TeamController::class, 'storeUser'])->name('team.storeUser');
    Route::patch('/team/user/{id}/inactivate', [TeamController::class, 'inactivateUser'])->name('team.inactivateUser');
    Route::delete('/team/profile/{id}', [TeamController::class, 'destroyProfile'])->name('team.destroyProfile');
    Route::patch('/team/user/{id}', [TeamController::class, 'updateUser'])->name('team.updateUser');

});