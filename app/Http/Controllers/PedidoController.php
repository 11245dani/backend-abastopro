<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Tienda;
use App\Models\Distribuidor;
use App\Models\DetallePedio;
use App\Models\Pedido;
use App\Models\DetallePedido;



use Illuminate\Http\Request;

class PedidoController extends Controller
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
     dd($request->all());
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




}
