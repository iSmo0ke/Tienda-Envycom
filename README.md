Envycom Ecommerce

<!-- Inicializar proyecto -->
1. Clonar repositorio
    En php.ini descomentar la linea extension=ftp para activarlo
    De igual forma con la line extension=zip
2. Para instalar dependencias y configuraciones ejecutar:
    composer run setup

4. Configuracion de entorno
    - Copiar .env
    - Copiar productos.xls en storage\app\productos.xlsx

5. Migraciones y Datos
    - php artisan migrate (Solo para la estructura de la BD)
    - php artisan migrate --seed (Para jalar productos de ct (productos.json))
    - php artisan products:import-local (Para jalar productos externos a CT)

    - php artisan storage:link (symlink para que las imagenes sean visibles)

6. Correr el proyecto
    - npm run dev
    - php artisan serv

<!-- Versiones requeridas -->
    PHP: 8.2
    Composer: 2.x.
    MySQL: 8.0 o superior
    Node.js: 24.14.0

<!-- Nomenclatura -->
Código (Backend/BD/CSS): Todo en Inglés. (Modelos, controladores, variables, clases de Tailwind
a exepcion de tabla products que esta en español porque se importo de CT).

Interfaz (UI): Todo en Español. (Botones, etiquetas, mensajes de error).

Nomenclatura de Archivos: Las vistas deben seguir la ruta en inglés: resources/views/admin/products/.

<!-- Diseño -->
Clases reutilizables en resources\views\layouts\app.blade.php
