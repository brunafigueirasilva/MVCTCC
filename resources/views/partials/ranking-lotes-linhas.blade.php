@foreach ($ranking as $item)
<tr>
    <td>
        @if ($item->posicao === 1)
            <span class="ranking-medalha ouro"><i class="fa-solid fa-medal" aria-hidden="true"></i> 1º</span>
        @elseif ($item->posicao === 2)
            <span class="ranking-medalha prata"><i class="fa-solid fa-medal" aria-hidden="true"></i> 2º</span>
        @elseif ($item->posicao === 3)
            <span class="ranking-medalha bronze"><i class="fa-solid fa-medal" aria-hidden="true"></i> 3º</span>
        @else
            {{ $item->posicao }}º
        @endif
    </td>
    <td><strong>{{ $item->nome_lote }}</strong></td>
    <td><span class="categoria-badge">{{ $item->tipo_animal }}</span></td>
    <td>{{ number_format($item->consumo_medio_dia, 1, ',', '.') }} kg</td>
    <td>R$ {{ number_format($item->custo_medio_dia, 2, ',', '.') }}</td>
    <td><strong>{{ $item->eficiencia }}%</strong></td>
    <td>
        @if ($item->variacao === null)
            <span class="trend-neutral"><i class="fa-solid fa-minus" aria-hidden="true"></i> Novo</span>
        @elseif ($item->variacao == 0)
            <span class="trend-neutral"><i class="fa-solid fa-minus" aria-hidden="true"></i> 0%</span>
        @elseif ($item->variacao > 0)
            <span class="trend-up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> {{ number_format($item->variacao, 1, ',', '.') }}%</span>
        @else
            <span class="trend-down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i> {{ number_format(abs($item->variacao), 1, ',', '.') }}%</span>
        @endif
    </td>
</tr>
@endforeach
