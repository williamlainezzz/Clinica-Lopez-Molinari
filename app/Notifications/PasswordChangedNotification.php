<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $username,
        private readonly string $newPassword
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $months = (int) config('security.password_expiry_months_label', 2);

        return (new MailMessage)
            ->subject('Confirmación de cambio de contraseña')
            ->greeting('Hola,')
            ->line('Se confirmó correctamente un cambio de contraseña en tu cuenta del sistema clínico.')
            ->line('**Usuario:** ' . $this->username)
            ->line('**Nueva contraseña:** ' . $this->newPassword)
            ->line('Por seguridad, esta contraseña vence en ' . $months . ' meses.')
            ->line('Recomendación: no compartas tu contraseña con nadie y cámbiala inmediatamente si no reconoces este cambio.')
            ->line('Si no reconoces este cambio, comunícate de inmediato con soporte.')
            ->salutation('Clínica Dental López Molinari');
    }
}
