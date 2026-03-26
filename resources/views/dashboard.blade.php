@extends('layouts.app')

@section('content')
<style>
    :root {
        --envy-lime: #dfff00;
        --envy-blue: #0b2b57;
        --envy-bg: #f5f6f8;
    }

    body {
        background: var(--envy-bg);
    }

    .dashboard-wrapper {
        padding: 40px 0 60px;
    }

    /* --- ESTILOS DEL MENÚ LATERAL --- */
    .nav-pills .nav-link {
        color: #4b5563;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .nav-pills .nav-link:hover {
        background-color: #e5e7eb;
        color: var(--envy-blue);
    }
    
    .nav-pills .nav-link.active {
        background-color: var(--envy-blue);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(11, 43, 87, 0.15);
    }

    /* --- TUS ESTILOS DE PEDIDOS (RECICLADOS INTACTOS) --- */
    .order-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0,0,0,.05);
        padding: 24px;
        margin-bottom: 20px;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 18px;
    }

    .order-folio {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1E2A3B;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-proceso { background: #fef3c7; color: #92400e; }
    .status-entregado { background: #dcfce7; color: #166534; }
    .status-enviado { background: #e0e7ff; color: #3730a3; }

    .product-row {
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #f1f5f9;
        padding: 12px 0;
    }

    .order-total {
        text-align: right;
        font-weight: 800;
        font-size: 1.05rem;
        margin-top: 10px;
    }

    .generic-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0,0,0,.05);
        padding: 30px;
    }
</style>

<div class="container dashboard-wrapper">
    <h1 class="fw-bold mb-4" style="font-size: 2.3rem; color: #111827;">Mi Cuenta</h1>

    <div class="row g-4">
        
        <div class="col-md-3">
            
            <div class="generic-card p-3 mb-4 d-flex align-items-center gap-3">
                <div class="bg-light rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-person-fill fs-3" style="color: var(--envy-blue);"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted small fw-bold text-uppercase">Hola,</p>
                    <h5 class="fw-bold mb-0 text-truncate" style="max-width: 140px; color: var(--envy-blue);">{{ Auth::user()->name }}</h5>
                </div>
            </div>

            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                
                <button class="nav-link active text-start" id="v-pills-pedidos-tab" data-bs-toggle="pill" data-bs-target="#v-pills-pedidos" type="button" role="tab">
                    <i class="bi bi-box-seam fs-5 me-2 align-middle"></i> Historial de Pedidos
                </button>
                
                <button class="nav-link text-start" id="v-pills-direcciones-tab" data-bs-toggle="pill" data-bs-target="#v-pills-direcciones" type="button" role="tab">
                    <i class="bi bi-geo-alt fs-5 me-2 align-middle"></i> Mis Direcciones
                </button>
                
                <button class="nav-link text-start" id="v-pills-facturacion-tab" data-bs-toggle="pill" data-bs-target="#v-pills-facturacion" type="button" role="tab">
                    <i class="bi bi-building fs-5 me-2 align-middle"></i> Datos Fiscales
                </button>
                
                <hr class="my-2">

                <a href="{{ route('profile.edit') }}" class="nav-link text-start bg-light text-dark border">
                    <i class="bi bi-shield-lock fs-5 me-2 align-middle"></i> Seguridad y Contraseña
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">
                
                <div class="tab-pane fade show active" id="v-pills-pedidos" role="tabpanel">
                    
                    @forelse($pedidos as $pedido)
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <div class="order-folio">Pedido {{ $pedido->order_number }}</div>
                                    <div class="text-muted small">Fecha: {{ $pedido->created_at->format('d/m/Y') }}</div>
                                </div>

                                <div>
                                    @php
                                        $claseEstatus = 'status-proceso'; // Amarillo
                                        if($pedido->status === 'entregado') $claseEstatus = 'status-entregado'; // Verde
                                        elseif($pedido->status === 'enviado') $claseEstatus = 'status-enviado'; // Azul
                                        elseif($pedido->status === 'cancelado') $claseEstatus = 'text-danger bg-light border border-danger'; // Rojo
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
                                    <div class="fw-bold text-secondary">${{ number_format($item->price * $item->quantity, 2) }}</div>
                                </div>
                            @endforeach

                            <div class="order-total d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <a href="{{ route('profile.pedido.detalle', $pedido->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">Ver detalle completo</a>
                                <span style="color: var(--envy-blue);">Total: ${{ number_format($pedido->total, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="generic-card text-center py-5">
                            <i class="bi bi-bag-x display-1 text-muted opacity-50 mb-3 d-block"></i>
                            <h4 class="fw-bold mb-3">Aún no tienes pedidos registrados.</h4>
                            <p class="text-muted mb-4">¡Explora nuestro catálogo y encuentra lo mejor en tecnología!</p>
                            <a href="{{ route('products.index') }}" class="btn rounded-pill px-5 py-2 fw-bold shadow-sm" style="background: var(--envy-lime); color: var(--envy-blue);">Ir a la tienda</a>
                        </div>
                    @endforelse
                </div>

<div class="tab-pane fade" id="v-pills-direcciones" role="tabpanel">
    <div class="generic-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bold mb-0" style="color: var(--envy-blue);">Mi Libreta de Direcciones</h4>
            <button class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevaDireccion" aria-expanded="false">
                <i class="bi bi-plus-lg me-1"></i> Nueva Dirección
            </button>
        </div>

        <div class="row g-3 mb-4">
            {{-- Usamos la relación del usuario para traer sus direcciones --}}
            @forelse(Auth::user()->addresses ?? [] as $direccion)
                <div class="col-md-6">
                    <div class="p-3 border rounded-4 bg-light position-relative h-100">
                        @if($direccion->is_default)
                            <span class="badge position-absolute top-0 end-0 m-3" style="background: var(--envy-lime); color: var(--envy-blue);">Principal</span>
                        @endif
                        <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $direccion->alias ?? 'Dirección de envío' }}</h6>
                        <p class="small text-muted mb-1 fw-bold">{{ $direccion->receptor_name }} - {{ $direccion->phone }}</p>
                        <p class="small text-muted mb-0 lh-base">
                            {{ $direccion->calle_numero }}, {{ $direccion->colonia }}<br>
                            {{ $direccion->municipio_alcaldia }}, {{ $direccion->estado }} (CP: {{ $direccion->codigo_postal }})<br>
                            <span class="fst-italic text-secondary">Ref: {{ $direccion->referencias ?? 'Sin referencias' }}</span>
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0">No tienes direcciones guardadas. ¡Agrega una para agilizar tus compras!</p>
                </div>
            @endforelse
        </div>

        <div class="collapse mt-4" id="formNuevaDireccion">
            <div class="card card-body border-0 shadow-sm rounded-4" style="background-color: #f8fafc;">
                <h5 class="fw-bold mb-3" style="color: var(--envy-blue);">Ingresar nueva dirección</h5>
                
                <form action="{{ route('profile.address.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Quien recibe (Nombre completo)</label>
                            <input type="text" name="receptor_name" class="form-control" placeholder="Nombre de quien recibe" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Teléfono</label>
                            <input type="text" name="phone" class="form-control" placeholder="10 dígitos" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Calle y número</label>
                            <input type="text" name="calle_numero" class="form-control" placeholder="Av. Reforma 123" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Colonia</label>
                            <input type="text" name="colonia" class="form-control" placeholder="Centro" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Municipio / Alcaldía</label>
                            <input type="text" name="municipio_alcaldia" class="form-control" placeholder="Tehuacán" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Código postal</label>
                            <input type="text" name="codigo_postal" class="form-control" placeholder="75700" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Estado</label>
                            <input type="text" name="estado" class="form-control" placeholder="Puebla" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Referencias (Opcional)</label>
                            <input type="text" name="referencias" class="form-control" placeholder="Entre calles, color de casa...">
                        </div>
                    </div>
                    
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-toggle="collapse" data-bs-target="#formNuevaDireccion">Cancelar</button>
                        <button type="submit" class="btn rounded-pill px-4 fw-bold" style="background: var(--envy-lime); color: var(--envy-blue);">
                            <i class="bi bi-save me-2"></i>Guardar Dirección
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

                <div class="tab-pane fade" id="v-pills-facturacion" role="tabpanel">
                    <div class="generic-card">
                        <h4 class="fw-bold mb-4" style="color: var(--envy-blue);">Datos Fiscales</h4>
                        
                        <div class="alert alert-primary bg-opacity-10 border-0 rounded-4 p-4 d-flex align-items-start gap-3">
                            <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Facturación Automática próximamente</h6>
                                <p class="mb-0 small text-muted">Estamos trabajando para que puedas guardar tu Constancia de Situación Fiscal y generar tus facturas con un solo clic al momento de pagar.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection