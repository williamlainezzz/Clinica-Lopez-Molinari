<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    public function index(Request $request)
    {
        // Catálogo de roles para el <select>
        $roles = DB::table('tbl_rol')
            ->select('COD_ROL','NOM_ROL')
            ->orderBy('NOM_ROL')
            ->get();

        // Rol seleccionado (o el primero disponible)
        $rolId = (int) ($request->query('rol_id') ?? ($roles->first()->COD_ROL ?? 1));

        // Objetos activos
        $objetos = DB::table('tbl_objeto')
            ->select('COD_OBJETO','NOM_OBJETO','URL_OBJETO','ESTADO_OBJETO')
            ->where('ESTADO_OBJETO','<>',0)
            ->orderBy('NOM_OBJETO')
            ->get();

        // Permisos del rol seleccionado, indexados por objeto
        $rows = DB::table('tbl_permiso')
            ->where('FK_COD_ROL', $rolId)
            ->get();

        // Estructura que consume el Blade: $permisos[rolId][cod_objeto] = fila permiso
        $permisos = collect([$rolId => $rows->keyBy('FK_COD_OBJETO')]);

        // 👇 Usa la ruta/nombre de vista correcto (carpeta seguridad/permisos)
        return view('seguridad.permisos.index', compact('roles','rolId','objetos','permisos'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'rol_id'              => 'required|integer',
            'permisos'            => 'array',
            'permisos.*.VER'      => 'nullable|in:0,1',
            'permisos.*.CREAR'    => 'nullable|in:0,1',
            'permisos.*.EDITAR'   => 'nullable|in:0,1',
            'permisos.*.ELIMINAR' => 'nullable|in:0,1',
        ]);

        $rolId    = (int) $data['rol_id'];
        $permisos = $data['permisos'] ?? [];

        DB::transaction(function () use ($rolId, $permisos) {
            foreach ($permisos as $codObjeto => $valores) {
                $ver      = (int)($valores['VER'] ?? 0);
                $crear    = (int)($valores['CREAR'] ?? 0);
                $editar   = (int)($valores['EDITAR'] ?? 0);
                $eliminar = (int)($valores['ELIMINAR'] ?? 0);

                DB::table('tbl_permiso')->updateOrInsert(
                    ['FK_COD_ROL' => $rolId, 'FK_COD_OBJETO' => (int)$codObjeto],
                    [
                        'ESTADO_PERMISO' => 1,
                        'VER'            => $ver,
                        'CREAR'          => $crear,
                        'EDITAR'         => $editar,
                        'ELIMINAR'       => $eliminar,
                    ]
                );
            }

            // Si quieres que lo NO enviado quede en 0, descomenta este bloque:
            // $idsEnForm = array_map('intval', array_keys($permisos));
            // DB::table('tbl_permiso')
            //     ->where('FK_COD_ROL', $rolId)
            //     ->whereNotIn('FK_COD_OBJETO', $idsEnForm)
            //     ->update(['VER'=>0,'CREAR'=>0,'EDITAR'=>0,'ELIMINAR'=>0]);
        });

        return back()->with('ok','Permisos actualizados');
    }
}
