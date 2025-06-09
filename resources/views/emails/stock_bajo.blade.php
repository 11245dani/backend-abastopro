<h2>⚠️ Alerta de Stock Bajo</h2>

<p>Hola {{ $producto->distribuidor->usuario->nombre }},</p>

<p>El producto <strong>{{ $producto->nombre }}</strong> ha alcanzado un nivel crítico de inventario.</p>

<ul>
    <li>Stock actual: <strong>{{ $producto->stock }}</strong></li>
    <li>Stock mínimo establecido: {{ $producto->stock_minimo }}</li>
</ul>

<p>Te recomendamos reponer este producto lo antes posible.</p>

<p>Gracias,<br>Equipo de Abastopro</p>
