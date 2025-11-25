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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->timestamp('fecha');
            $table->string('referencia')->nullable()->comment('Número de compra, venta, ajuste, etc.');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['aplicado', 'pendiente', 'cancelado'])->default('aplicado');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['tipo', 'fecha']);
            $table->index(['referencia']);
            $table->index(['estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
