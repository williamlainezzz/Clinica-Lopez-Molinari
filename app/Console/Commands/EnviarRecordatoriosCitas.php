<?php

namespace App\Console\Commands;

use App\Services\NotificacionCitaService;
use Illuminate\Console\Command;

class EnviarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:enviar-recordatorios';

    protected $description = 'Envía recordatorios de citas (24h y 1h antes)';

    public function handle(NotificacionCitaService $service): int
    {
        $tipos = ['RECORDATORIO_24H', 'RECORDATORIO_1H'];

        foreach ($tipos as $tipo) {
            $citas = $service->citasParaRecordatorio($tipo);

            foreach ($citas as $cita) {
                if ($tipo === 'RECORDATORIO_24H') {
                    $service->enviarRecordatorio24H($cita->COD_CITA);
                } else {
                    $service->enviarRecordatorio1H($cita->COD_CITA);
                }
            }
        }

        $this->info('Recordatorios de citas procesados.');

        return self::SUCCESS;
    }
}
