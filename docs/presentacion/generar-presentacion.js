const pptxgen = require('pptxgenjs');

// Paleta del sistema de diseño del proyecto (docs/DISENO.md)
const C = {
  ink: '1B2426', ink2: '5A6568', ink3: '8D9698',
  ground: 'F5F6F4', surface: 'FFFFFF', rule: 'DCE0DE', ruleStrong: 'B6BDBA',
  g1: 'EFE7CC', g2: 'E0CD8A', g3: 'CFAC45', g4: 'A9831A', g5: '75590A',
  alerta: 'B32747', alertaGrave: '7E1230', teal: '0F5C63',
};
const F = { titulo: 'Arial', cuerpo: 'Calibri' };
const W = 10, H = 5.625, M = 0.5;

const pres = new pptxgen();
pres.layout = 'LAYOUT_16x9';
pres.author = 'Equipo de desarrollo';
pres.title = 'Generación solar por departamento';

// --- helpers ---------------------------------------------------------------

function slideBase(dark = false) {
  const s = pres.addSlide();
  s.background = { color: dark ? C.ink : C.ground };
  return s;
}

// Motivo repetido: los cinco escalones de la escala de generación
function escala(s, x, y, ancho = 0.22, alto = 0.09) {
  [C.g1, C.g2, C.g3, C.g4, C.g5].forEach((color, i) => {
    s.addShape(pres.ShapeType.rect, { x: x + i * ancho, y, w: ancho, h: alto, fill: { color }, line: { width: 0 } });
  });
}

function titulo(s, texto, dark = false) {
  s.addText(texto, {
    x: M, y: 0.42, w: W - 2 * M, h: 0.62, isTextBox: true, margin: 0,
    fontFace: F.titulo, fontSize: 30, bold: true, color: dark ? C.surface : C.ink, align: 'left',
  });
}

function bajada(s, texto, dark = false) {
  s.addText(texto, {
    x: M, y: 1.06, w: W - 2 * M, h: 0.3, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 13, color: dark ? C.ink3 : C.ink2,
  });
}

// Panel blanco con borde de 1px, sin sombra ni esquinas redondeadas
function panel(s, x, y, w, h) {
  s.addShape(pres.ShapeType.rect, { x, y, w, h, fill: { color: C.surface }, line: { color: C.rule, width: 1 } });
}

// Cifra grande con su etiqueta debajo
function cifra(s, x, y, w, valor, unidad, etiqueta, color = C.ink) {
  s.addText(
    [
      { text: valor, options: { fontSize: 26, bold: true, color, fontFace: F.titulo } },
      ...(unidad ? [{ text: ' ' + unidad, options: { fontSize: 11, color: C.ink3, fontFace: F.cuerpo } }] : []),
    ],
    { x, y, w, h: 0.42, isTextBox: true, margin: 0, valign: 'bottom' },
  );
  s.addText(etiqueta, {
    x, y: y + 0.44, w, h: 0.24, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 11, color: C.ink2,
  });
}

function vinetas(s, x, y, w, items, opts = {}) {
  const fontSize = opts.fontSize || 13;
  s.addText(
    items.map((t, i) => ({
      text: t,
      options: { bullet: { code: '2013', indent: 14 }, breakLine: i < items.length - 1, paraSpaceAfter: 7 },
    })),
    {
      x, y, w, h: opts.h || 2.6, isTextBox: true, margin: 0, valign: 'top',
      fontFace: F.cuerpo, fontSize, color: opts.color || C.ink, lineSpacing: Math.round(fontSize * 1.3),
    },
  );
}

function rotulo(s, x, y, w, texto, color = C.ink2) {
  s.addText(texto, {
    x, y, w, h: 0.24, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 11, bold: true, color,
  });
}

function pie(s, texto) {
  s.addText(texto, {
    x: M, y: H - 0.42, w: W - 2 * M, h: 0.24, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 9, color: C.ink3,
  });
}

