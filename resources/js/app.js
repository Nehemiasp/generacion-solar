import L from 'leaflet';
import { Chart, BarController, LineController, BarElement, LineElement, PointElement, LinearScale, CategoryScale, Tooltip } from 'chart.js';

Chart.register(BarController, LineController, BarElement, LineElement, PointElement, LinearScale, CategoryScale, Tooltip);

// ---- Tokens (espejo de app.css; una sola fuente de verdad en DISENO.md) ----
const T = {
    gen: ['#EFE7CC', '#E0CD8A', '#CFAC45', '#A9831A', '#75590A'],
    ink: '#1B2426', ink2: '#5A6568', ink3: '#8D9698', rule: '#DCE0DE', grid: '#ECEFED',
    surface: '#FFFFFF', alerta: '#B32747', interactivo: '#0F5C63',
};
const MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
const reducirMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const fmt = (n, dec = 0) => Number(n).toLocaleString('es-GT', { minimumFractionDigits: dec, maximumFractionDigits: dec });
const energia = (kwh) => kwh >= 1_000_000 ? `${fmt(kwh / 1_000_000, 2)} GWh` : `${fmt(kwh)} kWh`;
const mes = (iso) => { const d = new Date(iso + 'T00:00:00'); return `${MESES[d.getMonth()]} ${d.getFullYear()}`; };

// Quintiles: 4 cortes
function quintiles(valores) {
    const v = valores.filter((x) => x > 0).sort((a, b) => a - b);
    if (v.length < 5) { const m = v.length ? v[v.length - 1] : 1; return [0.2, 0.4, 0.6, 0.8].map((q) => m * q); }
    return [0.2, 0.4, 0.6, 0.8].map((q) => v[Math.max(0, Math.floor(v.length * q) - 1)]);
}
const quintil = (x, cortes) => cortes.reduce((q, c) => (x > c ? q + 1 : q), 0);

const etiquetaFinal = {
    id: 'etiquetaFinal',
    afterDatasetsDraw(chart) {
        const { ctx } = chart;
        chart.data.datasets.forEach((ds, i) => {
            if (!ds.etiqueta) return;
            const meta = chart.getDatasetMeta(i);
            let ultimo = null;
            for (let k = meta.data.length - 1; k >= 0; k--) { if (ds.data[k] != null) { ultimo = meta.data[k]; break; } }
            if (!ultimo) return;
            ctx.save();
            ctx.font = '500 12px Archivo, system-ui, sans-serif';
            ctx.fillStyle = ds.etiquetaColor || T.ink2;
            ctx.textBaseline = 'middle';
            ctx.fillText(ds.etiqueta, ultimo.x + 8, ultimo.y);
            ctx.restore();
        });
    },
};

const baseOptions = (unidad = 'kWh') => ({
    responsive: true,
    maintainAspectRatio: false,
    animation: false,
    layout: { padding: { right: 72 } },
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: T.ink, titleColor: '#fff', bodyColor: '#fff', cornerRadius: 3,
            padding: 8, displayColors: false, titleFont: { weight: '500' },
            callbacks: { label: (c) => `${c.dataset.label}: ${fmt(c.parsed.y)} ${unidad}` },
        },
    },
    scales: {
        x: { grid: { display: false }, border: { color: T.rule }, ticks: { color: T.ink2, font: { size: 12 } } },
        y: {
            grid: { color: T.grid, lineWidth: 1 }, border: { display: false },
            ticks: { color: T.ink2, font: { size: 12 }, callback: (v) => (v >= 1_000_000 ? `${fmt(v / 1_000_000, 1)} M` : fmt(v)) },
        },
    },
});

const lineaReal = (label, data) => ({
    type: 'line', label, data, borderColor: T.ink, borderWidth: 1.5, tension: 0, fill: false,
    pointRadius: (c) => (c.dataIndex === c.dataset.data.length - 1 ? 3 : 0), pointBackgroundColor: T.ink,
    etiqueta: 'real', etiquetaColor: T.ink,
});
const lineaEsperada = (label, data) => ({
    type: 'line', label, data, borderColor: T.ink3, borderWidth: 1.5, borderDash: [3, 3], tension: 0, fill: false,
    pointRadius: 0, etiqueta: 'esperada', etiquetaColor: T.ink3,
});

// Irradiación mensual: 12 meses como columnas coloreadas por la escala + línea esperada
function bandaNacional(canvas) {
    const serie = JSON.parse(canvas.dataset.serie);
    const cortes = quintiles(serie.map((s) => s.real_kwh));
    new Chart(canvas, {
        data: {
            labels: serie.map((s) => mes(s.periodo)),
            datasets: [
                lineaEsperada('Esperada', serie.map((s) => s.esperada_kwh)),
                {
                    type: 'bar', label: 'Real', data: serie.map((s) => s.real_kwh),
                    backgroundColor: serie.map((s) => T.gen[quintil(s.real_kwh, cortes)]),
                    borderRadius: 0, borderWidth: 0, categoryPercentage: 0.9, barPercentage: 0.92,
                },
            ],
        },
        options: baseOptions(),
        plugins: [etiquetaFinal],
    });
}

