<?php

namespace App\Filament\Resources\Granjas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GranjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')->label('Nombre')->required()->maxLength(150),
                Select::make('departamento_id')->label('Departamento')
                    ->relationship('departamento', 'nombre')->searchable()->preload()->required(),
                TextInput::make('municipio')->label('Municipio')->maxLength(100),
                TextInput::make('direccion')->label('Dirección')->maxLength(200),
                TextInput::make('latitud')->label('Latitud')
                    ->required()->numeric()->minValue(-90)->maxValue(90)->step(0.0000001)
                    ->helperText('Guatemala: entre 13.7 y 17.8'),
                TextInput::make('longitud')->label('Longitud')
                    ->required()->numeric()->minValue(-180)->maxValue(180)->step(0.0000001)
                    ->helperText('Guatemala: entre -92.2 y -88.2'),
                TextInput::make('familias_beneficiadas')->label('Familias beneficiadas')
                    ->required()->numeric()->integer()->minValue(0)->default(0),
                TextInput::make('generacion_esperada_mensual_kwh')->label('Generación esperada mensual')->suffix('kWh')
                    ->required()->numeric()->minValue(0)->default(0),
                DatePicker::make('fecha_instalacion')->label('Fecha de instalación')->maxDate(now()),
                Toggle::make('activa')->label('Activa')->default(true),
            ]);
    }
}
