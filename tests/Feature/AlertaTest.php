<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\Departamento;
use App\Models\Generacion;
use App\Models\Granja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertaTest extends TestCase
{
    use RefreshDatabase;

    private function granja(): Granja
    {
        $depto = Departamento::create(['nombre' => 'Zacapa', 'codigo' => 'ZA', 'latitud' => 15, 'longitud' => -89.45]);

        return Granja::create([
            'nombre' => 'Granja Prueba', 'departamento_id' => $depto->id,
            'latitud' => 15, 'longitud' => -89.45, 'generacion_esperada_mensual_kwh' => 1000,
        ]);
    }

    private function registrar(Granja $granja, float $real, string $periodo = '2026-01-01'): Generacion
    {
        return Generacion::create([
            'granja_id' => $granja->id, 'periodo' => $periodo,
            'generacion_real_kwh' => $real, 'generacion_esperada_kwh' => 1000,
        ]);
    }

    public function test_79_por_ciento_dispara_alerta(): void
    {
        $gen = $this->registrar($this->granja(), 790);

        $this->assertDatabaseHas('alertas', ['generacion_id' => $gen->id, 'porcentaje_desviacion' => -21.0]);
    }

    public function test_80_por_ciento_exacto_dispara_alerta(): void
    {
        // RF-14: "al menos 20% por debajo" => real <= 80% es alerta
        $gen = $this->registrar($this->granja(), 800);

        $this->assertDatabaseHas('alertas', ['generacion_id' => $gen->id, 'porcentaje_desviacion' => -20.0]);
    }

    public function test_81_por_ciento_no_dispara_alerta(): void
    {
        $this->registrar($this->granja(), 810);

        $this->assertSame(0, Alerta::count());
    }

    public function test_al_corregir_la_generacion_la_alerta_desaparece(): void
    {
        $gen = $this->registrar($this->granja(), 500);
        $this->assertSame(1, Alerta::count());

        $gen->update(['generacion_real_kwh' => 950]);

        $this->assertSame(0, Alerta::count());
    }
}
