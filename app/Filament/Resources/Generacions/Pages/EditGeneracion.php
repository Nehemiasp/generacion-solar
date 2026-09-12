<?php

namespace App\Filament\Resources\Generacions\Pages;

use App\Filament\Resources\Generacions\GeneracionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGeneracion extends EditRecord
{
    protected static string $resource = GeneracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
