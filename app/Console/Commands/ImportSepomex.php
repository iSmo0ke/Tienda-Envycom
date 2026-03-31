<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use Illuminate\Support\Str;

class ImportSepomex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sepomex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa códigos postales desde CPdescarga.txt a la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = app_path('Console/Commands/CPdescarga.txt');

        if (!file_exists($filePath)) {
            $this->error("El archivo no se encuentra en: {$filePath}");
            return 1;
        }

        $this->info("Iniciando la importación de {$filePath}...");

        //validar que la tabla postal_codes esté vacía antes de insertar
        $existingCount = DB::table('postal_codes')->count();
        if ($existingCount > 0) {
            $this->error("La tabla postal_codes no está vacía. Por favor, vacíala antes de importar.");
            return 1;
        }

        $handle = fopen($filePath, 'r');

        if (!$handle) {
            $this->error("No se pudo abrir el archivo.");
            return 1;
        }

        $dataToInsert = [];
        $batchSize = 1000;
        $totalInserted = 0;
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;

            // Saltar las primeras 2 líneas (encabezado del archivo SEPOMEX)
            if ($lineNumber <= 2) {
                continue;
            }

            // Convertir encoding (el TXT de SEPOMEX viene en ISO-8859-1)
            $line = mb_convert_encoding($line, 'UTF-8', 'ISO-8859-1');

            $columns = explode('|', trim($line));

            // Validar que tenga columnas suficientes
            if (count($columns) < 6) {
                continue;
            }

            $dataToInsert[] = [
                'id'              => (string) Str::uuid(),
                'zip_code'        => trim($columns[0]),  // d_codigo
                'settlement'      => trim($columns[1]),  // d_asenta
                'settlement_type' => trim($columns[2]),  // d_tipo_asenta
                'municipality'    => trim($columns[3]),  // D_mnpio
                'state'           => trim($columns[4]),  // d_estado
                'city'            => trim($columns[5]),  // d_ciudad
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            if (count($dataToInsert) >= $batchSize) {
                DB::table('postal_codes')->insert($dataToInsert);
                $totalInserted += $batchSize;
                $dataToInsert = [];
            }
            $this->info("... Procesando línea {$lineNumber}");
        }
        $this->info("Insertados {$totalInserted} registros...");

        fclose($handle);

        if (!empty($dataToInsert)) {
            DB::table('postal_codes')->insert($dataToInsert);
            $totalInserted += count($dataToInsert);
        }

        $this->info("¡Importación completada! Total: {$totalInserted} registros.");
        return 0;
    }
}
