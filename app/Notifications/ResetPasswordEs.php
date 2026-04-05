<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordEs extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $token
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $email = $notifiable->getEmailForPasswordReset();
        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Recuperacion de contrasena')
            ->markdown('emails.reset-password', [
                'resetUrl' => route('password.reset', [
                    'token' => $this->token,
                    'email' => $email,
                ]),
                'email' => $email,
                'count' => $expire,
                'appName' => config('mail.from.name', 'Complejo Dental Lopez Molinari'),
            ]);
    }
}
