<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacacion;
use App\Models\Empleado;

class VacacionController extends Controller
{

    public function index()
    {
        $vacaciones = Vacacion::with('empleado')->get();

        return view('vacaciones.index', compact('vacaciones'));
    }

    public function crear()
    {
        $empleados = Empleado::orderByRaw('CAST(numero_empleado AS DECIMAL)')->get();

        return view('vacaciones.crear', compact('empleados'));
    }

    public function guardar(Request $request)
    {

        Vacacion::create([

            'empleado_id' => $request->empleado_id,
            'periodo' => $request->periodo,
            'dias_corresponden' => $request->dias_corresponden,
            'dias_usados' => $request->dias_usados,
            'dias_restantes' => $request->dias_restantes,
            'fecha_inicio_periodo' => $request->fecha_inicio_periodo,
            'fecha_fin_periodo' => $request->fecha_fin_periodo,
            'activo' => 1

        ]);

        return redirect('/vacaciones');

    }

    public function editar($id)
    {

        $vacacion = Vacacion::findOrFail($id);

        $empleados = Empleado::orderByRaw('CAST(numero_empleado AS DECIMAL)')->get();

        return view('vacaciones.editar', compact('vacacion', 'empleados'));

    }

    public function actualizar(Request $request, $id)
    {

        $vacacion = Vacacion::findOrFail($id);

        $vacacion->update([

            'empleado_id' => $request->empleado_id,
            'periodo' => $request->periodo,
            'dias_corresponden' => $request->dias_corresponden,
            'dias_usados' => $request->dias_usados,
            'dias_restantes' => $request->dias_restantes,
            'fecha_inicio_periodo' => $request->fecha_inicio_periodo,
            'fecha_fin_periodo' => $request->fecha_fin_periodo,

        ]);

        return redirect('/vacaciones');

    }

    public function eliminar($id)
{

    $vacacion = Vacacion::findOrFail($id);

    $vacacion->delete();

    return redirect('/vacaciones');
    }

}