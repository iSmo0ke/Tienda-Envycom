<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENVYCOM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body { background: #f5f6f8; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #0c2b45; }
        .logo { height: 40px; }
        .btn-envy { background: #d7ff00; border: none; font-weight: 600; }
        .product-card { border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.08); padding: 15px; background: white; }
        .product-card img { height: 120px; object-fit: contain; }
        .section-title { font-weight: 700; margin-top: 60px; text-align: center; }
        .service-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,.08); text-align: center; }
        .brand-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,.08); }
        .team-card { background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,.1); }
        .team-card img { width: 80px; height: 80px; border-radius: 50%; }
    </style>
</head>
<body>

    @include('layouts.navigation')

    <main class="container mt-4 mb-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>