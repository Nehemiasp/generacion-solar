<?php

namespace App\Services;

use App\Enums\EstadoAlerta;
use App\Models\Alerta;
use App\Models\Generacion;

/**
 * RF-14: una generación cuya real <= umbral × esperada produce (o actualiza) una alerta.
 * Si deja de cumplirse, la alerta se elimina.
 */
class AlertaService
{
    public function evaluar(Generacion $generacion): ?Alerta
    {
        if (! $generacion->bajo_desempeno) {
            Alerta::where('generacion_id', $generacion->id)->delete();

            return null;
        }

        return Alerta::updateOrCreate(
            ['generacion_id' => $generacion->id],
            [
                'granja_id' => $generacion->granja_id,
                'periodo' => $generacion->periodo,
                'generacion_esperada_kwh' => $generacion->generacion_esperada_kwh,
                'generacion_real_kwh' => $generacion->generacion_real_kwh,
                'porcentaje_desviacion' => $generacion->porcentaje_desviacion,
            ],
        );
    }

    /** Recorre todas las generaciones y reconstruye las alertas (útil tras cargas masivas). */
    public function reconstruirTodo(): int
    {
        $creadas = 0;

        Generacion::query()->orderBy('id')->chunkById(500, function ($generaciones) use (&$creadas) {
            foreach ($generaciones as $generacion) {
                if ($this->evaluar($generacion)) {
                    $creadas++;
                }
            }
        });

        // Alertas huérfanas (generación borrada o ya no en umbral) quedan eliminadas por evaluar() o por FK cascade.
        return $creadas;
    }

    public function activas(): int
    {
        return Alerta::where('estado', EstadoAlerta::Activa)->count();
    }
}
