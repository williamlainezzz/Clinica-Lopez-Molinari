<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Listado de usuarios con filtros y catálogos (roles, estados)
     */
  public function index(Request $request)
{
    // Filtros del request
    $q      = trim($request->input('q', ''));   // búsqueda libre
    $rolId  = $request->input('rol_id');        // filtrar por rol
    $estado = $request->input('estado');        // 1 = activo, 0 = inactivo

    // Subconsulta: tomar un correo (el mínimo) por persona
    $correoSub = DB::table('tbl_correo')
        ->select('FK_COD_PERSONA', DB::raw('MIN(CORREO) as CORREO'))
        ->groupBy('FK_COD_PERSONA');

    // Subconsulta: tomar un teléfono (el mínimo) por persona  ⚠️ usa NUM_TELEFONO
    $telSub = DB::table('tbl_telefono')
        ->select('FK_COD_PERSONA', DB::raw('MIN(NUM_TELEFONO) as TELEFONO'))
        ->groupBy('FK_COD_PERSONA');

    // Nombre completo según tu esquema
    $nombreCompleto = "CONCAT_WS(' ', p.PRIMER_NOMBRE, NULLIF(p.SEGUNDO_NOMBRE,''), p.PRIMER_APELLIDO, NULLIF(p.SEGUNDO_APELLIDO,''))";

    $usuarios = DB::table('tbl_usuario as u')
        ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'u.FK_COD_PERSONA')
        ->leftJoin('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
        ->leftJoinSub($correoSub, 'c', function ($join) {
            $join->on('c.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
        })
        ->leftJoinSub($telSub, 't', function ($join) {
            $join->on('t.FK_COD_PERSONA', '=', 'p.COD_PERSONA');
        })
        ->select([
            'u.COD_USUARIO',

            // El Blade usa $u->USR_USUARIO
            'u.USR_USUARIO',

            // El Blade usa $u->nombre
            DB::raw("$nombreCompleto as nombre"),

            // Rol con el alias exacto que usa la vista ($u->rol)
            'u.FK_COD_ROL',
            'r.NOM_ROL as rol',

            // Correo y teléfono con los alias que la vista usa
            DB::raw("COALESCE(c.CORREO, '') as correo"),
            DB::raw("COALESCE(c.CORREO, '') as EMAIL"),
            DB::raw("COALESCE(t.TELEFONO, '') as telefono"),

            // El Blade usa $u->ESTADO_USUARIO en mayúsculas
            'u.ESTADO_USUARIO',

            // Además, variantes útiles
            'u.ESTADO_USUARIO as estado_id',
            DB::raw("CASE u.ESTADO_USUARIO
                        WHEN 1 THEN 'ACTIVO'
                        WHEN 0 THEN 'INACTIVO'
                        ELSE CONCAT('EST-', u.ESTADO_USUARIO)
                     END as estado"),
        ])
        // Búsqueda libre: usuario, nombre completo, correo
        ->when($q !== '', function ($qb) use ($q, $nombreCompleto) {
            $qb->where(function ($w) use ($q, $nombreCompleto) {
                $w->where('u.USR_USUARIO', 'like', "%{$q}%")
                  ->orWhere(DB::raw($nombreCompleto), 'like', "%{$q}%")
                  ->orWhere('c.CORREO', 'like', "%{$q}%");
            });
        })
        // Filtro por rol
        ->when(!empty($rolId), function ($qb) use ($rolId) {
            $qb->where('u.FK_COD_ROL', $rolId);
        })
        // Filtro por estado (1/0)
        ->when($estado !== null && $estado !== '', function ($qb) use ($estado) {
            $qb->where('u.ESTADO_USUARIO', $estado);
        })
        ->orderBy('p.PRIMER_APELLIDO')
        ->orderBy('p.PRIMER_NOMBRE')
        ->paginate(10)
        ->appends($request->query());

    // Catálogo de roles
    $roles = DB::table('tbl_rol')
        ->select('COD_ROL', 'NOM_ROL')
        ->orderBy('NOM_ROL')
        ->get();

    // Catálogo básico de estados (no existe tabla)
    $estados = collect([
        (object)['COD_ESTADO_USUARIO' => 1, 'ESTADO_USUARIO' => 'ACTIVO'],
        (object)['COD_ESTADO_USUARIO' => 0, 'ESTADO_USUARIO' => 'INACTIVO'],
    ]);

    $filtros = [
        'q'      => $q,
        'rol_id' => $rolId,
        'estado' => $estado,
    ];

    return view('seguridad.usuarios.index', compact('usuarios', 'roles', 'estados', 'filtros'));
}

// Formulario de creación de usuario
public function create()
{
    // Personas disponibles (listado simple por apellido + nombre)
    $personas = DB::table('tbl_persona')
        ->select('COD_PERSONA',
            DB::raw("CONCAT_WS(' ', PRIMER_APELLIDO, SEGUNDO_APELLIDO, PRIMER_NOMBRE, SEGUNDO_NOMBRE) as nombre"))
        ->orderBy('PRIMER_APELLIDO')->orderBy('PRIMER_NOMBRE')
        ->get();

    // Roles
    $roles = DB::table('tbl_rol')->select('COD_ROL', 'NOM_ROL')->orderBy('NOM_ROL')->get();

    // Estados “en memoria”
    $estados = collect([
        (object)['id' => 1, 'txt' => 'ACTIVO'],
        (object)['id' => 0, 'txt' => 'INACTIVO'],
    ]);

    return view('seguridad.usuarios.create', compact('personas', 'roles', 'estados'));
}

public function store(Request $request)
{
    $request->validate([
        'FK_COD_PERSONA' => ['required', 'integer', 'exists:tbl_persona,COD_PERSONA'],
        'USR_USUARIO'    => ['required', 'string', 'max:50', 'unique:tbl_usuario,USR_USUARIO'],
        'FK_COD_ROL'     => ['required', 'integer', 'exists:tbl_rol,COD_ROL'],
        'ESTADO_USUARIO' => ['required', 'in:0,1'],
        'password'       => [\Illuminate\Validation\Rules\Password::defaults()],
    ], [], [
        'FK_COD_PERSONA' => 'persona',
        'USR_USUARIO'    => 'usuario',
        'FK_COD_ROL'     => 'rol',
        'ESTADO_USUARIO' => 'estado',
        'password'       => 'contraseña',
    ]);

    DB::transaction(function () use ($request) {
        DB::table('tbl_usuario')->insert([
            'FK_COD_PERSONA'  => (int) $request->FK_COD_PERSONA,
            'USR_USUARIO'     => $request->USR_USUARIO,
            'PWD_USUARIO'     => \Illuminate\Support\Facades\Hash::make($request->password),
            'FK_COD_ROL'      => (int) $request->FK_COD_ROL,
            'ESTADO_USUARIO'  => (int) $request->ESTADO_USUARIO,
        ]);
    });

    return redirect()->route('seguridad.usuarios.index')
        ->with('success', 'Usuario creado correctamente.');
}

public function edit($id)
{
    $u = DB::table('tbl_usuario')->where('COD_USUARIO', $id)->first();
    abort_if(!$u, 404);

    $personas = DB::table('tbl_persona')
        ->select('COD_PERSONA',
            DB::raw("CONCAT_WS(' ', PRIMER_APELLIDO, SEGUNDO_APELLIDO, PRIMER_NOMBRE, SEGUNDO_NOMBRE) as nombre"))
        ->orderBy('PRIMER_APELLIDO')->orderBy('PRIMER_NOMBRE')
        ->get();

    $roles = DB::table('tbl_rol')->select('COD_ROL', 'NOM_ROL')->orderBy('NOM_ROL')->get();

    $estados = collect([
        (object)['id' => 1, 'txt' => 'ACTIVO'],
        (object)['id' => 0, 'txt' => 'INACTIVO'],
    ]);

    return view('seguridad.usuarios.edit', compact('u', 'personas', 'roles', 'estados'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'FK_COD_PERSONA' => ['required', 'integer', 'exists:tbl_persona,COD_PERSONA'],
        'USR_USUARIO'    => ['required', 'string', 'max:50', "unique:tbl_usuario,USR_USUARIO,{$id},COD_USUARIO"],
        'FK_COD_ROL'     => ['required', 'integer', 'exists:tbl_rol,COD_ROL'],
        'ESTADO_USUARIO' => ['required', 'in:0,1'],
        'password'       => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    DB::transaction(function () use ($request, $id) {
        $data = [
            'FK_COD_PERSONA'  => (int) $request->FK_COD_PERSONA,
            'USR_USUARIO'     => $request->USR_USUARIO,
            'FK_COD_ROL'      => (int) $request->FK_COD_ROL,
            'ESTADO_USUARIO'  => (int) $request->ESTADO_USUARIO,
        ];
        if ($request->filled('password')) {
            $data['PWD_USUARIO'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        DB::table('tbl_usuario')->where('COD_USUARIO', $id)->update($data);
    });

    return redirect()->route('seguridad.usuarios.index')
        ->with('success', 'Usuario actualizado.');
}

public function destroy($id)
{
    // Eliminación suave: dejar el registro pero marcar INACTIVO
    DB::table('tbl_usuario')
        ->where('COD_USUARIO', $id)
        ->update(['ESTADO_USUARIO' => 0]);

    return redirect()->route('seguridad.usuarios.index')
        ->with('success', 'Usuario desactivado.');
}

}
