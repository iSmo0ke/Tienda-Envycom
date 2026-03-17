📏 Estándar de Nomenclatura del Proyecto (Regla: Código en Inglés, Interfaz en Español)
Para mantener el código limpio, escalable y aprovechar la sintaxis nativa de Laravel (como la pluralización automática de modelos), todo el equipo debe seguir este estándar de nombramiento:

1. Archivos y Carpetas (Inglés y minúsculas)

Las rutas de las vistas deben estar en inglés.

✅ Correcto: admin/products/create.blade.php

❌ Incorrecto: admin/productos/crear.blade.php

2. Código Backend y Base de Datos (Inglés)

Variables, modelos, relaciones y nombres de columnas en la BD van en inglés.

✅ Correcto: {{ $product->sale_price }}, $order->items

❌ Incorrecto: {{ $producto->precio_venta }}, $pedido->articulos

3. Clases CSS e IDs (Inglés)

Para mantener coherencia con frameworks como Bootstrap/Tailwind.

✅ Correcto: <div class="main-card">, <button id="save-btn">

❌ Incorrecto: <div class="tarjeta-principal">, <button id="boton-guardar">

4. Textos Visuales / Interfaz de Usuario (Español)

Lo único que debe ir en español es el texto real que el usuario o el administrador lee en la pantalla.

✅ Correcto: <button class="submit-btn">Guardar Producto</button>

❌ Incorrecto: <button class="submit-btn">Save Product</button>