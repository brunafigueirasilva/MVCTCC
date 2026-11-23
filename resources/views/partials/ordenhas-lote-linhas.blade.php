@php
    // Ícone + texto do turno (a cor nunca é a única pista).
    $iconesTurno = ['Manhã' => 'fa-sun', 'Tarde' => 'fa-cloud-sun', 'Noite' => 'fa-moon'];
@endphp
@foreach ($ordenhas as $ordenha)
    @php
        $hora = \Illuminate\Support\Str::substr($ordenha->horario, 0, 5);
        $descricao = $ordenha->data->format('d/m/Y') . ', ' . $ordenha->turno . ' (' . $hora . ')';
    @endphp
    <div class="ordenha-lote-item">
        <div class="ordenha-lote-info">
            <strong>{{ $ordenha->data->format('d/m/Y') }}</strong>
            <span class="ordenha-lote-hora">{{ $hora }}</span>
            <span class="ordenha-turno ordenha-turno-{{ \Illuminate\Support\Str::slug($ordenha->turno) }}">
                <i class="fa-solid {{ $iconesTurno[$ordenha->turno] ?? 'fa-clock' }}" aria-hidden="true"></i>{{ $ordenha->turno }}
            </span>
            <span class="ordenha-lote-litros">{{ number_format($ordenha->quantidade_litros, 1, ',', '.') }} L</span>
            @if ($ordenha->observacoes)
                <span class="ordenha-lote-obs">"{{ $ordenha->observacoes }}"</span>
            @endif
        </div>
        <div class="ordenha-lote-acoes">
            <button
                type="button"
                class="btn-acao-ordenha"
                title="Editar"
                aria-label="Editar ordenha de {{ $descricao }}"
                onclick="abrirModalOrdenhaLote(
                    {{ $lote->id }},
                    @js($lote->nome_lote),
                    {{ $ordenha->id }},
                    @js($ordenha->data->format('Y-m-d')),
                    @js($hora),
                    @js($ordenha->turno),
                    {{ $ordenha->quantidade_litros }},
                    @js($ordenha->observacoes)
                )"
            >
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
            </button>
            <form action="{{ route('lote.ordenha.delete', [$lote->id, $ordenha->id]) }}" method="POST"
                data-confirmar="Excluir este registro de ordenha?" data-confirmar-titulo="Excluir ordenha" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-acao-ordenha btn-acao-ordenha-excluir" title="Excluir" aria-label="Excluir ordenha de {{ $descricao }}">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
@endforeach
