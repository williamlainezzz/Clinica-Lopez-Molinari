<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    public function create(string $token)
    {
        // Vista de Breeze/AdminLTE para "Restablecer contraseña"
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
    'token'    => ['required'],
    'email'    => ['required','email'],
    'password' => ['required','confirmed', Rules\Password::defaults()],
]);

        // 1) Reubicar el usuario por el correo (mismo join que arriba)
        $user = Usuario::query()
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'tbl_usuario.FK_COD_PERSONA')
            ->join('tbl_correo as c', 'c.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->where('c.CORREO', $request->email)
            ->select('tbl_usuario.*')
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => __('passwords.user')]);
        }

        // 2) Validar token contra el broker
        if (! Password::broker()->tokenExists($user, $request->token)) {
            return back()->withErrors(['email' => __('passwords.token')]);
        }

        // 3) Guardar nueva contraseña en tu campo PWD_USUARIO
        $user->PWD_USUARIO = Hash::make($request->password);
        $user->save();

        // 4) Consumir el token
        Password::broker()->deleteToken($user);

        // 5) Redirigir a login
        return redirect()->route('login')->with('status', __('passwords.reset'));
    }
}
