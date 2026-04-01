<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('zip_code'); // d_codigo
            $table->string('settlement'); // d_asenta
            $table->string('settlement_type'); // d_tipo_asenta
            $table->string('municipality'); // D_mnpio
            $table->string('state'); // d_estado
            $table->string('city')->nullable(); // d_ciudad
            $table->timestamps();

            // Índices para optimizar búsquedas
            $table->index('zip_code');
            $table->index('state');
            $table->index('municipality');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('sepomex_id')->constrained('postal_codes');
            $table->string('calle');
            $table->string('alias')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('numero_exterior')->nullable();
            $table->string('receptor_name');
            $table->string('numero_interior')->nullable();
            $table->text('referencias')->nullable();
            $table->string('telefono',12);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('postal_codes');
    }
};

