<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('idProducto')->unique();
            $table->string('numParte')->nullable();
            $table->string('nombre');
            $table->string('modelo')->nullable();
            $table->string('marca')->nullable();
            $table->string('subcategoria')->nullable();
            $table->string('categoria')->nullable();
            $table->text('descripcion_corta')->nullable();
            $table->boolean('activo')->default(true);
            $table->json('existencia')->nullable();
            $table->decimal('precio', 10, 2);
            $table->json('especificaciones')->nullable();
            $table->json('promociones')->nullable();
            $table->string('imagen')->nullable();
            $table->string('source')->default('local');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function up_down(): void
    {
        Schema::dropIfExists('products');
    }
};

