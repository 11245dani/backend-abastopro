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

        if (!$usuario->tienda || !$usuario->tienda) {
            return response()->json(['message' => 'El usuario no es un tendero válido.'], 403);
        }

        $carrito = Carrito::where('tienda_id', $usuario->tienda->id)
            ->with('items.producto')
            ->first();

        if (!$carrito || $carrito->items->isEmpty()) {
            return response()->json(['message' => 'Carrito vacío'], 400);
        }

        DB::beginTransaction();
        try {
            $itemsAgrupados = $carrito->items->groupBy(function ($item) {
                return $item->producto->distribuidor_id;
            });

            foreach ($itemsAgrupados as $distribuidor_id => $items) {
                $pedido = Pedido::create([
                    'tienda_id' => $usuario->tienda->id,
                    'estado' => 'pendiente',
                ]);

                foreach ($items as $item) {
                    DetallePedido::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $item->producto_id,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->producto->precio,
                    ]);
                }

                // Aquí podrías notificar al distribuidor si se desea
            }

            // Vaciar carrito
            $carrito->items()->delete();

            DB::commit();
            return response()->json(['message' => 'Pedido confirmado y enviado a distribuidores']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al confirmar pedido', 'error' => $e->getMessage()], 500);
        }
    }
}
