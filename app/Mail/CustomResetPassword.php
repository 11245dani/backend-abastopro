<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $correo;

    public function __construct($url, $correo)
    {
        $this->url = $url;
        $this->correo = $correo;
    }

    public function build()
    {
        return $this->subject('Restablece tu contraseña - Abastopro')
                    ->view('emails.custom-reset-password')
                    ->with([
                        'url' => $this->url,
                        'correo' => $this->correo,
                    ]);
    }
}
