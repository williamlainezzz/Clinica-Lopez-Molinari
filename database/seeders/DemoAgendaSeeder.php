<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoAgendaSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('tbl_persona')->count() > 0) {
            return;
        }

        $roles = DB::table('tbl_rol')->pluck('COD_ROL', 'NOM_ROL');

        $personas = [];

        $personas['admin'] = $this->createPersona([
            'PRIMER_NOMBRE' => 'Wendy',
            'SEGUNDO_NOMBRE' => 'Marisol',
            'PRIMER_APELLIDO' => 'Solis',
            'SEGUNDO_APELLIDO' => 'Lopez',
            'TIPO_GENERO' => 2,
            'correo' => 'admin@clinica.test',
            'telefono' => '+50499998888',
            'rol' => $roles['ADMIN'] ?? null,
            'usuario' => 'admin',
        ]);

        $doctores = [
            [
                'PRIMER_NOMBRE' => 'Juan',
                'PRIMER_APELLIDO' => 'Lopez',
                'SEGUNDO_APELLIDO' => 'Molina',
                'TIPO_GENERO' => 1,
                'ESPECIALIDAD' => 'Ortodoncia',
                'correo' => 'juan.lopez@clinica.test',
                'telefono' => '+50488887777',
            ],
            [
                'PRIMER_NOMBRE' => 'Maria',
                'PRIMER_APELLIDO' => 'Castillo',
                'SEGUNDO_APELLIDO' => 'Rivas',
                'TIPO_GENERO' => 2,
                'ESPECIALIDAD' => 'Odontología general',
                'correo' => 'maria.castillo@clinica.test',
                'telefono' => '+50482226666',
            ],
        ];

        $doctorIds = [];
        foreach ($doctores as $index => $doctor) {
            $doctorIds[$index] = $this->createPersona(array_merge($doctor, [
                'rol' => $roles['DOCTOR'] ?? null,
                'usuario' => Str::slug($doctor['PRIMER_NOMBRE'] . '.' . $doctor['PRIMER_APELLIDO']),
            ]));
        }

        $pacientes = [
            ['PRIMER_NOMBRE' => 'Ana',    'PRIMER_APELLIDO' => 'Rivera',  'SEGUNDO_APELLIDO' => 'Lopez',   'TIPO_GENERO' => 2, 'correo' => 'ana.rivera@correo.test',    'telefono' => '+50499990001'],
            ['PRIMER_NOMBRE' => 'Carlos', 'PRIMER_APELLIDO' => 'Perez',   'SEGUNDO_APELLIDO' => 'Gomez',   'TIPO_GENERO' => 1, 'correo' => 'carlos.perez@correo.test',  'telefono' => '+50499990002'],
            ['PRIMER_NOMBRE' => 'Lucia',  'PRIMER_APELLIDO' => 'Romero',  'SEGUNDO_APELLIDO' => 'Diaz',    'TIPO_GENERO' => 2, 'correo' => 'lucia.romero@correo.test',  'telefono' => '+50499990003'],
            ['PRIMER_NOMBRE' => 'Marcos', 'PRIMER_APELLIDO' => 'Diaz',    'SEGUNDO_APELLIDO' => 'Flores',  'TIPO_GENERO' => 1, 'correo' => 'marcos.diaz@correo.test',   'telefono' => '+50499990004'],
        ];

        $pacienteIds = [];
        foreach ($pacientes as $index => $paciente) {
            $pacienteIds[$index] = $this->createPersona(array_merge($paciente, [
                'rol' => $roles['PACIENTE'] ?? null,
                'usuario' => Str::slug($paciente['PRIMER_NOMBRE'] . '.' . $paciente['PRIMER_APELLIDO']),
            ]));
        }

        // crear recepcionista demo
        $this->createPersona([
            'PRIMER_NOMBRE' => 'Laura',
            'PRIMER_APELLIDO' => 'Mejia',
            'SEGUNDO_APELLIDO' => 'Aguilar',
            'TIPO_GENERO' => 2,
            'correo' => 'recepcion@clinica.test',
            'telefono' => '+50496665555',
            'rol' => $roles['RECEPCIONISTA'] ?? null,
            'usuario' => 'recepcion',
        ]);

        $now = Carbon::now('America/Tegucigalpa');

        $citas = [
            [
                'doctor' => $doctorIds[0],
                'paciente' => $pacienteIds[0],
                'fecha' => $now->copy()->addDays(1),
                'hora' => '09:00:00',
                'estado' => 'CONFIRMADA',
                'motivo' => 'Ajuste de ortodoncia',
                'ubicacion' => 'Consultorio 1',
                'nota' => 'Paciente confirmó asistencia vía correo.',
                'canal' => 'WEB',
            ],
            [
                'doctor' => $doctorIds[0],
                'paciente' => $pacienteIds[1],
                'fecha' => $now->copy()->addDays(2),
                'hora' => '11:30:00',
                'estado' => 'PENDIENTE',
                'motivo' => 'Valoración general',
                'ubicacion' => 'Consultorio 2',
                'nota' => 'Confirmar estudios previos.',
                'canal' => 'TELÉFONO',
            ],
            [
                'doctor' => $doctorIds[1],
                'paciente' => $pacienteIds[2],
                'fecha' => $now->copy()->addDays(3),
                'hora' => '15:00:00',
                'estado' => 'CONFIRMADA',
                'motivo' => 'Limpieza preventiva',
                'ubicacion' => 'Consultorio 3',
                'nota' => 'Paciente solicita recordatorio SMS.',
                'canal' => 'APP',
            ],
            [
                'doctor' => $doctorIds[1],
                'paciente' => $pacienteIds[0],
                'fecha' => $now->copy()->subDays(5),
                'hora' => '08:00:00',
                'estado' => 'COMPLETADA',
                'motivo' => 'Control mensual',
                'ubicacion' => 'Consultorio 3',
                'nota' => 'Evolución favorable.',
                'canal' => 'WEB',
            ],
            [
                'doctor' => null,
                'paciente' => $pacienteIds[3],
                'fecha' => $now->copy()->addDays(4),
                'hora' => '10:30:00',
                'estado' => 'SOLICITADA',
                'motivo' => 'Dolor agudo en molar',
                'ubicacion' => 'Por asignar',
                'nota' => 'Pendiente asignar doctor.',
                'canal' => 'WHATSAPP',
            ],
        ];

        foreach ($citas as $cita) {
            DB::table('tbl_cita')->insert([
                'FK_COD_DOCTOR' => $cita['doctor'],
                'FK_COD_PACIENTE' => $cita['paciente'],
                'FEC_CITA' => $cita['fecha']->toDateString(),
                'HORA_CITA' => $cita['hora'],
                'ESTADO_CITA' => $cita['estado'],
                'MOTIVO_CITA' => $cita['motivo'],
                'UBICACION' => $cita['ubicacion'],
                'DURACION_MINUTOS' => 45,
                'CANAL' => $cita['canal'],
                'NOTAS_CITA' => $cita['nota'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createPersona(array $data): int
    {
        $personaId = DB::table('tbl_persona')->insertGetId([
            'PRIMER_NOMBRE' => $data['PRIMER_NOMBRE'],
            'SEGUNDO_NOMBRE' => $data['SEGUNDO_NOMBRE'] ?? null,
            'PRIMER_APELLIDO' => $data['PRIMER_APELLIDO'],
            'SEGUNDO_APELLIDO' => $data['SEGUNDO_APELLIDO'] ?? null,
            'TIPO_GENERO' => $data['TIPO_GENERO'] ?? 0,
            'ESPECIALIDAD' => $data['ESPECIALIDAD'] ?? null,
            'OCUPACION' => $data['OCUPACION'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!empty($data['correo'])) {
            DB::table('tbl_correo')->insert([
                'FK_COD_PERSONA' => $personaId,
                'CORREO' => $data['correo'],
                'TIPO_CORREO' => 'PRINCIPAL',
                'ES_PRINCIPAL' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!empty($data['telefono'])) {
            DB::table('tbl_telefono')->insert([
                'FK_COD_PERSONA' => $personaId,
                'NUM_TELEFONO' => preg_replace('/\s+/', '', $data['telefono']),
                'TIPO_TELEFONO' => 'MOVIL',
                'ES_PRINCIPAL' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!empty($data['rol'])) {
            DB::table('tbl_usuario')->insert([
                'USR_USUARIO' => $data['usuario'] ?? Str::lower($data['PRIMER_NOMBRE']),
                'PWD_USUARIO' => Hash::make('password'),
                'FK_COD_PERSONA' => $personaId,
                'FK_COD_ROL' => $data['rol'],
                'ESTADO_USUARIO' => 1,
            ]);
        }

        return $personaId;
    }
}
