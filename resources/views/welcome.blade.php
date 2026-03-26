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
            {{-- Dividimos los productos en grupos de 6 --}}
            @foreach ($productosDestacados->chunk(6) as $index => $grupoProductos)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="row g-4 justify-content-center">

                        @foreach ($grupoProductos as $producto)
                            <div class="col-md-2">
                                <div class="product-card text-center h-100 d-flex flex-column shadow-sm rounded-4 p-3 bg-white border-0">
                                    
                                    <a href="{{ route('products.show', $producto->id) }}" class="link-unstyled flex-grow-1">
                                        {{-- ¡AQUÍ ESTÁ LA MAGIA! Clases puras de Bootstrap --}}
                                        <x-product-image 
                                            :image="$producto->imagen" 
                                            :alt="$producto->nombre" 
                                            cssClass="img-fluid p-2 mb-2" 
                                        />

                                        <div class="brand text-uppercase text-muted text-brand mb-1">
                                            {{ $producto->marca ?? 'MARCA' }}
                                        </div>
                                        
                                        <div class="product-name fw-bold text-dark text-product-title mb-2">
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

        <button class="carousel-control-prev carousel-btn-custom justify-content-start" type="button" data-bs-target="#carruselProductos" data-bs-slide="prev">
            <span class="carousel-control-prev-icon carousel-icon-custom" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next carousel-btn-custom justify-content-end" type="button" data-bs-target="#carruselProductos" data-bs-slide="next">
            <span class="carousel-control-next-icon carousel-icon-custom" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <div class="text-center mt-5">
        <a href="{{ url('/productos') }}" class="btn btn-outline-dark px-5 py-2 fw-bold rounded-3">
            Ver todos los productos
        </a>
    </div>

    <h2 class="section-title">SERVICIOS</h2>

    @php
        $servicios = [
            ['titulo' => 'Hardware', 'icono' => 'bi-pc-display', 'texto' => 'Suministramos hardware de alto rendimiento para todas las necesidades de tu empresa. Desde estaciones de trabajo y laptops de última generación hasta servidores robustos.'],
            ['titulo' => 'Impresión y Digitalización', 'icono' => 'bi-printer', 'texto' => 'Soluciones integrales de impresión láser, inyección de tinta y sistemas de gran formato. Ofrecemos equipos eficientes que reducen costos operativos por página.'],
            ['titulo' => 'Software y Licenciamiento', 'icono' => 'bi-windows', 'texto' => 'Venta y gestión de licencias originales para Microsoft 365, sistemas operativos, antivirus, y herramientas de productividad.'],
            ['titulo' => 'Redes y Conectividad', 'icono' => 'bi-router', 'texto' => 'Diseño e implementación de infraestructura de red local (LAN) y Wi-Fi empresarial. Configuramos routers, switches y firewalls.'],
            ['titulo' => 'Mantenimiento Preventivo y Correctivo', 'icono' => 'bi-tools', 'texto' => 'Extendemos la vida útil de tu inversión mediante pólizas de mantenimiento programado. Limpieza física, optimización de sistema y actualización.']
        ];
    @endphp

    <div class="row mt-4 g-4 justify-content-center">
        @foreach ($servicios as $servicio)
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100 p-4 d-flex flex-column align-items-center text-center">
                    <div class="mb-3 text-dark">
                        <i class="bi {{ $servicio['icono'] }} display-5" style="color: #0c2b45;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">{{ $servicio['titulo'] }}</h5>
                    <p class="text-muted small mb-0 text-center">
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
                <div class="brand-card d-flex align-items-center justify-content-center h-100 p-3">
                    <img src="{{ asset('img/' . strtolower($marca) . '-logo.png') }}" alt="Logo de {{ $marca }}" class="img-fluid" style="max-height: 60px; object-fit: contain;">
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section-title text-center mb-4">EQUIPO DE TRABAJO</h2>

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
@endsection