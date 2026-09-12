# API REST

Base: `/api/v1`. Solo lectura, sin autenticación, respuestas en JSON.
Documentación interactiva generada con Scramble en `/docs/api`; el esquema OpenAPI está en `/docs/api.json`.

- **Límite de peticiones:** 60 por minuto por dirección IP. Al excederlo responde `429`.
- **Errores:** `404` con `{"mensaje": "Recurso no encontrado."}`, `422` con `{"message": ..., "errors": {...}}` en español.
- **Paginación:** los listados devuelven `data`, `links` y `meta` con `current_page`, `last_page`, `per_page`, `total`.
  El tamaño de página se controla con `por_pagina`.
- **Unidades:** generación en kWh, capacidad en kW, CO₂ en kg. Los períodos son mensuales y se
  devuelven como el día 1 del mes (`2026-08-01`).

## Endpoints

| Método | Ruta | Descripción | Parámetros |
|---|---|---|---|
| GET | `/departamentos` | Los 22 departamentos con su conteo de granjas activas | — |
| GET | `/departamentos/{id}` | Departamento con estadísticas agregadas | — |
| GET | `/granjas` | Granjas paginadas con métricas calculadas | `departamento_id`, `activa`, `buscar`, `por_pagina` |
| GET | `/granjas/{id}` | Granja con sus paneles | — |
| GET | `/granjas/mapa` | Payload liviano de granjas activas para el mapa | — |
| GET | `/granjas/{id}/generaciones` | Generación mensual de la granja | `desde`, `hasta` (`YYYY-MM`) |
| GET | `/granjas/{id}/proyeccion` | Proyección y precisión histórica | `meses` (1 a 12, por defecto 3) |
| GET | `/generaciones` | Generaciones paginadas | `periodo`, `desde`, `hasta`, `departamento_id`, `granja_id`, `por_pagina` |
| GET | `/estadisticas/nacional` | Totales nacionales y serie de 12 meses | `desde`, `hasta` |
| GET | `/estadisticas/departamentos` | Reporte por departamento | `desde`, `hasta` |
| GET | `/alertas` | Alertas paginadas | `estado` (`activa`, `revisada`, `resuelta`, `todas`), `departamento_id`, `granja_id`, `por_pagina` |

## Ejemplos de respuesta

### GET /api/v1/departamentos

```json
{
  "data": [
    {
      "id": 1,
      "codigo": "AV",
      "nombre": "Alta Verapaz",
      "cabecera": "Cobán",
      "latitud": 15.6,
      "longitud": -90.3,
      "granjas_activas": 0
    }
  ]
}
```

### GET /api/v1/departamentos/22

```json
{
  "data": {
    "id": 22,
    "codigo": "ZA",
    "nombre": "Zacapa",
    "cabecera": "Zacapa",
    "latitud": 15,
    "longitud": -89.45,
    "granjas": 6,
    "paneles": 41295,
    "capacidad_instalada_kw": 23578.59,
    "generacion_kwh": 50293385.62,
    "esperada_kwh": 49413179.5,
    "familias_beneficiadas": 17374,
    "co2_evitado_kg": 20117354.248,
    "alertas_activas": 3
  }
}
```

### GET /api/v1/granjas?buscar=Motagua

```json
{
  "data": [
    {
      "id": 1,
      "nombre": "Parque Solar Motagua",
      "departamento": { "id": 22, "codigo": "ZA", "nombre": "Zacapa" },
      "municipio": "Zacapa",
      "latitud": 14.972,
      "longitud": -89.53,
      "familias_beneficiadas": 3500,
      "generacion_esperada_mensual_kwh": 668307.75,
      "fecha_instalacion": "2024-09-01",
      "activa": true,
      "capacidad_instalada_kw": 5663.625,
      "total_paneles": 9264,
      "generacion_acumulada_kwh": 12076016.6,
      "co2_evitado_kg": 4830406.64
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 18, "per_page": 2, "total": 35 }
}
```

### GET /api/v1/granjas/1

Igual que el listado, más el detalle de paneles:

```json
{
  "data": {
    "id": 1,
    "nombre": "Parque Solar Motagua",
    "capacidad_instalada_kw": 5663.625,
    "paneles": [
      {
        "modelo_panel_id": 2,
        "marca": "Trina Solar",
        "modelo": "Vertex S+ TSM-NEG9R",
        "potencia_kw": 0.445,
        "cantidad": 2008,
        "capacidad_kw": 893.56
      }
    ]
  }
}
```

### GET /api/v1/granjas/mapa

```json
{
  "data": [
    {
      "id": 1,
      "nombre": "Parque Solar Motagua",
      "departamento_id": 22,
      "departamento": "Zacapa",
      "municipio": "Zacapa",
      "latitud": 14.972,
      "longitud": -89.53,
      "capacidad_instalada_kw": 5663.625,
      "total_paneles": 9264,
      "generacion_acumulada_kwh": 12076016.6,
      "familias_beneficiadas": 3500,
      "co2_evitado_kg": 4830406.64,
      "alertas_activas": 0
    }
  ]
}
```

