@extends('layouts.app')

@section('content')
<style>
    :root{
        --envy-lime: #dfff00;
        --envy-blue: #0b2b57;
        --envy-bg: #f5f6f8;
    }

    body{
        background: var(--envy-bg);
    }

    .detail-wrapper{
        padding: 40px 0 60px;
    }

    .detail-card{
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0,0,0,.05);
        padding: 30px;
        margin-bottom: 24px;
    }

    .section-title{
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--envy-blue);
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 10px;
        margin-bottom: 18px;
    }

    .status-badge{
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
    }

    .status-proceso{ background: #fef3c7; color: #92400e; }
    .status-entregado{ background: #dcfce7; color: #166534; }
    .status-cancelado{ background: #fee2e2; color: #b91c1c; }

    .product-row{
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 0;
    }

    .product-img{
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        background: #f3f4f6;
        margin-right: 15px;
    }

    .summary-row{
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        color: #4b5563;
    }

    .summary-total{
        font-size: 1.2rem;
        font-weight: 800;
        color: #111827;
        border-top: 1px solid #e5e7eb;
        padding-top: 12px;
        margin-top: 12px;
    }
</style>

<div class="container detail-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Detalle del Pedido {{ $pedido->order_number }}</h1>
        <a href="{{ route('profile.pedidos') }}" class="btn btn-outline-dark rounded-pill px-4">Volver a mis pedidos</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="detail-card h-100">
                <h2 class="section-title">Artículos comprados</h2>
                
                @foreach($pedido->items as $item)
                    <div class="product-row">
                        <x-product-image 
                            :image="$item->product ? $item->product->imagen : null" 
                            :alt="$item->product ? $item->product->nombre : 'Producto eliminado'" 
                            cssClass="product-img" 
                        />
                        
                        <div class="flex-grow-1">
                            <strong class="d-block">{{ $item->product->nombre ?? 'Producto no disponible' }}</strong>
                            <span class="text-muted small">Cantidad: {{ $item->quantity }} x ${{ number_format($item->price, 2) }}</span>
                        </div>
                        <div class="fw-bold">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-4">
            <div class="detail-card mb-4">
                <h2 class="section-title">Resumen</h2>
                
                <div class="mb-3">
                    <span class="text-muted d-block small mb-1">Estatus del pedido:</span>
                    @php
                        $claseEstatus = 'status-proceso';
                        if($pedido->status === 'entregado') $claseEstatus = 'status-entregado';
                        elseif($pedido->status === 'cancelado') $claseEstatus = 'status-cancelado';
                    @endphp
                    <span class="status-badge {{ $claseEstatus }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                    </span>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block small mb-1">Fecha de compra:</span>
                    <strong>{{ $pedido->created_at->format('d/m/Y h:i A') }}</strong>
                </div>

                <div class="summary-row mt-4">
                    <span>Subtotal</span>
                    <span>${{ number_format($pedido->subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Envío</span>
                    <span>${{ number_format($pedido->shipping_cost, 2) }}</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>${{ number_format($pedido->total, 2) }}</span>
                </div>
            </div>

            <div class="detail-card">
                <h2 class="section-title">Dirección de envío</h2>
                <p class="text-muted small mb-0 lh-lg">
                    {{ $pedido->shipping_address }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection