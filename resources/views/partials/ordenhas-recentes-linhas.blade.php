@foreach ($ordenhas as $ordenha)
<tr>
    <td>{{ $ordenha->data->format('d/m/Y') }}</td>
    <td>{{ \Illuminate\Support\Str::substr($ordenha->horario, 0, 5) }}</td>
    <td><span class="categoria-badge">{{ $ordenha->turno }}</span></td>
    <td><strong>{{ $ordenha->animal->nome ?? '—' }}</strong></td>
    <td>{{ number_format($ordenha->quantidade_litros, 1, ',', '.') }} L</td>
</tr>
@endforeach
