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
            ->subject('Confirmacion de cambio de contrasena')
            ->markdown('emails.password-changed', [
                'username' => $this->username,
                'newPassword' => $this->newPassword,
                'months' => $months,
                'appName' => config('mail.from.name', 'Complejo Dental Lopez Molinari'),
            ]);
    }
}
