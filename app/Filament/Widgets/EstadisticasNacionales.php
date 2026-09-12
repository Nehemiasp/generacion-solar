<?php

namespace App\Filament\Widgets;

use App\Services\EstadisticasService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstadisticasNacionales extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $n = app(EstadisticasService::class)->nacional();

        return [
            Stat::make('Granjas activas', number_format($n['granjas']))
                ->description($n['departamentos_con_granjas'].' departamentos con granjas'),
            Stat::make('Paneles instalados', number_format($n['paneles'])),
            Stat::make('Capacidad instalada', number_format($n['capacidad_instalada_kw'], 0).' kW'),
            Stat::make('Generación acumulada', number_format($n['generacion_acumulada_kwh'] / 1_000_000, 2).' GWh')
                ->description(number_format($n['generacion_acumulada_kwh']).' kWh'),
            Stat::make('Familias beneficiadas', number_format($n['familias_beneficiadas'])),
            Stat::make('CO₂ evitado', number_format($n['co2_evitado_t'], 1).' t')
                ->description(number_format($n['co2_evitado_kg']).' kg'),
        ];
    }
}
