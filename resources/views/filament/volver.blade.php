{{-- Enlace "Volver" en todas las páginas del panel, salvo el escritorio --}}
@unless (request()->routeIs('filament.admin.pages.dashboard'))
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/admin') }}"
       onclick="if (history.length > 1) { event.preventDefault(); history.back(); }"
       class="fi-btn fi-btn-size-sm fi-btn-color-gray fi-color-gray mb-4 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-950/10 hover:bg-gray-50 dark:text-gray-200 dark:ring-white/20 dark:hover:bg-white/5">
        <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M7.5 2 3.5 6l4 4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
        Volver
    </a>
@endunless
