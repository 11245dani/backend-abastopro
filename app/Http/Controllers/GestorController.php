<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\Subpedido;
use App\Models\DetalleSubpedido;

class GestorController extends Controller
{
    public function obtenerInformes(Request $request)
    {
        $usuario = auth()->user();
        $distribuidor_id = $usuario->distribuidor->id;

        // Fechas de filtro opcionales
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if ($fechaInicio && $fechaFin) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
            $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        }

        // Base query de subpedidos para este distribuidor
        $querySubpedidos = Subpedido::where('distribuidor_id', $distribuidor_id);

        if ($fechaInicio && $fechaFin) {
            $querySubpedidos->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        // Total de pedidos aceptados/no pendientes
        $totalPedidos = (clone $querySubpedidos)
            ->where('estado', '!=', 'pendiente')
            ->count();

        // Total de pedidos pendientes
        $totalPendientes = (clone $querySubpedidos)
            ->where('estado', 'pendiente')
            ->count();

        // Subconsulta base para DetalleSubpedido con relación directa a subpedidos
        $detalleSubpedidoQuery = DetalleSubpedido::whereHas('subpedido', function ($query) use ($distribuidor_id, $fechaInicio, $fechaFin) {
            $query->where('distribuidor_id', $distribuidor_id)
                  ->where('estado', '!=', 'pendiente');

            if ($fechaInicio && $fechaFin) {
                $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            }
        });

        // Total productos vendidos
        $totalProductosVendidos = (clone $detalleSubpedidoQuery)->sum('cantidad') ?? 0;

        // Total ingresos generados
        $totalIngresos = (clone $detalleSubpedidoQuery)->sum(DB::raw('cantidad * precio_unitario'));
        $totalIngresos = $totalIngresos ?? 0.0; // Garantiza que no sea null

        // Ventas por día en los últimos 30 días
        $ventasPorDia = DetalleSubpedido::select(
                DB::raw('DATE(subpedidos.created_at) as fecha'),
                DB::raw('SUM(detalle_subpedidos.cantidad * detalle_subpedidos.precio_unitario) as total')
            )
            ->join('subpedidos', 'detalle_subpedidos.subpedido_id', '=', 'subpedidos.id')
            ->where('subpedidos.distribuidor_id', $distribuidor_id)
            ->where('subpedidos.estado', '!=', 'pendiente')
            ->where('subpedidos.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Ventas por mes del año actual
        $ventasPorMes = DetalleSubpedido::select(
                DB::raw('MONTH(subpedidos.created_at) as mes'),
                DB::raw('SUM(detalle_subpedidos.cantidad * detalle_subpedidos.precio_unitario) as total')
            )
            ->join('subpedidos', 'detalle_subpedidos.subpedido_id', '=', 'subpedidos.id')
            ->where('subpedidos.distribuidor_id', $distribuidor_id)
            ->where('subpedidos.estado', '!=', 'pendiente')
            ->whereYear('subpedidos.created_at', now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return response()->json([
            'total_pedidos'            => $totalPedidos,
            'total_pendientes'         => $totalPendientes,
            'total_productos_vendidos' => $totalProductosVendidos,
            'total_ingresos'           => (float) $totalIngresos, // Siempre float
            'ventas_por_dia'           => $ventasPorDia,
            'ventas_por_mes'           => $ventasPorMes,
        ]);
    }
}
