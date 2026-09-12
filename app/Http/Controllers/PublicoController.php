<?php

namespace App\Http\Controllers;

use App\Enums\EstadoAlerta;
use App\Models\Alerta;
use App\Models\Departamento;
use App\Models\Granja;
use App\Services\EstadisticasService;
use App\Services\ProyeccionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Frontend público: se construye fuera de Filament con los tokens de docs/DISENO.md.
class PublicoController extends Controller
{
    public function __construct(
        private EstadisticasService $estadisticas,
        private ProyeccionService $proyeccion,
    ) {}

    public function inicio(): View
    {
        $departamentos = $this->estadisticas->porDepartamento();
        $serie = $this->estadisticas->serieNacional(12);

        return view('publico.inicio', [
            'nacional' => $this->estadisticas->nacional(),
            'serie' => $serie,
            'cortesMeses' => EstadisticasService::quintiles(array_column($serie, 'real_kwh')),
            'departamentos' => $departamentos,
            'cortesDeptos' => EstadisticasService::quintiles($departamentos->pluck('generacion_kwh')->all()),
            'series' => $this->estadisticas->seriesPorDepartamento(12),
            'alertas' => Alerta::with('granja.departamento')->where('estado', EstadoAlerta::Activa)
                ->orderByDesc('periodo')->orderBy('porcentaje_desviacion')->limit(12)->get(),
        ]);
    }

    public function mapa(): View
    {
        return view('publico.mapa', [
            'departamentos' => Departamento::orderBy('nombre')->withCount(['granjas' => fn ($q) => $q->activas()])->get(),
        ]);
    }

    public function reporte(Request $request): View
    {
        $datos = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m'],
            'hasta' => ['nullable', 'date_format:Y-m', 'after_or_equal:desde'],
            'orden' => ['nullable', 'in:nombre,granjas,paneles,capacidad_instalada_kw,generacion_kwh,familias_beneficiadas,co2_evitado_kg,alertas_activas'],
            'dir' => ['nullable', 'in:asc,desc'],
        ]);

        $desde = isset($datos['desde']) ? $datos['desde'].'-01' : null;
        $hasta = isset($datos['hasta']) ? $datos['hasta'].'-01' : null;
        $orden = $datos['orden'] ?? 'generacion_kwh';
        $dir = $datos['dir'] ?? ($orden === 'nombre' ? 'asc' : 'desc');

        $filas = $this->estadisticas->porDepartamento($desde, $hasta)
            ->sortBy($orden, SORT_REGULAR, $dir === 'desc')->values();

        return view('publico.reporte', [
            'filas' => $filas,
            'cortes' => EstadisticasService::quintiles($filas->pluck('generacion_kwh')->all()),
            'desde' => $datos['desde'] ?? null,
            'hasta' => $datos['hasta'] ?? null,
            'orden' => $orden,
            'dir' => $dir,
            'totales' => [
                'granjas' => $filas->sum('granjas'),
                'paneles' => $filas->sum('paneles'),
                'capacidad_instalada_kw' => $filas->sum('capacidad_instalada_kw'),
                'generacion_kwh' => $filas->sum('generacion_kwh'),
                'familias_beneficiadas' => $filas->sum('familias_beneficiadas'),
                'co2_evitado_kg' => $filas->sum('co2_evitado_kg'),
            ],
        ]);
    }

    public function alertas(Request $request): View
    {
        $estado = $request->query('estado', EstadoAlerta::Activa->value);

        return view('publico.alertas', [
            'estado' => $estado,
            'alertas' => Alerta::with('granja.departamento')
                ->when($estado !== 'todas', fn ($q) => $q->where('estado', $estado))
                ->orderByDesc('periodo')->orderBy('porcentaje_desviacion')->paginate(40)->withQueryString(),
        ]);
    }

    public function granja(Granja $granja): View
    {
        $granja->load(['departamento', 'paneles.modeloPanel', 'alertasActivas']);
        $generaciones = $granja->generaciones()->get();

        return view('publico.granja', [
            'granja' => $granja,
            'generaciones' => $generaciones,
            'proyeccion' => $this->proyeccion->proyectar($granja, 3),
            'comparacion' => $this->proyeccion->compararProyeccionVsReal($granja),
        ]);
    }
}