// --- 1. Portada ------------------------------------------------------------
{
  const s = slideBase(true);
  escala(s, M, 1.15, 0.5, 0.14);
  s.addText('Generación solar\npor departamento', {
    x: M, y: 1.6, w: 6.6, h: 1.7, isTextBox: true, margin: 0,
    fontFace: F.titulo, fontSize: 40, bold: true, color: C.surface, lineSpacing: 44,
  });
  s.addText('Sistema de registro y monitoreo para los 22 departamentos de Guatemala', {
    x: M, y: 3.45, w: 6.8, h: 0.4, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 15, color: C.g2,
  });
  s.addText('Competencia de Programación con IA · Laravel 13 · 11 y 12 de septiembre de 2026', {
    x: M, y: 4.0, w: 7.2, h: 0.3, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 12, color: C.ink3,
  });
  s.addText('Equipo', {
    x: 7.6, y: 3.45, w: 1.9, h: 0.24, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 10, color: C.ink3,
  });
  s.addText('Integrante 1\nIntegrante 2', {
    x: 7.6, y: 3.7, w: 1.9, h: 0.6, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 12, color: C.surface, lineSpacing: 16,
  });
  s.addNotes('Presentamos un sistema para monitorear la generación solar del país por departamento. Dos minutos de contexto y arquitectura, luego demostración en vivo.');
}

// --- 2. El problema --------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Qué había que resolver');
  bajada(s, 'Registrar activos solares, medir su desempeño real y detectar dónde está fallando la generación.');

  const bloques = [
    ['22', 'departamentos', 'Cada granja se asocia a uno, con coordenadas propias'],
    ['17', 'requerimientos', 'Del catálogo de departamentos a la proyección de generación'],
    ['1', 'día', 'De la base de datos vacía a la aplicación desplegada'],
  ];
  bloques.forEach(([n, u, d], i) => {
    const x = M + i * 3.07;
    panel(s, x, 1.6, 2.87, 1.5);
    s.addText(n, {
      x: x + 0.22, y: 1.78, w: 2.4, h: 0.6, isTextBox: true, margin: 0,
      fontFace: F.titulo, fontSize: 34, bold: true, color: C.ink,
    });
    s.addText(u, {
      x: x + 0.22, y: 2.34, w: 2.4, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 12, color: C.ink2,
    });
    s.addText(d, {
      x: x + 0.22, y: 2.6, w: 2.45, h: 0.44, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 10.5, color: C.ink3, lineSpacing: 13,
    });
  });

  rotulo(s, M, 3.35, 4.3, 'Lo que el sistema calcula solo');
  vinetas(s, M, 3.62, 4.3, [
    'Capacidad instalada, a partir de los paneles de cada granja',
    'CO₂ evitado, a 0.40 kg por kWh generado',
    'Desviación entre generación real y esperada',
  ], { h: 1.3, fontSize: 12 });

  rotulo(s, 5.2, 3.35, 4.3, 'Lo que el sistema vigila');
  vinetas(s, 5.2, 3.62, 4.3, [
    'Alerta cuando la generación real cae al 80% de la esperada o menos',
    'Proyección de los próximos meses con su margen de error',
  ], { h: 1.3, fontSize: 12 });

  pie(s, 'Bases del reto, secciones 4 y 14');
  s.addNotes('El reto pide registro, análisis, mapa, alertas y proyección. Decidimos separar lo que se captura de lo que se calcula: nada derivado se guarda en la base.');
}

