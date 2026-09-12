<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GranjaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'departamento' => new DepartamentoResource($this->whenLoaded('departamento')),
            'municipio' => $this->municipio,
            'direccion' => $this->direccion,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'familias_beneficiadas' => $this->familias_beneficiadas,
            'generacion_esperada_mensual_kwh' => $this->generacion_esperada_mensual_kwh,
            'fecha_instalacion' => $this->fecha_instalacion?->toDateString(),
            'activa' => $this->activa,
            'capacidad_instalada_kw' => round($this->capacidad_instalada_kw, 3),
            'total_paneles' => $this->total_paneles,
            'generacion_acumulada_kwh' => round($this->generacion_acumulada_kwh, 2),
            'co2_evitado_kg' => round($this->co2_evitado_kg, 2),
            'alertas_activas' => $this->whenCounted('alertasActivas'),
            'paneles' => $this->whenLoaded('paneles', fn () => $this->paneles->map(fn ($p) => [
                'modelo_panel_id' => $p->modelo_panel_id,
                'marca' => $p->modeloPanel->marca,
                'modelo' => $p->modeloPanel->modelo,
                'potencia_kw' => $p->modeloPanel->potencia_kw,
                'cantidad' => $p->cantidad,
                'capacidad_kw' => round($p->capacidad_kw, 3),
            ])),
        ];
    }
}
