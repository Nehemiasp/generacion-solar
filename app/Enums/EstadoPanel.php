<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoPanel: string implements HasLabel, HasColor
{
    case Activo = 'activo';
    case Inactivo = 'inactivo';
    case Descontinuado = 'descontinuado';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Activo => 'primary',
            self::Inactivo => 'gray',
            self::Descontinuado => 'warning',
        };
    }
}
