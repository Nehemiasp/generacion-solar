# Sistema de diseño — Monitoreo de Generación Solar
`docs/DISENO.md` · Documento vinculante para todo el frontend público

> **Para Claude Code:** este archivo no es una sugerencia. Cualquier valor de color, tipografía o espaciado que uses debe salir de aquí. Si necesitás un valor que no está definido, preguntá antes de inventarlo.

---

## 1. Qué estamos diseñando

Una consola de monitoreo de infraestructura energética nacional. La audiencia son analistas que necesitan ver el estado de la generación solar del país y detectar dónde está fallando. No es un producto SaaS, no es una landing, no es un portafolio.

La referencia mental correcta es un tablero de operación de red eléctrica o un gráfico del Financial Times. La referencia incorrecta es cualquier dashboard de plantilla.

**Decisión de fondo:** fondo claro, no oscuro. Esto se va a evaluar en una sala con luz encendida y posiblemente proyectado. Un tema oscuro se ve bien en una laptop y se lava en un proyector.

---

## 2. La idea que sostiene todo

En vez de un color de marca, la identidad del sistema es **una sola escala secuencial de generación** que aparece en absolutamente todas las vistas: los círculos del mapa, las barras del ranking, las celdas del reporte por departamento y los sparklines.

Un mismo valor se codifica siempre con el mismo color, en todo el sistema. La leyenda aparece una sola vez.

Esto no es decoración: es codificación consistente de una variable. Y es defendible frente al jurado en una frase — *"toda la aplicación usa una única escala de color para generación, así que el ojo aprende a leerla una vez y después funciona en cualquier pantalla."*

---

## 3. Color

### Escala de generación (la escala del sistema)

Rampa secuencial de un solo tono, de luz baja a luz alta. Se usa por quintiles de generación.

| Token | Hex | Uso |
|---|---|---|
| `gen-1` | `#EFE7CC` | Quintil más bajo |
| `gen-2` | `#E0CD8A` | |
| `gen-3` | `#CFAC45` | |
| `gen-4` | `#A9831A` | |
| `gen-5` | `#75590A` | Quintil más alto |

### Neutros

| Token | Hex | Uso |
|---|---|---|
| `ground` | `#F5F6F4` | Fondo de página |
| `surface` | `#FFFFFF` | Tablas, paneles, popups |
| `rule` | `#DCE0DE` | Todas las líneas divisorias |
| `rule-strong` | `#B6BDBA` | División entre secciones mayores |
| `ink` | `#1B2426` | Texto principal y cifras |
| `ink-2` | `#5A6568` | Etiquetas, ejes, texto secundario |
| `ink-3` | `#8D9698` | Unidades, texto deshabilitado, placeholders |

### Señal

| Token | Hex | Uso |
|---|---|---|
| `alerta` | `#B32747` | Desviación entre −20% y −40% |
| `alerta-grave` | `#7E1230` | Desviación peor que −40% |
| `interactivo` | `#0F5C63` | Enlaces, foco, selección activa |

**Reglas de color, sin excepciones:**

- El rojo/carmesí existe únicamente para alertas. Si aparece en otro lado, pierde su significado.
- `interactivo` es teal y nunca se usa para representar datos. Solo para cosas en las que se puede hacer clic.
- Una granja con alerta **no cambia de color de relleno** en el mapa: conserva su color de la escala de generación y recibe un anillo de `alerta` alrededor. El dato y la señal son dos capas distintas.
- Cero gradientes. En ningún elemento.

---

## 4. Tipografía

**Una sola familia: Archivo** (variable, Google Fonts). Sin segunda familia, sin monoespaciada.

```html
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600&display=swap" rel="stylesheet">
```

El recurso tipográfico distintivo de este proyecto es el **eje de ancho**: las cifras grandes van en Archivo semicondensado (`font-stretch: 78%`), que le da un aire de señalética de infraestructura en lugar de titular de startup. Si el eje variable da problemas, usá la familia `Archivo Narrow` para esas cifras.

### Escala