### GET /api/v1/granjas/1/generaciones?desde=2026-06&hasta=2026-08

```json
{
  "data": [
    {
      "id": 18,
      "granja_id": 1,
      "periodo": "2026-08-01",
      "generacion_real_kwh": 617489.22,
      "generacion_esperada_kwh": 588110.82,
      "porcentaje_desviacion": 5,
      "bajo_desempeno": false
    }
  ]
}
```

### GET /api/v1/granjas/3/proyeccion?meses=3

```json
{
  "data": {
    "granja_id": 3,
    "granja": "Granja Solar Teculután",
    "proyeccion": {
      "metodo": "regresion_lineal",
      "pendiente": -22174.7,
      "intercepto": 569047.42,
      "r2": 0.43,
      "proyecciones": [
        { "periodo": "2026-09-01", "kwh": 302952.02 },
        { "periodo": "2026-10-01", "kwh": 280777.32 },
        { "periodo": "2026-11-01", "kwh": 258602.62 }
      ],
      "mensaje": null,
      "periodos_usados": 12
    },
    "precision_historica": {
      "comparaciones": [
        {
          "periodo": "2026-08-01",
          "proyectado_kwh": 389010.15,
          "real_kwh": 232082.14,
          "error_pct": 67.62,
          "metodo": "regresion_lineal"
        }
      ],
      "error_absoluto_medio_pct": 25.7
    }
  }
}
```

`metodo` puede ser `regresion_lineal`, `promedio_movil` (menos de 3 períodos) o `insuficiente`
(sin períodos). En los dos últimos casos `mensaje` explica qué falta.

### GET /api/v1/estadisticas/nacional

```json
{
  "data": {
    "granjas": 35,
    "paneles": 260191,
    "capacidad_instalada_kw": 145484.99,
    "generacion_acumulada_kwh": 309032491.72,
    "familias_beneficiadas": 101398,
    "co2_evitado_kg": 123612996.69,
    "co2_evitado_t": 123612.99,
    "alertas_activas": 19,
    "departamentos_con_granjas": 15
  },
  "serie_12_meses": [
    { "periodo": "2025-09-01", "real_kwh": 15293274.77, "esperada_kwh": 15107161.39 }
  ]
}
```

### GET /api/v1/alertas?estado=activa

```json
{
  "data": [
    {
      "id": 7,
      "granja_id": 3,
      "granja": { "id": 3, "nombre": "Granja Solar Teculután", "departamento": "Zacapa" },
      "periodo": "2026-08-01",
      "generacion_esperada_kwh": 414432.34,
      "generacion_real_kwh": 232082.14,
      "porcentaje_desviacion": -44,
      "grave": true,
      "estado": "activa"
    }
  ],
  "links": {},
  "meta": {}
}
```

`grave` es verdadero cuando la desviación es peor que −40%.

## Proyección

**Método: regresión lineal por mínimos cuadrados sobre los últimos 12 períodos.**

Para una serie de generación mensual `y₀…yₙ₋₁` con `x = 0…n−1` se resuelven la pendiente y el
intercepto que minimizan la suma de los cuadrados de los residuos, y se evalúa la recta en `x = n`,
`n+1`, `n+2` para obtener los meses siguientes. Se reporta también el coeficiente de determinación R²,
que indica qué parte de la variación de la serie explica la recta.

Justificación:

- La generación mensual tiene tendencia y ruido, con pocos puntos por granja. Una recta captura la
  tendencia sin sobreajustar.
- Es determinista, reproducible y no requiere entrenamiento ni dependencias externas.
- Cada coeficiente es explicable: la pendiente son los kWh que gana o pierde la granja cada mes.
- Con 12 a 18 puntos por granja, un modelo de aprendizaje automático sobreajustaría sin aportar
  precisión real.

Salvaguardas:

- Con menos de 3 períodos no hay tendencia defendible, así que el sistema cae a promedio simple e
  indica el método usado en la respuesta.
- Las proyecciones tienen piso en cero: una pendiente negativa pronunciada nunca produce generación
  negativa.
- `precision_historica` recalcula, para cada período con dato real, qué habría proyectado el modelo
  usando solo la información anterior, y reporta el error porcentual y el error absoluto medio. Esto
  cumple el requisito de comparar la proyección con el resultado real y hace auditable el método.

La implementación está en [`app/Services/ProyeccionService.php`](../app/Services/ProyeccionService.php)
y las pruebas en `tests/Feature/ProyeccionTest.php`, que verifican que una serie perfectamente lineal
se proyecta exacta con R² igual a 1, que una serie plana repite su valor, que con menos de 3 períodos
cambia de método y que nunca se devuelven valores negativos.
