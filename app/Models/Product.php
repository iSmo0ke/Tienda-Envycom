<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'idProducto', 'numParte', 'nombre', 'modelo', 'marca', 
        'subcategoria', 'categoria', 'descripcion_corta', 'activo', 
        'existencia', 'precio', 'especificaciones', 'promociones', 'imagen', 'source'
    ];

    // Esto convierte los JSON de la base de datos en arreglos de PHP automáticamente
    protected $casts = [
        //'existencia' => 'array',
        'especificaciones' => 'array',
        'promociones' => 'array',
        'activo' => 'boolean',
    ];

    public function orderItems() {
    return $this->hasMany(OrderItem::class);
}
}