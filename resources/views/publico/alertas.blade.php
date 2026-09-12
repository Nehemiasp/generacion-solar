@php use App\Support\Formato; @endphp
<x-layouts.publico titulo="Alertas">
    <section class="panel">
        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-rule px-4 py-3 sm:px-6">
            <div>
                <h1 class="titulo">Alertas de generación</h1>
                <p class="etiqueta mt-1">Se registra una alerta cuando la generación real de un período es igual o menor al 80% de la esperada.</p>
            </div>
            <nav class="flex gap-1" aria-label="Filtrar por estado">
                @foreach ([['activa', 'Activas'], ['revisada', 'Revisadas'], ['resuelta', 'Resueltas'], ['todas', 'Todas']] as [$valor, $texto])
                    <a class="chip" href="{{ route('alertas', ['estado' => $valor]) }}" aria-current="{{ $estado === $valor ? 'true' : 'false' }}">{{ $texto }}</a>
                @endforeach
            </nav>
        </div>

        @if ($alertas->isEmpty())
            <p class="vacio">
                @if ($estado === 'activa')
                    Ninguna granja está por debajo del 80% de su generación esperada en este período.
                @else
                    No hay alertas en estado {{ $estado }}. Cambiá el filtro para ver otras.
                @endif
            </p>
        @else
            <div class="tabla-scroll">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Granja</th>
                            <th>Departamento</th>
                            <th>Período</th>
                            <th class="num">Esperada</th>
                            <th class="num">Real</th>
                            <th class="num">Desviación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alertas as $a)
                            <tr>
                                <td><a href="{{ route('granjas.show', $a->granja_id) }}" class="no-underline hover:underline">{{ $a->granja->nombre }}</a></td>
                                <td class="text-ink-2">{{ $a->granja->departamento->nombre }}</td>
                                <td class="text-ink-2">{{ Formato::mes($a->periodo) }}</td>
                                <td class="num">{{ Formato::numero($a->generacion_esperada_kwh) }}<span class="unidad">kWh</span></td>
                                <td class="num">{{ Formato::numero($a->generacion_real_kwh) }}<span class="unidad">kWh</span></td>
                                <td class="num desv {{ $a->grave ? 'desv-grave' : 'desv-alerta' }}">{{ Formato::desviacion($a->porcentaje_desviacion) }}</td>
                                <td class="text-ink-2">{{ $a->estado->getLabel() }}{{ $a->grave ? ' · grave' : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-rule px-4 py-3 text-[13px] sm:px-6">{{ $alertas->links() }}</div>
        @endif
    </section>
</x-layouts.publico>
