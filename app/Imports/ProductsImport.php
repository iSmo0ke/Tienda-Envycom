<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        if (!isset($data['idproducto'])) {
            return;
        }

        $specifications = json_decode($data['especificaciones'] ?? '[]', true);
        $promotions = json_decode($data['promociones'] ?? '[]', true);
        
        $stockRaw = json_decode($data['existencia'] ?? '{"local": 0}', true);
        $stock = is_numeric($stockRaw) ? ['local' => (int) $stockRaw] : (is_array($stockRaw) ? $stockRaw : ['local' => 0]);

        Product::updateOrCreate(
            ['idProducto' => $data['idproducto']],
            [
                'numParte'          => $data['numparte'] ?? null,
                'nombre'            => $data['nombre'],
                'modelo'            => $data['modelo'] ?? null,
                'marca'             => $data['marca'] ?? null,
                'subcategoria'      => $data['subcategoria'] ?? null,
                'categoria'         => $data['categoria'] ?? null,
                'descripcion_corta' => $data['descripcion_corta'] ?? null,
                'existencia'        => $stock,
                'precio'            => (float) ($data['precio'] ?? 0),
                'especificaciones'  => $specifications,
                'promociones'       => $promotions,
                'imagen'            => $data['imagen'] ?? null,
                'source'            => $data['source'] ?? 'local',
                'activo'            => true,
            ]
        );
    }
}