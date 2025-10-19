<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

// Global middleware
use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Middleware\HandleCors;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Http\Middleware\ValidatePostSize;
use App\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

// Web group
use App\Http\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Session\Middleware\AuthenticateSession;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

// Api group
use Illuminate\Routing\Middleware\ThrottleRequests;

// Auth (si tu proyecto la tiene)
use App\Http\Middleware\Authenticate;

// 🔒 Nuestro middleware de permisos
use App\Http\Middleware\CheckObjetoPermission;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     */
    protected $middleware = [
        TrustProxies::class,
        HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // Si usas sesiones de auth persistentes:
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],

        'api' => [
            ThrottleRequests::class.':api',
            SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware aliases (Laravel 10+).
     * Mantener también $routeMiddleware para compatibilidad.
     */
    protected $middlewareAliases = [
        'auth'      => Authenticate::class,                 // si tu proyecto la tiene
        'bindings'  => SubstituteBindings::class,
        'throttle'  => ThrottleRequests::class,
        'verified'  => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // 👇 Alias que necesitamos
        'permiso'   => CheckObjetoPermission::class,
    ];

    /**
     * Route middleware (compatibilidad con versiones anteriores).
     */
    protected $routeMiddleware = [
        'auth'      => Authenticate::class,                 // si tu proyecto la tiene
        'bindings'  => SubstituteBindings::class,
        'throttle'  => ThrottleRequests::class,
        'verified'  => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // 👇 Alias que necesitamos
        'permiso'   => CheckObjetoPermission::class,
    ];
}
