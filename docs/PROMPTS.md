# Prompts utilizados

Registro de las instrucciones dadas a Claude Code durante el desarrollo, en orden cronológico. Entre
corchetes va el contexto necesario para entender cada una (archivos o capturas adjuntas, resultado
obtenido).

El trabajo se apoyó en tres documentos que la IA debía respetar en todo momento: `CLAUDE.md`
(reglas permanentes), [PLAN.md](PLAN.md) (esquema de datos, fases y criterios de aceptación) y
[DISENO.md](DISENO.md) (sistema de diseño). Por eso los prompts de la conversación son cortos:
señalan qué fase o qué sección aplica, y el detalle vive en el documento.

## 0. Preparación

> Adjunto las bases del reto. Antes de escribir código necesito un plan de desarrollo: decisiones de
> stack cerradas, esquema de base de datos completo con tablas, columnas y restricciones, fases de
> trabajo con el prompt de cada una y su criterio de aceptación, y una lista de verificación contra
> los 17 requerimientos. Nombres de tablas y campos en español. Los valores derivados (capacidad
> instalada, generación acumulada, CO₂ evitado) no se guardan: se calculan.
>
> [Adjunto: PDF del reto. Resultado: `docs/PLAN.md`.]

> Ahora un sistema de diseño vinculante para el frontend público y el panel: una sola escala de color
> para representar generación en todas las vistas, tipografía, densidad de información, formato de
> cifras con separador de miles, tratamiento del mapa y de las gráficas, estados vacíos, y una lista
> explícita de lo prohibido (sombras, degradados, esquinas redondeadas en superficies de datos,
> colores por defecto de las librerías). El objetivo es que la aplicación no se vea genérica.
>
> [Resultado: `docs/DISENO.md`, secciones 1 a 14. La sección 15 se agregó durante la implementación.]

> Genera `CLAUDE.md` en la raíz con las reglas permanentes: dominio en español, nada de columnas
> derivadas, constantes de negocio solo desde `config/solar.php`, ningún secreto en el código, no
> inventar API de Filament de memoria, ejecutar el código antes de darlo por terminado y reportar
> qué se probó.
>
> [Resultado: `CLAUDE.md`, que el agente lee al inicio de cada sesión.]

## 1. Construcción por fases

> Adjunto el PDF con las indicaciones completas del reto. Ejecuta la Fase 0 de `PLAN.md`: andamiaje
> con Laravel 13, Filament y Scramble, commit inicial y repositorio listo para desplegar. Respeta
> `CLAUDE.md` en todo el trabajo.
>
> [Adjunto: PDF del reto. Resultado: commit `chore: proyecto inicial Laravel 13 + Filament 5 + Scramble`.]

> Continúa con las Fases 1, 3 y 6 del plan: modelo de datos con el esquema de la sección 1 tal cual,
> regla de alertas con su observador y su comando artisan, y servicio de proyección por mínimos
> cuadrados con sus pruebas. Al terminar, `migrate:fresh --seed` y la salida de `php artisan test`.
>
> [Resultado: commit `feat: modelo de datos, alertas (RF-14), proyección (RF-15) y datos demo`.
> Se verificó en tinker que los 22 departamentos y sus coordenadas caen dentro de Guatemala.]

> Antes de continuar, haz un respaldo del estado actual.
>
> [Resultado: commit y copia comprimida en `backups/`.]

> Inténtalo de nuevo escribiendo los archivos uno por uno.
>
> [Tras un fallo de la terminal al escribir varios archivos en un solo comando.]

> Fases 2, 4 y 7: recursos de Filament para modelos de panel, granjas (con relation manager de
> paneles) y generaciones, con las validaciones de RF-17; servicio de estadísticas con consultas
> agregadas; API REST versionada bajo `/api/v1` documentada con Scramble y en `docs/API.md`.
>
> [Resultado: commit `feat: CRUDs Filament, API REST v1, servicios de estadisticas y frontend publico base`.]

> Fase 5 y frontend público completo, aplicando `DISENO.md`: tablero con la estructura de la sección
> 6, mapa según la sección 7 (tamaño por capacidad, color por generación, anillo de alerta), gráficas
> según la sección 8 y estados vacíos de la sección 9. Usa Blade propio; Filament queda solo para el
> panel interno. Corrige de paso lo que falle al probar la API y el panel.
>
> [Resultado: commit `feat: frontend publico con mapa, tablero y proyeccion; correcciones de Filament y API`.
> Aquí se detectaron el limitador `api` sin definir, el parámetro `meses` opcional y el orden de la
> relación en la proyección.]

> Fase 9: README con instalación y ejecución, `docs/API.md`, `docs/DESPLIEGUE.md` y
> `docs/USO-DE-IA.md` con los fallos corregidos. Verifica la aplicación a 375 px y ajusta lo que
> desborde.
>
> [Resultado: commit `docs: README, API, despliegue y uso de IA; ajustes responsive`.]

> Presentación de 11 diapositivas siguiendo el guion de la Fase 9, con capturas reales de la
> aplicación y notas del orador.
>
> [Resultado: `docs/presentacion/`.]

## 2. Mapa

