<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SolicitudCambioRol;
use App\Models\Tienda;
use App\Models\Distribuidor;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use App\Notifications\GestorDespachoAprobado;
use App\Notifications\GestorRechazadoNotification;
use App\Models\Pedido;
use App\Models\Producto;

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

public function rechazarGestorDespacho($id)
{
    $usuario = Usuario::findOrFail($id);

    // Asegúrate de que el usuario tiene relación con distribuidor
    if ($usuario->rol !== 'gestor_despacho' || !$usuario->distribuidor) {
        return response()->json(['error' => 'Este usuario no es un gestor de despacho válido.'], 400);
    }

    $usuario->distribuidor->estado_autorizacion = 'rechazado';
    $usuario->distribuidor->save();
        //Envia el correo de rechazo
      $usuario->notify(new GestorRechazadoNotification());

    return response()->json(['message' => 'Gestor de despacho rechazado correctamente.']);
}

public function generarReportes(Request $request)
{
    $query = Pedido::with(['detalles.producto', 'tienda.usuario']);

    if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
        $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
    }

    if ($request->filled('producto_id')) {
        $query->whereHas('detalles', function ($q) use ($request) {
            $q->where('producto_id', $request->producto_id);
        });
    }

    if ($request->filled('usuario_id')) {
        $query->whereHas('tienda.usuario', function ($q) use ($request) {
            $q->where('id', $request->usuario_id);
        });
    }

    $pedidos = $query->get();

    return response()->json($pedidos);
}

public function dashboard()
{
    $totalPedidos = Pedido::count();
    $usuariosActivos = Usuario::whereNotNull('email_verified_at')->count();
    $totalProductos = Producto::count();
    
    // Sumar total de ventas (suponiendo que tienes un campo "total" o "precio * cantidad" por pedido)
    $ventasTotales = DB::table('detalle_pedidos')
        ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
        ->select(DB::raw('SUM(detalle_pedidos.cantidad * productos.precio) as total'))
        ->value('total');

    return response()->json([
        'total_pedidos' => $totalPedidos,
        'usuarios_activos' => $usuariosActivos,
        'total_productos' => $totalProductos,
        'ventas_totales' => round($ventasTotales, 2),
    ]);
}

}
