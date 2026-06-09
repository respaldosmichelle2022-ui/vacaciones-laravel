<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoVacacion extends Model
{
    protected $table = 'movimientos_vacaciones';

    protected $fillable = [

        'empleado_id',
        'periodo',
        'fecha_inicio',
        'fecha_fin',
        'dias',
        'salario_diario',
        'prima_vacacional'

    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}