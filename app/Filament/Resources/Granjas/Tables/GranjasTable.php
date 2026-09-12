<?php

namespace App\Filament\Resources\Granjas\Tables;

use App\Models\Granja;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GranjasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->conMetricas())
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('departamento.nombre')->label('Departamento')->sortable(),
                TextColumn::make('municipio')->label('Municipio')->searchable()->placeholder('—'),
                TextColumn::make('capacidad_instalada_kw')->label('Capacidad (kW)')
                    ->numeric(decimalPlaces: 1)->alignRight()->sortable(),
                TextColumn::make('total_paneles')->label('Paneles')
                    ->numeric(decimalPlaces: 0)->alignRight()->sortable(),
                TextColumn::make('familias_beneficiadas')->label('Familias')
                    ->numeric(decimalPlaces: 0)->alignRight()->sortable(),
                TextColumn::make('alertas_activas_count')->label('Alertas')
                    ->numeric(decimalPlaces: 0)->alignRight()->sortable()
                    ->color(fn (int $state) => $state > 0 ? 'danger' : null),
                IconColumn::make('activa')->label('Activa')->boolean(),
            ])
            ->filters([
                SelectFilter::make('departamento_id')->label('Departamento')
                    ->relationship('departamento', 'nombre')->searchable()->preload(),
                TernaryFilter::make('activa')->label('Activa'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('desactivar')
                    ->label(fn (Granja $record) => $record->activa ? 'Desactivar' : 'Activar')
                    ->icon(fn (Granja $record) => $record->activa ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                    ->color(fn (Granja $record) => $record->activa ? 'danger' : 'primary')
                    ->requiresConfirmation()
                    ->modalDescription('La granja no se borra: se conserva su historial y se oculta del mapa y las estadísticas.')
                    ->action(fn (Granja $record) => $record->update(['activa' => ! $record->activa])),
            ]);
    }
}
