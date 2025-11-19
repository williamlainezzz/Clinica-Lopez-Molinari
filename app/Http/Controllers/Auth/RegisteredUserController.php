<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\PreguntaSeguridad;
use App\Models\Usuario;
use App\Services\UsernameGenerator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $preguntas = PreguntaSeguridad::where('ESTADO', 1)
            ->orderBy('TEXTO_PREGUNTA')
            ->get();

        return view('auth.register', [
            'preguntasSeg'      => $preguntas,
            'hondurasLocations' => $this->hondurasLocations(),
        ]);
    }

    /**
     * Registra un nuevo usuario en la BD real:
     * 1) Crea tbl_persona
     * 2) Crea tbl_usuario (FK a persona, rol por defecto, password hasheado)
     * 3) Inserta correo, teléfono (opcional) y dirección (opcional)
     */
    public function store(Request $request)
    {
        $this->mergeFullNames($request);

        // 0) Normaliza el correo del request que usa tu form (CLAVE EN MAYÚSCULAS)
        $request->merge([
            'CORREO' => mb_strtolower(trim((string) $request->input('CORREO'))),
        ]);

        if ($request->filled('CIUDAD') && !$request->filled('MUNICIPIO')) {
            $request->merge([
                'MUNICIPIO' => $request->input('CIUDAD'),
            ]);
        }

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
    $usr = UsernameGenerator::generate($validated['PRIMER_NOMBRE'], $validated['PRIMER_APELLIDO']);

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
                    !empty($validated['REFERENCIA'])
                ) {
                    DB::table('tbl_direccion')->insert([
                        'FK_COD_PERSONA' => $persona->COD_PERSONA,
                        'DEPARTAMENTO'   => $validated['DEPARTAMENTO'] ?? '',
                        'MUNICIPIO'      => $validated['MUNICIPIO'] ?? '',
                        'CIUDAD'         => $validated['CIUDAD'] ?? null,
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

    private function mergeFullNames(Request $request): void
    {
        $fullNames = trim((string) $request->input('NOMBRES_COMPLETOS', ''));
        $fullSurnames = trim((string) $request->input('APELLIDOS_COMPLETOS', ''));

        if ($fullNames !== '' && !$request->filled('PRIMER_NOMBRE')) {
            [$first, $second] = $this->splitNameParts($fullNames);
            $request->merge([
                'PRIMER_NOMBRE'  => $first,
                'SEGUNDO_NOMBRE' => $second,
            ]);
        }

        if ($fullSurnames !== '' && !$request->filled('PRIMER_APELLIDO')) {
            [$first, $second] = $this->splitNameParts($fullSurnames);
            $request->merge([
                'PRIMER_APELLIDO'  => $first,
                'SEGUNDO_APELLIDO' => $second,
            ]);
        }
    }

    private function splitNameParts(string $fullName): array
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim($fullName))));
        $first = $parts[0] ?? '';
        $second = null;

        if (count($parts) > 1) {
            $second = trim(implode(' ', array_slice($parts, 1))) ?: null;
        }

        return [$first, $second];
    }

    private function hondurasLocations(): array
    {
        return [
            'Atlántida' => ['Arizona', 'El Porvenir', 'Esparta', 'Jutiapa', 'La Ceiba', 'La Masica', 'San Francisco', 'Tela'],
            'Choluteca' => ['Apacilagua', 'Choluteca', 'Concepción de María', 'Duyure', 'El Corpus', 'El Triunfo', 'Marcovia', 'Morolica', 'Namasigüe', 'Orocuina', 'Pespire', 'San Antonio de Flores', 'San Isidro', 'San José', 'San Marcos de Colón', 'Santa Ana de Yusguare'],
            'Colón' => ['Balfate', 'Bonito Oriental', 'Iriona', 'Limón', 'Sabá', 'Santa Fe', 'Santa Rosa de Aguán', 'Sonaguera', 'Tocoa', 'Trujillo'],
            'Comayagua' => ['Ajuterique', 'Comayagua', 'El Rosario', 'Esquías', 'Humuya', 'La Libertad', 'Lamaní', 'La Trinidad', 'Lejamaní', 'Meámbar', 'Minas de Oro', 'Ojos de Agua', 'San Jerónimo', 'San José de Comayagua', 'San José del Potrero', 'San Luis', 'San Sebastián', 'Siguatepeque', 'Taulabé', 'Villa de San Antonio', 'Las Lajas'],
            'Copán' => ['Cabañas', 'Concepción', 'Copán Ruinas', 'Corquín', 'Cucuyagua', 'Dolores', 'Dulce Nombre', 'El Paraíso', 'Florida', 'La Jigua', 'La Unión', 'Nueva Arcadia', 'San Agustín', 'San Antonio', 'San Jerónimo', 'San José', 'San Juan de Opoa', 'San Nicolás', 'San Pedro', 'Santa Rita', 'Santa Rosa de Copán', 'Trinidad de Copán', 'Veracruz'],
            'Cortés' => ['Choloma', 'La Lima', 'Omoa', 'Pimienta', 'Potrerillos', 'Puerto Cortés', 'San Antonio de Cortés', 'San Francisco de Yojoa', 'San Manuel', 'San Pedro Sula', 'Santa Cruz de Yojoa', 'Villanueva'],
            'El Paraíso' => ['Alauca', 'Danlí', 'El Paraíso', 'Güinope', 'Jacaleapa', 'Liure', 'Morocelí', 'Oropolí', 'Potrerillos', 'San Lucas', 'San Matías', 'Soledad', 'Teupasenti', 'Texiguat', 'Trojes', 'Vado Ancho', 'Yauyupe', 'Yuscarán'],
            'Francisco Morazán' => ['Alubarén', 'Cedros', 'Curarén', 'Distrito Central', 'El Porvenir', 'Guaimaca', 'La Libertad', 'La Venta', 'Lepaterique', 'Marale', 'Nueva Armenia', 'Ojojona', 'Orica', 'Reitoca', 'Sabanagrande', 'San Antonio de Oriente', 'San Buenaventura', 'San Ignacio', 'San Juan de Flores', 'San Miguelito', 'Santa Ana', 'Santa Lucía', 'Talanga', 'Tatumbla', 'Valle de Ángeles', 'Vallecillo', 'Villa de San Francisco'],
            'Gracias a Dios' => ['Ahuas', 'Brus Laguna', 'Juan Francisco Bulnes', 'Puerto Lempira', 'Villeda Morales', 'Wampusirpi'],
            'Intibucá' => ['Camasca', 'Colomoncagua', 'Concepción', 'Dolores', 'Intibucá', 'Jesús de Otoro', 'La Esperanza', 'Magdalena', 'Masaguara', 'San Antonio', 'San Francisco de Opalaca', 'San Isidro', 'San Juan', 'San Marcos de la Sierra', 'San Miguel Guancapla', 'Santa Lucía', 'Yamaranguila'],
            'Islas de la Bahía' => ['Guanaja', 'José Santos Guardiola', 'Roatán', 'Útila'],
            'La Paz' => ['Aguanqueterique', 'Cabañas', 'Cane', 'Chinacla', 'Guajiquiro', 'La Paz', 'Lauterique', 'Marcala', 'Mercedes de Oriente', 'Opatoro', 'San Antonio del Norte', 'San José', 'San Juan', 'San Pedro de Tutule', 'Santa Ana', 'Santa Elena', 'Santa María', 'Santiago de Puringla', 'Yarula'],
            'Lempira' => ['Belén', 'Candelaria', 'Cololaca', 'Erandique', 'Gracias', 'Gualcince', 'Guarita', 'La Campa', 'La Iguala', 'Las Flores', 'La Unión', 'La Virtud', 'Lepaera', 'Mapulaca', 'Piraera', 'San Andrés', 'San Francisco', 'San Juan Guarita', 'San Manuel Colohete', 'San Rafael', 'San Sebastián', 'Santa Cruz', 'Talgua', 'Tambla', 'Tomalá', 'Valladolid', 'Virginia'],
            'Ocotepeque' => ['Belén Gualcho', 'Concepción', 'Dolores Merendón', 'Fraternidad', 'La Encarnación', 'La Labor', 'Lucerna', 'Mercedes', 'Ocotepeque', 'San Fernando', 'San Francisco del Valle', 'San Jorge', 'San Marcos', 'Santa Fe', 'Sensenti', 'Sinuapa'],
            'Olancho' => ['Campamento', 'Catacamas', 'Concordia', 'Dulce Nombre de Culmí', 'El Rosario', 'Esquipulas del Norte', 'Gualaco', 'Guarizama', 'Guata', 'Guayape', 'Jano', 'Juticalpa', 'La Unión', 'Mangulile', 'Manto', 'Patuca', 'Salamá', 'San Esteban', 'San Francisco de Becerra', 'San Francisco de la Paz', 'Santa María del Real', 'Silca', 'Yocón'],
            'Santa Bárbara' => ['Arada', 'Atima', 'Azacualpa', 'Ceguaca', 'Chinda', 'Concepción del Norte', 'Concepción del Sur', 'El Níspero', 'Gualala', 'Ilama', 'Las Vegas', 'Macuelizo', 'Naranjito', 'Nueva Celilac', 'Petoa', 'Protección', 'Quimistán', 'San Francisco de Ojuera', 'San José de Colinas', 'San Luis', 'San Marcos', 'San Nicolás', 'San Pedro Zacapa', 'San Vicente Centenario', 'Santa Bárbara', 'Santa Rita', 'Trinidad'],
            'Valle' => ['Alianza', 'Amapala', 'Aramecina', 'Caridad', 'Goascorán', 'Langue', 'Nacaome', 'San Francisco de Coray', 'San Lorenzo'],
            'Yoro' => ['Arenal', 'El Negrito', 'El Progreso', 'Jocón', 'Morazán', 'Olanchito', 'Santa Rita', 'Sulaco', 'Victoria', 'Yoro', 'Yorito'],
        ];
    }

    /**
     * Genera username único: 1ra letra del nombre + apellido, minúsculas, sin acentos.
     * Si existe, agrega sufijo incremental (ej: wsolis, wsolis1, wsolis2...).
     */
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
