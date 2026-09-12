<?php

// Reglas de negocio del dominio solar. Nunca hardcodear estos valores en el código.
return [
    'factor_co2_kg_por_kwh' => (float) env('SOLAR_FACTOR_CO2', 0.40),   // RF-10
    'umbral_alerta' => (float) env('SOLAR_UMBRAL_ALERTA', 0.80),         // RF-14: real <= 80% de esperada
    'meses_historicos_proyeccion' => (int) env('SOLAR_MESES_PROYECCION', 12),
    'min_periodos_regresion' => 3,                                        // con menos, promedio móvil
    'umbral_alerta_grave' => -40,                                         // % de desviación para "grave"
];
