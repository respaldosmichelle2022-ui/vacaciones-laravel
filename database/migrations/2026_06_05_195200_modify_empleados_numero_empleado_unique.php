<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            // Validar que la columna exista antes de intentar eliminar el índice
            if (Schema::hasColumn('empleados', 'numero_empleado')) {
                try {
                    $table->dropUnique(['numero_empleado']);
                } catch (\Exception $e) {
                    // Ignorar el error si el índice no existe
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            // Restaurar el índice único si la columna existe
            if (Schema::hasColumn('empleados', 'numero_empleado')) {
                $table->unique('numero_empleado');
            }
        });
    }
};