// --- 3. Arquitectura -------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Arquitectura');
  bajada(s, 'Un panel interno para capturar y un frontend propio para analizar. Comparten modelos y servicios, no vistas.');

  const capas = [
    ['Captura', 'Filament 5', 'CRUD de granjas, paneles y generación, con validación y relación de paneles por granja', C.g3],
    ['Dominio', 'Servicios de Laravel', 'AlertaService, ProyeccionService y EstadisticasService. Toda la regla de negocio vive aquí', C.g4],
    ['Consulta', 'Blade y API REST', 'Tablero, mapa, reporte y ficha pública; trece endpoints bajo /api/v1', C.g5],
  ];
  capas.forEach(([capa, tec, desc, color], i) => {
    const y = 1.62 + i * 0.92;
    panel(s, M, y, 5.55, 0.8);
    s.addShape(pres.ShapeType.rect, { x: M, y, w: 0.1, h: 0.8, fill: { color }, line: { width: 0 } });
    s.addText(capa, {
      x: M + 0.26, y: y + 0.11, w: 1.1, h: 0.26, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11, bold: true, color: C.ink2,
    });
    s.addText(tec, {
      x: M + 1.35, y: y + 0.09, w: 4.0, h: 0.28, isTextBox: true, margin: 0,
      fontFace: F.titulo, fontSize: 14, bold: true, color: C.ink,
    });
    s.addText(desc, {
      x: M + 1.35, y: y + 0.38, w: 4.05, h: 0.36, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 10, color: C.ink2, lineSpacing: 12,
    });
  });

  panel(s, 6.35, 1.62, 3.15, 2.62);
  rotulo(s, 6.57, 1.8, 2.7, 'Stack');
  const stack = [
    ['Backend', 'Laravel 13, PHP 8.4'],
    ['Base de datos', 'PostgreSQL'],
    ['Panel', 'Filament 5'],
    ['Frontend', 'Blade y Tailwind 4'],
    ['Mapa', 'Leaflet, sin API key'],
    ['Gráficas', 'Chart.js'],
    ['Doc de API', 'Scramble, OpenAPI'],
  ];
  stack.forEach(([k, v], i) => {
    const y = 2.14 + i * 0.28;
    s.addText(k, {
      x: 6.57, y, w: 1.1, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 10, color: C.ink3,
    });
    s.addText(v, {
      x: 7.62, y, w: 1.7, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 10, bold: true, color: C.ink, align: 'right',
    });
  });

  s.addText('Filament resuelve el CRUD en horas; el frontend público es propio porque ahí se juega la lectura de los datos.', {
    x: M, y: 4.45, w: 9.0, h: 0.34, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 11.5, italic: true, color: C.ink2,
  });
  pie(s, '17 pruebas automatizadas, 71 aserciones');
  s.addNotes('Filament nos dio el CRUD y las validaciones. El análisis lo construimos aparte en Blade porque un panel administrativo no comunica bien un tablero de operación.');
}

// --- 4. Modelo de datos ----------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Modelo de datos');
  bajada(s, 'Seis tablas. Lo que se puede derivar de otra tabla no se guarda como columna.');

  const caja = (x, y, w, nombre, campos, color) => {
    panel(s, x, y, w, 0.86);
    s.addShape(pres.ShapeType.rect, { x, y, w, h: 0.06, fill: { color }, line: { width: 0 } });
    s.addText(nombre, {
      x: x + 0.14, y: y + 0.14, w: w - 0.28, h: 0.26, isTextBox: true, margin: 0,
      fontFace: F.titulo, fontSize: 12.5, bold: true, color: C.ink,
    });
    s.addText(campos, {
      x: x + 0.14, y: y + 0.42, w: w - 0.28, h: 0.4, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 9.5, color: C.ink2, lineSpacing: 11,
    });
  };

  caja(M, 1.6, 2.1, 'departamentos', '22 registros\ncódigo, cabecera, centroide', C.g2);
  caja(2.95, 1.6, 2.2, 'granjas', 'ubicación, familias,\nesperada mensual, activa', C.g4);
  caja(5.55, 1.6, 2.0, 'granja_panel', 'modelo de panel\ny cantidad', C.g3);
  caja(7.95, 1.6, 1.55, 'modelos_panel', 'marca, modelo,\npotencia kW', C.g2);
  caja(2.95, 2.72, 2.2, 'generaciones', 'período mensual,\nreal y esperada', C.g5);
  caja(5.55, 2.72, 2.0, 'alertas', 'desviación %,\nestado', C.alerta);

  // Conectores
  const linea = (x1, y1, x2, y2) => s.addShape(pres.ShapeType.line, {
    x: x1, y: y1, w: x2 - x1, h: y2 - y1, line: { color: C.ruleStrong, width: 1 },
  });
  linea(2.6, 2.03, 2.95, 2.03);
  linea(5.05, 2.03, 5.55, 2.03);
  linea(7.55, 2.03, 7.95, 2.03);
  linea(4.05, 2.46, 4.05, 2.72);
  linea(5.15, 3.15, 5.55, 3.15);

  panel(s, M, 3.78, 9.0, 1.12);
  rotulo(s, M + 0.22, 3.94, 4.0, 'Valores calculados, nunca almacenados');
  s.addText(
    'Capacidad instalada = Σ (cantidad × potencia kW)     ·     Generación acumulada = Σ generación real     ·     CO₂ evitado = generación × 0.40',
    { x: M + 0.22, y: 4.2, w: 8.6, h: 0.26, isTextBox: true, margin: 0, fontFace: F.cuerpo, fontSize: 11, color: C.ink },
  );
  s.addText(
    'Guardarlos como columnas los desincroniza cada vez que cambian los paneles de una granja. El scope conMetricas() los resuelve con subconsultas: 35 granjas con sus métricas en una sola consulta.',
    { x: M + 0.22, y: 4.48, w: 8.6, h: 0.32, isTextBox: true, margin: 0, fontFace: F.cuerpo, fontSize: 10, color: C.ink2 },
  );
  s.addNotes('Pregunta típica del jurado: por qué no guardaron la capacidad instalada. Porque es un dato derivado y guardarlo introduce riesgo de desincronización. Lo resolvemos con subconsultas, sin N+1.');
}

