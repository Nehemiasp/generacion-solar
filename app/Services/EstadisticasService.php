<?php

namespace App\Services;

use App\Enums\EstadoAlerta;
use App\Models\Alerta;
use App\Models\Departamento;
use App\Models\Generacion;
use App\Models\Granja;
use App\Models\GranjaPanel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Consultas agregadas del tablero (RF-11, RF-12). Todo se calcula en SQL;
 * nunca se cargan colecciones completas en memoria. Solo granjas activas.
 */
class EstadisticasService
{
    public function nacional(?string $desde = null, ?string $hasta = null): array
    {
        $granjas = Granja::activas();
        $granjaIds = (clone $granjas)->select('id');

        $generacion = (float) Generacion::whereIn('granja_id', $granjaIds)
            ->when($desde, fn ($q) => $q->where('periodo', '>=', $desde))
            ->when($hasta, fn ($q) => $q->where('periodo', '<=', $hasta))
            ->sum('generacion_real_kwh');

        return [
            'granjas' => (clone $granjas)->count(),
            'paneles' => (int) GranjaPanel::whereIn('granja_id', $granjaIds)->sum('cantidad'),
            'capacidad_instalada_kw' => (float) GranjaPanel::whereIn('granja_id', $granjaIds)
                ->join('modelos_panel', 'modelos_panel.id', '=', 'granja_panel.modelo_panel_id')
                ->selectRaw('COALESCE(SUM(granja_panel.cantidad * modelos_panel.potencia_kw), 0) as total')
                ->value('total'),
            'generacion_acumulada_kwh' => $generacion,
            'familias_beneficiadas' => (int) (clone $granjas)->sum('familias_beneficiadas'),
            'co2_evitado_kg' => $generacion * config('solar.factor_co2_kg_por_kwh'),
            'co2_evitado_t' => $generacion * config('solar.factor_co2_kg_por_kwh') / 1000,
            'alertas_activas' => Alerta::where('estado', EstadoAlerta::Activa)->count(),
            'departamentos_con_granjas' => (clone $granjas)->distinct()->count('departamento_id'),
        ];
    }

    /** RF-12: una fila por departamento (los 22, aunque no tengan granjas). */
    public function porDepartamento(?string $desde = null, ?string $hasta = null): Collection
    {
        $factor = config('solar.factor_co2_kg_por_kwh');

        $granjasDelDepto = fn (): Builder => Granja::activas()->whereColumn('granjas.departamento_id', 'departamentos.id');

        $filas = Departamento::query()
            ->select('departamentos.*')
            ->selectSub($granjasDelDepto()->selectRaw('COUNT(*)'), 'granjas')
            ->selectSub(
                GranjaPanel::query()->selectRaw('COALESCE(SUM(granja_panel.cantidad), 0)')
                    ->whereIn('granja_id', $granjasDelDepto()->select('granjas.id')),
                'paneles',
            )
            ->selectSub(
                GranjaPanel::query()
                    ->join('modelos_panel', 'modelos_panel.id', '=', 'granja_panel.modelo_panel_id')
                    ->selectRaw('COALESCE(SUM(granja_panel.cantidad * modelos_panel.potencia_kw), 0)')
                    ->whereIn('granja_id', $granjasDelDepto()->select('granjas.id')),
                'capacidad_instalada_kw',
            )
            ->selectSub(
                Generacion::query()->selectRaw('COALESCE(SUM(generacion_real_kwh), 0)')
                    ->whereIn('granja_id', $granjasDelDepto()->select('granjas.id'))
                    ->when($desde, fn ($q) => $q->where('periodo', '>=', $desde))
                    ->when($hasta, fn ($q) => $q->where('periodo', '<=', $hasta)),
                'generacion_kwh',
            )
            ->selectSub(
                Generacion::query()->selectRaw('COALESCE(SUM(generacion_esperada_kwh), 0)')
                    ->whereIn('granja_id', $granjasDelDepto()->select('granjas.id'))
                    ->when($desde, fn ($q) => $q->where('periodo', '>=', $desde))
                    ->when($hasta, fn ($q) => $q->where('periodo', '<=', $hasta)),
                'esperada_kwh',
            )
            ->selectSub($granjasDelDepto()->selectRaw('COALESCE(SUM(familias_beneficiadas), 0)'), 'familias_beneficiadas')
            ->selectSub(
                Alerta::query()->selectRaw('COUNT(*)')->where('estado', EstadoAlerta::Activa->value)
                    ->whereIn('granja_id', $granjasDelDepto()->select('granjas.id')),
                'alertas_activas',
            )
            ->orderBy('nombre')
            ->get();

        return $filas->map(fn (Departamento $d) => [
            'id' => $d->id,
            'codigo' => $d->codigo,
            'nombre' => $d->nombre,
            'cabecera' => $d->cabecera,
            'latitud' => $d->latitud,
            'longitud' => $d->longitud,
            'granjas' => (int) $d->granjas,
            'paneles' => (int) $d->paneles,
            'capacidad_instalada_kw' => (float) $d->capacidad_instalada_kw,
            'generacion_kwh' => (float) $d->generacion_kwh,
            'esperada_kwh' => (float) $d->esperada_kwh,
            'familias_beneficiadas' => (int) $d->familias_beneficiadas,
            'co2_evitado_kg' => (float) $d->generacion_kwh * $factor,
            'alertas_activas' => (int) $d->alertas_activas,
        ]);
    }

