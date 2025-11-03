<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitasApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Estas rutas se cargan con el middleware "api" y automáticamente están
| prefixadas con /api (por el RouteServiceProvider). Aquí agregamos /agenda.
*/

Route::prefix('agenda')->group(function () {
    Route::get('citas',        [CitasApiController::class, 'index']);
    Route::get('citas/{id}',   [CitasApiController::class, 'show'])->whereNumber('id');
    Route::get('doctores',     [CitasApiController::class, 'doctores']);
    Route::get('estados',      [CitasApiController::class, 'estados']);
});
