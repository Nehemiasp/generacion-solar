# Manual de usuario

Sistema de Registro y Monitoreo de Generación Solar por Departamento · Guatemala

La aplicación tiene dos partes: las **pantallas públicas**, que cualquiera puede consultar sin
cuenta, y el **panel de administración**, donde el personal autorizado registra granjas, paneles y
generación mensual.

- Sitio: https://generacion-solar-production-gtqero.laravel.cloud
- Panel: https://generacion-solar-production-gtqero.laravel.cloud/admin

## 1. Pantallas públicas

La barra superior lleva a las cinco secciones: **Tablero, Mapa, Reporte, Alertas, API**, más el
acceso a **Administración**. En pantallas angostas la barra se convierte en un menú (icono ☰).
Todas las pantallas, salvo el Tablero, tienen un botón **Volver** arriba a la izquierda.

### 1.1 Tablero

Es la página de inicio. De arriba hacia abajo:

1. **Generación nacional, últimos 12 meses.** Columnas: generación real de cada mes. Línea
   punteada: generación esperada. Cuanto más oscuro el dorado de una columna, mayor la generación.
2. **Cinco indicadores nacionales:** granjas activas, paneles instalados, capacidad instalada (MW),
   generación acumulada (GWh) y CO₂ evitado (toneladas).
3. **Granjas activas:** mapa reducido; el enlace "Mapa completo con filtros" abre la versión grande.
4. **Alertas activas:** las más recientes, con la desviación en rojo. Clic en el nombre de la granja
   abre su ficha.
5. **Por departamento:** una fila por cada uno de los 22 departamentos con granjas, paneles, kW,
   generación, familias, CO₂, alertas y una mini-gráfica de 12 meses. Los departamentos sin granjas
   aparecen en gris con "—".

### 1.2 Mapa

- Cada círculo es una granja activa. Su **tamaño** depende de la capacidad instalada y su **color**
  de la generación acumulada (la leyenda inferior indica los rangos).
- Un **anillo rojo** marca una granja con alerta activa.
- Clic en un círculo: nombre, departamento, capacidad, generación, familias, CO₂ y enlace a la ficha.
- Panel **Departamentos** a la izquierda: marque o desmarque casillas para mostrar u ocultar granjas.
  **Todos** y **Ninguno** actúan sobre la lista completa. El contador indica cuántas granjas se ven.

### 1.3 Reporte por departamento

Tabla con los 22 departamentos. Para acotar un rango de meses, elija **Desde** y **Hasta** y pulse
**Aplicar**: las columnas de generación, "vs. esperada" y CO₂ se recalculan; las de granjas, paneles y
kW no dependen de fechas. Clic en un encabezado ordena por esa columna.

### 1.4 Alertas

Lista de alertas con pestañas **Activas, Revisadas, Resueltas, Todas**. Una alerta marcada
"Activa · grave" tiene una desviación de −40 % o peor. Clic en la granja abre su ficha.

### 1.5 Ficha de granja

Se llega desde cualquier tabla o desde el mapa. Contiene:

- Cinco indicadores propios: capacidad, paneles, generación acumulada, familias, CO₂.
- **Generación histórica y proyección:** línea continua = real; punteada gris = esperada; punteada
  negra = proyección de los próximos tres meses. Debajo, la tabla de meses proyectados y la de
  **precisión histórica** (qué habría proyectado el modelo cada mes y su error).
- **Paneles instalados** por modelo y **Generación por período** con la desviación de cada mes.

### 1.6 API

`/docs/api` abre la documentación interactiva de la API REST: cada endpoint se puede probar desde el
navegador. La misma información está en [API.md](API.md).

## 2. Panel de administración

### 2.1 Entrar

Abra `/admin`, escriba el correo y la contraseña que le entregó el equipo y pulse **Acceso**. El menú
lateral tiene: Escritorio, Granjas, Modelos de panel, Generaciones, Alertas, y accesos directos al
tablero y mapa públicos y a la documentación de la API. En pantallas angostas el menú se abre con ☰.

### 2.2 Escritorio

Seis indicadores nacionales y la tabla de alertas activas más recientes. Es el mismo dato que ve el
público, para verificar el efecto de lo que se registra.

### 2.3 Modelos de panel

Catálogo de paneles (marca, modelo, potencia en kW, eficiencia, estado). Hay que crear el modelo
antes de asignarlo a una granja. **Crear modelo de panel** arriba a la derecha; **Editar** en cada
fila. Un modelo **Descontinuado** ya no se ofrece en nuevas instalaciones, pero conserva las que tenía.

### 2.4 Granjas

**Crear granja:** nombre, departamento, municipio, dirección, latitud y longitud (deben caer dentro
de Guatemala: 13.7–17.8 y −92.2 a −88.2), familias beneficiadas, generación esperada mensual en kWh,
fecha de instalación y el interruptor **Activa**.

**Agregar paneles:** al guardar, la ficha muestra la sección **Paneles instalados**. Pulse **Crear
panel**, elija el modelo y la cantidad. La capacidad total se recalcula sola.

**Desactivar:** una granja no se borra; la acción **Desactivar** de la lista la saca del mapa y de
los indicadores conservando su historial. **Ver** abre la ficha con las métricas calculadas y un enlace
a la ficha pública.

La lista se puede buscar por nombre y filtrar por departamento y por estado.

### 2.5 Generaciones

Registro mensual. **Crear generación:** elija la granja, el período (mes y año), y escriba la
generación **real**. La **esperada** se precarga desde la granja y se puede ajustar; queda guardada
como copia del período. Solo se admite un registro por granja y mes.

Al guardar, el sistema evalúa la regla: **si la real es igual o menor al 80 % de la esperada, se crea
una alerta automáticamente**. Si más tarde se corrige el valor y deja de cumplirse, la alerta se
elimina sola.

### 2.6 Alertas

Lista de solo lectura, filtrada por defecto a **Activa**. Para dar seguimiento, pulse **Cambiar
estado** en la fila y elija **Revisada** (alguien la está atendiendo) o **Resuelta** (la causa se
corrigió). El cambio se refleja de inmediato en la pestaña correspondiente de la página pública de
alertas.

## 3. Preguntas frecuentes

**¿Por qué no puedo escribir la capacidad instalada o el CO₂?** Son valores calculados a partir de los
paneles y de la generación registrada; así nunca quedan desactualizados.

**¿Qué pasa si registro una generación con el 80 % exacto de la esperada?** Se genera alerta. La regla
es "al menos 20 % por debajo", y el 80 % exacto la cumple.

**¿Cómo cargo muchos registros de golpe?** Insértelos por base de datos o API y ejecute
`php artisan solar:evaluar-alertas` para reconstruir las alertas.

**¿Cuántos meses proyecta el sistema?** Tres, con base en los últimos doce registrados. Con menos de
tres meses de historia usa el promedio en lugar de la tendencia, y lo indica en la ficha.
