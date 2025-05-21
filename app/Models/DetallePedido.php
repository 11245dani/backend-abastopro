<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;

        
    protected $table = 'detalle_pedidos'; // o el nombre correcto de tu tabla


        protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
    ];

    public function producto() {
    return $this->belongsTo(Producto::class);
}

public function pedido()
{
    return $this->belongsTo(Pedido::class);
}
}
