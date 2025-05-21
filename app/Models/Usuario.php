<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Notifications\RecuperarContrasena;
use App\Models\Tendero;


class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre', 'correo', 'password', 'rol', 'estado', 'direccion', 'nombre_empresa', 'verification_token'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    public function routeNotificationForMail()
    {
        return $this->correo;
    }

    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    public function sendPasswordResetNotification($token)
{
    $this->notify(new RecuperarContrasena($token, $this->correo));
}

// Usuario.php
public function tendero()
{
    return $this->hasOne(Tendero::class);
}    

public function getAuthIdentifierName()
{
    return 'correo';
}



public function tienda()
{
    return $this->hasOne(Tienda::class);
}

public function distribuidor()
{
    return $this->hasOne(Distribuidor::class);
}

}





