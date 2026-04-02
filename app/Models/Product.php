<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;

class Product extends Model
{
    protected $fillable = [
        'idProducto', 'numParte', 'nombre', 'modelo', 'marca', 
        'subcategoria', 'categoria', 'descripcion_corta', 'activo', 
        'existencia', 'precio', 'especificaciones', 'promociones', 'imagen', 'source'
    ];

    protected $casts = [
        'especificaciones' => 'array',
        'promociones' => 'array',
        'activo' => 'boolean',
    ];

    public function getPrecioAttribute($value)
    {
        $centavosBase = (int) round($value * 100);
        $dinero = new Money($centavosBase, new Currency('MXN'));
        $precioConMargen = $dinero->multiply('1.6427');
        return $precioConMargen->getAmount() / 100;
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}