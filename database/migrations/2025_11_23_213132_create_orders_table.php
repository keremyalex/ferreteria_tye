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
            $table->string('numero_orden')->unique();
            $table->enum('tipo', ['online', 'presencial'])->default('online');
            $table->enum('estado', ['pendiente', 'confirmado', 'en_proceso', 'listo_retiro', 'entregado', 'completado', 'cancelado'])->default('pendiente');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->json('direccion_facturacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('metodo_pago', ['contraentrega', 'transferencia', 'tarjeta', 'efectivo', 'qr'])->default('contraentrega');
            $table->enum('estado_pago', ['pendiente', 'pagado', 'fallido'])->default('pendiente');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('cliente_id')->nullable()->constrained('clients')->onDelete('restrict');
            $table->foreignId('vendedor_id')->nullable()->constrained('users')->onDelete('restrict');
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
