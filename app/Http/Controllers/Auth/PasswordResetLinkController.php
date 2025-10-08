<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Notifications\ResetPassword;
// Si ya creaste tu notificación propia con saludo y marca en ES:
// use App\Notifications\ResetPasswordEs;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        // Asegura validación básica
        $request->validate(['email' => ['required','email']]);

        // Fuerza locale a ES por si el runtime quedó en EN
        App::setLocale(config('app.locale', 'es'));

        // Buscar usuario por tu esquema (usuario -> persona -> correo)
        $user = Usuario::query()
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'tbl_usuario.FK_COD_PERSONA')
            ->join('tbl_correo as c', 'c.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->where('c.CORREO', $request->email)
            ->select('tbl_usuario.*')
            ->first();

        // Mensaje genérico SIEMPRE en español
        $translatedStatus = Lang::get('passwords.sent'); // => "¡Te hemos enviado por correo el enlace..."
        $generic = back()->with('status', $translatedStatus);

        if (!$user) {
            // No reveles si existe o no
            return $generic;
        }

        // Crear token y enviar notificación
        $token = Password::broker()->createToken($user);

        // Usa la notificación por defecto o tu notificación en español:
        // $user->notify(new ResetPasswordEs($token)); // ← si creaste la custom
        $user->notify(new ResetPassword($token));       // ← default

        return $generic;
    }
}
