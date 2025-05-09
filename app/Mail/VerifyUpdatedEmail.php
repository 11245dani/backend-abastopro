<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;

class VerifyUpdatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function build()
    {
        $url = url('/verificar-correo/' . $this->usuario->verification_token);

        return $this->subject('Confirma tu nuevo correo electrónico')
                    ->view('emails.verify_updated_email')
                    ->with([
                        'nombre' => $this->usuario->nombre,
                        'url' => $url,
                    ]);
    }
}
