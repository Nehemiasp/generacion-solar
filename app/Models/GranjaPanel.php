<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Pivot con datos (RF-05): cuántas unidades de un modelo tiene una granja.
class GranjaPanel extends Model
{
    protected $table = 'granja_panel';

    protected $fillable = ['granja_id', 'modelo_panel_id', 'cantidad', 'fecha_instalacion'];

    protected function casts(): array
    {
        return ['cantidad' => 'integer', 'fecha_instalacion' => 'date'];
    }

    public function granja(): BelongsTo
    {
        return $this->belongsTo(Granja::class);
    }

    public function modeloPanel(): BelongsTo
    {
        return $this->belongsTo(ModeloPanel::class);
    }

    public function getCapacidadKwAttribute(): float
    {
        return $this->cantidad * ($this->modeloPanel?->potencia_kw ?? 0);
    }
}
