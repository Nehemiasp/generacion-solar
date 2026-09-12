@props(['valores', 'ancho' => 64, 'alto' => 20])
@php
    $v = array_values(array_map('floatval', $valores));
    $n = count($v);
    $max = $n ? max($v) : 0;
    $min = $n ? min($v) : 0;
    $rango = $max - $min ?: 1;
    $puntos = [];
    foreach ($v as $i => $y) {
        $x = $n > 1 ? $i * ($ancho - 2) / ($n - 1) + 1 : $ancho / 2;
        $puntos[] = round($x, 1).','.round(($alto - 2) - ($y - $min) / $rango * ($alto - 4) + 1, 1);
    }
@endphp
@if($n > 1)
<svg width="{{ $ancho }}" height="{{ $alto }}" viewBox="0 0 {{ $ancho }} {{ $alto }}" role="img" aria-label="Últimos {{ $n }} meses">
    <polyline points="{{ implode(' ', $puntos) }}" fill="none" stroke="#5A6568" stroke-width="1" />
</svg>
@else
<span class="text-ink-3">—</span>
@endif
