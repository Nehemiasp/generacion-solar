<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlertaResource;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

/**
 * Alertas de generación (RF-14).
 */
class AlertaController extends Controller
{
    /**
     * Listar alertas (paginado).
     *
     * Filtros: `estado` (activa, revisada, resuelta), `departamento_id`, `granja_id`. Por defecto solo activas.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $datos = $request->validate([
            'estado' => ['nullable', Rule::in(['activa', 'revisada', 'resuelta', 'todas'])],
            'departamento_id' => ['nullable', 'integer', 'exists:departamentos,id'],
            'granja_id' => ['nullable', 'integer', 'exists:granjas,id'],
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);
        $estado = $datos['estado'] ?? 'activa';

        $alertas = Alerta::with('granja:id,nombre,departamento_id', 'granja.departamento:id,nombre')
            ->when($estado !== 'todas', fn ($q) => $q->where('estado', $estado))
            ->when(isset($datos['granja_id']), fn ($q) => $q->where('granja_id', $datos['granja_id']))
            ->when(isset($datos['departamento_id']), fn ($q) => $q->whereHas('granja', fn ($g) => $g->where('departamento_id', $datos['departamento_id'])))
            ->orderByDesc('periodo')->orderBy('porcentaje_desviacion')
            ->paginate($datos['por_pagina'] ?? 50);

        return AlertaResource::collection($alertas);
    }
}
