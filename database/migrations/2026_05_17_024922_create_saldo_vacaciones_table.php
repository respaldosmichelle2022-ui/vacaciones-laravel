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
        Schema::create('saldo_vacaciones', function (Blueprint $table) {

            $table->id();

            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->onDelete('cascade');

            $table->integer('periodo');

            $table->integer('dias_corresponden');

            $table->integer('dias_restantes');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_vacaciones');
    }
};