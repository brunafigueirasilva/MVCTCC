@foreach ($conversoes as $conversao)
<div class="ca-container-card">
    <div class="ca-header">
        <div class="ca-header-info">
            <h3 class="ca-title" title="{{ $conversao->nome_lote }}">{{ $conversao->nome_lote }}</h3>
            <span class="ca-subtitle">{{ $conversao->tipo_animal ?? '—' }}</span>
        </div>
        <span class="status-badge {{ $conversao->status_class }}">{{ $conversao->status_texto }}</span>
    </div>

    <div class="ca-grid">
        <!-- Métrica 1: Peso Médio -->
        <div class="ca-item">
            <div class="ca-icon-wrapper bg-green-light">
                <i class="fa-solid fa-weight-hanging text-green"></i>
            </div>
            <div class="ca-info">
                <span class="ca-label">Peso Médio</span>
                <span class="ca-value">{{ number_format($conversao->peso_medio, 1, ',', '.') }} kg</span>
            </div>
        </div>

        <!-- Métrica 2: Animais no Lote -->
        <div class="ca-item">
            <div class="ca-icon-wrapper bg-green-light">
                <i class="fa-solid fa-users text-green"></i>
            </div>
            <div class="ca-info">
                <span class="ca-label">Animais no Lote</span>
                <span class="ca-value">{{ $conversao->quantidade_animais }} cabeças</span>
            </div>
        </div>

        <!-- Métrica 3: Consumo Diário (direto da ração vinculada) -->
        <div class="ca-item">
            <div class="ca-icon-wrapper bg-green-light">
                <i class="fa-solid fa-bucket text-green"></i>
            </div>
            <div class="ca-info">
                <span class="ca-label">Consumo Diário</span>
                <span class="ca-value">{{ number_format($conversao->consumo_diario, 1, ',', '.') }} kg</span>
            </div>
        </div>

        <!-- Métrica 4: Consumo por Animal -->
        <div class="ca-item">
            <div class="ca-icon-wrapper bg-green-light">
                <i class="fa-solid fa-chart-line text-green"></i>
            </div>
            <div class="ca-info">
                <span class="ca-label">Consumo por Animal</span>
                <span class="ca-value">
                    @if ($conversao->variacao_por_animal !== null)
                        <i class="fa-solid {{ $conversao->variacao_por_animal > 0 ? 'fa-arrow-up' : ($conversao->variacao_por_animal < 0 ? 'fa-arrow-down' : 'fa-minus') }} {{ $conversao->variacao_por_animal > 0 ? 'trend-up' : ($conversao->variacao_por_animal < 0 ? 'trend-down' : 'trend-neutral') }}" style="font-size: 0.6875rem;" aria-hidden="true" title="{{ number_format(abs($conversao->variacao_por_animal), 1, ',', '.') }}% vs os 7 dias anteriores"></i>
                    @endif
                    {{ $conversao->consumo_por_animal !== null ? number_format($conversao->consumo_por_animal, 2, ',', '.') . ' kg' : '—' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Métrica principal: Conversão Alimentar (kg de ração / kg de peso vivo) -->
    <div class="ca-hero">
        <div class="ca-hero-icon">
            <i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>
        </div>
        <div class="ca-hero-info">
            <span class="ca-hero-label">
                Conversão Alimentar
                <button type="button" class="tooltip-icon" aria-label="O que é conversão alimentar?" aria-expanded="false" data-tooltip-text="Conversão alimentar é o consumo diário (kg/dia) cadastrado na ração vinculada, dividido pelo peso vivo total do lote (peso médio × quantidade de animais). Quanto menor o valor, mais eficiente é o lote no aproveitamento da ração.">
                    <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                </button>
            </span>
            <span class="ca-hero-value">
                @if ($conversao->variacao_por_kg_vivo !== null)
                    <i class="fa-solid {{ $conversao->variacao_por_kg_vivo > 0 ? 'fa-arrow-up' : ($conversao->variacao_por_kg_vivo < 0 ? 'fa-arrow-down' : 'fa-minus') }} {{ $conversao->variacao_por_kg_vivo > 0 ? 'trend-up' : ($conversao->variacao_por_kg_vivo < 0 ? 'trend-down' : 'trend-neutral') }}" style="font-size: 0.75rem;" aria-hidden="true" title="{{ number_format(abs($conversao->variacao_por_kg_vivo), 1, ',', '.') }}% vs os 7 dias anteriores"></i>
                @endif
                {{ $conversao->consumo_por_kg_vivo !== null ? number_format($conversao->consumo_por_kg_vivo, 3, ',', '.') . ' kg/kg' : '—' }}
            </span>
        </div>
    </div>

    <!-- Conversão Alimentar REAL (kg de ração / kg de peso ganho), a partir
         das pesagens registradas — só existe a partir da 2ª pesagem. -->
    <div class="ca-hero ca-hero-real {{ $conversao->conversao_real !== null ? '' : 'ca-hero-vazio' }}">
        <div class="ca-hero-icon">
            <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
        </div>
        <div class="ca-hero-info">
            <span class="ca-hero-label">
                Conversão Real (FCR)
                <button type="button" class="tooltip-icon" aria-label="O que é conversão alimentar real?" aria-expanded="false" data-tooltip-text="Kg de ração consumidos dividido pelo kg de peso ganho entre as duas últimas pesagens registradas do lote. Quanto menor, mais eficiente. Só aparece a partir da segunda pesagem.">
                    <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                </button>
            </span>
            <span class="ca-hero-value">
                {{ $conversao->conversao_real !== null ? number_format($conversao->conversao_real, 2, ',', '.') . ' kg/kg' : '—' }}
            </span>
            @if ($conversao->tem_pesagem)
                <span class="ca-hero-sub">
                    Ganho de {{ number_format($conversao->ganho_peso_kg, 1, ',', '.') }} kg desde {{ $conversao->data_ultima_pesagem->format('d/m/Y') }}
                </span>
            @else
                <span class="ca-hero-sub">Registre a primeira pesagem para começar a medir</span>
            @endif
        </div>
    </div>

    <button
        type="button"
        class="ca-btn-pesagem"
        onclick="abrirModalPesagem({{ $conversao->id }}, @js($conversao->nome_lote), {{ $conversao->peso_medio }})"
    >
        <i class="fa-solid fa-weight-scale" aria-hidden="true"></i> Registrar Pesagem
    </button>
</div>
@endforeach
