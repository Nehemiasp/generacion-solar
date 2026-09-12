<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EstadisticasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Indicadores agregados (RF-11, RF-12).
 */
class EstadisticaController extends Controller
{
    public function __construct(private EstadisticasService $estadisticas) {}

    /**
     * Totales nacionales.
     *
     * Granjas, paneles, capacidad kW, generación kWh, familias, CO₂ (kg y t), alertas activas.
     * Filtros opcionales `desde` y `hasta` (`YYYY-MM`) sobre la generación.
     */
    public function nacional(Request $request): JsonResponse
    {
        [$desde, $hasta] = $this->rango($request);

        return response()->json([
            'data' => $this->estadisticas->nacional($desde, $hasta),
            'serie_12_meses' => $this->estadisticas->serieNacional(12),
        ]);
    }

    /**
     * Reporte por departamento (los 22).
     *
     * Filtros opcionales `desde` y `hasta` (`YYYY-MM`) sobre la generación. Ordenado por generación descendente.
     */
    public function departamentos(Request $request): JsonResponse
    {
        [$desde, $hasta] = $this->rango($request);

        return response()->json([
            'data' => $this->estadisticas->porDepartamento($desde, $hasta)->sortByDesc('generacion_kwh')->values(),
        ]);
    }

    private function rango(Request $request): array
    {
        $datos = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m'],
            'hasta' => ['nullable', 'date_format:Y-m'],
        ]);

        return [
            isset($datos['desde']) ? $datos['desde'].'-01' : null,
            isset($datos['hasta']) ? $datos['hasta'].'-01' : null,
        ];
    }
}
