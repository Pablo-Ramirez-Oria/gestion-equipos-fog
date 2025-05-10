<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\UsuarioController;

Route::redirect('/', '/dashboard')
    ->middleware(['auth', 'verified'])
    ->name('home');

/* Rutas principales pasando por el middleware */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Rutas accesibles para todos los usuarios autenticados (lectura de inventario y préstamos)
    Route::resource('inventario', InventarioController::class)->only(['index', 'show']);
    Route::resource('prestamos', PrestamoController::class)->only(['index', 'show']);

    // Rutas restringidas a administradores
    Route::middleware('role:admin')->group(function () {
        Route::resource('inventario', InventarioController::class)->except(['index', 'show']);
        Route::resource('prestamos', PrestamoController::class)->except(['index', 'show']);
        Route::resource('ubicaciones', UbicacionController::class);
        Route::resource('estados', EstadoController::class);
        Route::resource('dispositivos', DispositivoController::class);
        Route::resource('usuarios', UsuarioController::class);
    });
});

/* Middleware de autenticación */
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
