<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneracionResource;
use App\Models\Generacion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Generación energética por período (RF-08, RF-09).
 */
class GeneracionController extends Controller
{
    /**
     * Listar generaciones (paginado).
     *
     * Filtros: `periodo` (`YYYY-MM`), `desde`, `hasta` (`YYYY-MM`), `departamento_id`, `granja_id`.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $datos = $request->validate([
            'periodo' => ['nullable', 'date_format:Y-m'],
            'desde' => ['nullable', 'date_format:Y-m'],
            'hasta' => ['nullable', 'date_format:Y-m'],
            'departamento_id' => ['nullable', 'integer', 'exists:departamentos,id'],
            'granja_id' => ['nullable', 'integer', 'exists:granjas,id'],
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $generaciones = Generacion::with('granja:id,nombre,departamento_id', 'granja.departamento:id,nombre')
            ->when(isset($datos['periodo']), fn ($q) => $q->where('periodo', $datos['periodo'].'-01'))
            ->when(isset($datos['desde']), fn ($q) => $q->where('periodo', '>=', $datos['desde'].'-01'))
            ->when(isset($datos['hasta']), fn ($q) => $q->where('periodo', '<=', $datos['hasta'].'-01'))
            ->when(isset($datos['granja_id']), fn ($q) => $q->where('granja_id', $datos['granja_id']))
            ->when(isset($datos['departamento_id']), fn ($q) => $q->whereHas('granja', fn ($g) => $g->where('departamento_id', $datos['departamento_id'])))
            ->orderByDesc('periodo')->orderBy('granja_id')
            ->paginate($datos['por_pagina'] ?? 50);

        return GeneracionResource::collection($generaciones);
    }
}
