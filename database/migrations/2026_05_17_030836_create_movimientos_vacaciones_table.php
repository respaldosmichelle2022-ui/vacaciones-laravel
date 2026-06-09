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
        Schema::create('movimientos_vacaciones', function (Blueprint $table) {

            $table->id();

            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->onDelete('cascade');

            $table->integer('periodo');

            $table->date('fecha_inicio');

            $table->date('fecha_fin');

            $table->integer('dias');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_vacaciones');
    }
};