// Historial real (sólida) + esperada (punteada) + proyección (punteada, prolonga la real)
function graficaGranja(canvas) {
    const hist = JSON.parse(canvas.dataset.historico);
    const proy = JSON.parse(canvas.dataset.proyeccion);
    const labels = [...hist.map((h) => mes(h.periodo)), ...proy.map((p) => mes(p.periodo))];
    const real = [...hist.map((h) => h.real_kwh), ...proy.map(() => null)];
    const esperada = [...hist.map((h) => h.esperada_kwh), ...proy.map(() => null)];
    const proyeccion = [...hist.map(() => null), ...proy.map((p) => p.kwh)];
    if (hist.length) proyeccion[hist.length - 1] = hist[hist.length - 1].real_kwh; // une la proyección con el último real
    new Chart(canvas, {
        data: {
            labels,
            datasets: [
                lineaEsperada('Esperada', esperada),
                lineaReal('Real', real),
                {
                    type: 'line', label: 'Proyección', data: proyeccion, borderColor: T.ink, borderWidth: 1.5,
                    borderDash: [3, 3], tension: 0, fill: false, spanGaps: false,
                    pointRadius: (c) => (c.dataIndex === labels.length - 1 ? 3 : 0), pointBackgroundColor: T.ink,
                    etiqueta: 'proyección', etiquetaColor: T.ink,
                },
            ],
        },
        options: baseOptions(),
        plugins: [etiquetaFinal],
    });
}

// ---- Mapa ----
function popupHtml(g) {
    const enlace = `/granjas/${g.id}`;
    return `
        <div class="popup-nombre">${g.nombre}</div>
        <div class="popup-lugar">${g.departamento}${g.municipio ? ', ' + g.municipio : ''}</div>
        <dl class="popup-metricas">
            <dt>Capacidad instalada</dt><dd>${fmt(g.capacidad_instalada_kw)} kW</dd>
            <dt>Paneles</dt><dd>${fmt(g.total_paneles)}</dd>
            <dt>Generación acumulada</dt><dd>${energia(g.generacion_acumulada_kwh)}</dd>
            <dt>Familias beneficiadas</dt><dd>${fmt(g.familias_beneficiadas)}</dd>
            <dt>CO₂ evitado</dt><dd>${fmt(g.co2_evitado_kg / 1000, 1)} t</dd>
        </dl>
        ${g.alertas_activas > 0 ? `<div class="popup-alerta">${g.alertas_activas} ${g.alertas_activas === 1 ? 'alerta activa' : 'alertas activas'} por generación bajo lo esperado</div>` : ''}
        <a class="popup-enlace" href="${enlace}">Ver ficha y proyección</a>`;
}

function leyendaHtml(cortes) {
    const limites = [0, ...cortes, Infinity];
    const filas = T.gen.map((c, i) => {
        const a = limites[i], b = limites[i + 1];
        const rango = b === Infinity ? `más de ${energia(a)}` : i === 0 ? `hasta ${energia(b)}` : `${energia(a)} a ${energia(b)}`;
        return `<div class="leyenda-fila"><span class="sw" style="background:${c}"></span>${rango}</div>`;
    }).join('');
    return `<div class="leyenda-titulo">Generación acumulada</div>${filas}
        <div class="leyenda-fila" style="margin-top:6px"><span class="leyenda-anillo"></span>Alerta activa (real ≤ 80% de esperada)</div>
        <div style="margin-top:4px;color:#8D9698">Tamaño: raíz cuadrada de la capacidad instalada</div>`;
}

