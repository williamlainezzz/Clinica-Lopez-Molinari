<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordChangedNotification;
use App\Support\PasswordSecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request, PasswordSecurityService $passwordSecurityService): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
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
}
