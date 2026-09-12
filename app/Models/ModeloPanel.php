<?php

namespace App\Models;

use App\Enums\EstadoPanel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeloPanel extends Model
{
    protected $table = 'modelos_panel';

    protected $fillable = ['marca', 'modelo', 'potencia_kw', 'eficiencia', 'estado'];

    protected function casts(): array
    {
        return [
            'potencia_kw' => 'float',
            'eficiencia' => 'float',
            'estado' => EstadoPanel::class,
        ];
    }

    public function instalaciones(): HasMany
    {
        return $this->hasMany(GranjaPanel::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->marca} {$this->modelo}";
    }
}
