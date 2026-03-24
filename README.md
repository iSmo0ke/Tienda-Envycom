Envycom Ecommerce
1. Clonar repositorio
2. Instalar dependencias de PHP:
    - composer install
3. Instalar dependecias frontend:
    - npm install

4. Configuracion de entorno
    - Copiar .env

5. Migraciones y Datos
    - php artisan migrate (Solo para la estructura de la BD)
    - php artisan migrate --seed (Para jalar productos de ct (productos.json))
    - php artisan app:import-local-products (Para jalar productos externos a CT)

    - php artisan storage:link (symlink para que las imagenes sean visibles)

Versiones
    PHP: 8.2
    Composer: 2.x.
    MySQL: 8.0 o superior
    Node.js: 24.14.0





Código (Backend/BD/CSS): Todo en Inglés. (Modelos, controladores, variables, clases de Tailwind
a exepcion de tabla products que esta en español porque se importo de CT).

Interfaz (UI): Todo en Español. (Botones, etiquetas, mensajes de error).

Nomenclatura de Archivos: Las vistas deben seguir la ruta en inglés: resources/views/admin/products/.