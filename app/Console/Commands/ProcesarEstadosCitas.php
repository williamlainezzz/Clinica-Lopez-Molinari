<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProcesarEstadosCitas extends Command
{
    protected $signature = 'citas:procesar-estados';

    protected $description = 'Actualiza citas a EN_CURSO, COMPLETADA o NO_SHOW segun su horario.';

    public function handle(): int
    {
        if (!Schema::hasTable('tbl_cita') || !Schema::hasTable('tbl_estado_cita')) {
            $this->warn('No existen las tablas necesarias para procesar estados de citas.');
            return self::SUCCESS;
        }

        $estadoIds = DB::table('tbl_estado_cita')
            ->pluck('COD_ESTADO', 'NOM_ESTADO')
            ->mapWithKeys(fn ($id, $nombre) => [strtoupper(trim((string) $nombre)) => (int) $id])
            ->all();

        $pendienteId  = $estadoIds['PENDIENTE'] ?? null;
        $confirmadaId = $estadoIds['CONFIRMADA'] ?? null;
        $enCursoId    = $estadoIds['EN_CURSO'] ?? null;
        $completadaId = $estadoIds['COMPLETADA'] ?? null;
        $noShowId     = $estadoIds['NO_SHOW'] ?? null;

        if (!$pendienteId || !$confirmadaId || !$enCursoId || !$completadaId || !$noShowId) {
            $this->warn('No se encontraron todos los estados requeridos para el procesamiento automatico.');
            return self::SUCCESS;
        }

        $citas = DB::table('tbl_cita as c')
            ->join('tbl_estado_cita as e', 'e.COD_ESTADO', '=', 'c.ESTADO_CITA')
            ->whereIn('c.ESTADO_CITA', [$pendienteId, $confirmadaId, $enCursoId])
            ->select([
                'c.COD_CITA',
                'c.HOR_CITA',
                'c.HOR_FIN',
                'c.FEC_CITA',
                'c.ESTADO_CITA',
                'e.NOM_ESTADO as estado_nombre',
            ])
            ->get();

        $now = now();
        $actualizadas = 0;

        foreach ($citas as $cita) {
            try {
                $inicio = Carbon::parse(sprintf('%s %s', $cita->FEC_CITA, $cita->HOR_CITA));
                $fin = $cita->HOR_FIN
                    ? Carbon::parse(sprintf('%s %s', $cita->FEC_CITA, $cita->HOR_FIN))
                    : $inicio->copy()->addMinutes(30);
            } catch (\Throwable $e) {
                continue;
            }

            $estadoActual = strtoupper(trim((string) $cita->estado_nombre));
            $nuevoEstadoId = null;

            if (in_array($estadoActual, ['PENDIENTE', 'CONFIRMADA'], true) && $now->gte($inicio) && $now->lt($fin)) {
                $nuevoEstadoId = $enCursoId;
            } elseif ($estadoActual === 'EN_CURSO' && $now->gte($fin)) {
                $nuevoEstadoId = $completadaId;
            } elseif (in_array($estadoActual, ['PENDIENTE', 'CONFIRMADA'], true) && $now->gte($fin)) {
                $nuevoEstadoId = $noShowId;
            }

            if (!$nuevoEstadoId || (int) $cita->ESTADO_CITA === $nuevoEstadoId) {
                continue;
            }

            $actualizadas += DB::table('tbl_cita')
                ->where('COD_CITA', $cita->COD_CITA)
                ->update([
                    'ESTADO_CITA' => $nuevoEstadoId,
                    'USUARIO_MOD' => null,
                ]);
        }

        $this->info("Citas actualizadas automaticamente: {$actualizadas}");

        return self::SUCCESS;
    }
}
