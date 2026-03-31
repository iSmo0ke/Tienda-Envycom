@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        @if($productos->isEmpty())
            No encontramos resultados para: <span class="text-red-500">"{{ $query }}"</span>
        @else
            Resultados para: <span class="text-green-600">"{{ $query }}"</span>
        @endif
    </h1>

    @if($productos->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm" role="alert">
            <p class="font-bold">¡Lo sentimos!</p>
            <p>No hay productos que coincidan con "<strong>{{ $query }}</strong>". Intenta buscar con términos más generales o sin caracteres especiales.</p>
            <div class="mt-4">
                <a href="{{ route('products.index') }}" class="inline-block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition">
                    Ver todo el catálogo
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($productos as $product)
                <div class="bg-white rounded-lg shadow-md p-4 flex flex-col hover:shadow-lg transition-shadow duration-300">
                    <a href="{{ route('products.show', $product->id) }}" class="group">
                        <div class="overflow-hidden rounded-md mb-3">
                            <x-product-image :product="$product" class="w-full h-48 object-contain group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <h3 class="font-bold text-gray-800 line-clamp-2 h-12 group-hover:text-green-600 transition-colors">
                            {{ $product->nombre }}
                        </h3>
                    </a>
                
                    <div class="mt-auto pt-4">
                        <p class="text-sm text-gray-500 mb-1">{{ $product->marca }}</p>
                        <p class="text-green-600 font-bold text-xl">
                            ${{ number_format($product->precio, 2) }}
                        </p>
                    </div>

                    <form action="{{ route('carrito.add', $product->id) }}" method="POST" class="mt-4 border-t pt-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input 
                                    type="number" 
                                    name="quantity" 
                                    value="1" 
                                    min="1" 
                                    max="20"
                                    class="w-full rounded border-gray-300 text-sm focus:ring-green-500 focus:border-green-500 @error('quantity') border-red-500 @enderror"
                                    required
                                >
                            </div>
                            <x-primary-button class="bg-black hover:bg-gray-800 text-white transition-colors py-2 px-4 rounded">
                                Agregar
                            </x-primary-button>
                        </div>

                        @error('quantity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center">
            {{ $productos->appends(['q' => $query])->links() }}
        </div>
    @endif
</div>
@endsection