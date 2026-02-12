<?php

namespace App\Http\Middleware;

use App\Support\PasswordSecurityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsNotExpired
{
    public function __construct(private PasswordSecurityService $passwordSecurityService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($this->passwordSecurityService->shouldEnforceExpiry($user)) {
            return redirect()
                ->route('usuario.password.edit')
                ->with('warning', 'Por seguridad, tu contraseña ha caducado y debes actualizarla para continuar.');
        }

        return $next($request);
    }
}
