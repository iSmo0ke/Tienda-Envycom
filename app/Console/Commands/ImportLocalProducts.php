<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

class ImportLocalProducts extends Command
{
    // Esta es la "firma" que Laravel no encontraba
    protected $signature = 'products:import-local {file=productos.xlsx}';

    protected $description = 'Import local products from an Excel file into the database';

    public function handle()
    {
        $fileName = $this->argument('file');
        
        $filePath = base_path($fileName);
        
        if (!File::exists($filePath)) {
            $filePath = storage_path('app/' . $fileName);
            if (!File::exists($filePath)) {
                $this->error("❌ El archivo {$fileName} no fue encontrado. Colócalo en la raíz de tu proyecto o en storage/app/");
                return;
            }
        }

        $this->info("🚀 Importando productos desde Excel: {$fileName}...");

        try {
            Excel::import(new ProductsImport, $filePath);
            $this->info("✅ ¡Importación completada con éxito!");
        } catch (\Exception $e) {
            $this->error("❌ Ocurrió un error durante la importación: " . $e->getMessage());
        }
    }
}