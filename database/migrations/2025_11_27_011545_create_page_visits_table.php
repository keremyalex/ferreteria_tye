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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('page_url', 500); // URL de la página
            $table->string('page_name', 100); // Nombre descriptivo de la página
            $table->unsignedBigInteger('visits_count')->default(0); // Contador de visitas
            $table->timestamp('last_visited_at')->nullable(); // Última visita
            $table->timestamps();
            
            // Índice único para la URL
            $table->unique('page_url');
            $table->index(['page_name', 'visits_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
