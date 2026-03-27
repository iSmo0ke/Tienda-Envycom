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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('order_number')->unique();
            $table->enum('status', ['pendiente', 'pagado', 'en_proceso', 'enviado', 'entregado', 'cancelado'])->default('pendiente');

            //Envio
            $table->string('shipping_tracking_number')->nullable();
            $table->string('shipping_carrier')->nullable();
            
            // Costos
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);

            // Datos de pago (Openpay)
            $table->string('payment_method')->nullable(); // card, spei, store
            $table->string('payment_id')->nullable(); // ID de transacción de Openpay

            // Snapshot de la dirección (Guardamos el texto completo por seguridad histórica)
            $table->text('shipping_address');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
