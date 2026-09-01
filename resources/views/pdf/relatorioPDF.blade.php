<style>
    /* O reset de margin/padding é aplicado por tag (não via seletor
       universal "*") porque o dompdf, com "* { margin/padding: 0 }", deixa
       de desenhar qualquer elemento com position:fixed (o cabeçalho e o
       rodapé somem da página inteira, mesmo com HTML/CSS válidos) — um
       comportamento específico dessa combinação, isolado e confirmado em
       testes isolados com o dompdf desta instalação. box-sizing pode
       continuar universal, só o reset de margin/padding precisa ser
       escopado. */
    * {
        box-sizing: border-box;
    }

    body, h1, h3, p, div, span, table, thead, tbody, tr, td, th, img {
        margin: 0;
        padding: 0;
    }

    body {
        font-family: Arial, sans-serif;
        background: #ffffff;
        color: #1f2937;
        font-size: 12px;
    }

    @page {
        margin: 130px 28px 60px 28px;
    }

    /* ==========================================================================
       CABEÇALHO (fixo, repete em toda página via position:fixed do dompdf)
       Usa <div class="pdf-header"> em vez da tag semântica <header>: o
       dompdf não posiciona corretamente position:fixed nas tags <header>/
       <footer> (o conteúdo simplesmente não aparece em nenhuma página) —
       com uma div comum funciona normalmente.
       ========================================================================== */
    .pdf-header {
        position: fixed;
        top: -110px;
        left: 0px;
        right: 0px;
        height: 100px;
    }

    .header-bar {
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
        border-radius: 10px;
        padding: 14px 24px;
        display: table;
        width: 100%;
    }

    .header-bar .col-logo,
    .header-bar .col-left,
    .header-bar .col-right {
        display: table-cell;
        vertical-align: middle;
    }

    .header-bar .col-logo {
        width: 1%;
        white-space: nowrap;
        padding-right: 18px;
    }

    .header-bar .col-right {
        text-align: right;
    }

    .header-bar .logo-chip {
        background: #ffffff;
        border-radius: 8px;
        padding: 6px 12px;
        display: inline-block;
    }

    .header-bar .logo-chip img {
        height: 30px;
        width: auto;
        display: block;
    }

    .header-bar h1 {
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 0.3px;
    }

    .header-bar .meta {
        font-size: 10.5px;
        color: #d1fae5;
        line-height: 1.6;
    }

    .filtros-bar {
        margin-top: 10px;
        font-size: 10px;
        color: #4b5563;
    }

    .filtros-bar .chip {
        display: inline-block;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
        border-radius: 20px;
        padding: 3px 10px;
        margin-right: 6px;
        font-weight: bold;
    }

    .filtros-bar .sem-filtro {
        font-style: italic;
        color: #9ca3af;
    }

    /* ==========================================================================
       RODAPÉ (fixo, com numeração de página nativa do dompdf)
       Mesma observação do cabeçalho: <div class="pdf-footer"> no lugar da
       tag <footer>, que não renderiza com position:fixed neste dompdf.
       ========================================================================== */
    .pdf-footer {
        position: fixed;
        bottom: -45px;
        left: 0px;
        right: 0px;
        height: 40px;
        border-top: 1px solid #e5e7eb;
        padding-top: 8px;
        font-size: 9.5px;
        color: #9ca3af;
        display: table;
        width: 100%;
    }

    .pdf-footer .col-left,
    .pdf-footer .col-right {
        display: table-cell;
        vertical-align: middle;
    }

    .pdf-footer .col-right {
        text-align: right;
    }

    .pdf-footer .pagenum:after {
        content: "Página " counter(page) " de " counter(pages);
    }

    /* ==========================================================================
       EXPLICAÇÃO DO RELATÓRIO (aparece uma única vez, no topo da 1ª página)
       ========================================================================== */
    .intro-explicacao {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 4px solid #16a34a;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 10.5px;
        color: #374151;
        line-height: 1.6;
    }

    .intro-explicacao .intro-titulo {
        font-size: 12px;
        font-weight: bold;
        color: #123f22;
        margin-bottom: 4px;
    }

    /* ==========================================================================
       BARRAS DE PORCENTAGEM (tabelas de gastos e eficiência)
       ========================================================================== */
    .bar-track {
        display: inline-block;
        vertical-align: middle;
        width: 50px;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        margin-right: 6px;
    }

    .bar-fill {
        display: block;
        height: 6px;
    }

    .bar-value {
        display: inline-block;
        vertical-align: middle;
        font-weight: bold;
        min-width: 32px;
    }

    /* ==========================================================================
       CARDS DE RESUMO
       ========================================================================== */
    .section-cards {
        display: table;
        width: 100%;
        margin-bottom: 22px;
        border-spacing: 10px 0;
    }

    .card {
        display: table-cell;
        width: 20%;
        border: 1px solid #e5e7eb;
        border-left: 4px solid #16a34a;
        border-radius: 10px;
        padding: 12px 14px;
    }

    /* Um tom por card — mesmo princípio de cor usado nos cards do site
       (uma faixa de degradê suave por cima do fundo neutro), adaptado pro
       suporte de gradiente do dompdf. */
    .card.verde { border-left-color: #059669; background: linear-gradient(135deg, rgba(16, 185, 129, 0.14), #f9fafb 65%); }
    .card.azul  { border-left-color: #2563eb; background: linear-gradient(135deg, rgba(37, 99, 235, 0.14), #f9fafb 65%); }
    .card.ambar { border-left-color: #d97706; background: linear-gradient(135deg, rgba(217, 119, 6, 0.14), #f9fafb 65%); }
    .card.roxo  { border-left-color: #7c3aed; background: linear-gradient(135deg, rgba(124, 58, 237, 0.14), #f9fafb 65%); }
    .card.ciano { border-left-color: #0891b2; background: linear-gradient(135deg, rgba(8, 145, 178, 0.14), #f9fafb 65%); }

    .card .small {
        font-size: 9.5px;
        color: #6b7280;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        font-weight: bold;
    }

    .card .big {
        font-size: 16px;
        font-weight: bold;
        color: #111827;
    }

    .card .trend {
        font-size: 9px;
        margin-top: 6px;
    }

    .trend-up { color: #16a34a; }
    .trend-down { color: #dc2626; }
    .trend-neutral { color: #9ca3af; }

    .insight-banner {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 3px solid #16a34a;
        border-radius: 8px;
        padding: 8px 12px;
        margin-top: 10px;
        font-size: 10px;
        color: #374151;
    }

    .categoria-badge {
        display: inline-block;
        background: #dcfce7;
        color: #15803d;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 9.5px;
        font-weight: bold;
    }

    /* ==========================================================================
       SEÇÕES / TABELAS
       ========================================================================== */
    .grid-2 {
        display: table;
        width: 100%;
        border-spacing: 16px 0;
        margin-bottom: 4px;
    }

    .grid-2 .col {
        display: table-cell;
        width: 50%;
        vertical-align: top;
    }

    .section {
        margin-bottom: 20px;
        page-break-inside: avoid;
    }

    .title {
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #123f22;
        border-left: 4px solid #16a34a;
        padding-left: 8px;
    }

    .title.azul  { border-left-color: #2563eb; }
    .title.ambar { border-left-color: #d97706; }
    .title.roxo  { border-left-color: #7c3aed; }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5px;
    }

    thead tr {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }

    th {
        text-align: left;
        padding: 7px 9px;
        font-size: 10px;
    }

    td {
        padding: 6px 9px;
        border-bottom: 1px solid #e5e7eb;
    }

    tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    td.numero,
    th.numero {
        text-align: right;
    }

    .empty-msg {
        text-align: center;
        color: #9ca3af;
        font-style: italic;
        padding: 14px !important;
    }

    table, tr, td, th {
        page-break-inside: avoid;
    }
</style>

<div class="pdf-header">
    <div class="header-bar">
        @if ($logoBase64 ?? null)
            <div class="col-logo">
                <div class="logo-chip"><img src="{{ $logoBase64 }}" alt="AgriFarm"></div>
            </div>
        @endif
        <div class="col-left">
            <h1>Relatório de Consumo e Eficiência</h1>
        </div>
        <div class="col-right meta">
            Emitido em {{ $dataEmissao->format('d/m/Y \à\s H:i') }}<br>
            Por {{ optional($cadastroLogado)->responsavel ?? 'Sistema' }}
        </div>
    </div>
    <div class="filtros-bar">
        @if (count($filtrosAplicados ?? []))
            <strong>Filtros aplicados:</strong>
            @foreach ($filtrosAplicados as $filtro)
                <span class="chip">{{ $filtro['label'] }}</span>
            @endforeach
        @else
            <span class="sem-filtro">Nenhum filtro aplicado — exibindo todos os dados disponíveis.</span>
        @endif
    </div>
</div>

<div class="pdf-footer">
    <div class="col-left">Relatório AgriFarm &middot; documento gerado automaticamente</div>
    <div class="col-right pagenum"></div>
</div>

<!-- EXPLICAÇÃO DO RELATÓRIO -->
<div class="intro-explicacao">
    <div class="intro-titulo">Sobre este relatório</div>
    Este documento resume o consumo de ração, os custos estimados e o desempenho dos lotes cadastrados
    no período e nos filtros selecionados. Os cartões abaixo trazem os principais números; as tabelas em
    seguida detalham o consumo dia a dia, a distribuição de gastos por tipo de ração, o consumo e custo de
    cada lote, e a eficiência de cada lote em seis critérios (ganho de peso, consumo, conversão alimentar,
    saúde, custo-benefício e sustentabilidade). Nas barras coloridas: verde indica bom desempenho, amarelo
    pede atenção e vermelho está abaixo do esperado.
</div>

<!-- CARDS DE RESUMO (mesmos 5 indicadores — e mesma variação vs período
     anterior — exibidos no topo da tela de Relatórios) -->
@php
    $statCards = $statCards ?? [];
    $trendClasse = function ($valor) {
        if (is_null($valor) || $valor == 0) return 'trend-neutral';
        return $valor > 0 ? 'trend-up' : 'trend-down';
    };
    $trendTexto = function ($valor) {
        if (is_null($valor)) return '— Sem período anterior';
        if ($valor == 0) return '— 0% vs período anterior';
        $seta = $valor > 0 ? '▲' : '▼';
        return $seta . ' ' . number_format(abs($valor), 1, ',', '.') . '% vs período anterior';
    };
@endphp
<div class="section-cards">
    <div class="card verde">
        <div class="small">Consumo Total de Ração</div>
        <div class="big">{{ number_format($statCards['consumo']['valor'] ?? 0, 0, ',', '.') }} kg</div>
        <div class="trend {{ $trendClasse($statCards['consumo']['variacao'] ?? null) }}">{{ $trendTexto($statCards['consumo']['variacao'] ?? null) }}</div>
    </div>
    <div class="card ambar">
        <div class="small">Custo Total</div>
        <div class="big">R$ {{ number_format($statCards['custo']['valor'] ?? 0, 2, ',', '.') }}</div>
        <div class="trend {{ $trendClasse($statCards['custo']['variacao'] ?? null) }}">{{ $trendTexto($statCards['custo']['variacao'] ?? null) }}</div>
    </div>
    <div class="card azul">
        <div class="small">Média Diária</div>
        <div class="big">{{ number_format($statCards['mediaDiaria']['valor'] ?? 0, 1, ',', '.') }} kg/dia</div>
        <div class="trend {{ $trendClasse($statCards['mediaDiaria']['variacao'] ?? null) }}">{{ $trendTexto($statCards['mediaDiaria']['variacao'] ?? null) }}</div>
    </div>
    <div class="card ciano">
        <div class="small">Consumo por kg Vivo</div>
        <div class="big">{{ ($statCards['porKgVivo']['valor'] ?? null) !== null ? number_format($statCards['porKgVivo']['valor'], 3, ',', '.') . ' kg/kg' : '—' }}</div>
        <div class="trend {{ $trendClasse($statCards['porKgVivo']['variacao'] ?? null) }}">{{ $trendTexto($statCards['porKgVivo']['variacao'] ?? null) }}</div>
    </div>
    <div class="card roxo">
        <div class="small">Eficiência Média</div>
        <div class="big">{{ $statCards['eficiencia']['valor'] ?? 0 }}%</div>
        <div class="trend {{ $trendClasse($statCards['eficiencia']['variacao'] ?? null) }}">{{ $trendTexto($statCards['eficiencia']['variacao'] ?? null) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="col">
        <!-- CONSUMO AO LONGO DO TEMPO -->
        <div class="section">
            <div class="title azul">Consumo ao Longo do Tempo</div>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="numero">Consumo (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($consumoPorDia ?? [] as $item)
                        <tr>
                            <td>{{ $item['data'] }}</td>
                            <td class="numero">{{ number_format($item['consumo'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty-msg" colspan="2">Nenhum consumo registrado para os filtros selecionados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col">
        <!-- DISTRIBUIÇÃO DE GASTOS -->
        <div class="section">
            <div class="title ambar">Distribuição de Gastos por Ração</div>
            <table>
                <thead>
                    <tr>
                        <th>Ração</th>
                        <th class="numero">Participação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gastos ?? [] as $gasto)
                        <tr>
                            <td>{{ $gasto['nome'] }}</td>
                            <td class="numero">@include('pdf.partials.barra', ['valor' => $gasto['percentual']])</td>
                        </tr>
                    @empty
                        <tr><td class="empty-msg" colspan="2">Nenhum dado de consumo por ração para os filtros selecionados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CONSUMO POR LOTE -->
<div class="section">
    <div class="title">Consumo e Custo por Lote</div>
    <table>
        <thead>
            <tr>
                <th>Lote</th>
                <th class="numero">Consumo (kg)</th>
                <th class="numero">Custo Estimado (R$)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($consumoLotes ?? [] as $lote)
                <tr>
                    <td>{{ $lote['nome'] }}</td>
                    <td class="numero">{{ number_format($lote['consumo'], 0, ',', '.') }}</td>
                    <td class="numero">R$ {{ number_format($lote['custo'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td class="empty-msg" colspan="3">Nenhum lote com consumo registrado para os filtros selecionados.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($melhorLote ?? null)
        <div class="insight-banner"><strong>{{ $melhorLote }}</strong> apresenta o maior consumo e custo diário no período analisado.</div>
    @endif
</div>

<!-- EFICIÊNCIA POR LOTE -->
<div class="section">
    <div class="title roxo">Eficiência por Lote</div>
    <table>
        <thead>
            <tr>
                <th>Lote</th>
                <th class="numero">Ganho de Peso</th>
                <th class="numero">Consumo</th>
                <th class="numero">Conversão</th>
                <th class="numero">Saúde</th>
                <th class="numero">Custo-Benefício</th>
                <th class="numero">Sustentabilidade</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($eficienciaLotes ?? [] as $lote)
                <tr>
                    <td>{{ $lote['lote'] }}</td>
                    <td class="numero">@include('pdf.partials.barra', ['valor' => $lote['ganho_peso']])</td>
                    <td class="numero">@include('pdf.partials.barra', ['valor' => $lote['consumo']])</td>
                    <td class="numero">@include('pdf.partials.barra', ['valor' => $lote['conversao']])</td>
                    <td class="numero">@include('pdf.partials.barra', ['valor' => $lote['saude']])</td>
                    <td class="numero">@include('pdf.partials.barra', ['valor' => $lote['custo_beneficio']])</td>
                    <td class="numero">@include('pdf.partials.barra', ['valor' => $lote['sustentabilidade']])</td>
                </tr>
            @empty
                <tr><td class="empty-msg" colspan="7">Nenhum lote disponível para calcular eficiência.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- RESUMO POR CATEGORIA -->
<div class="section">
    <div class="title ambar">Resumo por Categoria</div>
    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th class="numero">Lotes</th>
                <th class="numero">Consumo (kg)</th>
                <th class="numero">Custo Estimado (R$)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($resumoPorCategoria ?? [] as $categoria)
                <tr>
                    <td>{{ $categoria->label }}</td>
                    <td class="numero">{{ $categoria->quantidade_lotes }}</td>
                    <td class="numero">{{ number_format($categoria->consumo_total, 0, ',', '.') }}</td>
                    <td class="numero">R$ {{ number_format($categoria->custo_total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td class="empty-msg" colspan="4">Nenhum lote encontrado para os filtros selecionados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- RANKING DE LOTES POR DESEMPENHO -->
<div class="section">
    <div class="title roxo">Ranking de Lotes por Desempenho</div>
    <table>
        <thead>
            <tr>
                <th>Posição</th>
                <th>Lote</th>
                <th>Categoria</th>
                <th class="numero">Consumo Médio (kg/dia)</th>
                <th class="numero">Custo Médio (R$/dia)</th>
                <th class="numero">Eficiência</th>
                <th class="numero">Variação</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rankingLotesCompleto ?? [] as $item)
                <tr>
                    <td>{{ $item->posicao }}º</td>
                    <td><strong>{{ $item->nome_lote }}</strong></td>
                    <td><span class="categoria-badge">{{ $item->tipo_animal }}</span></td>
                    <td class="numero">{{ number_format($item->consumo_medio_dia, 1, ',', '.') }}</td>
                    <td class="numero">R$ {{ number_format($item->custo_medio_dia, 2, ',', '.') }}</td>
                    <td class="numero">@include('pdf.partials.barra', ['valor' => $item->eficiencia])</td>
                    <td class="numero">
                        @if ($item->variacao === null)
                            <span class="trend-neutral">— Novo</span>
                        @elseif ($item->variacao == 0)
                            <span class="trend-neutral">— 0%</span>
                        @elseif ($item->variacao > 0)
                            <span class="trend-up">▲ {{ number_format($item->variacao, 1, ',', '.') }}%</span>
                        @else
                            <span class="trend-down">▼ {{ number_format(abs($item->variacao), 1, ',', '.') }}%</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td class="empty-msg" colspan="7">Nenhum lote encontrado para os filtros selecionados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
