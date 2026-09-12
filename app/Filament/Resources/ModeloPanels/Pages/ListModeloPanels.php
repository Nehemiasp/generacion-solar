<?php

namespace App\Filament\Resources\ModeloPanels\Pages;

use App\Filament\Resources\ModeloPanels\ModeloPanelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListModeloPanels extends ListRecords
{
    protected static string $resource = ModeloPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
