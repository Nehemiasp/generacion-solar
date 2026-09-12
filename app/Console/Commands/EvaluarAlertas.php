<?php

namespace App\Console\Commands;

use App\Services\AlertaService;
use Illuminate\Console\Command;

class EvaluarAlertas extends Command
{
    protected $signature = 'solar:evaluar-alertas';

    protected $description = 'Recorre todas las generaciones y reconstruye las alertas (RF-14)';

    public function handle(AlertaService $alertas): int
    {
        $total = $alertas->reconstruirTodo();
        $this->info("Alertas evaluadas: {$total} generaciones bajo el umbral. Activas: {$alertas->activas()}.");

        return self::SUCCESS;
    }
}
