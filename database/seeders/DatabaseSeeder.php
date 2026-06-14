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
        // 1. Crear el usuario Administrador si no existe ninguno
        $adminExiste = User::where('role', 'administrador')->exists();
        if (!$adminExiste) {
            User::create([
                'name' => 'Administrador General',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'role' => 'administrador',
                'empleado_id' => null
            ]);
        }

        // 2. Asociar usuarios a los empleados de prueba únicamente si es una instalación limpia (1 usuario o menos)
        if (User::count() <= 1) {
            $jorge = Empleado::where('numero_empleado', '1')->first();
            if ($jorge) {
                User::firstOrCreate(
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
                User::firstOrCreate(
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
}
