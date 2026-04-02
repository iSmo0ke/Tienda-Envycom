@extends('layouts.app')

@section('content')
    <style>
        :root {
            --envy-lime: #dfff00;
            --envy-dark: #121012;
            --envy-blue: #0b2b57;
            --envy-gray: #6b7280;
            --envy-bg: #f5f6f8;
        }

        body {
            background: var(--envy-bg);
        }

        .catalog-wrapper {
            padding: 40px 0 60px;
        }

        .catalog-title {
            font-size: 3rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 24px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .product-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .09);
        }

        .product-image-wrap {
            background: #fff;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            border-bottom: 1px solid #f1f1f1;
        }

        /* Usamos img-fluid para que respete el contenedor */
        .product-image-wrap img {
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
        }

        .product-body {
            padding: 18px 18px 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-brand {
            font-size: .85rem;
            font-weight: 700;
            color: var(--envy-blue);
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 10px;
        }

        .product-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
            line-height: 1.25;
            margin-bottom: 10px;
            min-height: 72px;
        }

        .product-desc {
            font-size: .95rem;
            color: var(--envy-gray);
            line-height: 1.5;
            margin-bottom: 16px;
            min-height: 48px;
        }

        .product-price {
            font-size: 1.6rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 16px;
        }

        .product-actions {
            margin-top: auto;
        }

        .btn-cart {
            width: 100%;
            border: none;
            border-radius: 999px;
            background: var(--envy-lime);
            color: #111;
            font-weight: 800;
            padding: 12px 18px;
            transition: .2s ease;
        }

        .btn-cart:hover {
            background: #d3f200;
        }

        .empty-products {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            padding: 40px;
            text-align: center;
            color: var(--envy-gray);
        }

        @media (max-width: 768px) {
            .catalog-title {
                font-size: 2.2rem;
            }

            .product-name {
                font-size: 1.2rem;
                min-height: auto;
            }

            .product-image-wrap {
                height: 190px;
            }

            .product-link:hover .product-name {
                color: #0b2b57; /* Cambiado a tu color Envy Blue */
                text-decoration: underline;
            }
        }
    </style>

    <div class="container catalog-wrapper">
        <h1 class="catalog-title">Catálogo de Productos</h1>
        
        @if(session('success'))
            <div class="alert alert-success rounded-4 shadow-sm mb-4 fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($products->count())
            <div class="products-grid">
                @foreach ($products as $product)
                    <div class="product-card">
                        
                        {{-- Todo el contenido clickeable (Imagen + Textos) envuelto en un solo enlace --}}
                        <a href="{{ route('products.show', $product->id) }}" class="product-link" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; flex-grow: 1;">
                            
                            <div class="product-image-wrap">
                                {{-- Quitamos las clases de Tailwind y usamos las puras de Bootstrap/CSS --}}
                                <x-product-image 
                                    :image="$product->imagen" 
                                    :alt="$product->nombre" 
                                    cssClass="img-fluid" 
                                />
                            </div>

                            <div class="product-body">
                                <div class="product-brand">
                                    {{ $product->marca ?? 'Sin marca' }}
                                </div>

                                <div class="product-name">
                                    {{ $product->nombre }}
                                </div>

                                <div class="product-desc">
                                    {{ \Illuminate\Support\Str::limit($product->descripcion_corta ?? 'Sin descripción disponible.', 90) }}
                                </div>

                                <div class="product-price">
                                    ${{ number_format($product->precio, 2) }} MXN
                                </div>
                            </div>
                            
                        </a>

                        {{-- El botón de agregar al carrito queda fuera del enlace principal para que funcione el formulario --}}
                        <div class="product-actions" style="padding: 0 18px 20px;">
                            <form action="{{ route('carrito.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-cart">
                                    <i class="bi bi-cart-plus me-1"></i> Añadir al carrito
                                </button>
                            </form>
                        </div>
                        
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrapper mt-4 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        @else
            <div class="empty-products">
                <i class="bi bi-box-seam fs-1 text-muted d-block mb-3"></i>
                No hay productos disponibles por el momento.
            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alerta = document.querySelector('.alert-success');
            if (alerta) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alerta);
                    bsAlert.close();
                }, 3000);
            }
        });
    </script>
@endsection