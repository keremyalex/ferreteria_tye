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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad_actual', 10, 2)->default(0)->comment('Cantidad actual en stock');
            $table->decimal('cantidad_minima', 10, 2)->default(0)->comment('Nivel mínimo de stock');
            $table->decimal('cantidad_maxima', 10, 2)->nullable()->comment('Nivel máximo de stock');
            $table->decimal('precio_venta', 10, 2)->nullable()->comment('Precio de venta específico del producto en inventario');
            $table->foreignId('producto_id')->unique()->constrained('products')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['cantidad_actual']);
            $table->index(['cantidad_minima']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};