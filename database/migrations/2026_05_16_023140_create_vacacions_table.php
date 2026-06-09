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
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id');
            $table->year('periodo');
            $table->integer('dias_corresponden');
            $table->integer('dias_usados')->default(0);
            $table->integer('dias_restantes');
            $table->date('fecha_inicio_periodo')->nullable();
            $table->date('fecha_fin_periodo')->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
