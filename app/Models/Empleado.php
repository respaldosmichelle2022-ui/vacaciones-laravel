<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vacacion;

class Empleado extends Model
{
    protected $table = 'empleados';

protected $fillable = [

    'numero_empleado',
    'nombre',
    'apellido_paterno',
    'apellido_materno',
    'sitio',
    'sucursal',
    'puesto',
    'fecha_ingreso',
    'fecha_nacimiento',
    'activo'

];

    public function vacaciones()
    {
        return $this->hasMany(Vacacion::class);
    }

    public function saldosVacaciones()
{
    return $this->hasMany(SaldoVacacion::class);
}
 public function movimientosVacaciones()
{
    return $this->hasMany(MovimientoVacacion::class);
}

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($empleado) {
            $empleado->vacaciones()->delete();
            $empleado->saldosVacaciones()->delete();
            $empleado->movimientosVacaciones()->delete();
            $empleado->incidencias()->delete();
            if ($empleado->user) {
                $empleado->user->delete();
            }
        });
    }

    public static function calcularDiasVacacionesPorAntiguedad($anos, $anoAnniversario)
    {
        if ($anos < 1) {
            return 0;
        }

        if ($anoAnniversario >= 2023) {
            // Ley actual (Vacaciones Dignas, 2023+)
            if ($anos <= 5) {
                return 10 + (2 * $anos);
            } else {
                return 20 + (2 * (int)ceil(($anos - 5) / 5));
            }
        } else {
            // Ley anterior (Antes de 2023)
            if ($anos == 1) return 6;
            if ($anos == 2) return 8;
            if ($anos == 3) return 10;
            if ($anos == 4) return 12;
            if ($anos <= 9) return 14;
            if ($anos <= 14) return 16;
            if ($anos <= 19) return 18;
            if ($anos <= 24) return 20;
            if ($anos <= 29) return 22;
            return 24 + (2 * (int)ceil(($anos - 29) / 5));
        }
    }
}