<?php

namespace App\Observers;

use App\Models\Generacion;
use App\Services\AlertaService;

class GeneracionObserver
{
    public function __construct(private AlertaService $alertas) {}

    public function saved(Generacion $generacion): void
    {
        $this->alertas->evaluar($generacion);
    }
}
