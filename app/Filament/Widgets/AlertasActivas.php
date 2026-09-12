<?php

namespace App\Filament\Widgets;

use App\Enums\EstadoAlerta;
use App\Models\Alerta;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AlertasActivas extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Alertas activas más recientes';

    public function table(Table $table): Table
    {
        return $table
            ->query(Alerta::query()->with('granja.departamento')->where('estado', EstadoAlerta::Activa)->latest('periodo')->limit(10))
            ->paginated(false)
            ->emptyStateHeading('Ninguna granja está por debajo del 80% de su generación esperada en este período.')
            ->columns([
                TextColumn::make('granja.nombre')->label('Granja')
                    ->url(fn (Alerta $record) => route('granjas.show', $record->granja_id), shouldOpenInNewTab: true),
                TextColumn::make('granja.departamento.nombre')->label('Departamento'),
                TextColumn::make('periodo')->label('Período')->date('M Y'),
                TextColumn::make('generacion_esperada_kwh')->label('Esperada (kWh)')->numeric(decimalPlaces: 0)->alignRight(),
                TextColumn::make('generacion_real_kwh')->label('Real (kWh)')->numeric(decimalPlaces: 0)->alignRight(),
                TextColumn::make('porcentaje_desviacion')->label('Desviación')->badge()
                    ->formatStateUsing(fn (float $state) => sprintf('%+.1f %%', $state))
                    ->color(fn (Alerta $record) => $record->grave ? 'danger' : 'warning'),
            ]);
    }
}
