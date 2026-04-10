<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WarehouseService
{
    /**
     * Mapa de enrutamiento: Relaciona los Estados de México (según SEPOMEX) 
     * con las sucursales de CT ordenadas por cercanía/prioridad logística.
     */
    protected $routingMap = [
        'Aguascalientes' => ['AGS', 'LEO', 'GDL', 'ZAC'],
        'Baja California' => ['TIJ', 'MXL', 'ENS', 'NGL'],
        'Baja California Sur' => ['LAP', 'TIJ', 'CUL'],
        'Campeche' => ['CAM', 'MER', 'VHA'],
        'Chiapas' => ['TXL', 'OAX', 'VHA'],
        'Chihuahua' => ['CUU', 'JZA', 'TRC'],
        'Ciudad de México' => ['DFC', 'TOL', 'CUE', 'PAC'],
        'Coahuila de Zaragoza' => ['SLT', 'TRC', 'MTY'],
        'Colima' => ['COL', 'GDL', 'MOR'],
        'Durango' => ['DGO', 'TRC', 'ZAC'],
        'Estado de México' => ['TOL', 'DFC', 'PAC', 'CUE'],
        'Guanajuato' => ['LEO', 'QRO', 'AGS', 'SLP'],
        'Guerrero' => ['ACA', 'CUE', 'TOL', 'DFC'],
        'Hidalgo' => ['PAC', 'DFC', 'TOL', 'QRO'],
        'Jalisco' => ['GDL', 'COL', 'AGS', 'LEO'],
        'Michoacán de Ocampo' => ['MOR', 'LEO', 'QRO', 'TOL'],
        'Morelos' => ['CUE', 'DFC', 'TOL', 'ACA'],
        'Nayarit' => ['TEP', 'GDL', 'CUL'],
        'Nuevo León' => ['MTY', 'SLT', 'TAM'],
        'Oaxaca' => ['OAX', 'TXL', 'PUE', 'VER'],
        'Puebla' => ['PUE', 'VER', 'TXL', 'DFC'],
        'Querétaro' => ['QRO', 'LEO', 'PAC', 'TOL'],
        'Quintana Roo' => ['CAN', 'MER', 'CAM'],
        'San Luis Potosí' => ['SLP', 'QRO', 'ZAC', 'TAM'],
        'Sinaloa' => ['CUL', 'MZT', 'OBR'],
        'Sonora' => ['HMO', 'OBR', 'NGL', 'MXL'],
        'Tabasco' => ['VHA', 'CAM', 'TXL', 'VER'],
        'Tamaulipas' => ['TAM', 'MTY', 'SLT', 'SLP'],
        'Tlaxcala' => ['PUE', 'PAC', 'DFC'],
        'Veracruz de Ignacio de la Llave' => ['VER', 'PUE', 'OAX', 'VHA'],
        'Yucatán' => ['MER', 'CAN', 'CAM'],
        'Zacatecas' => ['ZAC', 'AGS', 'SLP', 'DGO'],
    ];

    // Almacenes con mayor volumen como último recurso nacional
    protected $fallbackWarehouses = ['DFC', 'GDL', 'MTY', 'MER', 'TIJ'];

    /**
     * Busca el mejor almacén que pueda surtir la cantidad completa solicitada.
     */
    public function getBestWarehouse(array $existenciasCT, string $estadoCliente, int $cantidadRequerida): ?string
    {
        $prioridades = $this->routingMap[$estadoCliente] ?? $this->fallbackWarehouses;

        // 1. Buscar en almacenes prioritarios (cercanos)
        foreach ($prioridades as $almacen) {
            if (isset($existenciasCT[$almacen]) && (int)$existenciasCT[$almacen] >= $cantidadRequerida) {
                return $almacen;
            }
        }

        // 2. Si no hay cerca, buscar en CUALQUIER OTRO almacén del país que tenga la pieza
        arsort($existenciasCT); // Ordena de mayor a menor stock
        foreach ($existenciasCT as $almacen => $cantidad) {
            if ((int)$cantidad >= $cantidadRequerida) {
                return $almacen;
            }
        }

        // Si llega aquí, significa que ningún almacén por sí solo tiene la cantidad que el cliente pide
        return null; 
    }

    /**
     * Consulta en la API de CT en TIEMPO REAL si el stock sigue ahí.
     */
    public function checkLiveStock(string $sku, string $warehouse, int $cantidadRequerida): bool
    {
        try {
            // Documentación API: http://sandbox.ctonline.mx/existencia/detalle/:codigo/:almacen
            $url = "http://sandbox.ctonline.mx/existencia/detalle/{$sku}/{$warehouse}";
            
            // Hacemos la petición a CT (Time out corto para no trabar el checkout de tu cliente)
            $response = Http::timeout(4)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && count($data) > 0) {
                    $stockApi = (int) ($data[0]['existencia'] ?? 0);
                    return $stockApi >= $cantidadRequerida;
                }
            }
            return false; // CT respondió que no hay stock
        } catch (\Exception $e) {
            Log::error("Error consultando API CT Live Stock para SKU {$sku}: " . $e->getMessage());
            // Si la API de CT se cae, confiamos en lo que dice nuestra base de datos para no perder la venta
            return true; 
        }
    }
}