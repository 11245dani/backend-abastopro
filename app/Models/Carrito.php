<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carrito extends Model
{
    use HasFactory;

    protected $fillable = ['tienda_id'];

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    public function items()
    {
        return $this->hasMany(CarritoItem::class);
    }
}
