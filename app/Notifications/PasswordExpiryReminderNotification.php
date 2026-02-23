<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordExpiryReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $username)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days = (int) config('security.password_reminder_days', 15);

        return (new MailMessage)
            ->subject('Recordatorio de caducidad de contraseña')
            ->greeting('Hola,')
            ->line('Tu contraseña del sistema clínico está próxima a vencer.')
            ->line('**Usuario:** ' . $this->username)
            ->line('Te recomendamos cambiarla antes de los próximos ' . $days . ' días.')
            ->line('Ingresa al sistema para renovarla antes del vencimiento.')
            ->action('Ir al sistema', url('/usuario/perfil'))
            ->salutation('Clínica Dental López Molinari');
    }
}
