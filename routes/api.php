<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

// ─── Rutas públicas ───────────────────────────────────────────
Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');

// ─── Rutas protegidas — solo nuestro middleware ───────────────
Route::middleware(['auth.tenant.api'])->group(function () {

    Route::get ('/me',     [ApiAuthController::class, 'me'])    ->name('api.me');
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');

});