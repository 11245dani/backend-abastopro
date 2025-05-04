<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'tiendas';

    protected $fillable = [
        'usuario_id',
        'nombre',
        'direccion',
        'telefono',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}