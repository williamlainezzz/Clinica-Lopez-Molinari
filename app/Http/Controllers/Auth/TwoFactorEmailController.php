<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Notifications\LoginEmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TwoFactorEmailController extends Controller
{
    /**
     * Muestra el formulario para ingresar el código OTP.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Debe existir un 2FA en curso
        $userId = session('2fa:user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors([
                'email' => 'La sesión de verificación caducó. Inicia sesión de nuevo.',
            ]);
        }

        return view('auth.two-factor-challenge'); // vista del Paso 3.3
    }

    /**
     * Verifica el código OTP y completa el login.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required','digits:6'],
        ], [
            'code.required' => 'Ingresa el código que enviamos a tu correo.',
            'code.digits'   => 'El código debe tener 6 dígitos.',
        ]);

        $userId   = session('2fa:user_id');
        $remember = (bool) session('2fa:remember', false);

        if (!$userId) {
            return redirect()->route('login')->withErrors([
                'email' => 'La sesión de verificación caducó. Inicia sesión de nuevo.',
            ]);
        }

        $cacheKey = "login_otp:{$userId}";
        $data = Cache::get($cacheKey);

        if (!$data) {
            return back()->withErrors(['code' => 'El código expiró. Solicita uno nuevo.']);
        }

        // Intentos y expiración
        $attempts   = (int) ($data['attempts'] ?? 0);
        $expiresAt  = (int) ($data['expires_at'] ?? 0);
        $remaining  = max(0, $expiresAt - now()->timestamp);

        if ($remaining === 0) {
            Cache::forget($cacheKey);
            return back()->withErrors(['code' => 'El código expiró. Solicita uno nuevo.']);
        }

        // Comparar el hash del código
        if (! Hash::check($request->code, $data['code_hash'] ?? '')) {
            $attempts++;
            // Guardar intento fallido conservando el mismo vencimiento
            Cache::put($cacheKey, [
                'code_hash'  => $data['code_hash'],
                'expires_at' => $expiresAt,
                'attempts'   => $attempts,
            ], now()->addSeconds($remaining));

            // Bloqueo si excede intentos (ej. 5)
            if ($attempts >= 5) {
                Cache::forget($cacheKey);
                return redirect()->route('login')->withErrors([
                    'email' => 'Demasiados intentos. Inicia sesión nuevamente.',
                ]);
            }

            return back()->withErrors(['code' => 'Código incorrecto. Inténtalo de nuevo.']);
        }

        // Código correcto → completar login
        Cache::forget($cacheKey);
        $request->session()->forget(['2fa:user_id', '2fa:remember']);

        // Autenticar definitivamente
        Auth::loginUsingId($userId, $remember);

        // Seguridad: regenerar la sesión
        $request->session()->regenerate();

        // Redirigir al intended o dashboard
        return redirect()->intended(route('dashboard', absolute: false))
            ->with('status', 'Inicio de sesión verificado.');
    }

    /**
     * Reenvía un nuevo código al correo del usuario.
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = session('2fa:user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors([
                'email' => 'La sesión de verificación caducó. Inicia sesión de nuevo.',
            ]);
        }

        $user = Usuario::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors([
                'email' => 'No se encontró el usuario. Inicia sesión de nuevo.',
            ]);
        }

        // Generar y guardar nuevo código
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $ttl  = 5; // minutos
        $expiresAt = now()->addMinutes($ttl)->timestamp;

        Cache::put("login_otp:{$userId}", [
            'code_hash'  => Hash::make($code),
            'expires_at' => $expiresAt,
            'attempts'   => 0,
        ], now()->addMinutes($ttl));

        // Enviar
        $user->notify(new LoginEmailOtp($code, $ttl));

        return back()->with('status', 'Te enviamos un nuevo código a tu correo.');
    }
}

