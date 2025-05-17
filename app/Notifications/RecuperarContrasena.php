<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecuperarContrasena extends Notification
{
    use Queueable;

    protected $token;
    protected $correo;

    public function __construct($token, $correo)
    {
        $this->token = $token;
        $this->correo = $correo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // CAMBIO CLAVE: usar "email" en el query string
        $url = url('/reset-password/' . $this->token . '?email=' . urlencode($this->correo));

        return (new MailMessage)
            ->subject('Recuperación de contraseña')
            ->greeting('Hola')
            ->line('Recibimos una solicitud para restablecer tu contraseña.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este mensaje.');
    }

    public function toArray($notifiable)
    {
        return [];
    }
}