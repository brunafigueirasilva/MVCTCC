@php
    $percentual = max(0, min(100, (float) $valor));
    $cor = $percentual >= 80 ? '#16a34a' : ($percentual >= 50 ? '#d97706' : '#dc2626');
    $rotulo = $label ?? number_format((float) $valor, 1, ',', '.');
@endphp
<span class="bar-track"><span class="bar-fill" style="width: {{ $percentual }}%; background: {{ $cor }};"></span></span>
<span class="bar-value">{{ $rotulo }}%</span>
