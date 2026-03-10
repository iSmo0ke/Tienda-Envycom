@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-3">Resultados de búsqueda</h1>

        @if($search)
            <p class="text-muted">Mostrando resultados para: <strong class="text-dark">{{ $search }}</strong></p>
        @endif

        <div class="row mt-4">
            @forelse($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="product-card text-center h-100 d-flex flex-column">
                        <img src="{{ $product->imagen }}" alt="{{ $product->nombre }}" class="img-fluid mb-3">
                        <h6 class="mt-auto">{{ $product->nombre }}</h6>
                        <p class="small text-muted">{{ $product->descripcion }}</p>
                        <h5>${{ number_format($product->precio, 2) }}</h5>
                        <button class="btn btn-envy w-100 mt-2">Agregar</button>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light text-center border py-5">
                        <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                        <h5>No encontramos productos</h5>
                        <p class="mb-0">Intenta buscar con otras palabras.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection