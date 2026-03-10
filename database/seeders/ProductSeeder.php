<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Leer el archivo JSON
        $json = File::get(database_path('seeders/productos.json'));
        $productos = json_decode($json, true);

        foreach ($productos as $item) {
            // 2. Insertar o actualizar cada producto
            Product::updateOrCreate(
                ['idProducto' => $item['idProducto']], // Si el ID ya existe, lo actualiza
                [
                    'numParte'          => $item['numParte'] ?? null,
                    'nombre'            => $item['nombre'],
                    'modelo'            => $item['modelo'] ?? null,
                    'marca'             => $item['marca'] ?? null,
                    'subcategoria'      => $item['subcategoria'] ?? null,
                    'categoria'         => $item['categoria'] ?? null,
                    'descripcion_corta' => $item['descripcion_corta'] ?? null,
                    'activo'            => $item['activo'] == 1,
                    'existencia'        => $item['existencia'], // Laravel lo convierte a JSON por el Model Cast
                    'precio'            => $item['precio'],
                    'especificaciones'  => $item['especificaciones'],
                    'promociones'       => $item['promociones'],
                    'imagen'            => $item['imagen'] ?? null,
                ]
            );
        }
        
        $this->command->info('¡Catálogo de CT importado con éxito!');
    }
}