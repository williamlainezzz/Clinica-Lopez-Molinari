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

        $securityObjetos = [
            'SEGURIDAD_ROLES',
            'SEGURIDAD_PERMISOS',
            'SEGURIDAD_OBJETOS',
            'SEGURIDAD_BITACORA',
            'SEGURIDAD_BACKUPS',
            'SEGURIDAD_USUARIOS',
        ];

        $personaObjetos = [
            'PERSONAS_DOCTORES',
            'PERSONAS_PACIENTES',
            'PERSONAS_RECEPCIONISTAS',
            'PERSONAS_ADMINISTRADORES',
        ];

        // Objetos creados en el seeder anterior
        $objetos = DB::table('tbl_objeto')
            ->whereIn('NOM_OBJETO', array_merge($securityObjetos, $personaObjetos))
            ->pluck('COD_OBJETO','NOM_OBJETO')
            ->all();

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

        // ADMIN: todo 1 en cada objeto del catálogo
        if ($adminId) {
            foreach ($objetos as $nom => $objId) {
                $upsertPerm($adminId, $objId, 1,1,1,1);
            }
        }

        // Seguridad: resto de roles quedan en 0 por defecto
        foreach ([$recepId, $doctorId, $pacId] as $rolId) {
            if (!$rolId) continue;
            foreach ($securityObjetos as $nom) {
                $objId = $objetos[$nom] ?? null;
                if (!$objId) continue;
                $upsertPerm($rolId, $objId, 0,0,0,0);
            }
        }

        // Personas: recepcionista ve doctores/pacientes por defecto (editable en Seguridad → Permisos)
        if ($recepId) {
            foreach ($personaObjetos as $nom) {
                $objId = $objetos[$nom] ?? null;
                if (!$objId) continue;
                $ver = in_array($nom, ['PERSONAS_DOCTORES','PERSONAS_PACIENTES'], true) ? 1 : 0;
                $upsertPerm($recepId, $objId, $ver,0,0,0);
            }
        }

        // Personas: doctores y pacientes sin acceso inicial
        foreach ([$doctorId, $pacId] as $rolId) {
            if (!$rolId) continue;
            foreach ($personaObjetos as $nom) {
                $objId = $objetos[$nom] ?? null;
                if (!$objId) continue;
                $upsertPerm($rolId, $objId, 0,0,0,0);
            }
        }
    }
}
