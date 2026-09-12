<?php

namespace App\Models;

use App\Observers\GeneracionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(GeneracionObserver::class)]
class Generacion extends Model
{
    protected $table = 'generaciones';

    protected $fillable = ['granja_id', 'periodo', 'generacion_real_kwh', 'generacion_esperada_kwh'];

    protected function casts(): array
    {
        return [
            'periodo' => 'date',
            'generacion_real_kwh' => 'float',
            'generacion_esperada_kwh' => 'float',
        ];
    }

    public function granja(): BelongsTo
    {
        return $this->belongsTo(Granja::class);
    }

    public function alerta(): HasOne
    {
        return $this->hasOne(Alerta::class);
    }

    /** (real − esperada) / esperada × 100. Negativo = por debajo. */
    public function getPorcentajeDesviacionAttribute(): ?float
    {
        if ($this->generacion_esperada_kwh <= 0) {
            return null;
        }

        return round(($this->generacion_real_kwh - $this->generacion_esperada_kwh) / $this->generacion_esperada_kwh * 100, 2);
    }

    /** RF-14: real <= umbral × esperada. */
    public function getBajoDesempenoAttribute(): bool
    {
        return $this->generacion_esperada_kwh > 0
            && $this->generacion_real_kwh <= $this->generacion_esperada_kwh * config('solar.umbral_alerta');
    }
}
