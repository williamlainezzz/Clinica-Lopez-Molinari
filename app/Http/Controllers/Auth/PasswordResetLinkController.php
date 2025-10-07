<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        // Vista de Breeze/AdminLTE para "Olvidé mi contraseña"
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // 1) Buscar el usuario por el correo (join con persona y correo)
        $user = Usuario::query()
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'tbl_usuario.FK_COD_PERSONA')
            ->join('tbl_correo as c', 'c.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->where('c.CORREO', $request->email)
            ->select('tbl_usuario.*')
            ->first();

        // Respuesta genérica (para no revelar si existe o no el correo)
        $generic = back()->with('status', __('passwords.sent'));

        if (!$user) {
            return $generic;
        }

        // 2) Crear token y enviar notificación (usa getEmailForPasswordReset del modelo)
        $token = Password::broker()->createToken($user);
        $user->notify(new ResetPassword($token));

        return $generic;
    }
}
