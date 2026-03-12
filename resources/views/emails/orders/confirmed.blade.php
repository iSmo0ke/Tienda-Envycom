<x-mail::message>
# ¡Gracias por tu compra en ENVYCOM!

Hola **{{ $order->user->name }}**, hemos recibido tu pedido correctamente. Estamos preparando todo para su envío.

Aquí tienes el resumen de tu compra:

**Folio del pedido:** {{ $order->order_number }}  
**Fecha:** {{ $order->created_at->format('d/m/Y h:i A') }}

<x-mail::table>
| Producto       | Cantidad         | Precio  |
| :------------- |:-------------:| --------:|
@foreach($order->items as $item)
| {{ $item->product->nombre ?? 'Producto' }} | {{ $item->quantity }} | ${{ number_format($item->price * $item->quantity, 2) }} |
@endforeach
| **Subtotal** | | **${{ number_format($order->subtotal, 2) }}** |
| **Envío** | | **${{ number_format($order->shipping_cost, 2) }}** |
| **Total** | | **${{ number_format($order->total, 2) }}** |
</x-mail::table>

<x-mail::panel>
**Dirección de entrega:** {{ $order->shipping_address }}
</x-mail::panel>

<x-mail::button :url="route('profile.pedido.detalle', $order->id)">
Ver detalle de mi pedido
</x-mail::button>

Cualquier duda, puedes responder a este correo.

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>