@vite(['resources/css/app.css', 'resources/js/app.js'])

<h1>Catálogo de Productos</h1>

@foreach ($products as $product)
    <div class="mb-6 border p-4 rounded">
        <p>{{ $product->marca }}</p>
        <h2>{{ $product->nombre }}</h2>

        @if($product->moneda == 'USD')
            <p>${{ number_format($product->precio * 17.85, 2) }} MXN</p>
        @else
            <p>${{ number_format($product->precio, 2) }} MXN</p>
        @endif

        <form action="{{ route('carrito.add', $product->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-black text-white rounded">
                Añadir al carrito
            </button>
        </form>
    </div>
@endforeach

{{ $products->links() }}