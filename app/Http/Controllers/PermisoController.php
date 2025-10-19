<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    public function index(Request $request)
    {
        // Traer roles para el <select> (ajusta NOMBRE/IDs a tu esquema)
        $roles = DB::table('tbl_rol')
            ->select('COD_ROL','NOM_ROL')
            ->orderBy('NOM_ROL')
            ->get();

        // Rol seleccionado (por defecto el primero)
        $rolId = (int)($request->query('rol_id') ?? optional($roles->first())->COD_ROL ?? 1);

        // Objetos activos
        $objetos = DB::table('tbl_objeto')
            ->select('COD_OBJETO','NOM_OBJETO','URL_OBJETO','ESTADO_OBJETO')
            ->whereRaw('IFNULL(ESTADO_OBJETO,1) <> 0')
            ->orderBy('NOM_OBJETO')
            ->get();

        // Permisos existentes para el rol
        $rows = DB::table('tbl_permiso')
            ->where('FK_COD_ROL', $rolId)
            ->get();

        // Mapear por objeto para lookup rápido en la vista
        $permisosPorObjeto = $rows->keyBy('FK_COD_OBJETO');

        // IMPORTANTE: usa la ruta real de la vista
        return view('seguridad.permisos.index', compact(
            'roles','rolId','objetos','permisosPorObjeto'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'rol_id'   => 'required|integer',
            'permisos' => 'array',
        ]);

        $rolId    = (int) $data['rol_id'];
        $permisos = $data['permisos'] ?? [];

        DB::transaction(function () use ($rolId, $permisos) {
            // 1) Actualiza exactamente lo que llegó (gracias a los hidden=0, siempre llega algo)
            foreach ($permisos as $codObjeto => $flags) {
                $ver      = isset($flags['VER'])      ? (int)$flags['VER']      : 0;
                $crear    = isset($flags['CREAR'])    ? (int)$flags['CREAR']    : 0;
                $editar   = isset($flags['EDITAR'])   ? (int)$flags['EDITAR']   : 0;
                $eliminar = isset($flags['ELIMINAR']) ? (int)$flags['ELIMINAR'] : 0;

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

            // 2) (OPCIONAL) poner en 0 todo objeto que no vino en el form.
            //    Úsalo si decides NO enviar hidden inputs. Si mantienes los hidden, no hace falta.
            // $idsEnForm = array_map('intval', array_keys($permisos));
            // DB::table('tbl_permiso')
            //     ->where('FK_COD_ROL', $rolId)
            //     ->whereNotIn('FK_COD_OBJETO', $idsEnForm)
            //     ->update(['VER'=>0,'CREAR'=>0,'EDITAR'=>0,'ELIMINAR'=>0]);
        });

        return back()->with('ok', 'Permisos actualizados');
    }
}
