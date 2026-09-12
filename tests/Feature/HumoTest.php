<?php

namespace Tests\Feature;

use App\Models\Granja;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DepartamentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Recorre las pantallas públicas y la API con los datos de demostración cargados. */
class HumoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([DepartamentoSeeder::class, DemoSeeder::class]);
    }

    public function test_las_pantallas_publicas_responden(): void
    {
        $granja = Granja::first();

        foreach (['/', '/mapa', '/reporte', '/alertas', "/granjas/{$granja->id}"] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_el_reporte_acepta_rango_y_orden(): void
    {
        $this->get('/reporte?desde=2026-01&hasta=2026-06&orden=capacidad_instalada_kw&dir=asc')->assertOk();
        $this->get('/reporte?desde=2026-06&hasta=2026-01')->assertSessionHasErrors('hasta');
    }

    public function test_la_api_expone_los_endpoints_minimos(): void
    {
        $granja = Granja::first();

        $this->getJson('/api/v1/departamentos')->assertOk()->assertJsonCount(22, 'data');
        $this->getJson('/api/v1/granjas')->assertOk()->assertJsonStructure(['data', 'links', 'meta']);
        $this->getJson("/api/v1/granjas/{$granja->id}")->assertOk()
            ->assertJsonPath('data.nombre', $granja->nombre)
            ->assertJsonStructure(['data' => ['capacidad_instalada_kw', 'co2_evitado_kg', 'paneles']]);
        $this->getJson("/api/v1/granjas/{$granja->id}/generaciones")->assertOk();
        $this->getJson("/api/v1/granjas/{$granja->id}/proyeccion")->assertOk()
            ->assertJsonStructure(['data' => ['proyeccion' => ['metodo', 'proyecciones'], 'precision_historica']]);
        $this->getJson('/api/v1/granjas/mapa')->assertOk();
        $this->getJson('/api/v1/generaciones')->assertOk();
        $this->getJson('/api/v1/estadisticas/nacional')->assertOk()
            ->assertJsonStructure(['data' => ['granjas', 'paneles', 'capacidad_instalada_kw', 'generacion_acumulada_kwh', 'familias_beneficiadas', 'co2_evitado_kg', 'co2_evitado_t']]);
        $this->getJson('/api/v1/estadisticas/departamentos')->assertOk()->assertJsonCount(22, 'data');
        $this->getJson('/api/v1/alertas')->assertOk();
    }

    public function test_un_id_inexistente_devuelve_404_en_json(): void
    {
        $this->getJson('/api/v1/granjas/999999')->assertNotFound()->assertJson(['mensaje' => 'Recurso no encontrado.']);
    }

    public function test_los_parametros_invalidos_devuelven_422(): void
    {
        $granja = Granja::first();

        $this->getJson("/api/v1/granjas/{$granja->id}/proyeccion?meses=99")->assertStatus(422);
        $this->getJson('/api/v1/generaciones?desde=enero')->assertStatus(422);
    }

    public function test_el_co2_usa_el_factor_de_configuracion(): void
    {
        $granja = Granja::conMetricas()->first();

        $this->assertEqualsWithDelta(
            $granja->generacion_acumulada_kwh * config('solar.factor_co2_kg_por_kwh'),
            $granja->co2_evitado_kg,
            0.01,
        );
    }

    public function test_el_seeder_deja_datos_suficientes_para_evaluar(): void
    {
        $this->assertSame(35, Granja::count());
        $this->assertGreaterThanOrEqual(15, Granja::distinct()->count('departamento_id'));
        $this->assertGreaterThanOrEqual(6, \App\Models\Alerta::distinct()->count('granja_id'));
        $this->assertGreaterThan(0, \App\Models\Generacion::count());
    }
}
