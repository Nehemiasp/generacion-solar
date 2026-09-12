<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartamentoResource;
use App\Models\Departamento;
use App\Services\EstadisticasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Departamentos de Guatemala (RF-01).
 */
class DepartamentoController extends Controller
{
    /**
     * Listar los 22 departamentos.
     *
     * Incluye el conteo de granjas activas de cada uno.
     */
    public function index(): AnonymousResourceCollection
    {
        return DepartamentoResource::collection(
            Departamento::withCount(['granjas' => fn ($q) => $q->activas()])->orderBy('nombre')->get()
        );
    }

    /**
     * Detalle de un departamento con estadísticas agregadas.
     *
     * Devuelve granjas, paneles, capacidad instalada, generación, familias y CO₂ evitado.
     */
    public function show(Departamento $departamento, EstadisticasService $estadisticas): JsonResponse
    {
        $fila = $estadisticas->porDepartamento()->firstWhere('id', $departamento->id);

        return response()->json(['data' => $fila]);
    }
}
