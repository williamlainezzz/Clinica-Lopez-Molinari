<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Notifications\LoginEmailOtp;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    // 1) Autentica credenciales (Breeze)
    $request->authenticate();

    // 2) Usuario autenticado provisionalmente
    $user   = Auth::user();
    $userId = $user->getAuthIdentifier();
    $remember = $request->boolean('remember');

    // 3) Generar OTP (6 dígitos) + TTL 5 min
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $ttl  = 5; // minutos

    // 4) Guardar OTP en cache (hash + expiración + intentos)
    Cache::put("login_otp:{$userId}", [
        'code_hash'  => Hash::make($code),
        'expires_at' => now()->addMinutes($ttl)->timestamp,
        'attempts'   => 0,
    ], now()->addMinutes($ttl));

    // 5) Enviar correo con el OTP
    $user->notify(new LoginEmailOtp($code, $ttl));

    // 6) Marcar sesión como "pendiente de 2FA" (sin completar login todavía)
    session([
        '2fa:user_id'  => $userId,
        '2fa:remember' => $remember,
        // opcional: '2fa:intended' => url()->previous(),
    ]);

    // 7) Cerrar sesión provisional (aún no hay acceso hasta pasar el OTP)
    Auth::logout();
    // No invalido toda la sesión para no perder las claves 2fa:*
    $request->session()->regenerateToken();

    // 8) Redirigir a la pantalla de reto (la crearemos en el PASO 3)
    return redirect()
        ->route('two-factor.challenge')
        ->with('status', 'Te enviamos un código a tu correo. Ingrésalo para continuar.');
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
