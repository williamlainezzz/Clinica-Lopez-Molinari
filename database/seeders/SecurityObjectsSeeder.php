<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityObjectsSeeder extends Seeder
{
    public function run(): void
    {
        $objetos = [
            ['NOM_OBJETO' => 'SEGURIDAD_ROLES',          'DESC_OBJETO' => 'Gestión de roles',                 'URL_OBJETO' => '/seguridad/roles',          'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_PERMISOS',       'DESC_OBJETO' => 'Matriz de permisos',               'URL_OBJETO' => '/seguridad/permisos',       'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_OBJETOS',        'DESC_OBJETO' => 'Catálogo de objetos',              'URL_OBJETO' => '/seguridad/objetos',        'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_BITACORA',       'DESC_OBJETO' => 'Registro de auditoría',            'URL_OBJETO' => '/seguridad/bitacora',       'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'SEGURIDAD_BACKUPS',        'DESC_OBJETO' => 'Gestión de respaldos',             'URL_OBJETO' => '/seguridad/backups',        'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'PERSONAS_DOCTORES',        'DESC_OBJETO' => 'Personas: Doctores',               'URL_OBJETO' => '/personas/doctores',        'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'PERSONAS_PACIENTES',       'DESC_OBJETO' => 'Personas: Pacientes',              'URL_OBJETO' => '/personas/pacientes',       'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'PERSONAS_RECEPCIONISTAS',  'DESC_OBJETO' => 'Personas: Recepcionistas',         'URL_OBJETO' => '/personas/recepcionistas',  'ESTADO_OBJETO' => 1],
            ['NOM_OBJETO' => 'PERSONAS_ADMINISTRADORES', 'DESC_OBJETO' => 'Personas: Administradores',        'URL_OBJETO' => '/personas/administradores', 'ESTADO_OBJETO' => 1],
        ];

        foreach ($objetos as $obj) {
            // Si existe por NOMBRE o por URL, actualiza esa fila; si no, inserta
            $existente = DB::table('tbl_objeto')
                ->where('NOM_OBJETO', $obj['NOM_OBJETO'])
                ->orWhere('URL_OBJETO', $obj['URL_OBJETO'])
                ->first();

            if ($existente) {
                DB::table('tbl_objeto')
                    ->where('COD_OBJETO', $existente->COD_OBJETO)
                    ->update([
                        'NOM_OBJETO'    => $obj['NOM_OBJETO'],
                        'DESC_OBJETO'   => $obj['DESC_OBJETO'],
                        'URL_OBJETO'    => $obj['URL_OBJETO'],
                        'ESTADO_OBJETO' => $obj['ESTADO_OBJETO'],
                    ]);
            } else {
                DB::table('tbl_objeto')->insert($obj);
            }
        }
    }
}
