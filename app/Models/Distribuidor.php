<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distribuidor extends Model
{
    use HasFactory;

    protected $table = 'distribuidores';

    protected $fillable = [
        'usuario_id',
        'nombre_empresa',
        'direccion',
        'telefono',
        'estado_autorizacion', // 👈 Añadido
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}