// --- 5. Tablero ------------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Tablero nacional');
  bajada(s, 'La banda de doce meses es el héroe: cada columna es un mes, coloreada con la escala de generación.');
  s.addImage({ path: 'c_banda.png', x: M, y: 1.55, w: 9.0, h: 2.556 });

  const notas = [
    ['Una sola escala de color', 'Los mismos cinco escalones en el mapa, la tabla y esta banda: el ojo la aprende una vez'],
    ['Cifras tabulares', 'Toda cifra con separador de miles y su unidad; GWh sobre el millón de kWh'],
    ['Sin adornos', 'Sin sombras, sin gradientes, sin tarjetas con ícono. Densidad de consola de operación'],
  ];
  notas.forEach(([t, d], i) => {
    const x = M + i * 3.07;
    s.addText(t, {
      x, y: 4.25, w: 2.9, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11.5, bold: true, color: C.ink,
    });
    s.addText(d, {
      x, y: 4.5, w: 2.9, h: 0.5, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 10, color: C.ink2, lineSpacing: 12,
    });
  });
  pie(s, 'RF-11, RF-12 · indicadores nacionales y comparativos por departamento');
  s.addNotes('La línea punteada es la generación esperada. Se ve de un vistazo que el verano seco de noviembre a abril genera más, y que los últimos tres meses caen por debajo de lo esperado.');
}

// --- 6. Mapa ---------------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Mapa interactivo');
  bajada(s, 'Las 35 granjas activas sobre base monocroma, con filtro por departamento y sin recargar la página.');
  s.addImage({ path: 'c_mapa.png', x: M, y: 1.55, w: 5.4, h: 4.203 * 5.4 / 5.4 * (1510 / 1940) * 5.4 / 5.4 });

  const decisiones = [
    ['Radio por raíz cuadrada', 'El área del círculo queda proporcional a la capacidad instalada; sin la raíz se exageran las granjas grandes'],
    ['Relleno por quintil', 'El color codifica generación acumulada, la misma escala del resto del sistema'],
    ['Anillo para la alerta', 'Una granja con alerta conserva su color y recibe un anillo carmesí: dato y señal son capas distintas'],
    ['Base sin API key', 'Mosaicos grises de Esri; CARTO pasó a exigir clave y lo detectamos al probarlo'],
  ];
  decisiones.forEach(([t, d], i) => {
    const y = 1.62 + i * 0.83;
    s.addText(t, {
      x: 6.15, y, w: 3.35, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11.5, bold: true, color: C.ink,
    });
    s.addText(d, {
      x: 6.15, y: y + 0.24, w: 3.35, h: 0.54, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 9.5, color: C.ink2, lineSpacing: 11,
    });
  });
  pie(s, 'RF-13 · datos servidos por /api/v1/granjas/mapa, no incrustados en la vista');
  s.addNotes('El mapa consume el mismo endpoint público que cualquier cliente. El filtro oculta marcadores en el navegador, sin ida y vuelta al servidor.');
}

