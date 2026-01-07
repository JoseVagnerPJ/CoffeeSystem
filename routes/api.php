<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CuppingController;
use Illuminate\Support\Facades\Route;

// Rota de Login (Pública)
Route::post('/login', [AuthController::class, 'login']);

// Rotas Protegidas (Exigem Token)
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cupping/sessions', [CuppingController::class, 'index']);
    Route::get('/cupping/sessions/{id}', [CuppingController::class, 'show']); // NOVA ROTA
    Route::post('/cupping/sessions', [CuppingController::class, 'startSession']);
    Route::post('/cupping/sessions/finish', [CuppingController::class, 'finishEvaluations']);
    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/cupping/sessions/{id}/join', [CuppingController::class, 'joinSession']);
});

