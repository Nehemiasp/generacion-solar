<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'granja_id' => $this->granja_id,
            'granja' => $this->whenLoaded('granja', fn () => [
                'id' => $this->granja->id,
                'nombre' => $this->granja->nombre,
                'departamento' => $this->granja->departamento?->nombre,
            ]),
            'periodo' => $this->periodo->toDateString(),
            'generacion_esperada_kwh' => $this->generacion_esperada_kwh,
            'generacion_real_kwh' => $this->generacion_real_kwh,
            'porcentaje_desviacion' => $this->porcentaje_desviacion,
            'grave' => $this->grave,
            'estado' => $this->estado,
        ];
    }
}
