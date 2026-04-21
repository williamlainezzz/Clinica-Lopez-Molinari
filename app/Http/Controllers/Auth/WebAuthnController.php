<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureSingleSession;
use App\Models\Usuario;
use App\Models\WebauthnCredential;
use App\Support\WebAuthn\Base64Url;
use App\Support\WebAuthn\WebAuthnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebAuthnController extends Controller
{
    public function registerOptions(Request $request, WebAuthnService $webauthn): JsonResponse
    {
        $user = $request->user();
        $challenge = Base64Url::encode(random_bytes(32));

        $request->session()->put('webauthn.register.challenge', $challenge);

        $credentials = WebauthnCredential::where('FK_COD_USUARIO', $user->COD_USUARIO)->get();

        return response()->json([
            'publicKey' => [
                'challenge' => $challenge,
                'rp' => [
                    'name' => config('app.name', 'Clinica Lopez Molinari'),
                    'id' => $webauthn->rpId($request),
                ],
                'user' => [
                    'id' => Base64Url::encode((string) $user->COD_USUARIO),
                    'name' => $user->USR_USUARIO,
                    'displayName' => $user->name,
                ],
                'pubKeyCredParams' => [
                    ['type' => 'public-key', 'alg' => -7],
                    ['type' => 'public-key', 'alg' => -257],
                ],
                'timeout' => 60000,
                'attestation' => 'none',
                'authenticatorSelection' => [
                    'residentKey' => 'preferred',
                    'userVerification' => 'required',
                ],
                'excludeCredentials' => $credentials->map(fn ($credential) => [
                    'type' => 'public-key',
                    'id' => $credential->CREDENTIAL_ID,
                    'transports' => $credential->TRANSPORTS ?: ['internal'],
                ])->values(),
            ],
        ]);
    }

    public function register(Request $request, WebAuthnService $webauthn): JsonResponse
    {
        $payload = $request->validate([
            'id' => ['required', 'string'],
            'rawId' => ['required', 'string'],
            'type' => ['required', 'in:public-key'],
            'response.clientDataJSON' => ['required', 'string'],
            'response.attestationObject' => ['required', 'string'],
            'response.transports' => ['nullable', 'array'],
        ]);

        $challenge = $request->session()->pull('webauthn.register.challenge');

        if (!$challenge) {
            return response()->json(['message' => 'El reto biometrico expiro. Intenta de nuevo.'], 422);
        }

        try {
            $verified = $webauthn->verifyRegistration($request, $payload, $challenge);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $user = $request->user();

        WebauthnCredential::updateOrCreate(
            ['CREDENTIAL_ID' => $verified['credential_id']],
            [
                'FK_COD_USUARIO' => $user->COD_USUARIO,
                'PUBLIC_KEY_COSE' => $verified['public_key_cose'],
                'SIGN_COUNT' => $verified['sign_count'],
                'NOMBRE' => $request->userAgent() ? Str::limit($request->userAgent(), 255, '') : 'Dispositivo biometrico',
                'TRANSPORTS' => $payload['response']['transports'] ?? ['internal'],
            ]
        );

        return response()->json([
            'message' => 'Inicio con biometria activado en este dispositivo.',
            'count' => WebauthnCredential::where('FK_COD_USUARIO', $user->COD_USUARIO)->count(),
        ]);
    }

    public function authenticationOptions(Request $request, WebAuthnService $webauthn): JsonResponse
    {
        $request->validate([
            'login' => ['nullable', 'string'],
        ]);

        $challenge = Base64Url::encode(random_bytes(32));
        $login = $request->string('login')->trim()->toString();
        $options = [
            'challenge' => $challenge,
            'rpId' => $webauthn->rpId($request),
            'timeout' => 60000,
            'userVerification' => 'required',
        ];

        if ($login !== '') {
            $user = $this->resolveUser($login);

            if (!$user) {
                return response()->json(['message' => 'No encontramos una cuenta con ese usuario o correo.'], 404);
            }

            $credentials = WebauthnCredential::where('FK_COD_USUARIO', $user->COD_USUARIO)->get();

            if ($credentials->isEmpty()) {
                return response()->json(['message' => 'Esta cuenta aun no tiene biometria activada. Inicia sesion con usuario y contrasena, luego activala desde Usuario / Perfil.'], 404);
            }

            $userId = $user->COD_USUARIO;
            $options['allowCredentials'] = $credentials->map(fn ($credential) => [
                'type' => 'public-key',
                'id' => $credential->CREDENTIAL_ID,
                'transports' => $credential->TRANSPORTS ?: ['internal'],
            ])->values();
        } else {
            $userId = null;
        }

        $request->session()->put('webauthn.login', [
            'challenge' => $challenge,
            'user_id' => $userId,
        ]);

        return response()->json([
            'publicKey' => $options,
        ]);
    }

    public function authenticate(Request $request, WebAuthnService $webauthn): JsonResponse
    {
        $payload = $request->validate([
            'id' => ['required', 'string'],
            'rawId' => ['required', 'string'],
            'type' => ['required', 'in:public-key'],
            'response.clientDataJSON' => ['required', 'string'],
            'response.authenticatorData' => ['required', 'string'],
            'response.signature' => ['required', 'string'],
        ]);

        $loginState = $request->session()->pull('webauthn.login');

        if (!$loginState) {
            return response()->json(['message' => 'El reto biometrico expiro. Intenta de nuevo.'], 422);
        }

        $credentialId = $payload['rawId'];
        $credentialQuery = WebauthnCredential::where('CREDENTIAL_ID', $credentialId);

        if (!empty($loginState['user_id'])) {
            $credentialQuery->where('FK_COD_USUARIO', $loginState['user_id']);
        }

        $credential = $credentialQuery->first();

        if (!$credential) {
            return response()->json(['message' => 'No encontramos biometria registrada en este dispositivo. Inicia sesion con usuario y contrasena, luego activala desde Usuario / Perfil.'], 404);
        }

        try {
            $newSignCount = $webauthn->verifyAuthentication($request, $credential, $payload, $loginState['challenge']);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $credential->SIGN_COUNT = $newSignCount;
        $credential->save();

        $user = Usuario::find($credential->FK_COD_USUARIO);

        if (!$user) {
            return response()->json(['message' => 'La cuenta ya no esta disponible.'], 404);
        }

        Auth::login($user);
        $request->session()->regenerate();
        EnsureSingleSession::storeSessionMeta($user->getAuthIdentifier(), $request->session()->getId());

        return response()->json([
            'message' => 'Inicio de sesion biometrico verificado.',
            'redirect' => route('dashboard', absolute: false),
        ]);
    }

    public function destroyCredential(Request $request, WebauthnCredential $credential)
    {
        abort_unless((int) $credential->FK_COD_USUARIO === (int) $request->user()->COD_USUARIO, 403);

        $credential->delete();

        return back()->with('status', 'passkey-deleted');
    }

    private function resolveUser(string $login): ?Usuario
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $username = DB::table('tbl_correo')
                ->join('tbl_persona', 'tbl_persona.COD_PERSONA', '=', 'tbl_correo.FK_COD_PERSONA')
                ->join('tbl_usuario', 'tbl_usuario.FK_COD_PERSONA', '=', 'tbl_persona.COD_PERSONA')
                ->where('tbl_correo.CORREO', $login)
                ->value('tbl_usuario.USR_USUARIO');

            if (!$username) {
                return null;
            }

            $login = $username;
        }

        return Usuario::where('USR_USUARIO', $login)->first();
    }
}
