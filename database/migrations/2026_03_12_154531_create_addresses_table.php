<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('alias')->nullable(); // Ej: Casa, Oficina
            $table->string('receptor_name'); // Quién recibe
            $table->string('phone');
            $table->string('calle_numero');
            $table->string('colonia');
            $table->string('municipio_alcaldia');
            $table->string('estado');
            $table->string('codigo_postal', 10);
            $table->text('referencias')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

