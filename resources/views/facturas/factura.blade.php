<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Pedido #{{ $pedido->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Factura Pedido #{{ $pedido->id }}</h2>

    <p><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y') }}</p>

    <h3>Información del Tendero</h3>
    <p><strong>Nombre:</strong> {{ $pedido->tienda->usuario->nombre }}</p>
    <p><strong>Correo:</strong> {{ $pedido->tienda->usuario->correo }}</p>
    <p><strong>Dirección:</strong> {{ $pedido->tienda->direccion }}</p>

    <h3>Información del Distribuidor</h3>
    <p><strong>Nombre:</strong> {{ $distribuidor->usuario->nombre }}</p>
    <p><strong>Empresa:</strong> {{ $distribuidor->nombre_empresa }}</p>

    <h3>Productos</h3>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subpedido->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                    <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total: ${{ number_format($subpedido->detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario), 0, ',', '.') }}</h3>
</body>
</html>
