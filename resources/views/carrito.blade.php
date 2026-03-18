@extends('layouts.app')

@php
    $subtotal = 0;
    foreach ($cart as $item) {
        // CAMBIO: $carrito a $cart
        $subtotal += $item['price'] * $item['quantity']; // CAMBIO: precio/cantidad a price/quantity
    }
    $envio = 0;
    $total = $subtotal + $envio;
@endphp

@section('content')
    <style>
        :root {
            --envy-lime: #dfff00;
            --envy-dark: #121012;
            --envy-gray: #4a4a4a;
            --envy-blue: #024ad8;
            --envy-light: #c6e5f8;
            --bg-soft: #f5f6f8;
        }

        body {
            background: var(--bg-soft);
            font-family: 'Segoe UI', sans-serif;
            color: var(--envy-dark);
        }

        .cart-title {
            font-weight: 800;
            color: #0b2b57;
        }

        .cart-card,
        .summary-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
            border: 1px solid #ececec;
        }

        .cart-item {
            padding: 22px 18px;
            border-bottom: 1px solid #ececec;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .product-img {
            width: 110px;
            height: 110px;
            object-fit: contain;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #eee;
            padding: 8px;
        }

        .brand {
            font-weight: 800;
            color: #102a5d;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .product-name {
            color: var(--envy-gray);
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0b2b57;
        }

        .qty-box {
            max-width: 95px;
        }

        .btn-envy {
            background: var(--envy-lime);
            border: none;
            color: #111;
            font-weight: 700;
            border-radius: 999px;
            padding: 10px 18px;
        }

        .btn-envy:hover {
            background: #d0ef00;
            color: #111;
        }

        .btn-outline-envy {
            border: 1px solid #d9d9d9;
            color: var(--envy-gray);
            border-radius: 999px;
            padding: 8px 16px;
            background: #fff;
        }

        .btn-outline-envy:hover {
            background: #f8f8f8;
        }

        .summary-card {
            padding: 24px;
            position: sticky;
            top: 20px;
        }

        .summary-title {
            font-weight: 800;
            color: #0b2b57;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--envy-gray);
        }

        .summary-total {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--envy-dark);
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart i {
            font-size: 4rem;
            color: #b8b8b8;
        }

        .empty-cart h3 {
            margin-top: 15px;
            font-weight: 800;
            color: #0b2b57;
        }

        .empty-cart p {
            color: #666;
        }

        .remove-link {
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
            background: none;
            border: none;
            padding: 0;
        }

        .remove-link:hover {
            text-decoration: underline;
        }

        .continue-link {
            text-decoration: none;
            color: #0b2b57;
            font-weight: 700;
        }

        .continue-link:hover {
            text-decoration: underline;
        }
    </style>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="cart-title mb-1">Carrito de compras</h1>
                <p class="text-secondary mb-0">Revisa tus productos antes de continuar.</p>
            </div>

            <a href="{{ url('/productos') }}" class="continue-link">
                <i class="bi bi-arrow-left"></i> Seguir comprando
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success rounded-4 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                @if (empty($cart) || count($cart) === 0)
                    <div class="cart-card empty-cart">
                        <i class="bi bi-cart-x"></i>
                        <h3>Tu carrito está vacío</h3>
                        <p>No has agregado productos todavía.</p>
                        <a href="{{ url('/productos') }}" class="btn btn-envy mt-2">
                            Ver productos
                        </a>
                    </div>
                @else
                    <div class="cart-card">
                        @foreach ($cart as $item)
                            <div class="cart-item">
                                <div class="row align-items-center g-3">
                                    <x-product-image :image="$item['image']" {{-- CAMBIADO --}} :alt="$item['name']"
                                        {{-- CAMBIADO --}} cssClass="h-16 w-16 object-cover rounded" />

                                    <div class="col-md-4 col-8">
                                        <div class="brand">{{ $item['brand'] ?? 'MARCA' }}</div> {{-- CAMBIADO --}}
                                        <div class="product-name">{{ $item['name'] }}</div> {{-- CAMBIADO --}}
                                        <div class="small text-secondary">
                                            SKU: {{ $item['sku'] ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-6">
                                        <label class="form-label small text-secondary mb-1">Cantidad</label>
                                        <form action="{{ route('carrito.update', $item['id']) }}" method="POST">
                                            @csrf
                                            <input type="number" name="cantidad" {{-- Se queda 'cantidad' porque es lo que espera el Request en el Controller --}}
                                                value="{{ $item['quantity'] }}" {{-- CAMBIADO --}} min="1"
                                                class="form-control qty-box" onchange="this.form.submit()">
                                        </form>
                                    </div>

                                    <div class="col-md-2 col-6">
                                        <label class="form-label small text-secondary mb-1">Precio</label>
                                        <div class="product-price">
                                            ${{ number_format($item['price'], 2) }} {{-- CAMBIADO --}}
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-12 text-md-end">
                                        <form action="{{ route('carrito.remove', $item['id']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="remove-link">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h3 class="summary-title mb-4">Resumen del pedido</h3>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Envío</span>
                        <span>
                            @if ($envio == 0)
                                Gratis
                            @else
                                ${{ number_format($envio, 2) }}
                            @endif
                        </span>
                    </div>

                    <hr>

                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>

                    <div class="d-grid mt-4">
                        <a href="{{ url('/checkout') }}" class="btn btn-envy btn-lg">
                            Confirmar pedido
                        </a>
                    </div>

                    <div class="d-grid mt-2">
                        <a href="{{ url('/productos') }}" class="btn btn-outline-envy">
                            Seguir comprando
                        </a>
                    </div>

                    @if (!empty($cart) && count($cart) > 0)
                        <div class="d-grid mt-2">
                            <form action="{{ route('carrito.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-envy w-100">
                                    Vaciar carrito
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
