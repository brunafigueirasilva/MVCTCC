@php
    // Selo "3D" do ícone de animal (gradiente diagonal + glow) — mesmo
    // padrão do resto do site (dashboard, cards de KPI etc.), com um par
    // claro/escuro por tipo de animal em vez de uma cor sólida.
    $iconesAnimalLote = [
        'Bovino' => ['icone' => 'fa-cow', 'claro' => '#4ade80', 'escuro' => '#059669', 'glow' => 'rgba(5, 150, 105, 0.5)'],
        'Suíno' => ['icone' => 'fa-piggy-bank', 'claro' => '#f472b6', 'escuro' => '#db2777', 'glow' => 'rgba(219, 39, 119, 0.45)'],
        'Equino' => ['icone' => 'fa-horse', 'claro' => '#fbbf24', 'escuro' => '#b45309', 'glow' => 'rgba(180, 83, 9, 0.45)'],
        'Avicultura' => ['icone' => 'fa-dove', 'claro' => '#60a5fa', 'escuro' => '#0ea5e9', 'glow' => 'rgba(14, 165, 233, 0.45)'],
        'Ovino' => ['icone' => 'fa-paw', 'claro' => '#c084fc', 'escuro' => '#7c3aed', 'glow' => 'rgba(124, 58, 237, 0.45)'],
        'Caprino' => ['icone' => 'fa-paw', 'claro' => '#fb923c', 'escuro' => '#f97316', 'glow' => 'rgba(234, 88, 12, 0.45)'],
    ];
@endphp
@foreach ($lotesDashboard as $lote)
    @php
        $animalIconeLote = $iconesAnimalLote[$lote->tipo_animal] ?? ['icone' => 'fa-paw', 'claro' => '#94a3b8', 'escuro' => '#64748b', 'glow' => 'rgba(100, 116, 139, 0.4)'];
        $animalIconeEstilo = 'background: linear-gradient(145deg, ' . $animalIconeLote['claro'] . ', ' . $animalIconeLote['escuro'] . '); box-shadow: 0 4px 10px -3px ' . $animalIconeLote['glow'] . ', inset 0 1px 1px rgba(255,255,255,0.4), inset 0 -1px 2px rgba(0,0,0,0.16);';
    @endphp
<tr>
    <td>
        <div class="lote-info-cell">
            <span class="lote-title">
                <span class="lote-animal-icon" style="{{ $animalIconeEstilo }}"><i class="fa-solid {{ $animalIconeLote['icone'] }}" aria-hidden="true"></i></span>
                {{ $lote->nome_lote }}
            </span>
            <span class="lote-subtitle">{{ $lote->tipo_animal }} • {{ $lote->quantidade_animais }} cabeças</span>
        </div>
    </td>
    <td class="bold-total-cell">{{ $lote->quantidade_animais }}</td>
    <td>
        <div class="time-weight-cell">
            <span class="time">{{ $lote->hora_manha }}</span>
            <span class="weight">{{ number_format($lote->peso_manha, 1, ',', '.') }} kg</span>
        </div>
    </td>
    <td>
        <div class="time-weight-cell">
            <span class="time">{{ $lote->hora_tarde }}</span>
            <span class="weight">{{ number_format($lote->peso_tarde, 1, ',', '.') }} kg</span>
        </div>
    </td>
    <td>
        <div class="time-weight-cell">
            <span class="time">{{ $lote->hora_noite }}</span>
            <span class="weight">{{ number_format($lote->peso_noite, 1, ',', '.') }} kg</span>
        </div>
    </td>
    <td class="bold-total-cell">{{ number_format($lote->total_dia, 1, ',', '.') }} kg</td>
    <td>
        <button type="button" class="status-badge status-badge-btn {{ $lote->status_class }}"
            onclick="abrirModalDetalhesLote(this)" data-detalhes='@json($lote->detalhes)'
            aria-haspopup="dialog" title="Ver detalhes do plano deste lote">
            <i class="fa-solid {{ $lote->icon }}"></i> {{ $lote->status_texto }}
        </button>
    </td>
</tr>
@endforeach
