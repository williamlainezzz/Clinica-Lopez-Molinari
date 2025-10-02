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
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
        // Validación (ya NO pedimos USR_USUARIO: se genera automáticamente)
        $validated = $request->validate([
            // Persona (tbl_persona)
            'PRIMER_NOMBRE'    => ['required', 'string', 'max:100'],
            'SEGUNDO_NOMBRE'   => ['nullable', 'string', 'max:100'],
            'PRIMER_APELLIDO'  => ['required', 'string', 'max:100'],
            'SEGUNDO_APELLIDO' => ['nullable', 'string', 'max:100'],
            'TIPO_GENERO'      => ['required', 'integer'],

            // Credenciales
            'password'               => ['required', 'string', 'confirmed', 'min:8'],

            // Correo (tbl_correo)
            'CORREO'      => ['required', 'email', 'max:100'],
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
        ]);

        $usuario = null;

        DB::transaction(function () use ($validated, &$usuario) {
            // 1) Crear PERSONA
            $persona = Persona::create([
                'PRIMER_NOMBRE'    => $validated['PRIMER_NOMBRE'],
                'SEGUNDO_NOMBRE'   => $validated['SEGUNDO_NOMBRE'] ?? null,
                'PRIMER_APELLIDO'  => $validated['PRIMER_APELLIDO'],
                'SEGUNDO_APELLIDO' => $validated['SEGUNDO_APELLIDO'] ?? null,
                'TIPO_GENERO'      => $validated['TIPO_GENERO'],
            ]);

            // 2) Generar username único (1ra letra del nombre + apellido, sin acentos, minúsculas)
            $usr = $this->makeUsername($validated['PRIMER_NOMBRE'], $validated['PRIMER_APELLIDO']);

            // 3) Crear USUARIO vinculado a la persona
            $usuario = Usuario::create([
                'USR_USUARIO'    => $usr,
                'PWD_USUARIO'    => Hash::make($validated['password']),
                'FK_COD_PERSONA' => $persona->COD_PERSONA,
                'FK_COD_ROL'     => 1, // usa el rol existente (ajusta si deseas otro)
                'ESTADO_USUARIO' => 1, // activo
            ]);

            // 4) Insertar CORREO
            DB::table('tbl_correo')->insert([
                'FK_COD_PERSONA' => $persona->COD_PERSONA,
                'CORREO'         => $validated['CORREO'],
                'TIPO_CORREO'    => $validated['TIPO_CORREO'] ?? 'PERSONAL',
            ]);

            // 5) Insertar TELÉFONO (si viene)
            if (!empty($validated['NUM_TELEFONO'])) {
                DB::table('tbl_telefono')->insert([
                    'FK_COD_PERSONA' => $persona->COD_PERSONA,
                    'NUM_TELEFONO'   => preg_replace('/\D+/', '', $validated['NUM_TELEFONO']), // solo dígitos
                    'TIPO_TELEFONO'  => $validated['TIPO_TELEFONO'] ?? 'MÓVIL',
                ]);
            }

            // 6) Insertar DIRECCIÓN (si viene algo)
            if (!empty($validated['DEPARTAMENTO']) || !empty($validated['MUNICIPIO']) ||
                !empty($validated['CIUDAD']) || !empty($validated['COLONIA']) || !empty($validated['REFERENCIA'])) {
                DB::table('tbl_direccion')->insert([
                    'FK_COD_PERSONA' => $persona->COD_PERSONA,
                    'DEPARTAMENTO'   => $validated['DEPARTAMENTO'] ?? '',
                    'MUNICIPIO'      => $validated['MUNICIPIO'] ?? '',
                    'CIUDAD'         => $validated['CIUDAD'] ?? null,
                    'COLONIA'        => $validated['COLONIA'] ?? null,
                    'REFERENCIA'     => $validated['REFERENCIA'] ?? null,
                ]);
            }
        });

        event(new Registered($usuario));
        Auth::login($usuario);

        // Mostrar el username generado en la UI después del registro
        session()->flash('username_generado', $usuario->USR_USUARIO);

        return redirect()->intended(route('dashboard', absolute: false));
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

        $user = $base; $i = 1;
        while (DB::table('tbl_usuario')->where('USR_USUARIO', $user)->exists()) {
            $s = (string) $i++;
            $user = substr($base, 0, $maxLen - strlen($s)) . $s;
        }
        return $user;
    }
}
