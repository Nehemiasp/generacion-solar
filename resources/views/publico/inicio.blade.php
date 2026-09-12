@php use App\Support\Formato; use App\Services\EstadisticasService; @endphp
<x-layouts.publico titulo="Tablero">
    {{-- Banda de irradiación: 12 meses de generación nacional, coloreados con la escala, con la esperada superpuesta --}}
    <section class="panel" aria-labelledby="t-banda">
        <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-rule px-4 py-3 sm:px-6">
            <h1 id="t-banda" class="titulo">Generación nacional, últimos 12 meses</h1>
            <p class="etiqueta">Columnas: generación real por mes. Línea punteada: generación esperada.</p>
        </div>
        <div class="h-[180px] px-2 py-3 sm:px-4">
            <canvas data-banda data-serie='@json($serie)' aria-label="Generación real y esperada por mes"></canvas>
        </div>
    </section>

    {{-- Totales nacionales: una sola fila dividida por líneas verticales --}}
    <section class="panel mt-4 grid grid-cols-2 divide-y divide-rule sm:divide-y-0 md:grid-cols-5 md:divide-x" aria-label="Totales nacionales">
        @php
            [$gen, $genU] = Formato::energia($nacional['generacion_acumulada_kwh']);
            [$cap, $capU] = Formato::potencia($nacional['capacidad_instalada_kw']);
            $totales = [
                [Formato::numero($nacional['granjas']), null, 'granjas activas'],
                [Formato::numero($nacional['paneles']), null, 'paneles instalados'],
                [$cap, $capU, 'capacidad instalada'],
                [$gen, $genU, 'generación acumulada'],
                [Formato::co2Toneladas($nacional['co2_evitado_kg']), 't', 'CO₂ evitado'],
            ];
        @endphp
        @foreach ($totales as [$valor, $unidad, $etiqueta])
            <div class="px-4 py-4 sm:px-6 {{ $loop->last ? 'col-span-2 md:col-span-1 border-t border-rule md:border-t-0' : '' }}">
                <div class="cifra-xl">{{ $valor }}@if($unidad)<span class="unidad">{{ $unidad }}</span>@endif</div>
                <div class="etiqueta mt-1">{{ $etiqueta }}</div>
            </div>
        @endforeach
    </section>

    {{-- Mapa 65% + alertas 35% --}}
    <section class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[65fr_35fr]">
        <div class="panel">
            <div class="flex items-baseline justify-between border-b border-rule px-4 py-3 sm:px-6">
                <h2 class="titulo">Granjas activas</h2>
                <a href="{{ route('mapa') }}" class="text-[13px]">Mapa completo con filtros</a>
            </div>
            <div data-mapa data-rueda="no" class="relative h-[60vh] lg:h-[460px]" role="region" aria-label="Mapa de granjas solares"></div>
        </div>

        <div class="panel">
            <div class="flex items-baseline justify-between border-b border-rule px-4 py-3 sm:px-6">
                <h2 class="titulo">Alertas activas</h2>
                <a href="{{ route('alertas') }}" class="text-[13px]">{{ Formato::numero($nacional['alertas_activas']) }} en total</a>
            </div>
            @if ($alertas->isEmpty())
                <p class="vacio">Ninguna granja está por debajo del 80% de su generación esperada en este período.</p>
            @else
                <div class="tabla-scroll">
                    <table class="tabla">
                        <thead>
                            <tr><th>Granja</th><th>Período</th><th class="num">Real</th><th class="num">Desviación</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($alertas as $a)
                                <tr>
                                    <td>
                                        <a href="{{ route('granjas.show', $a->granja_id) }}" class="no-underline hover:underline">{{ $a->granja->nombre }}</a>
                                        <span class="block text-[12px] text-ink-2">{{ $a->granja->departamento->nombre }}</span>
                                    </td>
                                    <td class="text-ink-2">{{ Formato::mes($a->periodo) }}</td>
                                    <td class="num">{{ Formato::numero($a->generacion_real_kwh) }}<span class="unidad">kWh</span></td>
                                    <td class="num desv {{ $a->grave ? 'desv-grave' : 'desv-alerta' }}">{{ Formato::desviacion($a->porcentaje_desviacion) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    {{-- Reporte por departamento con sparkline --}}
    <section class="panel mt-4" aria-labelledby="t-deptos">
        <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-rule px-4 py-3 sm:px-6">
            <h2 id="t-deptos" class="titulo">Por departamento</h2>
            <a href="{{ route('reporte') }}" class="text-[13px]">Reporte con rango de fechas y orden</a>
        </div>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th class="num">Granjas</th>
                        <th class="num">Paneles</th>
                        <th class="num">kW instalados</th>
                        <th class="num">Generación</th>
                        <th class="num">Familias</th>
                        <th class="num">CO₂ evitado</th>
                        <th class="num">Alertas</th>
                        <th>12 meses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departamentos->sortByDesc('generacion_kwh') as $d)
                        @php [$g, $gu] = Formato::energia($d['generacion_kwh']); $q = $d['generacion_kwh'] > 0 ? EstadisticasService::quintil($d['generacion_kwh'], $cortesDeptos) : 0; @endphp
                        <tr class="{{ $d['granjas'] === 0 ? 'text-ink-3' : '' }}">
                            <td>@if($q)<span class="sw q{{ $q }}"></span>@else<span class="sw" style="background:transparent"></span>@endif{{ $d['nombre'] }}</td>
                            <td class="num">{{ Formato::numero($d['granjas']) }}</td>
                            <td class="num">{{ Formato::numero($d['paneles']) }}</td>
                            <td class="num">{{ Formato::numero($d['capacidad_instalada_kw']) }}</td>
                            <td class="num">{{ $g }}<span class="unidad">{{ $gu }}</span></td>
                            <td class="num">{{ Formato::numero($d['familias_beneficiadas']) }}</td>
                            <td class="num">{{ Formato::numero($d['co2_evitado_kg']) }}<span class="unidad">kg</span> <span class="text-ink-3">{{ Formato::co2Toneladas($d['co2_evitado_kg']) }} t</span></td>
                            <td class="num {{ $d['alertas_activas'] > 0 ? 'desv-alerta' : '' }}">{{ $d['alertas_activas'] ?: '—' }}</td>
                            <td><x-sparkline :valores="$series[$d['id']] ?? []" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.publico>