async function mapa(el) {
    const conLeyenda = el.dataset.leyenda !== 'no';
    const map = L.map(el, { zoomControl: true, attributionControl: true, scrollWheelZoom: el.dataset.rueda !== 'no' })
        .setView([15.6, -90.3], 7);
    // Base monocroma: los marcadores mandan, ninguna carretera de color compite con los datos.
    const esri = 'https://services.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_';
    L.tileLayer(`${esri}Base/MapServer/tile/{z}/{y}/{x}`, {
        attribution: 'Esri, HERE, Garmin, &copy; OpenStreetMap', maxZoom: 16,
    }).addTo(map);
    L.tileLayer(`${esri}Reference/MapServer/tile/{z}/{y}/{x}`, { maxZoom: 16, pane: 'shadowPane' }).addTo(map);

    const vacio = document.createElement('div');
    vacio.className = 'mapa-vacio';
    vacio.hidden = true;
    el.appendChild(vacio);

    let granjas = [];
    try {
        const r = await fetch('/api/v1/granjas/mapa', { headers: { Accept: 'application/json' } });
        granjas = (await r.json()).data;
    } catch (e) {
        vacio.textContent = 'No se pudieron cargar las granjas. Recargá la página.';
        vacio.hidden = false;
        return;
    }
    if (!granjas.length) {
        vacio.textContent = 'No hay granjas activas registradas. Registrá una desde el panel de administración.';
        vacio.hidden = false;
        return;
    }

    const cortes = quintiles(granjas.map((g) => g.generacion_acumulada_kwh));
    const caps = granjas.map((g) => Math.sqrt(g.capacidad_instalada_kw));
    const [minC, maxC] = [Math.min(...caps), Math.max(...caps)];
    const radio = (kw) => (maxC === minC ? 12 : 4 + (18 * (Math.sqrt(kw) - minC)) / (maxC - minC));

    // Orden de entrada: escalonado por departamento, 400ms en total
    const deptos = [...new Set(granjas.map((g) => g.departamento_id))];
    const marcadores = granjas.map((g) => {
        const r = radio(g.capacidad_instalada_kw);
        const capa = L.layerGroup();
        if (g.alertas_activas > 0) {
            L.circleMarker([g.latitud, g.longitud], { radius: r + 3, color: T.alerta, weight: 2, fill: false, interactive: false }).addTo(capa);
        }
        const m = L.circleMarker([g.latitud, g.longitud], {
            radius: r, fillColor: T.gen[quintil(g.generacion_acumulada_kwh, cortes)], fillOpacity: 0.85,
            color: T.surface, weight: 1,
        }).bindPopup(popupHtml(g), { maxWidth: 320, closeButton: true }).addTo(capa);
        return { g, capa, m, r, retraso: (deptos.indexOf(g.departamento_id) / deptos.length) * 280 };
    });

    const bounds = L.latLngBounds(granjas.map((g) => [g.latitud, g.longitud]));
    map.fitBounds(bounds.pad(0.12));

    marcadores.forEach(({ capa, m, r, retraso }) => {
        capa.addTo(map);
        if (reducirMovimiento) return;
        m.setRadius(0);
        const inicio = performance.now() + retraso;
        const paso = (t) => {
            const p = Math.min(1, Math.max(0, (t - inicio) / 120));
            m.setRadius(r * p);
            if (p < 1) requestAnimationFrame(paso);
        };
        requestAnimationFrame(paso);
    });

    if (conLeyenda) {
        const leyenda = L.control({ position: 'bottomleft' });
        leyenda.onAdd = () => { const d = L.DomUtil.create('div', 'leyenda'); d.innerHTML = leyendaHtml(cortes); return d; };
        leyenda.addTo(map);
    }

    // Filtro por departamento (checkboxes nativos), sin recarga
    const filtro = document.querySelector(el.dataset.filtro || '#no-existe');
    if (filtro) {
        const aplicar = () => {
            const activos = new Set([...filtro.querySelectorAll('input:checked')].map((i) => Number(i.value)));
            let visibles = 0;
            marcadores.forEach(({ g, capa }) => {
                const ver = activos.has(g.departamento_id);
                if (ver) { capa.addTo(map); visibles++; } else { map.removeLayer(capa); }
            });
            vacio.hidden = visibles > 0;
            vacio.textContent = 'Ningún departamento seleccionado tiene granjas registradas. Ajustá el filtro.';
            const contador = document.querySelector('[data-contador]');
            if (contador) contador.textContent = `${visibles} de ${granjas.length} granjas`;
        };
        filtro.addEventListener('change', aplicar);
        filtro.querySelector('[data-todos]')?.addEventListener('click', () => { filtro.querySelectorAll('input').forEach((i) => (i.checked = true)); aplicar(); });
        filtro.querySelector('[data-ninguno]')?.addEventListener('click', () => { filtro.querySelectorAll('input').forEach((i) => (i.checked = false)); aplicar(); });
        aplicar();
    }
}

// Menú hamburguesa en móvil: muestra u oculta la navegación.
const botonMenu = document.querySelector('[data-menu]');
const nav = document.querySelector('[data-nav]');
botonMenu?.addEventListener('click', () => {
    const abierto = !nav.classList.toggle('hidden');
    nav.classList.toggle('flex', abierto); // columna en móvil; en escritorio sm:flex ya lo muestra
    botonMenu.setAttribute('aria-expanded', String(abierto));
    botonMenu.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
    // Los SVG no tienen la propiedad .hidden; hay que tocar el atributo.
    botonMenu.querySelector('[data-icono-abrir]').toggleAttribute('hidden', abierto);
    botonMenu.querySelector('[data-icono-cerrar]').toggleAttribute('hidden', !abierto);
});

// Volver: historial real del navegador cuando existe; si no, el href (referer o tablero).
document.querySelectorAll('[data-volver]').forEach((a) => {
    a.addEventListener('click', (e) => {
        if (history.length > 1) { e.preventDefault(); history.back(); }
    });
});

document.querySelectorAll('[data-mapa]').forEach(mapa);
document.querySelectorAll('[data-banda]').forEach(bandaNacional);
document.querySelectorAll('[data-grafica-granja]').forEach(graficaGranja);
