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

        /* ESTILOS NUEVOS PARA EL SIDEBAR Y TOPBAR */
        .sidebar-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .03);
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--envy-blue);
            margin-bottom: 16px;
        }

        .category-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            color: var(--envy-gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .category-link:hover {
            background: var(--envy-bg);
            color: var(--envy-blue);
        }

        .category-link.active {
            background: rgba(11, 43, 87, 0.05);
            /* Envy Blue con opacidad */
            color: var(--envy-blue);
            font-weight: 700;
        }

        .top-bar {
            background: #fff;
            border-radius: 12px;
            padding: 12px 20px;
            border: 1px solid #e5e7eb;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* TUS ESTILOS ORIGINALES DE TARJETAS (Intactos) */
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

            .top-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .product-link:hover .product-name {
                color: var(--envy-blue);
                text-decoration: underline;
            }
        }
    </style>

    <div class="container catalog-wrapper">
        <h1 class="catalog-title">Catálogo de Productos</h1>

        @if (session('success'))
            <div class="alert alert-success rounded-4 shadow-sm mb-4 fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">

            <div class="col-lg-3">
                <div class="sidebar-card sticky-top" style="top: 20px; z-index: 1;">

                    <h3 class="filter-title">Buscar</h3>
                    <form action="{{ url('/productos') }}" method="GET" class="mb-4">
                        @if (request('categoria'))
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        @endif
                        @if (request('ordenar'))
                            <input type="hidden" name="ordenar" value="{{ request('ordenar') }}">
                        @endif

                        <div class="input-group">
                            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control"
                                placeholder="¿Qué buscas?" style="border-radius: 8px 0 0 8px; border-color: #e5e7eb;">
                            <button type="submit" class="btn text-white"
                                style="background: var(--envy-blue); border-radius: 0 8px 8px 0;">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <hr style="border-color: #e5e7eb; margin: 24px 0;">

                    <h3 class="filter-title">Categorías</h3>
                    <div class="d-flex flex-column">
                        <a href="{{ request()->url() . '?' . http_build_query(request()->except(['categoria', 'page'])) }}"
                            class="category-link {{ !request('categoria') ? 'active' : '' }}">
                            Todas las categorías
                        </a>

                        @if (isset($categorias))
                            @foreach ($categorias as $categoria)
                                <a href="{{ request()->fullUrlWithQuery(['categoria' => $categoria, 'page' => 1]) }}"
                                    class="category-link {{ request('categoria') == $categoria ? 'active' : '' }}">
                                    {{ $categoria }}
                                    @if (request('categoria') == $categoria)
                                        <i class="bi bi-check2-circle"></i>
                                    @endif
                                </a>
                            @endforeach
                        @endif
                    </div>

                    @if (request()->anyFilled(['buscar', 'categoria', 'ordenar']))
                        <div class="mt-4">
                            <a href="{{ url('/productos') }}" class="btn btn-outline-danger w-100 rounded-pill"
                                style="font-weight: 600;">
                                <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-9">

                @if (request('buscar'))
                    <div class="mb-4">
                        <h2 class="fw-bold mb-1" style="color: var(--envy-blue);">Resultados de búsqueda</h2>
                        <p class="text-muted mb-0 fs-5">
                            Mostrando resultados para: <strong class="text-dark">"{{ request('buscar') }}"</strong>
                        </p>
                    </div>
                @endif

                <div class="top-bar">
                    <span style="color: var(--envy-gray);">
                        Mostrando <strong>{{ $products->total() ?? 0 }}</strong> productos
                    </span>

                    <form action="{{ url('/productos') }}" method="GET" class="d-flex align-items-center">
                        @foreach (request()->except(['ordenar', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <label class="me-2 mb-0" style="color: var(--envy-gray); font-size: 0.9rem;">Ordenar:</label>
                        <select name="ordenar" onchange="this.form.submit()" class="form-select form-select-sm"
                            style="border-radius: 8px; border-color: #e5e7eb; box-shadow: none;">
                            <option value="recientes" {{ request('ordenar') == 'recientes' ? 'selected' : '' }}>Más
                                recientes</option>
                            <option value="menor_precio" {{ request('ordenar') == 'menor_precio' ? 'selected' : '' }}>Menor
                                precio</option>
                            <option value="mayor_precio" {{ request('ordenar') == 'mayor_precio' ? 'selected' : '' }}>Mayor
                                precio</option>
                            <option value="az" {{ request('ordenar') == 'az' ? 'selected' : '' }}>Nombre: A - Z
                            </option>
                        </select>
                    </form>
                </div>

                @if ($products->count())
                    <div class="products-grid">
                        @foreach ($products as $product)
                            <div class="product-card">

                                <a href="{{ route('products.show', $product->idProducto ?? $product->id) }}"
                                    class="product-link"
                                    style="text-decoration: none; color: inherit; display: flex; flex-direction: column; flex-grow: 1;">

                                    <div class="product-image-wrap">
                                        <x-product-image :image="$product->imagen" :alt="$product->nombre" cssClass="img-fluid" />
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

                                <div class="product-actions" style="padding: 0 18px 20px;">
                                    <form action="{{ route('carrito.add', $product->idProducto ?? $product->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn-cart">
                                            <i class="bi bi-cart-plus me-1"></i> Añadir al carrito
                                        </button>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrapper mt-5 d-flex justify-content-center">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    <div class="empty-products">
                        <i class="bi bi-box-seam fs-1 d-block mb-3" style="opacity: 0.5;"></i>
                        No encontramos productos que coincidan con tus filtros.
                    </div>
                @endif

            </div>
        </div>
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
