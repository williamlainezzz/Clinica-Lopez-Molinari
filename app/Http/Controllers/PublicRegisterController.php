<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Persona;
use App\Models\Usuario;

class PublicRegisterController extends Controller
{
    /**
     * Store a newly created patient (persona + usuario + contacto).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'PRIMER_NOMBRE'    => 'required|string|max:120',
            'SEGUNDO_NOMBRE'   => 'nullable|string|max:120',
            'PRIMER_APELLIDO'  => 'required|string|max:120',
            'SEGUNDO_APELLIDO' => 'nullable|string|max:120',
            'TIPO_GENERO'      => 'required',
            'NUM_TELEFONO'     => 'nullable|string|max:50',
            'REFERENCIA'       => 'nullable|string|max:500',
            'CORREO'           => 'required|email|max:191',
            'DEPARTAMENTO'     => 'nullable|string|max:120',
            'MUNICIPIO'        => 'nullable|string|max:120',
            'COLONIA'          => 'nullable|string|max:120',
            'CIUDAD'           => 'nullable|string|max:120',
            'password'         => 'required|string|min:6|confirmed',
            'ESTADO'           => 'required|string|in:Activo,Inactivo',
        ]);

        // Generar username parecido al frontend: inicial + primer apellido, sin acentos, lowercase
        $baseUser = $this->makeUsername($data['PRIMER_NOMBRE'] ?? '', $data['PRIMER_APELLIDO'] ?? '');
        $username = $this->ensureUniqueUsername($baseUser);

        // Siempre asignar código 5 para pacientes
        $rolId = 5;

        DB::beginTransaction();
        try {
            // Crear persona (sin campos de dirección)
            $persona = Persona::create([
                'PRIMER_NOMBRE'    => $data['PRIMER_NOMBRE'],
                'SEGUNDO_NOMBRE'   => $data['SEGUNDO_NOMBRE'] ?? null,
                'PRIMER_APELLIDO'  => $data['PRIMER_APELLIDO'],
                'SEGUNDO_APELLIDO' => $data['SEGUNDO_APELLIDO'] ?? null,
                'TIPO_GENERO'      => $data['TIPO_GENERO'],
                // NOTA: campos de dirección se insertan en tbl_direccion abajo
            ]);

            $personaId = $persona->COD_PERSONA ?? ($persona->id ?? null);

            // Mapear ESTADO legible a valor esperado por BD (ej. 1 = Activo, 0 = Inactivo)
            $estadoUsuario = ($data['ESTADO'] === 'Activo' || $data['ESTADO'] === 'activo' || $data['ESTADO'] === 1) ? 1 : 0;

            // Crear usuario (tbl_usuario)
            $usuario = Usuario::create([
                'USR_USUARIO'     => $username,
                'PWD_USUARIO'     => Hash::make($data['password']),
                'FK_COD_PERSONA'  => $personaId,
                'FK_COD_ROL'      => $rolId,
                'ESTADO_USUARIO'  => $estadoUsuario,
            ]);

            // Guardar correo en tbl_correo
            DB::table('tbl_correo')->insert([
                'FK_COD_PERSONA' => $personaId,
                'CORREO'         => $data['CORREO'],
                'TIPO_CORREO'    => 1, // 1 = correo principal (ajusta según tu esquema)
                // agrega otros campos obligatorios si tu tabla los requiere (ej. ES_PRINCIPAL, FECHA_REG)
            ]);

            // Guardar teléfono si viene
            if (!empty($data['NUM_TELEFONO'])) {
                DB::table('tbl_telefono')->insert([
                    'FK_COD_PERSONA' => $personaId,
                    'NUM_TELEFONO'   => $data['NUM_TELEFONO'],
                    'TIPO_TELEFONO'  => 1, // 1 = teléfono principal (ajusta según tu esquema)
                    // agrega otros campos obligatorios si tu tabla los requiere
                ]);
            }

            // --- Nuevo: guardar dirección en tbl_direccion ---
            // Se usa la tabla tbl_direccion con FK_COD_PERSONA (ajusta nombres si difieren)
            DB::table('tbl_direccion')->insert([
                'FK_COD_PERSONA' => $personaId,
                'DEPARTAMENTO'   => $data['DEPARTAMENTO'] ?? null,
                'MUNICIPIO'      => $data['MUNICIPIO'] ?? null,
                'CIUDAD'         => $data['CIUDAD'] ?? null,
                'COLONIA'        => $data['COLONIA'] ?? null,
                'REFERENCIA'     => $data['REFERENCIA'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'ok' => true,
                'persona_id' => $personaId,
                'usuario' => $username,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error registrando persona pública: ' . $e->getMessage());

            $payload = ['error' => 'No se pudo crear la persona.'];
            if (config('app.debug')) {
                $payload['exception'] = $e->getMessage();
                $payload['trace'] = $e->getTraceAsString();
            }

            return response()->json($payload, 500);
        }
    }

    /**
     * Devuelve datos de persona para edición (JSON).
     */
    public function show($id)
    {
        $row = DB::table('tbl_persona as p')
            ->leftJoin('tbl_usuario as u', 'u.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->leftJoin('tbl_telefono as t', function($join){
                $join->on('t.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
                // opcional: filtrar por tipo principal si aplica ->where('t.TIPO_TELEFONO',1)
            })
            ->leftJoin('tbl_direccion as d', 'd.FK_COD_PERSONA', '=', 'p.COD_PERSONA')
            ->select(
                'p.COD_PERSONA as id',
                'p.PRIMER_NOMBRE',
                'p.PRIMER_APELLIDO',
                'p.TIPO_GENERO',
                't.NUM_TELEFONO as telefono',
                'd.REFERENCIA as direccion',
                'u.ESTADO_USUARIO as estado',
                'u.FK_COD_ROL as rol'
            )
            ->where('p.COD_PERSONA', $id)
            ->first();

        if (!$row) {
            return response()->json(['error' => 'No existe la persona'], 404);
        }

        return response()->json($row);
    }

