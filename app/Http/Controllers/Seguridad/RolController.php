<?php

namespace App\Http\Controllers\Seguridad;

use App\Services\BitacoraService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function index(Request $request)
    {
        $roles = DB::table('tbl_rol as r')
            ->select(
                'r.COD_ROL',
                'r.NOM_ROL',
                DB::raw('(SELECT COUNT(*) FROM tbl_usuario u WHERE u.FK_COD_ROL = r.COD_ROL) as usuarios'),
                DB::raw('(SELECT COUNT(*) FROM tbl_permiso p WHERE p.FK_COD_ROL = r.COD_ROL) as permisos')
            )
            ->orderBy('r.NOM_ROL')
            ->paginate(10);

        return view('seguridad.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('seguridad.roles.create');
    }

    public function store(Request $request)
{
    // Validación
    $request->validate([
        'NOM_ROL'      => 'required|string|max:100|unique:tbl_rol,NOM_ROL',
        'DESCRIPCION'  => 'nullable|string|max:255',
    ]);

    // Asegurar que siempre mandamos algún valor a DESCRIPCION
    $nom  = trim($request->input('NOM_ROL'));
    $desc = (string) $request->input('DESCRIPCION', '');

    // Insertar y obtener ID
    $id = DB::table('tbl_rol')->insertGetId([
        'NOM_ROL'     => $nom,
        'DESCRIPCION' => $desc,   // 👈 ¡Se envía!
    ]);

    \App\Services\BitacoraService::log('ROLES', 'CREAR', "Nuevo rol #{$id}: {$nom}");

    return redirect()->route('seguridad.roles.index')
        ->with('success', 'Rol creado correctamente.');
}



    public function edit($id)
    {
        $rol = DB::table('tbl_rol')->where('COD_ROL', $id)->first();
        abort_if(!$rol, 404);

        return view('seguridad.roles.edit', compact('rol'));
    }

    public function update(Request $request, $id)
{
    // Validación
    $request->validate([
        'NOM_ROL'      => 'required|string|max:100|unique:tbl_rol,NOM_ROL,' . $id . ',COD_ROL',
        'DESCRIPCION'  => 'nullable|string|max:255',
    ]);

    // Rol previo para bitácora
    $rolOld = DB::table('tbl_rol')->where('COD_ROL', $id)->first();
    abort_if(!$rolOld, 404, 'Rol no encontrado');

    $nom  = trim($request->input('NOM_ROL'));
    $desc = (string) $request->input('DESCRIPCION', '');

    // Actualizar
    DB::table('tbl_rol')->where('COD_ROL', $id)->update([
        'NOM_ROL'     => $nom,
        'DESCRIPCION' => $desc,   // 👈 también aquí
    ]);

    \App\Services\BitacoraService::log(
        'ROLES',
        'EDITAR',
        "Rol #{$id}: {$rolOld->NOM_ROL} -> {$nom}"
    );

    return redirect()->route('seguridad.roles.index')->with('success', 'Rol actualizado.');
}

    public function destroy($id)
    {
        $tieneUsuarios = DB::table('tbl_usuario')->where('FK_COD_ROL', $id)->exists();
        if ($tieneUsuarios) {
            return back()->with('error', 'No se puede eliminar: el rol tiene usuarios asociados.');
        }

        $tienePermisos = DB::table('tbl_permiso')->where('FK_COD_ROL', $id)->exists();
        if ($tienePermisos) {
            return back()->with('error', 'No se puede eliminar: el rol tiene permisos asociados.');
        }

        $rol = DB::table('tbl_rol')->where('COD_ROL', $id)->first();
        abort_if(!$rol, 404, 'Rol no encontrado');

        DB::table('tbl_rol')->where('COD_ROL', $id)->delete();

        BitacoraService::log('ROLES', 'ELIMINAR', "Rol #{$id}: {$rol->NOM_ROL}");

        return back()->with('success', 'Rol eliminado.');
    }
}
