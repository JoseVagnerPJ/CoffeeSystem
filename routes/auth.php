<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Nova rota de Identificação (CNPJ)
    Route::get('identify', [AuthenticatedSessionController::class, 'createIdentify'])->name('identify');
    Route::post('identify', [AuthenticatedSessionController::class, 'storeIdentify']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
