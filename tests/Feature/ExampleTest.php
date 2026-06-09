<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guest is redirected to login', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

test('admin can access dashboard', function () {
    $admin = \App\Models\User::factory()->create(['role' => 'administrador']);
    
    $response = $this->actingAs($admin)->get('/');
    $response->assertStatus(200);
});

test('deleting employee deletes associated user and saldos', function () {
    $admin = \App\Models\User::factory()->create(['role' => 'administrador']);
    
    // Create an employee
    $empleado = \App\Models\Empleado::create([
        'numero_empleado' => '999',
        'nombre' => 'Test',
        'apellido_paterno' => 'Cascade',
        'fecha_ingreso' => '2020-01-01',
        'sitio' => 'SITIO_TEST',
        'sucursal' => 'Sucursal',
        'puesto' => 'Developer',
    ]);
    
    // Create user and saldo
    $user = \App\Models\User::create([
        'name' => 'Test Cascade',
        'email' => 'cascade@test.com',
        'password' => bcrypt('password'),
        'role' => 'empleado',
        'empleado_id' => $empleado->id
    ]);

    $saldo = \App\Models\SaldoVacacion::create([
        'empleado_id' => $empleado->id,
        'periodo' => 2021,
        'dias_corresponden' => 14,
        'dias_restantes' => 14
    ]);

    // Verify they exist
    $this->assertDatabaseHas('empleados', ['id' => $empleado->id]);
    $this->assertDatabaseHas('users', ['id' => $user->id]);
    $this->assertDatabaseHas('saldo_vacaciones', ['id' => $saldo->id]);

    // Send delete request
    $response = $this->actingAs($admin)->delete("/empleados/eliminar/{$empleado->id}");
    $response->assertRedirect('/empleados');

    // Verify all are deleted
    $this->assertDatabaseMissing('empleados', ['id' => $empleado->id]);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertDatabaseMissing('saldo_vacaciones', ['id' => $saldo->id]);
});
