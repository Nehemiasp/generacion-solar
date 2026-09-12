<?php

namespace App\Support;

use Carbon\CarbonInterface;

/** Formato numérico del sistema: miles con coma, decimales solo si aportan, GWh sobre un millón de kWh. */
class Formato
{
    private const MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

    public static function numero(float|int|null $n, int $decimales = 0): string
    {
        return $n === null ? '—' : number_format($n, $decimales, '.', ',');
    }

    /** @return array{0: string, 1: string} valor y unidad */
    public static function energia(float $kwh): array
    {
        return $kwh >= 1_000_000
            ? [self::numero($kwh / 1_000_000, 2), 'GWh']
            : [self::numero($kwh), 'kWh'];
    }

    public static function energiaTexto(float $kwh): string
    {
        [$v, $u] = self::energia($kwh);

        return "{$v} {$u}";
    }

    /** kW; sobre 10,000 muestra MW. */
    public static function potencia(float $kw): array
    {
        return $kw >= 10_000 ? [self::numero($kw / 1000, 1), 'MW'] : [self::numero($kw), 'kW'];
    }

    public static function co2Toneladas(float $kg): string
    {
        return self::numero($kg / 1000, $kg >= 100_000 ? 0 : 1);
    }

    public static function desviacion(?float $pct): string
    {
        return $pct === null ? '—' : sprintf('%+.1f %%', $pct);
    }

    public static function mes(CarbonInterface|string $periodo): string
    {
        $d = $periodo instanceof CarbonInterface ? $periodo : \Carbon\Carbon::parse($periodo);

        return self::MESES[$d->month - 1].' '.$d->year;
    }

    public static function mesCorto(CarbonInterface|string $periodo): string
    {
        $d = $periodo instanceof CarbonInterface ? $periodo : \Carbon\Carbon::parse($periodo);

        return self::MESES[$d->month - 1];
    }
}
