<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCambioRol extends Model
{
    protected $table = 'solicitudes_cambio_rol';

    protected $fillable = [
        'usuario_id',
        'rol_solicitado',
        'nombre_empresa',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
