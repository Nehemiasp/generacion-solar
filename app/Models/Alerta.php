<?php

namespace App\Models;

use App\Enums\EstadoAlerta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $fillable = [
        'granja_id', 'generacion_id', 'periodo', 'generacion_esperada_kwh',
        'generacion_real_kwh', 'porcentaje_desviacion', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'periodo' => 'date',
            'generacion_esperada_kwh' => 'float',
            'generacion_real_kwh' => 'float',
            'porcentaje_desviacion' => 'float',
            'estado' => EstadoAlerta::class,
        ];
    }

    public function granja(): BelongsTo
    {
        return $this->belongsTo(Granja::class);
    }

    public function generacion(): BelongsTo
    {
        return $this->belongsTo(Generacion::class);
    }

    public function getGraveAttribute(): bool
    {
        return $this->porcentaje_desviacion <= config('solar.umbral_alerta_grave');
    }
}
