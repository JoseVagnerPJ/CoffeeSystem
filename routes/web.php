<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// 1. Rotas de Autenticação (Breeze) - DEVEM FICAR FORA DO MIDDLEWARE AUTH
require __DIR__.'/auth.php';

// 2. Rotas Protegidas (Exigem Login)
// routes/web.php

// routes/web.php

Route::middleware(['auth'])->group(function () {
    // Certifique-se de que o ->name('inicio') está aqui
    Route::get('/', [DashboardController::class, 'index'])->name('inicio');
    
    Route::get('/estoque', [DashboardController::class, 'estoque'])->name('estoque');
    Route::get('/laboratorio', [DashboardController::class, 'laboratorio'])->name('laboratorio');
    Route::get('/torrefacao', [DashboardController::class, 'torrefacao'])->name('torrefacao');
    Route::get('/cadastros', [DashboardController::class, 'cadastros'])->name('cadastros');
    Route::get('/configuracoes', [DashboardController::class, 'configuracoes'])->name('configuracoes');
});

require __DIR__.'/auth.php';