<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules (un solo campo: login = usuario o correo).
     */
    public function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login    = Str::of($this->string('login'))->trim()->toString();
        $remember = $this->boolean('remember');

        // Armamos el arreglo de credenciales que espera Auth::attempt()
        // (tu proveedor usa la columna USR_USUARIO como "username").
        $credentials = [
            'password'    => $this->input('password'),
            'USR_USUARIO' => null, // lo resolvemos abajo
        ];

        // Si escribieron un correo, buscamos el USR_USUARIO asociado
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $row = DB::table('tbl_correo')
                ->join('tbl_persona', 'tbl_persona.COD_PERSONA', '=', 'tbl_correo.FK_COD_PERSONA')
                ->join('tbl_usuario', 'tbl_usuario.FK_COD_PERSONA', '=', 'tbl_persona.COD_PERSONA')
                ->where('tbl_correo.CORREO', $login)
                ->select('tbl_usuario.USR_USUARIO')
                ->first();

            if (!$row) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'login' => trans('auth.failed'),
                ]);
            }

            $credentials['USR_USUARIO'] = $row->USR_USUARIO;
        } else {
            // Es un nombre de usuario directamente
            $credentials['USR_USUARIO'] = $login;
        }

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('login')).'|'.$this->ip();
    }
}
