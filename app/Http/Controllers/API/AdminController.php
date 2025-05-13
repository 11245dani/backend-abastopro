<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SolicitudCambioRol;
use App\Models\Tienda;
use App\Models\Distribuidor;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{


public function aprobarCambioRol($id)
{
    $solicitud = SolicitudCambioRol::with('usuario')->findOrFail($id);
    $usuario = $solicitud->usuario;

    if ($usuario->rol === 'admin') {
        return response()->json(['mensaje' => 'No puedes cambiar el rol de un administrador.'], 403);
    }

DB::transaction(function () use ($usuario, $solicitud) {
    $direccionAnterior = null;

    // Obtener dirección antes de eliminar
    if ($usuario->rol === 'tendero') {
        $tienda = Tienda::where('usuario_id', $usuario->id)->first();
        $direccionAnterior = $tienda?->direccion;
        $tienda?->delete();
    } elseif ($usuario->rol === 'gestor_despacho') {
        $distribuidor = Distribuidor::where('usuario_id', $usuario->id)->first();
        $direccionAnterior = $distribuidor?->direccion;
        $distribuidor?->delete();
    }

    // Crear nueva entidad
    if ($solicitud->rol_solicitado === 'tendero') {
        Tienda::create([
            'usuario_id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'direccion' => $direccionAnterior,
        ]);
    } elseif ($solicitud->rol_solicitado === 'gestor_despacho') {
        Distribuidor::create([
            'usuario_id' => $usuario->id,
            'nombre_empresa' => $solicitud->nombre_empresa,
            'direccion' => $direccionAnterior,
        ]);
    }

    // Actualizar usuario
    $usuario->rol = $solicitud->rol_solicitado;
    $usuario->save();

    // Marcar solicitud como aprobada
    $solicitud->estado = 'aprobada';
    $solicitud->save();
});


    return response()->json(['mensaje' => 'Cambio de rol aplicado correctamente.']);
}

public function listarSolicitudesPendientes()
{
    $solicitudes = SolicitudCambioRol::with('usuario')
        ->where('estado', 'pendiente')
        ->get();

    return response()->json($solicitudes);
}

public function rechazarCambioRol($id)
{
    $solicitud = SolicitudCambioRol::findOrFail($id);

    if ($solicitud->estado !== 'pendiente') {
        return response()->json(['mensaje' => 'Esta solicitud ya fue procesada.'], 400);
    }

    $solicitud->estado = 'rechazada';
    $solicitud->save();

    return response()->json(['mensaje' => 'Solicitud rechazada.']);
}


}
