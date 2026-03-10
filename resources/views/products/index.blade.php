<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Tecnología - CT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    <nav class="bg-white shadow-sm border-b mb-8">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <span class="text-2xl font-bold text-blue-600">Mi Tienda Online</span>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Catálogo de Productos</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                    <div class="p-4 bg-gray-50 flex items-center justify-center">
                        <img src="{{ $product->imagen }}" alt="{{ $product->nombre }}" class="h-40 object-contain">
                    </div>
                    
                    <div class="p-4 flex flex-col flex-grow">
                        <span class="text-xs font-semibold text-blue-500 uppercase tracking-wider">{{ $product->marca }}</span>
                        <h2 class="mt-1 text-sm font-medium text-gray-800 line-clamp-2 h-10">{{ $product->nombre }}</h2>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-bold text-gray-900">
                                @if($product->moneda == 'USD')
                                    {{-- Aquí usamos el tipo de cambio que viene en tu JSON --}}
                                    ${{ number_format($product->precio * 17.85, 2) }} <small class="text-xs text-gray-500">MXN</small>
                                @else
                                    ${{ number_format($product->precio, 2) }} <small class="text-xs text-gray-500">MXN</small>
                                @endif
                            </span>
                        </div>

                        <button class="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Añadir al carrito
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="py-12">
            {{ $products->links() }}
        </div>
    </main>

</body>
</html>