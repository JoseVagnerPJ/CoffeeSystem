<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GreenCoffeeLotController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\SensoryProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Api\CuppingController;
use App\Http\Controllers\LaboratoryController;

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Rotas Protegidas do Navegador (Web)
|--------------------------------------------------------------------------
*/
// Removido o prefixo 'api' daqui para que o Dashboard funcione em "/"
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Principal - Agora acessível em "/"
    Route::get('/', [DashboardController::class, 'index'])->name('inicio');

    // --- MÓDULO: ESTOQUE VERDE ---
    Route::prefix('green-coffee')->name('estoque.')->group(function () {
        Route::get('/', [GreenCoffeeLotController::class, 'index'])->name('index');
        Route::post('/store', [GreenCoffeeLotController::class, 'store'])->name('store');
        Route::get('/{id}', [GreenCoffeeLotController::class, 'show'])->name('show'); 
        Route::delete('/{id}', [GreenCoffeeLotController::class, 'destroy'])->name('destroy');
        Route::get('/capture/{id}', [GreenCoffeeLotController::class, 'capture'])->name('capture');
        Route::get('/clear-capture', [GreenCoffeeLotController::class, 'clearCapture'])->name('clear-capture');
        Route::post('/{id}/attach', [GreenCoffeeLotController::class, 'storeAttachment'])->name('attach');
        Route::delete('/attachment/{id}', [GreenCoffeeLotController::class, 'destroyAttachment'])->name('attachment.destroy');

        // GRUPO DE INVENTÁRIO (AUDITORIA MENSAL)
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::post('/start', [GreenCoffeeLotController::class, 'startInventory'])->name('start');
            Route::post('/store', [GreenCoffeeLotController::class, 'storeInventoryAdjustment'])->name('store');
            Route::post('/finish', [GreenCoffeeLotController::class, 'finishInventory'])->name('finish');
            Route::get('/history/{id}', [GreenCoffeeLotController::class, 'getInventoryDetail'])->name('detail');
        });
    });

    // --- MÓDULO: LABORATÓRIO ---
        Route::prefix('laboratory')->group(function () {
            Route::get('/', [LaboratoryController::class, 'index'])->name('laboratorio');
            Route::delete('/{id}', [LaboratoryController::class, 'destroy'])->name('laboratory.destroy');
        });

    // --- MÓDULO: TORREFAÇÃO ---
    Route::get('/roasting', [DashboardController::class, 'torrefacao'])->name('torrefacao');

    // --- MÓDULO: CONFIGURAÇÕES (Geral) ---
    Route::get('/settings', [DashboardController::class, 'configuracoes'])->name('settings');

    // --- GRUPO MESTRE: CADASTROS (REGISTRIES) ---
    Route::prefix('registries')->name('registries.')->group(function () {
        Route::get('/', [DashboardController::class, 'cadastros'])->name('index');

        // Fornecedores
        Route::prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::get('/search', [SupplierController::class, 'search'])->name('search');
            Route::get('/capture/{type}/{id}', [SupplierController::class, 'capture'])->name('capture');
            Route::get('/clear', [SupplierController::class, 'clearCapture'])->name('clear');
            Route::get('/link/{type}/{id}', [SupplierController::class, 'link'])->name('link');
            Route::post('/producer', [SupplierController::class, 'storeProducer'])->name('storeProducer');
            Route::post('/farm', [SupplierController::class, 'storeFarm'])->name('storeFarm');
            Route::post('/company', [SupplierController::class, 'storeCompany'])->name('storeCompany');
            Route::patch('/inactivate/{type}/{id}', [SupplierController::class, 'inactivate'])->name('inactivate');
        });

        // Máquinas
        Route::prefix('machines')->name('machines.')->group(function () {
            Route::get('/', [MachineController::class, 'index'])->name('index');
            Route::post('/', [MachineController::class, 'store'])->name('store');
            Route::patch('/{id}/inactivate', [MachineController::class, 'inactivate'])->name('inactivate');
            Route::post('/{id}/maintenance', [MachineController::class, 'storeMaintenance'])->name('storeMaintenance');
        });

        // Amostras
        Route::prefix('samples')->name('samples.')->group(function () {
            // ADICIONADO: Rota de busca para Autocomplete na WEB (Estoque Verde)
            Route::get('/search', [SampleController::class, 'search'])->name('search');
            
            Route::get('/search-origin', [SampleController::class, 'searchOrigin'])->name('search-origin');
            Route::get('/{id}/info', [SampleController::class, 'info'])->name('info');
            
            Route::resource('/', SampleController::class)->only([
                'index', 'store', 'show', 'update', 'destroy'
            ])->names([
                'index'   => 'index',
                'store'   => 'store',
                'show'    => 'show',
                'update'  => 'update',
                'destroy' => 'destroy',
            ])->parameter('', 'sample');
        });

        // Perfis Sensoriais
        Route::prefix('sensory-profiles')->name('sensory-profiles.')->group(function () {
            Route::get('/search-samples', [SensoryProfileController::class, 'searchSamples'])->name('search-samples');
            
            Route::resource('/', SensoryProfileController::class)->only([
                'index', 'store', 'show', 'update', 'destroy'
            ])->names([
                'index'   => 'index',
                'store'   => 'store',
                'show'    => 'show',
                'update'  => 'update',
                'destroy' => 'destroy',
            ])->parameter('', 'sensory_profile');
        });

        // Equipe e Unidade
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('/', [TeamController::class, 'index'])->name('index');
            Route::patch('/company', [TeamController::class, 'updateCompany'])->name('updateCompany');
            Route::post('/profile', [TeamController::class, 'storeProfile'])->name('storeProfile');
            Route::post('/user', [TeamController::class, 'storeUser'])->name('storeUser');
            Route::patch('/user/{id}/inactivate', [TeamController::class, 'inactivateUser'])->name('inactivateUser');
            Route::delete('/profile/{id}', [TeamController::class, 'destroyProfile'])->name('destroyProfile');
            Route::patch('/user/{id}', [TeamController::class, 'updateUser'])->name('updateUser');
        });
    });
});

/*
|--------------------------------------------------------------------------
| --- ROTAS DA API MOBILE ---
|--------------------------------------------------------------------------
*/
// IMPORTANTE: Como estas rotas estão no web.php, você DEVE excluir 'api/*' 
// da verificação CSRF no arquivo bootstrap/app.php ou VerifyCsrfToken.php
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    
    // ROTA: Listagem geral de amostras
    Route::get('/samples', [SampleController::class, 'index']); 

    // Busca de Amostras para o Autocomplete (Mobile)
    Route::get('/samples/search', [SampleController::class, 'search'])->name('api.samples.search');
    
    // Operações de Cupping
    Route::prefix('cupping')->group(function () {
        Route::get('/sessions', [CuppingController::class, 'index']);
        Route::post('/sessions', [CuppingController::class, 'startSession']);
        Route::post('/evaluations', [CuppingController::class, 'finishEvaluations']);
        Route::get('/tags', [CuppingController::class, 'getTags']);
    });
});