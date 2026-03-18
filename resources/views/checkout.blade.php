@extends('layouts.app')

@section('content')
    <style>
        :root {
            --envy-lime: #dfff00;
            --envy-dark: #121012;
            --envy-blue: #0b2b57;
            --envy-gray: #6b7280;
            --envy-bg: #f5f6f8;
        }

        body {
            background: var(--envy-bg);
        }

        .checkout-wrapper {
            padding: 40px 0 60px;
        }

        .checkout-title {
            font-size: 2.4rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .checkout-subtitle {
            color: var(--envy-gray);
            margin-bottom: 28px;
        }

        .checkout-card,
        .summary-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
        }

        .checkout-card {
            padding: 24px;
        }

        .summary-card {
            padding: 24px;
            position: sticky;
            top: 20px;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--envy-blue);
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .form-label {
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
        }

        .delivery-option {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .delivery-option input {
            margin-right: 10px;
        }

        .summary-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #4b5563;
        }

        .summary-total {
            font-size: 1.2rem;
            font-weight: 800;
            color: #111827;
        }

        .btn-envy {
            width: 100%;
            border: none;
            border-radius: 999px;
            background: var(--envy-lime);
            color: #111;
            font-weight: 800;
            padding: 13px 18px;
            transition: .2s ease;
        }

        .btn-envy:hover {
            background: #d3f200;
        }
    </style>

    <div class="container checkout-wrapper">
        <h1 class="checkout-title">Checkout</h1>
        <p class="checkout-subtitle">Completa tu información de envío y selecciona el método de entrega.</p>
        @if ($errors->any())
            <div class="alert alert-danger mb-4"
                style="color: #dc2626; background-color: #fef2f2; padding: 15px; border-radius: 10px; border: 1px solid #f87171;">
                <strong>¡Ojo! Falta algo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf <div class="row g-4">
                <div class="col-lg-8">
<div class="checkout-card mb-4">
                        <h2 class="section-title">Dirección de envío</h2>

                        @if ($direcciones->count() > 0)
                            <div class="mb-4">
                                <label class="form-label">Mis Direcciones Guardadas</label>
                                <select class="form-select" name="address_id" id="address_id">
                                    <option value="">-- Selecciona una dirección --</option>
                                    @foreach ($direcciones as $direccion)
                                        <option value="{{ $direccion->id }}" {{ $direccion->is_default ? 'selected' : '' }}>
                                            {{ $direccion->alias ?? 'Dirección' }} - {{ $direccion->calle_numero }},
                                            {{ $direccion->colonia }} (CP: {{ $direccion->codigo_postal }})
                                        </option>
                                    @endforeach
                                    <option value="new">+ Agregar nueva dirección</option>
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="address_id" value="new">
                        @endif

                        <div id="new-address-form" style="{{ $direcciones->count() > 0 ? 'display: none;' : '' }}">
                            <h5 class="form-label mb-3">Ingresar nueva dirección</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Quien recibe (Nombre completo)</label>
                                    <input type="text" name="receptor_name" class="form-control"
                                        placeholder="Nombre de quien recibe" value="{{ auth()->user()->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="phone" class="form-control" placeholder="10 dígitos">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Calle y número</label>
                                    <input type="text" name="calle_numero" class="form-control"
                                        placeholder="Av. Reforma 123">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Colonia</label>
                                    <input type="text" name="colonia" class="form-control" placeholder="Centro">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Municipio / Alcaldía</label>
                                    <input type="text" name="municipio_alcaldia" class="form-control"
                                        placeholder="Tehuacán">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Código postal</label>
                                    <input type="text" name="codigo_postal" class="form-control" placeholder="75700">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado</label>
                                    <input type="text" name="estado" class="form-control" placeholder="Puebla">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Referencias (Opcional)</label>
                                    <input type="text" name="referencias" class="form-control"
                                        placeholder="Entre calles, color de casa...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Seccion metodo de entrega --}}

                    <div class="checkout-card">
                        <h2 class="section-title">Método de entrega</h2>

                        <label class="delivery-option d-block">
                            <input type="radio" name="entrega" checked>
                            <strong>Envío estándar</strong>
                            <div class="text-muted small mt-1">Entrega estimada de 3 a 5 días hábiles.</div>
                        </label>

                        <label class="delivery-option d-block">
                            <input type="radio" name="entrega">
                            <strong>Entrega express</strong>
                            <div class="text-muted small mt-1">Entrega estimada de 1 a 2 días hábiles.</div>
                        </label>

                        <label class="delivery-option d-block">
                            <input type="radio" name="entrega">
                            <strong>Recoger en tienda</strong>
                            <div class="text-muted small mt-1">Recoge tu pedido directamente en ENVYCOM.</div>
                        </label>
                    </div>
                </div>

                {{-- Seccion de resumen de pedido --}}
                <div class="col-lg-4">
                    <div class="summary-card">
                        <h3 class="summary-title">Resumen del pedido</h3>

                        <div class="mb-3">
                            @foreach ($cart as $item) {{-- CAMBIO --}}
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>{{ $item['quantity'] }}x {{ $item['name'] }}</span> {{-- CAMBIO --}}
                                    <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span> {{-- CAMBIO --}}
                                </div>
                            @endforeach
                        </div>
                        <hr>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Envío</span>
                            <span>${{ number_format($costoEnvio, 2) }}</span>
                        </div>

                        <hr>

                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-envy w-100">
                                Proceder al pago
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


    </div>
@endsection
