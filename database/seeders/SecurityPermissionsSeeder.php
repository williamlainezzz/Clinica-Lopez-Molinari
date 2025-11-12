<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('tbl_rol')
            ->pluck('COD_ROL', 'NOM_ROL')
            ->mapWithKeys(fn ($id, $name) => [strtoupper(trim($name)) => (int) $id]);

        $adminId  = $roles['ADMIN'] ?? null;
        $recepId  = $roles['RECEPCIONISTA'] ?? null;
        $doctorId = $roles['DOCTOR'] ?? null;
        $pacId    = $roles['PACIENTE'] ?? null;

        $objetosNombres = [
            'SEGURIDAD_ROLES',
            'SEGURIDAD_PERMISOS',
            'SEGURIDAD_OBJETOS',
            'SEGURIDAD_BITACORA',
            'SEGURIDAD_BACKUPS',
            'PERSONAS_DOCTORES',
            'PERSONAS_PACIENTES',
            'PERSONAS_RECEPCIONISTAS',
            'PERSONAS_ADMINISTRADORES',
        ];

        $objetos = DB::table('tbl_objeto')
            ->whereIn('NOM_OBJETO', $objetosNombres)
            ->pluck('COD_OBJETO', 'NOM_OBJETO');

        $upsertPerm = function (?int $rolId, ?int $objId, int $ver, int $crear, int $editar, int $eliminar): void {
            if (!$rolId || !$objId) {
                return;
            }

            DB::table('tbl_permiso')->updateOrInsert(
                ['FK_COD_ROL' => $rolId, 'FK_COD_OBJETO' => $objId],
                [
                    'ESTADO_PERMISO' => 1,
                    'VER'      => $ver,
                    'CREAR'    => $crear,
                    'EDITAR'   => $editar,
                    'ELIMINAR' => $eliminar,
                ]
            );
        };

        if ($adminId) {
            foreach ($objetos as $objId) {
                $upsertPerm($adminId, $objId, 1, 1, 1, 1);
            }
        }

        $otrosRoles = array_filter([
            $recepId,
            $doctorId,
            $pacId,
        ]);

        foreach ($otrosRoles as $rolId) {
            foreach ($objetos as $objId) {
                $upsertPerm($rolId, $objId, 0, 0, 0, 0);
            }
        }

        $recepcionistaOverrides = [
            'PERSONAS_DOCTORES',
            'PERSONAS_PACIENTES',
        ];

        if ($recepId) {
            foreach ($recepcionistaOverrides as $objNombre) {
                $objId = $objetos[$objNombre] ?? null;
                $upsertPerm($recepId, $objId, 1, 0, 0, 0);
            }
        }
    }
}
