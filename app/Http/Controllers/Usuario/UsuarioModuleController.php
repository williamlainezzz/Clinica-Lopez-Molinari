<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\UsuarioPregunta;
use App\Notifications\PasswordChangedNotification;
use App\Support\PasswordSecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsuarioModuleController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        $correo = $user?->getEmailForPasswordReset();

        return view('usuario.perfil', compact('user', 'correo'));
    }

    public function editPassword()
    {
        return view('usuario.cambiar-password');
    }

    public function updatePassword(Request $request, PasswordSecurityService $passwordSecurityService): RedirectResponse
    {
        $validated = $request->validateWithBag('usuarioPassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $plainPassword = $validated['password'];

        $user->PWD_USUARIO = Hash::make($plainPassword);
        $user->save();

        $passwordSecurityService->markPasswordChanged((int) $user->COD_USUARIO);
        $user->notify(new PasswordChangedNotification($user->USR_USUARIO, $plainPassword));

        return back()->with('status', 'password-updated');
    }

    public function securityQuestions(Request $request)
    {
        $user = $request->user();

        $preguntas = UsuarioPregunta::with('pregunta')
            ->where('FK_COD_USUARIO', $user->COD_USUARIO)
            ->get();

        return view('usuario.preguntas', compact('preguntas'));
    }
}
