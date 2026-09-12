<?php

namespace App\Filament\Resources\Granjas\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

// RF-05 / RF-06: paneles por granja y capacidad resultante.
class PanelesRelationManager extends RelationManager
{
    protected static string $relationship = 'paneles';

    protected static ?string $title = 'Paneles instalados';

    protected static ?string $modelLabel = 'panel';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('modelo_panel_id')->label('Modelo de panel')
                    ->relationship('modeloPanel', 'modelo')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->marca} {$record->modelo} ({$record->potencia_kw} kW)")
                    ->searchable(['marca', 'modelo'])->preload()->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('granja_id', $this->getOwnerRecord()->id))
                    ->validationMessages(['unique' => 'Esta granja ya tiene ese modelo. Editá la cantidad en la fila existente.']),
                TextInput::make('cantidad')->label('Cantidad')->required()->numeric()->integer()->minValue(1),
                DatePicker::make('fecha_instalacion')->label('Fecha de instalación')->maxDate(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Paneles instalados · capacidad total '.number_format($this->getOwnerRecord()->capacidad_instalada_kw, 1).' kW')
            ->columns([
                TextColumn::make('modeloPanel.marca')->label('Marca'),
                TextColumn::make('modeloPanel.modelo')->label('Modelo'),
                TextColumn::make('modeloPanel.potencia_kw')->label('Potencia (kW)')->numeric(decimalPlaces: 3)->alignRight(),
                TextColumn::make('cantidad')->label('Cantidad')->numeric(decimalPlaces: 0)->alignRight(),
                TextColumn::make('capacidad_kw')->label('Capacidad (kW)')->numeric(decimalPlaces: 1)->alignRight(),
                TextColumn::make('fecha_instalacion')->label('Instalación')->date('d/m/Y')->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make()->after(fn () => $this->dispatch('$refresh')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
