@extends('layouts.app') {{-- Usa tu layout público normal --}}

@section('content')
<style>
    :root {
        --envy-blue: #0b2b57;
        --envy-lime: #dfff00;
    }
    body { background-color: #f8fafc; }
    
    .payment-wrapper {
        max-width: 600px;
        margin: 40px auto;
    }
    .payment-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(11, 43, 87, 0.08);
        padding: 40px;
        border: 1px solid #f1f5f9;
    }
    .secure-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        color: #166534;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 20px;
    }
    .form-control-custom {
        border-radius: 10px;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .form-control-custom:focus {
        border-color: var(--envy-blue);
        background-color: #fff;
        box-shadow: none;
        outline: none;
    }
    .card-icons {
        font-size: 1.5rem;
        color: var(--envy-blue);
        display: flex;
        gap: 10px;
    }
</style>

<div class="container">
    <div class="payment-wrapper">
        
        <a href="{{ route('checkout.index') }}" class="btn btn-link text-decoration-none text-muted mb-3 p-0">
            <i class="bi bi-arrow-left"></i> Volver a datos de envío
        </a>

        <div class="payment-card">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <div class="secure-badge">
                        <i class="bi bi-shield-lock-fill"></i> Pago 100% Seguro
                    </div>
                    <h2 class="fw-bold" style="color: var(--envy-blue);">Detalles de Pago</h2>
                    <p class="text-muted mb-0">Total a pagar: <strong class="text-dark fs-5">${{ number_format($total, 2) }}</strong></p>
                </div>
                <div class="card-icons">
                    <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-warning shadow-sm border-0 rounded-3 mb-4">
                    <strong>¡Falta un dato!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="token_id" id="token_id">
                <input type="hidden" name="device_session_id" id="device_session_id">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-secondary fw-bold small text-uppercase">Nombre en la tarjeta</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="holder_name" placeholder="Como aparece en la tarjeta" autocomplete="off" required>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label text-secondary fw-bold small text-uppercase">Número de tarjeta</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="card_number" placeholder="0000 0000 0000 0000" autocomplete="off" maxlength="19" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-secondary fw-bold small text-uppercase">Mes (MM)</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="expiration_month" placeholder="12" maxlength="2" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label text-secondary fw-bold small text-uppercase">Año (AA)</label>
                        <input type="text" class="form-control form-control-custom" data-openpay-card="expiration_year" placeholder="28" maxlength="2" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-secondary fw-bold small text-uppercase">CVV</label>
                        <input type="password" class="form-control form-control-custom" data-openpay-card="cvv2" placeholder="***" autocomplete="off" maxlength="4" required>
                    </div>
                </div>

                <div id="payment-errors" class="alert alert-danger mt-4 d-none border-0 rounded-3 shadow-sm"></div>

                <button type="submit" id="pay-button" class="btn w-100 mt-4 py-3 fw-bold fs-5 shadow-sm" style="background-color: var(--envy-lime); color: var(--envy-blue); border-radius: 12px;">
                    <i class="bi bi-lock-fill me-2"></i> Pagar Pedido
                </button>
                
                <div class="text-center mt-3">
                    <small class="text-muted"><i class="bi bi-info-circle"></i> Tus datos están encriptados y no se guardan en nuestros servidores.</small>
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
        // 1. Inicializamos Openpay con tus llaves
        OpenPay.setId('{{ config('services.openpay.merchant_id') }}');
        OpenPay.setApiKey('{{ config('services.openpay.public_key') }}');
        OpenPay.setSandboxMode(true); 

        // 2. Generamos la huella del dispositivo al cargar la página (¡Esto ya te funciona!)
        var deviceSessionId = OpenPay.deviceData.setup();
        $('#device_session_id').val(deviceSessionId);

        // 3. Interceptamos el formulario cuando el cliente da clic en "Pagar"
        $('#payment-form').submit(function(event) {
            
            event.preventDefault(); // <--- ¡ESTE ES EL FRENO VITAL PARA EL NAVEGADOR!
            
            // Bloqueamos el botón para evitar clics dobles
            $('#pay-button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Procesando...');
            $('#payment-errors').addClass('d-none'); 

            // Limpiamos los espacios en blanco de la tarjeta (La magia anti-errores)
            var inputTarjeta = $('input[data-openpay-card="card_number"]');
            var tarjetaLimpia = inputTarjeta.val().replace(/\s+/g, '');
            inputTarjeta.val(tarjetaLimpia);

            // Vamos al banco por el Token
            OpenPay.token.extractFormAndCreate('payment-form', onSuccess, onError);
        });

        // 4. Si el banco aprueba la tarjeta, nos manda para acá
        var onSuccess = function(response) {
var token_id = response.data.id;
            
            $('#token_id').val(token_id); // Lo inyectamos en el HTML
            
            // Enviamos a Laravel
            $('#payment-form')[0].submit();
        };

        // 5. Si la tarjeta está mal, mostramos el error
        var onError = function(error) {
            console.error("LOG DE OPENPAY:", error);
            $('#pay-button').prop('disabled', false).html('<i class="bi bi-lock-fill me-2"></i> Pagar Pedido');
            
            var errorMsg = "Error al procesar la tarjeta: " + error.message;
            if(error.status == 422) errorMsg = "Los datos de la tarjeta son inválidos. Revisa el número y el CVV.";
            if(error.status == 401) errorMsg = "Error de autenticación. Las llaves de Openpay no coinciden.";
            
            $('#payment-errors').removeClass('d-none').text(errorMsg);
        };
    });
</script>
@endsection