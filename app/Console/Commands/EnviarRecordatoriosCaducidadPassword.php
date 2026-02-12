<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use App\Notifications\PasswordExpiryReminderNotification;
use App\Support\PasswordSecurityService;
use Illuminate\Console\Command;

class EnviarRecordatoriosCaducidadPassword extends Command
{
    protected $signature = 'seguridad:recordar-caducidad-password';
    protected $description = 'Envía recordatorios de próxima caducidad de contraseña (si existen columnas de control).';

    public function handle(PasswordSecurityService $passwordSecurityService): int
    {
        if (!$passwordSecurityService->passwordMetadataColumnsExist()) {
            $this->info('Columnas de caducidad no disponibles. Se omite sin error.');
            return self::SUCCESS;
        }

        $usuarios = Usuario::query()->where('ESTADO_USUARIO', 1)->get();

        foreach ($usuarios as $usuario) {
            if (!$passwordSecurityService->shouldSendReminder($usuario)) {
                continue;
            }

            $usuario->notify(new PasswordExpiryReminderNotification($usuario->USR_USUARIO));

            $passwordSecurityService->markReminderSent((int) $usuario->COD_USUARIO);
        }

        $this->info('Proceso finalizado.');

        return self::SUCCESS;
    }
}
