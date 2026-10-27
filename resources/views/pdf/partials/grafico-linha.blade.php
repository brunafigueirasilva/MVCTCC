@php
    $labels = $labels ?? [];
    $valores = array_map('floatval', $valores ?? []);
    $cor = $cor ?? '#10b981';
    $unidade = $unidade ?? '';
    $largura = $largura ?? 480;
    $altura = $altura ?? 110;
    $padTop = 14;
    $padBottom = 20;
    $padLeft = 8;
    $padRight = 8;
    $n = count($valores);
    $max = $n ? max(max($valores), 0.0001) : 1;
    $areaW = $largura - $padLeft - $padRight;
    $areaH = $altura - $padTop - $padBottom;
    $passo = $n > 1 ? $areaW / ($n - 1) : 0;
    $pontos = [];
    foreach ($valores as $i => $v) {
        $x = $padLeft + ($n > 1 ? $i * $passo : $areaW / 2);
        $y = $padTop + $areaH - ($v / $max) * $areaH;
        $pontos[] = [$x, $y];
    }
    $pontosStr = collect($pontos)->map(fn ($p) => round($p[0], 1) . ',' . round($p[1], 1))->implode(' ');
    $maxRotulos = 6;
    $intervalo = $n > $maxRotulos ? (int) ceil($n / $maxRotulos) : 1;
@endphp
@if ($n > 0)
    <svg width="{{ $largura }}" height="{{ $altura }}" viewBox="0 0 {{ $largura }} {{ $altura }}">
        <line x1="{{ $padLeft }}" y1="{{ $padTop + $areaH }}" x2="{{ $largura - $padRight }}" y2="{{ $padTop + $areaH }}" stroke="#e5e7eb" stroke-width="1" />
        <text x="{{ $padLeft }}" y="10" font-size="8" fill="#6b7280">Máx: {{ number_format($max, 0, ',', '.') }}{{ $unidade }}</text>
        @if ($n > 1)
            <polyline points="{{ $pontosStr }}" fill="none" stroke="{{ $cor }}" stroke-width="2" />
        @endif
        @foreach ($pontos as $i => $p)
            <circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="2.3" fill="{{ $cor }}" />
            @if ($i % $intervalo === 0 || $i === $n - 1)
                <text x="{{ $p[0] }}" y="{{ $altura - 4 }}" font-size="7.5" fill="#6b7280" text-anchor="middle">{{ $labels[$i] ?? '' }}</text>
            @endif
        @endforeach
    </svg>
@else
    <div class="empty-msg">Sem dados suficientes para o gráfico.</div>
@endif
