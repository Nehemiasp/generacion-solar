<?php

namespace App\Filament\Resources\Granjas\Pages;

use App\Filament\Resources\Granjas\GranjaResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGranja extends EditRecord
{
    protected static string $resource = GranjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
