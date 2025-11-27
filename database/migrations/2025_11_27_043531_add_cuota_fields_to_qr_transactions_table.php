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
        Schema::table('qr_transactions', function (Blueprint $table) {
            // Campos para manejar pagos de cuotas
            $table->enum('payment_type', ['total', 'primera_cuota', 'segunda_cuota'])->default('total')->after('amount');
            $table->string('description')->nullable()->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'description']);
        });
    }
};
