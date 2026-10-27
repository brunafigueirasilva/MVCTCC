@php
    $itens = collect($itens ?? []);
    $paleta = $paleta ?? ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#f472b6', '#60a5fa'];
    $maiorValor = $itens->isNotEmpty() ? (float) max($itens->pluck('valor')->map(fn ($v) => (float) $v)->all()) : 0;
    $maiorValor = $maiorValor > 0 ? $maiorValor : 1;
    $sufixo = $sufixo ?? '';
    $casas = $casas ?? 0;
@endphp
@if ($itens->isNotEmpty())
    <table class="chart-bar-table">
        <tbody>
            @foreach ($itens as $i => $item)
                @php $pct = max(2, round(((float) $item['valor'] / $maiorValor) * 100, 1)); @endphp
                <tr>
                    <td class="chart-bar-label">{{ $item['label'] }}</td>
                    <td class="chart-bar-cell">
                        <span class="chart-bar-track"><span class="chart-bar-fill" style="width: {{ $pct }}%; background: {{ $paleta[$i % count($paleta)] }};"></span></span>
                    </td>
                    <td class="chart-bar-valor numero">{{ number_format((float) $item['valor'], $casas, ',', '.') }}{{ $sufixo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="empty-msg">Sem dados suficientes para o gráfico.</div>
@endif
