<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController; // --- IGNORE ---
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

// ─── Rutas públicas (sin autenticación) ─────────────────────
Route::get('/',      [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',[AuthController::class, 'login'])->name('login.post');

// ─── Rutas protegidas (requieren sesión de tenant) ───────────
Route::middleware(['auth.tenant'])->group(function () {

    // ─── Dashboard ───────────────────────────────────────────────
    Route::get('/dashboard', function () {
        $totalClientes = \App\Models\Cliente::where('activo', true)->count();
        return view('dashboard', compact('totalClientes'));
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); 
    

    // ─── Módulo Clientes ─────────────────────────────────────────
    Route::get('/clientes',             [ClienteController::class, 'index']) ->name('clientes.index');
    Route::get('/clientes/nuevo',       [ClienteController::class, 'create'])->name('clientes.create'); // Ruta para mostrar el formulario de creación
    Route::get('/clientes/pdf',         [PdfController::class, 'clientes'])->name('clientes.pdf');  // Ruta para controlador PDF
    Route::post('/clientes',            [ClienteController::class, 'store']) ->name('clientes.store');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])  ->name('clientes.edit');
    Route::put('/clientes/{cliente}',        [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}',     [ClienteController::class, 'destroy'])->name('clientes.destroy');

});

    // ═══════════════════════════════════════════════════════
    //  PANEL DE ADMINISTRACIÓN CENTRAL — Solo equipo IMS
    // ═══════════════════════════════════════════════════════
Route::prefix('admin')->name('admin.')->group(function () {
    // Rutas públicas del admin (sin middleware)
    Route::get('/login',  [App\Http\Controllers\Admin\AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login'])->name('login.post');
    // Rutas protegidas (requieren sesión de admin)
    Route::middleware('auth.admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout',   [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('logout');
    });
});