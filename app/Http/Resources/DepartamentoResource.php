<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartamentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'cabecera' => $this->cabecera,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'granjas_activas' => $this->whenCounted('granjas'),
        ];
    }
}
