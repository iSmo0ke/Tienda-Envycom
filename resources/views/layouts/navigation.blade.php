<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{ asset('img/logo.png') }}" class="logo" alt="Logo Envycom">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEnvycom" aria-controls="navbarEnvycom" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarEnvycom">
        <ul class="navbar-nav me-auto ms-4">
            <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->is('laptops*') ? 'active' : '' }}" href="#">Laptops</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->is('desktops*') ? 'active' : '' }}" href="#">Desktops</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->is('impresoras*') ? 'active' : '' }}" href="#">Impresoras</a></li>
        </ul>

<div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 mt-3 mt-lg-0">
            
            <form action="{{ route('productos.buscar') }}" method="GET" class="d-flex flex-grow-1" style="max-width: 400px;">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control me-2" 
                    placeholder="¿Qué estás buscando?"
                    value="{{ request('search') }}"
                >
                <button type="submit" class="btn btn-light">Buscar</button>
            </form>

            <div class="d-flex flex-row align-items-center gap-2">
                
                <a href="{{ route('carrito') }}" class="btn btn-outline-light px-3 d-flex align-items-center justify-content-center" style="height: 40px;">
                    <i class="bi bi-cart2 fs-5"></i>
                    @if(session('cart'))
                        <span class="badge bg-danger ms-2">{{ count(session('cart')) }}</span>
                    @endif
                </a>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-envy dropdown-toggle px-3 text-dark d-flex align-items-center justify-content-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="height: 40px; white-space: nowrap;">
                            Hola, {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Mi Perfil</a></li>
                            
                            @if(Auth::check() && Auth::user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                        Panel de Control
                                    </a>
                                </li>
                            @endif
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Salir</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-envy px-4 text-nowrap text-dark d-flex align-items-center justify-content-center" style="height: 40px;">Iniciar sesión</a>
                @endauth
            </div>
        </div>
    </div>
</nav>