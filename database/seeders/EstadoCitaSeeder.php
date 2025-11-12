<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoCitaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = ['PENDIENTE','CONFIRMADA','EN_CURSO','COMPLETADA','CANCELADA','NO_SHOW'];
        foreach ($estados as $e) {
            DB::table('tbl_estado_cita')->updateOrInsert(['NOM_ESTADO' => $e], []);
        }
    }
}
