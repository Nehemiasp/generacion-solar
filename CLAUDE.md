# Proyecto: Sistema de Generación Solar Guatemala

Competencia de 1 día. Laravel 13 + Filament 5 + PostgreSQL (SQLite en local) + Leaflet + Chart.js.
Diseño vinculante: `docs/DISENO.md`. Plan: `docs/PLAN.md`.

## Entorno local (Windows)
- PHP 8.4 en `C:\php` (con intl, pdo_pgsql, pdo_sqlite). Composer: `C:\php\composer.phar`.
- En Git Bash: `export PATH="/c/php:$PATH"` antes de `php`/`composer`.

## Reglas no negociables
- Idioma del dominio: ESPAÑOL. Tablas, modelos, campos y rutas en español
  (granjas, modelos_panel, generaciones, alertas). Código y comentarios en español.
- Nunca inventes API de Filament de memoria. Si no estás seguro de una firma,
  revisá un archivo generado por `make:` o el código en `vendor/filament`.
- Valores derivados = accessors o scopes. Nunca columnas duplicadas en BD.
- Constantes de negocio siempre desde config('solar.*'). Cero números mágicos.
- Ningún secreto en el código. Todo va a .env y .env.example.
- Después de cada cambio de migración: `php artisan migrate:fresh --seed` y confirmar que corre.
- No refactorices archivos que no te pedí tocar.
- Commits pequeños, mensaje en español: `feat: ...` / `fix: ...`

## Antes de terminar una tarea
1. Ejecutá el código, no asumas que sirve.
2. Reportá qué probaste y qué salida obtuviste.
3. Si algo quedó a medias, decilo explícitamente.

## Contexto de negocio
- 22 departamentos de Guatemala.
- CO2 evitado = kWh × 0.40 kg.
- Alerta cuando generación real <= 80% de la esperada en el mismo período.
- Períodos son mensuales, almacenados como fecha del día 1 del mes.
- Proyección: regresión lineal (mínimos cuadrados) sobre últimos 12 períodos; promedio móvil si hay < 3.