// --- 7. Alertas ------------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Alertas de generación');
  bajada(s, 'Un observador evalúa cada registro de generación al guardarlo. La alerta se crea, se actualiza o se retira sola.');

  panel(s, M, 1.58, 4.3, 1.35);
  s.addText('generación real  ≤  80% × generación esperada', {
    x: M + 0.24, y: 1.78, w: 3.85, h: 0.32, isTextBox: true, margin: 0,
    fontFace: F.titulo, fontSize: 14, bold: true, color: C.ink,
  });
  s.addText('El umbral vive en config/solar.php, no escrito en el código. Cambiarlo no exige tocar la lógica.', {
    x: M + 0.24, y: 2.16, w: 3.85, h: 0.5, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 10.5, color: C.ink2, lineSpacing: 13,
  });

  rotulo(s, M, 3.1, 4.3, 'El caso borde que el jurado va a probar');
  const casos = [
    ['79%', 'genera alerta', C.alerta],
    ['80%', 'genera alerta', C.alerta],
    ['81%', 'no genera alerta', C.ink2],
  ];
  casos.forEach(([pct, res, color], i) => {
    const y = 3.42 + i * 0.42;
    s.addShape(pres.ShapeType.rect, { x: M, y, w: 4.3, h: 0.36, fill: { color: C.surface }, line: { color: C.rule, width: 1 } });
    s.addText(pct, {
      x: M + 0.16, y: y + 0.06, w: 0.7, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.titulo, fontSize: 12, bold: true, color: C.ink,
    });
    s.addText(res, {
      x: M + 0.9, y: y + 0.07, w: 3.2, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11, color, align: 'right',
    });
  });
  s.addText('El requisito dice "al menos 20% por debajo", así que el 80% exacto sí alerta. Hay una prueba por cada caso.', {
    x: M, y: 4.72, w: 4.3, h: 0.4, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 10, italic: true, color: C.ink2, lineSpacing: 12,
  });

  s.addImage({ path: 'c_alertas.png', x: 5.1, y: 1.58, w: 4.4, h: 1.784 });
  panel(s, 5.1, 3.55, 4.4, 1.55);
  rotulo(s, 5.32, 3.72, 4.0, 'Lo que la alerta guarda');
  vinetas(s, 5.32, 3.98, 3.95, [
    'Granja, período, esperada, real y porcentaje de desviación',
    'Estado activa, revisada o resuelta, editable desde el panel',
    'Peor que −40% se marca como grave, con su propio color',
  ], { h: 1.0, fontSize: 10 });
  pie(s, 'RF-14 · comando solar:evaluar-alertas reconstruye todo tras una carga masiva');
  s.addNotes('El observador corre en el guardado del modelo. Para cargas masivas, que no disparan eventos, existe el comando artisan que reevalúa las 630 generaciones.');
}

// --- 8. Proyección ---------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Proyección de generación');
  bajada(s, 'Regresión lineal por mínimos cuadrados sobre los últimos 12 períodos registrados.');

  const meses = ['sep 25', 'oct 25', 'nov 25', 'dic 25', 'ene 26', 'feb 26', 'mar 26', 'abr 26', 'may 26', 'jun 26', 'jul 26', 'ago 26', 'sep 26', 'oct 26', 'nov 26'];
  const real = [449314, 436675, 550920, 538991, 532960, 574832, 524043, 535973, 442202, 281814, 265236, 232082, null, null, null];
  const esperada = [414432, 414432, 527458, 527458, 527458, 527458, 527458, 527458, 414432, 414432, 414432, 414432, null, null, null];
  const proy = [null, null, null, null, null, null, null, null, null, null, null, 232082, 302952, 280777, 258602];

  s.addChart(
    [
      {
        type: pres.ChartType.line,
        data: [{ name: 'Esperada', labels: meses, values: esperada }],
        options: { chartColors: [C.ink3], lineSize: 1.5, lineDash: 'dash' },
      },
      {
        type: pres.ChartType.line,
        data: [{ name: 'Real', labels: meses, values: real }],
        options: { chartColors: [C.ink], lineSize: 2.5 },
      },
      {
        type: pres.ChartType.line,
        data: [{ name: 'Proyección', labels: meses, values: proy }],
        options: { chartColors: [C.g4], lineSize: 2.5, lineDash: 'dash' },
      },
    ],
    {
      x: M, y: 1.55, w: 5.9, h: 2.75,
      lineDataSymbol: 'none', lineSmooth: false,
      showLegend: true, legendPos: 'b', legendFontSize: 9, legendColor: C.ink2,
      showTitle: false,
      catAxisLabelColor: C.ink2, catAxisLabelFontSize: 8, catGridLine: { style: 'none' },
      valAxisLabelColor: C.ink2, valAxisLabelFontSize: 8,
      valGridLine: { color: C.rule, size: 1 },
      valAxisLabelFormatCode: '#,##0,"k"',
      plotArea: { fill: { color: C.surface } },
      chartArea: { fill: { color: C.surface }, border: { pt: 0, color: C.surface } },
    },
  );
  s.addText('Granja Solar Teculután · kWh por mes', {
    x: M, y: 4.34, w: 5.9, h: 0.24, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 10, color: C.ink3,
  });

  rotulo(s, 6.6, 1.58, 3.0, 'Por qué una recta y no un modelo entrenado');
  vinetas(s, 6.6, 1.88, 2.9, [
    'Doce a dieciocho puntos por granja: un modelo entrenado sobreajusta sin ganar precisión',
    'Es determinista y reproducible, sin dependencias externas',
    'La pendiente es explicable: son los kWh que la granja gana o pierde cada mes',
  ], { h: 1.85, fontSize: 10 });

  panel(s, 6.6, 3.8, 2.9, 1.1);
  s.addText([
    { text: 'R² 0.43', options: { fontSize: 15, bold: true, color: C.ink, fontFace: F.titulo, breakLine: true } },
    { text: 'pendiente −22,175 kWh por mes', options: { fontSize: 10, color: C.ink2, fontFace: F.cuerpo, breakLine: true } },
    { text: 'error absoluto medio 25.7%', options: { fontSize: 10, color: C.ink2, fontFace: F.cuerpo } },
  ], { x: 6.82, y: 3.96, w: 2.5, h: 0.85, isTextBox: true, margin: 0, lineSpacing: 14 });

  pie(s, 'RF-15 · con menos de 3 períodos cae a promedio; las proyecciones tienen piso en cero');
  s.addNotes('Además de proyectar, el sistema se audita: para cada mes con dato real recalcula qué habría proyectado usando solo lo anterior y reporta el error. Esa tabla está en la ficha de cada granja.');
}