> Tengo una cuenta de CARTO pero no encuentro cómo generar la clave de API. ¿Es necesaria para los
> mosaicos que indica `DISENO.md`?
>
> [Adjunto: captura del panel de CARTO. Los mosaicos devolvían "API KEY REQUIRED".]

> Descarta CARTO y deja el mapa como estaba, con la base gris de Esri, que cumple el criterio de mapa
> monocromo sin clave. Registra la desviación en `DISENO.md`. Pasemos a los ajustes de navegación:
> todas las pantallas, públicas y del panel, necesitan un control para volver a la anterior; el
> botón "atrás" del navegador no basta para el flujo de uso.
>
> [Resultado: sección 15 de `DISENO.md` y commit `feat: boton Volver en todas las pantallas publicas y del panel`.]

## 3. Navegación y responsive

> Indícame las credenciales de acceso al panel.

> En el panel de administración, aumenta la separación entre el encabezado y el control Volver;
> ahora quedan pegados.
>
> [Adjunto: captura del panel.]

> El tablero es la pantalla de inicio, no tiene pantalla anterior: quítale el control Volver.

> Aplica esquinas redondeadas a las tarjetas.
>
> [Interrumpido y descartado: contradice la sección 5 de `DISENO.md`, que fija radio 0 en superficies
> de datos. Se mantuvo el sistema de diseño.]

> A 375 px la navegación no cabe. Conviértela en un menú de hamburguesa según el comportamiento
> responsive de `DISENO.md`, con iconos de abrir y cerrar y atributos `aria`.
>
> [Adjunto: captura del encabezado a 375 px.]

> Haz el encabezado fijo al hacer scroll. Lo de las esquinas redondeadas queda descartado.

> El menú funciona, pero al abrirse empuja el contenido hacia abajo. Debe superponerse al contenido,
> con z-index 999, sin alterar el flujo de la página.

> ¿Cómo se cambia una alerta a Revisada o Resuelta desde el panel?

## 4. Despliegue

> Necesito publicar la aplicación según la Fase 0 del plan. ¿Qué opciones hay para desplegarla con un
> dominio gratuito?

> ¿Debo subir el código a GitHub primero o crear el proyecto desde una plantilla del proveedor?
>
> [Adjunto: captura de Laravel Cloud.]

> Ya creé el repositorio: https://github.com/Nehemiasp/generacion-solar. Sube el código.

> ¿Procedo con el despliegue?

> Al agregar el recurso de base de datos aparece este diálogo y no me deja elegir otro tipo. ¿Cuál
> recomendaste?
>
> [Adjuntos: capturas del diálogo. Se eligió Laravel MySQL 8.4 en configuración Dev por costo;
> desviación registrada en `PLAN.md`.]

> Variables de entorno cargadas.
>
> [Adjunto: captura de las variables.]

> La URL de producción responde con error 500: https://generacion-solar-production-gtqero.laravel.cloud/
>
> [Causa: alias SQL `real`, palabra reservada en MySQL. Corregido en `EstadisticasService`.]

> Ya ejecuté el seed en producción. Verifica que todo cargue correctamente.

> El panel de administración responde 403 después de iniciar sesión con la cuenta de administrador.
>
> [Adjunto: captura del 403. Causa: Filament exige `FilamentUser::canAccessPanel()` fuera de local.]

## 5. Verificación en producción

> Ya tengo sesión iniciada en el panel. Revisa que todas sus pantallas carguen bien en producción.

> Revisa también las páginas públicas en producción, contra la lista de verificación de `PLAN.md`.

> Revisa las páginas públicas en modo responsive, a 375 px, según el piso de calidad de `DISENO.md`:
> sin scroll horizontal, cifras completas, menú superpuesto.
>
> [Se detectó y corrigió el desborde de las cifras del tablero.]

> Revisa el panel de administración en modo responsive.

## 6. Documentación y entrega

> Actualiza `USO-DE-IA.md` con el fallo del acceso al panel en producción.

> Retira el script generador de la presentación del repositorio; el archivo `.pptx` sí se queda.

> En la diapositiva 2, el bloque "1 día" describe una circunstancia nuestra, no del reto. Sustitúyelo
> por un dato que provenga del planteamiento del PDF.

> Adjunto la rúbrica de evaluación y las bases de la competencia. Compara el proyecto contra ambas y
> dime qué falta antes de hacer cualquier cambio.
>
> [Adjuntos: `Rubrica de Evaluacion.pdf` y `Bases para Competencia Dia del Programador.pdf`. La IA
> listó siete faltantes y esperó confirmación.]

> Procede con los puntos 1 al 4: prompts documentados, servidores MCP en `USO-DE-IA.md`, objetivos y
> manual de usuario en el README, y diagrama entidad-relación. Del manual y del diagrama genera
> también una versión en PDF.

> Genera la versión en PDF de toda la documentación relevante en Markdown y entrégamela.

## Patrón de trabajo

Cada prompt pide una sola cosa y remite al documento que ya contiene el detalle: la fase de
`PLAN.md` o la sección de `DISENO.md` que aplica. Cuando la IA propuso algo fuera de esos documentos
(mosaicos de CARTO, esquinas redondeadas) se descartó en el mensaje siguiente y, si la desviación era
necesaria, se registró en el documento correspondiente. Antes de los cambios de entrega se le pidió
primero un diagnóstico sin ejecutar nada.
