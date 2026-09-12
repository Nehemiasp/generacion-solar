<?php

namespace App\Filament\Resources\ModeloPanels\Pages;

use App\Filament\Resources\ModeloPanels\ModeloPanelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModeloPanel extends EditRecord
{
    protected static string $resource = ModeloPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
