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
        'existencia' => 'array',
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

    // Función para leer el stock de forma segura sin importar si es CT o Local
    public function getStockDisponibleAttribute()
    {
        $existencia = $this->existencia;

        if (!is_array($existencia)) {
            return 0;
        }

        // CASO 1: Producto LOCAL (Sobre pedido)
        // Retornamos un número alto o el valor de 'local' para que siempre deje comprar
        if ($this->source === 'local') {
            return $existencia['local'] ?? 100; 
        }

        // CASO 2: Producto de CT (Mayorista)
        // Sumamos las existencias de todas las sucursales dentro de la llave 'total'
        if ($this->source === 'CT' || isset($existencia['total'])) {
            $totalCT = 0;
            $sucursales = $existencia['total'] ?? [];
            
            if (is_array($sucursales)) {
                foreach ($sucursales as $sucursal => $cantidad) {
                    $totalCT += (int) $cantidad;
                }
            }
            return $totalCT;
        }

        return 0;
    }
}