| Rol | Tamaño / interlineado | Peso |
|---|---|---|
| Cifra nacional | 48px / 1.0 | 600, ancho 78% |
| Título de sección | 21px / 1.3 | 600 |
| Cifra de tarjeta | 26px / 1.1 | 600, ancho 85% |
| Cuerpo | 15px / 1.5 | 400 |
| Tabla | 13px / 1.4 | 400 (500 en cifras) |
| Etiqueta / unidad | 12px / 1.3 | 500, color `ink-2` |

### Cifras

Todo número va con cifras tabulares. Esto es obligatorio y es lo que hace que las tablas se vean profesionales:

```css
.cifra {
  font-variant-numeric: tabular-nums;
  text-align: right;
  color: var(--ink);
}
.unidad {
  font-size: 12px;
  color: var(--ink-3);
  margin-left: 3px;
}
```

Formato numérico: separador de miles con coma, decimales solo cuando aportan. `1,284,930 kWh`, no `1284930.00 kWh`. Sobre un millón de kWh, mostrar GWh. El CO₂ en kg y en toneladas en la misma celda, la tonelada como dato secundario.

### Prohibido en tipografía

- Mayúsculas sostenidas en etiquetas
- Etiquetas "eyebrow" encima de los títulos
- Resaltar una sola palabra del título en otro color o peso
- Cadenas tipo `Izabal · 4 granjas · 12 MW` con puntos medios
- Flechas `→` dentro de botones o enlaces
- Cualquier emoji

---

## 5. Espaciado, bordes y elevación

Grilla base de 4px: `4 · 8 · 12 · 16 · 24 · 32 · 48 · 64`.

**El radio comunica jerarquía, no se aplica parejo:**

- Superficies de datos (tablas, mapa, gráficas): radio `0`
- Controles interactivos (botones, inputs, selects, chips de filtro): radio `3px`
- Nada en la aplicación pasa de `3px`

**Cero sombras.** La separación se logra con líneas de 1px en `rule` y con cambios de fondo entre `ground` y `surface`. La única excepción es el popup del mapa, que lleva `box-shadow: 0 2px 8px rgba(27,36,38,.16)`.

**Densidad:**

- Fila de tabla: 34px de alto, padding horizontal 12px
- Encabezado de tabla: 30px, texto `ink-2`, borde inferior `rule-strong`
- Padding de panel: 16px o 24px, nunca más

---

## 6. Estructura de la pantalla principal

El héroe **no** son cuatro tarjetas con íconos. Es una banda de irradiación: los últimos 12 meses de generación nacional, cada mes como una columna coloreada con la escala, y la línea de generación esperada superpuesta. Es lo más característico del dominio y se lee de un vistazo.

```
┌──────────────────────────────────────────────────────────┐
│ Generación solar · Guatemala          [período ▾]        │  56px, borde inferior rule-strong
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ▁▂▃▅▆▇▇▆▅▃▂▁   ── esperada                             │  banda de 12 meses, 180px
│  sep    dic    mar    jun    ago                         │
│                                                          │
├──────────┬──────────┬──────────┬──────────┬──────────────┤
│ 35       │ 84,200   │ 41,900   │ 12.4M    │ 4,960        │  fila única, separada por
│ granjas  │ paneles  │ kW inst. │ kWh      │ toneladas CO₂│  líneas verticales de 1px
├──────────┴──────────┴──────────┴──────────┴──────────────┤
│                                        │                 │
│  Mapa                                  │  Alertas        │  mapa 65%, alertas 35%
│                                        │  activas        │
│                                        │                 │
├────────────────────────────────────────┴─────────────────┤
│  Departamento   Granjas  Paneles   kW    Generación  ▁▃▅ │  tabla densa con sparkline
│  Zacapa              6     14,200  8,400   2.9M      ▁▃▅ │  de 12 meses por fila
└──────────────────────────────────────────────────────────┘
```

La fila de totales es **una sola fila dividida por líneas verticales**, no cinco tarjetas separadas con sombra. Alineación de todo el contenido a la izquierda; solo las cifras van alineadas a la derecha dentro de su columna.

En móvil: la banda se mantiene, los totales pasan a dos columnas, el mapa va a 60vh de alto, la tabla hace scroll horizontal con la columna de departamento fijada.

---

## 7. Mapa

