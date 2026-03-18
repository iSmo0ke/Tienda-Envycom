@extends('admin.layouts.admin')

@section('content')
<style>
    .stat-card {
        background: white;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
        padding: 24px;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-title {
        color: #6b7280;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--envy-blue);
        margin-top: 10px;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-green { background: #f0fdf4; color: #22c55e; }
    .icon-orange { background: #fff7ed; color: #f97316; }
    
    .data-table-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
    }
</style>

<div class="container-fluid px-0">
    <h1 class="h3 fw-bold mb-4" style="color: var(--envy-blue);">Resumen General</h1>

    <div class="row g-4 mb-5">
<div class="col-md-4">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Ventas del Mes</div>
                    <div class="stat-value">${{ number_format($ventasMes, 2) }}</div>
                </div>
                <div class="stat-icon icon-green"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Pedidos Pendientes</div>
                    <div class="stat-value">{{ $pedidosPendientes }}</div>
                </div>
                <div class="stat-icon icon-orange"><i class="bi bi-box-seam"></i></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-title">Productos Propios</div>
                    <div class="stat-value">{{ $productosLocales }}</div>
                </div>
                <div class="stat-icon icon-blue"><i class="bi bi-laptop"></i></div>
            </div>
        </div>
    </div>

    <div class="data-table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Últimos Pedidos</h5>
            <a href="#" class="btn btn-sm" style="background: var(--envy-blue); color: white;">Ver todos</a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estatus</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimosPedidos as $pedido)
                        <tr>
                            <td class="fw-bold text-secondary">{{ $pedido->order_number }}</td>
                            <td>{{ $pedido->user->name ?? 'Usuario Eliminado' }}</td>
                            <td>{{ $pedido->created_at->format('d M, Y') }}</td>
                            <td class="fw-bold">${{ number_format($pedido->total, 2) }}</td>
                            <td>
                                @php
                                    $badge = match($pedido->status) {
                                        'entregado' => 'bg-success',
                                        'cancelado' => 'bg-danger',
                                        'enviado' => 'bg-info text-dark',
                                        'en_proceso' => 'bg-primary',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} rounded-pill px-3">
                                    {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $pedido->id) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Aún no hay pedidos recientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection