<?php

namespace App\Filament\Resources\Generacions\Schemas;

use App\Models\Generacion;
use App\Models\Granja;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class GeneracionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('granja_id')->label('Granja')
                    ->relationship('granja', 'nombre')->searchable()->preload()->required()
                    ->live()
                    // RF-09: al elegir granja se precarga su esperada mensual
                    ->afterStateUpdated(fn ($state, Set $set) => $set(
                        'generacion_esperada_kwh',
                        Granja::find($state)?->generacion_esperada_mensual_kwh,
                    )),
                // ponytail: <input type="month"> nativo; se guarda como día 1 del mes
                TextInput::make('periodo')->label('Período (mes)')->type('month')->required()
                    ->formatStateUsing(fn ($state) => $state ? substr((string) $state, 0, 7) : null)
                    ->dehydrateStateUsing(fn ($state) => $state ? "{$state}-01" : null)
                    ->rule(fn (Get $get, ?Model $record) => function (string $attr, $value, \Closure $fail) use ($get, $record) {
                        $periodo = Carbon::parse($value)->startOfMonth()->toDateString();
                        $existe = Generacion::where('granja_id', $get('granja_id'))
                            ->where('periodo', $periodo)
                            ->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                            ->exists();
                        if ($existe) {
                            $fail('Ya existe un registro de esta granja para ese período.');
                        }
                    }),
                TextInput::make('generacion_real_kwh')->label('Generación real')->suffix('kWh')
                    ->required()->numeric()->minValue(0),
                TextInput::make('generacion_esperada_kwh')->label('Generación esperada')->suffix('kWh')
                    ->required()->numeric()->minValue(0)
                    ->helperText('Se precarga desde la granja; se guarda como snapshot del período.'),
            ]);
    }
}
