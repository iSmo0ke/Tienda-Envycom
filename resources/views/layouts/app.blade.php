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
        /* 1. IMPORTAMOS LA FUENTE CORPORATIVA 'INTER' */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

        /* 2. REGLA GLOBAL: FUENTE SOBRIA Y TEXTO NORMAL */
        body { 
            background: #f5f6f8; 
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-weight: 400; /* Todo el texto es normal por defecto */
            color: #333; /* Un gris muy oscuro es más elegante que el negro puro */
        }

        /* 3. REGLA DEL DIRECTOR: NEGRITAS SOLO EN TÍTULOS Y PRECIOS */
        h1, h2, h3, h4, h5, h6, .section-title { 
            font-weight: 700 !important; 
        }
        
        .product-price { 
            font-weight: 700 !important; 
        }

        /* --- Clases reutilizables ENVYCOM --- */
        .link-unstyled { text-decoration: none; color: inherit; }
        
        /* Forzamos a que el título del producto NO sea negrita */
        .text-product-title { 
            font-size: 0.95rem; 
            line-height: 1.3; 
            font-weight: 400 !important; 
            color: #4a4a4a;
        }
        
        .text-brand { font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; }
        .carousel-btn-custom { width: 40px; }
        .carousel-icon-custom { background-color: #212529; border-radius: 50%; padding: 0.5rem; }

        .navbar { background: #0c2b45; }
        .logo { height: 40px; }
        
        /* Suavizamos un poco el botón para que no le robe atención a los precios */
        .btn-envy { background: #d7ff00; border: none; font-weight: 500; color: #0c2b45; } 
        
        .product-card { border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.08); padding: 15px; background: white; transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,.1); } /* Pequeño efecto premium al pasar el mouse */
        .product-card img { height: 120px; object-fit: contain; }
        
        .section-title { margin-top: 60px; text-align: center; text-transform: uppercase; letter-spacing: 1px; }
        .service-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,.08); text-align: center; }
        .brand-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,.08); }
        .team-card { background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,.1); }
        .team-card img { width: 80px; height: 80px; border-radius: 50%; }

        /* Botón Flotante de WhatsApp (Totalmente intacto) */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .whatsapp-float:hover {
            background-color: #128C7E;
            color: #FFF;
            transform: scale(1.1);
        }

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