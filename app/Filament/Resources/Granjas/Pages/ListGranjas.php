<?php

namespace App\Filament\Resources\Granjas\Pages;

use App\Filament\Resources\Granjas\GranjaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGranjas extends ListRecords
{
    protected static string $resource = GranjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
