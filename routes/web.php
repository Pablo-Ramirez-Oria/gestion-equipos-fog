<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UsuarioController;

Route::redirect('/', '/dashboard')
    ->middleware(['auth', 'verified'])
    ->name('home');

/* Rutas principales pasando por el middleware */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Las rutas tienen un middleware establecido dentro del controller
    Route::get('/inventario/exportar', [InventarioController::class, 'exportarCsv'])->name('inventario.exportar');
    Route::resource('inventario', InventarioController::class);
    Route::get('/prestamos/exportar', [PrestamoController::class, 'exportarCsv'])->name('prestamos.exportar');
    Route::resource('prestamos', PrestamoController::class);
    Route::resource('ubicaciones', UbicacionController::class)->parameters([
    'ubicaciones' => 'ubicacion'
    ]);
    Route::resource('estados', EstadoController::class);
    Route::resource('personas', PersonaController::class);
    // Route::resource('usuarios', UsuarioController::class);
});

/* Middleware de autenticación */
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
