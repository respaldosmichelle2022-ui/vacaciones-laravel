<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\SaldoVacacion;
use App\Models\MovimientoVacacion;

class DashboardController extends Controller
{
    public function index()
    {
        $sitio = auth()->user()->sitio;

        if ($sitio) {
            $totalEmpleados = Empleado::where('sitio', $sitio)->count();

            $totalSaldos = SaldoVacacion::whereHas('empleado', function($q) use ($sitio) {
                $q->where('sitio', $sitio);
            })->count();

            $totalMovimientos = MovimientoVacacion::whereHas('empleado', function($q) use ($sitio) {
                $q->where('sitio', $sitio);
            })->count();
        } else {
            $totalEmpleados = Empleado::count();

            $totalSaldos = SaldoVacacion::count();

            $totalMovimientos = MovimientoVacacion::count();
        }

        $cumpleanosProximos = [];
        $days = (int)\App\Models\Setting::getVal('birthday_alert_days', 7);
        
        $query = Empleado::where('activo', 1);
        if ($sitio) {
            $query->where('sitio', $sitio);
        }
        $empleados = $query->get();

        $today = \Carbon\Carbon::today();
        foreach ($empleados as $emp) {
            if (!$emp->fecha_nacimiento) continue;
            
            $birthDate = \Carbon\Carbon::parse($emp->fecha_nacimiento);
            $birthdayThisYear = $birthDate->copy()->year($today->year);
            
            if ($birthdayThisYear->isBefore($today)) {
                $birthdayThisYear->addYear();
            }
            
            $diffInDays = (int)$today->diffInDays($birthdayThisYear, false);
            if ($diffInDays >= 0 && $diffInDays <= $days) {
                $cumpleanosProximos[] = [
                    'id' => $emp->id,
                    'numero_empleado' => $emp->numero_empleado,
                    'nombre_completo' => "{$emp->nombre} {$emp->apellido_paterno} {$emp->apellido_materno}",
                    'fecha_nacimiento' => $birthDate->format('d/m/Y'),
                    'fecha_cumple' => $birthdayThisYear->format('d/m/Y'),
                    'dias_restantes' => $diffInDays,
                    'edad_nueva' => $birthdayThisYear->year - $birthDate->year,
                    'puesto' => $emp->puesto,
                    'sitio' => $emp->sitio
                ];
            }
        }
        usort($cumpleanosProximos, function($a, $b) {
            return $a['dias_restantes'] <=> $b['dias_restantes'];
        });

        return view('dashboard.index', compact(
            'totalEmpleados',
            'totalSaldos',
            'totalMovimientos',
            'cumpleanosProximos'
        ));
    }
}