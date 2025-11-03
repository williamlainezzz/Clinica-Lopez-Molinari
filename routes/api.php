<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitasApiController;

Route::prefix('agenda')->group(function () {
    Route::get('/citas',    [CitasApiController::class, 'index']);
    Route::get('/doctores', [CitasApiController::class, 'doctores']);
    Route::get('/estados',  [CitasApiController::class, 'estados']);
});
