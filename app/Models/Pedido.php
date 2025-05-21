<?php

namespace App\Models;
use App\Models\Tienda;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'tienda_id',
        'estado',
        // agrega aquí otros campos que uses con create()
    ];

    public function detalles() {
    return $this->hasMany(DetallePedido::class);
}

public function tienda()
{
    return $this->belongsTo(Tienda::class);
}




}
