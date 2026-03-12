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

        <h2>Uso del sitio</h2>
        <p>
            Al acceder y utilizar la tienda en línea de ENVYCOM, el usuario acepta los presentes términos y condiciones.
        </p>

        <h2>Productos y disponibilidad</h2>
        <p>
            Los productos publicados están sujetos a disponibilidad y pueden cambiar sin previo aviso en precio, existencia o características.
        </p>

        <h2>Precios y pagos</h2>
        <p>
            Los precios mostrados se expresan en moneda nacional y podrán actualizarse sin previo aviso.
        </p>

        <h2>Envíos y entregas</h2>
        <p>
            Los tiempos de entrega son estimados y pueden variar según la ubicación, disponibilidad del producto y paquetería.
        </p>

        <h2>Responsabilidad</h2>
        <p>
            ENVYCOM no será responsable por retrasos atribuibles a terceros, errores en datos proporcionados por el cliente o causas de fuerza mayor.
        </p>
    </div>
</div>
@endsection