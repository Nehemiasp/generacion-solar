<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\Generacion;
use App\Models\Granja;
use App\Models\GranjaPanel;
use App\Models\ModeloPanel;
use App\Services\AlertaService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Datos de demostración realistas. Determinista (semilla fija) para que
 * producción y local cuenten la misma historia.
 */
class DemoSeeder extends Seeder
{
    private const MESES_HISTORIA = 18;

    // kWh mensuales por kW instalado (≈4.6 h sol pico × 30 d × PR 0.85)
    private const KWH_POR_KW_MES = 118;

    private const MODELOS = [
        ['Jinko Solar',    'Tiger Neo 72HL4-BDV', 0.575, 22.3],
        ['Trina Solar',    'Vertex S+ TSM-NEG9R', 0.445, 22.0],
        ['Canadian Solar', 'HiKu7 CS7N-665MS',    0.665, 21.4],
        ['LONGi',          'Hi-MO 6 LR5-72HTH',   0.580, 22.5],
        ['JA Solar',       'DeepBlue 4.0 JAM72D', 0.610, 22.1],
        ['Risen Energy',   'Titan RSM132-8',      0.700, 21.9],
        ['Q Cells',        'Q.PEAK DUO ML-G10',   0.410, 20.9],
        ['First Solar',    'Series 6 FS-6470',    0.470, 18.6],
    ];

    // [nombre, código depto, municipio, lat, lon, perfil]
    // perfil: null | 'bajo' (alerta sostenida) | 'sube' | 'baja'
    private const GRANJAS = [
        ['Parque Solar Motagua',          'ZA', 'Zacapa',                    14.9720, -89.5300, 'sube'],
        ['Granja Solar Río Hondo',        'ZA', 'Río Hondo',                 15.0430, -89.5880, null],
        ['Granja Solar Teculután',        'ZA', 'Teculután',                 14.9890, -89.7160, 'bajo'],
        ['Parque Solar Gualán',           'ZA', 'Gualán',                    15.1170, -89.3630, null],
        ['Granja Solar Estanzuela',       'ZA', 'Estanzuela',                14.9990, -89.5710, null],
        ['Granja Solar La Unión',         'ZA', 'La Unión',                  14.9650, -89.2910, null],
        ['Parque Solar Guastatoya',       'PR', 'Guastatoya',                14.8530, -90.0680, null],
        ['Granja Solar San Agustín',      'PR', 'San Agustín Acasaguastlán', 14.9510, -89.9690, null],
        ['Granja Solar El Jícaro',        'PR', 'El Jícaro',                 14.9130, -89.8980, 'bajo'],
        ['Granja Solar Sanarate',         'PR', 'Sanarate',                  14.7950, -90.1920, null],
        ['Parque Solar Jutiapa',          'JU', 'Jutiapa',                   14.2910, -89.8960, null],
        ['Granja Solar El Progreso',      'JU', 'El Progreso',               14.3530, -89.8480, null],
        ['Granja Solar Asunción Mita',    'JU', 'Asunción Mita',             14.3320, -89.7110, null],
        ['Granja Solar Moyuta',           'JU', 'Moyuta',                    14.0360, -90.0830, 'bajo'],
        ['Parque Solar Taxisco',          'SR', 'Taxisco',                   14.0690, -90.4650, 'sube'],
        ['Granja Solar Chiquimulilla',    'SR', 'Chiquimulilla',             14.0850, -90.3800, null],
        ['Granja Solar Cuilapa',          'SR', 'Cuilapa',                   14.2770, -90.2990, null],
        ['Granja Solar Oratorio',         'SR', 'Oratorio',                  14.2290, -90.2020, null],
        ['Parque Solar Puerto Barrios',   'IZ', 'Puerto Barrios',            15.7270, -88.5940, null],
        ['Granja Solar Los Amates',       'IZ', 'Los Amates',                15.2590, -89.0970, 'bajo'],
        ['Granja Solar Morales',          'IZ', 'Morales',                   15.4750, -88.8300, null],
        ['Parque Solar Escuintla Sur',    'ES', 'Escuintla',                 14.3050, -90.7850, null],
        ['Granja Solar La Gomera',        'ES', 'La Gomera',                 14.0840, -91.0530, null],
        ['Granja Solar Tiquisate',        'ES', 'Tiquisate',                 14.2840, -91.3680, null],
        ['Granja Solar Chiquimula',       'CQ', 'Chiquimula',                14.8000, -89.5450, null],
        ['Granja Solar Esquipulas',       'CQ', 'Esquipulas',                14.5670, -89.3520, 'bajo'],
        ['Granja Solar Villa Nueva',      'GU', 'Villa Nueva',               14.5270, -90.5880, 'baja'],
        ['Granja Solar San José Pinula',  'GU', 'San José Pinula',           14.5460, -90.4120, null],
        ['Granja Solar Monjas',           'JA', 'Monjas',                    14.5010, -89.8710, null],
        ['Granja Solar Champerico',       'RE', 'Champerico',                14.2940, -91.9140, null],
        ['Parque Solar Sayaxché',         'PE', 'Sayaxché',                  16.5260, -90.1890, 'baja'],
        ['Granja Solar Salamá',           'BV', 'Salamá',                    15.1040, -90.3180, null],
        ['Granja Solar Mazatenango',      'SU', 'Mazatenango',               14.5340, -91.5030, 'bajo'],
        ['Granja Solar Las Palmas',       'CM', 'Chimaltenango',             14.6610, -90.8190, null],
        ['Granja Solar Coatepeque',       'QZ', 'Coatepeque',                14.7030, -91.8640, null],
    ];

    public function run(): void
    {
        mt_srand(42);

        $modelos = collect(self::MODELOS)->map(fn ($m) => ModeloPanel::updateOrCreate(
            ['marca' => $m[0], 'modelo' => $m[1]],
            ['potencia_kw' => $m[2], 'eficiencia' => $m[3], 'estado' => $m[0] === 'First Solar' ? 'descontinuado' : 'activo'],
        ));

        $departamentos = Departamento::pluck('id', 'codigo');
        $ultimoPeriodo = CarbonImmutable::now()->startOfMonth()->subMonth();
        $primerPeriodo = $ultimoPeriodo->subMonths(self::MESES_HISTORIA - 1);

        foreach (self::GRANJAS as [$nombre, $codigo, $municipio, $lat, $lon, $perfil]) {
            // 2–5 modelos de panel, 200–4000 unidades cada uno
            $seleccion = $modelos->shuffle()->take(mt_rand(2, 5));
            $capacidadKw = 0;
            $filasPaneles = [];
            foreach ($seleccion as $modelo) {
                $cantidad = mt_rand(200, 4000);
                $capacidadKw += $cantidad * $modelo->potencia_kw;
                $filasPaneles[] = ['modelo_panel_id' => $modelo->id, 'cantidad' => $cantidad];
            }

            $esperadaMensual = round($capacidadKw * self::KWH_POR_KW_MES, 2);

            $granja = Granja::updateOrCreate(['nombre' => $nombre], [
                'departamento_id' => $departamentos[$codigo],
                'municipio' => $municipio,
                'latitud' => $lat,
                'longitud' => $lon,
                'familias_beneficiadas' => (int) max(80, min(3500, round($capacidadKw * 0.9))),
                'generacion_esperada_mensual_kwh' => $esperadaMensual,
                'fecha_instalacion' => $primerPeriodo->subMonths(mt_rand(1, 30)),
                'activa' => true,
            ]);

            $granja->paneles()->delete();
            foreach ($filasPaneles as $fila) {
                GranjaPanel::create($fila + ['granja_id' => $granja->id, 'fecha_instalacion' => $granja->fecha_instalacion]);
            }

            $this->generarHistorico($granja, $esperadaMensual, $perfil, $primerPeriodo);
        }

        $total = app(AlertaService::class)->reconstruirTodo();
        $this->command?->info("DemoSeeder: {$modelos->count()} modelos, ".count(self::GRANJAS)." granjas, {$total} alertas.");
    }

    private function generarHistorico(Granja $granja, float $esperadaMensual, ?string $perfil, CarbonImmutable $primerPeriodo): void
    {
        $filas = [];
        $n = self::MESES_HISTORIA;

        for ($i = 0; $i < $n; $i++) {
            $periodo = $primerPeriodo->addMonths($i);
            // Estacionalidad: verano seco (nov–abr) genera más; época lluviosa (may–oct) menos.
            $estacional = in_array($periodo->month, [11, 12, 1, 2, 3, 4]) ? 1.12 : 0.88;
            $esperada = $esperadaMensual * $estacional;

            $factor = match ($perfil) {
                'sube' => 0.90 + 0.25 * $i / ($n - 1),          // 0.90 → 1.15
                'baja' => 1.12 - 0.28 * $i / ($n - 1),          // 1.12 → 0.84
                default => 1.00 + 0.003 * $i,                    // tendencia leve al alza
            };
            if ($perfil === 'bajo' && $i >= $n - 3) {
                $factor = mt_rand(55, 78) / 100;                 // últimos 3 meses entre 55% y 78%
            }
            $ruido = 1 + mt_rand(-80, 80) / 1000;                // ±8%
            $real = $perfil === 'bajo' && $i >= $n - 3
                ? $esperada * $factor                            // sin ruido: garantiza el rango
                : $esperada * $factor * $ruido;

            $filas[] = [
                'granja_id' => $granja->id,
                'periodo' => $periodo->format('Y-m-d'),
                'generacion_real_kwh' => round($real, 2),
                'generacion_esperada_kwh' => round($esperada, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Generacion::where('granja_id', $granja->id)->delete();
        Generacion::insert($filas); // inserción masiva; las alertas se reconstruyen al final
    }
}
