<?php

namespace App\Filament\Resources\Alertas;

use App\Enums\EstadoAlerta;
use App\Filament\Resources\Alertas\Pages\ListAlertas;
use App\Filament\Resources\Alertas\Tables\AlertasTable;
use App\Models\Alerta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

// RF-14: recurso de solo lectura. Las alertas las crea el sistema, no el usuario.
class AlertaResource extends Resource
{
    protected static ?string $model = Alerta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $modelLabel = 'alerta';

    protected static ?string $pluralModelLabel = 'alertas';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Alerta::where('estado', EstadoAlerta::Activa)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function table(Table $table): Table
    {
        return AlertasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlertas::route('/'),
        ];
    }
}
