<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiClienteController;
use App\Http\Controllers\Api\ApiCiudadController;
use Illuminate\Support\Facades\Route;

// ─── Rutas públicas ───────────────────────────────────────────
Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');

// ─── Rutas protegidas ─────────────────────────────────────────
Route::middleware(['auth.tenant.api'])->group(function () {

    // Auth 
    Route::get ('/me',     [ApiAuthController::class, 'me'])    ->name('api.me');
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');

    // Clientes 
    Route::get   ('/clientes',      [ApiClienteController::class, 'index'])  ->name('api.clientes.index');
    Route::post  ('/clientes',      [ApiClienteController::class, 'store'])  ->name('api.clientes.store');
    Route::get   ('/clientes/{id}', [ApiClienteController::class, 'show'])   ->name('api.clientes.show');
    Route::put   ('/clientes/{id}', [ApiClienteController::class, 'update']) ->name('api.clientes.update');
    Route::delete('/clientes/{id}', [ApiClienteController::class, 'destroy'])->name('api.clientes.destroy');
    Route::get('/ciudades', [ApiCiudadController::class, 'index'])->name('api.ciudades');
});