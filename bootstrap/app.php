<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

use App\Http\Middleware\CheckObjetoPermission;
use App\Http\Middleware\EnsurePasswordIsNotExpired;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        
        commands: __DIR__.'/../routes/console.php',
        // Si usas broadcasting:
        // channels: __DIR__.'/../routes/channels.php',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('citas:enviar-recordatorios')->everyFiveMinutes();
        $schedule->command('citas:generar-alertas-recepcion')->everyFiveMinutes();
        $schedule->command('citas:procesar-estados')->everyFiveMinutes();
        $schedule->command('citas:enviar-resumen-manana')->dailyAt('19:00');
        $schedule->command('seguridad:recordar-caducidad-password')->dailyAt('08:00');
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permiso' => CheckObjetoPermission::class,
            'password.expiry' => EnsurePasswordIsNotExpired::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
