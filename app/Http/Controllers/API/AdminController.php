<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distribuidor;
use App\Models\Usuario;
use App\Notifications\GestorDespachoAprobado;

class AdminController extends Controller
{
    public function aprobarGestorDespacho($usuario_id)
{
    // Buscar el usuario por ID
    $usuario = Usuario::find($usuario_id);

    // Verificar que el usuario exista y que su rol sea 'gestor_despacho'
    if (!$usuario || $usuario->rol !== 'gestor_despacho') {
        return response()->json(['mensaje' => 'Usuario no válido o no es un gestor de despacho'], 403);
    }

    // Buscar el distribuidor asociado al usuario usando el campo usuario_id
    $distribuidor = Distribuidor::where('usuario_id', $usuario_id)->first();

    // Si no se encuentra el distribuidor, devolver error
    if (!$distribuidor) {
        return response()->json(['mensaje' => 'Distribuidor no encontrado para este usuario'], 404);
    }

    // Actualizar el estado de autorización a "aprobado"
    $distribuidor->estado_autorizacion = 'aprobado';
    $distribuidor->save();

    $usuario->notify(new GestorDespachoAprobado($usuario));

    // Devolver respuesta de éxito
    return response()->json(['mensaje' => 'Gestor de despacho aprobado exitosamente']);
}
}
