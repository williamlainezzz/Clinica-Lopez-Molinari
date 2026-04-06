<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    private const PATIENT_ROLE_CACHE_KEY = 'auth.register.patient_role_id';
    private const DOCTOR_PATIENT_TABLE_CACHE_KEY = 'auth.register.has_doctor_patient_table';

    /**
     * Muestra la vista de registro (Breeze)
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Registra un nuevo usuario en la BD real.
     */
    public function store(Request $request)
    {
        $request->merge([
            'CORREO' => mb_strtolower(trim((string) $request->input('CORREO'))),
            'PRIMER_NOMBRE' => trim((string) $request->input('PRIMER_NOMBRE')),
            'SEGUNDO_NOMBRE' => trim((string) $request->input('SEGUNDO_NOMBRE')),
            'PRIMER_APELLIDO' => trim((string) $request->input('PRIMER_APELLIDO')),
            'SEGUNDO_APELLIDO' => trim((string) $request->input('SEGUNDO_APELLIDO')),
            'NUM_TELEFONO' => trim((string) $request->input('NUM_TELEFONO')),
            'CIUDAD' => trim((string) $request->input('CIUDAD')),
            'REFERENCIA' => trim((string) $request->input('REFERENCIA')),
            'RESPUESTA_1' => trim((string) $request->input('RESPUESTA_1')),
            'RESPUESTA_2' => trim((string) $request->input('RESPUESTA_2')),
        ]);

        $validated = $request->validateWithBag('register', [
            'PRIMER_NOMBRE' => ['required', 'string', 'max:100'],
            'SEGUNDO_NOMBRE' => ['nullable', 'string', 'max:100'],
            'PRIMER_APELLIDO' => ['required', 'string', 'max:100'],
            'SEGUNDO_APELLIDO' => ['nullable', 'string', 'max:100'],
            'TIPO_GENERO' => ['required', 'integer'],

            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],

            'CORREO' => ['required', 'email', 'max:100', \Illuminate\Validation\Rule::unique('tbl_correo', 'CORREO')],
            'TIPO_CORREO' => ['nullable', 'string', 'max:30'],

            'NUM_TELEFONO' => ['nullable', 'string', 'max:20'],
            'TIPO_TELEFONO' => ['nullable', 'string', 'max:30'],

            'DEPARTAMENTO' => ['nullable', 'string', 'max:30'],
            'MUNICIPIO' => ['nullable', 'string', 'max:30'],
            'CIUDAD' => ['nullable', 'string', 'max:30'],
            'COLONIA' => ['nullable', 'string', 'max:30'],
            'REFERENCIA' => ['nullable', 'string', 'max:255'],

            'PREGUNTA_1' => ['required', 'integer', 'exists:tbl_pregunta_seguridad,COD_PREGUNTA'],
            'RESPUESTA_1' => ['required', 'string', 'max:255'],
            'PREGUNTA_2' => ['required', 'integer', 'different:PREGUNTA_1', 'exists:tbl_pregunta_seguridad,COD_PREGUNTA'],
            'RESPUESTA_2' => ['required', 'string', 'max:255'],
        ]);

        $usuario = null;
        $doctorPersonaId = $this->resolveDoctorPersonaId($request);

        try {
            DB::transaction(function () use ($validated, &$usuario, $doctorPersonaId) {
                $persona = Persona::create([
                    'PRIMER_NOMBRE' => $validated['PRIMER_NOMBRE'],
                    'SEGUNDO_NOMBRE' => $validated['SEGUNDO_NOMBRE'] ?? null,
                    'PRIMER_APELLIDO' => $validated['PRIMER_APELLIDO'],
                    'SEGUNDO_APELLIDO' => $validated['SEGUNDO_APELLIDO'] ?? null,
                    'TIPO_GENERO' => $validated['TIPO_GENERO'],
                ]);

                $usr = $this->makeUsername($validated['PRIMER_NOMBRE'], $validated['PRIMER_APELLIDO']);

                $usuario = Usuario::create([
                    'USR_USUARIO' => $usr,
                    'PWD_USUARIO' => Hash::make($validated['password']),
                    'FK_COD_PERSONA' => $persona->COD_PERSONA,
                    'FK_COD_ROL' => $this->getPatientRoleId(),
                    'ESTADO_USUARIO' => 1,
                ]);

                DB::table('tbl_correo')->insert([
                    'FK_COD_PERSONA' => $persona->COD_PERSONA,
                    'CORREO' => $validated['CORREO'],
                    'TIPO_CORREO' => $validated['TIPO_CORREO'] ?? 'PERSONAL',
                ]);

                if (!empty($validated['NUM_TELEFONO'])) {
                    DB::table('tbl_telefono')->insert([
                        'FK_COD_PERSONA' => $persona->COD_PERSONA,
                        'NUM_TELEFONO' => preg_replace('/\D+/', '', $validated['NUM_TELEFONO']),
                        'TIPO_TELEFONO' => $validated['TIPO_TELEFONO'] ?? 'MOVIL',
                    ]);
                }

                if (
                    !empty($validated['DEPARTAMENTO']) ||
                    !empty($validated['MUNICIPIO']) ||
                    !empty($validated['CIUDAD']) ||
                    !empty($validated['COLONIA']) ||
                    !empty($validated['REFERENCIA'])
                ) {
                    DB::table('tbl_direccion')->insert([
                        'FK_COD_PERSONA' => $persona->COD_PERSONA,
                        'DEPARTAMENTO' => $validated['DEPARTAMENTO'] ?? '',
                        'MUNICIPIO' => $validated['MUNICIPIO'] ?? '',
                        'CIUDAD' => $validated['CIUDAD'] ?? null,
                        'COLONIA' => $validated['COLONIA'] ?? null,
                        'REFERENCIA' => $validated['REFERENCIA'] ?? null,
                    ]);
                }

                $ans1 = $this->normalizeAnswer($validated['RESPUESTA_1']);
                $ans2 = $this->normalizeAnswer($validated['RESPUESTA_2']);

                DB::table('tbl_usuario_pregunta')->insert([
                    [
                        'FK_COD_USUARIO' => $usuario->COD_USUARIO,
                        'FK_COD_PREGUNTA' => (int) $validated['PREGUNTA_1'],
                        'RESPUESTA_HASH' => Hash::make($ans1),
                    ],
                    [
                        'FK_COD_USUARIO' => $usuario->COD_USUARIO,
                        'FK_COD_PREGUNTA' => (int) $validated['PREGUNTA_2'],
                        'RESPUESTA_HASH' => Hash::make($ans2),
                    ],
                ]);

                if ($doctorPersonaId && $this->doctorPatientTableExists()) {
                    DB::table('tbl_doctor_paciente')->updateOrInsert(
                        [
                            'FK_COD_DOCTOR' => $doctorPersonaId,
                            'FK_COD_PACIENTE' => $persona->COD_PERSONA,
                        ],
                        [
                            'ACTIVO' => 1,
                            'FEC_ASIGNACION' => now(),
                        ]
                    );
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()
                    ->withErrors(['CORREO' => 'Este correo ya esta registrado.'])
                    ->withInput()
                    ->with('modal', 'register');
            }

            throw $e;
        }

        event(new Registered($usuario));

        return redirect()
            ->route('welcome')
            ->with('username_generado', $usuario->USR_USUARIO)
            ->with('registro_exitoso', true)
            ->with('modal', 'welcome-register-success');
    }

    private function resolveDoctorPersonaId(Request $request): ?int
    {
        $username = trim((string) ($request->query('doctor') ?? $request->input('doctor') ?? ''));

        if ($username !== '') {
            $doctorPersonaId = DB::table('tbl_usuario as u')
                ->join('tbl_rol as r', 'u.FK_COD_ROL', '=', 'r.COD_ROL')
                ->whereRaw('UPPER(u.USR_USUARIO) = ?', [strtoupper($username)])
                ->whereRaw('UPPER(r.NOM_ROL) LIKE ?', ['%DOCTOR%'])
                ->value('FK_COD_PERSONA');

            if ($doctorPersonaId) {
                return (int) $doctorPersonaId;
            }
        }

        $doctorId = (int) ($request->query('doctor_id') ?? $request->input('doctor_id') ?? 0);

        return $doctorId > 0 ? $doctorId : null;
    }

    /**
     * Genera username unico con una sola consulta.
     */
    protected function makeUsername(string $nombre, string $apellido, int $maxLen = 50): string
    {
        $base = Str::ascii(Str::lower(
            substr(trim($nombre), 0, 1) . preg_replace('/\s+/', '', trim($apellido))
        ));
        $base = preg_replace('/[^a-z0-9]/', '', $base) ?: 'user';
        $base = substr($base, 0, $maxLen);

        $existing = DB::table('tbl_usuario')
            ->where('USR_USUARIO', 'like', $base . '%')
            ->pluck('USR_USUARIO');

        if (!$existing->contains($base)) {
            return $base;
        }

        $nextSuffix = 1;

        foreach ($existing as $candidate) {
            if (!is_string($candidate) || !str_starts_with($candidate, $base)) {
                continue;
            }

            $suffix = substr($candidate, strlen($base));

            if ($suffix !== '' && ctype_digit($suffix)) {
                $nextSuffix = max($nextSuffix, ((int) $suffix) + 1);
            } elseif ($suffix === '') {
                $nextSuffix = max($nextSuffix, 1);
            }
        }

        $suffix = (string) $nextSuffix;

        return substr($base, 0, $maxLen - strlen($suffix)) . $suffix;
    }

    /**
     * Normaliza una respuesta: trim, minusculas y sin tildes.
     */
    protected function normalizeAnswer(string $s): string
    {
        $s = trim($s);
        $s = Str::lower($s);
        $s = Str::ascii($s);
        $s = preg_replace('/\s+/', ' ', $s);

        return $s;
    }

    private function getPatientRoleId(): int
    {
        $roleId = Cache::remember(self::PATIENT_ROLE_CACHE_KEY, now()->addHours(6), function () {
            return DB::table('tbl_rol')
                ->where('NOM_ROL', 'PACIENTE')
                ->value('COD_ROL');
        });

        return $roleId ? (int) $roleId : 3;
    }

    private function doctorPatientTableExists(): bool
    {
        return (bool) Cache::remember(self::DOCTOR_PATIENT_TABLE_CACHE_KEY, now()->addHours(6), function () {
            return Schema::hasTable('tbl_doctor_paciente');
        });
    }
}
