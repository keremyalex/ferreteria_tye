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
        Schema::create('inventory_movement_details', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->string('lote')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('movimiento_id')->constrained('inventory_movements')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('products')->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['movimiento_id']);
            $table->index(['producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movement_details');
    }
};