<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoVacacion extends Model
{
    protected $table = 'saldo_vacaciones';

    protected $fillable = [

        'empleado_id',
        'periodo',
        'dias_corresponden',
        'dias_restantes'

    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public static function sincronizarSaldos($empleadoId)
    {
        $empleado = Empleado::find($empleadoId);
        if (!$empleado || !$empleado->fecha_ingreso) {
            return;
        }

        $ingreso = \Carbon\Carbon::parse($empleado->fecha_ingreso);
        $hoy = \Carbon\Carbon::today();

        $anos = 0;
        while (true) {
            $proximoAniversario = (clone $ingreso)->addYears($anos + 1);
            if ($proximoAniversario->greaterThan($hoy)) {
                break;
            }
            $anos++;
        }

        if ($anos > 0) {
            $aniversario = (clone $ingreso)->addYears($anos);
            $periodo = $aniversario->year;

            // Solo generar saldos para periodos 2026 en adelante
            if ($periodo < 2026) {
                return;
            }

            // Verificar si ya existe un saldo para este periodo
            $existe = self::where('empleado_id', $empleadoId)
                ->where('periodo', $periodo)
                ->exists();

            if (!$existe) {
                $dias = Empleado::calcularDiasVacacionesPorAntiguedad($anos, $periodo);
                self::create([
                    'empleado_id' => $empleadoId,
                    'periodo' => $periodo,
                    'dias_corresponden' => $dias,
                    'dias_restantes' => $dias
                ]);
            }
        }
    }

    public static function sincronizarTodos()
    {
        $empleados = Empleado::where('activo', 1)->get();
        foreach ($empleados as $empleado) {
            self::sincronizarSaldos($empleado->id);
        }
    }
}