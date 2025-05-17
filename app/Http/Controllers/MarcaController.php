<?php

namespace App\Http\Controllers;
use App\Models\Marca;

use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        return response()->json(Marca::all());
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255|unique:marcas,nombre',
    ]);

    $marca = Marca::create([
        'nombre' => $request->nombre,
    ]);

    return response()->json($marca, 201);
}

}



