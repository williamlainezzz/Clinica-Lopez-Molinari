<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreguntasSeguridadSeeder extends Seeder
{
    public function run(): void
    {
        $preguntas = [
            '¿Cuál fue el nombre de tu primera mascota?',
            '¿En qué ciudad naciste?',
            '¿Cuál es el segundo apellido de tu madre?',
            '¿Cuál fue tu primer centro educativo?',
        ];

        foreach ($preguntas as $pregunta) {
            DB::table('tbl_pregunta_seguridad')->updateOrInsert(
                ['PREGUNTA' => $pregunta],
                []
            );
        }
    }
}
