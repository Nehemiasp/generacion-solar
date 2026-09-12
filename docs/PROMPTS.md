# Prompts utilizados

Registro de las instrucciones que se dieron a Claude Code durante el desarrollo, en orden
cronológico. Se transcriben como se escribieron; entre corchetes va el contexto que hacía falta para
entenderlas (capturas de pantalla adjuntas, resultado obtenido).

Los prompts largos de cada fase de construcción se redactaron antes de empezar y están en
[PLAN.md](PLAN.md), secciones "FASE 1" a "FASE 7". Aquí se listan los que se escribieron en la
conversación.

## 1. Arranque

> Tengo ya los md para que te guíes en la carpeta y aparte te pasaré el pdf donde se encuentran todas
> las indicaciones para realizar el proyecto, me ayudas a crear este proyecto y puedes usar skills para
> que se te haga más fácil pero skills buenos que el UI/UX no se mire genérico o hecho con IA.
>
> [Adjunto: PDF del reto. En la carpeta: `PLAN.md` con el esquema de datos y las fases, `DISENO.md`
> con el sistema de diseño vinculante.]

> Continúa.

> Haz un backup primero y después continúa.
>
> [Resultado: commit por fase y copia comprimida en `backups/` antes de seguir.]

> Intentar nuevamente.
>
> [Tras un fallo de la terminal al escribir varios archivos en un solo comando.]

## 2. Mapa

> Tengo CARTO pero no sé cómo generar la API.
>
> [Adjunto: captura del panel de CARTO. Los mosaicos de CARTO devolvían "API KEY REQUIRED".]

> No, no cambies nada, así déjalo mejor como estaba sin CARTO, empecemos con las mejoras del UI que
> te daré a continuación, primero que nada necesito que todo tenga un botón de regresar para regresar
> a la pantalla anterior porque estoy viendo que no se puede solo con el click anterior.
>
> [Decisión del equipo: mantener los mosaicos de Esri, que no requieren clave.]

## 3. Ajustes de interfaz

> Me pasas las credenciales del login.

> En la administración aumenta el gap entre el header y el botón de volver.
>
> [Adjunto: captura del panel.]

> Al home quítale el botón de volver.

> Puedes hacer de todas las cards que tengan bordes redondeados.
>
> [Interrumpido y descartado: el sistema de diseño fija radio 0 en superficies de datos.]

> En responsive haz un menú de hamburguesa.
>
> [Adjunto: captura del encabezado a 375 px.]

> Haz que el header sea sticky y no lo de los bordes, ignóralo.

> Sí, pero cuando se abre el menú de hamburguesa se baja el contenido y tiene que quedar un z-index
> 999.

> Pregunta: ¿cómo paso las alertas a revisadas o resueltas?

## 4. Despliegue

> ¿Me ayudas a deployarlo? ¿Dónde lo puedo hacer y conseguir un dominio gratis?

> Primero tengo que subirlo a GitHub o la creo desde un template.
>
> [Adjunto: captura de Laravel Cloud.]

> Aquí está, súbelo tú: https://github.com/Nehemiasp/generacion-solar

> ¿Le doy a deploy va?

> Esto me sale. / No me deja seleccionar otro type, o sea le doy a add resource-database y me sale
> eso que te compartí. / Aaa bien, sí me sale, ¿cuál dijiste?
>
> [Adjuntos: capturas del diálogo de base de datos. Se eligió Laravel MySQL 8.4 en configuración Dev.]

> Aquí están.
>
> [Adjunto: captura de las variables de entorno cargadas.]

> Eeeeh: https://generacion-solar-production-gtqero.laravel.cloud/
>
> [La URL devolvía 500. Causa: alias SQL `real`, palabra reservada en MySQL.]

> Ya corrí el seed, revisa que todo cargue bien.

> El admin no, puse admin@solar.gt.
>
> [Adjunto: captura del 403. Causa: Filament exige `FilamentUser::canAccessPanel()` fuera de local.]

## 5. Verificación en producción

> Ya entré al admin, revisa que todo cargue bien.

> Revisa también las páginas públicas en producción.

> Revisa también en responsive las páginas públicas.
>
> [Se detectó y corrigió el desborde de cifras a 375 px.]

> Revisa también el admin en responsive.

## 6. Documentación y entrega

> Actualiza el doc de uso de IA con el fallo 8.

> Quita el js de generar-presentación.js del GitHub.

> En la presentación quita lo de "1 día", eso es un problema mío no del pdf que nos mandaron, cambia
> por algún problema que sea del pdf que lo muestre ahí.

> Estas son las rúbricas y bases que nos dieron, cumple con todo, si no es así dime primero, no
> ejecutes nada.
>
> [Adjuntos: `Rubrica de Evaluacion.pdf` y `Bases para Competencia Dia del Programador.pdf`. La IA
> listó siete faltantes y esperó confirmación.]

> Sí, haz los puntos 1 al 4; el 3 haz un pdf de eso también y el 4 también haz un pdf de eso.

## Patrón de trabajo

Las instrucciones son cortas porque el contexto pesado ya estaba en archivos: `CLAUDE.md` (reglas
permanentes), `PLAN.md` (esquema y fases) y `DISENO.md` (sistema de diseño). Cada prompt de la
conversación pide una cosa; cuando la IA propuso algo que el equipo no quería (CARTO, bordes
redondeados) se descartó en el siguiente mensaje.
