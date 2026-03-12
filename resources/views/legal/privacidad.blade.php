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

        <h2>Datos que recabamos</h2>
        <p>
            ENVYCOM podrá recabar datos como nombre, correo electrónico, teléfono, dirección de envío y datos relacionados con compras realizadas en la tienda en línea.
        </p>

        <h2>Finalidad del tratamiento</h2>
        <p>
            Los datos serán utilizados para procesar pedidos, brindar atención al cliente, dar seguimiento a compras y mejorar la experiencia del usuario.
        </p>

        <h2>Protección de datos</h2>
        <p>
            ENVYCOM implementa medidas razonables de seguridad para proteger la información personal del usuario.
        </p>

        <h2>Derechos del usuario</h2>
        <p>
            El titular podrá solicitar acceso, rectificación o cancelación de sus datos personales conforme a la legislación aplicable.
        </p>

        <h2>Contacto</h2>
        <p>
            Para dudas relacionadas con este aviso de privacidad, el usuario podrá comunicarse con ENVYCOM por los medios oficiales de contacto.
        </p>
    </div>
</div>
@endsection