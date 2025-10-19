<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    /**
     * GET /seguridad/permisos
     */
    public function index(Request $request)
    {
        // Roles activos
        $roles = DB::table('tbl_rol')
            ->select('COD_ROL', 'NOM_ROL')
            ->whereNull('deleted_at')        // quita esto si tu tabla no tiene soft deletes
            ->where('ESTADO_ROL', '<>', 0)   // ajusta el nombre de columna si difiere
            ->orderBy('NOM_ROL')
            ->get();

        // Rol seleccionado (por querystring o primero de la lista)
        $selectedRoleId = (int) $request->query('rol_id', optional($roles->first())->COD_ROL ?? 1);

        // Objetos activos
        $objetos = DB::table('tbl_objeto')
            ->select('COD_OBJETO', 'NOM_OBJETO', 'URL_OBJETO', 'ESTADO_OBJETO')
            ->where('ESTADO_OBJETO', '<>', 0)
            ->orderBy('NOM_OBJETO')
            ->get();

        // Permisos del rol seleccionado, indexados por COD_OBJETO
        $rows = DB::table('tbl_permiso')
            ->where('FK_COD_ROL', $selectedRoleId)
            ->get()
            ->keyBy('FK_COD_OBJETO');

        // La vista espera $permisos como un mapa de rol => (objeto => registro)
        $permisos = collect([$selectedRoleId => $rows]);

        // IMPORTANTE: ruta real de la vista
        return view('seguridad.permisos.index', compact(
            'roles',
            'selectedRoleId',
            'objetos',
            'permisos'
        ));
    }

    /**
     * POST /seguridad/permisos
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'rol_id'                 => 'required|integer',
            'permisos'               => 'array',
            'permisos.*.VER'         => 'nullable|in:0,1',
            'permisos.*.CREAR'       => 'nullable|in:0,1',
            'permisos.*.EDITAR'      => 'nullable|in:0,1',
            'permisos.*.ELIMINAR'    => 'nullable|in:0,1',
        ]);

        $rolId    = (int) $data['rol_id'];
        $permisos = $data['permisos'] ?? [];

        DB::transaction(function () use ($rolId, $permisos) {
            foreach ($permisos as $codObjeto => $val) {
                $ver      = isset($val['VER'])      ? (int)$val['VER']      : 0;
                $crear    = isset($val['CREAR'])    ? (int)$val['CREAR']    : 0;
                $editar   = isset($val['EDITAR'])   ? (int)$val['EDITAR']   : 0;
                $eliminar = isset($val['ELIMINAR']) ? (int)$val['ELIMINAR'] : 0;

                DB::table('tbl_permiso')->updateOrInsert(
                    [
                        'FK_COD_ROL'    => $rolId,
                        'FK_COD_OBJETO' => (int) $codObjeto,
                    ],
                    [
                        'ESTADO_PERMISO' => 1,
                        'VER'            => $ver,
                        'CREAR'          => $crear,
                        'EDITAR'         => $editar,
                        'ELIMINAR'       => $eliminar,
                    ]
                );
            }

            // (Opcional) Descomenta si quieres apagar TODO lo que no vino en el formulario:
            /*
            $idsEnForm = array_map('intval', array_keys($permisos));
            DB::table('tbl_permiso')
                ->where('FK_COD_ROL', $rolId)
                ->whereNotIn('FK_COD_OBJETO', $idsEnForm)
                ->update(['VER'=>0,'CREAR'=>0,'EDITAR'=>0,'ELIMINAR'=>0]);
            */
        });

        // Volvemos a la misma pantalla preservando el rol seleccionado
        return redirect()
            ->route('seguridad.permisos.index', ['rol_id' => $rolId])
            ->with('ok', 'Permisos actualizados');
    }
}

