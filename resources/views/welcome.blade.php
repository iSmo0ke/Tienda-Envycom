@extends('layouts.app')

@section('content')
    <h3 class="mb-4">PRODUCTOS</h3>
    <p>Encuentra lo mejor en tecnología</p>
    @if(session('success'))
    <div class="alert alert-success rounded-4 shadow-sm mb-4">
        {{ session('success') }}
    </div>
@endif

    <div id="carruselProductos" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner px-4 pb-4">

            {{-- Dividimos los productos en grupos de 6 para cada slide del carrusel --}}
            @foreach ($productosDestacados->chunk(6) as $index => $grupoProductos)
    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
        <div class="row g-4 justify-content-center">

           @foreach ($grupoProductos as $producto)
    <div class="col-md-2">
        <div class="product-card text-center h-100 d-flex flex-column shadow-sm rounded-4 p-3 bg-white border-0">
            
            <a href="{{ route('products.show', $producto->id) }}" style="text-decoration: none; color: inherit;" class="flex-grow-1">
                <x-product-image 
                    :image="$producto->imagen" 
                    :alt="$producto->nombre"cssClass="w-full h-48 object-cover rounded-t-lg" 
                />

                <div class="brand text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    {{ $producto->marca ?? 'MARCA' }}
                </div>
                
                <div class="product-name fw-bold text-dark mb-2" style="font-size: 0.9rem; line-height: 1.2;">
                    {{ $producto->nombre }}
                </div>

                <div class="product-price text-primary fw-bold">
                    ${{ number_format($producto->precio, 2) }}
                </div>
            </a>

            <div class="product-actions mt-3">
                <form action="{{ route('carrito.add', $producto->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-envy w-100 rounded-pill py-2" style="font-size: 0.85rem;">
                        <i class="bi bi-cart-plus"></i> Agregar
                    </button>
                </form>
            </div>
            
        </div>
    </div>
@endforeach

        </div>
    </div>