    /**
     * Actualiza persona + contacto + estado (PUT /personas/{id}).
     */
    public function update(Request $request, $id)
    {
        $input = $request->validate([
            'full_name'   => 'required|string|max:255',
            'TIPO_GENERO' => 'required',
            'NUM_TELEFONO'=> 'nullable|string|max:50',
            'REFERENCIA'  => 'nullable|string|max:500',
            'ESTADO'      => 'required|string|in:Activo,Inactivo',
        ]);

        // dividir full_name en primer nombre y primer apellido (simple heurística)
        $parts = preg_split('/\s+/', trim($input['full_name']));
        $primer = $parts[0] ?? '';
        $segundo = count($parts) > 1 ? trim(implode(' ', array_slice($parts, 1))) : '';

        DB::beginTransaction();
        try {
            // actualizar persona
            DB::table('tbl_persona')->where('COD_PERSONA', $id)->update([
                'PRIMER_NOMBRE'    => $primer,
                'PRIMER_APELLIDO'  => $segundo,
                'TIPO_GENERO'      => $input['TIPO_GENERO'],
            ]);

            // actualizar estado en tbl_usuario (si existe)
            $estadoUsuario = ($input['ESTADO'] === 'Activo') ? 1 : 0;
            DB::table('tbl_usuario')->where('FK_COD_PERSONA', $id)->update([
                'ESTADO_USUARIO' => $estadoUsuario,
            ]);

            // actualizar telefono principal: si existe update, sino insert
            if (!empty($input['NUM_TELEFONO'])) {
                $tel = DB::table('tbl_telefono')->where('FK_COD_PERSONA', $id)->first();
                if ($tel) {
                    DB::table('tbl_telefono')->where('FK_COD_PERSONA', $id)->update([
                        'NUM_TELEFONO' => $input['NUM_TELEFONO'],
                        // 'TIPO_TELEFONO' => 1,  // si deseas forzar
                    ]);
                } else {
                    DB::table('tbl_telefono')->insert([
                        'FK_COD_PERSONA' => $id,
                        'NUM_TELEFONO'   => $input['NUM_TELEFONO'],
                        'TIPO_TELEFONO'  => 1,
                    ]);
                }
            } else {
                // si viene vacío, opcionalmente eliminar o dejar; aquí no hacemos nada
            }

            // actualizar dirección: escribimos en REFERENCIA si existe registro, sino insert
            $dir = DB::table('tbl_direccion')->where('FK_COD_PERSONA', $id)->first();
            if ($dir) {
                DB::table('tbl_direccion')->where('FK_COD_PERSONA', $id)->update([
                    'REFERENCIA' => $input['REFERENCIA'] ?? null,
                ]);
            } else {
                DB::table('tbl_direccion')->insert([
                    'FK_COD_PERSONA' => $id,
                    'REFERENCIA'     => $input['REFERENCIA'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json(['ok' => true]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error actualizando persona: ' . $e->getMessage());
            $payload = ['error' => 'No se pudo actualizar la persona.'];
            if (config('app.debug')) {
                $payload['exception'] = $e->getMessage();
            }
            return response()->json($payload, 500);
        }
    }

    private function makeUsername(string $nombre, string $apellido): string
    {
        $first = mb_substr(trim($nombre), 0, 1);
        $last = preg_replace('/\s+/', '', trim($apellido));
        $base = strtolower($this->stripDiacritics($first . $last));
        $base = preg_replace('/[^a-z0-9]/', '', $base);
        if ($base === '') $base = 'user' . Str::random(3);
        return Str::limit($base, 50);
    }

    private function ensureUniqueUsername(string $base): string
    {
        $candidate = $base;
        $i = 0;
        while ( DB::table('tbl_usuario')->where('USR_USUARIO', $candidate)->exists() ) {
            $i++;
            $candidate = $base . $i;
            if ($i > 9999) break;
        }
        return $candidate;
    }

    private function stripDiacritics(string $str): string
    {
        // Intentar Normalizer si está disponible
        if (function_exists('normalizer_normalize')) {
            $normalized = normalizer_normalize($str, \Normalizer::FORM_D);
            return preg_replace('/\p{M}/u', '', $normalized);
        }

        // Fallback: intentar iconv transliteration, luego eliminar caracteres no ascii
        if (function_exists('iconv')) {
            $trans = @iconv('UTF-8', 'ASCII//TRANSLIT', $str);
            if ($trans !== false) {
                return preg_replace('/[^a-zA-Z0-9]/', '', $trans);
            }
        }

        // Último recurso: eliminar marcas diacríticas mediante regex básica
        return preg_replace('/[^\p{L}\p{N}]+/u', '', $str);
    }
}
