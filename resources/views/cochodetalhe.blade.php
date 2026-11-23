@extends('layouts.app')

@section('title', 'AgriFarm | ' . $cocho->nome_cocho)

@push('scripts')
<script src="{{ asset('js/vendor/chart.min.js') }}"></script>
@endpush

@push('styles')
    @include('partials.page-identity')
    <style>
        /* ==========================================================================
        DETALHES DO COCHO (monitoramento IoT) — mesma linguagem dos cards da
        tela de Cochos. Todos os valores são preenchidos pelo JS a partir de
        route('cocho.iot') e route('cocho.iot.historico'); "—" enquanto carrega.
        ========================================================================== */
        .det-voltar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--verde-texto, #0e4a27);
            text-decoration: none;
            margin-bottom: 4px;
            min-height: 32px; /* área de toque (tinha ~16px de altura) */
        }

        .det-voltar:hover { text-decoration: underline; }

        .det-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.8125rem;
            font-weight: 700;
            background: rgba(148, 163, 184, 0.16);
            /* #475569 no claro: o cinza secundário (#64748b) sobre este
               fundo dava 4,0:1, abaixo do mínimo AA (4,5:1). */
            color: #475569;
            white-space: nowrap;
        }

        body.dark-theme .det-status { color: var(--text-secundario); }

        /* Indicador da atualização automática (balança/distância). */
        .det-ao-vivo { display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; color: var(--text-secundario, #64748b); }
        .det-ao-vivo-ponto { width: 8px; height: 8px; border-radius: 50%; background: #16a34a; box-shadow: 0 0 0 0 rgba(22, 163, 74, .5); animation: det-pulso 2s infinite; }
        .det-ao-vivo.pausado .det-ao-vivo-ponto { background: #94a3b8; animation: none; }
        @keyframes det-pulso { 0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, .5); } 70% { box-shadow: 0 0 0 7px rgba(22, 163, 74, 0); } 100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); } }
        body.high-contrast .det-ao-vivo-ponto { background: #ffff00; box-shadow: none; animation: none; }

        .det-status::before {
            content: '';
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: currentColor;
        }

        .det-simulado { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; background: #ede9fe; color: #6d28d9; }
        .det-simulado[hidden] { display: none; }
        body.dark-theme .det-simulado { background: rgba(167, 139, 250, 0.16); color: #c4b5fd; }
        body.high-contrast .det-simulado { background: #000; color: #ffff00; border: 1px solid #ffff00; }
        .det-status.online { background: #dcfce7; color: #15803d; }
        .det-status.offline { background: #fef3c7; color: #b45309; }
        .det-status.erro { background: #fee2e2; color: #dc2626; }
        body.dark-theme .det-status.online { background: rgba(74, 222, 128, 0.14); color: #4ade80; }
        body.dark-theme .det-status.offline { background: rgba(245, 158, 11, 0.16); color: #fbbf24; }
        body.dark-theme .det-status.erro { background: rgba(239, 68, 68, 0.16); color: #fca5a5; }

        .det-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            font-family: inherit;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .det-btn:hover { border-color: var(--verde-secundario, #16a765); color: var(--verde-texto, #0e4a27); }
        .det-btn.girando i { animation: detGirar 0.8s linear infinite; }
        @keyframes detGirar { to { transform: rotate(360deg); } }

        /* Alertas (nível baixo, sensor offline/erro, sem calibração...) */
        .det-alertas { display: flex; flex-direction: column; gap: 10px; margin-bottom: 22px; }
        .det-alertas:empty { display: none; }

        .det-alerta {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.875rem;
            border: 1px solid var(--surface-muted-border);
            background: var(--surface-muted-bg);
            color: var(--text-principal);
        }

        .det-alerta i { width: 18px; text-align: center; }
        .det-alerta.critico { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        .det-alerta.baixo { background: #fff7ed; border-color: #fed7aa; color: #9a3412; }
        .det-alerta.info { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
        body.dark-theme .det-alerta.critico { background: rgba(239, 68, 68, 0.10); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .det-alerta.baixo { background: rgba(249, 115, 22, 0.10); border-color: rgba(249, 115, 22, 0.3); color: #fdba74; }
        body.dark-theme .det-alerta.info { background: rgba(59, 130, 246, 0.10); border-color: rgba(59, 130, 246, 0.3); color: #93c5fd; }

        /* Armazém → Cocho: os dois tanques lado a lado, ligados por uma seta. */
        .det-fluxo {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .det-fluxo .pg-card-value { font-size: clamp(1.4rem, 2.2vw, 1.9rem); }
        .det-fluxo .pg-tank { width: 76px; height: 116px; }

        .det-fluxo-legenda {
            font-size: 0.71875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secundario);
            border-top: 1px dashed var(--surface-muted-border);
            padding-top: 10px;
        }

        .det-fluxo-seta {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--verde-claro, #e8f5ee);
            color: var(--verde-texto, #0e4a27);
            font-size: 1rem;
        }

        .det-cards-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }

        .det-racao-lote-grid { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .det-racao-lote-grid .pg-meter { flex: 1 1 220px; height: 10px; }
        .det-racao-lote-grid strong { color: var(--text-principal); }

        @media (max-width: 900px) {
            .det-fluxo { grid-template-columns: 1fr; }
            .det-fluxo-seta { justify-self: center; transform: rotate(90deg); }
            .det-cards-3 { grid-template-columns: 1fr; }
        }

        body.high-contrast .det-fluxo-seta { background: #000 !important; border: 2px solid #ffff00; color: #ffff00 !important; }

        /* Indicadores */
        .det-indicadores {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(190px, 100%), 1fr));
            gap: 12px;
        }

        .det-ind {
            padding: 14px 16px;
            border-radius: 14px;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            min-width: 0;
        }

        .det-ind dt {
            font-size: 0.71875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secundario);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .det-ind dt i { color: var(--verde-texto, #0e4a27); }
        .det-ind dd { margin-top: 6px; font-size: 1.125rem; font-weight: 700; color: var(--text-principal); overflow-wrap: anywhere; }
        .det-ind dd small { display: block; font-size: 0.75rem; font-weight: 500; color: var(--text-secundario); margin-top: 2px; }

        /* Gráficos */
        .det-graficos { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 20px; }
        .det-graficos .pg-box { margin-bottom: 0; }
        .det-box-topo { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
        .det-box-topo .pg-box-title { margin-bottom: 0; }
        .det-chart { position: relative; height: 300px; }

        .det-chart-vazio {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            color: var(--text-secundario);
            font-size: 0.875rem;
            background: var(--card-bg);
        }

        .det-chart-vazio.visivel { display: flex; }
        .det-chart-vazio i { display: block; font-size: 1.5rem; margin-bottom: 10px; opacity: 0.6; }

        .det-totais { display: flex; flex-wrap: wrap; gap: 8px 18px; margin-top: 12px; font-size: 0.8125rem; color: var(--text-secundario); }
        .det-totais strong { color: var(--text-principal); }

        /* Histórico */
        .det-tabela-wrap { overflow-x: auto; }
        /* Histórico de leituras: aba recolhida, abre ao clicar no título. */
        .det-aba summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; cursor: pointer; list-style: none; }
        .det-aba summary::-webkit-details-marker { display: none; }
        .det-aba summary .pg-box-title { margin-bottom: 0; }
        .det-aba-seta { color: var(--text-secundario); font-size: 0.85rem; transition: transform 0.2s ease; }
        .det-aba[open] .det-aba-seta { transform: rotate(90deg); }
        .det-aba[open] summary { margin-bottom: 16px; }
        .det-tabela { width: 100%; border-collapse: collapse; font-size: 0.84375rem; }
        .det-tabela th { text-align: left; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secundario); padding: 10px 12px; border-bottom: 1px solid var(--surface-muted-border); white-space: nowrap; }
        .det-tabela td { padding: 10px 12px; border-bottom: 1px solid var(--surface-muted-border); color: var(--text-principal); white-space: nowrap; }
        .det-tabela tr:last-child td { border-bottom: none; }
        .det-tabela .neg { color: #dc2626; font-weight: 700; }
        .det-tabela .pos { color: #15803d; font-weight: 700; }
        .det-tabela-vazia { text-align: center; color: var(--text-secundario); padding: 24px 12px !important; }

        /* Sensores cadastrados */
        .det-sensores { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(260px, 100%), 1fr)); gap: 12px; }

        .det-sensor {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--surface-muted-border);
            background: var(--surface-muted-bg);
        }

        .det-sensor-ico {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            flex-shrink: 0;
            background: linear-gradient(145deg, #60a5fa, #2563eb);
        }

        .det-sensor-ico.balanca { background: linear-gradient(145deg, #fb923c, #ea580c); }
        .det-sensor strong { display: block; color: var(--text-principal); font-size: 0.9375rem; }
        .det-sensor span { display: block; font-size: 0.78125rem; color: var(--text-secundario); margin-top: 2px; }
        .det-sensor .det-status { margin-top: 8px; padding: 4px 10px; font-size: 0.71875rem; }

        @media (max-width: 1100px) {
            .det-graficos { grid-template-columns: 1fr; }
        }

        @media (max-width: 560px) {
            .det-chart { height: 240px; }
        }

        body.high-contrast .det-status,
        body.high-contrast .det-alerta,
        body.high-contrast .det-ind,
        body.high-contrast .det-sensor,
        body.high-contrast .det-btn {
            background: #000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .det-voltar { color: #ffff00 !important; text-decoration: underline; }

        body.high-contrast .det-ind dt,
        body.high-contrast .det-ind dd,
        body.high-contrast .det-tabela th,
        body.high-contrast .det-tabela td { color: #ffff00 !important; }
    </style>
@endpush

@section('content')
    <main class="main" id="mainArea" data-pg="cochos"
        data-url-iot="{{ route('cocho.iot', $cocho->id) }}"
        data-url-historico="{{ route('cocho.iot.historico', $cocho->id) }}">
        <div class="pg-top">
            <div class="pg-top-text">
                <a href="{{ route('cocho.listar') }}" class="det-voltar"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Gestão de Cochos</a>
                <h1 class="pg-title"><i class="fa-solid fa-cube titulo-icone" aria-hidden="true"></i> {{ $cocho->nome_cocho }}</h1>
                <p class="pg-sub">
                    @php($nomeLote = $cocho->lote->nome_lote ?? '—'){{ preg_match('/^lote\b/i', $nomeLote) ? $nomeLote : 'Lote ' . $nomeLote }} · {{ $cocho->lote->fazenda->nome_fazenda ?? 'Sem fazenda' }}
                    · monitoramento IoT (sensor de distância e balança)
                </p>
            </div>
            <div class="pg-top-actions">
                <span class="det-status" id="detStatus" role="status" aria-live="polite">Carregando…</span>
                <span class="det-simulado" id="detSimulado" hidden title="Leituras geradas pelo simulador (php artisan iot:simular), não por um sensor físico"><i class="fa-solid fa-flask" aria-hidden="true"></i> Dados simulados</span>
                <span class="det-ao-vivo" id="detAoVivo" title="Balança, distância, tabela e gráficos atualizam sozinhos"><span class="det-ao-vivo-ponto" aria-hidden="true"></span> Ao vivo</span>
                <button type="button" class="det-btn" id="detAtualizar"><i class="fa-solid fa-rotate" aria-hidden="true"></i> Atualizar</button>
            </div>
        </div>

        <div class="det-alertas" id="detAlertas" aria-live="polite"></div>

        <!-- Armazém → Cocho: de onde a ração sai (estoque da fazenda) e quanto
             está disponível agora dentro deste cocho (medido pelo sensor). -->
        <section class="det-fluxo" aria-label="Estoque do armazém e quantidade no cocho">
            <div class="pg-card pg-laranja">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-warehouse" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Quantidade no Armazém</span>
                        <span class="pg-card-value" data-campo="armazem_quantidade">—</span>
                        <span class="pg-card-sub" data-campo="armazem_sub">—</span>
                    </div>
                    <div class="pg-card-visual">
                        <div class="pg-tank" id="detTanqueArmazem" role="img" aria-label="Ocupação do armazém">
                            <div class="pg-tank-fill"></div>
                            <div class="pg-tank-label" data-campo="armazem_percentual">—</div>
                        </div>
                    </div>
                </div>
                <span class="det-fluxo-legenda">Armazém · total disponível na fazenda</span>
            </div>

            <div class="det-fluxo-seta" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></div>

            <div class="pg-card pg-verde">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-bowl-food" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Quantidade no Cocho</span>
                        <span class="pg-card-value" data-campo="cocho_quantidade">—</span>
                        <span class="pg-card-sub" data-campo="cocho_sub">—</span>
                    </div>
                    <div class="pg-card-visual">
                        <div class="pg-tank" id="detTanque" role="img" aria-label="Nível do cocho">
                            <div class="pg-tank-fill"></div>
                            <div class="pg-tank-label" data-campo="cocho_percentual">—</div>
                        </div>
                    </div>
                </div>
                <span class="det-fluxo-legenda">Cocho · disponível agora, medido pelo sensor</span>
            </div>
        </section>

        <section class="pg-cards det-cards-3" aria-label="Dados do cocho e dos sensores">
            <div class="pg-card pg-azul">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-ruler-combined" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Capacidade Total</span>
                        <span class="pg-card-value" data-campo="capacidade_total">—</span>
                        <span class="pg-card-sub" data-campo="espaco_livre">—</span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-roxo">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-ruler-vertical" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Sensor de Distância</span>
                        <span class="pg-card-value" data-campo="distancia_cm">—</span>
                        <span class="pg-card-sub" data-campo="distancia_sub">—</span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-laranja">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-weight-scale" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Sensor da Balança</span>
                        <span class="pg-card-value" data-campo="peso_kg">—</span>
                        <span class="pg-card-sub" data-campo="peso_sub">—</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Ração vinculada ao lote: o estoque que abastece este cocho. -->
        <section class="pg-box det-racao-lote" aria-labelledby="tituloRacaoLote">
            <h2 class="pg-box-title" id="tituloRacaoLote"><i class="fa-solid fa-wheat-awn" aria-hidden="true"></i> Ração do lote no armazém</h2>
            <div id="detRacaoLote" class="pg-sub" data-ui-skeleton="1">Carregando…</div>
        </section>

        <!-- Indicadores calculados no servidor -->
        <section class="pg-box" aria-labelledby="tituloIndicadores">
            <h2 class="pg-box-title" id="tituloIndicadores"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i> Indicadores</h2>
            <dl class="det-indicadores">
                <div class="det-ind"><dt><i class="fa-solid fa-percent" aria-hidden="true"></i> Percentual disponível</dt><dd data-campo="ind_percentual">—</dd></div>
                <div class="det-ind"><dt><i class="fa-solid fa-utensils" aria-hidden="true"></i> Consumido hoje</dt><dd data-campo="ind_consumo_hoje">—</dd></div>
                <div class="det-ind"><dt><i class="fa-solid fa-calendar-week" aria-hidden="true"></i> Consumo (7 dias)</dt><dd data-campo="ind_consumo_7">—</dd></div>
                <div class="det-ind"><dt><i class="fa-solid fa-chart-line" aria-hidden="true"></i> Consumo médio</dt><dd data-campo="ind_consumo_medio">—</dd></div>
                <div class="det-ind"><dt><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i> Previsão de esvaziamento</dt><dd data-campo="ind_previsao">—</dd></div>
                <div class="det-ind"><dt><i class="fa-solid fa-truck-ramp-box" aria-hidden="true"></i> Última reposição</dt><dd data-campo="ind_reposicao">—</dd></div>
                <div class="det-ind"><dt><i class="fa-solid fa-satellite-dish" aria-hidden="true"></i> Última leitura</dt><dd data-campo="ind_leitura">—</dd></div>
                <div class="det-ind"><dt><i class="fa-solid fa-wifi" aria-hidden="true"></i> Status do sensor</dt><dd data-campo="ind_status">—</dd></div>
            </dl>
        </section>

        <!-- Gráficos (histórico IoT) -->
        <div class="det-graficos">
            <section class="pg-box" aria-labelledby="tituloGraficoNivel">
                <div class="det-box-topo">
                    <h2 class="pg-box-title" id="tituloGraficoNivel"><i class="fa-solid fa-chart-area" aria-hidden="true"></i> Quantidade no cocho ao longo do tempo</h2>
                    <div class="periodo-chips" role="group" aria-label="Período do histórico">
                        <button type="button" class="periodo-chip active" data-periodo="1h" aria-pressed="true">1 hora</button>
                        <button type="button" class="periodo-chip" data-periodo="hoje" aria-pressed="false">Hoje</button>
                        <button type="button" class="periodo-chip" data-periodo="7d" aria-pressed="false">7 dias</button>
                        <button type="button" class="periodo-chip" data-periodo="30d" aria-pressed="false">30 dias</button>
                    </div>
                </div>
                <div class="det-chart">
                    <canvas id="graficoNivel" role="img" aria-label="Quantidade de ração no cocho ao longo do tempo, com a linha da capacidade"></canvas>
                    <div class="det-chart-vazio" id="graficoNivelVazio"><div><i class="fa-solid fa-satellite-dish" aria-hidden="true"></i>Ainda não existe leitura do sensor neste período.</div></div>
                </div>
            </section>

            <section class="pg-box" aria-labelledby="tituloGraficoConsumo">
                <div class="det-box-topo">
                    <h2 class="pg-box-title" id="tituloGraficoConsumo"><i class="fa-solid fa-chart-column" aria-hidden="true"></i> Consumo</h2>
                </div>
                <div class="det-chart">
                    <canvas id="graficoConsumo" role="img" aria-label="Consumo de ração por intervalo"></canvas>
                    <div class="det-chart-vazio" id="graficoConsumoVazio"><div><i class="fa-solid fa-chart-column" aria-hidden="true"></i>Sem consumo registrado neste período.</div></div>
                </div>
                <div class="det-totais" id="detTotais"></div>
            </section>
        </div>

        <!-- Histórico de leituras -->
        <details class="pg-box det-aba" style="margin-top: 26px;">
            <summary><h2 class="pg-box-title" id="tituloHistorico"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Histórico de leituras</h2><i class="fa-solid fa-chevron-right det-aba-seta" aria-hidden="true"></i></summary>
            <div class="det-tabela-wrap">
                <table class="det-tabela">
                    <caption class="sr-only">Últimas leituras dos sensores deste cocho no período selecionado</caption>
                    <thead>
                        <tr>
                            <th scope="col">Data/hora</th>
                            <th scope="col">Sensor</th>
                            <th scope="col">Peso</th>
                            <th scope="col">Distância</th>
                            <th scope="col">Nível</th>
                            <th scope="col">Quantidade</th>
                            <th scope="col">Variação</th>
                        </tr>
                    </thead>
                    <tbody id="detHistorico">
                        <tr><td colspan="7" class="det-tabela-vazia" data-ui-skeleton="3">Carregando…</td></tr>
                    </tbody>
                </table>
            </div>
        </details>

        <!-- Sensores cadastrados -->
        <section class="pg-box" aria-labelledby="tituloSensores">
            <div class="det-box-topo">
                <h2 class="pg-box-title" id="tituloSensores"><i class="fa-solid fa-microchip" aria-hidden="true"></i> Sensores do cocho</h2>
                <a href="{{ route('cocho.listar') }}" class="det-btn"><i class="fa-solid fa-pen" aria-hidden="true"></i> Cadastrar / editar sensores</a>
            </div>
            <div class="det-sensores" id="detSensores"></div>
        </section>
    </main>
@endsection

@push('scripts')
<script>
    (function () {
        if (window.Chart) Chart.defaults.devicePixelRatio = 1;

        const main = document.getElementById('mainArea');
        const urlIot = main.dataset.urlIot;
        const urlHistorico = main.dataset.urlHistorico;
        // Atualização automática SÓ de balança, distância, tabela de leituras e
        // gráficos. O resto da tela (armazém, indicadores, alertas) carrega ao
        // abrir e no botão "Atualizar".
        const INTERVALO_AO_VIVO = 2000;

        let periodo = '1h';
        let graficoNivel = null;
        let graficoConsumo = null;
        let ultimoHistorico = null;

        const ROTULO_STATUS = { online: 'Online', offline: 'Sem comunicação', erro: 'Erro na leitura', sem_leitura: 'Sem leitura' };
        // Sensores "SIM-..." vêm do simulador (php artisan iot:simular), não
        // de um ESP32 — a tela avisa para não confundir com leitura real.
        const simulado = (s) => /^SIM-/i.test(s.codigo || '');
        const ICONE_ALERTA = { critico: 'fa-triangle-exclamation', baixo: 'fa-circle-exclamation', info: 'fa-circle-info' };

        const esc = (texto) => { const d = document.createElement('div'); d.textContent = texto == null ? '' : String(texto); return d.innerHTML; };
        const num = (v, casas = 1) => Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: casas, maximumFractionDigits: casas });
        const kg = (v) => num(v, 1) + ' kg';
        const dataHora = (s) => {
            if (!s) return '—';
            const d = new Date(s.replace(' ', 'T'));
            return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' }) + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        };
        const campo = (nome, valor) => document.querySelectorAll('[data-campo="' + nome + '"]').forEach((el) => { el.innerHTML = valor; });
        const css = (nome, padrao) => getComputedStyle(document.body).getPropertyValue(nome).trim() || padrao;
        const corNivel = (pct) => pct < 25 ? '#dc2626' : (pct < 50 ? '#f59e0b' : '#16a34a');

        function pintarTanque(id, percentual, cor, rotulo) {
            const tanque = document.getElementById(id);
            tanque.style.setProperty('--v', Math.max(0, Math.min(100, Number(percentual) || 0)) + '%');
            tanque.style.setProperty('--tank-cor', cor);
            tanque.setAttribute('aria-label', rotulo);
        }

        async function buscar(url) {
            const resposta = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!resposta.ok) throw new Error('HTTP ' + resposta.status);
            return (await resposta.json()).data;
        }

        // ---------------------------------------------------- situação atual
        // Tudo que vem da balança e do sensor de distância: status, nível do
        // cocho medido, cards de peso/distância e lista de sensores. Usado
        // também pela atualização ao vivo (a cada 2 s).
        function renderizarSensores(d) {
            const status = d.status_sensor || 'sem_leitura';
            const elStatus = document.getElementById('detStatus');
            elStatus.className = 'det-status ' + status;
            elStatus.textContent = 'Sensor ' + (ROTULO_STATUS[status] || status).toLowerCase()
                + (d.data_hora_leitura ? ' · ' + dataHora(d.data_hora_leitura) : '');

            // A API manda 0 quando ainda não existe leitura; aqui isso vira
            // "—" para não parecer uma medição real de cocho vazio.
            const desatualizada = status === 'offline' || status === 'erro';
            const cs = d.cocho_sensor || { quantidade_kg: 0, percentual: 0, tem_leitura: false };
            campo('cocho_quantidade', cs.tem_leitura ? kg(cs.quantidade_kg) : '—');
            campo('cocho_sub', cs.tem_leitura
                ? 'de ' + kg(d.capacidade_total) + ' de capacidade · leitura de ' + dataHora(cs.data_hora)
                    + (desatualizada ? '<br><strong>Leitura desatualizada</strong> — sensor sem comunicação' : '')
                : 'Sem leitura do sensor ainda — de ' + kg(d.capacidade_total) + ' de capacidade'
                    + (d.quantidade_atual > 0 ? '<br>Abastecido pelo estoque: <strong>' + kg(d.quantidade_atual) + '</strong>' : ''));
            campo('cocho_percentual', cs.tem_leitura ? num(cs.percentual, 0) + '%' : '—');
            pintarTanque('detTanque', cs.tem_leitura ? cs.percentual : 0, corNivel(cs.percentual),
                cs.tem_leitura ? 'Nível do cocho medido pelo sensor: ' + num(cs.percentual, 1) + '%' : 'Nível do cocho: sem leitura do sensor');

            const sensores = d.sensores || [];
            const dist = sensores.find((s) => s.tipo === 'distancia' && s.ativo);
            const bal = sensores.find((s) => s.tipo === 'balanca' && s.ativo);
            // Situação curta do sensor para o subtítulo dos cards.
            const situacao = (s) => (simulado(s) ? ' · simulado' : '')
                + (s.status === 'online' ? '' : ' · ' + (ROTULO_STATUS[s.status] || s.status).toLowerCase()
                    + (s.ultima_leitura_em && s.status !== 'sem_leitura' ? ' desde ' + dataHora(s.ultima_leitura_em) : ''));

            campo('distancia_cm', dist && dist.ultima_leitura_em ? num(d.distancia_cm, 1) + ' cm' : '—');
            campo('distancia_sub', dist
                ? esc(dist.codigo) + (dist.ultima_leitura_em ? ' · nível físico ' + num(d.nivel_sensor_percentual, 0) + '%' : '')
                    + (dist.calibrado ? '' : ' · sem calibração') + situacao(dist)
                : 'Nenhum sensor cadastrado');

            campo('peso_kg', bal && bal.ultima_leitura_em ? kg(d.peso_kg) : '—');
            campo('peso_sub', bal ? esc(bal.codigo) + situacao(bal) : 'Nenhuma balança cadastrada');

            document.getElementById('detSimulado').hidden = !sensores.some((s) => s.ativo && simulado(s));

            document.getElementById('detSensores').innerHTML = sensores.length
                ? sensores.map((s) => '<div class="det-sensor">'
                    + '<div class="det-sensor-ico ' + esc(s.tipo) + '"><i class="fa-solid ' + (s.tipo === 'balanca' ? 'fa-weight-scale' : 'fa-ruler-vertical') + '" aria-hidden="true"></i></div>'
                    + '<div><strong>' + esc(s.codigo) + '</strong>'
                    + '<span>' + (s.tipo === 'balanca' ? 'Balança / célula de carga' : 'Sensor de distância') + (s.ativo ? '' : ' · desativado') + (simulado(s) ? ' · simulado' : '') + '</span>'
                    + (s.tipo === 'distancia' ? '<span>Calibração: ' + (s.calibrado ? 'vazio ' + num(s.distancia_vazio_cm, 1) + ' cm · cheio ' + num(s.distancia_cheio_cm || 0, 1) + ' cm' : 'pendente') + '</span>' : '')
                    + '<span>Última leitura: ' + dataHora(s.ultima_leitura_em) + '</span>'
                    + '<span class="det-status ' + esc(s.status) + '">' + esc(ROTULO_STATUS[s.status] || s.status) + '</span>'
                    + '</div></div>').join('')
                : '<p class="pg-sub">Nenhum sensor cadastrado. Em "Gestão de Cochos → Editar", informe o código do sensor de distância e/ou da balança que o ESP32 envia.</p>';
        }

        function renderizarResumo(d) {
            renderizarSensores(d);
            const status = d.status_sensor || 'sem_leitura';
            const cs = d.cocho_sensor || { quantidade_kg: 0, percentual: 0, tem_leitura: false };

            // Armazém → total disponível nas rações da fazenda (mesma regra
            // do card "Quantidade no Armazém" da tela de Estoque).
            const arm = d.armazem || {};
            campo('armazem_quantidade', kg(arm.quantidade_total_kg));
            campo('armazem_sub', arm.capacidade_total_kg > 0
                ? 'de ' + kg(arm.capacidade_total_kg) + ' de capacidade · ' + arm.total_racoes + ' ração(ões)'
                : (arm.total_racoes ? 'Cadastre o Estoque Total das rações' : 'Nenhuma ração cadastrada nesta fazenda'));
            campo('armazem_percentual', arm.percentual != null ? arm.percentual + '%' : '—');
            pintarTanque('detTanqueArmazem', arm.percentual || 0, corNivel(arm.percentual || 0), 'Ocupação do armazém: ' + (arm.percentual != null ? arm.percentual + '%' : 'indisponível'));

            const rl = arm.racao_lote;
            const rotuloEstoque = { ok: 'Estoque OK', baixo: 'Estoque baixo', esgotado: 'Esgotado' };
            document.getElementById('detRacaoLote').innerHTML = rl
                ? '<div class="det-racao-lote-grid">'
                    + '<span><strong>' + esc(rl.nome_racao) + '</strong> · ' + kg(rl.estoque_kg)
                    + (rl.estoque_total_kg ? ' de ' + kg(rl.estoque_total_kg) : '') + '</span>'
                    // Cor pelo status (que considera os dias restantes), não só
                    // pela ocupação — senão "Estoque baixo" aparecia com barra verde.
                    + (rl.percentual != null ? '<span class="pg-meter" style="--meter-cor:' + ({ esgotado: '#dc2626', baixo: '#f59e0b' }[rl.status_estoque] || corNivel(rl.percentual)) + '" role="img" aria-label="Estoque da ração: ' + rl.percentual + '%"><span style="--v:' + rl.percentual + '%"></span></span>' : '')
                    + '<span>' + esc(rotuloEstoque[rl.status_estoque] || rl.status_estoque)
                    + (rl.dias_restantes != null ? ' · ' + rl.dias_restantes + ' dia(s) de estoque' : '') + '</span>'
                    + '</div>'
                : 'Nenhuma ração vinculada ao lote deste cocho — vincule uma no Plano Alimentar para acompanhar o estoque que abastece este cocho.';

            campo('capacidade_total', kg(d.capacidade_total));
            campo('espaco_livre', 'Espaço livre: ' + kg(d.espaco_livre_kg));

            const ind = d.indicadores || {};
            campo('ind_percentual', cs.tem_leitura
                ? num(cs.percentual, 1) + '%<small>' + kg(cs.quantidade_kg) + ' de ' + kg(d.capacidade_total) + '</small>'
                : '—<small>Sem leitura do sensor · capacidade ' + kg(d.capacidade_total) + '</small>');
            campo('ind_consumo_hoje', kg(ind.consumo_hoje_kg));
            campo('ind_consumo_7', kg(ind.consumo_7_dias_kg));
            campo('ind_consumo_medio', ind.consumo_medio_diario_kg != null
                ? kg(ind.consumo_medio_diario_kg) + '<small>por dia</small>'
                : '—<small>Leituras insuficientes (mín. 6 h)</small>');
            campo('ind_previsao', ind.previsao_esvaziamento
                ? dataHora(ind.previsao_esvaziamento.data_hora) + '<small>em ~' + num(ind.previsao_esvaziamento.horas, 1) + ' h no ritmo atual</small>'
                : '—<small>Sem consumo suficiente para prever</small>');
            campo('ind_reposicao', ind.ultima_reposicao
                ? dataHora(ind.ultima_reposicao.data_hora) + '<small>' + kg(ind.ultima_reposicao.quantidade_kg) + ' · ' + (ind.ultima_reposicao.origem === 'abastecimento' ? 'abastecimento pelo estoque' : 'detectada pela balança') + '</small>'
                : '—<small>Nenhuma reposição registrada</small>');
            campo('ind_leitura', ind.ultima_leitura
                ? dataHora(ind.ultima_leitura.data_hora) + '<small>' + esc(ind.tempo_desde_ultima_leitura || '') + ' · ' + esc(ind.ultima_leitura.sensor_codigo || ind.ultima_leitura.tipo_sensor) + '</small>'
                : '—<small>Ainda não existe leitura do sensor</small>');
            campo('ind_status', esc(ROTULO_STATUS[status] || status) + '<small>Sem comunicação após ' + esc(d.minutos_offline) + ' min sem leitura</small>');

            document.getElementById('detAlertas').innerHTML = (d.alertas || []).map((a) =>
                '<div class="det-alerta ' + esc(a.prioridade) + '"><i class="fa-solid ' + (ICONE_ALERTA[a.prioridade] || 'fa-circle-info') + '" aria-hidden="true"></i><span>' + esc(a.mensagem) + '</span></div>'
            ).join('');

        }

        // ------------------------------------------------------- histórico
        // suave: só troca os números dos gráficos já desenhados (ao vivo),
        // com transição curta — a curva e as barras andam a cada leitura;
        // muda de período/tema = recria.
        function renderizarHistorico(h, suave = false) {
            ultimoHistorico = h;
            const rotulos = h.pontos.map((p) => p.rotulo);
            const quantidades = h.pontos.map((p) => p.quantidade_kg);
            const temQuantidade = quantidades.some((v) => v !== null);
            const temMovimento = h.pontos.some((p) => p.consumo_kg > 0);
            // "Hoje" vem de 5 em 5 minutos (até 288 pontos): sem bolinha em
            // cada ponto e barras finas, senão o gráfico vira uma faixa.
            const denso = h.pontos.length > 60;
            // Consumo: até 5 kg na escala (cocho pequeno), crescendo com a
            // maior barra, mas nunca acima da capacidade — num cocho de 200 kg
            // a escala cheia deixava as barras de 1–2 kg invisíveis.
            const maiorConsumo = Math.max(0, ...h.pontos.map((p) => p.consumo_kg || 0));
            const tetoConsumo = Math.min(h.capacidade_total || 5, Math.max(5, maiorConsumo * 1.2));

            document.getElementById('graficoNivelVazio').classList.toggle('visivel', !temQuantidade);
            document.getElementById('graficoConsumoVazio').classList.toggle('visivel', !temMovimento);

            const texto = css('--text-secundario', '#64748b');
            const grade = document.body.classList.contains('dark-theme') ? 'rgba(74, 222, 128, 0.10)' : '#eef2f6';
            const verde = css('--verde-secundario', '#16a765');
            const eixos = {
                x: { grid: { display: false }, ticks: { color: texto, font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                // Cocho pequeno (ex.: 20 kg): escala com frações de kg.
                y: { beginAtZero: true, grid: { color: grade }, ticks: { color: texto, font: { size: 10 }, maxTicksLimit: 6, callback: (v) => num(v, Number.isInteger(v) ? 0 : 1) + ' kg' } },
            };

            if (suave && graficoNivel && graficoConsumo) {
                graficoNivel.data.labels = rotulos;
                graficoNivel.data.datasets[0].data = quantidades;
                graficoNivel.data.datasets[0].pointRadius = denso ? 0 : 2;
                graficoNivel.data.datasets[1].data = h.pontos.map(() => h.capacidade_total);
                graficoNivel.options.scales.y.suggestedMax = h.capacidade_total;
                graficoNivel.update();
                graficoConsumo.data.labels = rotulos;
                graficoConsumo.data.datasets[0].data = h.pontos.map((p) => p.consumo_kg);
                graficoConsumo.options.scales.y.suggestedMax = tetoConsumo;
                graficoConsumo.update();
            } else {
            if (graficoNivel) graficoNivel.destroy();
            graficoNivel = new Chart(document.getElementById('graficoNivel'), {
                type: 'line',
                data: {
                    labels: rotulos,
                    datasets: [
                        {
                            label: 'Quantidade (kg)',
                            data: quantidades,
                            borderColor: verde,
                            backgroundColor: verde + '33',
                            fill: true,
                            tension: 0.3,
                            spanGaps: false,
                            pointRadius: denso ? 0 : 2,
                            pointHitRadius: 6,
                        },
                        {
                            label: 'Capacidade',
                            data: h.pontos.map(() => h.capacidade_total),
                            borderColor: '#2563eb',
                            borderDash: [6, 6],
                            borderWidth: 1.5,
                            pointRadius: 0,
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { color: texto, usePointStyle: true } },
                        tooltip: { callbacks: { label: (c) => c.dataset.label + ': ' + (c.raw == null ? 'sem leitura' : kg(c.raw)) } },
                    },
                    scales: { ...eixos, y: { ...eixos.y, suggestedMax: h.capacidade_total } },
                },
            });

            if (graficoConsumo) graficoConsumo.destroy();
            graficoConsumo = new Chart(document.getElementById('graficoConsumo'), {
                type: 'bar',
                data: {
                    labels: rotulos,
                    datasets: [
                        { label: 'Consumo (kg)', data: h.pontos.map((p) => p.consumo_kg), backgroundColor: '#f97316', borderRadius: denso ? 2 : 6, maxBarThickness: 22 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: texto, usePointStyle: true } }, tooltip: { callbacks: { label: (c) => c.dataset.label + ': ' + kg(c.raw) } } },
                    scales: { ...eixos, y: { ...eixos.y, suggestedMax: tetoConsumo } },
                },
            });
            }

            document.getElementById('detTotais').innerHTML =
                '<span>Consumo no período: <strong>' + kg(h.totais.consumo_kg) + '</strong></span>'
                + '<span>Reposição: <strong>' + kg(h.totais.reposicao_kg) + '</strong></span>'
                + '<span>Leituras: <strong>' + h.totais.leituras + '</strong></span>';

            document.getElementById('detHistorico').innerHTML = h.leituras.length
                ? h.leituras.map((l) => {
                    const v = l.variacao_kg;
                    const classe = v == null ? '' : (v < 0 ? 'neg' : (v > 0 ? 'pos' : ''));
                    return '<tr>'
                        + '<td>' + dataHora(l.data_hora) + '</td>'
                        + '<td>' + esc(l.sensor_codigo || '') + ' <small>(' + (l.tipo_sensor === 'balanca' ? 'balança' : 'distância') + ')</small></td>'
                        + '<td>' + (l.peso_kg != null ? kg(l.peso_kg) : '—') + '</td>'
                        + '<td>' + (l.distancia_cm != null ? num(l.distancia_cm, 1) + ' cm' : '—') + '</td>'
                        + '<td>' + (l.nivel_percentual != null ? num(l.nivel_percentual, 1) + '%' : '—') + '</td>'
                        + '<td>' + (l.quantidade_kg != null ? kg(l.quantidade_kg) : '—') + '</td>'
                        + '<td class="' + classe + '">' + (v != null ? (v > 0 ? '+' : '') + kg(v) : '—') + '</td>'
                        + '</tr>';
                }).join('')
                : '<tr><td colspan="7" class="det-tabela-vazia">Ainda não existe leitura do sensor neste período.</td></tr>';
        }

        async function atualizar() {
            const botao = document.getElementById('detAtualizar');
            botao.classList.add('girando');
            try {
                const [resumo, historico] = await Promise.all([
                    buscar(urlIot),
                    buscar(urlHistorico + '?periodo=' + encodeURIComponent(periodo)),
                ]);
                renderizarResumo(resumo);
                renderizarHistorico(historico);
            } catch (e) {
                const elStatus = document.getElementById('detStatus');
                elStatus.className = 'det-status erro';
                elStatus.textContent = 'Não foi possível carregar os dados';
            } finally {
                botao.classList.remove('girando');
            }
        }

        document.querySelectorAll('[data-periodo]').forEach((botao) => {
            botao.addEventListener('click', () => {
                periodo = botao.dataset.periodo;
                document.querySelectorAll('[data-periodo]').forEach((b) => {
                    b.classList.toggle('active', b === botao);
                    b.setAttribute('aria-pressed', b === botao ? 'true' : 'false');
                });
                atualizar();
            });
        });

        document.getElementById('detAtualizar').addEventListener('click', atualizar);

        // Cores dos gráficos acompanham o tema (claro/escuro/alto contraste).
        ['chkDarkMode', 'chkHighContrast'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', () => setTimeout(() => { if (ultimoHistorico) renderizarHistorico(ultimoHistorico); }, 60));
        });

        // ------------------------------------------------------- ao vivo
        // A cada 5 s busca de novo e, SÓ se chegou leitura nova da balança ou
        // do sensor de distância (ou o status de algum sensor mudou), atualiza
        // cards dos sensores, tabela de leituras e gráficos — sem recarregar
        // o resto da tela. Pausa com a aba escondida; uma busca por vez.
        let assinaturaAoVivo = '';
        let buscandoAoVivo = false;
        const assinatura = (r, h) => [
            r.data_hora_leitura, r.peso_kg, r.distancia_cm, r.status_sensor, periodo,
            (r.sensores || []).map((s) => s.codigo + s.status + s.ultima_leitura_em).join(','),
            h.totais && h.totais.leituras,
        ].join('|');

        async function atualizarAoVivo() {
            if (document.hidden || buscandoAoVivo) return;
            buscandoAoVivo = true;
            const indicador = document.getElementById('detAoVivo');
            try {
                const periodoPedido = periodo;
                const [resumo, historico] = await Promise.all([
                    buscar(urlIot),
                    buscar(urlHistorico + '?periodo=' + encodeURIComponent(periodoPedido)),
                ]);
                if (periodoPedido !== periodo) return; // trocou de período no meio
                indicador.classList.remove('pausado');
                indicador.title = 'Atualizado às ' + new Date().toLocaleTimeString('pt-BR');
                const nova = assinatura(resumo, historico);
                if (nova === assinaturaAoVivo) return;
                assinaturaAoVivo = nova;
                renderizarSensores(resumo);
                renderizarHistorico(historico, true);
            } catch (e) {
                indicador.classList.add('pausado');
                indicador.title = 'Sem conexão — tentando de novo em 2 s';
            } finally {
                buscandoAoVivo = false;
            }
        }

        atualizar();
        setInterval(atualizarAoVivo, INTERVALO_AO_VIVO);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) atualizarAoVivo(); });
    })();
</script>
@endpush