// --- 9. API REST -----------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'API REST y documentación');
  bajada(s, 'Trece endpoints de solo lectura bajo /api/v1, documentados desde el propio código con Scramble.');

  const filas = [
    ['GET  /departamentos', 'Los 22, con granjas activas'],
    ['GET  /departamentos/{id}', 'Detalle con estadísticas agregadas'],
    ['GET  /granjas', 'Paginado; filtros de departamento, estado y nombre'],
    ['GET  /granjas/{id}', 'Detalle con sus paneles y métricas'],
    ['GET  /granjas/mapa', 'Payload liviano que consume el mapa'],
    ['GET  /granjas/{id}/generaciones', 'Serie mensual, con rango de períodos'],
    ['GET  /granjas/{id}/proyeccion', 'Proyección y precisión histórica'],
    ['GET  /generaciones', 'Paginado; período, departamento o granja'],
    ['GET  /estadisticas/nacional', 'Totales y serie de doce meses'],
    ['GET  /estadisticas/departamentos', 'Reporte por departamento'],
    ['GET  /alertas', 'Paginado; estado y departamento'],
  ];
  panel(s, M, 1.55, 6.0, 3.35);
  filas.forEach(([ruta, desc], i) => {
    const y = 1.66 + i * 0.29;
    if (i > 0) {
      s.addShape(pres.ShapeType.line, { x: M, y: y - 0.02, w: 6.0, h: 0, line: { color: C.rule, width: 1 } });
    }
    s.addText(ruta, {
      x: M + 0.18, y, w: 2.6, h: 0.26, isTextBox: true, margin: 0,
      fontFace: 'Courier New', fontSize: 9, color: C.teal,
    });
    s.addText(desc, {
      x: M + 2.8, y, w: 3.05, h: 0.26, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 9.5, color: C.ink2,
    });
  });

  const extras = [
    ['Respuestas consistentes', 'data, links y meta en cada listado'],
    ['Errores legibles', '404 y 422 en JSON y en español'],
    ['Límite de uso', '60 peticiones por minuto por IP'],
    ['Documentación viva', '/docs/api navegable y docs/API.md de respaldo'],
  ];
  extras.forEach(([t, d], i) => {
    const y = 1.58 + i * 0.84;
    s.addText(t, {
      x: 6.75, y, w: 2.75, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11.5, bold: true, color: C.ink,
    });
    s.addText(d, {
      x: 6.75, y: y + 0.24, w: 2.75, h: 0.5, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 9.5, color: C.ink2, lineSpacing: 11,
    });
  });
  pie(s, 'RF-16 · el esquema OpenAPI se genera leyendo los controladores, así que no se desactualiza');
  s.addNotes('Scramble lee los tipos y anotaciones de los controladores y genera el OpenAPI. Si cambiamos un parámetro, la documentación cambia con él.');
}

