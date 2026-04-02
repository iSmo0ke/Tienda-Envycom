@extends('layouts.app')

@section('content')
<style>
    .legal-wrapper{
        padding: 40px 0 60px;
    }

    .legal-card{
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0,0,0,.05);
        padding: 32px;
    }

    .legal-title{
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 20px;
        color: #111827;
    }

    .legal-card h2{
        font-size: 1.1rem;
        font-weight: 800;
        margin-top: 24px;
        margin-bottom: 10px;
        color: #1E2A3B;
    }

    .legal-card p{
        color: #4b5563;
        line-height: 1.8;
    }
</style>

<div class="container legal-wrapper">
    <div class="legal-card">
        <h1 class="legal-title">Términos y Condiciones</h1>

        <!-- <p class="text-secondary mb-4">Última actualización: {{ date('d/m/Y') }}</p> -->

        <h2>1. Aceptación de términos</h2>
        <p>
            Al acceder y utilizar la tienda en línea de <strong>ENVYCOM</strong>, el usuario acepta de manera íntegra los presentes términos y condiciones. El uso del sitio atribuye la condición de cliente e implica la adhesión a las políticas vigentes al momento del acceso.
        </p>

        <h2>2. Precisión de Precios y Moneda</h2>
        <p>
            Todos los precios mostrados en el sitio están expresados en pesos mexicanos (MXN). ENVYCOM utiliza sistemas de precisión financiera para el cálculo de márgenes y utilidades. Nos reservamos el derecho de actualizar los precios sin previo aviso. 
            <strong>Nota importante:</strong> Debido a la naturaleza dinámica del inventario, si existe una discrepancia entre el precio mostrado en el catálogo y el precio al momento del pago (Checkout), el sistema priorizará el valor vigente en la base de datos para garantizar la integridad de la transacción.
        </p>

        <h2>3. Disponibilidad y Stock</h2>
        <p>
            Los productos publicados están sujetos a disponibilidad limitada. El hecho de agregar un producto al carrito no garantiza su reserva. La disponibilidad real se valida de forma definitiva al momento de procesar el pago. En caso de que el stock se agote durante el proceso de compra, el sistema notificará al usuario y la transacción no será procesada.
        </p>

        <h2>4. Procesamiento de Pagos y Seguridad</h2>
        <p>
            Los pagos se procesan a través de la plataforma segura <strong>Openpay</strong>. ENVYCOM no almacena datos sensibles de tarjetas de crédito o débito. Contamos con protocolos de seguridad que validan la integridad de cada transacción; cualquier intento de manipulación de precios o datos resultará en la cancelación inmediata de la orden y, de ser necesario, el bloqueo del usuario.
        </p>

        <h2>5. Envíos y Entregas</h2>
        <p>
            Los costos de envío no están incluidos en el precio del producto y se calcularán de manera individual para cada pedido. Dicho monto se determina en función de:
            <ul>
                <li><strong>Dimensiones y peso:</strong> El tamaño del paquete final tras el embalaje.</li>
                <li><strong>Ubicación de entrega:</strong> El código postal y la zona de cobertura (incluyendo posibles cargos por zonas extendidas).</li>
            </ul>
            Los tiempos de entrega son estimados y están sujetos a la logística de las empresas de mensajería externas. <strong>ENVYCOM</strong> no se hace responsable por retrasos derivados de causas de fuerza mayor o datos de contacto incorrectos proporcionados por el cliente.
        </p>

        <h2>6. Responsabilidad Limitada</h2>
        <p>
            ENVYCOM no se hace responsable por:
            <ul>
                <li>Errores en la dirección de entrega proporcionada por el cliente.</li>
                <li>Retrasos atribuibles a las empresas de mensajería (terceros).</li>
                <li>Interrupciones técnicas en el sitio web ajenas a nuestra infraestructura.</li>
            </ul>
        </p>

        <h2>7. Modificaciones</h2>
        <p>
            ENVYCOM se reserva el derecho de modificar estos términos en cualquier momento para adaptarlos a novedades legislativas o mejoras en los procesos técnicos de la tienda.
        </p>
    </div>
</div>
@endsection