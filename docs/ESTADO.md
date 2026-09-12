# Estado del proyecto

Punto de partida para continuar el trabajo desde otra máquina o sesión. Última actualización:
11 de septiembre de 2026, commit `ab4b965`.

## Qué es

Sistema de Registro y Monitoreo de Generación Solar por Departamento (Guatemala), para la
Competencia de Programación con IA del 11 y 12 de septiembre de 2026. Participación individual:
Nehemías Pérez Palma, carné 0909-26-9887.

## Dónde está

| Recurso | Ubicación |
|---|---|
| Producción | https://generacion-solar-production-gtqero.laravel.cloud (Laravel Cloud, MySQL 8.4 Dev, despliegue automático con cada push a `main`) |
| Panel | `/admin` · `admin@solar.gt` / `Solar2026Gt` |
| Repositorio | https://github.com/Nehemiasp/generacion-solar |
| Documentación de la API | `/docs/api` (Scramble) · `/api` y `/api/v1` devuelven un índice JSON |

## Antes de tocar nada

Leer, en este orden: `CLAUDE.md` (reglas permanentes), `docs/PLAN.md` (esquema, fases, criterios de
aceptación), `docs/DISENO.md` (sistema de diseño vinculante). Los demás documentos de `docs/`
explican lo ya hecho; `docs/PROMPTS.md` y `docs/USO-DE-IA.md` son entregables y deben mantenerse al
día si se hacen cambios nuevos.

Entorno local en Windows: PHP 8.4 en `C:\php`, Composer en `C:\php\composer.phar`; en Git Bash
`export PATH="/c/php:$PATH"`. SQLite en local. `php artisan migrate:fresh --seed` carga 22
departamentos, 8 modelos, 35 granjas, 18 meses y 19 alertas. `php artisan test`: 17 pruebas.

## Estado: completo y verificado

- Los 17 requerimientos funcionales (RF-01 a RF-17) implementados y comprobados en producción.
- Público: tablero, mapa, reporte, alertas, ficha de granja. Panel: escritorio, granjas con
  paneles, modelos de panel, generaciones, alertas. API v1 con 11 endpoints.
- Responsive verificado a 375 × 812 en público y panel; menú de hamburguesa superpuesto.
- Rúbrica cubierta: objetivos, manual de usuario, tareas por integrante y rol, diagrama ER, prompts
  documentados, servidores MCP documentados, PDF de toda la documentación en `docs/pdf/`.
- Presentación: `docs/presentacion/Generacion-Solar-Guatemala.pptx`, 11 diapositivas con notas.

## Decisiones cerradas (no reabrir)

- Mosaicos de mapa de Esri; CARTO descartado (exigía clave). No agregar CARTO.
- Radio 0 en superficies de datos; las esquinas redondeadas se descartaron explícitamente.
- Tablero sin botón Volver; todas las demás pantallas lo tienen.
- Cifras con coma de miles y locale numérico inglés en Filament.
- MySQL en producción (Postgres era el plan; se cambió por costo en Laravel Cloud).
- Valores derivados nunca se guardan; se calculan con `Granja::conMetricas()`.

## Fuera del repositorio

- `docs/presentacion/generar-presentacion.js`: script que genera el `.pptx`. Está en `.gitignore` a
  propósito; vive solo en la laptop original. Si hay que regenerar la presentación desde otra
  máquina, hay que copiarlo a mano (necesita `pptxgenjs` y ejecutarse como `.cjs` porque el proyecto
  es ESM).
- `.env` de producción: las variables están cargadas en Laravel Cloud (APP_KEY, APP_URL,
  ADMIN_EMAIL, ADMIN_PASSWORD, DB_* inyectadas por el recurso de base de datos).

## Cómo regenerar los PDF

Los PDF de `docs/pdf/` se generan con Edge sin interfaz a partir del Markdown (`markdown` de
Python + `msedge --headless=new --print-to-pdf`). El diagrama Mermaid del README se renderiza en
el navegador antes de imprimir. Si cambia un `.md`, regenerar su PDF y confirmarlo junto con el
cambio.

## Pendiente (solo del equipo, no del código)

- Fotos de evidencia del avance en la etapa remota y check-in en el Meet (bases, sección 4.1).
- Ensayar la demostración en vivo: crear una generación por debajo del 80 % desde el panel y
  mostrar que la alerta aparece sola en el tablero público.
