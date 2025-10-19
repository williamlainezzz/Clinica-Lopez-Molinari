<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Objeto;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
   public function index(Request $request)
{
    $rolId = (int)($request->query('rol_id') ?? 1); // o el que selecciones
    $objetos = \DB::table('tbl_objeto')
        ->where('ESTADO_OBJETO', '<>', 0)->get();

    $rows = \DB::table('tbl_permiso')
        ->where('FK_COD_ROL', $rolId)
        ->get();

    $permisosPorObjeto = $rows->keyBy('FK_COD_OBJETO');

    return view('permisos.index', compact('objetos','permisosPorObjeto','rolId'));
}

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

    $rolId    = (int)$data['rol_id'];
    $permisos = $data['permisos'] ?? [];

    DB::transaction(function () use ($rolId, $permisos) {

        // 1) Garantiza que *exista* un registro por cada objeto que llegó,
        //    y setea exactamente lo que vino (0 o 1).
        foreach ($permisos as $codObjeto => $valores) {
            $ver      = isset($valores['VER']) ? (int)$valores['VER'] : 0;
            $crear    = isset($valores['CREAR']) ? (int)$valores['CREAR'] : 0;
            $editar   = isset($valores['EDITAR']) ? (int)$valores['EDITAR'] : 0;
            $eliminar = isset($valores['ELIMINAR']) ? (int)$valores['ELIMINAR'] : 0;

            DB::table('tbl_permiso')->updateOrInsert(
                [
                    'FK_COD_ROL'   => $rolId,
                    'FK_COD_OBJETO'=> (int)$codObjeto,
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

        // 2) Opcional: para objetos que NO llegaron en el form,
        //    puedes poner todo en 0. Si quieres esto, descomenta:
        /*
        $idsEnForm = array_map('intval', array_keys($permisos));
        DB::table('tbl_permiso')
            ->where('FK_COD_ROL', $rolId)
            ->whereNotIn('FK_COD_OBJETO', $idsEnForm)
            ->update([
                'VER' => 0, 'CREAR' => 0, 'EDITAR' => 0, 'ELIMINAR' => 0
            ]);
        */
    });

    return back()->with('ok', 'Permisos actualizados');
}
}
