@extends('layouts.app')

@section('content')
<style>
    :root {
        --envy-lime: #dfff00;
    }

    body {
        background-color: #f9fafb;
    }

    .payment-wrapper {
        max-width: 600px;
        margin: 40px auto;
    }

    .payment-card {
        background: #ffffff;
        border-radius: 0px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 40px;
        border: 1px solid #e5e7eb;
    }

    .secure-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f3f4f6;
        padding: 8px 16px;
        font-size: 0.85rem;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
    }

    .form-control-custom {
        border-radius: 0px;
        padding: 14px 16px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
    }

    .form-control-custom:focus {
        border-color: #111827;
        box-shadow: none;
    }

    .card-icons {
        font-size: 1.5rem;
        color: #4b5563;
    }

    .label-b2b {
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        display: block;
    }
</style>

<div class="container">
    <div class="payment-wrapper">

        <a href="{{ route('checkout.index') }}" class="btn btn-link text-muted mb-3 p-0">
            <i class="bi bi-arrow-left"></i> Volver a datos de envío
        </a>

        <div class="payment-card">

            <div class="d-flex justify-content-between mb-4">
                <div>
                    <div class="secure-badge">
                        <i class="bi bi-shield-lock-fill"></i> Pago 100% Seguro
                    </div>
                    <h2 class="fw-bold">Detalles de Pago</h2>
                    <p class="text-muted mb-0">Total a pagar: <strong class="text-dark fs-5">${{ number_format($total, 2) }}</strong></p>
                </div>
                    <i class="bi bi-credit-card-2-front-fill card-icons"></i>
            </div>

            @if ($errors->any())
            <div class="alert alert-warning shadow-sm mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            {{-- SCRIPTS --}}
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://js.openpay.mx/openpay.v1.min.js"></script>
            <script src="https://js.openpay.mx/openpay-data.v1.min.js"></script>

            {{-- FORM --}}
            <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                @csrf

                <input type="hidden" name="token_id" id="token_id">
                <input type="hidden" name="device_session_id" id="device_session_id">

                <div class="row g-3">

                    <div class="col-12">
                        <label class="label-b2b">Nombre en la tarjeta</label>
                        <input type="text" class="form-control form-control-custom"
                            data-openpay-card="holder_name" placeholder="Tarjeta habiente" autocomplete="off" required>
                    </div>

                    <div class="col-12">
                        <label class="label-b2b">Número de tarjeta</label>
                        <input type="text" class="form-control form-control-custom"
                            data-openpay-card="card_number" maxlength="19" placeholder="0000 0000 0000 0000" autocomplete="off" maxlength="19" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-b2b">Mes (MM)</label>
                        <input type="text" class="form-control form-control-custom"
                            data-openpay-card="expiration_month" placeholder="12" maxlength="2" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-b2b">Año (AA)</label>
                        <input type="text" class="form-control form-control-custom"
                            data-openpay-card="expiration_year" placeholder="28" maxlength="2" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-b2b">CVV</label>
                        <input type="password" class="form-control form-control-custom"
                            data-openpay-card="cvv2" placeholder="*" autocomplete="off" maxlength="4" required>
                    </div>

                </div>

                <div id="payment-errors" class="alert alert-danger mt-3 d-none"></div>

                <button type="submit" id="pay-button" class="btn w-100 mt-4 py-3 fw-bold fs-5 shadow-sm" style="background-color: #25D366 !important; color: #111827 !important; border-radius: 0px; border: none;">
                    <i class="bi bi-lock-fill me-2"></i> Pagar Pedido
                </button>

                 <div class="text-center mt-3">
                    <small class="text-muted"><i class="bi bi-info-circle"></i> Tus datos están encriptados y no se guardan en nuestros servidores.</small>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        OpenPay.setId('{{ config("services.openpay.merchant_id") }}');
        OpenPay.setApiKey('{{ config("services.openpay.public_key") }}');
        OpenPay.setSandboxMode(true);

        var deviceSessionId = OpenPay.deviceData.setup("payment-form");
        $('#device_session_id').val(deviceSessionId);

        $('#payment-form').submit(function(event) {
            event.preventDefault();

            let btn = $('#pay-button');

            btn.prop('disabled', true)
                .html('Procesando...');

            $('#payment-errors').addClass('d-none');

            // limpiar tarjeta
            let input = $('input[data-openpay-card="card_number"]');
            input.val(input.val().replace(/\s+/g, ''));

            OpenPay.token.extractFormAndCreate(
                'payment-form',
                success,
                error
            );
        });

        function success(response) {
            $('#token_id').val(response.data.id);
            $('#payment-form')[0].submit();
        }

        function error(err) {
            console.log(err);

            $('#pay-button')
                .prop('disabled', false)
                .html('Pagar Pedido');

            let msg = err.description || "Error en el pago";

            if (err.status == 422) msg = "Tarjeta inválida";
            if (err.status == 401) msg = "Error de configuración";

            $('#payment-errors')
                .removeClass('d-none')
                .text(msg);
        }

    });
</script>

@endsection