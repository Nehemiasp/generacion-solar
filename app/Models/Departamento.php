<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $fillable = ['nombre', 'codigo', 'cabecera', 'latitud', 'longitud'];

    protected function casts(): array
    {
        return ['latitud' => 'float', 'longitud' => 'float'];
    }

    public function granjas(): HasMany
    {
        return $this->hasMany(Granja::class);
    }
}
