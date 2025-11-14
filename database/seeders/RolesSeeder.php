<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['NOM_ROL' => 'ADMIN',         'DESC_ROL' => 'Administrador del sistema'],
            ['NOM_ROL' => 'DOCTOR',        'DESC_ROL' => 'Doctor tratante'],
            ['NOM_ROL' => 'RECEPCIONISTA', 'DESC_ROL' => 'Equipo de recepción'],
            ['NOM_ROL' => 'PACIENTE',      'DESC_ROL' => 'Pacientes registrados'],
        ];

        foreach ($roles as $rol) {
            DB::table('tbl_rol')->updateOrInsert(
                ['NOM_ROL' => $rol['NOM_ROL']],
                [
                    'DESC_ROL' => $rol['DESC_ROL'],
                    'ESTADO_ROL' => 1,
                ]
            );
        }
    }
}
