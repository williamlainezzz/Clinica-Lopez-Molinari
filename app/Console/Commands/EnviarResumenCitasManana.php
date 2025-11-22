<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use App\Notifications\ResumenCitasManana;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnviarResumenCitasManana extends Command
{
    protected $signature = 'citas:enviar-resumen-manana';

    protected $description = 'Envía un resumen de las citas del día siguiente a recepción (y admin).';

    public function handle(): int
    {
        if (!Schema::hasTable('tbl_cita') || !Schema::hasTable('tbl_persona')) {
            $this->warn('No existen las tablas de citas/persona.');
            return self::SUCCESS;
        }

        $manana = Carbon::today()->addDay();
        $fecha  = $manana->toDateString();

        $citasDb = DB::table('tbl_cita as c')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            ->join('tbl_persona as d', 'd.COD_PERSONA', '=', 'c.FK_COD_DOCTOR')
            ->leftJoin('tbl_estado_cita as e', 'e.COD_ESTADO', '=', 'c.ESTADO_CITA')
            ->where('c.FEC_CITA', $fecha)
            ->select([
                'c.COD_CITA',
                'c.FEC_CITA',
                'c.HOR_CITA',
                'c.MOT_CITA',
                'c.OBSERVACIONES',
                'p.PRIMER_NOMBRE as pac_nom',
                'p.PRIMER_APELLIDO as pac_ape',
                'd.PRIMER_NOMBRE as doc_nom',
                'd.PRIMER_APELLIDO as doc_ape',
                'e.NOM_ESTADO as estado_nombre',
            ])
            ->orderBy('c.HOR_CITA')
            ->get();

        if ($citasDb->isEmpty()) {
            $this->info('No hay citas para mañana. No se envía resumen.');
            return self::SUCCESS;
        }

        $normalizeEstado = function ($nombreEstado = null) {
            if (!$nombreEstado) {
                return 'Pendiente';
            }

            $nombreEstado = ucfirst(strtolower(trim($nombreEstado)));
            return str_replace('_', ' ', $nombreEstado);
        };

        $citas = $citasDb->map(function ($cita) use ($normalizeEstado) {
            return [
                'id'       => $cita->COD_CITA,
                'fecha'    => $cita->FEC_CITA,
                'hora'     => substr((string) $cita->HOR_CITA, 0, 5),
                'motivo'   => $cita->MOT_CITA,
                'nota'     => $cita->OBSERVACIONES,
                'paciente' => trim($cita->pac_nom . ' ' . $cita->pac_ape),
                'doctor'   => trim($cita->doc_nom . ' ' . $cita->doc_ape),
                'estado'   => $normalizeEstado($cita->estado_nombre),
            ];
        })->values()->all();

        if (!Schema::hasTable('tbl_usuario') || !Schema::hasTable('tbl_rol')) {
            $this->warn('No existen las tablas de usuario/rol para enviar el resumen.');
            return self::SUCCESS;
        }

        $usuarioIds = DB::table('tbl_usuario as u')
            ->join('tbl_rol as r', 'r.COD_ROL', '=', 'u.FK_COD_ROL')
            ->whereIn('r.NOM_ROL', ['RECEPCIONISTA', 'ADMIN'])
            ->pluck('u.COD_USUARIO')
            ->all();

        if (empty($usuarioIds)) {
            $this->info('No hay usuarios de recepción/admin para enviar resumen.');
            return self::SUCCESS;
        }

        $usuarios = Usuario::whereIn('COD_USUARIO', $usuarioIds)->get();

        if ($usuarios->isEmpty()) {
            $this->info('No se encontraron modelos Usuario para los IDs obtenidos.');
            return self::SUCCESS;
        }

        $clinica = config('app.name', 'Clínica');
        $payload = [
            'subject'       => "{$clinica} - Citas de mañana ({$manana->format('d/m/Y')})",
            'clinica'       => $clinica,
            'fecha'         => $fecha,
            'fecha_legible' => $manana->isoFormat('D [de] MMMM YYYY'),
            'citas'         => $citas,
            'intro'         => 'Resumen de pacientes y doctores programados para mañana.',
        ];

        foreach ($usuarios as $usuario) {
            $usuario->notify(new ResumenCitasManana($payload));
        }

        $this->info('Resumen de citas de mañana enviado correctamente.');

        return self::SUCCESS;
    }
}
