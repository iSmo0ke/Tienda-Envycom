@extends('layouts.app')

@section('content')
<style>
    /* Se mantienen los estilos de tus otros archivos legales para consistencia */
    .legal-wrapper{ padding: 40px 0 60px; }
    .legal-card{
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0,0,0,.05);
        padding: 32px;
    }
    .legal-title{ font-size: 2rem; font-weight: 800; margin-bottom: 20px; color: #111827; }
    .legal-card h2{ font-size: 1.1rem; font-weight: 800; margin-top: 24px; margin-bottom: 10px; color: #1E2A3B; }
    .legal-card p, .legal-card li{ color: #4b5563; line-height: 1.8; }
    .legal-card ul { margin-left: 20px; list-style-type: disc; }
</style>

<div class="container legal-wrapper">
    <div class="legal-card">
        <h1 class="legal-title">Políticas de Envío y Devolución</h1>

        <h2>1. Políticas de Envío</h2>
        <p>
            En ENVYCOM, nos comprometemos a que tu producto llegue de forma segura y eficiente:
        </p>
        <ul>
            <li><strong>Cobertura:</strong> Realizamos envíos a toda la República Mexicana a través de las principales paqueterías (Estafeta, FedEx, DHL, entre otras).</li>
            <li><strong>Tiempos de entrega:</strong> El tiempo estimado de entrega es de 3 a 7 días hábiles posteriores a la confirmación de tu pago.</li>
            <li><strong>Procesamiento:</strong> Los pedidos realizados después de las 14:00 horas se procesarán al siguiente día hábil.</li>
            <li><strong>Costo de envío:</strong> El costo se calculará al momento de finalizar la compra (Checkout) basándose en el peso y volumen del paquete.</li>
        </ul>

        <h2>2. Políticas de Devolución y Cancelación</h2>
        <p>
            Queremos que estés satisfecho con tu compra. Si necesitas realizar una devolución, considera lo siguiente:
        </p>
        <ul>
            <li><strong>Plazo:</strong> Cuentas con 5 días naturales tras recibir el producto para reportar cualquier anomalía o solicitar una devolución por defecto de fábrica.</li>
            <li><strong>Condiciones del producto:</strong> Para que la devolución sea aceptada, el producto debe estar nuevo, sin uso, en su empaque original sellado y con todos sus accesorios y manuales.</li>
            <li><strong>Garantías:</strong> Al ser productos tecnológicos, la garantía después de los primeros 5 días se tramitará directamente con el fabricante conforme a sus propias políticas.</li>
            <li><strong>Cancelaciones:</strong> Podrás cancelar tu pedido siempre y cuando este no haya sido entregado a la paquetería. Una vez generado el número de guía, no se aceptan cancelaciones.</li>
        </ul>

        <h2>3. Proceso de Reclamación</h2>
        <p>
            En caso de recibir un paquete dañado o abierto, es indispensable reportarlo en las primeras 24 horas enviando evidencia fotográfica a nuestros medios de contacto oficiales.
        </p>

        <h2>4. Reembolsos</h2>
        <p>
            Los reembolsos aprobados se realizarán a través del mismo método de pago utilizado en la compra y el tiempo de acreditación dependerá de la institución bancaria del cliente.
        </p>
    </div>
</div>
@endsection