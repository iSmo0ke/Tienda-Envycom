@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <div class="page-header mb-4" style="background: linear-gradient(135deg, var(--envy-blue) 0%, #1a4b8c 100%); padding: 30px; border-radius: 16px; color: white;">
        <h1 class="fw-bold m-0 fs-3">Gestión de Pedidos</h1>
        <p class="mt-2 mb-0" style="color: #cbd5e1;">Administra las ventas, envíos y estatus de los pedidos.</p>
    </div>

    <div class="table-card" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr style="text-transform: uppercase; font-size: 0.75rem; color: #64748b; letter-spacing: 1px;">
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estatus</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="fw-bold text-dark">{{ $order->order_number }}</td>
                            <td>
                                <div class="fw-bold">{{ $order->user->name ?? 'Usuario Eliminado' }}</div>
                                <div class="text-muted small">{{ $order->user->email ?? '' }}</div>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y h:i A') }}</td>
                            <td class="fw-bold" style="color: var(--envy-blue);">${{ number_format($order->total, 2) }}</td>
                            <td>
                                @php
                                    $badgeClass = match($order->status) {
                                        'entregado' => 'bg-success',
                                        'cancelado' => 'bg-danger',
                                        'enviado' => 'bg-info text-dark',
                                        'en_proceso' => 'bg-primary',
                                        default => 'bg-warning text-dark', // pendiente
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm" style="background: #eff6ff; color: #3b82f6; border-radius: 8px;">
                                    <i class="bi bi-eye-fill"></i> Ver Detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Aún no hay pedidos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection