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
        Schema::create('sepomex', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('d_codigo');   
            $table->string('d_asenta');   
            $table->string('d_tipo_asenta');
            $table->string('d_mnpio');    
            $table->string('d_estado');  
            $table->string('d_ciudad')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepomex');
    }
};