- **Tiles:** CARTO Positron (`https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png`). Gratis, sin API key, monocromo, así que los marcadores mandan. No uses el OSM estándar: sus carreteras de colores compiten con tus datos.
- **Marcadores:** `L.circleMarker`, nunca el pin azul por defecto.
  - Radio proporcional a la **raíz cuadrada** de la capacidad instalada (entre 4 y 22px). Con raíz cuadrada el área es proporcional al dato; sin ella, exagerás las granjas grandes. Esto es un punto que podés defender.
  - Relleno: color de la escala de generación según el quintil de la granja, opacidad 0.85.
  - Borde: 1px en `surface` para separar marcadores solapados.
  - Con alerta activa: anillo adicional de 2px en `alerta`.
- **Popup:** fondo `surface`, radio 0, sin la colita triangular por defecto. Nombre de la granja en 17px/600, departamento y municipio en `ink-2` debajo, y luego las métricas en dos columnas con cifras tabulares alineadas a la derecha.
- **Controles:** el zoom por defecto de Leaflet se ve a plantilla. Reestilizalo a cuadrados de 28px, fondo `surface`, borde `rule`.
- **Leyenda:** abajo a la izquierda, los cinco escalones de la escala con sus rangos en kWh, más el anillo de alerta explicado. Es la única leyenda de toda la aplicación.
- **Clustering:** solo por encima de 30 marcadores.

---

## 8. Gráficas (Chart.js)

Todo lo que viene por defecto en Chart.js grita plantilla. Configuración obligatoria:

- `legend: { display: false }`. Las series se etiquetan directamente sobre la línea, en su extremo derecho.
- Sin líneas de cuadrícula verticales. Horizontales en `#ECEFED`, 1px.
- Sin bordes en el área de la gráfica. Solo el eje inferior, en `rule`.
- Líneas de 1.5px, `tension: 0`. Sin curvas suavizadas.
- Sin puntos, excepto el último de cada serie (radio 3).
- **Generación real:** línea sólida en `ink`. **Generación esperada:** línea punteada 3-3 en `ink-3`. La real es la protagonista; la esperada es referencia.
- Barras del ranking: color de la escala según el valor, sin espaciado exagerado, `borderRadius: 0`.
- Tooltip: fondo `ink`, texto blanco, radio 3px, sin la caja translúcida por defecto.
- Sparklines de la tabla: 64×20px, línea de 1px en `ink-2`, sin ejes ni etiquetas.

---

## 9. Estados vacíos y de carga

Esto es lo que la IA nunca hace sola y lo que hace que un evaluador piense "aquí hubo un diseñador". Son obligatorios en: mapa filtrado sin resultados, lista de alertas, reporte por departamento, y proyección de una granja sin histórico suficiente.

Redacción: voz activa, sin disculpas, sin signos de admiración, y siempre diciendo qué hacer.

- Sin alertas: *"Ninguna granja está por debajo del 80% de su generación esperada en este período."*
- Mapa filtrado vacío: *"Ningún departamento seleccionado tiene granjas registradas. Ajustá el filtro."*
- Proyección insuficiente: *"Se necesitan al menos 3 períodos registrados para proyectar. Esta granja tiene 1."*
- Carga: bloques en `#ECEFED` con la forma y el tamaño del contenido real. Sin spinners giratorios, sin animación de pulso.

---

## 10. Movimiento

**Un solo momento orquestado en toda la aplicación:** al cargar el mapa, los marcadores escalan de 0 a su tamaño final, escalonados por departamento, 400ms en total. Nada más se anima por su cuenta.

Todo lo demás responde a una acción: apertura de popup (120ms), cambio de filtro (120ms de opacidad), hover de fila de tabla (fondo a `#EEF0EE`, sin transición).

Respetar `prefers-reduced-motion: reduce` desactivando la entrada del mapa.

---

## 11. Filament

Filament es el panel administrativo interno. Nadie evalúa la pantalla de crear un panel solar, así que **no inviertas horas ahí**. Alineación mínima de marca y seguís:

```php
->colors([
    'primary' => Color::hex('#0F5C63'),
    'danger'  => Color::hex('#B32747'),
    'warning' => Color::hex('#A9831A'),
])
->font('Archivo')
```

