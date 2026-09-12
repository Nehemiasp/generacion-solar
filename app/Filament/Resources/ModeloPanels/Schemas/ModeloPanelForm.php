<?php

namespace App\Filament\Resources\ModeloPanels\Schemas;

use App\Enums\EstadoPanel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ModeloPanelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('marca')->label('Marca')->required()->maxLength(100),
                TextInput::make('modelo')->label('Modelo')->required()->maxLength(100)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule, $get) => $rule->where('marca', $get('marca')))
                    ->validationMessages(['unique' => 'Ya existe este modelo para esa marca.']),
                TextInput::make('potencia_kw')->label('Potencia nominal')->suffix('kW')
                    ->required()->numeric()->minValue(0.001)->maxValue(10)->step(0.001),
                TextInput::make('eficiencia')->label('Eficiencia')->suffix('%')
                    ->numeric()->minValue(0)->maxValue(100)->step(0.01),
                Select::make('estado')->label('Estado')
                    ->options(EstadoPanel::class)->default(EstadoPanel::Activo)->required(),
            ]);
    }
}
