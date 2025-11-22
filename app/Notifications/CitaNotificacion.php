<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CitaNotificacion extends Notification
{
    use Queueable;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->payload['subject'] ?? 'Notificación de cita')
            ->markdown('emails.cita-notificacion', $this->payload);
    }
}
