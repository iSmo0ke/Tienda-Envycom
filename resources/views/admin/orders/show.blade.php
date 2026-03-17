@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid px-0">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" style="background: #dcfce7; color: #166534;" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0" style="color: var(--envy-blue);">Pedido {{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                <h5 class="fw-bold mb-3">Actualizar Estatus</h5>
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <select name="status" class="form-select border-2">
                            <option value="pendiente" {{ $order->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ $order->status == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="enviado" {{ $order->status == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            <option value="entregado" {{ $order->status == 'entregado' ? 'selected' : '' }}>Entregado</option>
                            <option value="cancelado" {{ $order->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold" style="background: var(--envy-lime); color: var(--envy-blue);">Guardar Estatus</button>
                </form>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Datos del Cliente</h5>
                <p class="mb-1"><strong>Nombre:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Correo:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                <hr>
                <h6 class="fw-bold mt-3 mb-2">Dirección de Envío Exacta:</h6>
                <p class="text-muted small lh-lg">{{ $order->shipping_address }}</p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4">Artículos del Pedido</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th>Cant.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->product->nombre ?? 'Producto no disponible' }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="fw-bold text-success">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top-2">
                            <tr>
                                <td colspan="3" class="text-end text-muted">Subtotal:</td>
                                <td class="fw-bold">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted">Envío:</td>
                                <td class="fw-bold">${{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fs-5">Total:</td>
                                <td class="fw-bold fs-5" style="color: var(--envy-blue);">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection