<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empleado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear el usuario Administrador
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('admin123'),
                'role' => 'administrador',
                'empleado_id' => null
            ]
        );

        // 2. Asociar usuarios a los empleados existentes para pruebas rápidas
        $jorge = Empleado::where('numero_empleado', '1')->first();
        if ($jorge) {
            User::updateOrCreate(
                ['email' => 'jorge@empresa.com'],
                [
                    'name' => 'Jorge Benitez',
                    'password' => Hash::make('password'),
                    'role' => 'empleado',
                    'empleado_id' => $jorge->id
                ]
            );
        }

        $maria = Empleado::where('numero_empleado', '24')->first();
        if ($maria) {
            User::updateOrCreate(
                ['email' => 'maria@empresa.com'],
                [
                    'name' => 'Maria Perez',
                    'password' => Hash::make('password'),
                    'role' => 'empleado',
                    'empleado_id' => $maria->id
                ]
            );
        }
    }
}
