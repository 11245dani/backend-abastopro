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

public function store(Request $request)
{
    $request->validate([
        'productos' => 'required|array',
        'productos.*.id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
    ]);

    $usuario = auth()->user();
    $tienda = $usuario->tienda;

    DB::beginTransaction();
    try {
        $pedido = Pedido::create([
            'tienda_id' => $tienda->id,
            'estado' => 'pendiente',
        ]);

        $agrupadoPorDistribuidor = [];

        foreach ($request->productos as $item) {
            $producto = Producto::find($item['id']);
            $distribuidorId = $producto->distribuidor_id;

            $agrupadoPorDistribuidor[$distribuidorId][] = [
                'producto' => $producto,
                'cantidad' => $item['cantidad']
            ];
        }

        foreach ($agrupadoPorDistribuidor as $distribuidorId => $items) {
            $subpedido = Subpedido::create([
                'pedido_id' => $pedido->id,
                'distribuidor_id' => $distribuidorId,
                'estado' => 'pendiente'
            ]);

            foreach ($items as $item) {
                $producto = $item['producto'];
                DetalleSubpedido::create([
                    'subpedido_id' => $subpedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                ]);
            }
        }

        DB::commit();

        return response()->json(['mensaje' => 'Pedido creado correctamente', 'pedido_id' => $pedido->id]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function subpedidosDistribuidor()
{
    $usuario = auth()->user();

    if ($usuario->rol !== 'gestor_despacho') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $distribuidor = $usuario->distribuidor;

    $subpedidos = Subpedido::with(['pedido.tienda.usuario', 'detalles.producto'])
        ->where('distribuidor_id', $distribuidor->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($subpedidos);
}

public function historialPedidosTienda()
{
    $usuario = auth()->user();

    if (!$usuario->tienda) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $pedidos = Pedido::with(['subpedidos.detalles.producto', 'subpedidos.distribuidor.usuario'])
        ->where('tienda_id', $usuario->tienda->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($pedidos);
}

public function actualizarEstadoSubpedido(Request $request, $id)
{
    $request->validate([
        'estado' => 'required|in:aceptado,en_camino,entregado,cancelado'
    ]);

    $usuario = auth()->user();

    if ($usuario->rol !== 'gestor_despacho') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $subpedido = Subpedido::where('id', $id)
        ->where('distribuidor_id', $usuario->distribuidor->id)
        ->first();

    if (!$subpedido) {
        return response()->json(['error' => 'Subpedido no encontrado o no autorizado'], 403);
    }

    $subpedido->estado = $request->estado;
    $subpedido->save();

    return response()->json(['mensaje' => 'Estado del subpedido actualizado', 'subpedido' => $subpedido]);
}

public function confirmarEntregaTienda($id)
{
    $usuario = auth()->user();

    if (!$usuario->tienda) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $pedido = Pedido::where('id', $id)
        ->where('tienda_id', $usuario->tienda->id)
        ->with('subpedidos')
        ->first();

    if (!$pedido) {
        return response()->json(['error' => 'Pedido no encontrado'], 404);
    }

    // Verificar que todos los subpedidos estén entregados
    $todosEntregados = $pedido->subpedidos->every(function ($subpedido) {
        return $subpedido->estado === 'entregado';
    });

    if (!$todosEntregados) {
        return response()->json(['error' => 'No todos los subpedidos están entregados'], 400);
    }

    $pedido->estado = 'entregado';
    $pedido->save();

    return response()->json(['mensaje' => 'Pedido confirmado como entregado por la tienda']);
}


public function pedidosTienda(Request $request)
{
    $usuario = $request->user();

    if (!$usuario->tienda) {
        return response()->json(['message' => 'El usuario no es un tendero válido.'], 403);
    }

    $pedidos = Pedido::where('tienda_id', $usuario->tienda->id)
        ->with([
            'subpedidos.distribuidor.usuario',
            'subpedidos.detalles.producto.marca',
            'subpedidos.detalles.producto.categoria',
        ])
        ->orderByDesc('created_at')
        ->get();

    return response()->json($pedidos);
}
 
}
