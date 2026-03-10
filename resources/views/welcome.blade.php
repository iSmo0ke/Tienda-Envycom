@extends('layouts.app')

@section('content')
    <h3 class="mb-4">PRODUCTOS</h3>
    <p>Encuentra lo mejor en tecnología</p>

    <div class="row g-4">
        @foreach(range(1,6) as $producto)
            <div class="col-md-2">
                <div class="product-card text-center">
                    <img src="https://via.placeholder.com/150" alt="Producto">
                    <h6 class="mt-3">DELL</h6>
                    <p class="small">Laptop i5 8GB</p>
                    <h5>$7,949.00</h5>
                    <button class="btn btn-envy w-100">Agregar</button>
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section-title">SERVICIOS</h2>
    <div class="row mt-4 g-4">
        @foreach(['Equipo de Cómputo', 'Impresión', 'Software', 'Redes', 'Mantenimiento'] as $servicio)
            <div class="col-md-2">
                <div class="service-card">
                    <p>{{ $servicio }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section-title">NUESTRAS MARCAS</h2>
    <div class="row mt-4 g-4 text-center">
        @foreach(['Dell','HP','Microsoft','Cisco','Asus','Benq','Apple'] as $marca)
            <div class="col-md-2">
                <div class="brand-card">
                    {{ $marca }}
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section-title">EQUIPO DE TRABAJO</h2>
    <div class="row mt-4 g-4">
        <div class="col-md-3">
            <div class="team-card">
                <img src="https://i.pravatar.cc/100" alt="Jesús">
                <h5 class="mt-3">Jesús Altamirano</h5>
                <p>Ingeniero en T.I</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="team-card">
                <img src="https://i.pravatar.cc/101" alt="Maria">
                <h5 class="mt-3">Maria Gómez</h5>
                <p>Ventas y soporte</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="team-card">
                <img src="https://i.pravatar.cc/102" alt="Carlos">
                <h5 class="mt-3">Carlos Ruiz</h5>
                <p>Consultor IT</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="team-card">
                <img src="https://i.pravatar.cc/103" alt="Ana">
                <h5 class="mt-3">Ana López</h5>
                <p>Administración</p>
            </div>
        </div>
    </div>
@endsection