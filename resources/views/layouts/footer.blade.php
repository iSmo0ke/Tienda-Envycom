<footer style="background-color: #061e37; color: #a1b1c3;" class="pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            
            <div class="col-lg-4 col-md-6">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Envycom" class="mb-3" style="height: 45px;">
                </a>
                <p class="small pe-lg-4" style="text-align: justify;">
                    Somos tu mejor aliado en tecnología. Ofrecemos soluciones integrales en hardware, software, impresión y redes para potenciar el crecimiento de tu empresa.
                </p>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3 fw-bold">Navegación</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ url('/') }}" class="text-decoration-none text-white-50 hover-white">Inicio</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white-50 hover-white">Laptops</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white-50 hover-white">Desktops</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white-50 hover-white">Impresoras</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-white mb-3 fw-bold">Contacto & Legal</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-white"></i> 238 289 9275</li>
                    <li class="mb-2"><i class="bi bi-envelope-fill me-2 text-white"></i> contacto@envycom.com</li>
                    <li class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-white"></i> Centenario 537, G.Hidalgo, C.P. 75790, Tehuacán, Pue.</li>
                    <li class="mb-2 mt-4">
                        <a href="{{ url('/aviso-de-privacidad') }}" class="text-decoration-none text-white-50 hover-white">Política de Privacidad</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ url('/terminos-y-condiciones') }}" class="text-decoration-none text-white-50 hover-white">Términos y Condiciones</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ url('/devoluciones') }}" class="text-decoration-none text-white-50 hover-white">Políticas de Devolución y Envío</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="text-white mb-3 fw-bold">Síguenos</h6>
                <div class="d-flex gap-3">
                    <a href= "https://www.facebook.com/envycom.mx/"target="_blank" class="text-white-50 hover-white" style="font-size: 1.5rem;">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href= "https://www.instagram.com/envycom.mx/"target="_blank" class="text-white-50 hover-white" style="font-size: 1.5rem;">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>
            
        </div>

        <hr class="mt-4 mb-3" style="border-color: rgba(255,255,255,0.1);">

        <div class="row">
            <div class="col-12 text-center small" style="color: #6c86a3;">
                {{-- date('Y') actualiza el año automáticamente a 2026 y futuros --}}
                &copy; {{ date('Y') }} Envycom. Todos los derechos reservados.
            </div>
        </div>
    </div>
</footer>