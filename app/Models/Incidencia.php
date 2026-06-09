<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table = 'incidencias';

    protected $fillable = [
        'empleado_id',
        'tipo',
        'fecha',
        'observaciones'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
