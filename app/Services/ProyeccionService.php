<?php

namespace App\Services;

use App\Models\Granja;
use Carbon\CarbonImmutable;

/**
 * RF-15: proyección de generación futura.
 *
 * Método: regresión lineal por mínimos cuadrados sobre los últimos N períodos
 * (config solar.meses_historicos_proyeccion). Con menos de 3 períodos no hay
 * tendencia defendible, así que se cae a promedio simple. Nunca proyecta negativos.
 */
class ProyeccionService
{
    public const METODO_REGRESION = 'regresion_lineal';

    public const METODO_PROMEDIO = 'promedio_movil';

    public const METODO_INSUFICIENTE = 'insuficiente';

    public function proyectar(Granja $granja, int $meses = 3): array
    {
        $historico = $granja->generaciones()
            ->reorder('periodo', 'desc') // la relación ya ordena asc; hay que reemplazarlo
            ->limit(config('solar.meses_historicos_proyeccion'))
            ->get()
            ->sortBy('periodo')
            ->values();

        $valores = $historico->pluck('generacion_real_kwh')->map(fn ($v) => (float) $v)->all();
        $ultimoPeriodo = $historico->last()?->periodo;

        return $this->calcular($valores, $ultimoPeriodo ? CarbonImmutable::parse($ultimoPeriodo) : null, $meses)
            + ['periodos_usados' => count($valores)];
    }

    /**
     * Para cada período con dato real, calcula qué habría proyectado el modelo usando
     * solo los datos anteriores, y el error porcentual. Sirve para mostrar precisión histórica.
     */
    public function compararProyeccionVsReal(Granja $granja): array
    {
        $serie = $granja->generaciones()->orderBy('periodo')->get();
        $valores = $serie->pluck('generacion_real_kwh')->map(fn ($v) => (float) $v)->all();
        $ventana = config('solar.meses_historicos_proyeccion');
        $minimo = config('solar.min_periodos_regresion');
        $filas = [];

        for ($i = $minimo; $i < count($valores); $i++) {
            $previos = array_slice($valores, max(0, $i - $ventana), $i - max(0, $i - $ventana));
            $resultado = $this->calcular($previos, null, 1);
            $proyectado = $resultado['proyecciones'][0]['kwh'];
            $real = $valores[$i];

            $filas[] = [
                'periodo' => $serie[$i]->periodo->format('Y-m-d'),
                'proyectado_kwh' => round($proyectado, 2),
                'real_kwh' => round($real, 2),
                'error_pct' => $real > 0 ? round(($proyectado - $real) / $real * 100, 2) : null,
                'metodo' => $resultado['metodo'],
            ];
        }

        $errores = array_filter(array_map(fn ($f) => $f['error_pct'], $filas), fn ($e) => $e !== null);

        return [
            'comparaciones' => $filas,
            'error_absoluto_medio_pct' => $errores ? round(array_sum(array_map('abs', $errores)) / count($errores), 2) : null,
        ];
    }

    /** @param float[] $y serie histórica en orden cronológico */
    public function calcular(array $y, ?CarbonImmutable $ultimoPeriodo, int $meses): array
    {
        $n = count($y);
        $minimo = config('solar.min_periodos_regresion');
        $base = [
            'metodo' => self::METODO_INSUFICIENTE,
            'pendiente' => null,
            'intercepto' => null,
            'r2' => null,
            'proyecciones' => [],
            'mensaje' => null,
        ];

        if ($n === 0) {
            $base['mensaje'] = 'Esta granja no tiene períodos registrados. Registrá generación para proyectar.';

            return $base;
        }

        if ($n < $minimo) {
            $promedio = array_sum($y) / $n;
            $base['metodo'] = self::METODO_PROMEDIO;
            $base['mensaje'] = "Se necesitan al menos {$minimo} períodos registrados para proyectar una tendencia. Esta granja tiene {$n}; se usa el promedio.";
            $base['proyecciones'] = $this->serie(fn () => $promedio, $ultimoPeriodo, $meses, 0);

            return $base;
        }

        // Mínimos cuadrados: x = 0..n-1
        $sumX = $sumY = $sumXY = $sumX2 = 0.0;
        foreach ($y as $x => $valor) {
            $sumX += $x;
            $sumY += $valor;
            $sumXY += $x * $valor;
            $sumX2 += $x * $x;
        }
        $denominador = $n * $sumX2 - $sumX * $sumX;
        $pendiente = $denominador == 0 ? 0.0 : ($n * $sumXY - $sumX * $sumY) / $denominador;
        $intercepto = ($sumY - $pendiente * $sumX) / $n;

        $media = $sumY / $n;
        $ssTot = $ssRes = 0.0;
        foreach ($y as $x => $valor) {
            $ssTot += ($valor - $media) ** 2;
            $ssRes += ($valor - ($intercepto + $pendiente * $x)) ** 2;
        }
        $r2 = $ssTot == 0 ? 1.0 : 1 - $ssRes / $ssTot;

        $base['metodo'] = self::METODO_REGRESION;
        $base['pendiente'] = round($pendiente, 4);
        $base['intercepto'] = round($intercepto, 4);
        $base['r2'] = round($r2, 4);
        $base['proyecciones'] = $this->serie(
            fn (int $x) => $intercepto + $pendiente * $x,
            $ultimoPeriodo,
            $meses,
            $n,
        );

        return $base;
    }

    private function serie(callable $f, ?CarbonImmutable $ultimoPeriodo, int $meses, int $desdeX): array
    {
        $salida = [];
        for ($k = 0; $k < $meses; $k++) {
            $salida[] = [
                'periodo' => $ultimoPeriodo?->addMonths($k + 1)->startOfMonth()->format('Y-m-d'),
                'kwh' => round(max(0.0, $f($desdeX + $k)), 2), // piso en 0
            ];
        }

        return $salida;
    }
}
