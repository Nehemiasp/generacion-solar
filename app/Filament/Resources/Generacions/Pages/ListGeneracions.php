<?php

namespace App\Filament\Resources\Generacions\Pages;

use App\Filament\Resources\Generacions\GeneracionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGeneracions extends ListRecords
{
    protected static string $resource = GeneracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
