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

        $matrix = [
            'SEGURIDAD_ROLES' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [0, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'SEGURIDAD_PERMISOS' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [0, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'SEGURIDAD_OBJETOS' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [0, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'SEGURIDAD_BITACORA' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [0, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'SEGURIDAD_BACKUPS' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [0, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'PERSONAS_DOCTORES' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [1, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'PERSONAS_PACIENTES' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [1, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'PERSONAS_RECEPCIONISTAS' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [0, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
            'PERSONAS_ADMINISTRADORES' => [
                'ADMIN'         => [1, 1, 1, 1],
                'RECEPCIONISTA' => [0, 0, 0, 0],
                'DOCTOR'        => [0, 0, 0, 0],
                'PACIENTE'      => [0, 0, 0, 0],
            ],
        ];

        $objetos = DB::table('tbl_objeto')
            ->whereIn('NOM_OBJETO', array_keys($matrix))
            ->pluck('COD_OBJETO', 'NOM_OBJETO');

        foreach ($matrix as $objeto => $rolesConfig) {
            $objId = $objetos[$objeto] ?? null;
            if (!$objId) {
                continue;
            }

            foreach ($roles as $rolNombre => $rolId) {
                $valores = $rolesConfig[$rolNombre] ?? [0, 0, 0, 0];
                [$ver, $crear, $editar, $eliminar] = $valores;

                DB::table('tbl_permiso')->updateOrInsert(
                    ['FK_COD_ROL' => $rolId, 'FK_COD_OBJETO' => $objId],
                    [
                        'ESTADO_PERMISO' => 1,
                        'VER'            => $ver,
                        'CREAR'          => $crear,
                        'EDITAR'         => $editar,
                        'ELIMINAR'       => $eliminar,
                    ]
                );
            }
        }
    }
}
