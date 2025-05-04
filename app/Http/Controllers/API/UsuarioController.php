<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['mensaje' => 'No autorizado'], 403);
        }

        return Usuario::all();
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($request->user()->id !== $usuario->id && $request->user()->rol !== 'admin') {
            return response()->json(['mensaje' => 'No autorizado'], 403);
        }

        $usuario->update($request->only(['nombre', 'correo', 'estado']));

        return response()->json(['mensaje' => 'Usuario actualizado', 'usuario' => $usuario]);
    }
}