El dashboard público, el mapa y los reportes se construyen en Blade + Tailwind con estos tokens, **fuera de Filament**, en rutas propias. Ahí es donde se gana el punto de UI/UX.

---

## 12. Configuración de Tailwind

```js
// tailwind.config.js
export default {
  theme: {
    extend: {
      colors: {
        gen: {
          1: '#EFE7CC', 2: '#E0CD8A', 3: '#CFAC45', 4: '#A9831A', 5: '#75590A',
        },
        ground: '#F5F6F4',
        surface: '#FFFFFF',
        rule: { DEFAULT: '#DCE0DE', strong: '#B6BDBA' },
        ink: { DEFAULT: '#1B2426', 2: '#5A6568', 3: '#8D9698' },
        alerta: { DEFAULT: '#B32747', grave: '#7E1230' },
        interactivo: '#0F5C63',
      },
      fontFamily: { sans: ['Archivo', 'system-ui', 'sans-serif'] },
      fontSize: {
        etiqueta: ['12px', { lineHeight: '1.3', fontWeight: '500' }],
        tabla:    ['13px', { lineHeight: '1.4' }],
        cuerpo:   ['15px', { lineHeight: '1.5' }],
        titulo:   ['21px', { lineHeight: '1.3', fontWeight: '600' }],
        cifra:    ['26px', { lineHeight: '1.1', fontWeight: '600' }],
        cifraXl:  ['48px', { lineHeight: '1.0', fontWeight: '600' }],
      },
      borderRadius: { none: '0', control: '3px' },
      boxShadow: { popup: '0 2px 8px rgba(27,36,38,.16)' },
    },
  },
}
```

---

## 13. Lista de lo prohibido

Si aparece cualquiera de estas cosas en el código, se quita:

- Violeta o índigo `#6366f1`, y cualquier gradiente
- `rounded-xl`, `rounded-2xl`, `rounded-full` en contenedores
- `shadow-md`, `shadow-lg`, `shadow-xl`
- `backdrop-blur` y cualquier efecto de vidrio
- Cuatro tarjetas idénticas con un ícono dentro de un círculo de color
- Inter como tipografía
- Emojis en la interfaz
- Texto en mayúsculas sostenidas
- Contenido centrado por defecto (todo se alinea a la izquierda salvo las cifras, que van a la derecha)
- Animaciones de entrada al hacer scroll
- Leyendas y colores por defecto de Chart.js
- Pines azules por defecto de Leaflet
- Copy tipo "¡Bienvenido de nuevo!" o cualquier saludo

---

## 14. Piso de calidad

Antes de considerar terminada una pantalla:

- [ ] Funciona a 375px de ancho sin scroll horizontal accidental
- [ ] Foco de teclado visible en todo elemento interactivo, con anillo en `interactivo`
- [ ] Contraste de texto mínimo 4.5:1 contra su fondo
- [ ] Ninguna información depende solo del color (las alertas también dicen el porcentaje)
- [ ] Todas las cifras con separador de miles y su unidad
- [ ] Estado vacío definido y escrito
- [ ] `prefers-reduced-motion` respetado
- [ ] Las tablas se leen bien con 35 filas, no solo con 3

---

## 15. Desviaciones registradas durante la implementación

Cambios sobre lo escrito arriba, con su motivo. Ningún valor se cambió sin dejarlo documentado aquí.

- **Mosaicos del mapa.** La sección 7 indica CARTO Positron por ser gratuito y sin API key. Al
  implementarlo, `basemaps.cartocdn.com` devolvía un mosaico gris con la leyenda "API KEY REQUIRED".
  Se sustituyó por **Esri World Light Gray Canvas**, que cumple el mismo criterio: monocromo, sin
  clave, sin tarjeta, y deja que los marcadores manden. La capa de referencia con topónimos se dibuja
  encima. Atribución: Esri, HERE, Garmin, OpenStreetMap.
- **Formato numérico en Filament.** Con `APP_LOCALE=es`, Filament formateaba las cifras con punto de
  miles. Se fijó el locale numérico a `en` solo para números, conservando las fechas en español, para
  respetar el formato con coma que pide la sección 4.
