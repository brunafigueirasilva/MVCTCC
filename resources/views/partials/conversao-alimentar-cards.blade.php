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

        <!-- Métrica 3: Consumo Diário PLANEJADO (ração vinculada) -->
        <div class="ca-item">
            <div class="ca-icon-wrapper bg-green-light">
                <i class="fa-solid fa-bucket text-green"></i>
            </div>
            <div class="ca-info">
                <span class="ca-label">Consumo Planejado</span>
                <span class="ca-value">{{ number_format($conversao->consumo_diario, 2, ',', '.') }} kg/dia</span>
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

    <!-- Consumo planejado por kg de peso vivo (NÃO é conversão alimentar:
         conversão é ração ÷ GANHO de peso — ver o bloco abaixo). Antes este
         número era chamado de "Conversão Alimentar". -->
    <div class="ca-hero">
        <div class="ca-hero-icon">
            <i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>
        </div>
        <div class="ca-hero-info">
            <span class="ca-hero-label">
                Ração por kg de peso vivo
                <button type="button" class="tooltip-icon" aria-label="O que é ração por kg de peso vivo?" aria-expanded="false" data-tooltip-text="Consumo diário planejado (kg/dia da ração vinculada) dividido pelo peso vivo total do lote (peso médio × quantidade de animais). Mostra quanto de ração o plano dá por kg de animal por dia — é uma meta do plano, não um resultado medido.">
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
                Conversão Alimentar
                <button type="button" class="tooltip-icon" aria-label="O que é conversão alimentar?" aria-expanded="false" data-tooltip-text="Ração consumida dividida pelo peso ganho entre as duas últimas pesagens do lote (kg de ração por kg ganho). Quanto menor, mais eficiente. O consumo vem da melhor fonte disponível: sensores dos cochos (real), ração colocada nos cochos (aproximada) ou plano alimentar (estimativa).">
                    <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                </button>
            </span>
            <span class="ca-hero-value">
                {{ $conversao->conversao_real !== null ? number_format($conversao->conversao_real, 2, ',', '.') . ' kg/kg' : '—' }}
            </span>
            @if ($conversao->conversao_real !== null)
                {{-- Origem do número sempre visível (texto, não só cor). --}}
                <span class="ca-fonte ca-fonte-{{ $conversao->fonte_consumo }}">
                    <i class="fa-solid {{ $conversao->conversao_eh_medida ? 'fa-gauge-high' : ($conversao->fonte_consumo === 'fornecido' ? 'fa-truck-ramp-box' : 'fa-clipboard-list') }}" aria-hidden="true"></i>
                    {{ $conversao->rotulo_fonte_consumo }}
                </span>
                <span class="ca-hero-sub">
                    Período até {{ $conversao->data_ultima_pesagem->format('d/m/Y') }}: ganho de {{ number_format($conversao->ganho_peso_kg, 1, ',', '.') }} kg com {{ number_format($conversao->racao_fornecida_kg, 1, ',', '.') }} kg de ração
                </span>
            @elseif ($conversao->tem_pesagem)
                <span class="ca-hero-sub">
                    Última pesagem em {{ $conversao->data_ultima_pesagem->format('d/m/Y') }}.
                    @if (($conversao->ganho_peso_kg ?? 0) < 0 || (($conversao->ganho_peso_kg ?? 0) == 0 && ($conversao->racao_fornecida_kg ?? 0) > 0))
                        O lote não ganhou peso no período, então não há conversão para calcular.
                    @elseif (($conversao->racao_fornecida_kg ?? 0) <= 0 && ($conversao->ganho_peso_kg ?? 0) > 0)
                        Não houve consumo registrado no período — abasteça os cochos do lote para calcular a conversão.
                    @else
                        Registre a próxima pesagem para calcular a conversão.
                    @endif
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
