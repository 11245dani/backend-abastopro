<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Usuario;

class GestorDespachoAprobado extends Notification
{
    use Queueable;

      protected $usuario;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
        public function __construct(Usuario $usuario)
        {
        $this->usuario = $usuario;
     }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('¡Tu cuenta ha sido aprobada!')
                    ->greeting('¡Hola! ' . $this->usuario->nombre . '!')
                    ->line('Tu cuenta como Distribuidor ha sido aprobada con éxito.')
                    ->action('Acceder a tu cuenta', url('/')) // Modifica la URL según corresponda
                    ->line('¡Gracias por unirte a nuestro sistema!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'usuario_id' => $this->usuario->id,
            'mensaje' => 'Tu cuenta como Gestor de Despacho ha sido aprobada.',
        ];
    }
}
