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
            ->subject('Recordatorio de caducidad de contrasena')
            ->markdown('emails.password-expiry-reminder', [
                'username' => $this->username,
                'days' => $days,
                'profileUrl' => url('/usuario/perfil'),
                'appName' => config('mail.from.name', 'Complejo Dental Lopez Molinari'),
            ]);
    }
}
