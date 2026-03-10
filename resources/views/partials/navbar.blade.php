<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo Envycom">
    </a>

    <ul class="navbar-nav ms-4">
        <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('laptops*') ? 'active' : '' }}" href="#">Laptops</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('desktops*') ? 'active' : '' }}" href="#">Desktops</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('impresoras*') ? 'active' : '' }}" href="#">Impresoras</a></li>
    </ul>

    <div class="ms-auto d-flex align-items-center"> {{-- Agregué align-items-center para que el texto y botones alineen bien --}}
        <form action="{{ route('productos.buscar') }}" method="GET" class="d-flex">
            <input 
                type="text" 
                name="search" 
                class="form-control me-2" 
                placeholder="¿Qué estás buscando?"
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-light me-2">Buscar</button>
        </form>

        <a href="{{ route('carrito') }}" class="btn btn-outline-light me-2">
            <i class="bi bi-cart2"></i>
            @if(session('cart')) 
                <span class="badge bg-danger">{{ count(session('cart')) }}</span>
            @endif
        </a>

        @auth
            {{-- Mostramos el nombre del usuario logueado --}}
            <span class="text-white me-3">Hola, {{ Auth::user()->name }}</span>
            
            <a href="{{ route('dashboard') }}" class="btn btn-envy me-2">Mi Cuenta</a>

            {{-- El botón de Logout SIEMPRE debe ser un formulario por seguridad en Laravel --}}
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    Salir
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-envy">Iniciar sesión</a>
        @endauth
    </div>
</nav>