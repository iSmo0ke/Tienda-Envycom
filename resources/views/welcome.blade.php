@extends('layouts.app')

@section('content')
    <h3 class="mb-4 fw-bold text-dark">PRODUCTOS</h3>
    <p class="text-muted">Encuentra lo mejor en tecnología corporativa</p>
    
    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div id="carruselProductos" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner px-4 pb-4">
            @foreach ($productosDestacados->chunk(6) as $index => $grupoProductos)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="row g-4 justify-content-center">

                        @foreach ($grupoProductos as $producto)
                            <div class="col-md-2">
                                <div class="product-card text-center h-100 d-flex flex-column shadow-sm rounded-4 p-3 bg-white border-0">
                                    
                                    <a href="{{ route('products.show', $producto->id) }}" class="link-unstyled flex-grow-1 text-decoration-none">
                                        <x-product-image 
                                            :image="$producto->imagen" 
                                            :alt="$producto->nombre" 
                                            cssClass="img-fluid p-2 mb-2" 
                                        />

                                        <div class="brand text-uppercase text-muted mb-1" style="font-size: 0.8rem;">
                                            {{ $producto->marca ?? 'MARCA' }}
                                        </div>
                                        
                                        <div class="product-name text-dark mb-2" style="font-size: 0.95rem;">
                                            {{ $producto->nombre }}
                                        </div>

                                        <div class="product-price text-primary fw-bold fs-5">
                                            ${{ number_format($producto->precio, 2) }}
                                        </div>
                                    </a>

                                    <div class="product-actions mt-3">
                                        <form action="{{ route('carrito.add', $producto->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-envy w-100 rounded-pill py-2" style="font-size: 0.9rem; font-weight: 500;">
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

    <div class="text-center mt-5 mb-5">
        <a href="{{ url('/productos') }}" class="btn btn-outline-dark px-5 py-2 rounded-3" style="font-weight: 500;">
            Ver todos los productos
        </a>
    </div>

    <h2 class="section-title fw-bold mt-5 mb-4 text-dark text-uppercase">Nuestros Servicios</h2>
    @php
        $servicios = [
            ['titulo' => 'Hardware', 'icono' => 'bi-pc-display', 'texto' => 'Suministramos hardware de alto rendimiento para todas las necesidades de tu empresa. Desde estaciones de trabajo y laptops de última generación hasta servidores robustos.'],
            ['titulo' => 'Impresión y Digitalización', 'icono' => 'bi-printer', 'texto' => 'Soluciones integrales de impresión láser, inyección de tinta y sistemas de gran formato. Ofrecemos equipos eficientes que reducen costos operativos por página.'],
            ['titulo' => 'Software y Licenciamiento', 'icono' => 'bi-windows', 'texto' => 'Venta y gestión de licencias originales para Microsoft 365, sistemas operativos, antivirus, y herramientas de productividad.'],
            ['titulo' => 'Redes y Conectividad', 'icono' => 'bi-router', 'texto' => 'Diseño e implementación de infraestructura de red local (LAN) y Wi-Fi empresarial. Configuramos routers, switches y firewalls.'],
            ['titulo' => 'Mantenimiento Preventivo y Correctivo', 'icono' => 'bi-tools', 'texto' => 'Extendemos la vida útil de tu inversión mediante pólizas de mantenimiento programado. Limpieza física, optimización de sistema y actualización.']
        ];
    @endphp

    <div class="row g-4 justify-content-center mb-5">
        @foreach ($servicios as $servicio)
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100 p-4 d-flex flex-column align-items-center text-center bg-white shadow-sm rounded-4 border-0">
                    <div class="mb-3 text-dark">
                        <i class="bi {{ $servicio['icono'] }} display-5" style="color: var(--envy-blue);"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-dark">{{ $servicio['titulo'] }}</h5>
                    <p class="text-muted small mb-0 text-center lh-lg">
                        {{ $servicio['texto'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section-title fw-bold mt-5 mb-4 text-dark text-uppercase">Nuestras Marcas</h2>
    <div class="row mt-4 g-4 text-center justify-content-center">
        @foreach (['Dell', 'HP', 'Asus', 'Benq', 'Apple'] as $marca)
            <div class="col-6 col-md-2">
                <div class="brand-card d-flex align-items-center justify-content-center h-100 p-3">
                    <img src="{{ asset('img/' . strtolower($marca) . '-logo.png') }}" alt="Logo de {{ $marca }}" class="img-fluid" style="max-height: 60px; object-fit: contain;">
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="section-title fw-bold mt-5 mb-4 text-dark text-uppercase">Nuestra Empresa</h2>
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h4 class="fw-bold text-dark mb-3"><i class="bi bi-building me-2 text-primary"></i>Quiénes Somos</h4>
                <p class="text-muted mb-0 lh-lg">
                    <strong>ENVYCOM</strong> 
                    submayorista consultor T.I, empresa establecida en 2024 dedicada a los servicios especializados en 
                    comercialización, servicio y consultoría en materia de tecnología de la información, infraestructura digital 
                    y consumibles de oficina, brindando atención de forma exclusiva a los sectores empresarial, educación y 
                    gobierno. Nuestra matriz radica en la Ciudad de Tehuacán, contando paralelamente con operaciones en la zona 
                    metropolitana de Puebla capital, por medio de nuestra oficina satélite ubicada en la colonia La Paz. 
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h4 class="fw-bold text-dark mb-3"><i class="bi bi-bullseye me-2 text-primary"></i>Misión</h4>
                <p class="text-muted mb-0 lh-lg">
                   Somos una empresa con un enfoque 100% B2B y B2G, deliberado y focalizado, esto con el propósito de mantener 
                   un estándar de atención y portafolio que sea consistente con las máximas exigencias del mundo empresarial y 
                   las grandes organizaciones. 
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h4 class="fw-bold text-dark mb-3"><i class="bi bi-eye me-2 text-primary"></i>Visión</h4>
                <p class="text-muted mb-0 lh-lg">
                    A corto y mediano plazo, nuestro objetivo es simple en papel, pero complejo en la ejecución: 
                    Ser EL proveedor singular de T.I en la región, y uno de los principales en tema de suministros. 
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h4 class="fw-bold text-dark mb-3"><i class="bi bi-diamond me-2 text-primary"></i>Valores</h4>
                <ul class="text-muted mb-0 lh-lg">
                    <li><strong>Seriedad y compromiso:</strong> Nuestra palabra y nuestra reputación lo es todo para nosotros.</li>
                    <li><strong>Transparencia:</strong> Su inversión es muy valiosa para todos, es por ello que somos concisos y explícitos en los productos y servicios a ejecutar en su proyecto organizacional y su alcance Su inversión es muy valiosa para todos, es por ello que somos concisos y explícitos en los productos y servicios a ejecutar en su proyecto organizacional y su alcance.</li>
                    <li><strong>Calidad:</strong> No venderíamos nada que no usaríamos nosotros mismos. Y nosotros somos muy exigentes. Hay un nivel que debemos preservar, el nivel que las compañías esperan, y se merecen.</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h4 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check me-2 text-primary"></i>Políticas</h4>
                <p class="text-muted mb-0 lh-lg">
                    Nos regimos por estrictos estándares de cumplimiento corporativo. Garantizamos la protección de datos de nuestros clientes, ofrecemos garantías directas y transparentes, y mantenemos una política de cero tolerancia hacia la piratería o software no licenciado.
                </p>
            </div>
        </div>
    </div>

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