<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subpedido;
use App\Mail\StockBajoAlert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubpedidoController extends Controller
{
    public function pedidosDistribuidor(Request $request)
    {
        try {
            $usuario = $request->user();

            if (!$usuario || !$usuario->distribuidor) {
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

        } catch (\Exception $e) {
            Log::error('Error al obtener pedidos del distribuidor: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
        'estado' => 'required|string|in:pendiente,aceptado,rechazado,en_camino,entregado'
        ]);

        DB::beginTransaction();

        try {
            $subpedido = Subpedido::with('detalles.producto.distribuidor.usuario')->findOrFail($id);
            $nuevoEstado = $request->input('estado');

            if ($subpedido->estado === $nuevoEstado) {
                return response()->json(['message' => 'El subpedido ya tiene ese estado'], 422);
            }

            $subpedido->estado = $nuevoEstado;
            $subpedido->save();

            // Si se acepta el subpedido, descontar stock
            if ($nuevoEstado === 'aceptado') {
                $this->procesarDescuentoStock($subpedido);
            }

            DB::commit();
            return response()->json(['message' => 'Estado actualizado correctamente']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar estado del subpedido: ' . $e->getMessage());

            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json(['error' => 'Subpedido no encontrado'], 404);
            }

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function procesarDescuentoStock(Subpedido $subpedido)
    {
        foreach ($subpedido->detalles as $detalle) {
            $producto = $detalle->producto;

            if (!$producto) {
                throw new \Exception('Producto no encontrado para el detalle del subpedido');
            }

            if ($producto->stock < $detalle->cantidad) {
                throw new \Exception("Stock insuficiente para el producto '{$producto->nombre}'. Disponible: {$producto->stock}, solicitado: {$detalle->cantidad}");
            }

            // Descontar stock
            $producto->stock -= $detalle->cantidad;
            $producto->save();

            // Verificar si el stock está por debajo del mínimo y enviar alerta
            $this->verificarStockMinimo($producto);
        }
    }

     private function verificarStockMinimo($producto)
        {
            if ($producto->stock <= $producto->stock_minimo) {
                try {
                    // Verificar que el producto tenga distribuidor y usuario asociado con correo
                    if ($producto->distribuidor && $producto->distribuidor->usuario && $producto->distribuidor->usuario->correo) {
                        $correo = $producto->distribuidor->usuario->correo;
                        Mail::to($correo)->send(new StockBajoAlert($producto));
                        Log::info("Alerta de stock bajo enviada para producto: {$producto->nombre}");
                    } else {
                        Log::warning("No se pudo enviar alerta de stock bajo: distribuidor o correo no disponible para producto {$producto->nombre}");
                    }
                } catch (\Exception $e) {
                    Log::error("Error al enviar alerta de stock bajo: " . $e->getMessage());
                }
            }
        }

}
