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

    .orders-wrapper{
        padding: 40px 0 60px;
    }

    .orders-title{
        font-size: 2.3rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 24px;
    }

    .order-card{
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0,0,0,.05);
        padding: 24px;
        margin-bottom: 20px;
    }

    .order-header{
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 18px;
    }

    .order-folio{
        font-size: 1.1rem;
        font-weight: 800;
        color: #1E2A3B;
    }

    .status-badge{
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-proceso{
        background: #fef3c7;
        color: #92400e;
    }

    .status-entregado{
        background: #dcfce7;
        color: #166534;
    }

    .product-row{
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #f1f5f9;
        padding: 12px 0;
    }

    .order-total{
        text-align: right;
        font-weight: 800;
        font-size: 1.05rem;
        margin-top: 10px;
    }
</style>

<div class="container orders-wrapper">
    <h1 class="orders-title">Historial de pedidos</h1>

    @forelse($pedidos as $pedido)
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-folio">Pedido {{ $pedido->order_number }}</div>
                    <div class="text-muted small">Fecha: {{ $pedido->created_at->format('d/m/Y') }}</div>
                </div>

                <div>
                    @php
                        $claseEstatus = 'status-proceso'; // Por defecto amarillo
                        if($pedido->status === 'entregado') {
                            $claseEstatus = 'status-entregado'; // Verde si ya se entregó
                        } elseif($pedido->status === 'cancelado') {
                            $claseEstatus = 'text-danger'; // Rojo si se canceló
                        }
                    @endphp
                    
                    <span class="status-badge {{ $claseEstatus }}">
                        {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                    </span>
                </div>
            </div>

            @foreach($pedido->items as $item)
                <div class="product-row">
                    <div>
                        <strong>{{ $item->product->nombre ?? 'Producto no disponible' }}</strong>
                        <div class="text-muted small">Cantidad: {{ $item->quantity }}</div>
                    </div>
                    <div>${{ number_format($item->price * $item->quantity, 2) }}</div>
                </div>
            @endforeach

            <div class="order-total d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                <a href="{{ route('profile.pedido.detalle', $pedido->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Ver detalle completo</a>
                <span>Total: ${{ number_format($pedido->total, 2) }}</span>
            </div>
        </div>
    @empty
        <div class="alert alert-light border rounded-4 text-center py-5">
            <h4 class="text-muted mb-0">Aún no tienes pedidos registrados.</h4>
            <a href="{{ route('products.index') }}" class="btn btn-dark mt-3 rounded-pill px-4">Ir a la tienda</a>
        </div>
    @endforelse
</div>
@endsection