// --- 10. Uso de IA ---------------------------------------------------------
{
  const s = slideBase();
  titulo(s, 'Cómo usamos la inteligencia artificial');
  bajada(s, 'Claude Code como acelerador, con el criterio técnico del equipo por delante.');

  panel(s, M, 1.55, 4.4, 3.35);
  rotulo(s, M + 0.22, 1.72, 4.0, 'Lo que decidimos nosotros', C.ink);
  vinetas(s, M + 0.22, 1.98, 3.95, [
    'El esquema de base de datos y la regla de no almacenar valores derivados',
    'El stack: Filament para capturar, Blade propio para analizar',
    'El método de proyección y sus salvaguardas',
    'El 80% exacto como caso que sí alerta',
    'El sistema de diseño completo, escrito antes de programar',
  ], { h: 2.8, fontSize: 10.5 });

  panel(s, 5.2, 1.55, 4.3, 3.35);
  rotulo(s, 5.42, 1.72, 4.0, 'Lo que tuvimos que corregirle', C.alerta);
  vinetas(s, 5.42, 1.98, 3.85, [
    'Los mosaicos de CARTO ya exigen API key: lo descubrimos al probar, no al leer',
    'El limitador de peticiones no venía definido y la API entera respondía 500',
    'Un parámetro opcional sin valor lanzaba excepción en la proyección',
    'La regresión tomaba los 12 períodos más antiguos por el orden de la relación',
    'Filament mostraba 414.432 en vez de 414,432 por el locale',
  ], { h: 2.8, fontSize: 10.5 });

  s.addText('Cada cambio se ejecutó antes de darlo por bueno. El detalle completo está en docs/USO-DE-IA.md.', {
    x: M, y: 5.0, w: 9.0, h: 0.3, isTextBox: true, margin: 0,
    fontFace: F.cuerpo, fontSize: 11, italic: true, color: C.ink2,
  });
  s.addNotes('La IA es rápida escribiendo y mala decidiendo. Escribimos primero las reglas del proyecto en CLAUDE.md y el sistema de diseño, y con eso el resultado deja de ser genérico.');
}

// --- 11. Cierre ------------------------------------------------------------
{
  const s = slideBase(true);
  escala(s, M, 0.5, 0.5, 0.14);
  s.addText('El sistema, en números', {
    x: M, y: 0.92, w: 6.5, h: 0.6, isTextBox: true, margin: 0,
    fontFace: F.titulo, fontSize: 30, bold: true, color: C.surface,
  });

  const metricas = [
    ['35', 'granjas activas'], ['260,191', 'paneles'], ['145.5 MW', 'capacidad'],
    ['309 GWh', 'generación'], ['101,398', 'familias'], ['123,613 t', 'CO₂ evitado'],
  ];
  metricas.forEach(([v, e], i) => {
    const x = M + (i % 3) * 3.07;
    const y = 1.75 + Math.floor(i / 3) * 1.0;
    s.addText(v, {
      x, y, w: 2.9, h: 0.44, isTextBox: true, margin: 0,
      fontFace: F.titulo, fontSize: 24, bold: true, color: C.g2,
    });
    s.addText(e, {
      x, y: y + 0.44, w: 2.9, h: 0.24, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11, color: C.ink3,
    });
  });

  s.addShape(pres.ShapeType.line, { x: M, y: 3.85, w: 9.0, h: 0, line: { color: C.ink2, width: 1 } });

  const enlaces = [
    ['Aplicación', 'generacion-solar-production-gtqero.laravel.cloud'],
    ['Repositorio', 'github.com/Nehemiasp/generacion-solar'],
    ['Documentación de la API', 'generacion-solar-production-gtqero.laravel.cloud/docs/api'],
  ];
  enlaces.forEach(([k, v], i) => {
    const y = 4.05 + i * 0.34;
    s.addText(k, {
      x: M, y, w: 2.6, h: 0.26, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11, color: C.ink3,
    });
    s.addText(v, {
      x: 3.2, y, w: 6.3, h: 0.26, isTextBox: true, margin: 0,
      fontFace: F.cuerpo, fontSize: 11, color: C.g2,
    });
  });
  s.addNotes('Cerramos con la demostración en vivo: crear una generación por debajo del umbral y ver aparecer la alerta sola en el tablero.');
}

pres.writeFile({ fileName: 'Generacion-Solar-Guatemala.pptx' }).then((f) => console.log('escrito', f));
