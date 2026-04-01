@extends('layouts.app')

@section('content')
<style>
    :root {
        --envy-lime: #dfff00;
        --envy-blue: #0b2b57;
        --envy-gray: #4a4a4a;
        --bg-soft: #f5f6f8;
    }

    body {
        background: var(--bg-soft);
    }

    .search-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #eaeaea;
    }

    .product-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #eaeaea;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .card-img-wrapper {
        padding: 20px;
        background: #fff;
        text-align: center;
        border-bottom: 1px solid #f8f9fa;
    }

    .card-img-wrapper img {
        height: 200px !important;
        width: 100%;
        object-fit: contain !important;
        transition: transform 0.3s ease;
    }

    .product-card:hover .card-img-wrapper img {
        transform: scale(1.05);
    }

    .product-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--envy-blue);
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-desc {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-size: 1.4rem;
        font-weight: 800;
        color: #111;
        margin-top: auto; 
    }

    .btn-envy {
        background: var(--envy-lime);
        color: var(--envy-blue);
        font-weight: 700;
        border-radius: 999px;
        padding: 10px 20px;
        transition: all 0.3s ease;
        border: none;
        width: 100%;
    }

    .btn-envy:hover {
        background: #d0ef00;
        transform: scale(1.02);
    }
</style>

<div class="container py-5">
    
    <div class="search-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: var(--envy-blue);">Resultados de búsqueda</h1>
            @if($search)
                <p class="text-muted mb-0 fs-5">
                    Mostrando resultados para: <strong class="text-dark">"{{ $search }}"</strong>
                </p>
            @endif
        </div>
        <a href="{{ url('/productos') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Volver al catálogo
        </a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($products as $product)
            <div class="col">
                <div class="product-card">
                    
                    <a href="{{ action([\App\Http\Controllers\ProductController::class, 'show'], $product->id) }}" class="card-img-wrapper text-decoration-none">
                        <x-product-image 
                            :image="$product->imagen" 
                            :alt="$product->nombre" 
                            cssClass="img-fluid" 
                        />
                    </a>

                    <div class="product-info">
                        <a href="{{ action([\App\Http\Controllers\ProductController::class, 'show'], $product->id) }}" class="text-decoration-none">
                            <h6 class="product-title">{{ $product->nombre }}</h6>
                        </a>
                        <p class="product-desc">{{ $product->descripcion_corta ?? $product->descripcion }}</p>
                        
                        <div class="d-flex justify-content-between align-items-end mt-auto mb-3">
                            <span class="product-price">${{ number_format($product->precio, 2) }}</span>
                        </div>

                        <form action="{{ url('/carrito/add/' . $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-envy">
                                <i class="bi bi-cart-plus me-2"></i>Agregar
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 border shadow-sm mx-auto" style="max-width: 600px;">
                    <i class="bi bi-search display-1 text-muted opacity-25 mb-4 d-block"></i>
                    <h3 class="fw-bold mb-3" style="color: var(--envy-blue);">¡Ups! No encontramos coincidencias</h3>
                    <p class="text-muted mb-4 fs-5">No pudimos encontrar ningún producto que coincida con "<strong>{{ $search }}</strong>". Intenta buscar usando otras palabras.</p>
                    <a href="{{ url('/productos') }}" class="btn rounded-pill px-5 py-2 fw-bold shadow-sm" style="background: var(--envy-lime); color: var(--envy-blue);">
                        Explorar todo el catálogo
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection