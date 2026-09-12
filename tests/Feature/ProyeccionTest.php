<?php

namespace Tests\Feature;

use App\Models\Departamento;
use App\Models\Generacion;
use App\Models\Granja;
use App\Services\ProyeccionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProyeccionTest extends TestCase
{
    use RefreshDatabase;

    private function granjaConSerie(array $valores): Granja
    {
        $depto = Departamento::create(['nombre' => 'Zacapa', 'codigo' => 'ZA', 'latitud' => 15, 'longitud' => -89.45]);
        $granja = Granja::create(['nombre' => 'G', 'departamento_id' => $depto->id, 'latitud' => 15, 'longitud' => -89.45]);

        foreach ($valores as $i => $v) {
            Generacion::create([
                'granja_id' => $granja->id,
                'periodo' => sprintf('2025-%02d-01', $i + 1),
                'generacion_real_kwh' => $v,
                'generacion_esperada_kwh' => $v,
            ]);
        }

        return $granja;
    }

    public function test_serie_lineal_proyecta_exacto(): void
    {
        $granja = $this->granjaConSerie([100, 200, 300, 400, 500]);

        $p = app(ProyeccionService::class)->proyectar($granja, 3);

        $this->assertSame(ProyeccionService::METODO_REGRESION, $p['metodo']);
        $this->assertEquals(100.0, $p['pendiente']);
        $this->assertEquals(1.0, $p['r2']);
        $this->assertEquals([600.0, 700.0, 800.0], array_column($p['proyecciones'], 'kwh'));
        $this->assertSame('2025-06-01', $p['proyecciones'][0]['periodo']);
    }

    public function test_serie_plana_proyecta_el_mismo_valor(): void
    {
        $granja = $this->granjaConSerie([250, 250, 250, 250]);

        $p = app(ProyeccionService::class)->proyectar($granja, 2);

        $this->assertEquals(0.0, $p['pendiente']);
        $this->assertEquals([250.0, 250.0], array_column($p['proyecciones'], 'kwh'));
    }

    public function test_con_menos_de_tres_periodos_usa_promedio(): void
    {
        $granja = $this->granjaConSerie([100, 300]);

        $p = app(ProyeccionService::class)->proyectar($granja, 1);

        $this->assertSame(ProyeccionService::METODO_PROMEDIO, $p['metodo']);
        $this->assertEquals([200.0], array_column($p['proyecciones'], 'kwh'));
        $this->assertStringContainsString('Esta granja tiene 2', $p['mensaje']);
    }

    public function test_nunca_proyecta_negativo(): void
    {
        $granja = $this->granjaConSerie([300, 200, 100]);

        $p = app(ProyeccionService::class)->proyectar($granja, 3);

        $this->assertEquals([0.0, 0.0, 0.0], array_column($p['proyecciones'], 'kwh'));
    }

    public function test_usa_solo_los_ultimos_periodos_configurados(): void
    {
        config(['solar.meses_historicos_proyeccion' => 3]);
        $granja = $this->granjaConSerie([1000, 1000, 1000, 100, 200, 300]);

        $p = app(ProyeccionService::class)->proyectar($granja, 1);

        $this->assertSame(3, $p['periodos_usados']);
        $this->assertEquals([400.0], array_column($p['proyecciones'], 'kwh'));
    }
}
