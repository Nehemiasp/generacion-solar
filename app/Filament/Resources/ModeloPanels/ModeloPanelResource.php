<?php

namespace App\Filament\Resources\ModeloPanels;

use App\Filament\Resources\ModeloPanels\Pages\CreateModeloPanel;
use App\Filament\Resources\ModeloPanels\Pages\EditModeloPanel;
use App\Filament\Resources\ModeloPanels\Pages\ListModeloPanels;
use App\Filament\Resources\ModeloPanels\Schemas\ModeloPanelForm;
use App\Filament\Resources\ModeloPanels\Tables\ModeloPanelsTable;
use App\Models\ModeloPanel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ModeloPanelResource extends Resource
{
    protected static ?string $model = ModeloPanel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $modelLabel = 'modelo de panel';

    protected static ?string $pluralModelLabel = 'modelos de panel';

    protected static ?string $navigationLabel = 'Modelos de panel';

    protected static ?string $slug = 'modelos-panel';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ModeloPanelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModeloPanelsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModeloPanels::route('/'),
            'create' => CreateModeloPanel::route('/create'),
            'edit' => EditModeloPanel::route('/{record}/edit'),
        ];
    }
}
