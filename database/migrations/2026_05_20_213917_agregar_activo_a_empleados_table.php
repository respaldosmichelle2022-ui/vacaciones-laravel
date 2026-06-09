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
    if (!Schema::hasColumn('empleados', 'activo')) {
        Schema::table('empleados', function (Blueprint $table) {
            $table->boolean('activo')
                  ->default(1);
        });
    }
}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    if (Schema::hasColumn('empleados', 'activo')) {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
}
};