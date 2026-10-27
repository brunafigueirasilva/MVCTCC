@extends('layouts.app')

@section('title', 'AgriFarm | Dashboard Avançado')
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

        .sidebar, .navbar, .main, .card, .box, .premium-card {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease, filter 0.25s ease;
        }

        /* ==========================================================================
        4. ÁREA PRINCIPAL E MÓDULOS DE CONTEÚDO
        (navbar/drawer em si vivem só em layouts/app.blade.php)
        ========================================================================== */
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
            grid-auto-rows: 1fr;
            gap: 20px;
        }

        .premium-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
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

        /* Card-resumo "Ordenha e Leite": além de premium-card, é um link
           inteiro pra tela própria — sem herdar o sublinhado/cor azul
           padrão de link. */
        a.premium-card-link {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
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
        }

        .premium-card-label {
            font-size: 0.844rem;
            font-weight: 600;
            letter-spacing: -0.1px;
        }

        /* Tamanho acompanha a largura do card e, no limite, quebra linha —
           nunca corta o valor com "…" (ex.: "R$ 1.04..."). */
        .premium-card-value {
            font-size: clamp(1.25rem, 1.75vw, 1.688rem);
            font-weight: 700;
            color: var(--text-principal);
            line-height: 1.15;
            white-space: nowrap;
            overflow-wrap: anywhere;
        }

        @media (max-width: 1400px) {
            .premium-card-value { white-space: normal; }
        }

        .premium-card-subtext {
            font-size: 0.75rem;
            color: var(--text-secundario);
            font-weight: 500;
            margin-top: 2px;
            line-height: 1.4;
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

        /* O sino de notificações (layout global) leva direto pra cá via
           #alertasSistema; sem isso o navegador rola até o topo exato do
           card, deixando-o embaixo dos botões flutuantes fixos (menu e
           acessibilidade) que ficam por cima de tudo no canto superior. */
        #alertasSistema {
            scroll-margin-top: 90px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: var(--surface-muted-bg);
            font-size: 1.375rem;
            color: #94a3b8;
        }

        .no-data-message i.icone-sucesso { background: var(--verde-claro, #e8f5ee); color: var(--verde-texto, #0e4a27); }
        body.dark-theme .no-data-message i.icone-sucesso { background: color-mix(in srgb, var(--verde-secundario, #16a765) 14%, transparent); color: var(--verde-texto, #0e4a27); }

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
            border: 1px solid var(--surface-muted-border);
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }

        .alert-item:hover {
            box-shadow: var(--card-shadow);
            transform: translateY(-1px);
        }

        /* Os tons "críticos"/"baixos" são pastéis pensados para o tema
           claro; sem uma versão própria, apareciam claros e destoantes
           sobre o fundo escuro. Nos dois temas o texto já usa
           var(--text-principal)/var(--text-secundario), então só a cor de
           fundo/realce precisa de uma variante por tema. */
        .alert-item.critico { background: #fef2f2; border-color: #fecaca; }
        .alert-item.baixo { background: #fff7ed; border-color: #fed7aa; }

        body.dark-theme .alert-item.critico { background: rgba(239, 68, 68, 0.10); border-color: rgba(239, 68, 68, 0.25); }
        body.dark-theme .alert-item.baixo { background: rgba(249, 115, 22, 0.10); border-color: rgba(249, 115, 22, 0.25); }

        .alert-item-main {
            display: flex;
            gap: 12px;
            min-width: 0;
        }

        /* Ícone em círculo por severidade — no lugar do emoji solto que
           vinha grudado no título, mesmo tratamento já usado no modal de
           alertas do layout global (sino de notificações). */
        .alert-item-icone {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            color: #ffffff;
        }

        .alert-item.critico .alert-item-icone {
            background: linear-gradient(145deg, #f87171, #dc2626);
            box-shadow: 0 4px 10px -3px rgba(220, 38, 38, 0.5);
        }

        .alert-item.baixo .alert-item-icone {
            background: linear-gradient(145deg, #fb923c, #ea580c);
            box-shadow: 0 4px 10px -3px rgba(234, 88, 12, 0.5);
        }

        .alert-item-text { min-width: 0; }

        .alert-item-text h3,
        .alert-item-text h4 {
            font-size: 0.844rem;
            font-weight: 700;
            color: var(--text-principal);
            margin-bottom: 4px;
            overflow-wrap: break-word;
        }

        .alert-item-text p {
            font-size: 0.781rem;
            color: var(--text-secundario);
            line-height: 1.45;
        }

        .badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
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
        body.high-contrast .box :is(h2, h3) i,
        body.high-contrast .alert-item-icone,
        body.high-contrast .no-data-message i {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .badge {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .alert-item {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
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

        body.high-contrast

        body.high-contrast a:focus-visible,
        body.high-contrast button:focus-visible,
        body.high-contrast select:focus-visible {
            outline: 3px solid #ffff00 !important;
            outline-offset: 2px !important;
        }

        /* ==========================================================================
        9. ADAPTAÇÕES RESPONSIVAS
        ========================================================================== */

        @media (max-width: 768px) {
            .header-title :is(h1, h2) { font-size: 1.5rem; }
            .box { padding: 20px; }
            .chart-container { height: 250px; }
        }

        @media (max-width: 576px) {
            .header-title p { font-size: 0.844rem; }
            .premium-card-value { font-size: 1.438rem; }
            .fazenda-selector-bar { flex-direction: column; align-items: stretch; padding: 16px; }
            .select-fazenda { max-width: 100%; }
            .selector-hint { width: 100%; }
            .cards-row { grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr)); gap: 14px; }
        }

        /* ==========================================================================
        10. MODO APP (MOBILE) — abaixo de 992px a página vira, visualmente, o
        equivalente ao app mobile: cabeçalho com o botão de acessibilidade
        fazendo o papel do "engrenagem" de configurações, cartões em grade 2
        colunas no estilo "selo em cima, valor grande embaixo" e uma barra de
        abas fixa no rodapé no lugar da sidebar (que continua existindo como
        o menu "Mais"). O "Sair" mora só na navbar (igual a todas as outras
        páginas do sistema) — não duplica mais no rodapé da sidebar. Acima de
        992px nada aqui se aplica — a experiência de admin com sidebar
        permanece intacta, como decidido.
        ========================================================================== */
        @media (max-width: 992px) {
            .header-title :is(h1, h2) {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            /* --- Seletor de fazenda: campo com aparência de "caixa"
               tocável, com seta própria no lugar da seta nativa do navegador. --- */
            .select-fazenda {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                width: 100%;
                max-width: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='9' viewBox='0 0 14 9'%3E%3Cpath fill='%2364748b' d='M7 9 0 1.6 1.6 0 7 5.8 12.4 0 14 1.6z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 14px center;
                padding-right: 36px;
            }

            /* --- Cartões: grade fixa de 2 colunas; cada cartão passa de
               "ícone à esquerda / texto empilhado à direita" para "ícone +
               rótulo numa linha, valor grande e legenda ocupando a linha
               inteira abaixo" — igual à referência. O card "Ordenha e
               Leite" é a exceção: continua no layout horizontal original,
               ocupando a linha toda. --- */
            .cards-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            /* A referência mobile não traz este cartão (fica só "Custo da
               Ração Hoje" em R$) — some apenas aqui; no desktop continua
               aparecendo normalmente. */
            .card-racao-consumida {
                display: none;
            }

            .premium-card {
                padding: 16px;
                flex-wrap: wrap;
                row-gap: 6px;
                column-gap: 10px;
            }

            .premium-card-icon-zone {
                width: 38px;
                height: 38px;
                font-size: 1rem;
            }

            .premium-card:not(.premium-card-link) .premium-card-icon-zone {
                order: 1;
            }

            .premium-card:not(.premium-card-link) .premium-card-data-zone {
                display: contents;
            }

            .premium-card:not(.premium-card-link) .premium-card-label {
                order: 2;
                flex: 1;
                min-width: 0;
                font-size: 0.75rem;
            }

            .premium-card:not(.premium-card-link) .premium-card-value {
                order: 3;
                flex-basis: 100%;
                font-size: 1.5rem;
                white-space: normal;
            }

            .premium-card:not(.premium-card-link) .premium-card-subtext {
                order: 4;
                flex-basis: 100%;
                font-size: 0.7rem;
            }

            .premium-card-link {
                grid-column: 1 / -1;
            }

            .premium-card-link .premium-card-subtext {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 8px;
            }

            .premium-card-link .ver-detalhes {
                white-space: nowrap;
                flex-shrink: 0;
            }

            .premium-card-link .middot {
                display: none;
            }

            /* --- Cartões de gráfico: viram cabeçalhos "expansíveis" (com
               setinha à direita) e uma prévia mais compacta do gráfico. --- */
            .box :is(h2, h3) {
                justify-content: space-between;
            }

            .box :is(h2, h3) i.box-chevron {
                margin-left: auto;
                width: auto;
                height: auto;
                background: transparent;
                color: var(--text-secundario);
                font-size: 0.8rem;
            }

            .chart-container {
                height: 200px;
            }

            /* --- Sidebar vira o menu "Mais": some por padrão e é acionada
               pela barra de abas / botão de menu como gaveta, exatamente
               como já acontecia. --- */

        }

        @media (min-width: 993px) {
            .box :is(h2, h3) i.box-chevron { display: none; }
        }

    </style>
@endpush

@section('content')
    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <main class="main" id="mainArea">
        <div class="header">
            <div class="header-title">
                <h1><i class="fa-solid fa-chart-line titulo-icone" aria-hidden="true"></i> Dashboard Avançado</h1>
                <p>Painel de controle centralizado para a gestão estratégica de sua fazenda.</p>
            </div>
        </div>

        @php
            // Toda a matemática (percentuais, médias, dias restantes) já chega
            // pronta do back-end (CadastroController::dashboard). Aqui só
            // decidimos ícone/cor/texto de apresentação a partir dos números
            // prontos — nenhum cálculo novo acontece na view.
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
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Lotes Ativos</span>
                        <span class="premium-card-value">{{ $lotesCount }}</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif ($lotesNovosSemana > 0)
                                <i class="fa-solid fa-arrow-up trend-up"></i>
                                <span class="trend-up">+{{ $lotesNovosSemana }}</span> nos últimos 7 dias
                            @else
                                Nenhum lote novo nos últimos 7 dias
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-verde">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Total de Animais</span>
                        <span class="premium-card-value">{{ number_format($totalAnimais ?? 0, 0, ',', '.') }}</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @else
                                Somando os lotes ativos desta fazenda
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-azul">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-cube"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Cochos Ativos</span>
                        <span class="premium-card-value">{{ $cochosCount }}</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif ($cochosCount > 0)
                                Nível médio de ração: <strong>{{ $nivelMedioCochos }}%</strong>
                                · <strong>{{ number_format($racaoNosCochos ?? 0, 0, ',', '.') }} kg</strong> nos cochos
                            @else
                                Nenhum cocho cadastrado
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-azul card-racao-consumida">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-bucket"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Ração Consumida Hoje</span>
                        <span class="premium-card-value">{{ number_format($consumoHoje ?? 0, 0, ',', '.') }} kg</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif (!is_null($variacaoConsumo))
                                <i class="fa-solid {{ $trendIcone($variacaoConsumo) }} {{ $trendClasse($variacaoConsumo) }}"></i>
                                <span class="{{ $trendClasse($variacaoConsumo) }}">{{ number_format(abs($variacaoConsumo), 1, ',', '.') }}%</span> vs. ontem
                            @else
                                Sem registro de ontem para comparar
                            @endif
                            @unless ($semFazenda)
                                <br>Previsto pelo plano · <strong>{{ number_format($consumoRegistradoHoje ?? 0, 0, ',', '.') }} kg</strong> registrados nos cochos
                            @endunless
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-laranja">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Custo da Ração Hoje</span>
                        <span class="premium-card-value">R$ {{ number_format($custoHoje ?? 0, 2, ',', '.') }}</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @else
                                Preço médio do kg: R$ {{ number_format($precoMedioKg ?? 0, 2, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-roxo">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Eficiência Alimentar</span>
                        <span class="premium-card-value">{{ number_format($eficienciaAlimentar ?? 0, 1, ',', '.') }}%</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif (($lotesComRegistroHoje ?? 0) > 0)
                                <strong>{{ $lotesComRacaoHoje }}</strong> de {{ $lotesComRegistroHoje }} lote(s) com ração vinculada
                            @else
                                Sem consumo registrado hoje
                            @endif
                        </span>
                    </div>
                </div>

                <div class="premium-card premium-verde">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Estoque no Armazém</span>
                        <span class="premium-card-value">{{ number_format($estoqueAtual ?? 0, 0, ',', '.') }} kg</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @elseif (!is_null($diasRestantesEstoque))
                                Autonomia estimada: <strong>{{ $diasRestantesEstoque }}</strong> dia(s)
                            @else
                                Sem consumo para estimar autonomia
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Ordenha e Leite (produção por LOTE) — resumo apenas; os
                     cartões e gráficos completos vivem na tela própria
                     "Ordenha e Leite". -->
                <a href="{{ route('ordenha.listar') }}" class="premium-card premium-azul premium-card-link">
                    <div class="premium-card-icon-zone">
                        <i class="fa-solid fa-jug-detergent"></i>
                    </div>
                    <div class="premium-card-data-zone">
                        <span class="premium-card-label">Ordenha e Leite</span>
                        <span class="premium-card-value">{{ number_format($producaoLeiteHoje ?? 0, 1, ',', '.') }} L</span>
                        <span class="premium-card-subtext">
                            @if ($semFazenda)
                                Selecione uma fazenda
                            @else
                                <span>{{ $lotesComOrdenha ?? 0 }} lote(s) em produção</span>
                                <span class="ver-detalhes"><span class="middot">· </span><strong>Ver detalhes completos <i class="fa-solid fa-arrow-right"></i></strong></span>
                            @endif
                        </span>
                    </div>
                </a>
            </div>
        </div>

        <!-- MÓDULO DOS GRÁFICOS E SISTEMA DE ALERTAS -->
        <div class="content-graphics-grid">
            <div class="box">
                <h2 id="tituloGraficoBarras"><i class="fa-solid fa-chart-bar" style="color: var(--verde-texto, #0e4a27);"></i> Histórico de Consumo (7 dias) <i class="fa-solid fa-chevron-right box-chevron"></i></h2>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                    <div class="chart-empty-overlay" id="barEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-chart-simple"></i>
                            {{ $semFazenda ? 'Selecione uma fazenda para ver o histórico.' : 'Nenhum consumo registrado nos últimos 7 dias.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">
                <h2 id="tituloGraficoTipos"><i class="fa-solid fa-chart-pie" style="color: #2563eb;"></i> Animais por Tipo <i class="fa-solid fa-chevron-right box-chevron"></i></h2>
                <div class="chart-container" id="containerDonut">
                    <canvas id="donutChart"></canvas>
                    <div class="chart-empty-overlay" id="donutEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-paw"></i>
                            {{ $semFazenda ? 'Selecione uma fazenda para ver o rebanho.' : 'Nenhum animal em lotes ativos nesta fazenda.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box" id="alertasSistema">
                <h2><i class="fa-solid fa-bell" style="color: #dc2626;"></i> Alertas do Sistema</h2>
                <div class="alerts-wrapper">
                    @php $temAlertas = isset($alertas) && count($alertas) > 0; @endphp

                    @if ($semFazenda)
                        <div class="no-data-message">
                            <div>
                                <i class="fa-solid fa-tractor" aria-hidden="true"></i>
                                <p>Selecione uma fazenda para ver os alertas.</p>
                            </div>
                        </div>
                    @elseif ($temAlertas)
                        @foreach ($alertas as $alerta)
                            @php $critico = $alerta->prioridade == 'critico' || $alerta->prioridade == 'alta'; @endphp
                            <div class="alert-item {{ $critico ? 'critico' : 'baixo' }}">
                                <div class="alert-item-main">
                                    <div class="alert-item-icone">
                                        <i class="fa-solid {{ $critico ? 'fa-triangle-exclamation' : 'fa-circle-exclamation' }}" aria-hidden="true"></i>
                                    </div>
                                    <div class="alert-item-text">
                                        <h3>{{ $alerta->titulo }}</h3>
                                        <p>{{ $alerta->descricao }}</p>
                                    </div>
                                </div>
                                <span class="badge {{ $critico ? 'badge-critico' : 'badge-baixo' }}">
                                    {{ ucfirst($alerta->prioridade) }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="no-data-message">
                            <div>
                                <i class="fa-solid fa-circle-check icone-sucesso" aria-hidden="true"></i>
                                <p>Nenhum alerta pendente.<br>Operação rodando normalmente.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </main>

    {{-- A barra de abas própria do Dashboard foi removida: ficava POR CIMA
         da navegação inferior global (layouts/app), com itens diferentes
         das outras telas, e o "Mais" dela não abria o menu (usava uma
         classe que a sidebar do layout não conhece). A navegação global
         tem os mesmos destinos (Relatórios fica em "Mais"). --}}
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
            /* --- GRÁFICOS (CHART.JS) --- */
            /* Dados 100% vindos do back-end (CadastroController::dashboard).
               Sem fazenda selecionada, o controller já envia tudo zerado. */
            const dadosGraficoBarras = {
                labels: @json($labelsConsumo),
                valores: @json($dadosConsumo)
            };

            const dadosGraficoDonut = {
                labels: @json($labelsAnimais),
                valores: @json($dadosAnimais)
            };

            const temConsumo = dadosGraficoBarras.valores.some(v => Number(v) > 0);
            const temAnimais = dadosGraficoDonut.valores.some(v => Number(v) > 0);

            const barEmptyState = document.getElementById('barEmptyState');
            const donutEmptyState = document.getElementById('donutEmptyState');
            if (barEmptyState) barEmptyState.classList.toggle('visible', !temConsumo);
            if (donutEmptyState) donutEmptyState.classList.toggle('visible', !temAnimais);

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

            let barChart = null;
            let donutChart = null;

            function montarGraficos() {
                const textoSecundario = cssVar('--text-secundario', '#64748b');
                const textoPrincipal = cssVar('--text-principal', '#0f172a');
                const corGrid = cssVar('--chart-grid', '#eef2f6');
                const corSurface = cssVar('--card-bg', '#ffffff');
                const corBase = cssVar('--chart-1', '#16a34a');

                if (barChart) barChart.destroy();
                if (donutChart) donutChart.destroy();

                const ctxBar = document.getElementById('barChart').getContext('2d');
                barChart = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: dadosGraficoBarras.labels,
                        datasets: [{
                            label: 'Consumo (kg)',
                            data: dadosGraficoBarras.valores,
                            backgroundColor: function(context) {
                                const { chart } = context;
                                const { ctx, chartArea } = chart;
                                if (!chartArea) return corBase;
                                const gradiente = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                gradiente.addColorStop(0, corBase);
                                gradiente.addColorStop(1, hexParaRgba(corBase, 0.35));
                                return gradiente;
                            },
                            hoverBackgroundColor: corBase,
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
                                backgroundColor: corSurface,
                                titleColor: textoPrincipal,
                                bodyColor: textoSecundario,
                                borderColor: corGrid,
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: false,
                                titleFont: { weight: '700' },
                                callbacks: {
                                    label: (item) => `${item.formattedValue} kg`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: corGrid, drawTicks: false },
                                border: { display: false },
                                ticks: { color: textoSecundario, padding: 8, callback: (v) => v + ' kg' }
                            },
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: textoSecundario }
                            }
                        }
                    }
                });

                const ctxDonut = document.getElementById('donutChart').getContext('2d');
                donutChart = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: dadosGraficoDonut.labels,
                        datasets: [{
                            data: dadosGraficoDonut.valores,
                            backgroundColor: paletaCategorica(),
                            borderColor: corSurface,
                            borderWidth: 3,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        animation: { duration: 500 },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textoSecundario,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 16,
                                    font: { size: 12, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: corSurface,
                                titleColor: textoPrincipal,
                                bodyColor: textoSecundario,
                                borderColor: corGrid,
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 10
                            }
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
                    if (barChart) barChart.resize();
                    if (donutChart) donutChart.resize();
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
