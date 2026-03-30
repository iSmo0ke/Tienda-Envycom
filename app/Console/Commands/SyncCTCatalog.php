<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SyncCTCatalog extends Command
{
    protected $signature = 'ct:sync-catalog {file=ct_productos.json}';
    protected $description = 'Sincroniza el catálogo de CT leyendo un archivo JSON y aplicando reglas de negocio.';

    public function handle()
    {
       $this->info("Iniciando sincronización automática del catálogo CT...");

        
        $remoteFileName = 'catalogo_xml/productos.json';
        $localFileName = 'ct_productos_temp.json';
        
        $this->info("📥 Descargando archivo JSON desde el FTP de CT...");

        try {
            // Descargar del FTP a tu disco local
            $fileContents = Storage::disk('ftp_ct')->get($remoteFileName);
            Storage::disk('local')->put($localFileName, $fileContents);
            $this->info("✅ Archivo descargado exitosamente.");
        } catch (\Exception $e) {
            $this->error("❌ Error al conectar al FTP o descargar el archivo: " . $e->getMessage());
            return;
        }

        $filePath = storage_path('app/private/' . $localFileName);

        // 1. Leer y decodificar el JSON (Aquí entra el código que ya teníamos)
        $jsonContent = File::get($filePath);
        $productosCT = json_decode($jsonContent, true);

        if (!$productosCT) {
            $this->error("❌ El archivo JSON no tiene un formato válido.");
            return;
        }

        $this->info("📦 Se encontraron " . count($productosCT) . " productos en el JSON crudo.");

        //Cargar la configuración de marcas
        $config = config('ct_brands');
        $approvedBrands = array_map('strtolower', $config['approved']);
        $rejectedBrands = array_map('strtolower', $config['rejected']);
        $conditionalBrands = array_change_key_case($config['conditional'], CASE_LOWER);
        
        $minProducts = $config['rules']['min_products'];
        $requireStock = $config['rules']['require_stock'];

        //Agrupar por marca para validar volumen y stock total
        $productosPorMarca = collect($productosCT)->groupBy(function ($item) {
            return strtolower($item['marca'] ?? 'desconocida');
        });

        $productosAImportar = [];
        $skusProcesados = []; // Para saber cuáles desactivar al final

        $this->info("Filtrando productos según reglas de negocio...");

        foreach ($productosPorMarca as $marca => $productos) {
            //Regla General: Menos de X productos, se ignora la marca completa
            if ($productos->count() < $minProducts) continue;

            //Regla General: Suma de stock debe ser mayor a 0
            if ($requireStock) {
                $stockTotal = $productos->sum(function ($p) {
                    return (int) ($p['existencia'] ?? 0);
                });
                if ($stockTotal <= 0) continue;
            }

            //Si está en rechazadas, saltar
            if (in_array($marca, $rejectedBrands)) continue;

            //Si NO está en aprobadas y NO es condicional (?), saltar
            if (!in_array($marca, $approvedBrands) && !array_key_exists($marca, $conditionalBrands)) continue;

            //Iterar sobre los productos de las marcas que sí pasaron los filtros
            foreach ($productos as $prod) {
                //Si es marca CONDICIONAL (EXP), aplicar filtros extra
                if (array_key_exists($marca, $conditionalBrands)) {
                    $condiciones = $conditionalBrands[$marca];
                    $categoria = $prod['categoria'] ?? '';
                    $sku = $prod['numParte'] ?? '';

                    $pasaCategoria = in_array($categoria, $condiciones['allowed_categories'] ?? []);
                    $pasaSku = in_array($sku, $condiciones['allowed_skus'] ?? []);

                    if (!$pasaCategoria && !$pasaSku) {
                        continue; //No cumple condición de SKU o Categoría, lo saltamos
                    }
                }

                //Mapear los datos del JSON a las columnas de la BD
                $numParte = $prod['numParte'] ?? 'N/A';
                $skusProcesados[] = $numParte;

                $productosAImportar[] = [
                    'idProducto' => $prod['idProducto'] ?? null,
                    'numParte' => $numParte,
                    'nombre' => $prod['nombre'] ?? 'Sin nombre',
                    'modelo' => $prod['modelo'] ?? null,
                    'marca' => $prod['marca'] ?? 'Desconocida',
                    'categoria' => $prod['categoria'] ?? null,
                    'descripcion_corta' => $prod['descripcion_corta'] ?? null,
                    'precio' => $prod['precio'] ?? 0,
                    //Si viene en array, lo convertimos a JSON para el upsert masivo
                    'existencia' => isset($prod['existencia']) ? json_encode(['total' => $prod['existencia']]) : json_encode([]), 
                    'especificaciones' => isset($prod['especificaciones']) ? json_encode($prod['especificaciones']) : json_encode([]),
                    'activo' => true,
                    'source' => 'CT', // Clave para diferenciar de los productos locales
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $this->info("Productos listos para importar después de filtros: " . count($productosAImportar));

        if (count($productosAImportar) > 0) {
            //UPSERT MASIVO (Insertar nuevos, actualizar existentes) en bloques de 500
            $this->withProgressBar(array_chunk($productosAImportar, 500), function ($bloque) {
                Product::upsert(
                    $bloque,
                    ['numParte'], // Columna única para identificar si ya existe
                    ['nombre', 'precio', 'existencia', 'activo', 'updated_at'] // Columnas a actualizar si ya existe
                );
            });
            $this->newLine();
        }

        //LIMPIEZA / DESACTIVACIÓN
        //Desactivar productos de 'CT' que ya no vienen en este JSON o ya no cumplen las reglas
        $this->info("Desactivando productos que ya no cumplen las reglas o no tienen stock...");
        
        $desactivados = Product::where('source', 'CT')
            ->whereNotIn('numParte', $skusProcesados)
            ->update(['activo' => false]);

        Storage::disk('local')->delete($localFileName);
        $this->info("🗑️ Archivo temporal eliminado.");
        $this->info("🎉 Sincronización finalizada con éxito.");
    }
}