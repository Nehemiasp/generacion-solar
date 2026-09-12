<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoAlerta: string implements HasLabel, HasColor
{
    case Activa = 'activa';
    case Revisada = 'revisada';
    case Resuelta = 'resuelta';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Activa => 'danger',
            self::Revisada => 'warning',
            self::Resuelta => 'gray',
        };
    }
}
