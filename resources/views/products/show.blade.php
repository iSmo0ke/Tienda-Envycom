@extends('layouts.app')

@section('content')
    <style>
        :root {
            --envy-lime: #dfff00;
            --envy-dark: #121012;
            --envy-gray: #4a4a4a;
            --envy-blue: #024ad8;
            --bg-soft: #f5f6f8;
        }

        body {
            background: var(--bg-soft);
            font-family: 'Segoe UI', sans-serif;
            color: var(--envy-dark);
        }

        .detail-title {
            font-weight: 800;
            color: #0b2b57;
            font-size: 2rem;
            line-height: 1.2;
        }

        .detail-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
            border: 1px solid #ececec;
            padding: 30px;
        }

        .img-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
            border: 1px solid #ececec;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .product-main-img {
            width: 100%;
            max-height: 550px; 
            object-fit: contain;
            border-radius: 14px;
        }

        .brand-badge {
            font-weight: 800;
            color: #102a5d;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: block;
        }

        .detail-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0b2b57;
            margin-bottom: 20px;
        }

        .btn-add-cart {
            background: var(--envy-lime);
            border: none;
            color: #111;
            font-weight: 700;
            border-radius: 999px;
            padding: 12px 24px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-add-cart:hover {
            background: #d0ef00;
            color: #111;
            transform: translateY(-2px);
        }

        .info-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            color: var(--envy-gray);
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .continue-link {
            text-decoration: none;
            color: #0b2b57;
            font-weight: 700;
            transition: opacity 0.3s;
        }

        .continue-link:hover {
            text-decoration: underline;
            opacity: 0.8;
        }
    </style>

    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ url('/productos') }}" class="continue-link">
                <i class="bi bi-arrow-left"></i> Volver al catálogo
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-4 shadow-sm mb-4 fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="img-card">
                    <x-product-image 
                        :image="$product->imagen" 
                        :alt="$product->nombre" 
                        cssClass="product-main-img" 
                    />
                </div>
            </div>

            <div class="col-lg-6">
                <div class="detail-card h-100 d-flex flex-column">
                    <div>
                        <span class="brand-badge">{{ $product->marca ?? 'Sin marca' }}</span>
                        <h1 class="detail-title mb-3">{{ $product->nombre }}</h1>

                        <div class="detail-price">
                            ${{ number_format($product->precio, 2) }} <span class="fs-5 text-secondary">MXN</span>
                        </div>

                        <p style="color: var(--envy-gray); line-height: 1.6; margin-bottom: 25px;">
                            {{ $product->descripcion_corta }}
                        </p>

                        <div class="bg-light p-3 rounded-4 mb-4" style="border: 1px solid #ececec;">
                            <ul class="list-unstyled info-list mb-0">
                                <li><strong>Modelo:</strong> {{ $product->modelo }}</li>
                                <li><strong>Número de Parte:</strong> {{ $product->numParte }}</li>
                                <li><strong>Categoría:</strong> {{ $product->categoria }} > {{ $product->subcategoria }}</li>
                                <li>
                                    <strong>Disponibilidad:</strong>
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">
                                        Consultar stock en sucursales
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <form action="{{ route('carrito.add', $product->id) }}" method="POST" class="mt-auto">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-add-cart btn-lg">
                                <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if (is_array($product->especificaciones) && count($product->especificaciones) > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="detail-card">
                        <h3 class="detail-title fs-4 mb-4">Especificaciones Técnicas</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-unstyled info-list" style="column-count: 2; column-gap: 40px;">
                                    @foreach ($product->especificaciones as $especificacion)
                                        @if (is_string($especificacion))
                                            <li><i class="bi bi-check2-circle text-success me-2"></i> {{ $especificacion }}
                                            </li>
                                        @elseif(is_array($especificacion))
                                            <li><i class="bi bi-check2-circle text-success me-2"></i>
                                                {{ implode(': ', $especificacion) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
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