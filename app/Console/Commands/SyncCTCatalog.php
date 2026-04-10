<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

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
        
        $minProducts = $config['rules']['min_products'];
        $requireStock = $config['rules']['require_stock'];
        $excludedSkus = $config['rules']['excluded_skus'] ?? [];

        // 3. Agrupar por marca para validar volumen y stock total
        $productosPorMarca = collect($productosCT)->groupBy(function ($item) {
            return strtolower($item['marca'] ?? 'desconocida');
        });

        $productosAImportar = [];
        $skusProcesados = []; // Para saber cuáles desactivar al final

        $this->info("🔍 Filtrando productos según reglas de negocio...");

        foreach ($productosPorMarca as $marca => $productos) {
            // Regla General: Menos de X productos, se ignora la marca completa
            if ($productos->count() < $minProducts) continue;

            // Regla General: Suma de stock debe ser mayor a 0
            if ($requireStock) {
                $stockTotal = $productos->sum(function ($p) {
                    return (int) ($p['existencia'] ?? 0);
                });
                if ($stockTotal <= 0) continue;
            }

            // Regla de Excel: Si está en rechazadas, saltar
            if (in_array($marca, $rejectedBrands)) continue;

            // Regla de Excel: Si NO está en aprobadas, saltar
            if (!in_array($marca, $approvedBrands)) continue;

            // Iterar sobre los productos de las marcas que sí pasaron los filtros
            foreach ($productos as $prod) {
                
                $numParte = $prod['numParte'] ?? 'N/A';

                // Si este SKU está en nuestra lista negra, lo saltamos y no se importa
                if (in_array($numParte, $excludedSkus)) {
                    continue; 
                }

                $skusProcesados[] = $numParte;
                
                // --- NUEVA LÓGICA DE DESCARGA DE IMÁGENES ---
                $imagenUrl = $prod['imagen'] ?? null;
                $rutaLocalImagen = null;

                if ($imagenUrl) {
                    // Sacamos el nombre del archivo (ej. ACPARU150_full.jpg)
                    $nombreImagen = basename(parse_url($imagenUrl, PHP_URL_PATH));
                    $rutaDestino = 'productos/' . $nombreImagen;

                    // Verificamos si YA descargamos esta imagen antes
                    if (!Storage::disk('public')->exists($rutaDestino)) {
                        try {
                            $response = Http::timeout(5)->get($imagenUrl);
                            
                            if ($response->successful()) {
                                Storage::disk('public')->put($rutaDestino, $response->body());
                                $rutaLocalImagen = $rutaDestino;
                            }
                        } catch (\Exception $e) {
                            $rutaLocalImagen = null;
                        }
                    } else {
                        // Si ya existe, guardamos la ruta
                        $rutaLocalImagen = $rutaDestino;
                    }
                }

                // Mapear los datos del JSON a las columnas de nuestra BD
                $productosAImportar[] = [
                    'idProducto' => $prod['idProducto'] ?? null,
                    'numParte' => $numParte,
                    'nombre' => $prod['nombre'] ?? 'Sin nombre',
                    'modelo' => $prod['modelo'] ?? null,
                    'marca' => $prod['marca'] ?? 'Desconocida',
                    'categoria' => $prod['categoria'] ?? null,
                    'subcategoria' => $prod['subcategoria'] ?? null,
                    'descripcion_corta' => $prod['descripcion_corta'] ?? null,
                    'precio' => $prod['precio'] ?? 0,
                    'moneda' => $prod['moneda'] ?? 'MXN',
                    'tipo_cambio' => $prod['tipoCambio'] ?? 1,
                    'existencia' => isset($prod['existencia']) ? json_encode(['total' => $prod['existencia']]) : json_encode([]), 
                    'especificaciones' => isset($prod['especificaciones']) ? json_encode($prod['especificaciones']) : json_encode([]),
                    'promociones' => isset($prod['promociones']) ? json_encode($prod['promociones']) : json_encode([]),
                    'imagen' => $rutaLocalImagen,
                    'activo' => true,
                    'source' => 'CT',
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
                    ['nombre', 
                    'precio', 
                    'moneda',
                    'tipo_cambio',
                    'existencia',
                    'subcategoria',
                    'promociones', 
                    'imagen',    
                    'activo', 
                    'updated_at']
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