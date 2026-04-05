<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\UsuarioPregunta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        $email = $request->email;

        $user = null;
        if ($email) {
            $user = Usuario::select('tbl_usuario.*')
                ->join('tbl_persona', 'tbl_persona.COD_PERSONA', '=', 'tbl_usuario.FK_COD_PERSONA')
                ->join('tbl_correo', 'tbl_correo.FK_COD_PERSONA', '=', 'tbl_persona.COD_PERSONA')
                ->where('tbl_correo.CORREO', $email)
                ->first();
        }

        $secQuestion = null;
        $secQuestionId = null;

        if ($user) {
            $uQ = UsuarioPregunta::with('pregunta')
                ->where('FK_COD_USUARIO', $user->COD_USUARIO)
                ->inRandomOrder()
                ->first();

            if ($uQ && $uQ->pregunta) {
                $secQuestion = $uQ->pregunta->TEXTO_PREGUNTA;
                $secQuestionId = $uQ->FK_COD_PREGUNTA;
            }
        }

        return view('auth.reset-password', [
            'request' => $request,
            'secQuestion' => $secQuestion,
            'secQuestionId' => $secQuestionId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                'min:10',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            'security_question_id' => ['nullable', 'integer'],
            'security_answer' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('security_question_id')) {
            $limitKey = 'reset_q_attempts:' . sha1($request->email . '|' . $request->ip());
            $attempts = Cache::get($limitKey, 0);

            if ($attempts >= 5) {
                return back()->withErrors([
                    'security_answer' => 'Demasiados intentos. Inténtelo más tarde o contacte a soporte: ' . config('mail.from.address'),
                ])->withInput();
            }

            $user = Usuario::select('tbl_usuario.*')
                ->join('tbl_persona', 'tbl_persona.COD_PERSONA', '=', 'tbl_usuario.FK_COD_PERSONA')
                ->join('tbl_correo', 'tbl_correo.FK_COD_PERSONA', '=', 'tbl_persona.COD_PERSONA')
                ->where('tbl_correo.CORREO', $request->email)
                ->first();

            if (! $user) {
                Cache::put($limitKey, $attempts + 1, now()->addMinutes(10));

                return back()->withErrors([
                    'security_answer' => 'No fue posible validar su identidad. Revise los datos o contacte a soporte: ' . config('mail.from.address'),
                ])->withInput();
            }

            $uQ = UsuarioPregunta::where('FK_COD_USUARIO', $user->COD_USUARIO)
                ->where('FK_COD_PREGUNTA', (int) $request->security_question_id)
                ->first();

            if (! $uQ) {
                Cache::put($limitKey, $attempts + 1, now()->addMinutes(10));

                return back()->withErrors([
                    'security_answer' => 'No fue posible validar su identidad. Revise los datos o contacte a soporte: ' . config('mail.from.address'),
                ])->withInput();
            }

            $normalized = $this->normalizeAnswer($request->security_answer);

            if (! Hash::check($normalized, $uQ->RESPUESTA_HASH)) {
                Cache::put($limitKey, $attempts + 1, now()->addMinutes(10));

                return back()->withErrors([
                    'security_answer' => 'Respuesta incorrecta. Intente de nuevo o contacte a soporte: ' . config('mail.from.address'),
                ])->withInput();
            }

            Cache::forget($limitKey);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return back()->withErrors(['email' => 'El enlace no es válido o ya fue utilizado.'])->withInput();
        }

        if (! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'El enlace no es válido o ya fue utilizado.'])->withInput();
        }

        $ttl = config('auth.passwords.users.expire', 60);
        if (Carbon::parse($record->created_at)->addMinutes($ttl)->isPast()) {
            return back()->withErrors(['email' => 'El enlace ha caducado. Solicite uno nuevo.'])->withInput();
        }

        $user = Usuario::select('tbl_usuario.*')
            ->join('tbl_persona', 'tbl_persona.COD_PERSONA', '=', 'tbl_usuario.FK_COD_PERSONA')
            ->join('tbl_correo', 'tbl_correo.FK_COD_PERSONA', '=', 'tbl_persona.COD_PERSONA')
            ->where('tbl_correo.CORREO', $request->email)
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No se encontró el usuario asociado a este correo.'])->withInput();
        }

        $user->PWD_USUARIO = Hash::make($request->password);
        $user->timestamps = false;
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/')->with('status', 'Tu contraseña fue restablecida correctamente.');
    }

    protected function normalizeAnswer(string $s): string
    {
        $s = trim($s);
        $s = Str::lower($s);
        $s = Str::ascii($s);
        $s = preg_replace('/\s+/', ' ', $s);

        return $s;
    }
}
