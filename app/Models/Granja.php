<?php

namespace App\Models;

use App\Enums\EstadoAlerta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Granja extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'departamento_id', 'municipio', 'direccion', 'latitud', 'longitud',
        'familias_beneficiadas', 'generacion_esperada_mensual_kwh', 'fecha_instalacion', 'activa',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'familias_beneficiadas' => 'integer',
            'generacion_esperada_mensual_kwh' => 'float',
            'fecha_instalacion' => 'date',
            'activa' => 'boolean',
        ];
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function paneles(): HasMany
    {
        return $this->hasMany(GranjaPanel::class);
    }

    public function generaciones(): HasMany
    {
        return $this->hasMany(Generacion::class)->orderBy('periodo');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class);
    }

    public function alertasActivas(): HasMany
    {
        return $this->hasMany(Alerta::class)->where('estado', EstadoAlerta::Activa);
    }

    // ---- Valores derivados (RF-06, RF-10). Nunca columnas. ----

    /** Subconsultas para listar sin N+1: capacidad_instalada_kw, total_paneles, generacion_acumulada_kwh, alertas_activas_count. */
    public function scopeConMetricas(Builder $query): Builder
    {
        return $query
            ->addSelect([
                'capacidad_instalada_kw' => GranjaPanel::query()
                    ->selectRaw('COALESCE(SUM(granja_panel.cantidad * modelos_panel.potencia_kw), 0)')
                    ->join('modelos_panel', 'modelos_panel.id', '=', 'granja_panel.modelo_panel_id')
                    ->whereColumn('granja_panel.granja_id', 'granjas.id'),
                'total_paneles' => GranjaPanel::query()
                    ->selectRaw('COALESCE(SUM(cantidad), 0)')
                    ->whereColumn('granja_panel.granja_id', 'granjas.id'),
                'generacion_acumulada_kwh' => Generacion::query()
                    ->selectRaw('COALESCE(SUM(generacion_real_kwh), 0)')
                    ->whereColumn('generaciones.granja_id', 'granjas.id'),
            ])
            ->withCount('alertasActivas');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }

    public function getCapacidadInstaladaKwAttribute(): float
    {
        if (array_key_exists('capacidad_instalada_kw', $this->attributes)) {
            return (float) $this->attributes['capacidad_instalada_kw'];
        }

        return (float) $this->paneles()
            ->join('modelos_panel', 'modelos_panel.id', '=', 'granja_panel.modelo_panel_id')
            ->sum(DB::raw('granja_panel.cantidad * modelos_panel.potencia_kw'));
    }

    public function getTotalPanelesAttribute(): int
    {
        if (array_key_exists('total_paneles', $this->attributes)) {
            return (int) $this->attributes['total_paneles'];
        }

        return (int) $this->paneles()->sum('cantidad');
    }

    public function getGeneracionAcumuladaKwhAttribute(): float
    {
        if (array_key_exists('generacion_acumulada_kwh', $this->attributes)) {
            return (float) $this->attributes['generacion_acumulada_kwh'];
        }

        return (float) $this->generaciones()->sum('generacion_real_kwh');
    }

    public function getCo2EvitadoKgAttribute(): float
    {
        return $this->generacion_acumulada_kwh * config('solar.factor_co2_kg_por_kwh');
    }
}
