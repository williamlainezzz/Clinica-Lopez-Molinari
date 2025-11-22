<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerarAlertasRecepcionCitas extends Command
{
    protected $signature = 'citas:generar-alertas-recepcion';

    protected $description = 'Genera notificaciones internas para recepción 30 minutos antes de cada cita.';

    public function handle(): int
    {
        if (!Schema::hasTable('tbl_cita') || !Schema::hasTable('tbl_notificacion')) {
            $this->warn('No existen las tablas de citas o notificaciones.');
            return self::SUCCESS;
        }

        $now    = Carbon::now();
        $inicio = $now->copy()->addMinutes(25);
        $fin    = $now->copy()->addMinutes(35);

        $timestampExpr = DB::raw("TIMESTAMP(c.FEC_CITA, c.HOR_CITA)");

        $citas = DB::table('tbl_cita as c')
            ->join('tbl_persona as p', 'p.COD_PERSONA', '=', 'c.FK_COD_PACIENTE')
            ->join('tbl_persona as d', 'd.COD_PERSONA', '=', 'c.FK_COD_DOCTOR')
            ->select([
                'c.COD_CITA',
                'c.FEC_CITA',
                'c.HOR_CITA',
                'c.MOT_CITA',
                'p.PRIMER_NOMBRE as pac_nom',
                'p.PRIMER_APELLIDO as pac_ape',
                'd.PRIMER_NOMBRE as doc_nom',
                'd.PRIMER_APELLIDO as doc_ape',
            ])
            ->whereBetween($timestampExpr, [$inicio, $fin])
            ->orderBy('c.FEC_CITA')
            ->orderBy('c.HOR_CITA')
            ->get();

        if ($citas->isEmpty()) {
            $this->info('No hay citas en la ventana de 30 minutos.');
            return self::SUCCESS;
        }

        foreach ($citas as $cita) {
            $paciente = trim($cita->pac_nom . ' ' . $cita->pac_ape);
            $doctor   = trim($cita->doc_nom . ' ' . $cita->doc_ape);
            $hora     = substr((string) $cita->HOR_CITA, 0, 5);

            // Evitar duplicados: buscamos una notificación MANUAL con este patrón
            $existe = DB::table('tbl_notificacion')
                ->where('FK_COD_CITA', $cita->COD_CITA)
                ->where('TIPO_NOTIFICACION', 'MANUAL')
                ->where('MSG_NOTIFICACION', 'like', '[Recepción 30m] %')
                ->exists();

            if ($existe) {
                continue;
            }

            $mensaje = sprintf(
                '[Recepción 30m] En 30 minutos llegará %s con %s a las %s.',
                $paciente,
                $doctor,
                $hora
            );

            try {
                Notificacion::create([
                    'FK_COD_CITA'       => $cita->COD_CITA,
                    'MSG_NOTIFICACION'  => $mensaje,
                    'FEC_ENVIO'         => now(),
                    'TIPO_NOTIFICACION' => 'MANUAL',
                    'LEIDA'             => 0, // se verá en la campana
                ]);
            } catch (\Throwable $e) {
                // no interrumpir por errores individuales
            }
        }

        $this->info('Alertas de recepción generadas correctamente.');

        return self::SUCCESS;
    }
}
