<?php

namespace App\Filament\Resources\Granjas\Schemas;

use App\Models\Granja;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GranjaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ubicación')->columns(3)->schema([
                    TextEntry::make('departamento.nombre')->label('Departamento'),
                    TextEntry::make('municipio')->label('Municipio')->placeholder('—'),
                    TextEntry::make('direccion')->label('Dirección')->placeholder('—'),
                    TextEntry::make('latitud')->label('Latitud')->numeric(decimalPlaces: 5),
                    TextEntry::make('longitud')->label('Longitud')->numeric(decimalPlaces: 5),
                    TextEntry::make('fecha_instalacion')->label('Instalación')->date('d/m/Y')->placeholder('—'),
                ]),
                Section::make('Métricas calculadas')->columns(3)->schema([
                    TextEntry::make('capacidad_instalada_kw')->label('Capacidad instalada')->numeric(decimalPlaces: 1)->suffix(' kW'),
                    TextEntry::make('total_paneles')->label('Paneles instalados')->numeric(decimalPlaces: 0),
                    TextEntry::make('familias_beneficiadas')->label('Familias beneficiadas')->numeric(decimalPlaces: 0),
                    TextEntry::make('generacion_esperada_mensual_kwh')->label('Esperada mensual')->numeric(decimalPlaces: 0)->suffix(' kWh'),
                    TextEntry::make('generacion_acumulada_kwh')->label('Generación acumulada')->numeric(decimalPlaces: 0)->suffix(' kWh'),
                    TextEntry::make('co2_evitado_kg')->label('CO₂ evitado')
                        ->formatStateUsing(fn (float $state) => number_format($state, 0).' kg ('.number_format($state / 1000, 1).' t)'),
                    IconEntry::make('activa')->label('Activa')->boolean(),
                    TextEntry::make('publico')->label('Ficha pública')
                        ->state(fn (Granja $record) => route('granjas.show', $record))
                        ->url(fn (Granja $record) => route('granjas.show', $record), shouldOpenInNewTab: true)
                        ->color('primary'),
                ]),
            ]);
    }
}
