@php use App\Support\Formato; use App\Services\ProyeccionService; @endphp
<x-layouts.publico :titulo="$granja->nombre">
    @php
        [$cap, $capU] = Formato::potencia($granja->capacidad_instalada_kw);
        [$gen, $genU] = Formato::energia($granja->generacion_acumulada_kwh);
        $historico = $generaciones->map(fn ($g) => [
            'periodo' => $g->periodo->toDateString(),
            'real_kwh' => $g->generacion_real_kwh,
            'esperada_kwh' => $g->generacion_esperada_kwh,
        ]);
    @endphp

    <div class="panel">
        <div class="border-b border-rule px-4 py-4 sm:px-6">
            <nav class="etiqueta mb-2"><a href="{{ route('mapa') }}">Mapa</a> <span class="text-ink-3">/</span> {{ $granja->departamento->nombre }}</nav>
            <h1 class="text-[28px] font-semibold leading-tight">{{ $granja->nombre }}</h1>
            <p class="mt-1 text-[13px] text-ink-2">
                {{ $granja->municipio ? $granja->municipio.', ' : '' }}{{ $granja->departamento->nombre }}.
                Coordenadas {{ number_format($granja->latitud, 5) }}, {{ number_format($granja->longitud, 5) }}.
                @if ($granja->fecha_instalacion) En operación desde {{ Formato::mes($granja->fecha_instalacion) }}. @endif
                @unless ($granja->activa) <span class="desv-alerta">Granja desactivada.</span> @endunless
            </p>
            @if ($granja->alertasActivas->isNotEmpty())
                @php $peor = $granja->alertasActivas->sortBy('porcentaje_desviacion')->first(); @endphp
                <p class="mt-3 border-l-2 border-alerta pl-3 text-[13px] {{ $peor->grave ? 'desv-grave' : 'desv-alerta' }}">
                    {{ $granja->alertasActivas->count() }} {{ $granja->alertasActivas->count() === 1 ? 'alerta activa' : 'alertas activas' }}.
                    La peor es {{ Formato::mes($peor->periodo) }} con {{ Formato::desviacion($peor->porcentaje_desviacion) }} respecto a lo esperado.
                </p>
            @endif
        </div>

        <div class="grid grid-cols-2 divide-rule md:grid-cols-5 md:divide-x">
            @foreach ([
                [$cap, $capU, 'capacidad instalada'],
                [Formato::numero($granja->total_paneles), null, 'paneles'],
                [$gen, $genU, 'generación acumulada'],
                [Formato::numero($granja->familias_beneficiadas), null, 'familias'],
                [Formato::co2Toneladas($granja->co2_evitado_kg), 't', 'CO₂ evitado'],
            ] as [$valor, $unidad, $etiqueta])
                <div class="border-t border-rule px-4 py-4 sm:px-6 md:border-t-0 {{ $loop->last ? 'col-span-2 md:col-span-1' : '' }}">
                    <div class="cifra-md">{{ $valor }}@if($unidad)<span class="unidad">{{ $unidad }}</span>@endif</div>
                    <div class="etiqueta mt-1">{{ $etiqueta }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- RF-15: histórico + proyección --}}
    <section class="panel mt-4" aria-labelledby="t-proy">
        <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-rule px-4 py-3 sm:px-6">
            <h2 id="t-proy" class="titulo">Generación histórica y proyección</h2>
            <p class="etiqueta">
                @if ($proyeccion['metodo'] === ProyeccionService::METODO_REGRESION)
                    Regresión lineal por mínimos cuadrados sobre {{ $proyeccion['periodos_usados'] }} períodos. R² {{ number_format($proyeccion['r2'], 3) }}.
                @elseif ($proyeccion['metodo'] === ProyeccionService::METODO_PROMEDIO)
                    Promedio de los períodos disponibles.
                @endif
            </p>
        </div>

        @if ($generaciones->isEmpty())
            <p class="vacio">Esta granja no tiene períodos de generación registrados. Registrá al menos uno desde el panel de administración.</p>
        @else
            <div class="h-[260px] px-2 py-3 sm:px-4">
                <canvas data-grafica-granja
                        data-historico='@json($historico)'
                        data-proyeccion='@json($proyeccion['proyecciones'])'
                        aria-label="Generación real, esperada y proyectada"></canvas>
            </div>

            @if ($proyeccion['mensaje'])
                <p class="border-t border-rule px-4 py-3 text-[13px] text-ink-2 sm:px-6">{{ $proyeccion['mensaje'] }}</p>
            @endif

            <div class="grid grid-cols-1 divide-y divide-rule border-t border-rule md:grid-cols-2 md:divide-x md:divide-y-0">
                <div class="px-4 py-4 sm:px-6">
                    <h3 class="etiqueta mb-2">Próximos meses proyectados</h3>
                    <table class="tabla border border-rule">
                        <thead><tr><th>Período</th><th class="num">Proyectado</th></tr></thead>
                        <tbody>
                            @foreach ($proyeccion['proyecciones'] as $p)
                                <tr><td>{{ Formato::mes($p['periodo']) }}</td><td class="num">{{ Formato::numero($p['kwh']) }}<span class="unidad">kWh</span></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($proyeccion['metodo'] === ProyeccionService::METODO_REGRESION)
                        <p class="mt-2 text-[12px] text-ink-3">
                            Pendiente {{ Formato::numero($proyeccion['pendiente'], 1) }} kWh por mes. Intercepto {{ Formato::numero($proyeccion['intercepto'], 1) }} kWh.
                        </p>
                    @endif
                </div>

                <div class="px-4 py-4 sm:px-6">
                    <h3 class="etiqueta mb-2">Precisión histórica del modelo</h3>
                    @if (empty($comparacion['comparaciones']))
                        <p class="text-[13px] text-ink-2">Se necesitan al menos 4 períodos registrados para comparar la proyección con el resultado real. Esta granja tiene {{ $generaciones->count() }}.</p>
                    @else
                        <div class="tabla-scroll max-h-[220px] overflow-y-auto">
                            <table class="tabla border border-rule">
                                <thead><tr><th>Período</th><th class="num">Proyectado</th><th class="num">Real</th><th class="num">Error</th></tr></thead>
                                <tbody>
                                    @foreach (array_reverse($comparacion['comparaciones']) as $c)
                                        <tr>
                                            <td>{{ Formato::mes($c['periodo']) }}</td>
                                            <td class="num">{{ Formato::numero($c['proyectado_kwh']) }}</td>
                                            <td class="num">{{ Formato::numero($c['real_kwh']) }}</td>
                                            <td class="num desv {{ abs($c['error_pct'] ?? 0) > 20 ? 'desv-alerta' : '' }}">{{ Formato::desviacion($c['error_pct']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-2 text-[12px] text-ink-3">Error absoluto medio {{ Formato::numero($comparacion['error_absoluto_medio_pct'], 1) }}% sobre {{ count($comparacion['comparaciones']) }} períodos.</p>
                    @endif
                </div>
            </div>
        @endif
    </section>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- RF-05 / RF-06 --}}
        <section class="panel">
            <h2 class="titulo border-b border-rule px-4 py-3 sm:px-6">Paneles instalados</h2>
            @if ($granja->paneles->isEmpty())
                <p class="vacio">Esta granja no tiene paneles asociados. Agregá modelos y cantidades desde el panel de administración.</p>
            @else
                <div class="tabla-scroll">
                    <table class="tabla">
                        <thead><tr><th>Marca</th><th>Modelo</th><th class="num">Potencia</th><th class="num">Cantidad</th><th class="num">Capacidad</th></tr></thead>
                        <tbody>
                            @foreach ($granja->paneles as $p)
                                <tr>
                                    <td>{{ $p->modeloPanel->marca }}</td>
                                    <td class="text-ink-2">{{ $p->modeloPanel->modelo }}</td>
                                    <td class="num">{{ Formato::numero($p->modeloPanel->potencia_kw, 3) }}<span class="unidad">kW</span></td>
                                    <td class="num">{{ Formato::numero($p->cantidad) }}</td>
                                    <td class="num">{{ Formato::numero($p->capacidad_kw, 1) }}<span class="unidad">kW</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Total</td>
                                <td class="num">{{ Formato::numero($granja->total_paneles) }}</td>
                                <td class="num">{{ Formato::numero($granja->capacidad_instalada_kw, 1) }}<span class="unidad">kW</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>

        {{-- RF-08 / RF-09 --}}
        <section class="panel">
            <h2 class="titulo border-b border-rule px-4 py-3 sm:px-6">Generación por período</h2>
            @if ($generaciones->isEmpty())
                <p class="vacio">Sin períodos registrados.</p>
            @else
                <div class="tabla-scroll max-h-[420px] overflow-y-auto">
                    <table class="tabla">
                        <thead><tr><th>Período</th><th class="num">Esperada</th><th class="num">Real</th><th class="num">Desviación</th></tr></thead>
                        <tbody>
                            @foreach ($generaciones->sortByDesc('periodo') as $g)
                                <tr>
                                    <td>{{ Formato::mes($g->periodo) }}</td>
                                    <td class="num">{{ Formato::numero($g->generacion_esperada_kwh) }}</td>
                                    <td class="num">{{ Formato::numero($g->generacion_real_kwh) }}</td>
                                    <td class="num desv {{ $g->bajo_desempeno ? 'desv-alerta' : '' }}">{{ Formato::desviacion($g->porcentaje_desviacion) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.publico>
