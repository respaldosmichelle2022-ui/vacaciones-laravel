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
    Schema::table('empleados', function (Blueprint $table) {
        if (!Schema::hasColumn('empleados', 'sitio')) {
            $table->string('sitio')->nullable();
        }
        if (!Schema::hasColumn('empleados', 'sucursal')) {
            $table->string('sucursal')->nullable();
        }
        if (!Schema::hasColumn('empleados', 'puesto')) {
            $table->string('puesto')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('empleados', function (Blueprint $table) {
        $cols = [];
        if (Schema::hasColumn('empleados', 'sitio')) {
            $cols[] = 'sitio';
        }
        if (Schema::hasColumn('empleados', 'sucursal')) {
            $cols[] = 'sucursal';
        }
        // Do not drop 'puesto' if it was part of the original table
        // but if it is in the array, let's keep it safe.
        if (count($cols) > 0) {
            $table->dropColumn($cols);
        }
    });
}
};