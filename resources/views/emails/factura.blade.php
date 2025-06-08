@component('mail::message')
# ¡Gracias por tu compra!

Adjuntamos la factura del pedido **#{{ $pedido->id }}**.

Si tienes dudas, no dudes en contactarnos.

Gracias,<br>
**{{ config('app.name') }}**
@endcomponent
