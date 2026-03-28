<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    public const ONLINE_WINDOW_MINUTES = 1;
    public const AWAY_WINDOW_MINUTES = 5;

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $cacheKey = self::cacheKey($user->getAuthIdentifier());
            $sessionMeta = self::normalizeSessionMeta(Cache::get($cacheKey));
            $activeSessionId = $sessionMeta['session_id'];
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
            self::storeSessionMeta(Auth::id(), $request->session()->getId());
        }

        return $response;
    }

    public static function cacheKey(int|string $userId): string
    {
        return "active_session:{$userId}";
    }

    public static function storeSessionMeta(int|string $userId, string $sessionId): void
    {
        Cache::put(
            self::cacheKey($userId),
            [
                'session_id' => $sessionId,
                'last_seen_at' => now()->toDateTimeString(),
            ],
            now()->addMinutes(config('session.lifetime'))
        );
    }

    public static function normalizeSessionMeta(mixed $value): array
    {
        if (is_array($value)) {
            return [
                'session_id' => $value['session_id'] ?? null,
                'last_seen_at' => $value['last_seen_at'] ?? null,
            ];
        }

        if (is_string($value) && $value !== '') {
            return [
                'session_id' => $value,
                'last_seen_at' => null,
            ];
        }

        return [
            'session_id' => null,
            'last_seen_at' => null,
        ];
    }
}
