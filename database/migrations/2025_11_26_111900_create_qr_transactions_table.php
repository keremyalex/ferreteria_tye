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
        Schema::create('qr_transactions', function (Blueprint $table) {
            $table->id();
            
            // Identificadores únicos
            $table->string('payment_number')->unique(); // Ej: grupo03sc_0001
            $table->string('transaction_id')->nullable(); // ID de transacción de PagoFácil
            
            // Relación con orden
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            
            // Datos del cliente
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone');
            $table->string('client_document_id')->nullable();
            $table->tinyInteger('document_type')->default(1); // 1=CI, 2=Pasaporte, etc.
            $table->string('client_code'); // ID del usuario/cliente para PagoFácil
            
            // Datos de pago
            $table->decimal('amount', 10, 2); // Monto en bolivianos
            $table->tinyInteger('currency')->default(2); // 2=BOB
            $table->tinyInteger('payment_method')->default(4); // 4=QR
            
            // Estado de la transacción
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled', 'failed'])->default('pending');
            
            // Datos de respuesta de PagoFácil
            $table->text('access_token')->nullable(); // Token de la transacción
            $table->timestamp('expires_at')->nullable(); // Expiración del QR
            
            // Respuestas de la API
            $table->json('generate_response')->nullable(); // Respuesta del generate-qr
            $table->json('verify_response')->nullable(); // Última respuesta de verify
            $table->json('callback_data')->nullable(); // Datos del callback
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['status']);
            $table->index(['payment_number']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_transactions');
    }
};
