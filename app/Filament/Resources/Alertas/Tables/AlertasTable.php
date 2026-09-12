<?php

namespace App\Filament\Resources\Alertas\Tables;

use App\Enums\EstadoAlerta;
use App\Models\Alerta;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AlertasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('periodo', 'desc')
            ->columns([
                TextColumn::make('granja.nombre')->label('Granja')->searchable()->sortable()
                    ->url(fn (Alerta $record) => route('granjas.show', $record->granja_id), shouldOpenInNewTab: true),
                TextColumn::make('granja.departamento.nombre')->label('Departamento'),
                TextColumn::make('periodo')->label('Período')->date('M Y')->sortable(),
                TextColumn::make('generacion_esperada_kwh')->label('Esperada (kWh)')->numeric(decimalPlaces: 0)->alignRight(),
                TextColumn::make('generacion_real_kwh')->label('Real (kWh)')->numeric(decimalPlaces: 0)->alignRight(),
                TextColumn::make('porcentaje_desviacion')->label('Desviación')->badge()
                    ->formatStateUsing(fn (float $state) => sprintf('%+.1f %%', $state))
                    ->color(fn (Alerta $record) => $record->grave ? 'danger' : 'warning')
                    ->sortable(),
                TextColumn::make('estado')->label('Estado')->badge(),
            ])
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(EstadoAlerta::class)->default(EstadoAlerta::Activa->value),
                SelectFilter::make('departamento')->label('Departamento')
                    ->relationship('granja.departamento', 'nombre')->preload(),
            ])
            ->recordActions([
                Action::make('estado')->label('Cambiar estado')->icon('heroicon-o-check-circle')
                    ->schema([
                        \Filament\Forms\Components\Select::make('estado')->label('Estado')
                            ->options(EstadoAlerta::class)->required(),
                    ])
                    ->fillForm(fn (Alerta $record) => ['estado' => $record->estado->value])
                    ->action(fn (Alerta $record, array $data) => $record->update(['estado' => $data['estado']])),
            ]);
    }
}
