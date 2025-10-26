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

// Api / otros
use Illuminate\Routing\Middleware\ThrottleRequests;
use App\Http\Middleware\Authenticate;               // si tu proyecto lo usa

// Tu middleware de permisos
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
            AuthenticateSession::class,            // si no usas sesiones persistentes, puedes quitarlo
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],

        'api' => [
            ThrottleRequests::class . ':api',
            SubstituteBindings::class,
        ],
    ];

    /**
     * Aliases (Laravel 10+). Mantén también $routeMiddleware por compatibilidad si lo prefieres.
     */
    protected $middlewareAliases = [
        'auth'     => Authenticate::class,               // si tu proyecto lo usa
        'bindings' => SubstituteBindings::class,
        'throttle' => ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Alias propio para permisos
        'permiso'  => CheckObjetoPermission::class,
    ];

    /**
     * Compat con versiones anteriores (opcional, si tu app ya lo tenía).
     */
    protected $routeMiddleware = [
        'auth'     => Authenticate::class,               // si tu proyecto lo usa
        'bindings' => SubstituteBindings::class,
        'throttle' => ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Alias propio para permisos
        'permiso'  => CheckObjetoPermission::class,
    ];
}
