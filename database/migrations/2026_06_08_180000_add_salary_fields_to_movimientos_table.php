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
        Schema::table('movimientos_vacaciones', function (Blueprint $table) {
            $table->decimal('salario_diario', 10, 2)->nullable()->after('dias');
            $table->decimal('prima_vacacional', 10, 2)->nullable()->after('salario_diario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_vacaciones', function (Blueprint $table) {
            $table->dropColumn(['salario_diario', 'prima_vacacional']);
        });
    }
};
