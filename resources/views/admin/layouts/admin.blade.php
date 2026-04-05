<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - ENVYCOM</title>
    <link rel="shortcut icon" href="{{ asset('img/icono-verde.jpg') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --envy-blue: #0b2b57;
            --envy-lime: #dfff00;
            --admin-bg: #f3f4f6;
        }
        body {
            background-color: var(--admin-bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow-x: hidden;
        }
        /* Sidebar Styling */
        .admin-sidebar {
            width: 260px;
            height: 100vh;
            background-color: var(--envy-blue);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            z-index: 1000;
        }
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--envy-lime);
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }
        .nav-link {
            color: #9ca3af;
            padding: 12px 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--envy-blue);
            background-color: var(--envy-lime);
            border-radius: 0 25px 25px 0;
            margin-right: 20px;
        }
        .nav-link i {
            font-size: 1.2rem;
        }
        /* Main Content Area */
        .admin-main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }
        .topbar {
            background: white;
            padding: 15px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            ENVYCOM <span style="color:white; font-size: 1rem;">Admin</span>
        </div>
        <nav class="nav flex-column mt-4">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Panel de Control
            </a>
            
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i> Mis Productos
            </a>
            
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check-fill"></i> Pedidos
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Clientes
            </a>
            
            <hr class="text-secondary my-4 mx-3">
            
            <a href="{{ route('products.index') }}" class="nav-link">
                <i class="bi bi-shop"></i> Ver Tienda
            </a>
        </nav>
    </aside>

    <main class="admin-main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <span class="fw-bold text-dark">Hola, {{ auth()->user()->name }}</span>
                <button class="btn btn-sm btn-outline-danger rounded-pill">Salir</button>
            </div>
        </header>

        @yield('content')

    </main>

</body>
</html>