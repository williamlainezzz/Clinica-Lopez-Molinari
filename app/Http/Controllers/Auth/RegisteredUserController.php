<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Muestra la vista de registro (Breeze)
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Registra un nuevo usuario en la BD real:
     * 1) Crea tbl_persona
     * 2) Crea tbl_usuario (FK a persona, rol por defecto, password hasheado)
     * 3) Inserta correo, teléfono (opcional) y dirección (opcional)
     */
    public function store(Request $request)
    {
        // 0) Normaliza el correo del request que usa tu form (CLAVE EN MAYÚSCULAS)
        $request->merge([
            'CORREO' => mb_strtolower(trim((string) $request->input('CORREO'))),
        ]);

        // 1) Validación (UNA sola vez) con UNIQUE en tbl_correo.CORREO
        $validated = $request->validateWithBag('register', [
            // Persona (tbl_persona)
            'PRIMER_NOMBRE'    => ['required', 'string', 'max:100'],
            'SEGUNDO_NOMBRE'   => ['nullable', 'string', 'max:100'],
            'PRIMER_APELLIDO'  => ['required', 'string', 'max:100'],
            'SEGUNDO_APELLIDO' => ['nullable', 'string', 'max:100'],
            'TIPO_GENERO'      => ['required', 'integer'],

            // Credenciales
            'password'         => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],

            // Correo (tbl_correo)
            'CORREO'      => ['required', 'email', 'max:100', \Illuminate\Validation\Rule::unique('tbl_correo', 'CORREO')],
            'TIPO_CORREO' => ['nullable', 'string', 'max:30'],

            // Teléfono (tbl_telefono)
            'NUM_TELEFONO'  => ['nullable', 'string', 'max:20'],
            'TIPO_TELEFONO' => ['nullable', 'string', 'max:30'],

            // Dirección (tbl_direccion)
            'DEPARTAMENTO' => ['nullable', 'string', 'max:30'],
            'MUNICIPIO'    => ['nullable', 'string', 'max:30'],
            'CIUDAD'       => ['nullable', 'string', 'max:30'],
            'COLONIA'      => ['nullable', 'string', 'max:30'],
            'REFERENCIA'   => ['nullable', 'string', 'max:255'],

            // Preguntas de seguridad
            'PREGUNTA_1'   => ['required', 'integer', 'exists:tbl_pregunta_seguridad,COD_PREGUNTA'],
            'RESPUESTA_1'  => ['required', 'string', 'max:255'],
            'PREGUNTA_2'   => ['required', 'integer', 'different:PREGUNTA_1', 'exists:tbl_pregunta_seguridad,COD_PREGUNTA'],
            'RESPUESTA_2'  => ['required', 'string', 'max:255'],
        ]);

        $usuario = null;
        $doctorPersonaId = $this->resolveDoctorPersonaId($request);

        try {
            DB::transaction(function () use ($validated, &$usuario, $doctorPersonaId) {
    // 2) Crear PERSONA
    $persona = Persona::create([
        'PRIMER_NOMBRE'    => $validated['PRIMER_NOMBRE'],
        'SEGUNDO_NOMBRE'   => $validated['SEGUNDO_NOMBRE'] ?? null,
        'PRIMER_APELLIDO'  => $validated['PRIMER_APELLIDO'],
        'SEGUNDO_APELLIDO' => $validated['SEGUNDO_APELLIDO'] ?? null,
        'TIPO_GENERO'      => $validated['TIPO_GENERO'],
    ]);

    // 3) Generar username único
    $usr = $this->makeUsername($validated['PRIMER_NOMBRE'], $validated['PRIMER_APELLIDO']);

    // 4) Buscar el rol PACIENTE en la tabla tbl_rol
    $rolPacienteId = DB::table('tbl_rol')
        ->where('NOM_ROL', 'PACIENTE')
        ->value('COD_ROL');

    // Si por alguna razón no existe, caemos a 3 (para no romper)
    if (!$rolPacienteId) {
        $rolPacienteId = 3;
    }

    // 5) Crear USUARIO con rol PACIENTE por defecto
    $usuario = Usuario::create([
        'USR_USUARIO'    => $usr,
        'PWD_USUARIO'    => Hash::make($validated['password']),
        'FK_COD_PERSONA' => $persona->COD_PERSONA,
        'FK_COD_ROL'     => $rolPacienteId,
        'ESTADO_USUARIO' => 1,
    ]);

    // 6) Insertar CORREO (ya normalizado por el merge inicial)
    DB::table('tbl_correo')->insert([
        'FK_COD_PERSONA' => $persona->COD_PERSONA,
        'CORREO'         => $validated['CORREO'],
        'TIPO_CORREO'    => $validated['TIPO_CORREO'] ?? 'PERSONAL',
    ]);

    // (a partir de aquí dejas TODO igual: teléfono, dirección, preguntas…)

                // 7) TELÉFONO (si viene)
                if (!empty($validated['NUM_TELEFONO'])) {
                    DB::table('tbl_telefono')->insert([
                        'FK_COD_PERSONA' => $persona->COD_PERSONA,
                        'NUM_TELEFONO'   => preg_replace('/\D+/', '', $validated['NUM_TELEFONO']),
                        'TIPO_TELEFONO'  => $validated['TIPO_TELEFONO'] ?? 'MÓVIL',
                    ]);
                }

                // 8) DIRECCIÓN (si viene algo)
                if (
                    !empty($validated['DEPARTAMENTO']) ||
                    !empty($validated['MUNICIPIO']) ||
                    !empty($validated['CIUDAD']) ||
                    !empty($validated['COLONIA']) ||
                    !empty($validated['REFERENCIA'])
                ) {
                    DB::table('tbl_direccion')->insert([
                        'FK_COD_PERSONA' => $persona->COD_PERSONA,
                        'DEPARTAMENTO'   => $validated['DEPARTAMENTO'] ?? '',
                        'MUNICIPIO'      => $validated['MUNICIPIO'] ?? '',
                        'CIUDAD'         => $validated['CIUDAD'] ?? null,
                        'COLONIA'        => $validated['COLONIA'] ?? null,
                        'REFERENCIA'     => $validated['REFERENCIA'] ?? null,
                    ]);
                }

                // 9) PREGUNTAS DE SEGURIDAD (respuesta normalizada + hash)
                $ans1 = $this->normalizeAnswer($validated['RESPUESTA_1']);
                $ans2 = $this->normalizeAnswer($validated['RESPUESTA_2']);

                DB::table('tbl_usuario_pregunta')->insert([
                    [
                        'FK_COD_USUARIO'  => $usuario->COD_USUARIO,
                        'FK_COD_PREGUNTA' => (int) $validated['PREGUNTA_1'],
                        'RESPUESTA_HASH'  => Hash::make($ans1),
                    ],
                    [
                        'FK_COD_USUARIO'  => $usuario->COD_USUARIO,
                        'FK_COD_PREGUNTA' => (int) $validated['PREGUNTA_2'],
                        'RESPUESTA_HASH'  => Hash::make($ans2),
                    ],
                ]);

                if ($doctorPersonaId && DB::getSchemaBuilder()->hasTable('tbl_doctor_paciente')) {
                    DB::table('tbl_doctor_paciente')->updateOrInsert(
                        [
                            'FK_COD_DOCTOR'   => $doctorPersonaId,
                            'FK_COD_PACIENTE' => $persona->COD_PERSONA,
                        ],
                        [
                            'ACTIVO'         => 1,
                            'FEC_ASIGNACION' => now(),
                        ]
                    );
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Violación de UNIQUE (correo ya existe)
            if ($e->getCode() === '23000') {
                return back()
                    ->withErrors(['CORREO' => 'Este correo ya está registrado.'])
                    ->withInput();
            }
            throw $e;
        }

        event(new Registered($usuario));
        Auth::login($usuario);

        session()->flash('username_generado', $usuario->USR_USUARIO);

        return redirect()->intended(route('dashboard', absolute: false));
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
     * Genera username único: 1ra letra del nombre + apellido, minúsculas, sin acentos.
     * Si existe, agrega sufijo incremental (ej: wsolis, wsolis1, wsolis2...).
     */
    protected function makeUsername(string $nombre, string $apellido, int $maxLen = 50): string
    {
        $base = Str::ascii(Str::lower(
            substr(trim($nombre), 0, 1) . preg_replace('/\s+/', '', trim($apellido))
        ));
        $base = preg_replace('/[^a-z0-9]/', '', $base) ?: 'user';
        $base = substr($base, 0, $maxLen);

        $user = $base;
        $i    = 1;

        while (DB::table('tbl_usuario')->where('USR_USUARIO', $user)->exists()) {
            $s    = (string) $i++;
            $user = substr($base, 0, $maxLen - strlen($s)) . $s;
        }
        return $user;
    }


    /**
     * Normaliza una respuesta: trim, minúsculas, sin tildes/acentos.
     * Así el usuario puede escribir "Bogotá" o "bogota" y validará igual.
     */
    protected function normalizeAnswer(string $s): string
    {
        $s = trim($s);
        $s = \Illuminate\Support\Str::lower($s);
        $s = \Illuminate\Support\Str::ascii($s); // quita tildes: á->a, ñ->n
        // opcional: remover espacios internos múltiples
        $s = preg_replace('/\s+/', ' ', $s);
        return $s;
    }
}
