<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Tienda;
use App\Models\Distribuidor;
use App\Models\DetallePedio;
use App\Models\Pedido;
use App\Models\DetallePedido;



use Illuminate\Http\Request;

class PedidoControllerr extends Controller
{
    public function pedidosDistribuidor()
{
    $usuario = auth()->user();

    if ($usuario->rol !== 'gestor_despacho') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Obtener productos del distribuidor
    $productos = $usuario->distribuidor->productos()->pluck('id');

    // Obtener detalles de pedidos que contienen esos productos
    $detallePedidos = DetallePedido::with(['pedido.tienda.usuario', 'producto'])
        ->whereIn('producto_id', $productos)
        ->get();

    return response()->json($detallePedidos);
}

public function actualizarEstado(Request $request, $pedidoId)
{
    $request->validate([
        'estado' => 'required|in:aceptado,en_camino,entregado'
    ]);

    $pedido = Pedido::findOrFail($pedidoId);

    // Verifica que el pedido contiene productos del distribuidor autenticado
    $usuario = auth()->user();
    if ($usuario->rol !== 'gestor_despacho') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $productoIds = $usuario->distribuidor->productos()->pluck('id')->toArray();
    $tieneProducto = DetallePedido::where('pedido_id', $pedido->id)
        ->whereIn('producto_id', $productoIds)
        ->exists();

    if (!$tieneProducto) {
        return response()->json(['error' => 'No autorizado para modificar este pedido'], 403);
    }

    $pedido->estado = $request->estado;
    $pedido->save();

    return response()->json(['message' => 'Estado del pedido actualizado', 'pedido' => $pedido]);
}

public function historialPedidosTienda()
{
    $usuario = auth()->user();

    if (!$usuario->tienda) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $pedidos = Pedido::with('detalles.producto')
        ->where('tienda_id', $usuario->tienda->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($pedidos);
}

public function cambiarEstado(Request $request, $id)
{
    try {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        // ✅ Validar que el estado sea uno permitido
        $request->validate([
            'estado' => 'required|in:pendiente,procesado,enviado,entregado,cancelado,aceptado'
        ]);

        // ✅ Obtener el nuevo estado ya validado
        $nuevoEstado = $request->input('estado');

        $pedido->estado = $nuevoEstado;
        $pedido->save();

        return response()->json(['mensaje' => 'Estado actualizado correctamente']);
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Error interno al actualizar el estado',
            'exception' => $e->getMessage()
        ], 500);
    }
}


}
