# Prompts utilizados

Registro de las instrucciones dadas a Claude Code durante el desarrollo, en orden cronológico. Entre
corchetes va el contexto necesario para entender cada una (archivos o capturas adjuntas, resultado
obtenido).

Los prompts de las siete fases de construcción se redactaron en la etapa de preparación y están en
[PLAN.md](PLAN.md), secciones "FASE 1" a "FASE 7". Aquí se listan los de la conversación.

## 0. Preparación

> Adjunto las bases del reto. Antes de escribir código necesito dos documentos de trabajo. Primero, un
> plan de desarrollo: esquema de base de datos cerrado, con tablas, columnas y restricciones; fases de
> trabajo; el prompt que le daré a la IA en cada fase; y criterios de aceptación
> por fase. Las tablas y campos van en español. Los valores derivados (capacidad instalada, generación
> acumulada, CO₂ evitado) no se guardan como columnas: se calculan.
>
> [Adjunto: PDF del reto. Resultado: `docs/PLAN.md`, revisado y ajustado por el equipo.]

> Segundo, un sistema de diseño vinculante para el frontend público y el panel: paleta con una sola
> escala de color para representar generación, tipografía, densidad de información, formato de cifras
> con separador de miles, tratamiento del mapa y de las gráficas, y una lista explícita de lo que está
> prohibido (sombras, degradados, esquinas redondeadas en superficies de datos, colores por defecto de
> las librerías). El objetivo es que la aplicación no se vea genérica ni "hecha con IA".
>
> [Resultado: `docs/DISENO.md`. Se agregó después la sección 15 con las desviaciones justificadas.]

> Reglas permanentes del proyecto para el archivo `CLAUDE.md`: dominio en español, nada de columnas
> derivadas, constantes de negocio solo desde `config/solar.php`, ningún secreto en el código, ejecutar
> el código antes de darlo por terminado y reportar qué se probó.
>
> [Resultado: `CLAUDE.md`, que el agente lee al inicio de cada sesión.]

## 1. Construcción

> Adjunto el PDF con las indicaciones completas del reto. En la carpeta están `PLAN.md` y `DISENO.md`,
> que debes seguir. Construye el proyecto fase por fase según el plan. Puedes apoyarte en skills para
> el frontend, pero la interfaz debe respetar el sistema de diseño: nada genérico.
>
> [Adjunto: PDF del reto.]

> Continúa con la siguiente fase.

> Antes de continuar, haz un respaldo del estado actual.
>
> [Resultado: commit por fase y copia comprimida en `backups/`.]

> Inténtalo de nuevo.
>
> [Tras un fallo de la terminal al escribir varios archivos en un solo comando.]

## 2. Mapa

> Tengo una cuenta de CARTO pero no encuentro cómo generar la clave de API. ¿Es necesaria?
>
> [Adjunto: captura del panel de CARTO. Los mosaicos devolvían "API KEY REQUIRED".]

> Descarta CARTO y deja el mapa como estaba, con los mosaicos de Esri. Pasemos a los ajustes de
> interfaz. El primero: todas las pantallas, públicas y del panel, necesitan un botón para volver a la
> pantalla anterior; el botón "atrás" del navegador no basta para el flujo de uso.
>
> [Decisión del equipo: mantener Esri, que no requiere clave.]

## 3. Ajustes de interfaz

> Indícame las credenciales de acceso al panel.

> En el panel de administración, aumenta la separación entre el encabezado y el botón Volver.
>
> [Adjunto: captura del panel.]

> El tablero es la pantalla de inicio: quítale el botón Volver.

> Aplica esquinas redondeadas a todas las tarjetas.
>
> [Interrumpido y descartado por el equipo: el sistema de diseño fija radio 0 en superficies de datos.]

> En pantallas angostas, convierte la navegación en un menú de hamburguesa.
>
> [Adjunto: captura del encabezado a 375 px.]

> Haz el encabezado fijo al hacer scroll. Lo de las esquinas redondeadas queda descartado.

> El menú de hamburguesa funciona, pero al abrirse empuja el contenido hacia abajo. Debe superponerse
> al contenido con z-index 999.

> ¿Cómo cambio una alerta a Revisada o Resuelta desde el panel?

## 4. Despliegue

> Necesito publicar la aplicación. ¿Qué opciones hay para desplegarla con un dominio gratuito?

> ¿Debo subir el código a GitHub primero o crear el proyecto desde una plantilla del proveedor?
>
> [Adjunto: captura de Laravel Cloud.]

> Ya creé el repositorio: https://github.com/Nehemiasp/generacion-solar. Sube el código.

> ¿Procedo con el despliegue?

> Al agregar el recurso de base de datos aparece este diálogo y no me deja elegir otro tipo. ¿Cuál
> recomendaste?
>
> [Adjuntos: capturas del diálogo. Se eligió Laravel MySQL 8.4 en configuración Dev por costo.]

> Variables de entorno cargadas.
>
> [Adjunto: captura de las variables.]

> La URL de producción responde con error 500: https://generacion-solar-production-gtqero.laravel.cloud/
>
> [Causa: alias SQL `real`, palabra reservada en MySQL. Corregido.]

> Ya ejecuté el seed en producción. Verifica que todo cargue correctamente.

> El panel de administración responde 403 después de iniciar sesión con la cuenta de administrador.
>
> [Adjunto: captura del 403. Causa: Filament exige `FilamentUser::canAccessPanel()` fuera de local.]

## 5. Verificación en producción

> Ya tengo sesión iniciada en el panel. Revisa que todas sus pantallas carguen bien en producción.

> Revisa también las páginas públicas en producción.

> Revisa las páginas públicas en modo responsive.
>
> [Se detectó y corrigió el desborde de cifras a 375 px.]

> Revisa el panel de administración en modo responsive.

## 6. Documentación y entrega

> Actualiza el documento de uso de IA con el fallo del acceso al panel en producción.

> Retira el script generador de la presentación del repositorio; el archivo `.pptx` sí se queda.

> En la diapositiva 2, el bloque "1 día" describe una circunstancia nuestra, no del reto. Sustitúyelo
> por un dato que sí provenga del planteamiento del PDF.

> Adjunto la rúbrica de evaluación y las bases de la competencia. Compara el proyecto contra ambas y
> dime qué falta antes de hacer cualquier cambio.
>
> [Adjuntos: `Rubrica de Evaluacion.pdf` y `Bases para Competencia Dia del Programador.pdf`. La IA
> listó siete faltantes y esperó confirmación.]

> Procede con los puntos 1 al 4. Del manual de usuario y del diagrama de base de datos genera también
> una versión en PDF.

> Genera la versión en PDF de toda la documentación relevante en Markdown y entrégamela.

## Patrón de trabajo

Las instrucciones son cortas porque el contexto pesado ya estaba en archivos: `CLAUDE.md` (reglas
permanentes), `PLAN.md` (esquema y fases) y `DISENO.md` (sistema de diseño). Cada prompt de la
conversación pide una sola cosa. Cuando la IA propuso algo que el equipo no quería (CARTO, esquinas
redondeadas) se descartó en el mensaje siguiente, y antes de los cambios de entrega se le pidió
primero un diagnóstico sin ejecutar nada.
