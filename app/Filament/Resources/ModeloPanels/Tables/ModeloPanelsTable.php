<?php

namespace App\Filament\Resources\ModeloPanels\Tables;

use App\Enums\EstadoPanel;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ModeloPanelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('marca')
            ->columns([
                TextColumn::make('marca')->label('Marca')->searchable()->sortable(),
                TextColumn::make('modelo')->label('Modelo')->searchable()->sortable(),
                TextColumn::make('potencia_kw')->label('Potencia (kW)')->numeric(decimalPlaces: 3)->sortable()->alignRight(),
                TextColumn::make('eficiencia')->label('Eficiencia (%)')->numeric(decimalPlaces: 2)->alignRight()->placeholder('—'),
                TextColumn::make('instalaciones_sum_cantidad')->label('Unidades instaladas')
                    ->sum('instalaciones', 'cantidad')->numeric(decimalPlaces: 0)->alignRight()->sortable(),
                TextColumn::make('estado')->label('Estado')->badge(),
            ])
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(EstadoPanel::class),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
