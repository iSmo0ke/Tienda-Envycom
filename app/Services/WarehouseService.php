namespace App\Services;

class WarehouseService
{
    // Mapa de prioridades según el estado del cliente
    protected $routingMap = [
        'Puebla' => ['PUE', 'VER', 'TXL', 'DFC'], 
        'Jalisco' => ['GDL', 'LEO', 'SLP'],
        'Nuevo Leon' => ['MTY', 'SLT', 'TAM'],
        // ... mapear los estados de México a las claves de CT
    ];

    public function getBestAvailableWarehouse(array $existenciasCT, string $estadoCliente)
    {
        $prioridades = $this->routingMap[$estadoCliente] ?? ['DFC']; // DFC (CDMX) como fallback nacional

        // 1. Buscar en el almacén más cercano
        foreach ($prioridades as $sucursal) {
            if (isset($existenciasCT[$sucursal]) && $existenciasCT[$sucursal] > 0) {
                return [
                    'almacen' => $sucursal,
                    'stock' => $existenciasCT[$sucursal],
                    'is_local' => true
                ];
            }
        }

        // 2. Si no hay en sucursales cercanas, buscar en cualquier otra que tenga stock
        foreach ($existenciasCT as $sucursal => $cantidad) {
            if ($cantidad > 0) {
                return [
                    'almacen' => $sucursal,
                    'stock' => $cantidad,
                    'is_local' => false // Avisa que vendrá de lejos (puede afectar tiempo de envío)
                ];
            }
        }

        return null; // Sin stock en todo el país
    }
}