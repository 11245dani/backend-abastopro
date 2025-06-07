<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subpedido;


class SubpedidoController extends Controller
{
  public function pedidosDistribuidor(Request $request)
{
    $usuario = $request->user();

    if (!$usuario->distribuidor) {
        return response()->json(['message' => 'El usuario no es un distribuidor válido.'], 403);
    }

    $subpedidos = Subpedido::where('distribuidor_id', $usuario->distribuidor->id)
        ->with([
            'pedido.tienda.usuario',
            'detalles.producto.marca',
            'detalles.producto.categoria',
        ])
        ->orderByDesc('created_at')
        ->get();

    return response()->json($subpedidos);
}

public function actualizarEstado(Request $request, $id)
{
    $subpedido = Subpedido::findOrFail($id);
    $nuevoEstado = $request->input('estado');

    // Actualizas el estado
    $subpedido->estado = $nuevoEstado;
    $subpedido->save();

    // Aquí es donde debes descontar el stock si el nuevo estado es 'aceptado'
    if ($nuevoEstado === 'aceptado') {
        foreach ($subpedido->detalles as $detalle) {
            $producto = $detalle->producto;
            if ($producto && $producto->stock >= $detalle->cantidad) {
                $producto->stock -= $detalle->cantidad;
                $producto->save();
            } else {
                // Maneja el caso de stock insuficiente, opcional
                return response()->json(['error' => 'Stock insuficiente para el producto ' . $producto->nombre], 400);
            }
        }
    }

    return response()->json(['message' => 'Estado actualizado correctamente']);
}



}
