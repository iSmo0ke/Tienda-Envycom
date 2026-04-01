<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{ asset('img/logo.png') }}" class="logo" alt="Logo Envycom">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEnvycom">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarEnvycom">

        {{-- MENÚ --}}
        <ul class="navbar-nav me-auto ms-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('laptops*') ? 'active' : '' }}" href="#">Laptops</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('desktops*') ? 'active' : '' }}" href="#">Desktops</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('impresoras*') ? 'active' : '' }}" href="#">Impresoras</a>
            </li>
        </ul>

        {{-- BUSCADOR + USUARIO --}}
        <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 mt-3 mt-lg-0">

            {{-- 🔍 BUSCADOR UNIFICADO --}}
            <form action="{{ route('productos.buscar') }}" method="GET" class="d-flex flex-grow-1 position-relative" style="max-width: 400px;">
                
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar productos..." 
                    class="form-control me-2 @error('q') is-invalid @enderror"
                >

                <button type="submit" class="btn btn-light">
                    <i class="fas fa-search"></i>
                </button>

                {{-- ERROR --}}
                @error('q')
                    <div class="invalid-feedback d-block position-absolute" style="top: 100%;">
                        {{ $message }}
                    </div>
                @enderror
            </form>

            {{-- CARRITO + USUARIO --}}
            <div class="d-flex flex-row align-items-center gap-2">

                {{-- CARRITO --}}
                <a href="{{ route('carrito') }}" class="btn btn-outline-light px-3 d-flex align-items-center" style="height: 40px;">
                    <i class="bi bi-cart2 fs-5"></i>
                    @if(session('cart'))
                        <span class="badge bg-danger ms-2">{{ count(session('cart')) }}</span>
                    @endif
                </a>

                {{-- USUARIO --}}
                @auth
                    <div class="dropdown">
                        <button class="btn btn-envy dropdown-toggle text-dark d-flex align-items-center" data-bs-toggle="dropdown">
                            Hola, {{ Auth::user()->name }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                    Mi Perfil
                                </a>
                            </li>

                            @if(Auth::user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                        Panel de Control
                                    </a>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Salir</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-envy text-dark d-flex align-items-center" style="height: 40px;">
                        Iniciar sesión
                    </a>
                @endauth

            </div>
        </div>
    </div>
</nav>