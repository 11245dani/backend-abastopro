<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;

    /**
     * Crea una nueva instancia del correo.
     *
     * @param mixed $usuario
     */
    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Construye el correo.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Verifica tu correo')
                    ->view('emails.verify', ['usuario' => $this->usuario])
                    ->with(['usuario' => $this->usuario]);
    }
}

