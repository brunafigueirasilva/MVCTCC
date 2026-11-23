@extends('layouts.app')

@section('title', 'AgriFarm | Plano Alimentar')

@push('scripts')
<script src="{{ asset('js/vendor/chart.min.js') }}"></script>
@endpush

@push('styles')
    <style>
        /* ==========================================================================
           RESET & VARIÁVEIS DE ESTILO PREMIUM (FOTO DE ALTA FIDELIDADE)
           ========================================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /* Mesma fonte do Dashboard e das demais páginas — esta era a
               única tela do sistema usando "Inter" (com import próprio do
               Google Fonts), o que fazia a sidebar e todo o resto do
               conteúdo parecerem "de outro sistema" ao lado das outras
               telas. */
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }


        /* Some visualmente mas continua legível por leitor de tela — usado
           em legendas/rótulos que só existem pra dar contexto a quem não
           enxerga a tela (ex.: legenda de tabela, título de seção). */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        :root {
            --bg-global: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --brand-green-dark: var(--verde-principal, #0e4a27);
            --brand-green-gradient: #123f22;
            --brand-green-light: #0f172a;
            --chart-blue-light: #bfdbfe;
            --chart-blue-dark: #1d4ed8;
            --chart-red: #ef4444;
            --chart-green: #15803d;
            --chart-grid: #eef2f6;
            --chart-tick: #94a3b8;
            --success-bg: var(--verde-claro, #e8f5ee);
            --success-text: var(--verde-texto, #0e4a27);
            --warning-bg: #fef3c7;
            --warning-text: #b45309;
            --danger-bg: #fee2e2;
            --danger-text: #b91c1c;
            --info-bg: #e0f2fe;
            --info-text: #0369a1;
            --surface-2: #f1f5f9;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --sidebar-width: 260px;

            /* Mesmas variáveis de navbar/sidebar do Dashboard, para os dois
               ficarem visualmente idênticos. */

            /* Painel de Acessibilidade (componente compartilhado, ver
               partials/acessibilidade.blade.php) — sem essas variáveis o
               painel ficava sempre no visual claro padrão, mesmo com o tema
               escuro ou o alto contraste ativados nesta página. */

            /* Paleta vibrante — usada nos gradientes dos cards de KPI, nos
               gráficos (barras/rosca/linha) e nos badges/ícones "3D" da
               Análise Inteligente. Tons validados para leitura em conjunto
               (sem duas cores próximas demais), misturando o verde da marca
               com azul, laranja, roxo e ciano complementares. */
            --viz-esmeralda: #10b981;
            --viz-esmeralda-forte: #059669;
            --viz-lima: #84cc16;
            --viz-azul: #2563eb;
            --viz-ceu: #0ea5e9;
            --viz-ciano: #0891b2;
            --viz-laranja: #f97316;
            --viz-ambar: #f59e0b;
            --viz-roxo: #7c3aed;
            --viz-magenta: #db2777;
            --viz-vermelho: #e11d48;
            --title-rico: #0f3d24;
        }

        body {
            background:
                radial-gradient(1100px 560px at 8% -12%, color-mix(in srgb, var(--verde-principal, #0e4a27) 7%, transparent), transparent 60%),
                radial-gradient(900px 480px at 100% 0%, rgba(37, 99, 235, 0.05), transparent 55%),
                radial-gradient(700px 500px at 50% 100%, rgba(245, 158, 11, 0.04), transparent 60%),
                var(--bg-global);
            color: var(--text-main);
            overflow-x: hidden;
        }

        body.dark-theme {
            background:
                radial-gradient(1100px 560px at 8% -12%, color-mix(in srgb, var(--verde-secundario, #16a765) 7%, transparent), transparent 60%),
                radial-gradient(900px 480px at 100% 0%, rgba(14, 165, 233, 0.05), transparent 55%),
                var(--bg-global);
        }
        body.high-contrast { background: var(--bg-global); }

        .sidebar, .navbar, .main {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, filter 0.25s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease;
        }

        /* Quando o painel de acessibilidade está aberto, desfoca navbar,
           sidebar e conteúdo principal (mesmo comportamento do Dashboard). */
        body.acc-modal-open .sidebar,
        body.acc-modal-open .navbar,
        body.acc-modal-open .main {
            filter: blur(6px);
            pointer-events: none;
        }

        /* Fundo escurecido do menu mobile (fecha a sidebar ao clicar fora),
           igual ao Dashboard. */

        @media (max-width: 992px) {
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
        }

        .ca-section-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--title-rico, #0f3d24);
    margin: 4px 0 2px;
}

.ca-section-subtitle {
    font-size: 0.8125rem;
    color: var(--text-muted, #64748b);
    margin: 0 0 18px;
}

/* O wrapper (#corpoConversoesAlimentares) e o container das extras
   carregadas via AJAX (#conversoesExtras) ficam "invisíveis" pro layout
   (display:contents) — assim os .ca-container-card de dentro deles viram
   itens diretos do grid abaixo, lado a lado com o card "Ver todas", em vez
   de empilhar dentro de uma única célula. */
.ca-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr));
    gap: 20px;
    margin-bottom: 8px;
}

#corpoConversoesAlimentares,
#conversoesExtras {
    display: contents;
}

#conversoesExtras[hidden] {
    display: none;
}

.ca-container-card {
    background: var(--card-bg, #ffffff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 16px;
    padding: 20px 22px 22px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-width: 0;
    overflow: hidden;
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.ca-container-card:hover {
    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.ca-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
}

.ca-header-info {
    min-width: 0;
    flex: 1;
}

.ca-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-main, #1e293b);
    margin: 0;
    /* Nome do lote inteiro (quebra linha) — antes era cortado com "…". */
    overflow-wrap: anywhere;
}

.ca-subtitle {
    font-size: 0.75rem;
    color: var(--text-muted, #64748b);
    margin-top: 2px;
    overflow-wrap: anywhere;
}

.ca-header .status-badge {
    flex-shrink: 0;
}

.ca-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px 18px;
}

.ca-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    min-width: 0;
}

/* Selo "3D": gradiente diagonal + brilho interno + glow — mesmo padrão
   usado no dashboard (.premium-card-icon-zone) e nos ícones dos cards de
   KPI do Relatórios (.stat-card-icon). */
.ca-icon-wrapper {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9375rem;
    flex-shrink: 0;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
}

.ca-icon-wrapper .text-green { color: #ffffff; }

/* Setas de variação dos cards de Conversão Alimentar (partials/conversao-
   alimentar-cards.blade.php) — mesma cor usada no Relatório Avançado. */
.trend-up { color: var(--verde-texto, #0e4a27); }
.trend-down { color: #dc2626; }
.trend-neutral { color: #94a3b8; }

body.high-contrast .trend-up,
body.high-contrast .trend-down,
body.high-contrast .trend-neutral {
    color: #ffff00 !important;
}

/* ==========================================================================
   FAIXA DE INDICADORES (topo da página + destaques de Conversão Alimentar)
   — mesmo componente .stat-card do Relatório Avançado, pra manter a
   identidade visual entre as duas telas.
   ========================================================================== */
.stat-cards-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 25px;
}

.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color, #e2e8f0);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-width: 0;
    position: relative;
    overflow: hidden;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 22px -8px rgba(15, 23, 42, 0.16); }

.stat-card--verde { background: linear-gradient(160deg, color-mix(in srgb, var(--verde-principal, #0e4a27) 12%, transparent), transparent 60%), var(--card-bg); }
.stat-card--ambar { background: linear-gradient(160deg, rgba(249, 115, 22, 0.12), transparent 60%), var(--card-bg); }
.stat-card--azul  { background: linear-gradient(160deg, rgba(37, 99, 235, 0.12), transparent 60%), var(--card-bg); }
.stat-card--ciano { background: linear-gradient(160deg, rgba(8, 145, 178, 0.12), transparent 60%), var(--card-bg); }
.stat-card--roxo  { background: linear-gradient(160deg, rgba(124, 58, 237, 0.12), transparent 60%), var(--card-bg); }

.stat-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    color: #ffffff;
}

.stat-card-label {
    font-size: 0.75rem;
    color: var(--text-muted, #64748b);
    font-weight: 600;
}

.stat-card-valor {
    font-size: 1.375rem;
    font-weight: 800;
    color: var(--text-main, #1e293b);
    overflow-wrap: anywhere;
}

.stat-card-trend {
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.icone-verde {
    background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
    box-shadow: 0 6px 14px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
}
.icone-ambar {
    background: linear-gradient(145deg, #fbbf24, #f97316);
    box-shadow: 0 6px 14px -4px rgba(217, 119, 6, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
}
.icone-azul {
    background: linear-gradient(145deg, #60a5fa, #2563eb);
    box-shadow: 0 6px 14px -4px rgba(37, 99, 235, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
}
.icone-roxo {
    background: linear-gradient(145deg, #c084fc, #7c3aed);
    box-shadow: 0 6px 14px -4px rgba(124, 58, 237, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
}
.icone-ciano {
    background: linear-gradient(145deg, #22d3ee, #0891b2);
    box-shadow: 0 6px 14px -4px rgba(8, 145, 178, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
}

body.high-contrast .stat-card,
body.high-contrast .stat-card-icon {
    background: #000000 !important;
    border: 2px solid #ffff00 !important;
    color: #ffff00 !important;
    box-shadow: none !important;
}

/* Chips de período (7/15/30/60/90 dias) da Conversão Alimentar — o estilo
   é o padrão do sistema (partials/paleta-verde); aqui só o espaçamento. */
.periodo-chips { margin-bottom: 18px; }

@media (max-width: 992px) {
    .stat-cards-row { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .stat-cards-row { grid-template-columns: 1fr; }
}

/* Paleta de Cores dos Ícones baseada na imagem */
.bg-green-light {
    background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
    box-shadow: 0 5px 12px -3px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
}
.text-green { color: var(--verde-texto, #0e4a27); }
.text-green-bold { color: var(--verde-texto, #0e4a27); font-weight: 700; }
.text-green-dark { color: var(--verde-texto, #0e4a27); }
.bg-transparent { background-color: transparent; }

.bg-green-circle {
    background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
    box-shadow: 0 5px 12px -3px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
    border-radius: 50%;
    width: 36px;
    height: 36px;
}
.text-green-circle-icon { color: #ffffff; }

.ca-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.ca-label-flex {
    display: flex;
    align-items: center;
    gap: 5px;
}

.ca-label {
    font-size: 0.65625rem;
    color: var(--text-muted, #64748b);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    overflow-wrap: break-word;
}

.ca-value {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--text-main, #1e293b);
    margin-top: 3px;
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    overflow-wrap: break-word;
}

/* Métrica principal (conversão alimentar) — destacada em uma faixa própria
   no rodapé do card, separada das demais pra puxar o olho primeiro, já que
   é o indicador mais importante da seção. */
.ca-hero {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--success-bg, var(--verde-claro, #e8f5ee));
    border-radius: 12px;
    padding: 12px 16px;
    min-width: 0;
}

.ca-hero-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: var(--success-text, var(--verde-texto, #0e4a27));
    flex-shrink: 0;
}

.ca-hero-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.ca-hero-label {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--success-text, var(--verde-texto, #0e4a27));
    opacity: 0.85;
}

.ca-hero-value {
    font-size: 1.1875rem;
    font-weight: 800;
    color: var(--success-text, var(--verde-texto, #0e4a27));
    margin-top: 2px;
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    overflow-wrap: break-word;
}

/* Conversão Alimentar REAL (kg ração / kg ganho) — mesmo layout do
   .ca-hero (proxy por kg vivo), em tom azul/ciano pra diferenciar visualmente
   das duas métricas, já que uma é proxy e a outra é medida de verdade. */
.ca-hero-real {
    background: var(--info-bg, #e0f2fe);
}
.ca-hero-real .ca-hero-icon { color: var(--info-text, #0369a1); }
.ca-hero-real .ca-hero-label { color: var(--info-text, #0369a1); }
.ca-hero-real .ca-hero-value { color: var(--info-text, #0369a1); }

.ca-hero-sub {
    font-size: 0.688rem;
    font-weight: 500;
    color: var(--info-text, #0369a1);
    opacity: 0.85;
    margin-top: 2px;
}

/* Sem pesagem suficiente ainda: mesmo cartão, em tom neutro — não é um
   erro, só falta o usuário registrar a segunda pesagem do lote. */
.ca-hero-vazio { background: var(--surface-2, #f1f5f9); }
.ca-hero-vazio .ca-hero-icon,
.ca-hero-vazio .ca-hero-label,
.ca-hero-vazio .ca-hero-value,
.ca-hero-vazio .ca-hero-sub { color: var(--text-muted, #64748b); }

/* Origem do consumo da conversão: ícone + texto (medido / aproximado /
   estimado) — o produtor sabe se o número é da fazenda ou do plano. */
.ca-fonte {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    width: fit-content;
    margin-top: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 0.6875rem;
    font-weight: 700;
    line-height: 1.35;
}
.ca-fonte-sensor { background: #dcfce7; color: #166534; }
.ca-fonte-fornecido { background: #fef3c7; color: #92400e; }
.ca-fonte-plano { background: #e2e8f0; color: #334155; }
body.dark-theme .ca-fonte-sensor { background: rgba(74, 222, 128, 0.15); color: #86efac; }
body.dark-theme .ca-fonte-fornecido { background: rgba(251, 191, 36, 0.15); color: #fcd34d; }
body.dark-theme .ca-fonte-plano { background: rgba(148, 163, 184, 0.18); color: #cbd5e1; }
body.high-contrast .ca-fonte { background: #000 !important; color: #ffff00 !important; border: 1px solid #ffff00; }

.ca-btn-pesagem {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 10px;
    border: 1px solid var(--border-color, #e2e8f0);
    background: transparent;
    color: var(--text-main, #1e293b);
    font-size: 0.813rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
}

.ca-btn-pesagem:hover {
    background: var(--info-bg, #e0f2fe);
    border-color: var(--info-text, #0369a1);
    color: var(--info-text, #0369a1);
}

.tooltip-icon {
    /* Agora é um <button> de verdade (focável por teclado, com nome
       acessível via aria-label) em vez de um <i> clicável — este reset
       devolve a aparência de ícone simples que tinha antes. */
    background: none;
    border: none;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    color: #94a3b8;
    cursor: pointer;
    transition: color 0.2s;
}

.tooltip-icon:hover {
    color: #475569;
}

.tooltip-icon:focus-visible {
    outline: 2px solid var(--verde-ativo, #087a47);
    outline-offset: 2px;
    border-radius: 4px;
}

/* Popover de explicação, aberto ao clicar/tocar no botão de interrogação —
   um elemento único, reaproveitado via JS por delegação de evento (funciona
   também nos cards carregados depois via AJAX em "Ver todas as
   conversões", sem precisar religar handlers). */
.tooltip-popover {
    position: absolute;
    z-index: 60;
    max-width: 260px;
    background: #1e293b;
    color: #f1f5f9;
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1.5;
    padding: 10px 14px;
    border-radius: 10px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    display: none;
}

.tooltip-popover.is-open {
    display: block;
}

body.high-contrast .tooltip-popover {
    background: #000000 !important;
    color: #ffff00 !important;
    border: 2px solid #ffff00 !important;
}

/* Card/CTA "Ver todas as conversões" — mesmo grid dos cards de lote, ocupa
   a última célula e mantém a altura visualmente alinhada aos demais. */
.ca-ver-todas-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: var(--card-bg, #ffffff);
    border: 1.5px dashed var(--border-color, #cbd5e1);
    border-radius: 16px;
    padding: 20px;
    min-height: 100%;
    cursor: pointer;
    color: var(--text-muted, #64748b);
    font-size: 0.8125rem;
    font-weight: 600;
    text-align: center;
    transition: border-color 0.2s ease, background 0.2s ease, color 0.2s ease;
}

.ca-ver-todas-card:hover,
.ca-ver-todas-card:focus-visible {
    border-color: var(--verde-ativo, #087a47);
    background: var(--success-bg, var(--verde-claro, #e8f5ee));
    color: var(--verde-texto, #0e4a27);
    outline: none;
}

.ca-ver-todas-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--success-bg, var(--verde-claro, #e8f5ee));
    color: var(--verde-texto, #0e4a27);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.0625rem;
    transition: transform 0.2s ease;
}

.ca-ver-todas-card:hover .ca-ver-todas-icon {
    transform: scale(1.08);
}

/* Responsividade para telas menores */
@media (max-width: 480px) {
    .ca-grid { grid-template-columns: 1fr; }
}

        /* ==========================================================================
           NAVBAR SUPERIOR (FIXA)
           ========================================================================== */



        .toggle-menu-btn {
            background: #f1f5f9;
            border: 1px solid var(--surface-border);
            color: var(--brand-green-light);
            font-size: 1.063rem;
            padding: 9px 13px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toggle-menu-btn:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
            color: #0f172a;
            transform: translateY(-1px);
        }

        /* Logo diretamente sobre o fundo escuro da navbar, sem caixa/quadrado
           atrás — igual ao Dashboard. */




        .user-profile:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-green-light);
            font-size: 1rem;
            border: 1px solid var(--surface-border);
            flex-shrink: 0;
        }


        .user-name {
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .user-role {
            color: #a7f3d0;
            font-size: 0.6875rem;
            font-weight: 500;
        }


        .navbar-logout {
            text-decoration: none;
            color: #dc2626;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.12);
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }



        /* Botão de acessibilidade inserido dentro da navbar (ver partials/acessibilidade). */
        .navbar-right .accessibility-trigger {
            background: #f1f5f9;
            color: var(--brand-green-light);
            border: 1px solid var(--surface-border);
        }
        .navbar-right .accessibility-trigger:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* ==========================================================================
           SIDEBAR LATERAL
           ========================================================================== */





        @keyframes menuIconBounce {
            0% { transform: translateY(0) scale(1); }
            30% { transform: translateY(-9px) scale(1.16); }
            50% { transform: translateY(1px) scale(0.95); }
            70% { transform: translateY(-4px) scale(1.06); }
            100% { transform: translateY(0) scale(1); }
        }




        html.ui-movimento-reduzido { /* antes: @media (prefers-reduced-motion) — agora só a opção do painel desliga */
            .menu a, .menu i { transition: background-color 0.18s ease, color 0.18s ease; }
            .menu a:hover { transform: none; }
            .menu a:hover i { animation: none; }
        }

        .footer-right {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid var(--surface-border);
            text-align: center;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.35);
            letter-spacing: 0.5px;
        }

        /* ==========================================================================
           ÁREA PRINCIPAL & GRID DE DASHBOARD HIGH FIDELITY
           ========================================================================== */
        .main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 40px;
            padding-top: 20px;
        }


        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        /* Barra "Unidade Produtiva" — mesmo componente/comportamento do
           Dashboard (ver dashboard.blade.php .fazenda-selector-bar), só
           adaptado aos nomes de variável desta página (--card-bg/
           --text-main/--text-muted/--border-color em vez de --text-
           principal/--text-secundario/--surface-muted-*). Fica logo no
           topo do conteúdo, antes de qualquer cartão/tabela/gráfico —
           tudo abaixo dela passa a refletir só a fazenda escolhida aqui. */
        .fazenda-selector-bar {
            background: var(--card-bg);
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color, #e2e8f0);
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .fazenda-selector-bar-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .select-fazenda {
            flex: 1;
            min-width: 200px;
            max-width: 400px;
            padding: 11px 16px;
            border-radius: 10px;
            border: 1px solid var(--border-color, #e2e8f0);
            outline: none;
            background: var(--card-bg);
            color: var(--text-main);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
        }

        .select-fazenda:focus {
            border-color: var(--verde-ativo, #087a47);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--verde-principal, #0e4a27) 10%, transparent);
        }

        .selector-hint {
            font-size: 0.781rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-global);
            border: 1px dashed var(--border-color, #e2e8f0);
            padding: 8px 14px;
            border-radius: 10px;
        }

        .selector-hint i { color: #f59e0b; }

        @media (max-width: 576px) {
            .fazenda-selector-bar { flex-direction: column; align-items: stretch; padding: 16px; }
            .select-fazenda { max-width: 100%; }
            .selector-hint { width: 100%; }
        }

        .header h1 {
            font-size: 1.75rem;
            color: #0f172a;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .header p {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .btn-add {
            background: var(--verde-principal, #0e4a27);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 20%, transparent);
        }

        .btn-add:hover {
            background: var(--verde-hover, #0b3d21);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px color-mix(in srgb, var(--verde-principal, #0e4a27) 30%, transparent);
        }

        .alerta-flash {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 0.875rem;
        }

        .alerta-flash i { margin-top: 2px; flex-shrink: 0; }
        .alerta-flash ul { margin: 6px 0 0 18px; }
        .alerta-flash strong { display: block; }

        .alerta-erro {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alerta-sucesso {
            background: var(--verde-claro, #e8f5ee);
            border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28));
            color: var(--verde-texto, #0e4a27);
        }

        /* Antes só a navbar/sidebar/menu escureciam no tema escuro (e só 2
           classes de alerta no alto contraste) — o resto da página (cards,
           tabelas, badges, modais, formulário) ficava sempre no visual
           claro, mesmo com os dois modos ativados. Agora cobre as mesmas
           variáveis e componentes do padrão usado no cadastro de
           Cocho/Lote/Fazenda. */
        body.dark-theme {
            --bg-global: #0a1810;
            --text-main: #f1f9f4;
            --text-muted: #9db8a8;
            --border-color: #2a4d38;
            --card-bg: #12251a;
            --chart-grid: rgba(74, 222, 128, 0.10);
            --chart-tick: #9db8a8;
            --title-rico: var(--verde-secundario, #16a765);
        }

        body.dark-theme .alerta-erro { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 30%, transparent); color: var(--verde-texto, #0e4a27); }

        /* Textos com cor fixa (não usavam var(--text-main)) — sem isso,
           ficavam escuros demais para ler sobre o fundo escuro. */
        body.dark-theme .header h1,
        body.dark-theme .card-title,
        body.dark-theme .ca-title,
        body.dark-theme .lote-title,
        body.dark-theme .bold-total-cell,
        body.dark-theme .progress-percent,
        body.dark-theme .metric-value,
        body.dark-theme .modal-top h2,
        body.dark-theme .smart-headline,
        body.dark-theme label {
            color: #f1f9f4;
        }

        body.dark-theme .photo-table td,
        body.dark-theme .time-weight-cell .weight,
        body.dark-theme .ca-value {
            color: #d9ede1;
        }

        body.dark-theme .ca-label { color: #9db8a8; }
        body.dark-theme .ca-divider { background-color: #2a4d38; }

        body.dark-theme .photo-table th { background: #16281c; }
        body.dark-theme .photo-table td { border-bottom-color: #1e3a2a; }

        body.dark-theme .ca-container-card,
        body.dark-theme .smart-analysis-container,
        body.dark-theme .modal-card {
            background: #12251a;
            border-color: #2a4d38;
        }

        body.dark-theme .close {
            background: #1e3a2a;
            color: #d9ede1;
        }
        body.dark-theme .close:hover {
            background: #2a4d38;
            color: #f1f9f4;
        }

        body.dark-theme .input-box input,
        body.dark-theme .input-box select {
            background: #0f1712;
            color: #ededec;
            border-color: #374151;
        }
        body.dark-theme .input-box input:focus,
        body.dark-theme .input-box select:focus {
            background: #16281c;
            border-color: var(--verde-ativo, #087a47);
        }

        body.dark-theme .select-filter,
        body.dark-theme .btn-view-more {
            background: #16281c;
            border-color: #2a4d38;
            color: #d9ede1;
        }
        body.dark-theme .btn-view-more:hover {
            background: #1e3a2a;
            border-color: #374151;
        }

        /* ALTO CONTRASTE — antes só existia uma regra "coringa" pintando todo
           texto de amarelo puro, mas nunca redefinia var(--bg-global)/
           var(--card-bg) como o tema escuro faz — fora dos cards com
           override explícito, o fundo continuava claro e o texto amarelo
           ficava quase ilegível sobre ele (e células de tabela sem <span>,
           como "Total do Dia"/"Meta", nem entravam no coringa). Agora
           redefine as mesmas variáveis usadas no resto da página, com uma
           paleta escura coesa — não um preto chapado — reservando o amarelo
           para bordas, estados e ações, em vez de pintar tudo da mesma cor. */
        body.high-contrast {
            --bg-global: #000000;
            --text-main: #ffff00;
            --text-muted: #ffff00;
            --border-color: #ffff00;
            --card-bg: #000000;
            --chart-grid: #ffff00;
            --chart-tick: #ffff00;
            --title-rico: #ffff00;

            --navbar-bg: #000000;
            --sidebar-bg: #000000;
            --surface-border: #ffff00;
            --menu-text: #ffffff;
            --menu-text-hover: #ffff00;
            --menu-hover-bg: rgba(255, 255, 0, 0.12);
            --menu-active-bg: rgba(255, 255, 0, 0.18);
            --menu-active-text: #ffff00;
            --menu-active-accent: #ffff00;

            --panel-bg: #000000;
            --panel-text: #ffffff;
            --panel-item-bg: #111111;
            --panel-btn-bg: #ffffff;
            --panel-btn-text: #000000;
            --panel-btn-hover: #ffff00;
        }

        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3, body.high-contrast h4,
        body.high-contrast p, body.high-contrast span, body.high-contrast i, body.high-contrast label,
        body.high-contrast div, body.high-contrast strong,
        body.high-contrast td, body.high-contrast th {
            color: var(--text-main) !important;
        }

        body.high-contrast .alerta-erro,
        body.high-contrast .alerta-sucesso,
        body.high-contrast .dashboard-card,
        body.high-contrast .ca-container-card,
        body.high-contrast .smart-analysis-container,
        body.high-contrast .modal-card,
        body.high-contrast .fazenda-selector-bar,
        body.high-contrast .selector-hint {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }

        body.high-contrast .select-fazenda {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }

        body.high-contrast .ca-icon-wrapper,
        body.high-contrast .bg-green-circle {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
        }

        body.high-contrast .status-badge,
        body.high-contrast .select-filter,
        body.high-contrast .btn-view-more,
        body.high-contrast .photo-table th {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }

        body.high-contrast .ca-hero,
        body.high-contrast .ca-hero-icon,
        body.high-contrast .ca-hero-real,
        body.high-contrast .ca-hero-vazio,
        body.high-contrast .ca-btn-pesagem,
        body.high-contrast .ca-ver-todas-card {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }
        body.high-contrast .ca-ver-todas-icon {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }
        body.high-contrast .ca-hero-label,
        body.high-contrast .ca-hero-value,
        body.high-contrast .ca-hero-sub {
            color: var(--text-main) !important;
        }

        body.high-contrast .photo-table td { border-bottom-color: var(--border-color) !important; }

        /* Botões primários (CTAs) — mesmo padrão contornado (fundo preto,
           texto/borda amarela) usado em todas as outras telas do sistema. */
        body.high-contrast .btn-add,
        body.high-contrast .submit-btn {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }

        body.high-contrast .close {
            background: var(--card-bg) !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .tooltip-icon {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .input-box input,
        body.high-contrast .input-box select {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast a:focus-visible,
        body.high-contrast button:focus-visible {
            outline: 3px solid #ffff00 !important;
            outline-offset: 2px !important;
        }

        /* O Grid unificado combinando a foto e a estrutura original */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--title-rico, #1e293b);
        }

        .card-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ==========================================================================
           DISTRIBUIÇÃO DE RAÇÃO (donut + legenda)
           ========================================================================== */
        .donut-layout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex: 1;
        }

        .donut-chart-box {
            width: 44%;
            max-width: 170px;
            aspect-ratio: 1 / 1;
            position: relative;
            flex-shrink: 0;
        }

        .donut-center-total {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .donut-center-total .valor {
            display: block;
            font-size: 1.0625rem;
            font-weight: 800;
            background: linear-gradient(90deg, var(--viz-azul), var(--viz-esmeralda-forte));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body.dark-theme .donut-center-total .valor {
            background: none;
            -webkit-text-fill-color: unset;
            color: var(--verde-texto, #0e4a27);
        }

        body.high-contrast .donut-center-total .valor {
            background: none !important;
            -webkit-text-fill-color: unset !important;
            color: var(--text-main) !important;
        }

        .donut-center-total .rotulo {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
        }

        .donut-legend-box { flex: 1; min-width: 0; }
        .donut-legend-box ul { list-style: none; display: flex; flex-direction: column; gap: 6px; }

        .donut-legend-box li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 0.8125rem;
        }

        .legend-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-main);
            font-weight: 600;
            min-width: 0;
        }

        /* Nome inteiro (quebra linha se precisar) em vez de cortar com "…". */
        .legend-label span { white-space: normal; overflow-wrap: anywhere; line-height: 1.25; }

        .legend-swatch {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #ffffff;
            font-size: 0.5625rem;
            box-shadow: 0 2px 5px -1px rgba(0, 0, 0, 0.3);
        }

        body.high-contrast .legend-swatch { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }

        .legend-value {
            font-weight: 700;
            color: var(--text-main);
            flex-shrink: 0;
            text-align: right;
        }

        .legend-value small { display: block; font-weight: 500; color: var(--text-muted); font-size: 0.6875rem; }

        /* SELECT ESTILIZADO DE FILTRO */
        .select-filter {
            padding: 8px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: #fff;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-main);
            outline: none;
            cursor: pointer;
        }

        /* ==========================================================================
           TABELA INTERNA DE CONSUMO (IDÊNTICA À IMAGEM PROPORCIONADA)
           ========================================================================== */
        .photo-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        /* Antes a tabela de "Consumo por Lote - Hoje" (e a área de
           "Conversão Alimentar") sempre renderizava, mesmo sem nenhum lote
           pra mostrar — sobrava só o cabeçalho da tabela grande, vazio, ou
           o título da seção sem nada embaixo. Agora, sem dados, some por
           completo e dá lugar a esta mensagem. */
        .no-data-message {
            text-align: center;
            padding: 30px 16px;
            color: var(--text-muted);
            font-size: 0.844rem;
            line-height: 1.5;
        }

        .no-data-message i {
            display: block;
            font-size: 1.625rem;
            margin-bottom: 10px;
            color: var(--text-muted);
        }

        .photo-table th {
            padding: 12px 10px;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            background: #fafafa;
        }

        .photo-table td {
            padding: 16px 10px;
            font-size: 0.8125rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .photo-table tbody tr:last-child td {
            border-bottom: none;
        }

        .lote-info-cell {
            display: flex;
            flex-direction: column;
        }

        .lote-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #0f172a;
            font-size: 0.875rem;
        }

        /* Gradiente + glow vêm inline por linha (ver partials/lote-consumo-
           linhas.blade.php — a cor muda por tipo de animal), aqui só o
           formato/tamanho fixo do selo. */
        .lote-animal-icon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.6875rem;
            flex-shrink: 0;
        }

        body.high-contrast .lote-animal-icon { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; box-shadow: none !important; }

        .lote-subtitle {
            font-size: 0.6875rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .time-weight-cell {
            display: flex;
            flex-direction: column;
            font-size: 0.8125rem;
        }

        .time-weight-cell .time {
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        .time-weight-cell .weight {
            font-weight: 500;
            color: #1e293b;
            margin-top: 1px;
        }

        .bold-total-cell {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.875rem;
        }

        /* STATUS BADGES PERFEITAS */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-success {
            background: linear-gradient(135deg, var(--viz-lima), var(--viz-esmeralda-forte));
            color: #ffffff;
            box-shadow: 0 3px 8px -2px color-mix(in srgb, var(--verde-principal, #0e4a27) 45%, transparent);
        }
        .status-warning {
            background: linear-gradient(135deg, #fbbf24, var(--viz-laranja));
            color: #ffffff;
            box-shadow: 0 3px 8px -2px rgba(217, 119, 6, 0.4);
        }
        .status-danger {
            background: linear-gradient(135deg, #fb7185, var(--viz-vermelho));
            color: #ffffff;
            box-shadow: 0 3px 8px -2px rgba(225, 29, 72, 0.4);
        }

        /* O status agora é um botão real (abre o modal de detalhes/vincular
           ração) — mantém a aparência de badge, mas com affordance de
           clique. */
        .status-badge-btn {
            font: inherit;
            line-height: inherit;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .status-badge-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(15, 23, 42, 0.12); }
        .status-badge-btn:focus-visible { outline: 2px solid var(--verde-ativo, #087a47); outline-offset: 2px; }

        body.high-contrast .status-badge-btn:focus-visible { outline-color: #ffff00; }

        .center-footer-btn {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .btn-view-more {
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-view-more:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* ==========================================================================
           COMPONENTE DE RESUMO MENSAL ESQUERDO
           ========================================================================== */
        .monthly-layout {
            display: grid;
            grid-template-columns: 1fr 1.8fr;
            gap: 20px;
            height: 100%;
        }

        .monthly-metrics {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid var(--border-color);
            padding-right: 20px;
        }

        .metric-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 0.875rem;
        }

        .metric-label {
            color: var(--text-muted);
        }

        .metric-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Número de destaque do card "Resumo Mensal" (Total de Ração) —
           texto em gradiente vivo (azul → esmeralda) no tema claro; nos
           temas escuro/alto contraste cai pra uma cor sólida legível em
           vez do gradiente (texto com fill transparente ficaria invisível
           se só a propriedade "color" fosse sobrescrita). */
        .metric-value--destaque {
            font-size: 1.0625rem;
            background: linear-gradient(90deg, var(--viz-azul), var(--viz-esmeralda-forte));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body.dark-theme .metric-value--destaque {
            background: none;
            -webkit-text-fill-color: unset;
            color: var(--verde-texto, #0e4a27);
        }

        body.high-contrast .metric-value--destaque {
            background: none !important;
            -webkit-text-fill-color: unset !important;
            color: var(--text-main) !important;
        }

        .metric-value.highlight-up {
            color: #047857; /* 5,5:1 no branco */
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .metric-value.highlight-up i { filter: drop-shadow(0 0 5px color-mix(in srgb, var(--verde-principal, #0e4a27) 55%, transparent)); }

        .metric-value.highlight-down {
            color: #c2410c; /* 5,2:1 no branco */
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .metric-value.highlight-down i { filter: drop-shadow(0 0 5px rgba(240, 101, 62, 0.5)); }
        body.dark-theme .metric-value.highlight-up { color: #34d399; }
        body.dark-theme .metric-value.highlight-down { color: #fb8a63; }

        /* ==========================================================================
           ANÁLISE INTELIGENTE (CARD DA IMAGEM)
           ========================================================================== */
        .smart-analysis-container {
            background: var(--verde-claro, #e8f5ee);
            border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28));
        }

        .smart-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 24px;
            margin-bottom: 20px;
        }

        .smart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px 16px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .smart-item:hover { transform: translateY(-1px); box-shadow: 0 8px 16px -8px rgba(15, 23, 42, 0.18); }

        /* Fundo levemente tingido de acordo com o tipo do card (a mesma
           cor do selo do ícone) — degrada com elegância em navegadores sem
           suporte a :has() (o card só fica sem o tingimento, mantém tudo
           legível). */
        .smart-item:has(.smart-icon.info) { background: linear-gradient(160deg, rgba(14, 165, 233, 0.09), var(--card-bg) 65%); }
        .smart-item:has(.smart-icon.success) { background: linear-gradient(160deg, color-mix(in srgb, var(--verde-principal, #0e4a27) 9%, transparent), var(--card-bg) 65%); }
        .smart-item:has(.smart-icon.warning) { background: linear-gradient(160deg, rgba(249, 115, 22, 0.09), var(--card-bg) 65%); }
        .smart-item:has(.smart-icon.danger) { background: linear-gradient(160deg, rgba(225, 29, 72, 0.09), var(--card-bg) 65%); }

        /* :has() é mais específico que ".smart-item" sozinho — mesmo em
           alto contraste, essas 4 variantes venceriam um simples
           "background: #000" sem !important, mantendo um tom colorido
           sutil sobre o preto. */
        body.high-contrast .smart-item:has(.smart-icon) {
            background: #000000 !important;
        }

        .smart-item-main {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
            /* Encolhe primeiro; só quando sobram < 180px o "Ver detalhes"
               desce pra linha de baixo (antes saía do card com texto
               ampliado/zoom). */
            flex: 1 1 180px;
        }

        .smart-item { flex-wrap: wrap; }

        .smart-ver-detalhes {
            flex-shrink: 0;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            font-family: inherit;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.06);
            transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
        }

        .smart-ver-detalhes:hover {
            background: linear-gradient(145deg, var(--viz-lima), var(--viz-esmeralda-forte));
            border-color: transparent;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px -6px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent);
        }

        /* Selos "3D": caixa com gradiente diagonal + brilho interno + glow
           colorido, no lugar do ícone plano de texto usado antes. */
        .smart-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            flex-shrink: 0;
            color: #ffffff;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.45), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        .smart-icon.success { background: linear-gradient(145deg, var(--viz-lima), var(--viz-esmeralda-forte)); box-shadow: 0 6px 14px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 55%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.45), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }
        .smart-icon.info { background: linear-gradient(145deg, #38bdf8, var(--viz-ceu)); box-shadow: 0 6px 14px -4px rgba(2, 132, 199, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.45), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }
        .smart-icon.warning { background: linear-gradient(145deg, #fb923c, #ea580c); box-shadow: 0 6px 14px -4px rgba(234, 88, 12, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.45), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }
        .smart-icon.danger { background: linear-gradient(145deg, #fb7185, var(--viz-vermelho)); box-shadow: 0 6px 14px -4px rgba(225, 29, 72, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.45), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }

        body.high-contrast .smart-icon {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
            box-shadow: none !important;
        }

        .smart-text-block {
            display: flex;
            flex-direction: column;
        }

        .smart-headline {
            font-size: 0.875rem;
            font-weight: 700;
            color: #1e293b;
        }

        .smart-desc {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .green-action-btn {
            background: linear-gradient(135deg, var(--viz-lima), var(--viz-esmeralda-forte) 60%, var(--verde-principal, #0e4a27));
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            box-shadow: 0 8px 20px -8px color-mix(in srgb, var(--verde-principal, #0e4a27) 55%, transparent);
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .green-action-btn:hover {
            filter: brightness(1.06);
            transform: translateY(-1px);
            box-shadow: 0 10px 24px -6px color-mix(in srgb, var(--verde-principal, #0e4a27) 60%, transparent);
        }

        body.high-contrast .green-action-btn {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }

        /* ==========================================================================
           TABELA PRINCIPAL ORIGINAL (CRUD & GERENCIAMENTO)
           ========================================================================== */
        .crud-container {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-top: 10px;
        }

        .table-premium {
            width: 100%;
            border-collapse: collapse;
        }

        .table-premium thead {
            background: var(--verde-claro, #e8f5ee);
        }

        .table-premium th {
            padding: 18px 24px;
            text-align: left;
            color: var(--verde-texto, #0e4a27);
            font-size: 0.8125rem;
            font-weight: 600;
            border-bottom: 2px solid var(--verde-borda, rgba(14, 74, 39, 0.28));
        }

        .table-premium td {
            padding: 18px 24px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        .table-premium tbody tr:hover {
            background: #f8fafc;
        }

        .crud-badge {
            display: inline-flex;
            background: var(--verde-claro, #e8f5ee);
            color: var(--verde-texto, #0e4a27);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .table-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8125rem;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-action.edit { background: #f1f5f9; color: #334155; }
        .btn-action.edit:hover { background: #e2e8f0; }
        .btn-action.delete { background: var(--danger-bg); color: var(--danger-text); }
        .btn-action.delete:hover { background: #fca5a5; }

        /* ==========================================================================
           MODAIS UNIFICADOS E ACESSIBILIDADE
           ========================================================================== */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .modal.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card {
            background: white;
            width: 480px;
            max-width: 95%;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            position: relative;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal.active .modal-card {
            transform: translateY(0);
        }

        .modal-top {
            text-align: center;
            margin-bottom: 24px;
        }

        .modal-top img {
            width: 100px;
            margin-bottom: 10px;
        }

        .modal-top h2 {
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .modal-top p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .detalhes-lote-info-animal {
            font-weight: 600;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
        }

        .input-box {
            position: relative;
            margin-bottom: 16px;
        }

        .input-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        .input-box input, 
        .input-box select {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #f8fafc;
            outline: none;
            font-size: 0.875rem;
            transition: 0.2s;
        }

        .input-box input:focus, 
        .input-box select:focus {
            border-color: var(--verde-ativo, #087a47);
            background: white;
        }

        .form-row {
            display: flex;
            gap: 12px;
        }

        .form-row .campo {
            flex: 1;
        }

        .submit-btn {
            width: 100%;
            background: var(--brand-green-dark);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: var(--verde-hover, #0b3d21);
        }

        /* ==========================================================================
           MODAL DE DETALHES DO LOTE (Consumo por Lote → clique no status)
           ========================================================================== */
        .detalhes-lote-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 16px;
            margin-bottom: 18px;
        }

        .detalhes-lote-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 12px;
        }

        .detalhes-lote-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            font-weight: 700;
        }

        .detalhes-lote-item strong {
            font-size: 0.875rem;
            color: var(--text-main);
            font-weight: 700;
        }

        .detalhes-lote-subtitulo {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 4px 0 8px;
        }

        .detalhes-lote-distribuicao {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .detalhes-lote-distribuicao th {
            text-align: left;
            padding: 8px 10px;
            font-size: 0.71875rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }

        .detalhes-lote-distribuicao td {
            padding: 9px 10px;
            font-size: 0.8125rem;
            color: var(--text-main);
            border-bottom: 1px solid #f1f5f9;
        }

        .detalhes-lote-status { margin-bottom: 18px; }

        .detalhes-lote-aviso {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.8125rem;
            line-height: 1.5;
            margin-bottom: 18px;
        }

        .detalhes-lote-aviso-warning { background: var(--warning-bg); border-color: transparent; color: var(--warning-text); }

        .detalhes-lote-aviso a { color: inherit; font-weight: 700; text-decoration: underline; }

        .vincular-quantidade-total {
            margin: -6px 0 14px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--verde-texto, #0e4a27);
            min-height: 1.2em;
        }
        body.high-contrast .vincular-quantidade-total { color: #ffff00 !important; }

        body.dark-theme .detalhes-lote-item { background: #16281c; }
        body.dark-theme .detalhes-lote-distribuicao td { border-bottom-color: #1e3a2a; }
        body.dark-theme .detalhes-lote-aviso { background: rgba(59, 130, 246, 0.12); border-color: rgba(59, 130, 246, 0.3); color: #93c5fd; }

        body.high-contrast .detalhes-lote-item,
        body.high-contrast .detalhes-lote-distribuicao th,
        body.high-contrast .detalhes-lote-distribuicao td,
        body.high-contrast .detalhes-lote-aviso {
            background: #000000 !important;
            border-color: #ffff00 !important;
            color: #ffff00 !important;
        }

        @media (max-width: 480px) {
            .detalhes-lote-grid { grid-template-columns: 1fr; }
        }

        /* ==========================================================================
           RESPONSIVIDADE COMPLETA
           ========================================================================== */
        @media(max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 20px; }
            .smart-list { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .main { padding: 18px; padding-top: 20px; }
            .header h1 { font-size: 1.5rem; }

            .top-bar { flex-direction: column; gap: 20px; align-items: flex-start; }
            .monthly-layout { grid-template-columns: 1fr; }
            .monthly-metrics { border-right: none; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; padding-right: 0; }
            .photo-table, .table-premium { display: block; overflow-x: auto; white-space: nowrap; }
            .form-row { flex-direction: column; gap: 0; }
            .donut-layout { flex-direction: column; }
            .donut-chart-box { max-width: 150px; }
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header p { font-size: 0.844rem; }
        }

        @media (max-width: 400px) {
            .navbar-right { gap: 10px; }
            .navbar-logout span { display: none; }
        }

        /* ==========================================================================
           FILTROS AVANÇADOS (mesmo padrão do Ração/Estoque/Cocho, adaptado
           às variáveis de cor desta página).
           ========================================================================== */
        .btn-abrir-filtros {
            background: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            transition: background 0.2s, border-color 0.2s;
        }
        .btn-abrir-filtros:hover { border-color: var(--verde-hover, #0b3d21); color: var(--verde-texto, #0e4a27); }

        .filtros-badge {
            background: #123f22;
            color: var(--verde-texto, #0e4a27);
            font-size: 11px;
            font-weight: 800;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .filtros-badge.visivel { display: flex; }

        body.high-contrast .filtros-badge {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        .filtros-ativos { display: none; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 20px; }
        .filtros-ativos.visivel { display: flex; }
        .filtros-ativos .chip {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 8px 6px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        .filtros-ativos .chip button {
            background: var(--bg-global);
            border: none;
            color: var(--text-muted);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .filtros-ativos .chip button:hover { background: #fee2e2; color: #dc2626; }
        .filtros-ativos .btn-limpar-tudo {
            background: none;
            border: none;
            color: #dc2626;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
        }

        .btn-limpar-filtros {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            background: var(--card-bg);
            color: var(--verde-texto, #0e4a27);
            border: 1px solid var(--border-color);
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-limpar-filtros:hover { background: var(--bg-global); }

        body.high-contrast .btn-abrir-filtros,
        body.high-contrast .filtros-ativos .chip,
        body.high-contrast .btn-limpar-filtros {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }
    </style>
@endpush

@section('content')
    {{-- "Pular para o conteúdo" vem do partials/a11y-global (1º item do
         Tab, antes da sidebar) — o daqui ficava depois do menu e abria
         fora da tela. --}}

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="main" id="conteudo-principal" role="main">
        <div class="top-bar">
            <div class="header">
                <h1><i class="fa-solid fa-clipboard-list titulo-icone" aria-hidden="true"></i> Plano Alimentar</h1>
                <p>Acompanhe o plano alimentar de cada lote, calculado automaticamente a partir da ração vinculada.</p>
            </div>
            <button type="button" class="btn-abrir-filtros" id="openFiltrosBtn">
                <i class="fa-solid fa-sliders"></i> Filtros Avançados
                <span class="filtros-badge {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}">{{ count($filtrosAplicados ?? []) }}</span>
            </button>
        </div>

        <!-- Barra "Unidade Produtiva" — mesma lógica/aparência/comportamento
             do Dashboard: escolher a fazenda aqui é o que decide quais
             lotes, rações, consumo e planos alimentares o resto da tela
             mostra. -->
        <div class="fazenda-selector-bar">
            <div class="fazenda-selector-bar-label"><i class="fa-solid fa-tractor"></i> Unidade Produtiva:</div>
            <select class="select-fazenda" aria-label="Selecionar fazenda" onchange="window.location.href='{{ route('plano.listar') }}?fazenda_id=' + this.value" aria-label="Selecionar Unidade Produtiva">
                <option value="">Escolha uma fazenda</option>
                @foreach ($fazendas as $fazenda)
                    <option value="{{ $fazenda->id }}" {{ $fazenda->id == ($fazendaSelecionadaId ?? null) ? 'selected' : '' }}>
                        {{ $fazenda->nome_fazenda }}
                    </option>
                @endforeach
            </select>
            @if (empty($fazendaSelecionadaId))
                <div class="selector-hint">
                    <i class="fa-solid fa-circle-info"></i>
                    Selecione uma Unidade Produtiva para carregar o plano alimentar, o consumo e a conversão alimentar reais.
                </div>
            @endif
        </div>

        <div class="filtros-ativos {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}" id="filtrosAtivos">
            @foreach ($filtrosAplicados ?? [] as $filtro)
                <div class="chip" data-chave="{{ $filtro['chave'] }}">
                    {{ $filtro['label'] }}
                    <button type="button" class="remover-filtro" data-chave="{{ $filtro['chave'] }}" title="Remover este filtro">&times;</button>
                </div>
            @endforeach
            @if (count($filtrosAplicados ?? []))
                <button type="button" class="btn-limpar-tudo" id="limparTudoBtn">Limpar todos</button>
            @endif
        </div>

        @php
            $trendIcone = fn ($v) => is_null($v) ? 'fa-minus' : ($v > 0 ? 'fa-arrow-up' : ($v < 0 ? 'fa-arrow-down' : 'fa-minus'));
            $trendClasse = fn ($v) => (is_null($v) || $v == 0) ? 'trend-neutral' : ($v > 0 ? 'trend-up' : 'trend-down');
        @endphp
        <div class="stat-cards-row">
            <div class="stat-card stat-card--verde">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-verde"><i class="fa-solid fa-sack-dollar" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Consumo Total Hoje</div>
                    <div class="stat-card-valor">{{ number_format($statCardsTopo['consumoHoje']['valor'] ?? 0, 0, ',', '.') }} kg</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCardsTopo['consumoHoje']['variacao'] ?? null) }}">
                    @if (!is_null($statCardsTopo['consumoHoje']['variacao'] ?? null))
                        <i class="fa-solid {{ $trendIcone($statCardsTopo['consumoHoje']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCardsTopo['consumoHoje']['variacao']), 1, ',', '.') }}% vs. ontem
                    @else
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem registro de ontem
                    @endif
                </div>
            </div>

            <div class="stat-card stat-card--ambar">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-ambar"><i class="fa-solid fa-coins" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Custo Estimado Hoje</div>
                    <div class="stat-card-valor">R$ {{ number_format($statCardsTopo['custoHoje']['valor'] ?? 0, 2, ',', '.') }}</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCardsTopo['custoHoje']['variacao'] ?? null) }}">
                    @if (!is_null($statCardsTopo['custoHoje']['variacao'] ?? null))
                        <i class="fa-solid {{ $trendIcone($statCardsTopo['custoHoje']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCardsTopo['custoHoje']['variacao']), 1, ',', '.') }}% vs. ontem
                    @else
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem registro de ontem
                    @endif
                </div>
            </div>

            <div class="stat-card stat-card--azul">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-azul"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Lotes em Acompanhamento</div>
                    <div class="stat-card-valor">{{ $statCardsTopo['lotesAtivos']['valor'] ?? 0 }}</div>
                </div>
                <div class="stat-card-trend trend-neutral">
                    {{ $statCardsTopo['lotesAtivos']['comRacao'] ?? 0 }} com ração vinculada
                </div>
            </div>

            <div class="stat-card stat-card--roxo">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-roxo"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i></div>
                </div>
                <div>
                    {{-- Era "Eficiência Média Hoje", mas o número é a parte dos
                         lotes que estão com ração vinculada (plano em dia). --}}
                    <div class="stat-card-label">Lotes com Plano em Dia</div>
                    <div class="stat-card-valor">{{ $statCardsTopo['eficienciaMedia']['valor'] ?? 0 }}%</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCardsTopo['eficienciaMedia']['variacao'] ?? null) }}">
                    @if (!is_null($statCardsTopo['eficienciaMedia']['variacao'] ?? null))
                        <i class="fa-solid {{ $trendIcone($statCardsTopo['eficienciaMedia']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCardsTopo['eficienciaMedia']['variacao']), 1, ',', '.') }}% vs. ontem
                    @else
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem registro de ontem
                    @endif
                </div>
            </div>
        </div>

        <div id="modalFiltros" class="modal">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="tituloModalFiltros">
                <button class="close" id="closeFiltrosBtn" aria-label="Fechar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <div class="modal-top">
                    <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
                    <h2 id="tituloModalFiltros">Filtros Avançados</h2>
                    <p>Escolha o que exibir no painel do Plano Alimentar</p>
                </div>

                <form action="{{ route('plano.listar') }}" method="GET">
                    <!-- A Fazenda não é mais escolhida aqui — é a Unidade
                         Produtiva selecionada na barra do topo. Este campo
                         oculto só garante que aplicar Lote/Ração não perca
                         a unidade atual (um <form method="GET"> só manda os
                         campos que ele mesmo tem). -->
                    <input type="hidden" name="fazenda_id" value="{{ $fazendaSelecionadaId }}">

                    <div class="campo">
                        <label for="filtro_lote_id">Lote</label>
                        <div class="input-box">
                            <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                            <select name="lote_id" id="filtro_lote_id">
                                <option value="">Todos os lotes</option>
                                @foreach ($todosLotes as $lote)
                                    <option value="{{ $lote->id }}" {{ ($filtros['lote_id'] ?? null) == $lote->id ? 'selected' : '' }}>{{ $lote->nome_lote }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="filtro_racao_id">Ração</label>
                        <div class="input-box">
                            <i class="fa-solid fa-wheat-awn" aria-hidden="true"></i>
                            <select name="racao_id" id="filtro_racao_id">
                                <option value="">Todas as rações</option>
                                @foreach ($racaos as $racao)
                                    <option value="{{ $racao->id }}" {{ ($filtros['racao_id'] ?? null) == $racao->id ? 'selected' : '' }}>{{ $racao->nome_racao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn"><i class="fa-solid fa-check"></i> Aplicar Filtros</button>
                    <a href="{{ route('plano.listar', ['fazenda_id' => $fazendaSelecionadaId]) }}" class="btn-limpar-filtros"><i class="fa-solid fa-eraser"></i> Limpar Filtros</a>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alerta-flash alerta-sucesso" role="status" aria-live="polite">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alerta-flash alerta-erro" role="alert" aria-live="assertive">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                <div>
                    <strong>Não foi possível salvar:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Linha 1: Consumo por Lote + Últimos 7 Dias -->
        <div class="dashboard-grid">

          <div class="dashboard-card" id="tabelaConsumoLotes">
    <div class="card-header-flex">
        <span class="card-title" id="sec-plano-diario">Consumo por Lote - Plano Diário</span>
    </div>
    @php $totalLotesConsumo = $lotesDashboardTotal ?? $lotesDashboard->count(); @endphp
    @if ($totalLotesConsumo > 0)
    <table class="photo-table">
        <caption class="sr-only">Consumo de ração por lote, calculado direto da ração vinculada a cada um</caption>
        <thead>
            <tr>
                <th scope="col">Lote</th>
                <th scope="col">Animais</th>
                <th scope="col">Manhã</th>
                <th scope="col">Tarde</th>
                <th scope="col">Noite</th>
                <th scope="col">Total do Dia</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody id="corpoConsumoLotes">
            @include('partials.lote-consumo-linhas', ['lotesDashboard' => $lotesDashboard])
        </tbody>
    </table>
    @if ($totalLotesConsumo > $lotesDashboard->count())
    <div class="center-footer-btn" id="footerConsultarLotes">
        <button type="button" class="btn-view-more" id="btnConsultarLotes" onclick="consultarLotes()">Consultar Lotes</button>
    </div>
    @endif
    @else
    <div class="no-data-message">
        <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
        Nenhum lote encontrado. Cadastre um lote e uma ração compatível (mesma fazenda, tipo e sexo) para ver o consumo automático.
    </div>
    @endif
</div>

<div class="dashboard-card">
    <div class="card-header-flex">
        <span class="card-title" id="sec-consumo-7dias">Consumo dos Últimos 7 Dias</span>
    </div>
    <div style="flex-grow: 1; height: 220px; position: relative;">
        <canvas id="chart7Dias" role="img" aria-label="Gráfico de barras: consumo de ração nos últimos 7 dias, em quilogramas. Total da semana: {{ $totalSemana }} kg."></canvas>
    </div>
    <div class="card-header-flex" style="margin-bottom: 0; margin-top: 15px; flex-wrap: wrap; gap: 8px;">
        <span style="font-size: 0.875rem; font-weight: 700;">Total da Semana: <span style="color: var(--text-principal, #0f172a);">{{ $totalSemana }} kg</span></span>
        <span style="font-size: 0.75rem; color: var(--text-muted);"><i class="fa-solid fa-chart-line" aria-hidden="true"></i> Registrado dia a dia a partir de hoje</span>
    </div>
</div>

        </div>

        <!-- Linha 2: Resumo Mensal + Distribuição de Ração -->
        <div class="dashboard-grid">

<div class="dashboard-card" id="resumoMensal">
    <div class="card-header-flex">
        <span class="card-title" id="sec-resumo-mensal">Resumo Mensal - {{ $mesAnoAtual }}</span>
        <select class="select-filter" id="filtroMesResumo" aria-label="Mês do resumo mensal" onchange="mudarMesResumoMensal(this.value)">
            @foreach ($mesesDisponiveis ?? [] as $opcaoMes)
                <option value="{{ $opcaoMes['valor'] }}" {{ ($mesSelecionado ?? '') === $opcaoMes['valor'] ? 'selected' : '' }}>{{ $opcaoMes['label'] }}</option>
            @endforeach
        </select>
    </div>
    @if (!($temHistoricoMes ?? true))
        <p class="card-subtitle" style="margin-bottom: 14px;"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Histórico começou a ser gravado a partir de hoje — meses anteriores ainda não têm dado registrado.</p>
    @endif
    <div class="monthly-layout">
        <div class="monthly-metrics">
            <div class="metric-row">
                <span class="metric-label">Total de Ração</span>
                <span class="metric-value metric-value--destaque">{{ $totalRacaoMes }} kg</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Custo Total</span>
                <span class="metric-value">R$ {{ $custoTotalMes }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Média Diária</span>
                <span class="metric-value">{{ $mediaDiariaMes }} kg</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Maior Consumo</span>
                <span class="metric-value highlight-up"><i class="fa-solid fa-arrow-up"></i> {{ $maiorConsumo }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Menor Consumo</span>
                <span class="metric-value highlight-down"><i class="fa-solid fa-arrow-down"></i> {{ $menorConsumo }}</span>
            </div>
        </div>
        <div style="position: relative; height: 200px; flex-grow: 1;">
            <canvas id="chartMensal" role="img" aria-label="Gráfico de linha: consumo total de ração por semana em {{ $mesAnoAtual }}. Total do mês: {{ $totalRacaoMes }} kg."></canvas>
        </div>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header-flex">
        <span class="card-title" id="sec-distribuicao-racao">Distribuição de Ração</span>
    </div>
    @if (($distribuicaoRacoes ?? collect())->isNotEmpty())
        <div class="donut-layout">
            <div class="donut-chart-box">
                <canvas id="chartDistribuicaoRacoes"></canvas>
                <div class="donut-center-total">
                    <span class="valor">{{ $totalRacaoMes }} kg</span>
                    <span class="rotulo">Total</span>
                </div>
            </div>
            <div class="donut-legend-box">
                <ul id="donutLegendListPlano"></ul>
            </div>
        </div>
    @else
        <div class="no-data-message">
            <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
            Nenhum consumo por ração registrado neste mês ainda.
        </div>
    @endif
</div>

        </div>

        <!-- Análise Inteligente: agora ocupando a largura toda, em grade 2x2 -->
        <div class="dashboard-card smart-analysis-container" style="margin-bottom: 30px;">
            <div class="card-header-flex">
                <span class="card-title" id="sec-analise-inteligente">Análise Inteligente</span>
            </div>
            <div class="smart-list">
                <div class="smart-item">
                    <div class="smart-item-main">
                        @if (($coberturaPercentual ?? null) === null)
                            <i class="fa-solid fa-circle-info smart-icon info"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">Nenhum lote cadastrado</span>
                                <span class="smart-desc">Cadastre um lote para começar a acompanhar o consumo.</span>
                            </div>
                        @elseif ($coberturaPercentual >= 95)
                            <i class="fa-solid fa-circle-check smart-icon success"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">Cobertura de ração completa</span>
                                <span class="smart-desc">{{ $coberturaPercentual }}% dos lotes têm uma ração vinculada.</span>
                            </div>
                        @elseif ($coberturaPercentual >= 50)
                            <i class="fa-solid fa-triangle-exclamation smart-icon warning"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">Cobertura de ração parcial</span>
                                <span class="smart-desc">{{ $coberturaPercentual }}% dos lotes têm uma ração vinculada.</span>
                            </div>
                        @else
                            <i class="fa-solid fa-circle-xmark smart-icon danger"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">Poucos lotes com ração vinculada</span>
                                <span class="smart-desc">{{ $coberturaPercentual }}% dos lotes têm uma ração vinculada.</span>
                            </div>
                        @endif
                    </div>
                    <a href="#tabelaConsumoLotes" class="smart-ver-detalhes">Ver detalhes</a>
                </div>

                <div class="smart-item">
                    <div class="smart-item-main">
                        <i class="fa-solid fa-bullseye smart-icon info"></i>
                        <div class="smart-text-block">
                            <span class="smart-headline">Consumo diário planejado</span>
                            <span class="smart-desc">{{ number_format($totalDiario ?? 0, 1, ',', '.') }} kg/dia &middot; custo estimado de R$ {{ number_format($custoDiario ?? 0, 2, ',', '.') }}.</span>
                        </div>
                    </div>
                    <a href="#resumoMensal" class="smart-ver-detalhes">Ver detalhes</a>
                </div>

                <div class="smart-item">
                    <div class="smart-item-main">
                        @if (($lotesSemRacao ?? 0) > 0)
                            <i class="fa-solid fa-triangle-exclamation smart-icon warning"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">{{ $lotesSemRacao }} {{ Str::plural('lote', $lotesSemRacao) }} sem ração vinculada</span>
                                <span class="smart-desc">{{ $lotesComRacao }} de {{ $lotesComRacao + $lotesSemRacao }} lotes já têm uma ração compatível cadastrada (mesma fazenda, tipo e sexo).</span>
                            </div>
                        @else
                            <i class="fa-solid fa-circle-check smart-icon success"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">Todos os lotes com ração vinculada</span>
                                <span class="smart-desc">{{ $lotesComRacao }} {{ Str::plural('lote', $lotesComRacao) }} com ração compatível cadastrada.</span>
                            </div>
                        @endif
                    </div>
                    <a href="#tabelaConsumoLotes" class="smart-ver-detalhes">Ver detalhes</a>
                </div>

                <div class="smart-item">
                    <div class="smart-item-main">
                        @if ($loteMaiorConsumo ?? null)
                            <i class="fa-solid fa-trophy smart-icon success"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">Maior consumo: {{ $loteMaiorConsumo->nome_lote }}</span>
                                <span class="smart-desc">{{ number_format($loteMaiorConsumo->total_dia, 1, ',', '.') }} kg/dia planejados para este lote.</span>
                            </div>
                        @else
                            <i class="fa-solid fa-circle-info smart-icon info"></i>
                            <div class="smart-text-block">
                                <span class="smart-headline">Nenhum destaque</span>
                                <span class="smart-desc">Vincule uma ração compatível a um lote para começar a acompanhar o consumo.</span>
                            </div>
                        @endif
                    </div>
                    @if ($loteMaiorConsumo ?? null)
                        <button type="button" class="smart-ver-detalhes" onclick="abrirModalDetalhesLote(this)" data-detalhes='@json($loteMaiorConsumo->detalhes)'>Ver detalhes</button>
                    @else
                        <a href="#tabelaConsumoLotes" class="smart-ver-detalhes">Ver detalhes</a>
                    @endif
                </div>
            </div>
            <a href="{{ route('relatorio.listar') }}" class="green-action-btn" style="text-decoration:none; text-align:center; display:block;">Ver Relatório Completo</a>
        </div>

<!-- Card Amplo: Conversão Alimentar -->
<h2 class="ca-section-title" id="sec-conversao-lote">Conversão Alimentar por Lote</h2>
<p class="ca-section-subtitle">Peso médio, consumo e conversão alimentar calculados automaticamente a partir da ração vinculada a cada lote.</p>

<!-- Período usado pelo comparativo "Consumo por Animal"/"Consumo por kg
     Vivo" (variação vs período anterior) dos cards abaixo. -->
<div class="periodo-chips" role="group" aria-label="Período da Conversão Alimentar">
    @foreach (\App\Http\Controllers\PlanoController::PERIODOS_CONVERSAO as $dias)
        <a href="{{ request()->fullUrlWithQuery(['periodo_conversao' => $dias]) }}#tabelaConsumoLotes" class="periodo-chip {{ ($periodoConversao ?? 7) == $dias ? 'active' : '' }}">{{ $dias }} dias</a>
    @endforeach
</div>

@if ($comparativoConversao['temDados'] ?? false)
<div class="stat-cards-row" style="grid-template-columns: repeat(3, 1fr);">
    <div class="stat-card stat-card--verde">
        <div class="stat-card-top"><div class="stat-card-icon icone-verde"><i class="fa-solid fa-trophy" aria-hidden="true"></i></div></div>
        <div>
            <div class="stat-card-label">Melhor Conversão Alimentar</div>
            <div class="stat-card-valor" title="{{ $comparativoConversao['melhor']->nome_lote }}">{{ $comparativoConversao['melhor']->nome_lote }}</div>
        </div>
        <div class="stat-card-trend trend-up"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i> {{ number_format($comparativoConversao['melhor']->conversao_real, 2, ',', '.') }} kg/kg</div>
    </div>

    <div class="stat-card stat-card--ambar">
        <div class="stat-card-top"><div class="stat-card-icon icone-ambar"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div></div>
        <div>
            <div class="stat-card-label">Pior Conversão Alimentar</div>
            <div class="stat-card-valor" title="{{ $comparativoConversao['pior']->nome_lote }}">{{ $comparativoConversao['pior']->nome_lote }}</div>
        </div>
        <div class="stat-card-trend trend-down"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i> {{ number_format($comparativoConversao['pior']->conversao_real, 2, ',', '.') }} kg/kg</div>
    </div>

    <div class="stat-card stat-card--azul">
        <div class="stat-card-top"><div class="stat-card-icon icone-azul"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i></div></div>
        <div>
            <div class="stat-card-label">Conversão Média</div>
            <div class="stat-card-valor">{{ number_format($comparativoConversao['media'], 2, ',', '.') }} kg/kg</div>
        </div>
        <div class="stat-card-trend trend-neutral">kg de ração por kg ganho, entre lotes com 2+ pesagens</div>
    </div>
</div>

<div class="dashboard-card" style="margin-bottom: 25px;">
    <div class="card-header-flex">
        <span class="card-title" id="sec-evolucao-conversao">Evolução da Conversão Alimentar</span>
    </div>
    <div style="flex-grow: 1; height: 260px; position: relative;">
        <canvas id="chartEvolucaoConversao" role="img" aria-label="Gráfico de linha: evolução da conversão alimentar real, em kg de ração por kg de peso ganho, por lote, ao longo das pesagens registradas."></canvas>
    </div>
</div>
@else
<div class="no-data-message" style="margin-bottom: 25px;">
    <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
    Nenhum lote com pesagem registrada ainda. Clique em "Registrar Pesagem" em um dos cards abaixo para começar a medir a conversão alimentar real.
</div>
@endif

@php $totalConversoes = $conversoesAlimentaresTotal ?? $conversoesAlimentaresExibidas->count(); @endphp
@if ($totalConversoes > 0)
<div class="ca-cards-grid">
    <div id="corpoConversoesAlimentares">
        @include('partials.conversao-alimentar-cards', ['conversoes' => $conversoesAlimentaresExibidas])
    </div>
    @if ($totalConversoes > $conversoesAlimentaresExibidas->count())
    <button type="button" class="ca-ver-todas-card" id="btnConsultarConversoes" onclick="consultarConversoes()">
        <span class="ca-ver-todas-icon" aria-hidden="true"><i class="fa-solid fa-layer-group"></i></span>
        <span id="textoConsultarConversoes">Ver todas as conversões</span>
    </button>
    @endif
</div>
@else
<div class="no-data-message">
    <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
    Nenhum lote encontrado para calcular a conversão alimentar.
</div>
@endif
    </div>

    <!-- Modal de detalhes do plano do lote / vincular ração — conteúdo
         totalmente preenchido via JS a partir do data-detalhes do botão
         de status clicado (ver abrirModalDetalhesLote). -->
    <div class="modal" id="modalDetalhesLote">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="tituloModalDetalhesLote">
            <button class="close" onclick="fecharModalDetalhesLote()" aria-label="Fechar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            <div class="modal-top">
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
                <h2 id="tituloModalDetalhesLote">Plano Alimentar do Lote</h2>
                <p id="detalhesLoteSubtitulo"></p>
                <p id="detalhesLoteInfoAnimal" class="detalhes-lote-info-animal"></p>
            </div>

            <div id="detalhesLoteRacaoBloco" hidden>
                <div class="detalhes-lote-grid">
                    <div class="detalhes-lote-item">
                        <span class="detalhes-lote-label">Ração</span>
                        <strong id="detalhesLoteNomeRacao"></strong>
                    </div>
                    <div class="detalhes-lote-item">
                        <span class="detalhes-lote-label">Quantidade total</span>
                        <strong id="detalhesLoteQuantidadeTotal"></strong>
                    </div>
                    <div class="detalhes-lote-item">
                        <span class="detalhes-lote-label">Quantidade por animal</span>
                        <strong id="detalhesLoteQuantidadePorAnimal"></strong>
                    </div>
                    <div class="detalhes-lote-item">
                        <span class="detalhes-lote-label">Valor/kg</span>
                        <strong id="detalhesLoteValorKg"></strong>
                    </div>
                    <div class="detalhes-lote-item">
                        <span class="detalhes-lote-label">Estoque disponível</span>
                        <strong id="detalhesLoteEstoque"></strong>
                    </div>
                </div>

                <h3 class="detalhes-lote-subtitulo">Distribuição das refeições</h3>
                <table class="detalhes-lote-distribuicao">
                    <thead>
                        <tr><th scope="col">Período</th><th scope="col">Horário</th><th scope="col">Quantidade</th></tr>
                    </thead>
                    <tbody id="detalhesLoteDistribuicao"></tbody>
                </table>

                <div class="detalhes-lote-status" id="detalhesLoteStatusPlano"></div>
            </div>

            <div id="detalhesLoteSemRacaoAviso" class="detalhes-lote-aviso" hidden>
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                Este lote ainda não tem uma ração vinculada — vincule uma abaixo para começar a acompanhar o plano.
            </div>

            <div id="detalhesLoteSemCompativel" class="detalhes-lote-aviso detalhes-lote-aviso-warning" hidden>
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                <span>
                    Nenhuma ração compatível (mesma fazenda, tipo e sexo) está cadastrada ainda.
                    <a href="{{ route('racao.cadastrar') }}">Cadastrar uma ração</a>
                </span>
            </div>

            <form id="formVincularRacao" method="POST">
                @csrf
                <!-- Mesmo motivo do campo oculto em #formPesagem: garante
                     que salvar o vínculo volte pra tela, não pro endpoint
                     JSON de "Consultar Lotes"/"Consultar mais Conversões". -->
                <input type="hidden" name="pagina_atual" id="vincularRacaoPaginaAtual" value="">
                <label for="vincularRacaoSelect">Ração vinculada a este lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-wheat-awn" aria-hidden="true"></i>
                    <select name="racao_id" id="vincularRacaoSelect"></select>
                </div>

                {{-- Quanto cada animal come: o total do lote acompanha a
                     quantidade de animais (mesma regra do app). --}}
                <div id="vincularQuantidadeBloco">
                    <label for="vincularQuantidadePorAnimal">Quanto cada animal come por dia (kg)</label>
                    <div class="input-box">
                        <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
                        <input type="number" step="0.01" min="0.01" max="999" name="quantidade_por_animal" id="vincularQuantidadePorAnimal"
                            inputmode="decimal" aria-describedby="vincularQuantidadeTotal" placeholder="Ex.: 3,5">
                    </div>
                    <p class="vincular-quantidade-total" id="vincularQuantidadeTotal" aria-live="polite"></p>
                </div>

                <button type="submit" class="submit-btn">Salvar plano</button>
            </form>
        </div>
    </div>

    <!-- Modal "Registrar Pesagem" — checkpoint de peso médio do lote usado
         pra calcular a Conversão Alimentar real (ver PlanoController::
         registrarPesagem / partials/conversao-alimentar-cards.blade.php). -->
    <div class="modal" id="modalPesagem">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="tituloModalPesagem">
            <button class="close" onclick="fecharModalPesagem()" aria-label="Fechar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            <div class="modal-top">
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
                <h2 id="tituloModalPesagem">Registrar Pesagem</h2>
                <p id="pesagemSubtitulo"></p>
            </div>

            @if ($errors->has('peso_medio_kg') || $errors->has('data'))
                <div class="detalhes-lote-aviso detalhes-lote-aviso-warning" style="margin-bottom: 16px;">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    {{ $errors->first('peso_medio_kg') ?: $errors->first('data') }}
                </div>
            @endif

            <form id="formPesagem" method="POST">
                @csrf
                <input type="hidden" name="lote_id" id="pesagemLoteId" value="{{ old('lote_id') }}">
                <!-- Pra onde voltar depois de salvar — preenchido em JS com
                     a URL real da tela no momento em que o modal abriu (ver
                     abrirModalPesagem). Sem isso, o controller cairia num
                     back() que, depois de "Consultar Lotes"/"Consultar mais
                     Conversões" (fetch() sem cabeçalho de AJAX), mandava de
                     volta pro endpoint JSON puro em vez da tela. -->
                <input type="hidden" name="pagina_atual" id="pesagemPaginaAtual" value="">

                <label for="pesagemData">Data da pesagem</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
                    <input type="date" name="data" id="pesagemData" value="{{ old('data') }}" max="{{ now()->toDateString() }}" required>
                </div>

                <label for="pesagemPeso">Peso médio atual do lote (kg)</label>
                <div class="input-box">
                    <i class="fa-solid fa-weight-hanging" aria-hidden="true"></i>
                    <input type="number" name="peso_medio_kg" id="pesagemPeso" value="{{ old('peso_medio_kg') }}" step="0.01" min="0.01" max="99999" required>
                </div>

                <button type="submit" class="submit-btn">Salvar pesagem</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- LOGIC CONTROLLERS & RENDER CHARTS JS -->
    <script>
        // O "zoom" do <html> (ver partials/acessibilidade) muda o pixel
        // ratio efetivo da página sem alterar window.devicePixelRatio —
        // deixando o Chart.js no automático, ele mede o canvas já
        // "zoomado" e desenha em cima disso de novo, esticando/cortando os
        // gráficos. Fixando em 1 aqui, o dimensionamento fica só por conta
        // do zoom do navegador (que já escala tudo igualmente), sem essa
        // duplicação.
        if (window.Chart) {
            Chart.defaults.devicePixelRatio = 1;
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Abrir/fechar a drawer (ver layouts/app.blade.php) muda a
            // largura do .main numa transição CSS — os gráficos (Chart.js)
            // não recalculam sozinhos o tamanho do canvas durante isso, daí
            // o resize manual aqui depois que a transição termina.
            const toggleBtn = document.getElementById('toggleMenuBtn');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function remedirGraficosPlano() {
                setTimeout(function() {
                    if (chart7) chart7.resize();
                    if (chartMensal) chartMensal.resize();
                }, 320);
            }

            if (toggleBtn) toggleBtn.addEventListener('click', remedirGraficosPlano);
            if (sidebarOverlay) sidebarOverlay.addEventListener('click', remedirGraficosPlano);

            // ==========================================
            // GRÁFICOS 1 E 2: CONSUMO 7 DIAS / MENSAL — DINÂMICOS E CIENTES DE TEMA
            // ==========================================
            // Injeção segura de arrays do Blade para o Javascript
            const labelsDoBanco = {!! $labels7Dias !!};
            const valoresDoBanco = {!! $valores7Dias !!};
            const labelsMensalDoBanco = @json(json_decode($labelsMensal ?? '[]'));
            const valoresMensalDoBanco = @json(json_decode($valoresMensal ?? '[]'));
            const distribuicaoRacoesDoBanco = @json($distribuicaoRacoes ?? []);
            const evolucaoConversaoDoBanco = @json($evolucaoConversao ?? ['labels' => [], 'series' => [], 'temDados' => false]);

            function cssVarPlano(nome, fallback) {
                const valor = getComputedStyle(document.body).getPropertyValue(nome).trim();
                return valor || fallback;
            }

            // Converte "#rrggbb" em "rgba(r, g, b, alpha)" — usado nos
            // gradientes dos gráficos (barras, linha, sparklines).
            function hexToRgba(hex, alpha) {
                const limpo = hex.replace('#', '');
                const bigint = parseInt(limpo.length === 3
                    ? limpo.split('').map((c) => c + c).join('')
                    : limpo, 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }

            let chart7 = null;
            let chartMensal = null;
            let chartDistribuicaoRacoes = null;
            let chartEvolucaoConversao = null;

            // Antes os gráficos eram criados uma única vez, com cores fixas
            // (#0f172a, #bfdbfe...) — ilegíveis no modo escuro/alto contraste
            // (eixos e legendas escuros sobre fundo escuro). Agora lê as
            // variáveis de cor do tema atual e recria os gráficos sempre que
            // o tema muda, sem precisar recarregar a página.
            // Paleta categórica fixa (mesma ordem sempre — não é cor por
            // dia da semana, é cor por posição na barra) usada no gráfico
            // "Consumo dos Últimos 7 Dias". Uma cor viva por dia, em vez da
            // única barra azul-clara monótona de antes.
            const paletaSemanaPlano = ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#008300', '#4a3aa7'];

            // Plugin do Chart.js: desenha um glow radial suave atrás da
            // barra de "hoje" (último dia) no gráfico de 7 dias — só essa
            // barra recebe o destaque, as demais ficam com o gradiente
            // normal.
            let corGlowHojePlano = '#059669';
            const brilhoHojePlanoPlugin = {
                id: 'brilhoHojePlano',
                beforeDatasetsDraw(chart) {
                    if (chart.canvas.id !== 'chart7Dias') return;
                    const meta = chart.getDatasetMeta(0);
                    const barra = meta.data[meta.data.length - 1];
                    if (!barra) return;
                    const { ctx } = chart;
                    const { x, y } = barra.getProps(['x', 'y'], true);
                    const raio = Math.max(barra.width * 1.8, 40);
                    const gradiente = ctx.createRadialGradient(x, y, 0, x, y, raio);
                    gradiente.addColorStop(0, hexToRgba(corGlowHojePlano, 0.38));
                    gradiente.addColorStop(1, hexToRgba(corGlowHojePlano, 0));
                    ctx.save();
                    ctx.fillStyle = gradiente;
                    ctx.beginPath();
                    ctx.arc(x, y, raio, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.restore();
                }
            };
            Chart.register(brilhoHojePlanoPlugin);

            function montarGraficosPlano() {
                const corGrid = cssVarPlano('--chart-grid', '#eef2f6');
                const corTick = cssVarPlano('--chart-tick', '#94a3b8');
                const corTextoPrincipal = cssVarPlano('--text-main', '#0f172a');
                const corSurface = cssVarPlano('--card-bg', '#ffffff');
                const altoContraste = document.body.classList.contains('high-contrast');
                const corHoje = altoContraste ? '#ffff00' : cssVarPlano('--viz-esmeralda-forte', '#059669');

                if (chart7) chart7.destroy();
                if (chartMensal) chartMensal.destroy();
                if (chartDistribuicaoRacoes) chartDistribuicaoRacoes.destroy();
                if (chartEvolucaoConversao) chartEvolucaoConversao.destroy();

                // Cada barra recebe uma cor viva e distinta (ordem fixa da
                // paleta categórica); a última (hoje) usa a cor de destaque
                // do tema e ganha o glow do plugin acima.
                const coresDinamicas = valoresDoBanco.map((val, idx) => {
                    if (altoContraste) {
                        return idx === (valoresDoBanco.length - 1) ? '#ffff00' : 'rgba(255, 255, 255, 0.35)';
                    }
                    return idx === (valoresDoBanco.length - 1) ? corHoje : paletaSemanaPlano[idx % paletaSemanaPlano.length];
                });
                corGlowHojePlano = corHoje;

                const ctx7 = document.getElementById('chart7Dias').getContext('2d');
                chart7 = new Chart(ctx7, {
                    type: 'bar',
                    data: {
                        labels: labelsDoBanco,
                        datasets: [{
                            data: valoresDoBanco,
                            backgroundColor: (context) => {
                                const { chart, dataIndex } = context;
                                const { ctx, chartArea } = chart;
                                const corBase = coresDinamicas[dataIndex];
                                if (altoContraste || !chartArea) return corBase;
                                const ehHoje = dataIndex === (valoresDoBanco.length - 1);
                                const grad = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                grad.addColorStop(0, hexToRgba(corBase, ehHoje ? 1 : 0.92));
                                grad.addColorStop(1, hexToRgba(corBase, ehHoje ? 0.78 : 0.55));
                                return grad;
                            },
                            borderRadius: 6,
                            borderSkipped: false,
                            borderWidth: (context) => context.dataIndex === (valoresDoBanco.length - 1) ? 2 : 0,
                            borderColor: '#ffffff',
                            barThickness: 26
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: corSurface,
                                titleColor: corTextoPrincipal,
                                bodyColor: corTick,
                                borderColor: corGrid,
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: { label: (item) => item.formattedValue + ' kg' }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: corTick } },
                            y: {
                                beginAtZero: true,
                                grid: { color: corGrid },
                                ticks: {
                                    color: corTick,
                                    callback: function(value) {
                                        return value + ' kg';
                                    }
                                }
                            }
                        }
                    }
                });

                const corInicioMensal = cssVarPlano('--viz-azul', '#2563eb');
                const corFimMensal = cssVarPlano('--viz-esmeralda-forte', '#059669');

                const ctxMensal = document.getElementById('chartMensal').getContext('2d');
                chartMensal = new Chart(ctxMensal, {
                    type: 'line',
                    data: {
                        labels: labelsMensalDoBanco,
                        datasets: [{
                            data: valoresMensalDoBanco,
                            borderColor: altoContraste ? '#ffff00' : (context) => {
                                const { chart } = context;
                                const { ctx, chartArea } = chart;
                                if (!chartArea) return corInicioMensal;
                                const grad = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                                grad.addColorStop(0, corInicioMensal);
                                grad.addColorStop(1, corFimMensal);
                                return grad;
                            },
                            backgroundColor: altoContraste ? 'rgba(255, 255, 0, 0.10)' : (context) => {
                                const { chart } = context;
                                const { ctx, chartArea } = chart;
                                if (!chartArea) return hexToRgba(corFimMensal, 0.08);
                                const grad = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                grad.addColorStop(0, hexToRgba(corFimMensal, 0.22));
                                grad.addColorStop(1, hexToRgba(corInicioMensal, 0.02));
                                return grad;
                            },
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3, // Curva suave nos pontos
                            pointBackgroundColor: altoContraste ? '#ffff00' : corFimMensal,
                            pointBorderColor: corSurface,
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: corSurface,
                                titleColor: corTextoPrincipal,
                                bodyColor: corTick,
                                borderColor: corGrid,
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: { label: (item) => item.formattedValue + ' kg' }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: corTick } },
                            y: {
                                beginAtZero: false,
                                grid: { color: corGrid },
                                ticks: {
                                    color: corTick,
                                    callback: function(value) {
                                        return value + ' kg';
                                    }
                                }
                            }
                        }
                    }
                });

                // GRÁFICO 5 — DISTRIBUIÇÃO DE RAÇÃO (donut + legenda), mês
                // selecionado no "Resumo Mensal". Paleta categórica fixa
                // (ordem validada — nunca repetida ciclicamente antes do
                // fim da lista) pra cada fatia ser bem distinta da vizinha.
                const paletaDistribuicao = altoContraste
                    ? ['#ffff00', 'rgba(255,255,255,0.7)', 'rgba(255,255,255,0.45)', 'rgba(255,255,255,0.3)']
                    : ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#008300', '#4a3aa7', '#e34948'];
                const canvasDistribuicao = document.getElementById('chartDistribuicaoRacoes');
                const legendaDistribuicao = document.getElementById('donutLegendListPlano');

                if (legendaDistribuicao) {
                    legendaDistribuicao.innerHTML = distribuicaoRacoesDoBanco.map((item, i) => {
                        const cor = paletaDistribuicao[i % paletaDistribuicao.length];
                        const fundoLi = altoContraste ? 'rgba(255, 255, 0, 0.08)' : hexToRgba(cor, 0.12);
                        return `
                        <li style="background: ${fundoLi}; border-left: 4px solid ${cor};">
                            <span class="legend-label"><span class="legend-swatch" style="background: ${cor};"><i class="fa-solid fa-check" aria-hidden="true"></i></span> <span>${item.nome}</span></span>
                            <span class="legend-value">${item.percentual}%<small>${item.kg.toLocaleString('pt-BR')} kg</small></span>
                        </li>
                    `;
                    }).join('');
                }

                if (canvasDistribuicao) {
                    chartDistribuicaoRacoes = new Chart(canvasDistribuicao, {
                        type: 'doughnut',
                        data: {
                            labels: distribuicaoRacoesDoBanco.map((item) => item.nome),
                            datasets: [{
                                data: distribuicaoRacoesDoBanco.map((item) => item.percentual),
                                backgroundColor: distribuicaoRacoesDoBanco.map((_, i) => paletaDistribuicao[i % paletaDistribuicao.length]),
                                borderColor: corSurface,
                                borderWidth: 3,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '72%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: corSurface,
                                    titleColor: corTextoPrincipal,
                                    bodyColor: corTick,
                                    borderColor: corGrid,
                                    borderWidth: 1,
                                    callbacks: { label: (item) => item.formattedValue + '%' }
                                }
                            }
                        }
                    });
                }

                // GRÁFICO 6 — EVOLUÇÃO DA CONVERSÃO ALIMENTAR: uma linha por
                // lote (até 6), plotada por ORDEM de pesagem — ver
                // PlanoController::montarEvolucaoConversao. Só existe quando
                // pelo menos um lote já tem 2+ pesagens registradas.
                const canvasEvolucaoConversao = document.getElementById('chartEvolucaoConversao');
                if (canvasEvolucaoConversao && evolucaoConversaoDoBanco.temDados) {
                    const paletaConversao = altoContraste
                        ? ['#ffff00', 'rgba(255,255,255,0.7)', 'rgba(255,255,255,0.45)', 'rgba(255,255,255,0.3)']
                        : ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#4a3aa7'];

                    chartEvolucaoConversao = new Chart(canvasEvolucaoConversao, {
                        type: 'line',
                        data: {
                            labels: evolucaoConversaoDoBanco.labels,
                            datasets: evolucaoConversaoDoBanco.series.map((serie, i) => ({
                                label: serie.label,
                                data: serie.data,
                                borderColor: paletaConversao[i % paletaConversao.length],
                                backgroundColor: hexToRgba(paletaConversao[i % paletaConversao.length], 0.12),
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: paletaConversao[i % paletaConversao.length],
                                spanGaps: false,
                            })),
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: corTick, usePointStyle: true, pointStyle: 'circle', padding: 14, font: { size: 11, weight: '600' } }
                                },
                                tooltip: {
                                    backgroundColor: corSurface,
                                    titleColor: corTextoPrincipal,
                                    bodyColor: corTick,
                                    borderColor: corGrid,
                                    borderWidth: 1,
                                    callbacks: { label: (item) => `${item.dataset.label}: ${item.formattedValue} kg/kg` }
                                }
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: corTick } },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: corGrid },
                                    ticks: { color: corTick, callback: (v) => v + ' kg/kg' }
                                }
                            }
                        }
                    });
                }

            }

            montarGraficosPlano();

            // Reforço de segurança: força os gráficos a se remedirem depois
            // que a página termina de carregar (fontes, imagens), já com o
            // zoom totalmente aplicado — sem isso, um gráfico criado um
            // instante antes do layout "assentar" pode ficar com o tamanho
            // levemente errado.
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (chart7) chart7.resize();
                    if (chartMensal) chartMensal.resize();
                    if (chartDistribuicaoRacoes) chartDistribuicaoRacoes.resize();
                    if (chartEvolucaoConversao) chartEvolucaoConversao.resize();
                }, 150);
            });

            // Recria os gráficos com as cores certas sempre que o tema
            // (claro/escuro/alto contraste) muda — mesmo padrão do Dashboard.
            ['chkDarkMode', 'chkHighContrast'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', () => setTimeout(montarGraficosPlano, 60));
            });

        });

        // ==========================================
        // FILTROS AVANÇADOS (mesmo padrão do Ração/Estoque/Cocho)
        // ==========================================
        const modalFiltros = document.getElementById('modalFiltros');
        const openFiltrosBtn = document.getElementById('openFiltrosBtn');
        const closeFiltrosBtn = document.getElementById('closeFiltrosBtn');

        if (openFiltrosBtn) {
            openFiltrosBtn.onclick = () => modalFiltros.classList.add('active');
        }
        if (closeFiltrosBtn) {
            closeFiltrosBtn.onclick = () => modalFiltros.classList.remove('active');
        }

        // A Fazenda virou a Unidade Produtiva selecionada no topo da tela
        // (não mais um filtro dentro deste modal) — o <select> de Lote já
        // chega do servidor só com os lotes dela, então não existe mais
        // combinação contraditória de Fazenda+Lote pra validar aqui.

        const PARAMS_POR_FILTRO_PLANO = {
            lote_id: ['lote_id'],
            racao_id: ['racao_id'],
        };

        function removerFiltroPlanoDaURL(chaves) {
            const url = new URL(window.location.href);
            chaves.forEach((chave) => {
                (PARAMS_POR_FILTRO_PLANO[chave] || [chave]).forEach((param) => url.searchParams.delete(param));
            });
            window.location.href = url.pathname + (url.search || '');
        }

        // Card "Resumo Mensal": troca o mês exibido sem perder os filtros
        // (fazenda/lote/ração) já aplicados na URL atual.
        function mudarMesResumoMensal(mes) {
            const url = new URL(window.location.href);
            url.searchParams.set('mes', mes);
            window.location.href = url.pathname + url.search;
        }

        document.querySelectorAll('.remover-filtro').forEach((botao) => {
            botao.addEventListener('click', () => removerFiltroPlanoDaURL([botao.dataset.chave]));
        });

        const limparTudoBtn = document.getElementById('limparTudoBtn');
        if (limparTudoBtn) {
            limparTudoBtn.addEventListener('click', () => {
                // "Limpar todos" só zera Lote/Ração — a Unidade Produtiva
                // continua selecionada (ver Filtros Avançados acima).
                window.location.href = '{{ route('plano.listar') }}?fazenda_id={{ $fazendaSelecionadaId }}';
            });
        }

        // ==========================================
        // MODAL DE DETALHES DO LOTE / VINCULAR RAÇÃO — aberto ao clicar no
        // badge de status de qualquer linha da tabela "Consumo por Lote".
        // Todo o conteúdo vem de data-detalhes (JSON já renderizado pelo
        // servidor naquele botão, ver partials/lote-consumo-linhas.blade.php)
        // — nada fixo, cada abertura reflete o lote clicado.
        // ==========================================
        let elementoComFocoAntesDoModalDetalhes = null;

        function abrirModalDetalhesLote(botao) {
            const dados = JSON.parse(botao.dataset.detalhes);

            document.getElementById('detalhesLoteSubtitulo').textContent =
                dados.nome_lote + ' · ' + dados.quantidade_animais + ' ' + (dados.quantidade_animais === 1 ? 'animal' : 'animais');

            document.getElementById('detalhesLoteInfoAnimal').textContent =
                [dados.tipo_animal, dados.sexo, dados.finalidade].filter(Boolean).join(' · ');

            const blocoRacao = document.getElementById('detalhesLoteRacaoBloco');
            const avisoSemRacao = document.getElementById('detalhesLoteSemRacaoAviso');

            if (dados.vinculada) {
                blocoRacao.hidden = false;
                avisoSemRacao.hidden = true;

                document.getElementById('detalhesLoteNomeRacao').textContent = dados.racao.nome_racao;
                document.getElementById('detalhesLoteQuantidadeTotal').textContent = Number(dados.racao.consumo_total_dia).toLocaleString('pt-BR', { maximumFractionDigits: 1 }) + ' kg/dia';
                document.getElementById('detalhesLoteQuantidadePorAnimal').textContent =
                    dados.racao.consumo_por_animal !== null
                        ? Number(dados.racao.consumo_por_animal).toLocaleString('pt-BR', { maximumFractionDigits: 2 }) + ' kg por animal/dia' + (dados.racao.fonte_consumo === 'racao' ? ' (do cadastro da ração — defina abaixo)' : '')
                        : '—';
                document.getElementById('detalhesLoteValorKg').textContent = dados.racao.valor_kg ? `R$ ${dados.racao.valor_kg}/kg` : '—';
                document.getElementById('detalhesLoteEstoque').textContent = dados.racao.estoque_disponivel + ' kg' +
                    (dados.racao.dias_restantes_estoque !== null ? ` (~${dados.racao.dias_restantes_estoque} dias restantes)` : '');

                const corpoDistribuicao = document.getElementById('detalhesLoteDistribuicao');
                corpoDistribuicao.innerHTML = '';
                dados.racao.distribuicao.forEach(function (item) {
                    const linha = document.createElement('tr');
                    const celulaPeriodo = document.createElement('td');
                    celulaPeriodo.textContent = item.periodo;
                    const celulaHorario = document.createElement('td');
                    celulaHorario.textContent = item.horario;
                    const celulaQuantidade = document.createElement('td');
                    celulaQuantidade.textContent = item.quantidade + ' kg';
                    linha.append(celulaPeriodo, celulaHorario, celulaQuantidade);
                    corpoDistribuicao.appendChild(linha);
                });

                const statusPorEstoque = {
                    ok: { classe: 'status-success', texto: 'Plano ativo — estoque suficiente', icone: 'fa-circle-check' },
                    baixo: { classe: 'status-warning', texto: 'Plano ativo — estoque baixo, repor em breve', icone: 'fa-triangle-exclamation' },
                    esgotado: { classe: 'status-danger', texto: 'Plano interrompido — sem estoque disponível', icone: 'fa-circle-xmark' },
                };
                const statusInfo = statusPorEstoque[dados.racao.status_estoque] || statusPorEstoque.ok;

                const statusEl = document.getElementById('detalhesLoteStatusPlano');
                statusEl.innerHTML = '';
                const statusBadge = document.createElement('span');
                statusBadge.className = 'status-badge ' + statusInfo.classe;
                statusBadge.innerHTML = `<i class="fa-solid ${statusInfo.icone}" aria-hidden="true"></i> `;
                statusBadge.append(statusInfo.texto);
                statusEl.appendChild(statusBadge);
            } else {
                blocoRacao.hidden = true;
                avisoSemRacao.hidden = false;
            }

            const select = document.getElementById('vincularRacaoSelect');
            select.innerHTML = '';
            const opcaoNenhuma = document.createElement('option');
            opcaoNenhuma.value = '';
            opcaoNenhuma.textContent = '— Nenhuma —';
            select.appendChild(opcaoNenhuma);

            const formVincular = document.getElementById('formVincularRacao');
            const avisoSemCompativel = document.getElementById('detalhesLoteSemCompativel');

            if (dados.racoes_compativeis.length === 0) {
                avisoSemCompativel.hidden = false;
                formVincular.hidden = true;
            } else {
                avisoSemCompativel.hidden = true;
                formVincular.hidden = false;

                dados.racoes_compativeis.forEach(function (racao) {
                    const opcao = document.createElement('option');
                    opcao.value = racao.id;
                    opcao.textContent = `${racao.nome_racao} (${racao.estoque} kg em estoque)`;
                    if (dados.vinculada && dados.racao.id === racao.id) opcao.selected = true;
                    select.appendChild(opcao);
                });
            }

            formVincular.action = `{{ url('/plano/lotes') }}/${dados.lote_id}/vincular-racao`;
            document.getElementById('vincularRacaoPaginaAtual').value = window.location.href;

            // Quantidade por animal: vem do plano atual; vínculo antigo (sem
            // quantidade por animal) começa com o consumo da ração ÷ animais.
            const campoQtd = document.getElementById('vincularQuantidadePorAnimal');
            const textoTotal = document.getElementById('vincularQuantidadeTotal');
            const blocoQtd = document.getElementById('vincularQuantidadeBloco');
            const animais = Number(dados.quantidade_animais) || 0;
            campoQtd.value = dados.vinculada && dados.racao.consumo_por_animal ? dados.racao.consumo_por_animal : '';
            const atualizarTotal = function () {
                const porAnimal = parseFloat(String(campoQtd.value).replace(',', '.'));
                blocoQtd.hidden = !select.value;
                if (!select.value) { textoTotal.textContent = ''; return; }
                textoTotal.textContent = porAnimal > 0
                    ? `= ${(porAnimal * animais).toLocaleString('pt-BR', { maximumFractionDigits: 1 })} kg de ração por dia para ${animais} ${animais === 1 ? 'animal' : 'animais'}`
                    : 'Deixe em branco para usar o consumo cadastrado na ração.';
            };
            campoQtd.oninput = atualizarTotal;
            select.onchange = function () {
                // Trocar de ração: sugere o consumo da nova ração ÷ animais.
                const escolhida = dados.racoes_compativeis.find(function (r) { return String(r.id) === select.value; });
                if (escolhida && !(dados.vinculada && dados.racao.id === escolhida.id) && animais > 0 && escolhida.consumo > 0) {
                    campoQtd.value = (escolhida.consumo / animais).toFixed(2);
                }
                atualizarTotal();
            };
            atualizarTotal();

            elementoComFocoAntesDoModalDetalhes = document.activeElement;
            const modal = document.getElementById('modalDetalhesLote');
            modal.classList.add('active');
            const primeiroCampo = modal.querySelector('select, input, button');
            if (primeiroCampo) primeiroCampo.focus();
        }

        function fecharModalDetalhesLote() {
            document.getElementById('modalDetalhesLote').classList.remove('active');
            if (elementoComFocoAntesDoModalDetalhes) elementoComFocoAntesDoModalDetalhes.focus();
        }

        // ==========================================
        // MODAL "REGISTRAR PESAGEM" — checkpoint de peso do lote usado pra
        // calcular a Conversão Alimentar real (ver PlanoController::
        // registrarPesagem). Aberto a partir do botão de cada card em
        // partials/conversao-alimentar-cards.blade.php.
        // ==========================================
        let elementoComFocoAntesDoModalPesagem = null;

        function abrirModalPesagem(loteId, nomeLote, pesoAtual) {
            document.getElementById('pesagemSubtitulo').textContent = nomeLote;
            document.getElementById('pesagemPeso').value = pesoAtual > 0 ? pesoAtual : '';
            document.getElementById('pesagemData').value = new Date().toISOString().slice(0, 10);
            document.getElementById('pesagemLoteId').value = loteId;
            document.getElementById('pesagemPaginaAtual').value = window.location.href;
            document.getElementById('formPesagem').action = `{{ url('/plano/lotes') }}/${loteId}/pesagem`;

            elementoComFocoAntesDoModalPesagem = document.activeElement;
            const modal = document.getElementById('modalPesagem');
            modal.classList.add('active');
            const primeiroCampo = modal.querySelector('input');
            if (primeiroCampo) primeiroCampo.focus();
        }

        function fecharModalPesagem() {
            document.getElementById('modalPesagem').classList.remove('active');
            if (elementoComFocoAntesDoModalPesagem) elementoComFocoAntesDoModalPesagem.focus();
        }

        @if ($errors->has('peso_medio_kg') || $errors->has('data'))
            // Erro de validação na pesagem (ex.: data anterior à última já
            // registrada) — reabre o modal com o que o usuário digitou
            // (old()), em vez de voltar pra tela "limpa" perdendo o contexto.
            document.getElementById('modalPesagem').classList.add('active');
        @endif

        window.onclick = function(event) {
            if (event.target == modalFiltros) modalFiltros.classList.remove('active');
            if (event.target == document.getElementById('modalDetalhesLote')) fecharModalDetalhesLote();
            if (event.target == document.getElementById('modalPesagem')) fecharModalPesagem();
        }

        // Fecha o modal aberto ao pressionar Esc — sem isso, quem navega só
        // pelo teclado não tinha nenhuma forma de fechar o modal (o clique
        // fora dele, tratado acima, não é alcançável via teclado).
        document.addEventListener('keydown', function(event) {
            if (event.key !== 'Escape') return;
            if (modalFiltros && modalFiltros.classList.contains('active')) modalFiltros.classList.remove('active');
            const modalDetalhes = document.getElementById('modalDetalhesLote');
            if (modalDetalhes && modalDetalhes.classList.contains('active')) fecharModalDetalhesLote();
            const modalPesagem = document.getElementById('modalPesagem');
            if (modalPesagem && modalPesagem.classList.contains('active')) fecharModalPesagem();
        });

        // Card "Consumo por Lote - Plano Diário": busca no backend os lotes que
        // ficaram de fora da carga inicial e insere na tabela — preservando
        // os filtros (fazenda/lote/ração) ativos na URL atual. Depois de
        // carregadas, o botão vira "Ocultar" pra poder recolher de volta
        // sem precisar recarregar a página (mesmo padrão de
        // consultarConversoes()) — sem nova requisição, as linhas extras só
        // ficam escondidas, não são removidas do DOM.
        let lotesExtrasCarregados = false;
        let linhasLoteExtras = [];

        async function consultarLotes() {
            const botao = document.getElementById('btnConsultarLotes');

            if (lotesExtrasCarregados) {
                const estaAberto = linhasLoteExtras.length > 0 && linhasLoteExtras[0].style.display !== 'none';
                linhasLoteExtras.forEach((linha) => { linha.style.display = estaAberto ? 'none' : ''; });
                botao.textContent = estaAberto ? 'Consultar Lotes' : 'Ocultar lotes extras';
                return;
            }

            const textoOriginal = botao.textContent;
            botao.disabled = true;
            AgriUI.indicarCarregando(botao, 'Carregando…');

            try {
                const resposta = await fetch('{{ route('plano.lotesConsumoJson') }}' + window.location.search, {
                    headers: { 'Accept': 'application/json' },
                });

                if (resposta.status === 401) {
                    window.location.href = '{{ route('login.index') }}';
                    return;
                }

                const dados = await resposta.json();

                if (dados.success) {
                    const corpo = document.getElementById('corpoConsumoLotes');
                    const linhasAntes = corpo.children.length;
                    corpo.insertAdjacentHTML('beforeend', dados.html);
                    linhasLoteExtras = Array.from(corpo.children).slice(linhasAntes);
                    lotesExtrasCarregados = true;
                    botao.disabled = false;
                    botao.textContent = 'Ocultar lotes extras';
                } else {
                    botao.disabled = false;
                    botao.textContent = textoOriginal;
                }
            } catch (erro) {
                botao.disabled = false;
                botao.textContent = textoOriginal;
            }
        }

        // Card "Conversão Alimentar por Lote": busca no backend os cards
        // que ficaram de fora da carga inicial — preservando os filtros
        // (fazenda/lote/ração) ativos na URL atual. Depois de carregados,
        // o botão vira "Fechar" pra poder recolher de volta sem precisar
        // recarregar a página (e sem nova requisição, já que os cards
        // extras só ficam escondidos, não são removidos do DOM).
        let conversoesExtrasCarregadas = false;

        async function consultarConversoes() {
            const botao = document.getElementById('btnConsultarConversoes');
            const texto = document.getElementById('textoConsultarConversoes');
            const extras = document.getElementById('conversoesExtras');

            if (conversoesExtrasCarregadas) {
                const estaAberto = !extras.hidden;
                extras.hidden = estaAberto;
                texto.textContent = estaAberto ? 'Ver todas as conversões' : 'Ocultar conversões extras';
                return;
            }

            const textoOriginal = texto.textContent;
            botao.disabled = true;
            texto.textContent = 'Carregando...';

            try {
                const resposta = await fetch('{{ route('plano.conversoesJson') }}' + window.location.search, {
                    headers: { 'Accept': 'application/json' },
                });

                if (resposta.status === 401) {
                    window.location.href = '{{ route('login.index') }}';
                    return;
                }

                const dados = await resposta.json();

                if (dados.success) {
                    document.getElementById('corpoConversoesAlimentares')
                        .insertAdjacentHTML('beforeend', '<div id="conversoesExtras">' + dados.html + '</div>');
                    conversoesExtrasCarregadas = true;
                    botao.disabled = false;
                    texto.textContent = 'Ocultar conversões extras';
                } else {
                    botao.disabled = false;
                    texto.textContent = textoOriginal;
                }
            } catch (erro) {
                botao.disabled = false;
                texto.textContent = textoOriginal;
            }
        }

        // Popover de explicação dos botões "?" (ex.: "O que é conversão
        // alimentar?"). Um único elemento reaproveitado e posicionado via
        // JS, com delegação de evento em vez de listener por botão — assim
        // continua funcionando nos cards carregados depois via AJAX em
        // "Ver todas as conversões", sem precisar religar nada.
        (function () {
            let popover = document.getElementById('caTooltipPopover');
            if (!popover) {
                popover = document.createElement('div');
                popover.id = 'caTooltipPopover';
                popover.className = 'tooltip-popover';
                popover.setAttribute('role', 'tooltip');
                document.body.appendChild(popover);
            }

            function fecharTooltipPopover() {
                popover.classList.remove('is-open');
                const botaoAberto = document.querySelector('.tooltip-icon[aria-expanded="true"]');
                if (botaoAberto) botaoAberto.setAttribute('aria-expanded', 'false');
            }

            document.addEventListener('click', function (evento) {
                const botao = evento.target.closest('.tooltip-icon');

                if (botao) {
                    const jaAberto = botao.getAttribute('aria-expanded') === 'true';
                    fecharTooltipPopover();

                    if (!jaAberto) {
                        popover.textContent = botao.dataset.tooltipText || '';
                        popover.classList.add('is-open');
                        botao.setAttribute('aria-expanded', 'true');

                        const rect = botao.getBoundingClientRect();
                        const scrollY = window.scrollY || window.pageYOffset;
                        const scrollX = window.scrollX || window.pageXOffset;

                        popover.style.top = (rect.bottom + scrollY + 8) + 'px';

                        let left = rect.left + scrollX - 8;
                        const maxLeft = document.documentElement.clientWidth - popover.offsetWidth - 12 + scrollX;
                        if (left > maxLeft) left = maxLeft;
                        if (left < 8 + scrollX) left = 8 + scrollX;
                        popover.style.left = left + 'px';
                    }
                    return;
                }

                if (!evento.target.closest('.tooltip-popover')) {
                    fecharTooltipPopover();
                }
            });

            document.addEventListener('keydown', function (evento) {
                if (evento.key === 'Escape') fecharTooltipPopover();
            });
        })();
    </script>
@endpush