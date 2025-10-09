<?php
// app/Http/Controllers/Seguridad/RolController.php

namespace App\Http\Controllers\Seguridad;

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
        $request->validate([
            'NOM_ROL' => 'required|string|max:100|unique:tbl_rol,NOM_ROL',
        ]);

        DB::table('tbl_rol')->insert([
            'NOM_ROL' => trim($request->NOM_ROL),
        ]);

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
        $request->validate([
            'NOM_ROL' => 'required|string|max:100|unique:tbl_rol,NOM_ROL,' . $id . ',COD_ROL',
        ]);

        DB::table('tbl_rol')->where('COD_ROL', $id)->update([
            'NOM_ROL' => trim($request->NOM_ROL),
        ]);

        return redirect()->route('seguridad.roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy($id)
    {
        // Bloqueo por relaciones (mejoraremos en Paso 3)
        $tieneUsuarios = DB::table('tbl_usuario')->where('FK_COD_ROL', $id)->exists();
        $tienePermisos = DB::table('tbl_permiso')->where('FK_COD_ROL', $id)->exists();

        if ($tieneUsuarios || $tienePermisos) {
            return redirect()->route('seguridad.roles.index')
                ->with('error', 'No se puede eliminar: el rol tiene usuarios o permisos asociados.');
        }

        DB::table('tbl_rol')->where('COD_ROL', $id)->delete();

        return redirect()->route('seguridad.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}
