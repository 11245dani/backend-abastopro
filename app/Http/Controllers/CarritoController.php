<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{
    public function ver(Request $request)
    {
        $usuario = $request->user();

        if (!$usuario->tienda || !$usuario->tienda) {
            return response()->json(['message' => 'El usuario no es un tendero válido.'], 403);
        }

        $carrito = Carrito::firstOrCreate(['tienda_id' => $usuario->tienda->id]);
        $carrito->load('items.producto');

        return response()->json([
            'carrito' => $carrito,
            'estado' => $carrito->items->isEmpty() ? 'vacio' : 'con_items'
        ]);
    }

    public function agregar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $usuario = $request->user();

        if (!$usuario->tienda || !$usuario->tienda) {
            return response()->json(['message' => 'El usuario no es un tendero válido.'], 403);
        }

        $carrito = Carrito::firstOrCreate(['tienda_id' => $usuario->tienda->id]);

        $item = CarritoItem::where('carrito_id', $carrito->id)
            ->where('producto_id', $request->producto_id)
            ->first();

        if ($item) {
            $item->cantidad += $request->cantidad;
            $item->save();
        } else {
            $item = CarritoItem::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad,
            ]);
        }

        return response()->json(['message' => 'Producto agregado al carrito']);
    }

    public function eliminar(Request $request, CarritoItem $item)
    {
        $usuario = $request->user();

        if (!$usuario->tienda || !$usuario->tienda) {
            return response()->json(['message' => 'El usuario no es un tendero válido.'], 403);
        }

        if ($item->carrito->tienda_id !== $usuario->tienda->id) {
            return response()->json(['message' => 'No autorizado para eliminar este producto'], 403);
        }

        $item->delete();
        return response()->json(['message' => 'Producto eliminado del carrito']);
    }

public function confirmar(Request $request)
{
    $usuario = $request->user();
    $productos = $request->input('productos'); // Array de productos con id y cantidad

    if (!$productos || !is_array($productos)) {
        return response()->json(['message' => 'Productos inválidos.'], 400);
    }

    if (!$usuario->tienda) {
        return response()->json(['message' => 'El usuario no es un tendero válido.'], 403);
    }

    // Obtener carrito con productos y su distribuidor
    $carrito = Carrito::where('tienda_id', $usuario->tienda->id)
        ->with(['items.producto.distribuidor']) // Asegura que el distribuidor esté cargado
        ->first();

    if (!$carrito || $carrito->items->isEmpty()) {
        return response()->json(['message' => 'Carrito vacío'], 400);
    }

    // Mapear los productos recibidos para validar cantidades
    $productoMapeado = collect($productos)->keyBy('id'); // id => ['id' => ..., 'cantidad' => ...]

    // Filtrar ítems del carrito que coincidan con los productos enviados y tengan distribuidor
    $itemsValidos = $carrito->items->filter(function ($item) use ($productoMapeado) {
        return $item->producto && $item->producto->distribuidor_id && $productoMapeado->has($item->producto_id);
    });

    if ($itemsValidos->isEmpty()) {
        return response()->json(['message' => 'No hay productos válidos para confirmar'], 400);
    }

    // Actualizar las cantidades de los ítems con lo enviado por el frontend
    foreach ($itemsValidos as $item) {
        $cantidadSolicitada = $productoMapeado[$item->producto_id]['cantidad'];
        $item->cantidad = $cantidadSolicitada;
    }

    DB::beginTransaction();
    try {
        // 1. Crear pedido general
        $pedido = Pedido::create([
            'tienda_id' => $usuario->tienda->id,
            'estado' => 'pendiente',
            'fecha' => now(),
        ]);

        // 2. Agrupar por distribuidor
        $itemsAgrupados = $itemsValidos->groupBy(fn($item) => $item->producto->distribuidor_id);

        // 3. Crear subpedidos con detalles
        foreach ($itemsAgrupados as $distribuidor_id => $items) {
            if ($items->isEmpty()) continue;

            $subpedido = $pedido->subpedidos()->create([
                'distribuidor_id' => $distribuidor_id,
                'estado' => 'pendiente',
            ]);

            foreach ($items as $item) {
                $subpedido->detalles()->create([
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->producto->precio,
                ]);
            }
        }

        // 4. Vaciar el carrito
        $carrito->items()->delete();

        DB::commit();
        return response()->json(['message' => 'Pedido confirmado y enviado a distribuidores']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error al confirmar pedido', 'error' => $e->getMessage()], 500);
    }
}


}
