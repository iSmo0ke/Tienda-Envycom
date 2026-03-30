@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid px-0">

    {{-- ALERTA --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" style="background: #dcfce7; color: #166534;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Pedido {{ $order->order_number }}</h1>

            <span class="badge 
                @if($order->status == 'pagado') bg-success
                @elseif($order->status == 'enviado') bg-primary
                @elseif($order->status == 'entregado') bg-dark
                @elseif($order->status == 'cancelado') bg-danger
                @else bg-secondary
                @endif">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="row g-4">

        {{-- COLUMNA IZQUIERDA --}}
        <div class="col-md-4">

            {{-- ESTADO --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                <h5 class="fw-bold mb-3 text-uppercase small text-muted">Gestión de Pedido</h5>

                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label class="small text-muted">Estado</label>

                    <select name="status" id="status" class="form-control mb-3">
                        <option value="en_proceso" {{ $order->status == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="pagado" {{ $order->status == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="enviado" {{ $order->status == 'enviado' ? 'selected' : '' }}>Enviado</option>
                        <option value="entregado" {{ $order->status == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelado" {{ $order->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>

                    {{-- CAMPOS DE ENVÍO --}}
                    <div id="shippingFields" class="d-none">
                        <label class="small">Paquetería</label>
                        <input type="text" name="carrier" class="form-control mb-2">

                        <label class="small">Número de guía</label>
                        <input type="text" name="tracking_number" class="form-control mb-3">
                    </div>

                    <button class="btn btn-dark w-100">Actualizar</button>
                </form>
            </div>

            {{-- CLIENTE --}}
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 text-uppercase small text-muted">Cliente</h5>

                <p class="fw-bold mb-1">
                    {{ $order->user->name ?? 'Cliente eliminado' }}
                </p>

                <p class="small text-muted mb-2">
                    {{ $order->user->email ?? 'Sin email' }}
                </p>

                <hr>

                <h6 class="fw-bold small mt-3">Dirección</h6>
                <p class="text-muted small bg-light p-3 border rounded">
                    {{ $order->shipping_address }}
                </p>

                <p class="small mt-2">
                    Método de pago: <strong>{{ $order->payment_method }}</strong>
                </p>
            </div>
        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="col-md-8">

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4 text-uppercase small text-muted">Productos</h5>

                <div class="table-responsive">
                    <table class="table align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <strong>
                                        {{ $item->product->nombre ?? 'Producto eliminado' }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        SKU: {{ $item->product->sku ?? 'N/A' }}
                                    </small>
                                </td>

                                <td class="text-center">
                                    ${{ number_format($item->price, 2) }}
                                </td>

                                <td class="text-center">
                                    {{ $item->quantity }}
                                </td>

                                <td class="text-end fw-bold text-success">
                                    ${{ number_format($item->price * $item->quantity, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end">Subtotal</td>
                                <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>

                            <tr>
                                <td colspan="3" class="text-end">Envío</td>
                                <td class="text-end">${{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>

                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    const status = document.getElementById('status');
    const shipping = document.getElementById('shippingFields');

    function toggleShipping() {
        if (status.value === 'enviado' || status.value === 'entregado') {
            shipping.classList.remove('d-none');
        } else {
            shipping.classList.add('d-none');
        }
    }

    toggleShipping();
    status.addEventListener('change', toggleShipping);

});
</script>

@endsection