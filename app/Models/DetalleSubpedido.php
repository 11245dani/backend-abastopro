<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleSubpedido extends Model
{
    use HasFactory;

    protected $fillable = ['subpedido_id', 'producto_id', 'cantidad', 'precio_unitario'];

    public function subpedido() {
        return $this->belongsTo(Subpedido::class);
    }

    public function producto() {
        return $this->belongsTo(Producto::class);
    }
}
