@php use App\Support\Formato; use App\Services\EstadisticasService; @endphp
<x-layouts.publico titulo="Reporte por departamento">
    @php
        $col = fn (string $campo, string $texto, bool $num = true) => sprintf(
            '<th class="%s"><a href="%s" %s>%s</a></th>',
            $num ? 'num' : '',
            route('reporte', array_filter(['desde' => $desde, 'hasta' => $hasta, 'orden' => $campo, 'dir' => $orden === $campo && $dir === 'desc' ? 'asc' : 'desc'])),
            $orden === $campo ? 'aria-sort="'.($dir === 'asc' ? 'ascending' : 'descending').'"' : '',
            $texto,
        );
    @endphp
    <section class="panel">
        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-rule px-4 py-3 sm:px-6">
            <div>
                <h1 class="titulo">Reporte por departamento</h1>
                <p class="etiqueta mt-1">Los 22 departamentos. El rango de fechas afecta las columnas de generación y CO₂.</p>
            </div>
            <form method="get" class="flex flex-wrap items-end gap-2">
                <input type="hidden" name="orden" value="{{ $orden }}"><input type="hidden" name="dir" value="{{ $dir }}">
                <label class="etiqueta">Desde<br><input class="campo mt-1" type="month" name="desde" value="{{ $desde }}"></label>
                <label class="etiqueta">Hasta<br><input class="campo mt-1" type="month" name="hasta" value="{{ $hasta }}"></label>
                <button class="btn btn-primario" type="submit">Aplicar</button>
                @if ($desde || $hasta)<a class="btn inline-flex items-center no-underline" href="{{ route('reporte') }}">Quitar rango</a>@endif
            </form>
        </div>
        @error('hasta')<p class="px-4 py-2 text-[13px] text-alerta sm:px-6">{{ $message }}</p>@enderror
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr>
                        {!! $col('nombre', 'Departamento', false) !!}
                        {!! $col('granjas', 'Granjas') !!}
                        {!! $col('paneles', 'Paneles') !!}
                        {!! $col('capacidad_instalada_kw', 'kW instalados') !!}
                        {!! $col('generacion_kwh', 'Generación') !!}
                        <th class="num">vs. esperada</th>
                        {!! $col('familias_beneficiadas', 'Familias') !!}
                        {!! $col('co2_evitado_kg', 'CO₂ evitado') !!}
                        {!! $col('alertas_activas', 'Alertas') !!}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filas as $d)
                        @php
                            [$g, $gu] = Formato::energia($d['generacion_kwh']);
                            $q = $d['generacion_kwh'] > 0 ? EstadisticasService::quintil($d['generacion_kwh'], $cortes) : 0;
                            $desv = $d['esperada_kwh'] > 0 ? ($d['generacion_kwh'] - $d['esperada_kwh']) / $d['esperada_kwh'] * 100 : null;
                        @endphp
                        @php $sinGranjas = $d['granjas'] === 0; @endphp
                        <tr class="{{ $sinGranjas ? 'text-ink-3' : '' }}">
                            <td>@if($q)<span class="sw q{{ $q }}"></span>@else<span class="sw" style="background:transparent"></span>@endif{{ $d['nombre'] }}</td>
                            <td class="num">{{ $sinGranjas ? '—' : Formato::numero($d['granjas']) }}</td>
                            <td class="num">{{ $sinGranjas ? '—' : Formato::numero($d['paneles']) }}</td>
                            <td class="num">{{ $sinGranjas ? '—' : Formato::numero($d['capacidad_instalada_kw']) }}</td>
                            <td class="num">@if($sinGranjas)—@else{{ $g }}<span class="unidad">{{ $gu }}</span>@endif</td>
                            <td class="num desv {{ $desv !== null && $desv <= -20 ? 'desv-alerta' : '' }}">{{ Formato::desviacion($desv) }}</td>
                            <td class="num">{{ $sinGranjas ? '—' : Formato::numero($d['familias_beneficiadas']) }}</td>
                            <td class="num">@if($sinGranjas)—@else{{ Formato::numero($d['co2_evitado_kg']) }}<span class="unidad">kg</span> <span class="text-ink-3">{{ Formato::co2Toneladas($d['co2_evitado_kg']) }} t</span>@endif</td>
                            <td class="num {{ $d['alertas_activas'] > 0 ? 'desv-alerta' : '' }}">{{ $d['alertas_activas'] ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="vacio">No hay departamentos registrados. Ejecutá el seeder de departamentos.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    @php [$tg, $tgu] = Formato::energia($totales['generacion_kwh']); @endphp
                    <tr>
                        <td>Total nacional</td>
                        <td class="num">{{ Formato::numero($totales['granjas']) }}</td>
                        <td class="num">{{ Formato::numero($totales['paneles']) }}</td>
                        <td class="num">{{ Formato::numero($totales['capacidad_instalada_kw']) }}</td>
                        <td class="num">{{ $tg }}<span class="unidad">{{ $tgu }}</span></td>
                        <td></td>
                        <td class="num">{{ Formato::numero($totales['familias_beneficiadas']) }}</td>
                        <td class="num">{{ Formato::numero($totales['co2_evitado_kg']) }}<span class="unidad">kg</span> <span class="text-ink-3">{{ Formato::co2Toneladas($totales['co2_evitado_kg']) }} t</span></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</x-layouts.publico>
