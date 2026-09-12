<x-layouts.publico titulo="Mapa">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[260px_1fr]">
        {{-- Filtro por departamento: colapsable en móvil con <details> nativo --}}
        <details class="panel lg:!block" open id="filtro-panel">
            <summary class="cursor-pointer list-none border-b border-rule px-4 py-3 lg:cursor-default">
                <span class="titulo">Departamentos</span>
                <span class="etiqueta mt-1 block" data-contador>{{ $departamentos->sum('granjas_count') }} granjas</span>
            </summary>
            <div id="filtro" class="max-h-[40vh] overflow-y-auto px-4 py-3 text-[13px] lg:max-h-none">
                <div class="mb-3 flex gap-2">
                    <button type="button" class="btn h-7 px-2 text-[12px]" data-todos>Todos</button>
                    <button type="button" class="btn h-7 px-2 text-[12px]" data-ninguno>Ninguno</button>
                </div>
                @foreach ($departamentos as $d)
                    <label class="flex h-7 items-center gap-2 {{ $d->granjas_count === 0 ? 'text-ink-3' : '' }}">
                        <input type="checkbox" value="{{ $d->id }}" checked class="accent-interactivo">
                        <span class="flex-1">{{ $d->nombre }}</span>
                        <span class="cifra text-[12px] text-ink-2">{{ $d->granjas_count ?: '' }}</span>
                    </label>
                @endforeach
            </div>
        </details>

        <div class="panel">
            <div data-mapa data-filtro="#filtro" class="relative h-[60vh] lg:h-[calc(100vh-140px)]" role="region" aria-label="Mapa de granjas solares de Guatemala"></div>
        </div>
    </div>
</x-layouts.publico>
