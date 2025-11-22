<?php

namespace App\Console;

use App\Console\Commands\EnviarRecordatoriosCitas;
use App\Console\Commands\EnviarResumenCitasManana;
use App\Console\Commands\GenerarAlertasRecepcionCitas;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Comandos de consola registrados manualmente.
     */
    protected $commands = [
        EnviarRecordatoriosCitas::class,
        EnviarResumenCitasManana::class,
        GenerarAlertasRecepcionCitas::class,
    ];

    /**
     * Define la programación de las tareas (scheduler).
     */
    protected function schedule(Schedule $schedule): void
    {
        // Recordatorios de citas (24h y 1h antes)
        $schedule->command('citas:enviar-recordatorios')->everyFiveMinutes();

        // Resumen de citas de mañana para recepción/admin (ajusta la hora si quieres)
        $schedule->command('citas:enviar-resumen-manana')->dailyAt('19:00');

        // Alertas internas para recepción 30 minutos antes de cada cita
        $schedule->command('citas:generar-alertas-recepcion')->everyFiveMinutes();
    }

    /**
     * Registra los comandos de la aplicación.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
