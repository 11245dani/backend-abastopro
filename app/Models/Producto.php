<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribuidor_id',
        'nombre',
        'descripcion',
        'imagen_url',
        'precio',
        'stock',
        'categoria_id',
        'marca_id',
    ];

    public function distribuidor()
{
    return $this->belongsTo(Distribuidor::class);
}

public function categoria()
{
    return $this->belongsTo(Categoria::class);
}

public function marca()
{
    return $this->belongsTo(Marca::class);
}

}