@endforeach

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carruselProductos" data-bs-slide="prev"
            style="width: 40px; justify-content: flex-start;">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carruselProductos" data-bs-slide="next"
            style="width: 40px; justify-content: flex-end;">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <div class="text-center mt-5">
        <a href="{{ url('/productos') }}" class="btn btn-outline-dark px-5 py-2 fw-bold" style="border-radius: 8px;">
            Ver todos losproductos
        </a>
    </div>

    <h2 class="section-title">SERVICIOS</h2>

    @php
        $servicios = [
            [
                'titulo' => 'Hardware',
                'icono' => 'bi-pc-display',
                'texto' =>
                    'Suministramos hardware de alto rendimiento para todas las necesidades de tu empresa. Desde estaciones de trabajo y laptops de última generación hasta servidores robustos.',
            ],
            [
                'titulo' => 'Impresión y Digitalización',
                'icono' => 'bi-printer',
                'texto' =>
                    'Soluciones integrales de impresión láser, inyección de tinta y sistemas de gran formato. Ofrecemos equipos eficientes que reducen costos operativos por página.',
            ],
            [
                'titulo' => 'Software y Licenciamiento',
                'icono' => 'bi-windows',
                'texto' =>
                    'Venta y gestión de licencias originales para Microsoft 365, sistemas operativos, antivirus, y herramientas de productividad. Te asesoramos para elegir el esquema que mejor se adapte al tamaño de tu organización.',
            ],
            [
                'titulo' => 'Redes y Conectividad',
                'icono' => 'bi-router',
                'texto' =>
                    'Diseño e implementación de infraestructura de red local (LAN) y Wi-Fi empresarial. Configuramos routers, switches y firewalls para asegurar que tu conexión sea estable, rápida y segura contra intrusiones.',
            ],
            [
                'titulo' => 'Mantenimiento Preventivo y Correctivo',
                'icono' => 'bi-tools',
                'texto' =>
                    'Extendemos la vida útil de tu inversión mediante pólizas de mantenimiento programado. Limpieza física, optimización de sistema y actualización de hardware.',
            ],
        ];
    @endphp

    <div class="row mt-4 g-4 justify-content-center">
        @foreach ($servicios as $servicio)
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100 p-4 d-flex flex-column align-items-center text-center">

                    <div class="mb-3" style="color: #0c2b45;">
                        <i class="bi {{ $servicio['icono'] }}" style="font-size: 2.5rem;"></i>
                    </div>

                    <h5 class="fw-bold mb-3">{{ $servicio['titulo'] }}</h5>
                    <p class="text-muted small mb-0" style="text-align: justify; text-align-last: center;">
                        {{ $servicio['texto'] }}
                    </p>

                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section-title">NUESTRAS MARCAS</h2>

    <div class="row mt-4 g-4 text-center justify-content-center">

        @foreach (['Dell', 'HP', 'Asus', 'Benq', 'Apple'] as $marca)
            <div class="col-6 col-md-2">

                <div class="brand-card d-flex align-items-center justify-content-center" style="height: 100px;">

                    <img src="{{ asset('img/' . strtolower($marca) . '-logo.png') }}" alt="Logo de {{ $marca }}"
                        class="img-fluid" style="max-height: 60px; object-fit: contain;">

                </div>

            </div>
        @endforeach

    </div>

    <h2 class="section-title text-center mb-4">EQUIPO DE TRABAJO</h2>

    <div id="teamCarousel" class="carousel slide mt-4" data-bs-ride="carousel">
        <div class="carousel-inner">

            <div class="carousel-item active">
                <div class="row justify-content-center">
                    <div class="col-md-5 mb-3">
                        <div class="team-card contact-highlight p-4 shadow-sm">
                            <h5 class="mt-3 fw-bold">Alonso Bautista Castillo</h5>
                            <p class="text-primary fw-bold mb-1">Director General</p>
                            <p class="mb-2"><i class="bi bi-telephone-fill me-2"></i>238 289 9275</p>
                            <div class="partner-logos border-top pt-2">
                                <small class="text-muted">Envycom | Dell | Apple | HP | Odoo</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <div class="team-card contact-highlight p-4 shadow-sm">
                            <h5 class="mt-3 fw-bold">Jesús Altamirano Carrillo</h5>
                            <p class="text-primary fw-bold mb-1">Project Manager</p>
                            <p class="mb-2"><i class="bi bi-telephone-fill me-2"></i>238 289 9275</p>
                            <div class="partner-logos border-top pt-2">
                                <small class="text-muted">Envycom | Dell | Apple | HP | Odoo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="row justify-content-center">
                    <div class="col-md-5 mb-3">
                        <div class="team-card contact-highlight p-4 shadow-sm">
                            <h5 class="mt-3 fw-bold">Sugeiri Daniela Castillo Reyes</h5>
                            <p class="text-primary fw-bold mb-1">INGENIERO EN T.I</p>
                            <p class="mb-2"><i class="bi bi-telephone-fill me-2"></i>238 289 9275</p>
                            <div class="partner-logos border-top pt-2">
                                <small class="text-muted">Envycom | Dell | Apple | HP | Odoo</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <div class="team-card contact-highlight p-4 shadow-sm">
                            <h5 class="mt-3 fw-bold">Hilda Michelle Linares Narciso</h5>
                            <p class="text-primary fw-bold mb-1">INGENIERO EN T.I</p>
                            <p class="mb-2"><i class="bi bi-telephone-fill me-2"></i>238 289 9275</p>
                            <div class="partner-logos border-top pt-2">
                                <small class="text-muted">Envycom | Dell | Apple | HP | Odoo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#teamCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#teamCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const alerta = document.querySelector('.alert-success');
        if (alerta) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alerta);
                bsAlert.close();
            }, 3000);
        }
    });
</script>