@extends('layouts.app')

@section('content')
<style>
    :root {
        --envy-lime: #dfff00;
    }
    body { background-color: #f9fafb; }
    
    .payment-wrapper {
        max-width: 600px;
        margin: 40px auto;
    }
    
    .payment-card {
        background: #ffffff;
        border-radius: 0px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        padding: 40px;
        border: 1px solid #e5e7eb;
    }

    .secure-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f3f4f6; 
        color: #374151; 
        padding: 8px 16px;
        border-radius: 0px; 
        font-weight: normal; 
        font-size: 0.85rem;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
    }

    .form-control-custom {
        border-radius: 0px; 
        padding: 14px 16px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        transition: all 0.3s ease;
        font-weight: normal; 
        color: #111827;
    }

    .form-control-custom:focus {
        border-color: #111827; 
        background-color: #fff;
        box-shadow: none;
        outline: none;
    }

    .card-icons {
        font-size: 1.5rem;
        color: #4b5563; 
        display: flex;
        gap: 10px;
    }

    .label-b2b {
        color: #4b5563;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        display: block;
    }
</style>

<div class="container">
    <div class="payment-wrapper">
        
        <a href="{{ route('checkout.index') }}" class="btn btn-link text-decoration-none text-muted mb-3 p-0" style="border-radius: 0;">
            <i class="bi bi-arrow-left"></i> Volver a datos de envío
        </a>

        <div class="payment-card">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <div class="secure-badge">
                        <i class="bi bi-shield-lock-fill"></i> Pago 100% Seguro
                    </div>
                    <h2 class="fw-bold" style="color: #111827;">Detalles de Pago</h2>
                    <p class="text-muted mb-0">Total a pagar: <strong class="text-dark fs-5">${{ number_format($total, 2) }}</strong></p>
                </div>
                <div class="card-icons">
                    <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-warning shadow-sm mb-4" style="border-radius: 0; border: 1px solid #fcd34d;">
                    <strong>¡Falta un dato!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 0; border: 1px solid #fca5a5; background-color: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="token_id" id="token_id">
                <input type="hidden" name="device_session_id" id="device_session_id">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="label-b2b">Nombre en la tarjeta</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="holder_name" placeholder="Tarjeta habiente" autocomplete="off" required>
                    </div>
                    
                    <div class="col-12">
                        <label class="label-b2b">Número de tarjeta</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="card_number" placeholder="0000 0000 0000 0000" autocomplete="off" maxlength="19" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-b2b">Mes (MM)</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="expiration_month" placeholder="12" maxlength="2" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="label-b2b">Año (AA)</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="expiration_year" placeholder="28" maxlength="2" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-b2b">CVV</label>
                        <input type="password" class="form-control form-control-custom" data-openpay-card="cvv2" placeholder="***" autocomplete="off" maxlength="4" required>
                    </div>
                </div>

                <div id="payment-errors" class="alert alert-danger mt-4 d-none shadow-sm" style="border-radius: 0; border: 1px solid #fca5a5; background-color: #fef2f2; color: #991b1b;"></div>

                <button type="submit" id="pay-button" class="btn w-100 mt-4 py-3 fw-bold fs-5 shadow-sm" style="background-color: #25D366 !important; color: #111827 !important; border-radius: 0px; border: none;">
                    <i class="bi bi-shield-check me-2"></i> Continuar con verificacion segura
                </button>
                
                <div class="text-center mt-3">
                    <small class="text-muted"><i class="bi bi-info-circle"></i> Seras redirigido al banco para autenticar 3D Secure. Tus datos estan encriptados y no se guardan en nuestros servidores.</small>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="https://js.openpay.mx/openpay.v1.min.js"></script>
<script type="text/javascript" src="https://js.openpay.mx/openpay-data.v1.min.js"></script>

<script>
    $(document).ready(function() {
        OpenPay.setId('{{ config('services.openpay.merchant_id') }}');
        OpenPay.setApiKey('{{ config('services.openpay.public_key') }}');
        OpenPay.setSandboxMode(@json(!config('services.openpay.production')));

        var deviceSessionId = OpenPay.deviceData.setup();
        $('#device_session_id').val(deviceSessionId);

        $('#payment-form').submit(function(event) {
            event.preventDefault(); 
            
            $('#pay-button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Procesando...');
            $('#payment-errors').addClass('d-none'); 

            var inputTarjeta = $('input[data-openpay-card="card_number"]');
            var tarjetaLimpia = inputTarjeta.val().replace(/\s+/g, '');
            inputTarjeta.val(tarjetaLimpia);

            OpenPay.token.extractFormAndCreate('payment-form', onSuccess, onError);
        });

        var onSuccess = function(response) {
            var token_id = response.data.id;
            $('#token_id').val(token_id); 
            $('#payment-form')[0].submit();
        };

        var onError = function(error) {
            console.error("LOG DE OPENPAY:", error);
            $('#pay-button').prop('disabled', false).html('<i class="bi bi-shield-check me-2"></i> Continuar con verificacion segura');
            
            var errorMsg = "Error al procesar la tarjeta: " + error.message;
            if(error.status == 422) errorMsg = "Los datos de la tarjeta son inválidos. Revisa el número y el CVV.";
            if(error.status == 401) errorMsg = "Error de autenticación. Las llaves de Openpay no coinciden.";
            
            $('#payment-errors').removeClass('d-none').text(errorMsg);
        };
    });
</script>
@endsection