@extends('layouts.app')

@section('content')
    <h1>Resultados de búsqueda</h1>

    @if($search)
        <p>Mostrando resultados para: {{ $search }}</p>
    @endif

    @forelse($products as $product)
        <div class="mb-6 border p-4 rounded">
            <h6>{{ $product->nombre }}</h6>
            <p>{{ $product->descripcion }}</p>
            <h5>${{ number_format($product->precio, 2) }}</h5>

            <form action="{{ route('carrito.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-black text-white rounded">
                    Agregar
                </button>
            </form>
        </div>
    @empty
        <h5>No encontramos productos</h5>
        <p>Intenta buscar con otras palabras.</p>
    @endforelse
@endsection