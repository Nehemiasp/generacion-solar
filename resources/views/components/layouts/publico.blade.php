<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Generación solar' }} · Guatemala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@75..100,400..600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ground text-ink font-sans">
    <header class="sticky top-0 z-[1100] border-b border-rule-strong bg-surface">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center px-4 sm:h-14 sm:flex-nowrap sm:gap-6 sm:px-6">
            <a href="{{ route('inicio') }}" class="h-14 shrink-0 whitespace-nowrap text-[15px] font-semibold leading-[56px] text-ink no-underline">Generación solar · Guatemala</a>

            {{-- Hamburguesa: solo en móvil --}}
            <button type="button" data-menu aria-expanded="false" aria-controls="nav-principal" aria-label="Abrir menú"
                    class="ml-auto flex h-9 w-9 items-center justify-center rounded-control border border-rule-strong text-ink sm:hidden">
                <svg data-icono-abrir width="18" height="18" viewBox="0 0 18 18" aria-hidden="true"><path d="M2 4h14M2 9h14M2 14h14" stroke="currentColor" stroke-width="1.5"/></svg>
                <svg data-icono-cerrar width="18" height="18" viewBox="0 0 18 18" aria-hidden="true" hidden><path d="M4 4l10 10M14 4L4 14" stroke="currentColor" stroke-width="1.5"/></svg>
            </button>

            <nav id="nav-principal" data-nav aria-label="Principal"
                 class="hidden w-full flex-col gap-1 border-t border-rule py-2 text-[14px] sm:ml-auto sm:flex sm:w-auto sm:flex-row sm:items-center sm:border-0 sm:py-0 sm:text-[13px]">
                @foreach ([['inicio', 'Tablero'], ['mapa', 'Mapa'], ['reporte', 'Reporte'], ['alertas', 'Alertas']] as [$ruta, $nombre])
                    <a href="{{ route($ruta) }}"
                       class="shrink-0 px-3 py-2 no-underline rounded-control sm:py-1.5 {{ request()->routeIs($ruta) ? 'bg-ground text-ink font-medium' : 'text-ink-2 hover:text-ink' }}"
                       @if(request()->routeIs($ruta)) aria-current="page" @endif>{{ $nombre }}</a>
                @endforeach
                <a href="/docs/api" class="shrink-0 px-3 py-2 text-ink-2 no-underline hover:text-ink sm:py-1.5">API</a>
                <a href="/admin" class="mt-1 shrink-0 px-3 py-2 text-interactivo no-underline border border-rule-strong rounded-control hover:bg-ground sm:ml-2 sm:mt-0 sm:py-1.5">Administración</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
        @php
            // Volver: usa el historial del navegador si lo hay; si se entró directo, va al tablero.
            $anterior = url()->previous();
            $volverA = $anterior !== url()->current() ? $anterior : route('inicio');
        @endphp
        @unless (request()->routeIs('inicio'))
            <a href="{{ $volverA }}" data-volver class="btn mb-4 inline-flex items-center gap-2 no-underline">
                <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M7.5 2 3.5 6l4 4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
                Volver
            </a>
        @endunless
        {{ $slot }}
    </main>

    <footer class="mx-auto max-w-7xl px-4 py-6 text-[12px] text-ink-3 sm:px-6">
        Sistema de registro y monitoreo de generación solar por departamento. Datos de demostración. CO₂ evitado calculado con {{ config('solar.factor_co2_kg_por_kwh') }} kg por kWh.
    </footer>
</body>
</html>
