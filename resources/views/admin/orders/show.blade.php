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
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--envy-blue);">Pedido {{ $order->order_number }}</h1>
            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Volver al listado</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                <h5 class="fw-bold mb-3 text-uppercase small text-muted">Gestión de Envío</h5>
                
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Estatus del Pedido</label>
                        <select name="status" id="orderStatus" class="form-select border-2">
                            <option value="pendiente" {{ $order->status == 'pendiente' ? 'selected' : '' }}>Pendiente de Pago</option>
                            <option value="en_proceso" {{ $order->status == 'en_proceso' ? 'selected' : '' }}>En Proceso (Preparando)</option>
                            <option value="enviado" {{ $order->status == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            <option value="entregado" {{ $order->status == 'entregado' ? 'selected' : '' }}>Entregado</option>
                            <option value="cancelado" {{ $order->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <div id="shippingFields" class="p-3 bg-light rounded-3 mb-3 border d-none">
                        <h6 class="fw-bold mb-3 fs-6"><i class="bi bi-truck me-2"></i>Datos de Paquetería</h6>
                        
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1">Empresa Transportista</label>
                            <select name="shipping_carrier" class="form-select form-select-sm">
                                <option value="">Seleccione paquetería...</option>
                                <option value="DHL" {{ $order->shipping_carrier == 'DHL' ? 'selected' : '' }}>DHL</option>
                                <option value="FedEx" {{ $order->shipping_carrier == 'FedEx' ? 'selected' : '' }}>FedEx</option>
                                <option value="Estafeta" {{ $order->shipping_carrier == 'Estafeta' ? 'selected' : '' }}>Estafeta</option>
                                <option value="Redpack" {{ $order->shipping_carrier == 'Redpack' ? 'selected' : '' }}>Redpack</option>
                                <option value="Entrega Local" {{ $order->shipping_carrier == 'Entrega Local' ? 'selected' : '' }}>Entrega Local (Directa)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="form-label small text-muted mb-1">Número de Guía (Tracking)</label>
                            <input type="text" name="tracking_number" class="form-control form-control-sm" value="{{ $order->tracking_number }}" placeholder="Ej. 1234567890">
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 fw-bold mt-2" style="background: var(--envy-lime); color: var(--envy-blue);">
                        Actualizar Pedido
                    </button>
                </form>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 text-uppercase small text-muted">Información del Cliente</h5>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded-circle p-3 me-3">
                        <i class="bi bi-person-fill fs-4 text-secondary"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold">{{ $order->user->name ?? 'Cliente Eliminado' }}</p>
                        <a href="mailto:{{ $order->user->email ?? '' }}" class="text-decoration-none small">{{ $order->user->email ?? 'N/A' }}</a>
                    </div>
                </div>

                <hr class="text-muted">
                
                <h6 class="fw-bold mt-3 mb-2 small"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Dirección de Envío</h6>
                <p class="text-muted small lh-lg bg-light p-3 rounded-3 border mb-0">
                    {{ $order->shipping_address }}
                </p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 text-uppercase small text-muted">Artículos del Pedido</h5>
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-secondary small">Producto</th>
                                <th class="text-secondary small text-center">Precio Unit.</th>
                                <th class="text-secondary small text-center">Cant.</th>
                                <th class="text-secondary small text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->product->nombre ?? 'Producto no disponible' }}</div>
                                        <small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-center">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="fw-bold text-end text-success">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top-2">
                            <tr>
                                <td colspan="3" class="text-end text-muted small pb-1 pt-3">Subtotal:</td>
                                <td class="fw-bold text-end pb-1 pt-3">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted small py-1">Envío:</td>
                                <td class="fw-bold text-end py-1">${{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fs-6 pt-3">Total pagado:</td>
                                <td class="fw-bold fs-5 text-end pt-3" style="color: var(--envy-blue);">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('orderStatus');
        const shippingFields = document.getElementById('shippingFields');

        // Función que evalúa si mostrar o esconder
        function toggleShippingFields() {
            // Si es enviado o entregado, mostramos la info de la paquetería
            if (statusSelect.value === 'enviado' || statusSelect.value === 'entregado') {
                shippingFields.classList.remove('d-none');
            } else {
                shippingFields.classList.add('d-none');
            }
        }

        // Ejecutar al cargar la página (por si ya estaba en estatus "enviado")
        toggleShippingFields();

        // Escuchar cada que el administrador cambie la opción del select
        statusSelect.addEventListener('change', toggleShippingFields);
    });
</script>
@endsection