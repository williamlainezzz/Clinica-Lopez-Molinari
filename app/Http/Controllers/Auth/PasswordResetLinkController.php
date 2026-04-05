<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        App::setLocale(config('app.locale', 'es'));

        $translatedStatus = Lang::get('passwords.sent');
        $generic = back()->with('status', $translatedStatus);

        $user = Usuario::query()
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'tbl_usuario.FK_COD_PERSONA')
            ->join('tbl_correo as c', 'c.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->where('c.CORREO', $request->email)
            ->select('tbl_usuario.*')
            ->first();

        if (!$user) {
            return $generic;
        }

        try {
            $token = Password::broker()->createToken($user);
            $user->sendPasswordResetNotification($token);
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de recuperacion de contrasena.', [
                'email' => $request->email,
                'user_id' => $user->COD_USUARIO ?? null,
                'message' => $e->getMessage(),
            ]);

            return $generic;
        }

        return $generic;
    }
}
