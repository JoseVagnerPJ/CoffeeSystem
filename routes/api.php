<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CuppingController;
use Illuminate\Support\Facades\Route;

// Rota de Login (Pública)
Route::post('/login', [AuthController::class, 'login']);

// Rotas Protegidas (Exigem Token)
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    
    // Rota que você já testou e deu []
    Route::get('/cupping/sessions', [CuppingController::class, 'index']);
    
    // ESTA É A LINHA QUE ESTÁ FALTANDO:
    Route::post('/cupping/sessions', [CuppingController::class, 'startSession']);

    // Rota para finalizar
    Route::post('/cupping/sessions/finish', [CuppingController::class, 'finishEvaluations']);
});

