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
    }

    .detail-card, .img-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
        border: 1px solid #ececec;
    }

    .img-card {
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-main-img {
        width: 100%;
        max-height: 500px;
        object-fit: contain;
    }

    .detail-title {
        font-weight: 800;
        font-size: 2rem;
        color: #0b2b57;
    }

    .brand-badge {
        font-weight: 800;
        text-transform: uppercase;
        color: #0b2b57;
        letter-spacing: 1px;
    }

    .detail-price {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0b2b57;
    }

    .btn-add-cart {
        background: var(--envy-lime);
        border-radius: 999px;
        font-weight: 700;
        padding: 12px;
        transition: 0.3s;
    }

    .btn-add-cart:hover {
        background: #d0ef00;
        transform: translateY(-2px);
    }

    .breadcrumb a {
        text-decoration: none;
        font-weight: 600;
        color: #0b2b57;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }
</style>

<div class="container py-5">
    <nav class="breadcrumb mb-4">
        <a href="{{ route('products.index') }}">Catálogo</a> /
        <span>{{ $product->categoria }}</span> /
        <span class="fw-bold">{{ $product->nombre }}</span>
    </nav>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm mb-4 fade show">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">

        <div class="col-lg-6">
            <div class="img-card h-100">
                <x-product-image 
                    :image="$product->imagen" 
                    :alt="$product->nombre" 
                    cssClass="product-main-img"
                />
            </div>
        </div>

        <div class="col-lg-6">
            <div class="detail-card p-4 h-100 d-flex flex-column">

                <div>
                    <span class="brand-badge">{{ $product->marca ?? 'Sin marca' }}</span>
                    <h1 class="detail-title mt-2">{{ $product->nombre }}</h1>

                    <p class="text-muted mb-2">
                        Modelo: {{ $product->modelo ?? 'N/A' }} | SKU: {{ $product->numParte }}
                    </p>

                    <div class="detail-price mb-3">
                        ${{ number_format($product->precio, 2) }} MXN
                    </div>

                    <p class="text-muted mb-4">
                        {{ $product->descripcion_corta ?? 'Sin descripción disponible.' }}
                    </p>
                </div>

                <form action="{{ route('carrito.add', $product->id) }}" method="POST" class="mt-auto">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="d-flex gap-3 align-items-center mb-3">
                        <label class="fw-bold">Cantidad:</label>
                        <input 
                            type="number" 
                            name="quantity" 
                            value="1" 
                            min="1" 
                            max="10"
                            class="form-control"
                            style="width: 80px;"
                        >
                    </div>

                    <button type="submit" class="btn btn-add-cart w-100">
                        <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
                    </button>

                    @error('quantity')
                        <p class="text-danger small mt-2">{{ $message }}</p>
                    @enderror
                </form>

            </div>
        </div>
    </div>

    @php $specs = json_decode($product->especificaciones, true); @endphp

    @if(!empty($specs))
        <div class="mt-5">
            <div class="detail-card p-4">
                <h3 class="fw-bold mb-4">Especificaciones Técnicas</h3>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            @foreach($specs as $key => $value)
                                <tr>
                                    <th style="width:30%" class="bg-light">
                                        {{ $key }}
                                    </th>
                                    <td>
                                        {{ is_array($value) ? implode(', ', $value) : $value }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @endif

</div>

{{-- AUTO CERRAR ALERTA --}}
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