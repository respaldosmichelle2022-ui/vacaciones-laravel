<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    protected $table = 'vacaciones';

    protected $fillable = [

        'empleado_id',
        'periodo',
        'dias_corresponden',
        'dias_usados',
        'dias_restantes',
        'fecha_inicio_periodo',
        'fecha_fin_periodo',
        'activo'

    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}