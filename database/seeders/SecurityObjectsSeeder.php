<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityObjectsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $objetos = [
            ['NOM_OBJETO' => 'SEGURIDAD_ROLES',     'DESC_OBJETO' => 'Gestión de roles',        'URL_OBJETO' => '/seguridad/roles',     'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_PERMISOS',  'DESC_OBJETO' => 'Matriz de permisos',      'URL_OBJETO' => '/seguridad/permisos',  'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_OBJETOS',   'DESC_OBJETO' => 'Catálogo de objetos',     'URL_OBJETO' => '/seguridad/objetos',   'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_BITACORA',  'DESC_OBJETO' => 'Registro de auditoría',   'URL_OBJETO' => '/seguridad/bitacora',  'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_BACKUPS',   'DESC_OBJETO' => 'Gestión de respaldos',    'URL_OBJETO' => '/seguridad/backups',   'ESTADO_OBJETO' => 1],
        ];

        foreach ($objetos as $obj) {
            DB::table('tbl_objeto')->updateOrInsert(
                ['NOM_OBJETO' => $obj['NOM_OBJETO']],
                [
                    'DESC_OBJETO'  => $obj['DESC_OBJETO'],
                    'URL_OBJETO'   => $obj['URL_OBJETO'],
                    'ESTADO_OBJETO'=> $obj['ESTADO_OBJETO'],
                    'UPDATED_AT'   => $now,
                ]
            );
        }
    }
}