    /** Generación mensual por departamento en los últimos N meses, para sparklines. [departamento_id => [kwh...]] */
    public function seriesPorDepartamento(int $meses = 12): array
    {
        $inicio = $this->inicioSerie($meses);
        $periodos = $this->periodos($inicio, $meses);

        $filas = Generacion::query()
            ->join('granjas', 'granjas.id', '=', 'generaciones.granja_id')
            ->where('granjas.activa', true)->whereNull('granjas.deleted_at')
            ->where('periodo', '>=', $inicio->toDateString())
            ->selectRaw('granjas.departamento_id, periodo, SUM(generacion_real_kwh) as total')
            ->groupBy('granjas.departamento_id', 'periodo')
            ->get();

        $series = [];
        foreach ($filas->groupBy('departamento_id') as $deptoId => $grupo) {
            $porPeriodo = $grupo->keyBy(fn ($f) => CarbonImmutable::parse($f->periodo)->format('Y-m'));
            $series[$deptoId] = array_map(fn ($p) => (float) ($porPeriodo[$p]->total ?? 0), $periodos);
        }

        return $series;
    }

    /** Real vs esperada nacional, un punto por mes. */
    public function serieNacional(int $meses = 12): array
    {
        $inicio = $this->inicioSerie($meses);

        $filas = Generacion::query()
            ->join('granjas', 'granjas.id', '=', 'generaciones.granja_id')
            ->where('granjas.activa', true)->whereNull('granjas.deleted_at')
            ->where('periodo', '>=', $inicio->toDateString())
            // 'real' es palabra reservada en MySQL: los alias llevan sufijo
            ->selectRaw('periodo, SUM(generacion_real_kwh) as real_kwh, SUM(generacion_esperada_kwh) as esperada_kwh')
            ->groupBy('periodo')->orderBy('periodo')
            ->get()->keyBy(fn ($f) => CarbonImmutable::parse($f->periodo)->format('Y-m'));

        return array_map(fn ($p) => [
            'periodo' => $p.'-01',
            'real_kwh' => (float) ($filas[$p]->real_kwh ?? 0),
            'esperada_kwh' => (float) ($filas[$p]->esperada_kwh ?? 0),
        ], $this->periodos($inicio, $meses));
    }

    /** Payload liviano para el mapa (RF-13). */
    public function granjasMapa(): Collection
    {
        return Granja::activas()->conMetricas()->with('departamento:id,nombre,codigo')
            ->orderBy('nombre')->get()
            ->map(fn (Granja $g) => [
                'id' => $g->id,
                'nombre' => $g->nombre,
                'departamento_id' => $g->departamento_id,
                'departamento' => $g->departamento->nombre,
                'municipio' => $g->municipio,
                'latitud' => $g->latitud,
                'longitud' => $g->longitud,
                'capacidad_instalada_kw' => $g->capacidad_instalada_kw,
                'total_paneles' => $g->total_paneles,
                'generacion_acumulada_kwh' => $g->generacion_acumulada_kwh,
                'familias_beneficiadas' => $g->familias_beneficiadas,
                'co2_evitado_kg' => $g->co2_evitado_kg,
                'alertas_activas' => $g->alertas_activas_count,
            ]);
    }

    /**
     * Cortes de quintil para la escala de color del sistema.
     * Devuelve 4 cortes; un valor cae en el quintil 1..5 según cuántos cortes supera.
     */
    public static function quintiles(array $valores): array
    {
        $v = array_values(array_filter($valores, fn ($x) => $x > 0));
        if (count($v) < 5) {
            $max = $v ? max($v) : 1;

            return [$max * 0.2, $max * 0.4, $max * 0.6, $max * 0.8];
        }
        sort($v);
        $n = count($v);

        return array_map(fn ($q) => $v[(int) floor($n * $q) - 1] ?? $v[0], [0.2, 0.4, 0.6, 0.8]);
    }

    public static function quintil(float $valor, array $cortes): int
    {
        $q = 1;
        foreach ($cortes as $c) {
            if ($valor > $c) {
                $q++;
            }
        }

        return $q;
    }

    private function inicioSerie(int $meses): CarbonImmutable
    {
        $ultimo = Generacion::max('periodo');
        $fin = $ultimo ? CarbonImmutable::parse($ultimo) : CarbonImmutable::now()->startOfMonth();

        return $fin->startOfMonth()->subMonths($meses - 1);
    }

    private function periodos(CarbonImmutable $inicio, int $meses): array
    {
        return array_map(fn ($i) => $inicio->addMonths($i)->format('Y-m'), range(0, $meses - 1));
    }
}
