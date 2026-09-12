<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneracionResource;
use App\Http\Resources\GranjaResource;
use App\Models\Granja;
use App\Services\EstadisticasService;
use App\Services\ProyeccionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Granjas solares (RF-03, RF-04, RF-06, RF-13, RF-15).
 */
class GranjaController extends Controller
{
    /**
     * Listar granjas (paginado).
     *
     * Filtros: `departamento_id`, `activa` (true/false), `buscar` (por nombre).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $datos = $request->validate([
            'departamento_id' => ['nullable', 'integer', 'exists:departamentos,id'],
            // acepta 1/0 y true/false, que es lo que manda un cliente HTTP típico
            'activa' => ['nullable', 'in:0,1,true,false'],
            'buscar' => ['nullable', 'string', 'max:100'],
            'por_pagina' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $granjas = Granja::conMetricas()->with('departamento')
            ->when(isset($datos['departamento_id']), fn ($q) => $q->where('departamento_id', $datos['departamento_id']))
            ->when(isset($datos['activa']), fn ($q) => $q->where('activa', filter_var($datos['activa'], FILTER_VALIDATE_BOOLEAN)))
            ->when(isset($datos['buscar']), fn ($q) => $q->where('nombre', 'like', '%'.$datos['buscar'].'%'))
            ->orderBy('nombre')
            ->paginate($datos['por_pagina'] ?? 20);

        return GranjaResource::collection($granjas);
    }

    /**
     * Detalle de una granja con sus paneles y métricas calculadas.
     */
    public function show(Granja $granja): GranjaResource
    {
        $granja = Granja::conMetricas()->with(['departamento', 'paneles.modeloPanel'])->findOrFail($granja->id);

        return new GranjaResource($granja);
    }

    /**
     * Payload liviano de granjas activas para el mapa.
     */
    public function mapa(EstadisticasService $estadisticas): JsonResponse
    {
        return response()->json(['data' => $estadisticas->granjasMapa()]);
    }

    /**
     * Generaciones de la granja por período.
     *
     * Filtros opcionales `desde` y `hasta` en formato `YYYY-MM`.
     */
    public function generaciones(Granja $granja, Request $request): AnonymousResourceCollection
    {
        $datos = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m'],
            'hasta' => ['nullable', 'date_format:Y-m'],
        ]);

        $generaciones = $granja->generaciones()
            ->when(isset($datos['desde']), fn ($q) => $q->where('periodo', '>=', $datos['desde'].'-01'))
            ->when(isset($datos['hasta']), fn ($q) => $q->where('periodo', '<=', $datos['hasta'].'-01'))
            ->get();

        return GeneracionResource::collection($generaciones);
    }

    /**
     * Proyección de generación futura (RF-15).
     *
     * Regresión lineal por mínimos cuadrados sobre los últimos 12 períodos; promedio si hay menos de 3.
     * `meses` entre 1 y 12 (por defecto 3). Incluye la comparación histórica proyección vs real.
     */
    public function proyeccion(Granja $granja, Request $request, ProyeccionService $proyeccion): JsonResponse
    {
        $datos = $request->validate(['meses' => ['nullable', 'integer', 'min:1', 'max:12']]);
        $meses = (int) ($datos['meses'] ?? 3);

        return response()->json([
            'data' => [
                'granja_id' => $granja->id,
                'granja' => $granja->nombre,
                'proyeccion' => $proyeccion->proyectar($granja, $meses),
                'precision_historica' => $proyeccion->compararProyeccionVsReal($granja),
            ],
        ]);
    }
}
