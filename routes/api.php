<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CuppingController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\RoastController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Q-App & Q-Master
|--------------------------------------------------------------------------
*/

// Rota de Login (Pública)
// O AuthController deve validar se o usuário tem permissão mínima para logar no App
Route::post('/login', [AuthController::class, 'login']);

// Rotas Protegidas por Token
Route::middleware('auth:sanctum')->group(function () {

    /*
    |----------------------------------------------------------------------
    | MÓDULO: LABORATÓRIO (Q-App / Q-Taster)
    |----------------------------------------------------------------------
    */
    // Grupo para quem pode Visualizar/Provar (lab_view OU lab_full)
    Route::middleware('can:lab_view,lab_full')->group(function () {
        
        // Listagem e Detalhes de Mesas
        Route::get('/cupping/sessions', [CuppingController::class, 'index']);
        Route::get('/cupping/sessions/{id}', [CuppingController::class, 'show']);
        
        // Participação e Notas
        Route::post('/cupping/sessions/{id}/join', [CuppingController::class, 'joinSession']);
        Route::post('/cupping/sessions/finish', [CuppingController::class, 'finishEvaluations']);
        
        // Auxiliares (Tags sensoriais)
        Route::get('/tags', [TagController::class, 'index']);

        // Ação Restrita: APENAS quem tem acesso TOTAL pode iniciar uma mesa nova pelo App
        Route::post('/cupping/sessions', [CuppingController::class, 'startSession'])
              ->middleware('can:lab_full');
    });

    /*
    |----------------------------------------------------------------------
    | MÓDULO: TORREFAÇÃO (Q-Master)
    |----------------------------------------------------------------------
    */
    // Grupo para quem opera ou visualiza a torra (roast_view OU roast_full)
    Route::middleware('can:roast_view,roast_full')->group(function () {
        
        // Fila de Produção
        Route::get('/my-queue', [RoastController::class, 'apiQueue']);
        
        // Controle de Batch (Lote)
        Route::post('/batch/{id}/start', [RoastController::class, 'apiStartBatch']);
        Route::post('/batch/{id}/finish', [RoastController::class, 'apiFinishBatch']);
        Route::post('/batch/{id}/discard', [RoastController::class, 'apiDiscardBatch']);
        
        // Manutenção de Equipamentos
        Route::post('/machines/{id}/clean', [RoastController::class, 'apiRegisterCleaning']);
    });

});