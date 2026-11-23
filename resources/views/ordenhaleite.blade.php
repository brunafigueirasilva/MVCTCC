@extends('layouts.app')

@section('title', 'AgriFarm | Ordenha e Leite')
@section('body-attrs')data-tema-padrao="escuro"@endsection

@push('scripts')
<script src="{{ asset('js/vendor/chart.min.js') }}"></script>
@endpush

@push('styles')
    <style>
        /* ==========================================================================
        1. RESET, VARIÁVEIS DE SISTEMA E CONFIGURAÇÕES GERAIS
        ========================================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }


        :root {
            --bg-principal: #f8fafc;
            --text-principal: #0f172a;
            --text-secundario: #64748b;
            --card-bg: #ffffff;
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 8px -1px rgba(15, 23, 42, 0.02);
            --card-border: 1px solid rgba(226, 232, 240, 0.8);

            /* Cores dos Cartões Modernos */
            --card-verde-bg: #f0fdf4;
            --card-verde-border: var(--verde-claro, #e8f5ee);
            --card-verde-icon-bg: var(--verde-claro, #e8f5ee);
            --card-verde-texto: var(--verde-texto, #0e4a27);

            --card-azul-bg: #f0f7ff;
            --card-azul-border: #e0f2fe;
            --card-azul-icon-bg: #e0f0ff;
            --card-azul-texto: #0369a1;

            --card-laranja-bg: #fff7ed;
            --card-laranja-border: #ffedd5;
            --card-laranja-icon-bg: #ffedd5;
            --card-laranja-texto: #c2410c;

            --card-roxo-bg: #faf5ff;
            --card-roxo-border: #f3e8ff;
            --card-roxo-icon-bg: #f3e8ff;
            --card-roxo-texto: #6b21a8;

            --card-ciano-bg: #ecfeff;
            --card-ciano-border: #cffafe;
            --card-ciano-icon-bg: #cffafe;
            --card-ciano-texto: #0e7490;

            /* Superfícies "neutras" (dica de seleção, item de alerta padrão)
               usadas nos dois temas — mantidas como variável para nunca
               precisar de uma cor fixa duplicada por tema. */
            --surface-muted-bg: #f8fafc;
            --surface-muted-border: #cbd5e1;

            /* Paleta dos gráficos (Chart.js). Ordem fixa e validada para
               distinção segura em daltonismo (ver skill de dataviz). */
            --chart-1: #16a34a;
            --chart-2: #2563eb;
            --chart-3: #f97316;
            --chart-4: #7c3aed;
            --chart-5: #0d9488;
            --chart-6: #e11d48;
            --chart-grid: #eef2f6;
            --chart-tick: #94a3b8;

        }

        body {
            background: var(--bg-principal);
            color: var(--text-principal);
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar, .navbar, .main, .card, .box, .premium-card {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease, filter 0.25s ease;
        }

        /* ==========================================================================
        2. NAVBAR SUPERIOR
        (blur do modal de acessibilidade e estrutura de navbar/sidebar/main
        já vêm de layouts/app.blade.php — fonte única, não duplicar aqui)
        ========================================================================== */





        /* Logo diretamente sobre o fundo escuro da navbar, sem caixa/quadrado
           atrás — apenas a imagem (com fundo transparente) maior e nítida. */













        /* Botão de acessibilidade inserido dentro da navbar (ver partials/acessibilidade). */
        .navbar-right .accessibility-trigger {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid var(--surface-border);
        }
        .navbar-right .accessibility-trigger:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* ==========================================================================
        3. SIDEBAR LATERAL
        ========================================================================== */


        /* Fundo escurecido atrás do menu lateral quando ele abre como
           "gaveta" (drawer) por cima do conteúdo em telas de celular/tablet
           — antes não existia, então o menu abria sem nenhuma forma de
           fechar clicando fora dele. Só existe (via @media) em telas
           estreitas, onde a sidebar vira overlay. */

        @media (max-width: 992px) {
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
        }




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


        /* ==========================================================================
        4. ÁREA PRINCIPAL E MÓDULOS DE CONTEÚDO
        ========================================================================== */
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
            padding-top: 20px;
        }


        .header {
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-title :is(h1, h2) {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-principal);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .header-title p {
            color: var(--text-secundario);
            font-size: 0.9375rem;
        }

        /* Grid Multilinhamento para Seleção e Métricas Finais */
        .dashboard-layout-grid {
            display: flex;
            flex-direction: column;
            gap: 22px;
            margin-bottom: 28px;
        }

        .fazenda-selector-bar {
            background: var(--card-bg);
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .fazenda-selector-bar-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secundario);
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
            border: 1px solid var(--surface-muted-border);
            outline: none;
            background: var(--card-bg);
            color: var(--text-principal);
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
            color: var(--text-secundario);
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--surface-muted-bg);
            border: 1px dashed var(--surface-muted-border);
            padding: 8px 14px;
            border-radius: 10px;
        }

        .selector-hint i { color: #f59e0b; }

        /* ==========================================================================
        5. ESTILIZAÇÃO DOS CARTÕES PREMIUM
        ========================================================================== */
        .cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(240px, 100%), 1fr));
            gap: 20px;
        }

        .premium-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-wrap: wrap; /* card estreito: ícone vai pra cima do texto */
            align-items: center;
            gap: 12px 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.015);
            border: 1px solid var(--card-border, rgba(226, 232, 240, 0.8));
            min-width: 0;
        }

        .premium-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px -10px rgba(15, 23, 42, 0.12);
        }

        /* Selo "3D": gradiente diagonal + brilho interno + glow colorido —
           no lugar do círculo plano (fundo pastel + ícone da mesma cor). */
        .premium-card-icon-zone {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.375rem;
            flex-shrink: 0;
            color: #ffffff;
            box-shadow: inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        }

        .premium-card-data-zone {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            flex: 1 1 120px;
        }

        .premium-card-label {
            font-size: 0.844rem;
            font-weight: 600;
            letter-spacing: -0.1px;
        }

        .premium-card-value {
            font-size: 1.688rem;
            font-weight: 700;
            color: var(--text-principal);
            line-height: 1.15;
            /* Antes: nowrap + reticências — com texto ampliado/zoom o valor
               (ex.: "1.250,0 L") virava "1.25…" e o número se perdia. Agora
               quebra linha nos espaços, nunca esconde o valor. */
            overflow-wrap: break-word;
        }

        .premium-card-subtext {
            font-size: 0.75rem;
            color: var(--text-secundario);
            font-weight: 500;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .trend-up { color: var(--verde-texto, #0e4a27); }
        .trend-down { color: #dc2626; }
        .trend-neutral { color: #94a3b8; }

        /* Cada variante: fundo do card com um degradê sutil (não mais um
           bloco de cor pastel chapado) e o selo do ícone com gradiente
           vivo + glow — sem borda/linha de destaque lateral. */
        .premium-verde {
            background: linear-gradient(160deg, color-mix(in srgb, var(--verde-principal, #0e4a27) 12%, transparent), transparent 60%), var(--card-bg);
        }
        .premium-verde .premium-card-icon-zone {
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            box-shadow: 0 6px 16px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        }
        .premium-verde .premium-card-label { color: var(--card-verde-texto); }

        .premium-azul {
            background: linear-gradient(160deg, rgba(37, 99, 235, 0.12), transparent 60%), var(--card-bg);
        }
        .premium-azul .premium-card-icon-zone {
            background: linear-gradient(145deg, #60a5fa, #2563eb);
            box-shadow: 0 6px 16px -4px rgba(37, 99, 235, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        }
        .premium-azul .premium-card-label { color: var(--card-azul-texto); }

        .premium-laranja {
            background: linear-gradient(160deg, rgba(249, 115, 22, 0.12), transparent 60%), var(--card-bg);
        }
        .premium-laranja .premium-card-icon-zone {
            background: linear-gradient(145deg, #fb923c, #ea580c);
            box-shadow: 0 6px 16px -4px rgba(234, 88, 12, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        }
        .premium-laranja .premium-card-label { color: var(--card-laranja-texto); }

        .premium-roxo {
            background: linear-gradient(160deg, rgba(124, 58, 237, 0.12), transparent 60%), var(--card-bg);
        }
        .premium-roxo .premium-card-icon-zone {
            background: linear-gradient(145deg, #c084fc, #7c3aed);
            box-shadow: 0 6px 16px -4px rgba(124, 58, 237, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        }
        .premium-roxo .premium-card-label { color: var(--card-roxo-texto); }

        .premium-ciano {
            background: linear-gradient(160deg, rgba(8, 145, 178, 0.12), transparent 60%), var(--card-bg);
        }
        .premium-ciano .premium-card-icon-zone {
            background: linear-gradient(145deg, #22d3ee, #0891b2);
            box-shadow: 0 6px 16px -4px rgba(8, 145, 178, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        }
        .premium-ciano .premium-card-label { color: var(--card-ciano-texto); }

        /* ==========================================================================
        6. SEÇÃO DE GRÁFICOS E ALERTAS
        ========================================================================== */
        /* Grid fluido em vez de colunas fixas + media queries por largura de
           janela: ele se reorganiza sozinho a partir do espaço realmente
           disponível no container. Isso corrige dois problemas do layout
           anterior — (1) ao usar o "Zoom do Sistema" do painel de
           acessibilidade o conteúdo passava a precisar de mais espaço
           visual, mas a media query continuava calculada em cima da largura
           real da janela (que não muda com o zoom da página), então os 3
           painéis ficavam espremidos/cortados; (2) largarguras
           intermediárias que caíam "entre" dois breakpoints fixos. Com
           auto-fit + minmax, cada painel nunca fica menor que 320px — reflui
           para 2 ou 1 coluna sozinho, em qualquer zoom ou resolução. */
        .content-graphics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
            gap: 22px;
            align-items: stretch;
        }

        .box {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 26px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .box :is(h2, h3) {
            margin-bottom: 20px;
            color: var(--text-principal);
            font-size: 0.969rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .box :is(h2, h3) i {
            font-size: 0.875rem;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: var(--card-verde-bg);
            flex-shrink: 0;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 290px;
        }

        .chart-empty-overlay {
            display: none;
            position: absolute;
            inset: 0;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: var(--card-bg);
        }

        .chart-empty-overlay.visible { display: flex; }

        .no-data-message {
            color: var(--text-secundario);
            font-size: 0.844rem;
            line-height: 1.5;
        }

        .no-data-message i {
            display: block;
            font-size: 1.625rem;
            margin-bottom: 10px;
            color: #cbd5e1;
        }

        /* ==========================================================================
        7. HISTÓRICO DE ORDENHAS (tabela "ver mais")
        ========================================================================== */
        .ordenha-historico-box { margin-top: 22px; }

        .ordenha-historico-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .ordenha-periodo-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ordenha-periodo-form label {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 0.75rem;
            color: var(--text-secundario);
            font-weight: 600;
        }

        .ordenha-periodo-form input[type="date"] {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            font-size: 0.8125rem;
        }

        .btn-filtrar-periodo {
            align-self: flex-end;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: border-color 0.18s ease, color 0.18s ease;
        }

        .btn-filtrar-periodo:hover { border-color: #0891b2; color: #0891b2; }

        .ordenha-table-wrap { overflow-x: auto; }

        .ordenha-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .ordenha-table th {
            text-align: left;
            padding: 10px 14px;
            color: var(--text-secundario);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid var(--surface-muted-border);
            white-space: nowrap;
        }

        .ordenha-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--surface-muted-border);
            color: var(--text-principal);
            white-space: nowrap;
        }

        .ordenha-table tbody tr:hover { background: var(--surface-muted-bg); }

        .categoria-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.719rem;
            font-weight: 600;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            color: var(--text-secundario);
        }

        .btn-ver-mais {
            display: block;
            margin: 16px auto 0;
            padding: 10px 24px;
            border-radius: 10px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            font-size: 0.844rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s ease, border-color 0.18s ease;
        }

        .btn-ver-mais:hover { background: var(--surface-muted-bg); border-color: #0891b2; color: #0891b2; }
        .btn-ver-mais:disabled { opacity: 0.6; cursor: default; }

        .alerts-wrapper {
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow-y: auto;
            max-height: 290px;
            padding-right: 4px;
            flex: 1;
        }

        /* Quando o card não tem alertas para listar (seleciona uma fazenda /
           nenhum alerta pendente), o texto ficava colado no topo do card,
           com um vão vazio embaixo. Enquanto o único filho for a mensagem
           de estado vazio, ela ocupa e centraliza o espaço todo. */
        .alerts-wrapper:has(> .no-data-message) {
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .alert-item {
            background: var(--surface-muted-bg);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            border-left: 4px solid var(--surface-muted-border);
            border: 1px solid var(--surface-muted-border);
            border-left-width: 5px;
        }

        /* Os tons "críticos"/"baixos" são pastéis pensados para o tema
           claro; sem uma versão própria, apareciam claros e destoantes
           sobre o fundo escuro. Nos dois temas o texto já usa
           var(--text-principal)/var(--text-secundario), então só a cor de
           fundo/realce precisa de uma variante por tema. */
        .alert-item.critico {
            border-left-color: #ef4444;
            background: #fef2f2;
        }

        .alert-item.baixo {
            border-left-color: #f97316;
            background: #fff7ed;
        }

        body.dark-theme .alert-item.critico { background: rgba(239, 68, 68, 0.12); }
        body.dark-theme .alert-item.baixo { background: rgba(249, 115, 22, 0.12); }

        .alert-item-main {
            display: flex;
            gap: 10px;
        }

        .alert-item-text h4 {
            font-size: 0.844rem;
            font-weight: 600;
            color: var(--text-principal);
            margin-bottom: 4px;
        }

        .alert-item-text p {
            font-size: 0.781rem;
            color: var(--text-secundario);
            line-height: 1.4;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.6875rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-critico {
            background: #fee2e2;
            color: #ef4444;
        }

        .badge-baixo {
            background: #ffedd5;
            color: #ea580c;
        }

        body.dark-theme .badge-critico { background: rgba(239, 68, 68, 0.18); color: #fca5a5; }
        body.dark-theme .badge-baixo { background: rgba(249, 115, 22, 0.18); color: #fdba74; }

        /* ==========================================================================
        8. CONTROLES DE ACESSIBILIDADE APLICADOS (TEMAS)
        Modo escuro com a paleta verde do projeto (nada de cinza-azulado).
        O modal/botões do painel de acessibilidade em si (incluindo o alto
        contraste) são estilizados pelo partials.acessibilidade.
        ========================================================================== */
        body.dark-theme {
            --bg-principal: #0a1810;
            --text-principal: #f1f9f4;
            --text-secundario: #9db8a8;
            --card-bg: #12251a;
            --card-border: 1px solid color-mix(in srgb, var(--verde-secundario, #16a765) 16%, transparent);
            --card-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);

            /* Mesma ideia do tema claro: um degrau a mais de escuro que o
               fundo da página, só para separar visualmente navbar/sidebar
               do conteúdo — não um tom totalmente novo. */
            --surface-muted-bg: #0a1810;
            --surface-muted-border: #2a4d38;


            --chart-1: #16a34a;
            --chart-2: #3b82f6;
            --chart-3: #ea580c;
            --chart-4: #a855f7;
            --chart-5: #0d9488;
            --chart-6: #f43f5e;
            --chart-grid: rgba(74, 222, 128, 0.10);
            --chart-tick: #9db8a8;

        }
        body.dark-theme .navbar-right .accessibility-trigger:hover { background: rgba(255, 255, 255, 0.16); color: #ffffff; }

        body.dark-theme .navbar-right .accessibility-trigger { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.16); color: #f1f5f9; }
        body.dark-theme .box :is(h2, h3) i { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); }
        body.dark-theme .selector-hint { background: #0a1810; border-color: #2a4d38; color: #9db8a8; }

        /* Os cartões premium usavam fundo pastel claro fixo (pensado só para
           o tema claro); no escuro isso deixava o texto branco ilegível em
           cima do próprio fundo claro. Agora, no escuro, o cartão fica com o
           mesmo tom de fundo dos outros boxes e a cor da categoria vira um
           destaque (ícone, rótulo e borda esquerda) em vez de um bloco cheio. */
        body.dark-theme .premium-card {
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Mesmo degradê + selo 3D do tema claro (cores autocontidas, já
           funcionam bem no fundo escuro) — só o rótulo muda de tom pra um
           pouco mais claro, sem a linha de borda esquerda que existia antes. */
        body.dark-theme .premium-verde .premium-card-label { color: var(--verde-texto, #0e4a27); }
        body.dark-theme .premium-azul .premium-card-label { color: #38bdf8; }
        body.dark-theme .premium-laranja .premium-card-label { color: #fb923c; }
        body.dark-theme .premium-roxo .premium-card-label { color: #c084fc; }
        body.dark-theme .premium-ciano .premium-card-label { color: #22d3ee; }

        body.dark-theme .select-fazenda { color: #fff; }

        body.high-contrast {
            background: #000000 !important;
            color: #ffff00 !important;

            /* Sem isso, textos que usam --text-principal/--text-secundario
               ficavam com a cor cinza-escura do tema claro sobre o fundo
               preto forçado — quase ilegível. */
            --text-principal: #ffff00;
            --text-secundario: #ffff00;

            --chart-grid: #ffff00;
            --chart-tick: #ffff00;

            --panel-bg: #000000;
            --panel-text: #ffffff;
            --panel-item-bg: #111111;
            --panel-btn-bg: #ffffff;
            --panel-btn-text: #000000;
            --panel-btn-hover: #ffff00;
        }

        body.high-contrast .card,
        body.high-contrast .box,
        body.high-contrast .premium-card,
        body.high-contrast .fazenda-selector-bar,
        body.high-contrast .navbar,
        body.high-contrast .sidebar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }

        /* A logo original (verde/colorida) fica ilegível sobre o fundo preto
           do alto contraste. Convertendo para um silhueta sólida branca (a
           mesma técnica já usada no logo do Google no cadastro), ela some do
           preto e mantém contraste consistente com o resto do tema. */
        body.high-contrast .navbar-logo img {
            filter: brightness(0) invert(1);
        }

        body.high-contrast h2, body.high-contrast h3, body.high-contrast h4,
        body.high-contrast h1, body.high-contrast p, body.high-contrast span,
        body.high-contrast i, body.high-contrast select, body.high-contrast .premium-card-value,
        body.high-contrast .premium-card-label {
            color: #ffff00 !important;
        }

        body.high-contrast .menu a.active,
        body.high-contrast .menu a:hover {
            background: #1a1a00 !important;
        }

        /* Ícones dos cartões e badges de alerta também seguem o padrão
           preto/amarelo em vez de manterem as cores pastel originais. */
        body.high-contrast .premium-card-icon-zone,
        body.high-contrast .toggle-menu-btn,
        body.high-contrast .user-avatar,
        body.high-contrast .box :is(h2, h3) i {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .badge {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .alert-item {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            border-left: 5px solid #ffff00 !important;
        }

        /* Estas três superfícies usam as variáveis --card-bg/--surface-muted-bg,
           que o alto contraste não redefine — sem esta regra, o seletor de
           fazenda e os avisos ficavam com fundo branco (e texto amarelo em
           cima, quase ilegível) no meio de uma tela toda preta. */
        body.high-contrast .select-fazenda,
        body.high-contrast .selector-hint,
        body.high-contrast .chart-empty-overlay {
            background: #000000 !important;
            border-color: #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .navbar-logout {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
        }

        body.high-contrast a:focus-visible,
        body.high-contrast button:focus-visible,
        body.high-contrast select:focus-visible {
            outline: 3px solid #ffff00 !important;
            outline-offset: 2px !important;
        }

        /* ==========================================================================
        9. ADAPTAÇÕES RESPONSIVAS
        ========================================================================== */

        @media (max-width: 992px) {
            .main {
                margin-left: 0;
                width: 100%;
                padding: 24px;
                padding-top: 20px;
            }

        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .header-title :is(h1, h2) { font-size: 1.5rem; }
            .main { padding: 18px; padding-top: 20px; }
            .box { padding: 20px; }
            .chart-container { height: 250px; }
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header-title p { font-size: 0.844rem; }
            .premium-card-value { font-size: 1.438rem; }
            .fazenda-selector-bar { flex-direction: column; align-items: stretch; padding: 16px; }
            .select-fazenda { max-width: 100%; }
            .selector-hint { width: 100%; }
            .cards-row { grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr)); gap: 14px; }
        }

        @media (max-width: 400px) {
            .premium-card { flex-direction: column; align-items: flex-start; gap: 14px; padding: 18px; }
            .premium-card-icon-zone { width: 48px; height: 48px; font-size: 1.188rem; }
            .navbar-right { gap: 10px; }
            .navbar-logout span { display: none; }
        }


        /* Grade de indicadores equilibrada: 4, 2 ou 1 coluna conforme o
           espaço REAL do conteúdo (menu aberto ou recolhido), nunca uma
           última linha com cartão "sobrando" (antes: 8 cartões em 3
           colunas deixavam um buraco na grade). */
        .dashboard-layout-grid { container-type: inline-size; }
        @container (min-width: 940px) {
            .cards-row { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        /* 4 colunas apertadas (notebook): selo em cima e valor embaixo, como
           no celular — o valor usa a largura toda do cartão (antes "R$" e o
           número quebravam em duas linhas) e rótulos/valores ficam alinhados
           entre os cartões. */
        @container (min-width: 940px) and (max-width: 1239.98px) {
            .premium-card { flex-direction: column; align-items: flex-start; gap: 14px; padding: 20px; }
            .premium-card-icon-zone { width: 48px; height: 48px; font-size: 1.125rem; }
            .premium-card-value { font-size: 1.5rem; }
        }
        @container (min-width: 460px) and (max-width: 939.98px) {
            .cards-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
@endpush

@section('content')
    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <main class="main" id="mainArea">
        <div class="header">
            <div class="header-title">
                <h1><i class="fa-solid fa-droplet titulo-icone" aria-hidden="true"></i> Ordenha e Leite</h1>
                <p>Produção de leite por lote — registro, histórico e indicadores de ordenha.</p>
            </div>
        </div>

        @php
            // Toda a matemática (percentuais, médias) já chega pronta do
            // back-end (OrdenhaLeiteController). Aqui só decidimos ícone/cor/
            // texto de apresentação a partir dos números prontos — nenhum
            // cálculo novo acontece na view.
            $semFazenda = empty($fazendaSelecionadaId);

            $trendIcone = function ($valor) {
                if (is_null($valor)) return 'fa-minus';
                if ($valor > 0) return 'fa-arrow-up';
                if ($valor < 0) return 'fa-arrow-down';
                return 'fa-minus';
            };
            $trendClasse = function ($valor) {
                if (is_null($valor) || $valor == 0) return 'trend-neutral';
                return $valor > 0 ? 'trend-up' : 'trend-down';
            };
        @endphp

        <div class="dashboard-layout-grid">
            <!-- Barra Seletora Integrada -->
            <div class="fazenda-selector-bar">
                <div class="fazenda-selector-bar-label"><i class="fa-solid fa-tractor"></i> Unidade Produtiva:</div>
                <select class="select-fazenda" aria-label="Selecionar fazenda" onchange="window.location.href='?fazenda_id=' + this.value">
                    <option value="">Escolha uma fazenda</option>
                    @foreach ($fazendas as $fazenda)
                        <option value="{{ $fazenda->id }}"
                            {{ $fazenda->id == ($fazendaSelecionadaId ?? null) ? 'selected' : '' }}>
                            {{ $fazenda->nome_fazenda }}
                        </option>
                    @endforeach
                </select>
                @if ($semFazenda)
                    <div class="selector-hint">
                        <i class="fa-solid fa-circle-info"></i>
                        Selecione uma fazenda para carregar os dados reais.
                    </div>
                @endif
            </div>

            <!-- LINHA DE CARTÕES: DESIGN PREMIUM -->
            <div class="cards-row">
                <div class="premium-card premium-verde">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Lotes em Produção</span>
                        <span class="premium-card-value">{{ $lotesComOrdenha ?? 0 }}</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @else
                                Média: <strong>{{ number_format($producaoMediaDiaria ?? 0, 1, ',', '.') }} L/dia</strong> (últimos 7 dias)
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-azul">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-jug-detergent"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Produção de Leite Hoje</span>
                        <span class="premium-card-value">{{ number_format($producaoLeiteHoje ?? 0, 1, ',', '.') }} L</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif (!is_null($variacaoProducaoHoje))
                                <i class="fa-solid {{ $trendIcone($variacaoProducaoHoje) }} {{ $trendClasse($variacaoProducaoHoje) }}"></i>
                                <span class="{{ $trendClasse($variacaoProducaoHoje) }}">{{ number_format(abs($variacaoProducaoHoje), 1, ',', '.') }}%</span> vs. ontem
                            @else
                                Sem registro de ontem para comparar
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-ciano">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Produção do Mês</span>
                        <span class="premium-card-value">{{ number_format(end($dadosEvolucaoProducao) ?: 0, 0, ',', '.') }} L</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif (!is_null($variacaoProducaoMes))
                                <i class="fa-solid {{ $trendIcone($variacaoProducaoMes) }} {{ $trendClasse($variacaoProducaoMes) }}"></i>
                                <span class="{{ $trendClasse($variacaoProducaoMes) }}">{{ number_format(abs($variacaoProducaoMes), 1, ',', '.') }}%</span> vs. mês anterior
                            @else
                                Sem registro do mês anterior para comparar
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-roxo">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Lote com Maior Produção</span>
                        <span class="premium-card-value" style="font-size: 1.25rem;">{{ $melhorLoteNome ?? '—' }}</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif ($melhorLoteNome)
                                <strong>{{ number_format($melhorLoteTotal ?? 0, 1, ',', '.') }} L</strong> produzidos este mês
                            @else
                                Nenhuma ordenha registrada este mês
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MÓDULO DOS GRÁFICOS: produção de leite por lote, sempre a
             partir dos registros reais de Ordenha. -->
        <div class="content-graphics-grid">
            <div class="box">
                <h2 id="sec-producao-dia"><i class="fa-solid fa-droplet" style="color: #0891b2;"></i> Produção de Leite por Dia (7 dias)</h2>
                <div class="chart-container">
                    <canvas id="producaoDiariaChart"></canvas>
                    <div class="chart-empty-overlay" id="producaoDiariaEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-chart-simple"></i>
                            {{ $semFazenda ? 'Selecione uma fazenda para ver a produção.' : 'Nenhuma ordenha registrada nos últimos 7 dias.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">
                <h2 id="sec-evolucao-producao"><i class="fa-solid fa-chart-line" style="color: #0891b2;"></i> Evolução da Produção (6 meses)</h2>
                <div class="chart-container">
                    <canvas id="evolucaoProducaoChart"></canvas>
                    <div class="chart-empty-overlay" id="evolucaoProducaoEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-chart-simple"></i>
                            {{ $semFazenda ? 'Selecione uma fazenda para ver a evolução.' : 'Sem produção registrada nos últimos 6 meses.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">
                <h2 id="sec-producao-lote"><i class="fa-solid fa-ranking-star" style="color: #0891b2;"></i> Produção por Lote (mês atual)</h2>
                <div class="chart-container">
                    <canvas id="producaoLoteChart"></canvas>
                    <div class="chart-empty-overlay" id="producaoLoteEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-cow"></i>
                            {{ $semFazenda ? 'Selecione uma fazenda para ver a produção.' : 'Nenhuma ordenha registrada este mês.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTÓRICO: ordenhas registradas no período selecionado, com "ver
             mais" AJAX mesmo padrão do Plano Alimentar/Relatórios. -->
        <div class="box ordenha-historico-box">
            <div class="ordenha-historico-header">
                <h2 id="sec-ordenhas-periodo"><i class="fa-solid fa-clock-rotate-left" style="color: #0891b2;"></i> Ordenhas Registradas por Período</h2>
                <form method="GET" class="ordenha-periodo-form">
                    @if ($fazendaSelecionadaId)
                        <input type="hidden" name="fazenda_id" value="{{ $fazendaSelecionadaId }}">
                    @endif
                    <label>De
                        <input type="date" name="data_inicio" value="{{ $dataInicio ?? '' }}" max="{{ now()->format('Y-m-d') }}">
                    </label>
                    <label>Até
                        <input type="date" name="data_fim" value="{{ $dataFim ?? '' }}" max="{{ now()->format('Y-m-d') }}">
                    </label>
                    <button type="submit" class="btn-filtrar-periodo"><i class="fa-solid fa-filter"></i> Filtrar</button>
                </form>
            </div>
            @if (($historicoOrdenhasTotal ?? 0) > 0)
                <div class="ordenha-table-wrap">
                    <table class="ordenha-table">
                        <thead>
                            <tr>
                                <th scope="col">Data</th>
                                <th scope="col">Horário</th>
                                <th scope="col">Turno</th>
                                <th scope="col">Lote</th>
                                <th scope="col">Litros</th>
                            </tr>
                        </thead>
                        <tbody id="corpoHistoricoOrdenhas">
                            @include('partials.ordenhas-recentes-linhas', ['ordenhas' => $historicoOrdenhas])
                        </tbody>
                    </table>
                </div>
                @if ($historicoOrdenhasTotal > $historicoOrdenhas->count())
                    <button type="button" class="btn-ver-mais" id="btnVerMaisHistorico">Ver mais ordenhas</button>
                @endif
            @else
                <div class="no-data-message">
                    <i class="fa-solid fa-droplet"></i>
                    {{ $semFazenda ? 'Selecione uma fazenda para ver o histórico.' : 'Nenhuma ordenha registrada no período selecionado.' }}
                </div>
            @endif
        </div>
    </main>

@endsection

@push('scripts')
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
            /* --- HISTÓRICO: "ver mais ordenhas" (restante dos últimos 60
               dias), preservando a fazenda selecionada na URL atual. Mesmo
               padrão de "Consultar Lotes"/"Consultar mais Conversões" do
               Plano Alimentar. --- */
            const btnVerMaisHistorico = document.getElementById('btnVerMaisHistorico');
            if (btnVerMaisHistorico) {
                btnVerMaisHistorico.addEventListener('click', async function() {
                    const textoOriginal = btnVerMaisHistorico.textContent;
                    btnVerMaisHistorico.disabled = true;
                    AgriUI.indicarCarregando(btnVerMaisHistorico, 'Carregando…');

                    try {
                        const resposta = await fetch('{{ route('ordenha.historicoJson') }}' + window.location.search, {
                            headers: { 'Accept': 'application/json' },
                        });

                        if (resposta.status === 401) {
                            window.location.href = '{{ route('login.index') }}';
                            return;
                        }

                        const dados = await resposta.json();

                        if (dados.success) {
                            document.getElementById('corpoHistoricoOrdenhas').insertAdjacentHTML('beforeend', dados.html);
                            btnVerMaisHistorico.remove();
                        } else {
                            btnVerMaisHistorico.disabled = false;
                            btnVerMaisHistorico.textContent = textoOriginal;
                        }
                    } catch (erro) {
                        btnVerMaisHistorico.disabled = false;
                        btnVerMaisHistorico.textContent = textoOriginal;
                    }
                });
            }

            /* --- GRÁFICOS (CHART.JS) --- */
            /* Dados 100% vindos do back-end (OrdenhaLeiteController::listar).
               Sem fazenda selecionada, o controller já envia tudo zerado. */
            const dadosProducaoDiaria = {
                labels: @json($labelsProducaoDiaria),
                valores: @json($dadosProducaoDiaria)
            };
            const dadosEvolucaoProducao = {
                labels: @json($labelsEvolucaoProducao),
                valores: @json($dadosEvolucaoProducao)
            };
            const dadosProducaoPorLote = {
                labels: @json($labelsProducaoPorLote),
                valores: @json($dadosProducaoPorLote)
            };

            const temProducaoDiaria = dadosProducaoDiaria.valores.some(v => Number(v) > 0);
            const temEvolucaoProducao = dadosEvolucaoProducao.valores.some(v => Number(v) > 0);
            const temProducaoPorLote = dadosProducaoPorLote.valores.some(v => Number(v) > 0);

            const producaoDiariaEmptyState = document.getElementById('producaoDiariaEmptyState');
            const evolucaoProducaoEmptyState = document.getElementById('evolucaoProducaoEmptyState');
            const producaoLoteEmptyState = document.getElementById('producaoLoteEmptyState');
            if (producaoDiariaEmptyState) producaoDiariaEmptyState.classList.toggle('visible', !temProducaoDiaria);
            if (evolucaoProducaoEmptyState) evolucaoProducaoEmptyState.classList.toggle('visible', !temEvolucaoProducao);
            if (producaoLoteEmptyState) producaoLoteEmptyState.classList.toggle('visible', !temProducaoPorLote);

            function cssVar(name, fallback) {
                const valor = getComputedStyle(document.body).getPropertyValue(name).trim();
                return valor || fallback;
            }

            function hexParaRgba(hex, alpha) {
                let h = hex.replace('#', '').trim();
                if (h.length === 3) h = h.split('').map(c => c + c).join('');
                const bigint = parseInt(h, 16);
                if (isNaN(bigint)) return `rgba(22, 163, 74, ${alpha})`;
                const r = (bigint >> 16) & 255, g = (bigint >> 8) & 255, b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }

            function paletaCategorica() {
                return [1, 2, 3, 4, 5, 6].map(i => cssVar(`--chart-${i}`, '#16a34a'));
            }

            let producaoDiariaChart = null;
            let evolucaoProducaoChart = null;
            let producaoLoteChart = null;

            function montarGraficos() {
                const textoSecundario = cssVar('--text-secundario', '#64748b');
                const textoPrincipal = cssVar('--text-principal', '#0f172a');
                const corGrid = cssVar('--chart-grid', '#eef2f6');
                const corSurface = cssVar('--card-bg', '#ffffff');

                if (producaoDiariaChart) producaoDiariaChart.destroy();
                if (evolucaoProducaoChart) evolucaoProducaoChart.destroy();
                if (producaoLoteChart) producaoLoteChart.destroy();

                const corProducao = cssVar('--chart-2', '#0891b2');

                const ctxProducaoDiaria = document.getElementById('producaoDiariaChart').getContext('2d');
                producaoDiariaChart = new Chart(ctxProducaoDiaria, {
                    type: 'bar',
                    data: {
                        labels: dadosProducaoDiaria.labels,
                        datasets: [{
                            label: 'Produção (L)',
                            data: dadosProducaoDiaria.valores,
                            backgroundColor: hexParaRgba(corProducao, 0.85),
                            hoverBackgroundColor: corProducao,
                            borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
                            borderSkipped: false,
                            maxBarThickness: 40,
                            categoryPercentage: 0.62,
                            barPercentage: 0.9
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 500 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: corSurface, titleColor: textoPrincipal, bodyColor: textoSecundario,
                                borderColor: corGrid, borderWidth: 1, padding: 12, cornerRadius: 10, displayColors: false,
                                callbacks: { label: (item) => `${item.formattedValue} L` }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: corGrid, drawTicks: false },
                                border: { display: false },
                                ticks: { color: textoSecundario, padding: 8, callback: (v) => v + ' L' }
                            },
                            x: { grid: { display: false }, border: { display: false }, ticks: { color: textoSecundario } }
                        }
                    }
                });

                const ctxEvolucaoProducao = document.getElementById('evolucaoProducaoChart').getContext('2d');
                evolucaoProducaoChart = new Chart(ctxEvolucaoProducao, {
                    type: 'line',
                    data: {
                        labels: dadosEvolucaoProducao.labels,
                        datasets: [{
                            label: 'Produção (L)',
                            data: dadosEvolucaoProducao.valores,
                            borderColor: corProducao,
                            backgroundColor: hexParaRgba(corProducao, 0.15),
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: corProducao
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 500 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: corSurface, titleColor: textoPrincipal, bodyColor: textoSecundario,
                                borderColor: corGrid, borderWidth: 1, padding: 12, cornerRadius: 10, displayColors: false,
                                callbacks: { label: (item) => `${item.formattedValue} L` }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: corGrid, drawTicks: false },
                                border: { display: false },
                                ticks: { color: textoSecundario, padding: 8, callback: (v) => v + ' L' }
                            },
                            x: { grid: { display: false }, border: { display: false }, ticks: { color: textoSecundario } }
                        }
                    }
                });

                const ctxProducaoLote = document.getElementById('producaoLoteChart').getContext('2d');
                producaoLoteChart = new Chart(ctxProducaoLote, {
                    type: 'bar',
                    data: {
                        labels: dadosProducaoPorLote.labels,
                        datasets: [{
                            label: 'Produção (L)',
                            data: dadosProducaoPorLote.valores,
                            backgroundColor: paletaCategorica(),
                            borderRadius: 8,
                            maxBarThickness: 32
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 500 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: corSurface, titleColor: textoPrincipal, bodyColor: textoSecundario,
                                borderColor: corGrid, borderWidth: 1, padding: 12, cornerRadius: 10, displayColors: false,
                                callbacks: { label: (item) => `${item.formattedValue} L` }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: corGrid, drawTicks: false },
                                border: { display: false },
                                ticks: { color: textoSecundario }
                            },
                            y: { grid: { display: false }, border: { display: false }, ticks: { color: textoSecundario } }
                        }
                    }
                });
            }

            montarGraficos();

            // Reforço de segurança: força os gráficos a se remedirem depois
            // que a página termina de carregar (fontes, imagens), já com o
            // zoom totalmente aplicado — sem isso, um gráfico criado um
            // instante antes do layout "assentar" pode ficar com o tamanho
            // levemente errado.
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (producaoDiariaChart) producaoDiariaChart.resize();
                    if (evolucaoProducaoChart) evolucaoProducaoChart.resize();
                    if (producaoLoteChart) producaoLoteChart.resize();
                }, 150);
            });

            // Os gráficos são recriados com as cores certas sempre que o
            // tema (claro/escuro/alto contraste) muda, sem precisar recarregar.
            ['chkDarkMode', 'chkHighContrast'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', () => setTimeout(montarGraficos, 60));
            });
        });
    </script>
@endpush
