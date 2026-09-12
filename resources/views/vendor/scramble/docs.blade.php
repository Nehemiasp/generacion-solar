<!doctype html>
<html lang="en" data-theme="{{ $config->renderer()->get('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="{{ $config->renderer()->get('theme', 'light') }}">
    <title>{{ $config->get('ui.title') ?? config('app.name') . ' - API Docs' }}</title>

    <script src="https://unpkg.com/@stoplight/elements@8.4.2/web-components.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements@8.4.2/styles.min.css">

    @include('scramble::dev-tools', ['renderer' => 'elements'])

    <script>
        const originalFetch = window.fetch;

        // intercept TryIt requests and add the XSRF-TOKEN header,
        // which is necessary for Sanctum cookie-based authentication to work correctly
        window.fetch = (url, options) => {
            const CSRF_TOKEN_COOKIE_KEY = "XSRF-TOKEN";
            const CSRF_TOKEN_HEADER_KEY = "X-XSRF-TOKEN";
            const getCookieValue = (key) => {
                const cookie = document.cookie.split(';').find((cookie) => cookie.trim().startsWith(key));
                return cookie?.split("=")[1];
            };

            const updateFetchHeaders = (
                headers,
                headerKey,
                headerValue,
            ) => {
                if (headers instanceof Headers) {
                    headers.set(headerKey, headerValue);
                } else if (Array.isArray(headers)) {
                    headers.push([headerKey, headerValue]);
                } else if (headers) {
                    headers[headerKey] = headerValue;
                }
            };
            const csrfToken = getCookieValue(CSRF_TOKEN_COOKIE_KEY);
            if (csrfToken) {
                const { headers = new Headers() } = options || {};
                updateFetchHeaders(headers, CSRF_TOKEN_HEADER_KEY, decodeURIComponent(csrfToken));
                return originalFetch(url, {
                    ...options,
                    headers,
                });
            }

            return originalFetch(url, options);
        };
    </script>

    <style>
        html, body { margin:0; height:100%; }
        body { background-color: var(--color-canvas); }
        /* issues about the dark theme of stoplight/mosaic-code-viewer using web component:
         * https://github.com/stoplightio/elements/issues/2188#issuecomment-1485461965
         */

        /* ------------------------------------------------------------------
         * Botón "Export" y su menú, alineados con docs/DISENO.md.
         * Stoplight Elements se carga desde CDN, así que solo se puede
         * intervenir por CSS: se apunta al atributo aria-label del disparador
         * y al rol del menú, no a las clases utilitarias de Mosaic, que
         * cambian entre versiones del paquete.
         * ------------------------------------------------------------------ */
        :root {
            --solar-interactivo: #0F5C63;
            --solar-interactivo-claro: rgba(15, 92, 99, .10);
            --solar-rule: #DCE0DE;
            --solar-ink: #1B2426;
            --solar-ground: #F5F6F4;
            --solar-surface: #FFFFFF;
        }

        button[aria-label="Export"] {
            height: 28px;
            padding: 0 10px;
            border: 1px solid var(--solar-rule);
            border-radius: 3px;
            background-color: var(--solar-surface);
            color: var(--solar-ink);
            font-size: 13px;
            font-weight: 500;
            box-shadow: none;
            transition: background-color .12s linear, border-color .12s linear, color .12s linear;
        }

        button[aria-label="Export"]:hover {
            background-color: var(--solar-ground);
            border-color: var(--solar-interactivo);
            color: var(--solar-interactivo);
        }

        /* Reemplaza el anillo azul de Mosaic por el color interactivo del sistema. */
        button[aria-label="Export"].sl-focus-ring,
        button[aria-label="Export"]:focus-visible {
            box-shadow: 0 0 0 3px var(--solar-interactivo-claro);
            border-color: var(--solar-interactivo);
            outline: none;
        }

        /* Estado abierto: el botón queda relleno, así se ve de dónde salió el menú. */
        button[aria-label="Export"][aria-expanded="true"],
        button[aria-label="Export"][aria-expanded="true"]:hover {
            background-color: var(--solar-interactivo);
            border-color: var(--solar-interactivo);
            color: var(--solar-surface);
        }

        button[aria-label="Export"] .sl-icon {
            transition: transform .12s linear;
        }

        button[aria-label="Export"][aria-expanded="true"] .sl-icon {
            transform: rotate(180deg);
        }

        /* Menú desplegable: misma densidad y mismos bordes que las tablas del tablero. */
        .sl-menu[role="menu"] {
            padding: 4px 0;
            border: 1px solid var(--solar-rule);
            border-radius: 3px;
            background-color: var(--solar-surface);
            /* Única sombra permitida por el sistema de diseño, la misma del popup del mapa. */
            box-shadow: 0 2px 8px rgba(27, 36, 38, .16);
        }

        /* Sin colita triangular, igual que los popups del mapa. */
        .sl-popover__tip {
            display: none;
        }

        .sl-menu[role="menu"] .sl-menu-item {
            min-height: 30px;
            padding: 0 12px;
            font-size: 13px;
            color: var(--solar-ink);
        }

        /* Mosaic pinta el resaltado con --color-primary (azul); aquí manda el teal. */
        .sl-menu--pointer-interactions .sl-menu-item:not(.sl-menu-item--disabled):hover,
        .sl-menu[role="menu"] .sl-menu-item--focused {
            background-color: var(--solar-interactivo);
            color: var(--solar-surface);
        }

        [data-theme="dark"] .token.property {
            color: rgb(128, 203, 196) !important;
        }
        [data-theme="dark"] .token.operator {
            color: rgb(255, 123, 114) !important;
        }
        [data-theme="dark"] .token.number {
            color: rgb(247, 140, 108) !important;
        }
        [data-theme="dark"] .token.string {
            color: rgb(165, 214, 255) !important;
        }
        [data-theme="dark"] .token.boolean {
            color: rgb(121, 192, 255) !important;
        }
        [data-theme="dark"] .token.punctuation {
            color: #dbdbdb !important;
        }
    </style>
</head>
<body style="height: 100vh; overflow-y: hidden">
<elements-api
    id="docs"
    @foreach($config->renderer()->all(except: ['theme']) as $key => $value)
        @continue(! $value)
        {{ $key }}="{{ $value === true ? 'true' : ($value === false ? 'false' : $value) }}"
    @endforeach
/>
<script>
    (async () => {
        const docs = document.getElementById('docs');
        docs.apiDescriptionDocument = @json($spec);
    })();
</script>

@if($config->renderer()->get('theme', 'light') === 'system')
    <script>
        var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        function updateTheme(e) {
            if (e.matches) {
                window.document.documentElement.setAttribute('data-theme', 'dark');
                window.document.getElementsByName('color-scheme')[0].setAttribute('content', 'dark');
            } else {
                window.document.documentElement.setAttribute('data-theme', 'light');
                window.document.getElementsByName('color-scheme')[0].setAttribute('content', 'light');
            }
        }

        mediaQuery.addEventListener('change', updateTheme);
        updateTheme(mediaQuery);
    </script>
@endif
</body>
</html>
