<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CuppingController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\RoastController;
use Illuminate\Support\Facades\Route;

// Rota de Login (Pública)
Route::post('/login', [AuthController::class, 'login']);

// Rotas Protegidas
Route::middleware('auth:sanctum')->group(function () {

//  Q-Taster
    Route::get('/cupping/sessions', [CuppingController::class, 'index']);
    Route::get('/cupping/sessions/{id}', [CuppingController::class, 'show']); // NOVA ROTA
    Route::post('/cupping/sessions', [CuppingController::class, 'startSession']);
    Route::post('/cupping/sessions/finish', [CuppingController::class, 'finishEvaluations']);
    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/cupping/sessions/{id}/join', [CuppingController::class, 'joinSession']);

//  Q-Master
    Route::get('/my-queue', [RoastController::class, 'apiQueue']);
    Route::post('/batch/{id}/start', [RoastController::class, 'apiStartBatch']);
    Route::post('/batch/{id}/finish', [RoastController::class, 'apiFinishBatch']);
    Route::post('/batch/{id}/discard', [RoastController::class, 'apiDiscardBatch']);
    Route::post('/machines/{id}/clean', [RoastController::class, 'apiRegisterCleaning']);
});

