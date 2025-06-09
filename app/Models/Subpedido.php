<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subpedido extends Model
{
    use HasFactory;

    protected $fillable = ['pedido_id', 'distribuidor_id', 'estado'];

    public function pedido() {
        return $this->belongsTo(Pedido::class);
    }

    public function distribuidor() {
        return $this->belongsTo(Distribuidor::class);
    }

    public function detalles() {
        return $this->hasMany(DetalleSubpedido::class);
    }
}


