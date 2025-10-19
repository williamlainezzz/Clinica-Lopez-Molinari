<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //agrega/une tu alias aquí
        $middleware->alias([
            'permiso' => \App\Http\Middleware\CheckObjetoPermission::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();
