<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Busca IDs de roles por nombre. Ajusta los nombres si tu tabla usa otros.
        $roles = DB::table('tbl_rol')->pluck('COD_ROL','NOM_ROL'); // ['ADMIN'=>1, 'DOCTOR'=>2, ...]

        $adminId  = $roles['ADMIN'] ?? null;
        $recepId  = $roles['RECEPCIONISTA'] ?? null;
        $doctorId = $roles['DOCTOR'] ?? null;
        $pacId    = $roles['PACIENTE'] ?? null;

        // Objetos creados en el seeder anterior
        $objetos = DB::table('tbl_objeto')
            ->whereIn('NOM_OBJETO', [
                'SEGURIDAD_ROLES','SEGURIDAD_PERMISOS','SEGURIDAD_OBJETOS','SEGURIDAD_BITACORA','SEGURIDAD_BACKUPS'
            ])->pluck('COD_OBJETO','NOM_OBJETO');

        // Helper para upsert permiso
        $upsertPerm = function($rolId, $objId, $ver, $crear, $editar, $eliminar){
            if (!$rolId || !$objId) return;
            DB::table('tbl_permiso')->updateOrInsert(
                ['FK_COD_ROL' => $rolId, 'FK_COD_OBJETO' => $objId],
                [
                    'ESTADO_PERMISO' => 1,
                    'VER' => $ver, 'CREAR' => $crear, 'EDITAR' => $editar, 'ELIMINAR' => $eliminar
                ]
            );
        };

        // ADMIN: todo 1
        if ($adminId) {
            foreach ($objetos as $nom => $objId) {
                $upsertPerm($adminId, $objId, 1,1,1,1);
            }
        }

        // Resto de roles: todo 0
        foreach ([$recepId, $doctorId, $pacId] as $rolId) {
            if (!$rolId) continue;
            foreach ($objetos as $nom => $objId) {
                $upsertPerm($rolId, $objId, 0,0,0,0);
            }
        }
    }
}
