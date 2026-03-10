<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENVYCOM</title>

    <link rel="shortcut icon" href="{{ asset('img/icono-verde.jpg') }}" />
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



        /* Botón Flotante de WhatsApp */
.whatsapp-float {
    position: fixed; /* Esto es lo que lo hace flotar */
    width: 60px;
    height: 60px;
    bottom: 40px; /* Distancia desde abajo */
    right: 40px; /* Distancia desde la derecha */
    background-color: #25d366; /* Color oficial de WhatsApp */
    color: #FFF;
    border-radius: 50px; /* Lo hace completamente redondo */
    text-align: center;
    font-size: 30px; /* Tamaño del ícono */
    box-shadow: 2px 2px 10px rgba(0,0,0,0.2); /* Sombrita para que resalte */
    z-index: 1000; /* Asegura que siempre esté por encima de las fotos y el footer */
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease; /* Efecto suave de movimiento */
}

/* Efecto cuando pasan el mouse por encima */
.whatsapp-float:hover {
    background-color: #128C7E; /* Verde más oscuro */
    color: #FFF;
    transform: scale(1.1); /* Se hace un 10% más grande */
}

/* Ajuste para celulares (para que no estorbe tanto en pantallas pequeñas) */
@media (max-width: 768px) {
    .whatsapp-float {
        width: 50px;
        height: 50px;
        bottom: 20px;
        right: 20px;
        font-size: 25px;
    }
}
    </style>
</head>
<body>

    @include('layouts.navigation')

    <main class="container mt-4 mb-5">
        @yield('content')
    </main>

    @include('layouts.footer')

    <a href="https://wa.me/522382742748?text=Hola,%20vengo%20de%20tu%20sitio%20web" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>