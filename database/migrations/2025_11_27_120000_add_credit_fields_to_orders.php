<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Campos para manejo de crédito
            $table->enum('tipo_pago', ['contado', 'credito'])->default('contado')->after('metodo_pago');
            $table->decimal('primer_cuota', 10, 2)->nullable()->after('tipo_pago');
            $table->decimal('segunda_cuota', 10, 2)->nullable()->after('primer_cuota');
            $table->boolean('primer_cuota_pagada')->default(false)->after('segunda_cuota');
            $table->boolean('segunda_cuota_pagada')->default(false)->after('primer_cuota_pagada');
            $table->date('fecha_vencimiento_segunda_cuota')->nullable()->after('segunda_cuota_pagada');
            $table->datetime('fecha_pago_primer_cuota')->nullable()->after('fecha_vencimiento_segunda_cuota');
            $table->datetime('fecha_pago_segunda_cuota')->nullable()->after('fecha_pago_primer_cuota');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_pago', 'primer_cuota', 'segunda_cuota',
                'primer_cuota_pagada', 'segunda_cuota_pagada',
                'fecha_vencimiento_segunda_cuota',
                'fecha_pago_primer_cuota', 'fecha_pago_segunda_cuota'
            ]);
        });
    }
};