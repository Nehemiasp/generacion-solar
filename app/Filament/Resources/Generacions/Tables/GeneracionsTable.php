<?php

namespace App\Filament\Resources\Generacions\Tables;

use App\Models\Generacion;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GeneracionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('periodo', 'desc')
            ->columns([
                TextColumn::make('granja.nombre')->label('Granja')->searchable()->sortable(),
                TextColumn::make('granja.departamento.nombre')->label('Departamento'),
                TextColumn::make('periodo')->label('Período')->date('M Y')->sortable(),
                TextColumn::make('generacion_esperada_kwh')->label('Esperada (kWh)')->numeric(decimalPlaces: 0)->alignRight()->sortable(),
                TextColumn::make('generacion_real_kwh')->label('Real (kWh)')->numeric(decimalPlaces: 0)->alignRight()->sortable(),
                TextColumn::make('porcentaje_desviacion')->label('Desviación')
                    ->formatStateUsing(fn (?float $state) => $state === null ? '—' : sprintf('%+.1f %%', $state))
                    ->alignRight()
                    ->color(fn (Generacion $record) => $record->bajo_desempeno ? 'danger' : null),
            ])
            ->filters([
                SelectFilter::make('granja_id')->label('Granja')->relationship('granja', 'nombre')->searchable()->preload(),
                SelectFilter::make('departamento')->label('Departamento')
                    ->relationship('granja.departamento', 'nombre')->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
