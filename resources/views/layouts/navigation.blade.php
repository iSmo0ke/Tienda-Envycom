<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo Envycom">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEnvycom" aria-controls="navbarEnvycom" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarEnvycom">
        <ul class="navbar-nav ms-4">
            <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->is('laptops*') ? 'active' : '' }}" href="#">Laptops</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->is('desktops*') ? 'active' : '' }}" href="#">Desktops</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->is('impresoras*') ? 'active' : '' }}" href="#">Impresoras</a></li>
        </ul>

        <div class="ms-auto d-flex flex-column flex-lg-row align-items-center mt-3 mt-lg-0">
            
            <form action="{{ route('productos.buscar') }}" method="GET" class="d-flex w-100 me-lg-3 mb-2 mb-lg-0">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control me-2" 
                    placeholder="¿Qué estás buscando?"
                    value="{{ request('search') }}"
                >
                <button type="submit" class="btn btn-light">Buscar</button>
            </form>

            <a href="{{ route('carrito') }}" class="btn btn-outline-light me-lg-3 mb-2 mb-lg-0 w-100 w-lg-auto text-center">
                <i class="bi bi-cart2"></i>
                @if(session('cart')) 
                    <span class="badge bg-danger">{{ count(session('cart')) }}</span>
                @endif
            </a>

            @auth
                <div class="dropdown w-100 w-lg-auto text-center">
                    <button class="btn btn-envy dropdown-toggle text-white w-100" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        Hola, {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                Mi Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    Salir
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-envy w-100 w-lg-auto text-center">Iniciar sesión</a>
            @endauth
        </div>
    </div>
</nav>