<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('tbl_rol')
            ->select('COD_ROL', 'NOM_ROL')
            ->get()
            ->mapWithKeys(fn ($rol) => [strtoupper(trim($rol->NOM_ROL)) => (int) $rol->COD_ROL]);

        $objetosPersonaSeguridad = [
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
            ->whereIn('NOM_OBJETO', $objetosPersonaSeguridad)
            ->pluck('COD_OBJETO', 'NOM_OBJETO');

        $defaultGrant = fn (int $rolId, int $objId, array $flags) => DB::table('tbl_permiso')->updateOrInsert(
            ['FK_COD_ROL' => $rolId, 'FK_COD_OBJETO' => $objId],
            [
                'ESTADO_PERMISO' => 1,
                'VER'            => $flags[0] ?? 0,
                'CREAR'          => $flags[1] ?? 0,
                'EDITAR'         => $flags[2] ?? 0,
                'ELIMINAR'       => $flags[3] ?? 0,
            ]
        );

        foreach ($roles as $rolNombre => $rolId) {
            foreach ($objetos as $nomObjeto => $objId) {
                $defaultFlags = $rolNombre === 'ADMIN' ? [1, 1, 1, 1] : [0, 0, 0, 0];
                $defaultGrant($rolId, $objId, $defaultFlags);
            }
        }

        $overrides = [
            'PERSONAS_DOCTORES' => [
                'RECEPCIONISTA' => [1, 0, 0, 0],
            ],
            'PERSONAS_PACIENTES' => [
                'RECEPCIONISTA' => [1, 0, 0, 0],
            ],
        ];

        foreach ($overrides as $objeto => $rolesConfig) {
            $objId = $objetos[$objeto] ?? null;
            if (!$objId) {
                continue;
            }

            foreach ($rolesConfig as $rolNombre => $flags) {
                $rolId = $roles[$rolNombre] ?? null;
                if (!$rolId) {
                    continue;
                }

                $defaultGrant($rolId, $objId, $flags);
            }
        }
    }
}
