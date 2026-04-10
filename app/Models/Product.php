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
        'existencia', 'precio', 'moneda', 'tipo_cambio', 'especificaciones', 'promociones', 'imagen', 'source'
    ];

    protected $casts = [
        'especificaciones' => 'array',
        'promociones' => 'array',
        'activo' => 'boolean',
        'existencia' => 'array',
    ];

    public function getPrecioAttribute($value)
    {
        // 1. Obtener precio base en pesos
        $precioBaseMXN = $value;
        if ($this->moneda === 'USD') {
            $tipoCambio = $this->tipo_cambio > 0 ? $this->tipo_cambio : 18.00; // Fallback de seguridad
            $precioBaseMXN = $value * $tipoCambio;
        }

        // 2. Aplicar lógica actual con MoneyPHP
        $centavosBase = (int) round($precioBaseMXN * 100);
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

    // Scope para motor de búsqueda y filtros
    public function scopeFiltrar($query, array $filtros)
    {
        // 1. Buscador de texto (Insensible a mayúsculas/minúsculas)
        $query->when($filtros['buscar'] ?? null, function ($query, $buscar) {
            // Convertimos lo que el usuario escribió a minúsculas
            $buscarLimpio = strtolower($buscar); 
            
            $query->where(function ($q) use ($buscarLimpio) {
                // Usamos LOWER() para convertir temporalmente la base de datos a minúsculas y comparar
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . $buscarLimpio . '%'])
                  ->orWhereRaw('LOWER(descripcion_corta) LIKE ?', ['%' . $buscarLimpio . '%'])
                  ->orWhereRaw('LOWER(numParte) LIKE ?', ['%' . $buscarLimpio . '%'])
                  ->orWhereRaw('LOWER(marca) LIKE ?', ['%' . $buscarLimpio . '%']);
            });
        });

        // 2. Filtro exacto por Categoría
        $query->when($filtros['categoria'] ?? null, function ($query, $categoria) {
            $query->where('categoria', $categoria);
        });

        // 3. Filtro exacto por Marca
        $query->when($filtros['marca'] ?? null, function ($query, $marca) {
            $query->where('marca', $marca);
        });

        // 4. Ordenamiento dinámico
        $query->when($filtros['ordenar'] ?? 'recientes', function ($query, $ordenar) {
            if ($ordenar === 'menor_precio') {
                $query->orderBy('precio', 'asc');
            } elseif ($ordenar === 'mayor_precio') {
                $query->orderBy('precio', 'desc');
            } elseif ($ordenar === 'az') {
                $query->orderBy('nombre', 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
        });
    }
}