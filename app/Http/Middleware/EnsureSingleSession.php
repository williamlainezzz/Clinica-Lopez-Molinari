<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $cacheKey = self::cacheKey($user->getAuthIdentifier());
            $activeSessionId = Cache::get($cacheKey);
            $currentSessionId = $request->session()->getId();

            if ($activeSessionId && !hash_equals((string) $activeSessionId, (string) $currentSessionId)) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Tu sesion se cerro porque tu cuenta inicio sesion en otro dispositivo.',
                ]);
            }
        }

        $response = $next($request);

        if (Auth::check()) {
            Cache::put(
                self::cacheKey(Auth::id()),
                $request->session()->getId(),
                now()->addMinutes(config('session.lifetime'))
            );
        }

        return $response;
    }

    public static function cacheKey(int|string $userId): string
    {
        return "active_session:{$userId}";
    }
}
