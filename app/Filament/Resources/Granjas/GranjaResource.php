<?php

namespace App\Filament\Resources\Granjas;

use App\Filament\Resources\Granjas\Pages\CreateGranja;
use App\Filament\Resources\Granjas\Pages\EditGranja;
use App\Filament\Resources\Granjas\Pages\ListGranjas;
use App\Filament\Resources\Granjas\Pages\ViewGranja;
use App\Filament\Resources\Granjas\RelationManagers\PanelesRelationManager;
use App\Filament\Resources\Granjas\Schemas\GranjaForm;
use App\Filament\Resources\Granjas\Schemas\GranjaInfolist;
use App\Filament\Resources\Granjas\Tables\GranjasTable;
use App\Models\Granja;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GranjaResource extends Resource
{
    protected static ?string $model = Granja::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSun;

    protected static ?string $modelLabel = 'granja';

    protected static ?string $pluralModelLabel = 'granjas';

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return GranjaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GranjaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GranjasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PanelesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGranjas::route('/'),
            'create' => CreateGranja::route('/create'),
            'view' => ViewGranja::route('/{record}'),
            'edit' => EditGranja::route('/{record}/edit'),
        ];
    }
}
