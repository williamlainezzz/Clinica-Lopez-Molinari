<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Objeto;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{
    public function index(Request $req)
    {
        $roles   = Rol::orderBy('NOM_ROL')->get();
        $objetos = Objeto::orderBy('NOM_OBJETO')->get();

        // permisos existentes como [rol][obj] => flags
        $permisos = Permiso::get()->groupBy('FK_COD_ROL')->map(function($g){
            return $g->keyBy('FK_COD_OBJETO');
        });

        return view('seguridad.permisos.index', compact('roles','objetos','permisos'));
    }

    public function update(Request $req)
    {
        $data = $req->validate([
            'rol_id' => 'required|integer',
            'permisos' => 'array' // permisos[obj_id] = [VER,CREAR,EDITAR,ELIMINAR]
        ]);

        DB::transaction(function() use ($data){
            $rol = (int)$data['rol_id'];
            foreach (($data['permisos'] ?? []) as $objId => $flags) {
                $ver  = isset($flags['VER']) ? 1 : 0;
                $cre  = isset($flags['CREAR']) ? 1 : 0;
                $edi  = isset($flags['EDITAR']) ? 1 : 0;
                $eli  = isset($flags['ELIMINAR']) ? 1 : 0;

                Permiso::updateOrInsert(
                    ['FK_COD_ROL'=>$rol, 'FK_COD_OBJETO'=>$objId],
                    [
                        'ESTADO_PERMISO'=>1,
                        'VER'=>$ver,'CREAR'=>$cre,'EDITAR'=>$edi,'ELIMINAR'=>$eli,
                        // mantener sincronizadas las columnas legadas
                        'PER_SELECT'=>$ver,'PER_INSERTAR'=>$cre,'PER_UPDATE'=>$edi,
                    ]
                );
            }
        });

        return back()->with('ok','Permisos actualizados');
    }
}
