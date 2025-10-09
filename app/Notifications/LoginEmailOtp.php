<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LoginEmailOtp extends Notification
{
    use Queueable;

    public function __construct(
        public string $code,           // Código OTP (ej. 6 dígitos)
        public int $ttlMinutes = 5     // Minutos de validez
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tu código de verificación')
            ->markdown('emails.login-otp', [
                'code'       => $this->code,
                'ttlMinutes' => $this->ttlMinutes,
                'appName'    => config('app.name', 'Clínica Odontológica'),
            ]);
    }
}
