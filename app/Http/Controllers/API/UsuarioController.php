<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\SolicitudCambioRol;

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


    public function cambiarRol(Request $request, $id)
{
    $admin = $request->user();

    if ($admin->id == $id) {
        return response()->json(['mensaje' => 'No puedes cambiar tu propio rol.'], 403);
    }

    $request->validate([
        'nuevo_rol' => 'required|in:tendero,gestor_despacho',
    ]);

    $usuario = Usuario::find($id);

    if (!$usuario) {
        return response()->json(['mensaje' => 'Usuario no encontrado.'], 404);
    }

    if ($usuario->rol === 'admin') {
        return response()->json(['mensaje' => 'No se puede cambiar el rol de otro administrador.'], 403);
    }

    $usuario->rol = $request->nuevo_rol;
    $usuario->save();

    return response()->json([
        'mensaje' => 'Rol actualizado correctamente.',
        'usuario' => $usuario
    ]);
}

public function solicitarCambioRol(Request $request)
{
    $request->validate([
        'rol_solicitado' => 'required|in:tendero,gestor_despacho',
        'nombre_empresa' => 'required_if:rol_solicitado,gestor_despacho|string|max:255',
    ]);

    $usuario = auth()->user();

    if ($usuario->rol === $request->rol_solicitado) {
        return response()->json(['mensaje' => 'Ya tienes ese rol asignado.'], 400);
    }

    // Revisar si ya hay una solicitud pendiente
    if (SolicitudCambioRol::where('usuario_id', $usuario->id)->where('estado', 'pendiente')->exists()) {
        return response()->json(['mensaje' => 'Ya tienes una solicitud pendiente.'], 400);
    }

    SolicitudCambioRol::create([
        'usuario_id' => $usuario->id,
        'rol_solicitado' => $request->rol_solicitado,
        'nombre_empresa' => $request->nombre_empresa,
    ]);

    return response()->json(['mensaje' => 'Solicitud de cambio de rol enviada.']);
}


}