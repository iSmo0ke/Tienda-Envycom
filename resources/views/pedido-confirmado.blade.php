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

        .thankyou-wrapper {
            padding: 60px 0;
        }

        .thankyou-card {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
            padding: 36px;
            text-align: center;
        }

        .success-icon {
            width: 82px;
            height: 82px;
            background: var(--envy-lime);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 800;
            margin: 0 auto 22px;
            color: #111;
        }

        .thankyou-title {
            font-size: 2.1rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 10px;
        }

        .thankyou-subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }

        .order-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            text-align: left;
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .btn-order-success {
            display: inline-block;
            margin-top: 26px;
            padding: 13px 22px;
            border-radius: 999px;
            background: #1E2A3B;
            color: #fff;
            font-weight: 800;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .btn-order-success:hover {
            opacity: 0.9;
            color: #fff;
        }
    </style>

    <div class="container thankyou-wrapper">
        <div class="thankyou-card">
            <div class="success-icon">✓</div>

            <h1 class="thankyou-title">¡Gracias por tu compra!</h1>
            <p class="thankyou-subtitle">
                Tu pedido ha sido registrado correctamente. Te compartimos el resumen para tu tranquilidad.
            </p>

            <div class="order-box">
                <div class="order-row">
                    <strong>Número de orden</strong>
                    <span>{{ $order->order_number }}</span>
                </div>
                <div class="order-row">
                    <strong>Fecha</strong>
                    <span>{{ $order->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="order-row">
                    <strong>Método de entrega</strong>
                    <span>Envío estándar</span>
                </div>
                <div class="order-row">
                    <strong>Total</strong>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="btn-order-success">
                Ver mis pedidos
            </a>
        </div>
    </div>
@endsection