<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index()
    {
        return response()->json(Categoria::all());
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255|unique:categorias,nombre',
    ]);

    $categoria = Categoria::create([
        'nombre' => $request->nombre,
    ]);

    return response()->json($categoria, 201);
}
}