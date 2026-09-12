<?php

namespace App\Filament\Resources\Granjas\Pages;

use App\Filament\Resources\Granjas\GranjaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGranja extends ViewRecord
{
    protected static string $resource = GranjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
