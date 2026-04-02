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
        <h1 class="legal-title">Aviso de Privacidad</h1>
        <!-- <p class="text-secondary mb-4">Última actualización: {{ date('d/m/Y') }}</p> -->

        <p>
            En <strong>ENVYCOM</strong>, la privacidad de nuestros usuarios es una prioridad. Este Aviso de Privacidad detalla cómo recopilamos, usamos y protegemos su información personal de acuerdo con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.
        </p>

        <h2>1. Información que Recopilamos</h2>
        <p>
            Para ofrecer una experiencia de compra óptima, solicitamos los siguientes datos:
            <ul>
                <li><strong>Identificación:</strong> Nombre completo y correo electrónico para la creación de cuenta y seguimiento de pedidos.</li>
                <li><strong>Logística:</strong> Dirección de envío y número telefónico para la entrega de mercancía.</li>
                <li><strong>Navegación:</strong> Dirección IP, tipo de navegador y cookies para mejorar la seguridad y funcionalidad del carrito de compras.</li>
            </ul>
        </p>

        <h2>2. Protección de Datos Financieros</h2>
        <p>
            <strong>ENVYCOM NO almacena ni tiene acceso a los números de su tarjeta de crédito o débito, fechas de vencimiento ni códigos de seguridad (CVV).</strong> 
            El procesamiento de los pagos se realiza de forma externa y cifrada a través de <strong>Openpay</strong> (una empresa de BBVA), quien cumple con los más altos estándares de seguridad internacional PCI DSS.
        </p>

        <h2>3. Uso de la Información</h2>
        <p>
            Sus datos personales se utilizan exclusivamente para:
            <ul>
                <li>Procesar sus órdenes de compra y gestionar el envío.</li>
                <li>Enviar confirmaciones de pedido y actualizaciones de estatus vía correo electrónico.</li>
                <li>Detectar y prevenir posibles fraudes o abusos técnicos en nuestra plataforma.</li>
                <li>Brindar soporte técnico y atención al cliente.</li>
            </ul>
        </p>

        <h2>4. Transferencia de Datos a Terceros</h2>
        <p>
            Sus datos solo serán compartidos con terceros en los siguientes casos necesarios para la operación:
            <ul>
                <li><strong>Empresas de Mensajería:</strong> Únicamente nombre y dirección para concretar la entrega.</li>
                <li><strong>Pasarelas de Pago:</strong> Información necesaria para la validación del cobro (Openpay).</li>
                <li><strong>Autoridades:</strong> Únicamente cuando exista un requerimiento legal vigente.</li>
            </ul>
        </p>

        <h2>5. Seguridad Técnica</h2>
        <p>
            Implementamos medidas de seguridad administrativas, técnicas y físicas para proteger sus datos personales contra daño, pérdida, alteración o uso no autorizado. Esto incluye el uso de protocolos de cifrado y validaciones de integridad de datos en nuestro servidor.
        </p>

        <h2>6. Derechos ARCO</h2>
        <p>
            Usted tiene derecho a <strong>Acceder, Rectificar, Cancelar u Oponerse</strong> al tratamiento de sus datos personales. Para ejercer estos derechos, puede ponerse en contacto con nuestro equipo de soporte a través del correo electrónico registrado en su cuenta de usuario.
        </p>

        <h2>7. Uso de Cookies</h2>
        <p>
            Utilizamos cookies para recordar los productos en su carrito y mantener su sesión activa. Usted puede desactivar las cookies desde la configuración de su navegador, aunque esto podría afectar la funcionalidad de la tienda.
        </p>
    </div>
</div>
@endsection