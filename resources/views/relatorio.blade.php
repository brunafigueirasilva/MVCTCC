@extends('layouts.app')

@section('title', 'AgriFarm | Relatórios Avançados')

@push('styles')
    <style>
        /* ==========================================
        1. RESET & CONFIGURAÇÕES GERAIS
        ========================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }


        :root {
            --bg-principal: #f5f7fa;
            --text-principal: #2b303a;
            --text-secundario: #6b7280;
            --card-bg: #ffffff;
            --card-border: 1px solid #edf2f7;
            --card-shadow: 0 10px 30px rgba(15, 23, 42, .03);

            --surface-muted-bg: #f9fafb;
            --surface-muted-border: #e5e7eb;


            /* Paleta dos gráficos (Chart.js). Mesma ordem/lógica do Dashboard,
               lida em JS e reaplicada quando o tema muda. */
            --chart-1: #10b981;
            --chart-2: #3b82f6;
            --chart-3: #f59e0b;
            --chart-4: #8b5cf6;
            --chart-5: #f472b6;
            --chart-6: #60a5fa;
            --chart-grid: #eef2f6;
            --chart-tick: #94a3b8;

        }

        body {
            background: var(--bg-principal);
            color: var(--text-principal);
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        /* Some visualmente mas continua legível por leitor de tela — usado
           em legendas/rótulos que só existem pra dar contexto a quem não
           enxerga a tela (ex.: legenda de tabela). */
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

        /* Transições suaves globais para a interatividade do menu */
        .sidebar,
        .navbar,
        .main {
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

        /* ==========================================
        2. NAVBAR SUPERIOR (VERDE FIXA)
        ========================================== */





        /* Logo diretamente sobre o fundo escuro da navbar, sem caixa/quadrado
           atrás — igual ao Dashboard. */


        .btn-pdf {
            background: var(--verde-principal, #0e4a27);
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 20%, transparent);
            transition: all 0.3s ease;
        }

        .btn-pdf i {
            transition: transform 0.3s ease;
        }

        .btn-pdf:hover {
            background: var(--verde-hover, #0b3d21);
            color: #ffffff;
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px color-mix(in srgb, var(--verde-principal, #0e4a27) 35%, transparent);
        }

        .btn-pdf:hover i {
            transform: translateY(2px);
        }

        .btn-pdf:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px color-mix(in srgb, var(--verde-principal, #0e4a27) 25%, transparent);
        }


        .btn-abrir-filtros {
            background: var(--card-bg);
            color: var(--text-principal);
            border: 1px solid var(--surface-muted-border);
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

        .btn-abrir-filtros:hover {
            border-color: var(--verde-hover, #0b3d21);
            color: var(--verde-texto, #0e4a27);
        }

        /* Badge com o número de filtros ativos, para o usuário ver de
           relance que o relatório está filtrado sem precisar abrir o modal. */
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

        .filtros-badge.visivel {
            display: flex;
        }

        /* Barra "Unidade Produtiva" — mesmo componente, aparência e
           comportamento do Dashboard (ver dashboard.blade.php
           .fazenda-selector-bar). Fica antes de qualquer chip/card/gráfico:
           tudo abaixo dela passa a refletir só a fazenda escolhida aqui. */
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
            margin-bottom: 20px;
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

        @media (max-width: 576px) {
            .fazenda-selector-bar { flex-direction: column; align-items: stretch; padding: 16px; }
            .select-fazenda { max-width: 100%; }
            .selector-hint { width: 100%; }
        }

        /* Linha de "chips" no topo do conteúdo, resumindo os filtros
           aplicados com opção de remover cada um individualmente. */
        .filtros-ativos {
            display: none;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .filtros-ativos.visivel {
            display: flex;
        }

        .filtros-ativos .chip {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--card-bg);
            border: 1px solid var(--surface-muted-border);
            color: var(--text-principal);
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 8px 6px 12px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .filtros-ativos .chip button {
            background: var(--surface-muted-bg);
            border: none;
            color: var(--text-secundario);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filtros-ativos .chip button:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .filtros-ativos .btn-limpar-tudo {
            background: none;
            border: none;
            color: #dc2626;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
        }











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

        /* ==========================================
        3. SIDEBAR LATERAL
        ========================================== */





        @keyframes menuIconBounce {
            0% { transform: translateY(0) scale(1); }
            30% { transform: translateY(-9px) scale(1.16); }
            50% { transform: translateY(1px) scale(0.95); }
            70% { transform: translateY(-4px) scale(1.06); }
            100% { transform: translateY(0) scale(1); }
        }




        @media (prefers-reduced-motion: reduce) {
            .menu a, .menu i { transition: background-color 0.18s ease, color 0.18s ease; }
            .menu a:hover { transform: none; }
            .menu a:hover i { animation: none; }
        }


        /* ==========================================================================
        CONTROLES DE ACESSIBILIDADE (TEMAS)
        Mesma paleta verde do Dashboard, para a página de Relatórios reagir
        do mesmo jeito ao modo escuro e ao alto contraste.
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


            --chart-1: #10b981;
            --chart-2: #3b82f6;
            --chart-3: #f59e0b;
            --chart-4: #a855f7;
            --chart-5: #f472b6;
            --chart-6: #60a5fa;
            --chart-grid: rgba(74, 222, 128, 0.10);
            --chart-tick: #9db8a8;

        }
        body.dark-theme .navbar-right .accessibility-trigger:hover { background: rgba(255, 255, 255, 0.16); color: #ffffff; }

        body.dark-theme .navbar-right .accessibility-trigger { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.16); color: #f1f5f9; }
        body.dark-theme .input-box select,
        body.dark-theme .input-box input[type="date"] { color-scheme: dark; }
        body.dark-theme .card-grafico-header :is(h2, h3) i { filter: brightness(1.1); }

        body.high-contrast {
            background: #000000 !important;
            color: #ffff00 !important;

            /* Sem isso, qualquer texto que use --text-principal/
               --text-secundario (valores dos cards do topo, "Resumo por
               Categoria" etc.) ficava com a cor cinza-escura do tema claro
               sobre o fundo preto forçado — quase ilegível, o oposto do que
               o alto contraste deveria garantir. */
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

        body.high-contrast .card-grafico,
        body.high-contrast .modal-card,
        body.high-contrast .navbar,
        body.high-contrast .sidebar,
        body.high-contrast .card {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }

        body.high-contrast .navbar-logo img { filter: brightness(0) invert(1); }

        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3, body.high-contrast h4,
        body.high-contrast p, body.high-contrast span, body.high-contrast label,
        body.high-contrast i, body.high-contrast select, body.high-contrast td, body.high-contrast th {
            color: #ffff00 !important;
        }

        body.high-contrast thead tr { background: #000000 !important; }

        body.high-contrast .menu a.active,
        body.high-contrast .menu a:hover {
            background: #1a1a00 !important;
        }

        body.high-contrast .toggle-menu-btn,
        body.high-contrast .user-avatar,
        body.high-contrast .btn-abrir-filtros,
        body.high-contrast .btn-limpar-filtros,
        body.high-contrast .btn-preset,
        body.high-contrast .input-box input,
        body.high-contrast .input-box select,
        body.high-contrast .filtros-ativos .chip {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* --card-bg/--surface-muted-bg não são redefinidas acima — sem
           esta regra a barra de Unidade Produtiva ficava com fundo branco
           (texto amarelo em cima, quase ilegível) no meio de uma tela toda
           preta. */
        body.high-contrast .fazenda-selector-bar,
        body.high-contrast .select-fazenda,
        body.high-contrast .selector-hint {
            background: #000000 !important;
            border-color: #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .close {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .modal-top {
            background: #000000 !important;
            border-bottom: 2px solid #ffff00 !important;
        }

        body.high-contrast .modal-top img { background: #000000 !important; }

        body.high-contrast .submit-btn {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }

        body.high-contrast .btn-pdf {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .chart-empty-overlay {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .card-link {
            color: #ffff00 !important;
        }

        body.high-contrast .ranking-medalha.ouro i,
        body.high-contrast .ranking-medalha.prata i,
        body.high-contrast .ranking-medalha.bronze i {
            color: #ffff00 !important;
        }

        body.high-contrast .navbar-logout {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
        }

        body.high-contrast a:focus-visible,
        body.high-contrast button:focus-visible,
        body.high-contrast select:focus-visible,
        body.high-contrast input:focus-visible {
            outline: 3px solid #ffff00 !important;
            outline-offset: 2px !important;
        }

        /* ==========================================
        4. MODAL DE FILTROS AVANÇADOS (mesmo padrão do Plano Alimentar/
        Ração/Estoque/Cocho/Lote)
        ========================================== */
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
            background: var(--card-bg);
            color: var(--text-principal);
            width: 450px;
            max-width: 95%;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal.active .modal-card {
            transform: translateY(0);
        }

        /* Cabeçalho simples (logo + título), igual ao resto do sistema —
           nada de faixa colorida, pra este modal não destoar dos demais. */
        .modal-top {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-top img { width: 110px; }
        .modal-top h2 { font-size: 1.25rem; font-weight: 700; color: var(--text-principal); }
        .modal-top p { font-size: 0.875rem; font-weight: 400; color: var(--text-secundario); margin-top: 4px; }

        .close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--surface-muted-bg);
            color: var(--text-secundario);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .close:hover {
            background: var(--surface-muted-border);
            color: var(--text-principal);
        }

        .campo { margin-bottom: 16px; }

        .campo label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-secundario);
        }

        .input-box { position: relative; }

        .input-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secundario);
            pointer-events: none;
        }

        .input-box input,
        .input-box select {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            background: var(--surface-muted-bg);
            color: var(--text-principal);
            outline: none;
            font-size: 0.875rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            appearance: none;
        }

        .input-box input:focus,
        .input-box select:focus {
            border-color: var(--verde-ativo, #087a47);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--verde-principal, #0e4a27) 15%, transparent);
        }

        .date-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* Presets de período (7 dias / 30 dias / mês atual), atalho para
           não precisar digitar as duas datas manualmente toda vez. */
        .presets-periodo { margin-top: 8px; }   /* estilo: partials/paleta-verde */

        .submit-btn {
            width: 100%;
            background: var(--verde-principal, #0e4a27);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 15%, transparent);
        }

        .submit-btn:hover { background: var(--verde-hover, #0b3d21); transform: translateY(-1px); box-shadow: 0 10px 25px color-mix(in srgb, var(--verde-principal, #0e4a27) 35%, transparent); }

        .btn-limpar-filtros {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            background: var(--card-bg);
            color: var(--verde-texto, #0e4a27);
            border: 1px solid var(--surface-muted-border);
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-limpar-filtros:hover { background: var(--surface-muted-bg); }

        /* ==========================================
        5. CONTEÚDO PRINCIPAL (MAIN)
        ========================================== */
        /* Sem "margin-top: 70px" (resquício de um layout antigo com navbar
           fixa no topo) — a navbar de verdade já não empurra o conteúdo
           assim, e esse valor jogava o título bem mais baixo que os botões
           flutuantes de notificações/acessibilidade (topo em y:20, mesmo
           padrão do Dashboard e das demais telas). "padding-top: 20px" é o
           que alinha o título "Relatório Avançado" na mesma linha deles. */
        .main {
            margin-left: 260px;
            padding: 40px;
            padding-top: 20px;
            min-height: 100vh;
        }


        /* Título da página — mesmo componente/medidas do Dashboard e das
           telas de Lote/Cocho/Fazenda/Ordenha (.header > .header-title).
           Esta tela era a única sem nenhum título/subtítulo próprio: ia
           direto da navbar pro seletor de Unidade Produtiva. */
        .header {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-title :is(h1, h2) {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-principal);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title :is(h1, h2) i {
            color: var(--verde-texto, #0e4a27);
            font-size: 1.375rem;
        }

        .header-title p {
            color: var(--text-secundario);
            font-size: 0.9375rem;
        }

        @media (max-width: 768px) {
            .header-title :is(h1, h2) { font-size: 1.5rem; }
        }

        @media (max-width: 576px) {
            .header-title p { font-size: 0.844rem; }
        }

        .grid-relatorios {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(48%, 1fr));
            gap: 25px;
        }

        .card-grafico {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 28px;
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
        }

        .card-grafico-header :is(h2, h3) {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-principal);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-grafico-header p {
            color: var(--text-secundario);
            font-size: 13px;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        /* Cabeçalho do Ranking de Lotes: título à esquerda, botão de
           ocultar/mostrar à direita. */
        .card-grafico-header-flex {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-toggle-ranking {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            color: var(--text-secundario);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .btn-toggle-ranking:hover {
            background: var(--surface-muted-border);
            color: var(--text-principal);
        }

        body.high-contrast .btn-toggle-ranking {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Estado "sem dados": cobre o gráfico com uma mensagem em vez de
           mostrar um canvas vazio ou (como antes) números fictícios só
           para preencher o espaço — os gráficos agora refletem só o que
           realmente está no banco. */
        .chart-empty-overlay {
            display: none;
            position: absolute;
            inset: 0;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: var(--card-bg);
            border-radius: 12px;
        }

        .chart-empty-overlay.visible {
            display: flex;
        }

        .no-data-message {
            color: var(--text-secundario);
            font-size: 13px;
            line-height: 1.5;
            max-width: 220px;
        }

        .no-data-message i {
            display: block;
            font-size: 26px;
            margin-bottom: 10px;
            color: #cbd5e1;
        }

        .donut-layout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 300px;
            position: relative;
        }

        .donut-chart-box {
            width: 50%;
            height: 100%;
            position: relative;
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
            font-size: 1.125rem;
            font-weight: 800;
            color: var(--text-principal);
        }

        .donut-center-total .rotulo {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
        }

        .card-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            color: var(--verde-texto, #0e4a27);
            font-weight: 700;
            font-size: 0.8125rem;
            text-decoration: none;
            min-height: 32px; /* área de toque (tinha ~16px de altura) */
        }

        .card-link:hover { text-decoration: underline; }

        .donut-legend-box {
            width: 45%;
        }

        .donut-legend-box ul {
            list-style: none;
        }

        .donut-legend-box li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--surface-muted-border);
            font-size: 14px;
        }

        .donut-legend-box li:last-child {
            border-bottom: none;
        }

        .legend-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secundario);
        }

        .legend-label i {
            font-size: 10px;
        }

        .legend-value {
            font-weight: 700;
            color: var(--text-principal);
        }

        /* ==========================================
        5b. CARDS DO TOPO (Consumo/Custo/Média Diária/kg Vivo/Eficiência) —
        mesmos 5 cards que existiam no Plano Alimentar, movidos pra cá.
        ========================================== */
        .trend-up { color: var(--verde-texto, #0e4a27); }
        .trend-down { color: #dc2626; }
        .trend-neutral { color: #94a3b8; }

        /* Alto contraste é só preto/amarelo em toda a página — sem esta
           regra, a variação dos cards do topo (div, não span) ficava verde/
           vermelha/cinza mesmo com o resto da tela já em amarelo, quebrando
           o contraste que o modo deveria garantir. */
        body.high-contrast .trend-up,
        body.high-contrast .trend-down,
        body.high-contrast .trend-neutral {
            color: #ffff00 !important;
        }

        /* Chips de atalho de período (Hoje/7/15/30/60/90 dias) — estilo
           padrão do sistema (partials/paleta-verde); aqui só o espaçamento. */
        .periodo-chips { margin-bottom: 18px; }

        /* Cards de destaque "Melhor Desempenho"/"Necessita Atenção",
           extraídos do topo/base do Ranking de Lotes já calculado. */
        .destaque-cards-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 25px;
        }

        .relatorio-fonte-dados {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin: 0 0 16px;
            padding: 10px 14px;
            border-radius: 10px;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            font-size: 0.8125rem;
            line-height: 1.45;
            color: var(--text-secundario);
        }
        .relatorio-fonte-dados i { margin-top: 3px; color: var(--verde-texto, #0e4a27); }
        .relatorio-fonte-dados strong { color: var(--text-principal); }
        .relatorio-fonte-dados a { color: var(--verde-texto, #0e4a27); font-weight: 600; }
        body.high-contrast .relatorio-fonte-dados { background: #000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
        body.high-contrast .relatorio-fonte-dados :is(i, strong, a) { color: #ffff00 !important; }

        .stat-cards-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
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

        /* Gradiente sutil por card — mesmo padrão do Plano Alimentar e do
           Dashboard: um tom vivo no canto superior, esmaecendo pro fundo
           neutro do card. */
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
            color: var(--text-secundario);
            font-weight: 600;
        }

        .stat-card-valor {
            font-size: 1.375rem;
            font-weight: 800;
            color: var(--text-principal);
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

        body.high-contrast .stat-card-icon {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
            box-shadow: none !important;
        }

        /* ==========================================
        5c. GRADE DE GRÁFICOS (2 colunas / 3 colunas)
        ========================================== */
        .relatorio-grid-2col {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .relatorio-grid-3col {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 25px;
        }

        .insight-banner {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: var(--verde-claro, #e8f5ee);
            border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28));
            color: var(--verde-texto, #0e4a27);
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 0.8125rem;
            line-height: 1.5;
            margin-top: 16px;
        }

        .insight-banner i { margin-top: 2px; flex-shrink: 0; }

        body.dark-theme .insight-banner { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 30%, transparent); color: var(--verde-texto, #0e4a27); }
        body.high-contrast .insight-banner { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }

        /* ==========================================
        5d. RESUMO POR CATEGORIA
        ========================================== */
        .resumo-categoria-list { display: flex; flex-direction: column; }

        .resumo-categoria-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--surface-muted-border);
        }

        .resumo-categoria-item:last-child { border-bottom: none; }

        .resumo-categoria-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 13px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        body.high-contrast .resumo-categoria-icon {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
            box-shadow: none !important;
        }

        .resumo-categoria-info { flex: 1; min-width: 0; }
        .resumo-categoria-nome { font-weight: 700; font-size: 0.875rem; color: var(--text-principal); }
        .resumo-categoria-sub { font-size: 0.75rem; color: var(--text-secundario); }

        .resumo-categoria-numeros { text-align: right; flex-shrink: 0; }
        .resumo-categoria-numeros .consumo { display: block; font-weight: 700; font-size: 0.8125rem; color: var(--text-principal); }
        .resumo-categoria-numeros .custo { font-size: 0.75rem; color: var(--text-secundario); }

        /* ==========================================
        5e. RANKING DE LOTES POR DESEMPENHO
        ========================================== */
        .ranking-card { grid-column: 1 / -1; }

        .ranking-table-wrap { overflow-x: auto; }

        .ranking-table { width: 100%; border-collapse: collapse; }

        .ranking-table th {
            text-align: left;
            padding: 10px 12px;
            font-size: 0.71875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
            border-bottom: 1px solid var(--surface-muted-border);
            white-space: nowrap;
        }

        .ranking-table td {
            padding: 13px 12px;
            font-size: 0.8125rem;
            color: var(--text-principal);
            border-bottom: 1px solid var(--surface-muted-border);
            white-space: nowrap;
        }

        .ranking-table tbody tr:last-child td { border-bottom: none; }
        .ranking-table tbody tr:hover { background: var(--surface-muted-bg); }

        .ranking-medalha { display: inline-flex; align-items: center; gap: 6px; font-weight: 700; }
        .ranking-medalha.ouro i { color: #eab308; }
        .ranking-medalha.prata i { color: #94a3b8; }
        .ranking-medalha.bronze i { color: #b45309; }

        .categoria-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.71875rem;
            font-weight: 700;
            background: var(--verde-claro, #e8f5ee);
            color: var(--verde-texto, #0e4a27);
        }

        body.dark-theme .categoria-badge { background: color-mix(in srgb, var(--verde-secundario, #16a765) 14%, transparent); color: var(--verde-texto, #0e4a27); }

        .btn-ver-mais {
            background: var(--card-bg);
            border: 1px solid var(--surface-muted-border);
            color: var(--text-principal);
            padding: 10px 22px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8125rem;
            cursor: pointer;
            display: block;
            margin: 18px auto 0;
        }

        .btn-ver-mais:hover { border-color: var(--verde-hover, #0b3d21); color: var(--verde-texto, #0e4a27); }
        .btn-ver-mais:disabled { opacity: 0.6; cursor: not-allowed; }

        body.high-contrast .ranking-table th,
        body.high-contrast .ranking-table td,
        body.high-contrast .stat-card,
        body.high-contrast .categoria-badge,
        body.high-contrast .btn-ver-mais {
            background: #000000 !important;
            border-color: #ffff00 !important;
            color: #ffff00 !important;
        }

        /* ==========================================
        6. RESPONSIVIDADE
        ========================================== */
        @media(max-width: 992px) {
            .navbar {
                padding: 0 20px;
            }
            .main {
                margin-left: 0;
                width: 100%;
                padding: 20px;
                padding-top: 20px;
            }
            .grid-relatorios {
                grid-template-columns: 1fr;
            }
            .donut-layout {
                flex-direction: column;
                height: auto;
                gap: 20px;
            }
            .donut-chart-box, .donut-legend-box {
                width: 100%;
            }
            .stat-cards-row {
                grid-template-columns: repeat(2, 1fr);
            }
            .relatorio-grid-2col,
            .relatorio-grid-3col {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .stat-cards-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div id="modalFiltros" class="modal">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="tituloModalFiltros">
            <button class="close" id="closeFiltrosBtn" aria-label="Fechar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            <div class="modal-top">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <h2 id="tituloModalFiltros">Filtros Avançados</h2>
                <p>Escolha o que exibir nos relatórios</p>
            </div>

            <form action="{{ route('relatorio.listar') }}" method="GET">
                <!-- A Fazenda não é mais escolhida aqui — é a Unidade
                     Produtiva selecionada na barra do topo. Este campo
                     oculto só garante que aplicar Lote/Ração/Período não
                     perca a unidade atual (um <form method="GET"> só manda
                     os campos que ele mesmo tem). -->
                <input type="hidden" name="fazenda_id" value="{{ $fazendaSelecionadaId }}">

                <div class="campo">
                    <label for="lote">Lote</label>
                    <div class="input-box">
                        <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                        <select name="lote" id="lote">
                            <option value="todos">Todos os Lotes</option>
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}" {{ request('lote') == $lote->id ? 'selected' : '' }}>
                                    {{ $lote->nome_lote }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="campo">
                    <label for="categoria_animal">Tipo de Animal</label>
                    <div class="input-box">
                        <i class="fa-solid fa-cow" aria-hidden="true"></i>
                        <select name="categoria_animal" id="categoria_animal">
                            <option value="" {{ !request('categoria_animal') ? 'selected' : '' }}>Todos os tipos de animal</option>
                            @forelse ($categoriasAnimais ?? [] as $categoria)
                                <option value="{{ $categoria }}" {{ request('categoria_animal') == $categoria ? 'selected' : '' }}>
                                    {{ $categoria }}
                                </option>
                            @empty
                                <option value="" disabled>Nenhum lote cadastrado ainda</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="campo">
                    <label for="tipo_racao">Tipo de Ração</label>
                    <div class="input-box">
                        <i class="fa-solid fa-wheat-awn" aria-hidden="true"></i>
                        <select name="tipo_racao" id="tipo_racao">
                            <option value="todos">Todas as Rações</option>
                            @foreach($racoes as $racao)
                                <option value="{{ $racao->id }}" {{ request('tipo_racao') == $racao->id ? 'selected' : '' }}>
                                    {{ $racao->nome_racao }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="campo">
                    <label>Período</label>
                    <div class="date-group">
                        <div class="input-box">
                            <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                            <input type="date" name="data_inicio" id="dataInicio" value="{{ request('data_inicio') }}">
                        </div>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                            <input type="date" name="data_fim" id="dataFim" value="{{ request('data_fim') }}">
                        </div>
                    </div>
                    <div class="presets-periodo">
                        @foreach ([7, 15, 30, 60, 90] as $diasPreset)
                            <button type="button" class="btn-preset" data-dias="{{ $diasPreset }}">{{ $diasPreset }} dias</button>
                        @endforeach
                        <button type="button" class="btn-preset" data-mes-atual="1">Este mês</button>
                    </div>
                </div>

                <button type="submit" class="submit-btn"><i class="fa-solid fa-check"></i> Aplicar Filtros</button>
                <a href="{{ route('relatorio.listar', ['fazenda_id' => $fazendaSelecionadaId]) }}" class="btn-limpar-filtros"><i class="fa-solid fa-eraser"></i> Limpar Filtros</a>
            </form>
        </div>
    </div>

    <div class="main" id="mainContent">
        <div class="header">
            <div class="header-title">
                <h1><i class="fa-regular fa-file-lines titulo-icone" aria-hidden="true"></i> Relatório Avançado</h1>
                <p>Indicadores, gráficos e ranking de desempenho da Unidade Produtiva selecionada.</p>
            </div>

            <div class="header-actions">
                <a href="{{ route('relatorio.pdf', request()->all()) }}" class="btn-pdf">
                    <i class="fas fa-download"></i> Exportar PDF
                </a>
                <button class="btn-abrir-filtros" id="openFiltrosBtn">
                    <i class="fa-solid fa-sliders"></i> Filtros Avançados
                    <span class="filtros-badge {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}">{{ count($filtrosAplicados ?? []) }}</span>
                </button>
            </div>
        </div>

        <!-- Barra "Unidade Produtiva" — mesma lógica/aparência/comportamento
             do Dashboard: escolher a fazenda aqui é o que decide quais
             indicadores, gráficos, tabelas e filtros o resto da tela
             mostra. -->
        <div class="fazenda-selector-bar">
            <div class="fazenda-selector-bar-label"><i class="fa-solid fa-tractor"></i> Unidade Produtiva:</div>
            <select class="select-fazenda" aria-label="Selecionar fazenda" onchange="window.location.href='{{ route('relatorio.listar') }}?fazenda_id=' + this.value" aria-label="Selecionar Unidade Produtiva">
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
                    Selecione uma Unidade Produtiva para carregar os indicadores, gráficos e tabelas do relatório.
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
            $hojeChip = \Carbon\Carbon::today();
            $periodosPreset = ['hoje' => ['label' => 'Hoje', 'inicio' => $hojeChip->toDateString(), 'fim' => $hojeChip->toDateString()]];
            foreach ([7, 15, 30, 60, 90] as $diasChip) {
                $periodosPreset[(string) $diasChip] = [
                    'label' => "$diasChip dias",
                    'inicio' => $hojeChip->copy()->subDays($diasChip - 1)->toDateString(),
                    'fim' => $hojeChip->toDateString(),
                ];
            }
            $dataInicioAtual = request('data_inicio');
            $dataFimAtual = request('data_fim');
            $semFiltroPeriodo = !$dataInicioAtual && !$dataFimAtual;
            $bateComAlgumPreset = false;
        @endphp
        <div class="periodo-chips" role="group" aria-label="Atalho de período">
            @foreach ($periodosPreset as $chave => $preset)
                @php
                    $ativo = ((string) $chave === '7' && $semFiltroPeriodo) || ($dataInicioAtual === $preset['inicio'] && $dataFimAtual === $preset['fim']);
                    $bateComAlgumPreset = $bateComAlgumPreset || $ativo;
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['data_inicio' => $preset['inicio'], 'data_fim' => $preset['fim']]) }}"
                   class="periodo-chip {{ $ativo ? 'active' : '' }}">{{ $preset['label'] }}</a>
            @endforeach
            <button type="button" class="periodo-chip {{ (!$semFiltroPeriodo && !$bateComAlgumPreset) ? 'active' : '' }}" onclick="document.getElementById('openFiltrosBtn').click()">
                <i class="fa-solid fa-sliders" aria-hidden="true"></i> Personalizado
            </button>
        </div>

        @php
            $statCards = $statCards ?? [];
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

        {{-- De onde vêm os números: o produtor precisa saber que é o plano,
             não o que foi medido no cocho. --}}
        <p class="relatorio-fonte-dados">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            Consumo e custo deste relatório vêm do <strong>plano alimentar</strong>: a ração vinculada a cada lote, pelo preço cadastrado de cada ração.
            O que foi realmente colocado ou consumido em cada cocho aparece em <a href="{{ route('cocho.listar') }}">Gestão de Cochos</a>.
        </p>

        <div class="stat-cards-row">
            <div class="stat-card stat-card--verde">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-verde"><i class="fa-solid fa-sack-dollar" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Consumo Planejado</div>
                    <div class="stat-card-valor">{{ number_format($statCards['consumo']['valor'] ?? 0, 0, ',', '.') }} kg</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCards['consumo']['variacao'] ?? null) }}">
                    @if (is_null($statCards['consumo']['variacao'] ?? null))
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem período anterior
                    @else
                        <i class="fa-solid {{ $trendIcone($statCards['consumo']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCards['consumo']['variacao']), 1, ',', '.') }}% vs período anterior
                    @endif
                </div>
            </div>

            <div class="stat-card stat-card--ambar">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-ambar"><i class="fa-solid fa-coins" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Custo Planejado</div>
                    <div class="stat-card-valor">R$ {{ number_format($statCards['custo']['valor'] ?? 0, 2, ',', '.') }}</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCards['custo']['variacao'] ?? null) }}">
                    @if (is_null($statCards['custo']['variacao'] ?? null))
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem período anterior
                    @else
                        <i class="fa-solid {{ $trendIcone($statCards['custo']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCards['custo']['variacao']), 1, ',', '.') }}% vs período anterior
                    @endif
                </div>
            </div>

            <div class="stat-card stat-card--azul">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-azul"><i class="fa-solid fa-cow" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Média Diária</div>
                    <div class="stat-card-valor">{{ number_format($statCards['mediaDiaria']['valor'] ?? 0, 2, ',', '.') }} kg/dia</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCards['mediaDiaria']['variacao'] ?? null) }}">
                    @if (is_null($statCards['mediaDiaria']['variacao'] ?? null))
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem período anterior
                    @else
                        <i class="fa-solid {{ $trendIcone($statCards['mediaDiaria']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCards['mediaDiaria']['variacao']), 1, ',', '.') }}% vs período anterior
                    @endif
                </div>
            </div>

            <div class="stat-card stat-card--ciano">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-ciano"><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Consumo por kg Vivo</div>
                    <div class="stat-card-valor">{{ $statCards['porKgVivo']['valor'] !== null ? number_format($statCards['porKgVivo']['valor'], 3, ',', '.') . ' kg/kg' : '—' }}</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCards['porKgVivo']['variacao'] ?? null) }}">
                    @if (is_null($statCards['porKgVivo']['variacao'] ?? null))
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem período anterior
                    @else
                        <i class="fa-solid {{ $trendIcone($statCards['porKgVivo']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCards['porKgVivo']['variacao']), 1, ',', '.') }}% vs período anterior
                    @endif
                </div>
            </div>

            <div class="stat-card stat-card--roxo">
                <div class="stat-card-top">
                    <div class="stat-card-icon icone-roxo"><i class="fa-solid fa-bullseye" aria-hidden="true"></i></div>
                </div>
                <div>
                    <div class="stat-card-label">Eficiência Média</div>
                    <div class="stat-card-valor">{{ $statCards['eficiencia']['valor'] ?? 0 }}%</div>
                </div>
                <div class="stat-card-trend {{ $trendClasse($statCards['eficiencia']['variacao'] ?? null) }}">
                    @if (is_null($statCards['eficiencia']['variacao'] ?? null))
                        <i class="fa-solid fa-minus" aria-hidden="true"></i> Sem período anterior
                    @else
                        <i class="fa-solid {{ $trendIcone($statCards['eficiencia']['variacao']) }}" aria-hidden="true"></i>
                        {{ number_format(abs($statCards['eficiencia']['variacao']), 1, ',', '.') }}% vs período anterior
                    @endif
                </div>
            </div>
        </div>

        @if (($rankingLotesTotal ?? 0) >= 2)
            @php
                $loteDestaqueMelhor = $rankingLotesCompleto->first();
                $loteDestaquePior = $rankingLotesCompleto->last();
            @endphp
            <div class="destaque-cards-row">
                <div class="stat-card stat-card--verde">
                    <div class="stat-card-top">
                        <div class="stat-card-icon icone-verde"><i class="fa-solid fa-trophy" aria-hidden="true"></i></div>
                    </div>
                    <div>
                        <div class="stat-card-label">Melhor Desempenho</div>
                        <div class="stat-card-valor">{{ $loteDestaqueMelhor->nome_lote }}</div>
                    </div>
                    <div class="stat-card-trend trend-up">
                        <i class="fa-solid fa-gauge-high" aria-hidden="true"></i> {{ $loteDestaqueMelhor->eficiencia }}% de eficiência · {{ number_format($loteDestaqueMelhor->consumo_medio_dia, 2, ',', '.') }} kg/dia
                    </div>
                </div>

                <div class="stat-card stat-card--ambar">
                    <div class="stat-card-top">
                        <div class="stat-card-icon icone-ambar"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div>
                    </div>
                    <div>
                        <div class="stat-card-label">Necessita Atenção</div>
                        <div class="stat-card-valor">{{ $loteDestaquePior->nome_lote }}</div>
                    </div>
                    <div class="stat-card-trend trend-down">
                        <i class="fa-solid fa-gauge-high" aria-hidden="true"></i> {{ $loteDestaquePior->eficiencia }}% de eficiência · {{ number_format($loteDestaquePior->consumo_medio_dia, 2, ',', '.') }} kg/dia
                    </div>
                </div>
            </div>
        @endif

        <div class="relatorio-grid-2col">

            <div class="card-grafico">
                <div class="card-grafico-header">
                    <h2 id="sec-consumo-tempo"><i class="fa-solid fa-wave-square" style="color: var(--verde-texto, #0e4a27);"></i> Consumo de Ração ao Longo do Tempo</h2>
                    <p>Consumo diário previsto pelo plano alimentar (ração vinculada a cada lote) &middot; {{ $periodoLabel ?? '' }}</p>
                </div>
                <div class="chart-container">
                    <canvas id="chartConsumoTempo"></canvas>
                    <div class="chart-empty-overlay" id="consumoTempoEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-chart-simple"></i>
                            Nenhum consumo registrado para os filtros selecionados.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-grafico">
                <div class="card-grafico-header">
                    <h2 id="sec-consumo-custo-lote"><i class="fa-solid fa-chart-bar" style="color: var(--verde-texto, #0e4a27);"></i> Consumo e Custo por Lote</h2>
                    <p>Comparativo entre consumo (kg/dia) e custo (R$/dia)</p>
                </div>
                <div class="chart-container">
                    <canvas id="chartConsumoLote"></canvas>
                    <div class="chart-empty-overlay" id="consumoLoteEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-layer-group"></i>
                            Nenhum lote com consumo registrado para os filtros selecionados.
                        </div>
                    </div>
                </div>
                @if ($loteMaiorConsumo ?? null)
                    <div class="insight-banner">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <span><strong>{{ $loteMaiorConsumo }}</strong> apresenta o maior consumo e custo diário no período analisado.</span>
                    </div>
                @endif
            </div>

        </div>

        <div class="card-grafico" style="margin-bottom: 25px;">
            <div class="card-grafico-header">
                <h2 id="sec-evolucao-historica"><i class="fa-solid fa-chart-line" style="color: var(--verde-texto, #0e4a27);"></i> Evolução Histórica (6 meses)</h2>
                <p>Consumo total mensal, sempre os últimos 6 meses — independente do período filtrado acima</p>
            </div>
            <div class="chart-container">
                <canvas id="chartEvolucaoHistorica"></canvas>
                <div class="chart-empty-overlay" id="evolucaoHistoricaEmptyState">
                    <div class="no-data-message">
                        <i class="fa-solid fa-chart-simple"></i>
                        Sem consumo registrado nos últimos 6 meses para os lotes selecionados.
                    </div>
                </div>
            </div>
        </div>

        <div class="relatorio-grid-3col">

            <div class="card-grafico">
                <div class="card-grafico-header">
                    <h2 id="sec-distribuicao-gastos"><i class="fa-solid fa-chart-pie" style="color: var(--verde-texto, #0e4a27);"></i> Distribuição de Gastos por Ração</h2>
                    <p>Composição do custo total por tipo de ração</p>
                </div>
                <div class="donut-layout">
                    <div class="donut-chart-box">
                        <canvas id="chartDistribucaoGastos"></canvas>
                        <div class="donut-center-total">
                            <span class="valor">R$ {{ number_format($custoTotalEstimado ?? 0, 0, ',', '.') }}</span>
                            <span class="rotulo">Total</span>
                        </div>
                    </div>
                    <div class="donut-legend-box">
                        <ul id="donutLegendList"></ul>
                    </div>
                    <div class="chart-empty-overlay" id="distribuicaoEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-wheat-awn"></i>
                            Nenhum gasto com ração registrado para os filtros selecionados.
                        </div>
                    </div>
                </div>
                <a href="{{ route('racao.listar') }}" class="card-link">Ver detalhes completos <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="card-grafico">
                <div class="card-grafico-header">
                    <h2 id="sec-eficiencia-lote"><i class="fa-solid fa-bullseye" style="color:var(--verde-texto, #0e4a27);"></i> Eficiência por Lote</h2>
                    <p>Indicadores comparativos de desempenho</p>
                </div>
                <div class="chart-container">
                    <canvas id="chartEficienciaLote"></canvas>
                    <div class="chart-empty-overlay" id="eficienciaEmptyState">
                        <div class="no-data-message">
                            <i class="fa-solid fa-bullseye"></i>
                            Nenhum lote disponível para calcular eficiência.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-grafico">
                <div class="card-grafico-header">
                    <h2 id="sec-resumo-categoria"><i class="fa-solid fa-layer-group" style="color:var(--verde-texto, #0e4a27);"></i> Resumo por Categoria</h2>
                    <p>Visão geral consolidada por tipo de animal</p>
                </div>
                @if (($resumoPorCategoria ?? collect())->isNotEmpty())
                    <div class="resumo-categoria-list">
                        @foreach ($resumoPorCategoria as $categoria)
                            <div class="resumo-categoria-item">
                                <div class="resumo-categoria-icon"><i class="fa-solid {{ $categoria->icone }}" aria-hidden="true"></i></div>
                                <div class="resumo-categoria-info">
                                    <div class="resumo-categoria-nome">{{ $categoria->label }}</div>
                                    <div class="resumo-categoria-sub">{{ $categoria->quantidade_lotes }} {{ Str::plural('lote', $categoria->quantidade_lotes) }}</div>
                                </div>
                                <div class="resumo-categoria-numeros">
                                    <span class="consumo">{{ number_format($categoria->consumo_total, 0, ',', '.') }} kg</span>
                                    <span class="custo">R$ {{ number_format($categoria->custo_total, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="#rankingLotes" class="card-link">Ver ranking completo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                @else
                    <div class="no-data-message">
                        <i class="fa-solid fa-layer-group"></i>
                        Nenhum lote encontrado para os filtros selecionados.
                    </div>
                @endif
            </div>

        </div>

        <div class="card-grafico" id="rankingLotes">
            <div class="card-grafico-header card-grafico-header-flex">
                <div>
                    <h2 id="sec-ranking-lotes"><i class="fa-solid fa-ranking-star" style="color:var(--verde-texto, #0e4a27);"></i> Ranking de Lotes por Desempenho</h2>
                    <p>Ordenação por eficiência alimentar</p>
                </div>
                <button type="button" class="btn-toggle-ranking" id="btnToggleRanking" onclick="alternarRankingLotes()" aria-expanded="true" aria-controls="conteudoRankingLotes">
                    <i class="fa-solid fa-eye-slash" aria-hidden="true"></i> <span>Ocultar ranking</span>
                </button>
            </div>
            <div id="conteudoRankingLotes">
                @if (($rankingLotesTotal ?? 0) > 0)
                    <div class="ranking-table-wrap">
                        <table class="ranking-table">
                            <caption class="sr-only">Ranking de lotes por desempenho, do mais ao menos eficiente</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Posição</th>
                                    <th scope="col">Lote</th>
                                    <th scope="col">Categoria</th>
                                    <th scope="col">Consumo Médio (kg/dia)</th>
                                    <th scope="col">Custo Médio (R$/dia)</th>
                                    <th scope="col">Eficiência (%)</th>
                                    <th scope="col">Variação</th>
                                </tr>
                            </thead>
                            <tbody id="corpoRankingLotes">
                                @include('partials.ranking-lotes-linhas', ['ranking' => $rankingLotesExibidos])
                            </tbody>
                        </table>
                    </div>
                    @if ($rankingLotesTotal > $rankingLotesExibidos->count())
                        <button type="button" class="btn-ver-mais" id="btnVerMaisRanking" onclick="consultarMaisRanking()">Ver mais lotes</button>
                    @endif
                @else
                    <div class="no-data-message">
                        <i class="fa-solid fa-ranking-star"></i>
                        Nenhum lote encontrado para os filtros selecionados.
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/vendor/chart.min.js') }}"></script>

<script>
    // O "zoom" do <html> (ver partials/acessibilidade) muda o pixel ratio
    // efetivo da página sem alterar window.devicePixelRatio — deixando o
    // Chart.js no automático, ele mede o canvas já "zoomado" e desenha em
    // cima disso de novo, esticando/cortando os gráficos. Fixando em 1
    // aqui, o dimensionamento fica só por conta do zoom do navegador (que
    // já escala tudo igualmente), sem essa duplicação.
    if (window.Chart) {
        Chart.defaults.devicePixelRatio = 1;
    }

    // ==========================================
    // DADOS VINDOS DO BACKEND (LARAVEL)
    // ==========================================

    let datasX = @json($labelsDatas ?? []);
    let consumoY = @json($valoresConsumo ?? []);

    let lotesLabels = @json($labelsLotes ?? []);
    let kgLoteDados = @json($kgPorLote ?? []);
    let custoLoteDados = @json($custoPorLote ?? []);

    let labelsGastos = @json($labelsGastos ?? []);
    let valoresGastos = @json($valoresGastos ?? []);

    let datasetsRadar = @json($dadosRadarLotes ?? []);

    let labelsEvolucaoHistorica = @json($evolucaoHistorica['labels'] ?? []);
    let valoresEvolucaoHistorica = @json($evolucaoHistorica['valores'] ?? []);

    // Estado real de cada gráfico (sem dados fictícios de preenchimento —
    // se o banco não tem nada para os filtros escolhidos, o gráfico mostra
    // a mensagem de "sem dados" em vez de números inventados).
    const temConsumoTempo = datasX.length > 0 && consumoY.some(v => Number(v) > 0);
    const temConsumoLote = lotesLabels.length > 0 && kgLoteDados.some(v => Number(v) > 0);
    const temDistribuicao = labelsGastos.length > 0 && valoresGastos.some(v => Number(v) > 0);
    const temRadar = datasetsRadar.length > 0;
    const temEvolucaoHistorica = valoresEvolucaoHistorica.some(v => Number(v) > 0);

    document.getElementById('consumoTempoEmptyState')?.classList.toggle('visible', !temConsumoTempo);
    document.getElementById('consumoLoteEmptyState')?.classList.toggle('visible', !temConsumoLote);
    document.getElementById('distribuicaoEmptyState')?.classList.toggle('visible', !temDistribuicao);
    document.getElementById('eficienciaEmptyState')?.classList.toggle('visible', !temRadar);
    document.getElementById('evolucaoHistoricaEmptyState')?.classList.toggle('visible', !temEvolucaoHistorica);

    // ==========================================
    // MENU E MODAL
    // ==========================================

    const modal = document.getElementById('modalFiltros');
    const openFiltrosBtn = document.getElementById('openFiltrosBtn');
    const closeFiltrosBtn = document.getElementById('closeFiltrosBtn');
    const toggleMenuBtn = document.getElementById('toggleMenuBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (openFiltrosBtn) {
        openFiltrosBtn.onclick = () => modal.classList.add('active');
    }

    if (closeFiltrosBtn) {
        closeFiltrosBtn.onclick = () => modal.classList.remove('active');
    }

    window.onclick = (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.classList.remove('active');
        }
    });

    // Abrir/fechar a sidebar (drawer) muda a largura do .main (transição de
    // 0.3s), mas o Chart.js só remede sozinho no "resize" da janela — sem
    // isso os gráficos ficavam com o tamanho antigo e "quebravam" (overflow)
    // até a próxima ação. Remede logo após a transição terminar. Função
    // compartilhada por TODOS os jeitos de fechar/abrir a drawer (botão do
    // menu, clique no overlay no mobile) — antes só o botão chamava isso, e
    // fechar clicando fora (overlay) deixava os gráficos quebrados.
    function remedirGraficosAposTransicaoSidebar() {
        setTimeout(function () {
            if (chartConsumoTempo) chartConsumoTempo.resize();
            if (chartConsumoLote) chartConsumoLote.resize();
            if (chartDistribuicao) chartDistribuicao.resize();
            if (chartRadar) chartRadar.resize();
            if (chartEvolucaoHistorica) chartEvolucaoHistorica.resize();
        }, 320);
    }

    // O botão de menu e o overlay já abrem/fecham a drawer (ver
    // layouts/app.blade.php) — aqui só reagimos à mesma ação para remedir
    // os gráficos depois da transição.
    if (toggleMenuBtn) {
        toggleMenuBtn.addEventListener('click', remedirGraficosAposTransicaoSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', remedirGraficosAposTransicaoSidebar);
    }

    // ==========================================
    // FILTROS: chips ativos, remoção individual, cascata
    // Fazenda → Lote e atalhos de período
    // ==========================================

    // Mapeia o "chave" de cada chip para o(s) parâmetro(s) de query real(is)
    // que devem ser removidos da URL ao clicar no "×". A Fazenda não entra
    // mais aqui — virou a Unidade Produtiva selecionada no topo, e
    // removerFiltrosDaURL() só mexe nos parâmetros que ela mesma apaga, sem
    // tocar em fazenda_id (que já está na URL atual).
    const PARAMS_POR_FILTRO = {
        lote: ['lote'],
        categoria_animal: ['categoria_animal'],
        tipo_racao: ['tipo_racao'],
        periodo: ['data_inicio', 'data_fim']
    };

    function removerFiltrosDaURL(chaves) {
        const url = new URL(window.location.href);
        chaves.forEach((chave) => {
            (PARAMS_POR_FILTRO[chave] || [chave]).forEach((param) => url.searchParams.delete(param));
        });
        window.location.href = url.pathname + (url.search || '');
    }

    document.querySelectorAll('.remover-filtro').forEach((botao) => {
        botao.addEventListener('click', () => removerFiltrosDaURL([botao.dataset.chave]));
    });

    const limparTudoBtn = document.getElementById('limparTudoBtn');
    if (limparTudoBtn) {
        limparTudoBtn.addEventListener('click', () => {
            // "Limpar todos" só zera Lote/Categoria/Ração/Período — a
            // Unidade Produtiva continua selecionada.
            window.location.href = '{{ route('relatorio.listar') }}?fazenda_id={{ $fazendaSelecionadaId }}';
        });
    }

    // Atalhos de período: preenchem as duas datas de uma vez.
    document.querySelectorAll('.btn-preset').forEach((botao) => {
        botao.addEventListener('click', () => {
            const hoje = new Date();
            const fim = new Date(hoje);
            let inicio = new Date(hoje);

            if (botao.dataset.dias) {
                inicio.setDate(hoje.getDate() - (parseInt(botao.dataset.dias, 10) - 1));
            } else if (botao.dataset.mesAtual) {
                inicio = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
            }

            const paraISO = (d) => d.toISOString().slice(0, 10);
            document.getElementById('dataInicio').value = paraISO(inicio);
            document.getElementById('dataFim').value = paraISO(fim);

            document.querySelectorAll('.btn-preset').forEach((b) => b.classList.remove('ativo'));
            botao.classList.add('ativo');
        });
    });

    // ==========================================
    // GRÁFICOS: cores lidas das variáveis CSS do tema atual e recriados
    // sempre que o modo escuro / alto contraste é alternado (mesma técnica
    // do Dashboard), para os 4 gráficos acompanharem o tema em tempo real.
    // ==========================================

    function cssVar(name, fallback) {
        const valor = getComputedStyle(document.body).getPropertyValue(name).trim();
        return valor || fallback;
    }

    function hexParaRgba(hex, alpha) {
        let h = hex.replace('#', '').trim();
        if (h.length === 3) h = h.split('').map(c => c + c).join('');
        const bigint = parseInt(h, 16);
        if (isNaN(bigint)) return `rgba(16, 185, 129, ${alpha})`;
        const r = (bigint >> 16) & 255, g = (bigint >> 8) & 255, b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function paletaCategorica() {
        return [1, 2, 3, 4, 5, 6].map(i => cssVar(`--chart-${i}`, '#10b981'));
    }

    const canvasConsumoTempo = document.getElementById('chartConsumoTempo');
    const canvasConsumoLote = document.getElementById('chartConsumoLote');
    const canvasDistribuicao = document.getElementById('chartDistribucaoGastos');
    const radarCanvas = document.getElementById('chartEficienciaLote');
    const canvasEvolucaoHistorica = document.getElementById('chartEvolucaoHistorica');
    const legendaGastos = document.getElementById('donutLegendList');

    let chartConsumoTempo = null;
    let chartConsumoLote = null;
    let chartDistribuicao = null;
    let chartRadar = null;
    let chartEvolucaoHistorica = null;

    function montarGraficos() {
        const textoSecundario = cssVar('--text-secundario', '#6b7280');
        const textoPrincipal = cssVar('--text-principal', '#1f2937');
        const corGrid = cssVar('--chart-grid', '#eef2f6');
        const corSurface = cssVar('--card-bg', '#ffffff');
        const corBase = cssVar('--chart-1', '#10b981');
        const corSecundaria = cssVar('--chart-2', '#3b82f6');
        const paleta = paletaCategorica();

        if (chartConsumoTempo) chartConsumoTempo.destroy();
        if (chartConsumoLote) chartConsumoLote.destroy();
        if (chartDistribuicao) chartDistribuicao.destroy();
        if (chartRadar) chartRadar.destroy();
        if (chartEvolucaoHistorica) chartEvolucaoHistorica.destroy();

        // GRÁFICO 1 - CONSUMO AO LONGO DO TEMPO
        if (canvasConsumoTempo) {
            chartConsumoTempo = new Chart(canvasConsumoTempo, {
                type: 'line',
                data: {
                    labels: datasX,
                    datasets: [{
                        label: 'Consumo Total',
                        data: consumoY,
                        borderColor: corBase,
                        backgroundColor: hexParaRgba(corBase, 0.08),
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: corBase,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: corSurface,
                            titleColor: textoPrincipal,
                            bodyColor: textoSecundario,
                            borderColor: corGrid,
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textoSecundario }
                        },
                        y: {
                            beginAtZero: false,
                            grid: { color: corGrid },
                            ticks: { color: textoSecundario, callback: value => value + ' kg' }
                        }
                    }
                }
            });
        }

        // GRÁFICO 2 - CONSUMO POR LOTE
        if (canvasConsumoLote) {
            // Math.max(...[]) daria -Infinity sem lotes filtrados; o
            // gráfico fica coberto pelo overlay de "sem dados" de qualquer
            // forma, mas o eixo precisa de um teto numérico válido.
            const maxKg = (Math.max(0, ...kgLoteDados) * 1.3) || 10;
            const maxCusto = (Math.max(0, ...custoLoteDados) * 1.3) || 10;

            chartConsumoLote = new Chart(canvasConsumoLote, {
                type: 'bar',
                data: {
                    labels: lotesLabels,
                    datasets: [
                        {
                            label: 'Consumo (kg/dia)',
                            data: kgLoteDados,
                            backgroundColor: corBase,
                            borderRadius: 8,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Custo (R$/dia)',
                            data: custoLoteDados,
                            backgroundColor: hexParaRgba(corSecundaria, 0.55),
                            borderRadius: 8,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { color: textoSecundario } },
                        tooltip: {
                            backgroundColor: corSurface,
                            titleColor: textoPrincipal,
                            bodyColor: textoSecundario,
                            borderColor: corGrid,
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textoSecundario }
                        },
                        y: {
                            type: 'linear',
                            position: 'left',
                            min: 0,
                            max: maxKg,
                            grid: { color: corGrid },
                            ticks: { color: textoSecundario }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            min: 0,
                            max: maxCusto,
                            grid: { drawOnChartArea: false },
                            ticks: { color: textoSecundario }
                        }
                    }
                }
            });
        }

        // GRÁFICO 3 - DISTRIBUIÇÃO DE GASTOS
        // Paleta usada tanto no gráfico quanto na legenda ao lado, para os
        // dois ficarem sempre sincronizados (repete a paleta caso existam
        // mais rações cadastradas do que cores).
        const coresGastos = labelsGastos.map((_, i) => paleta[i % paleta.length]);

        if (legendaGastos) {
            legendaGastos.innerHTML = labelsGastos.map((label, i) => `
                <li>
                    <span class="legend-label"><i class="fa-solid fa-circle" style="color: ${coresGastos[i]};"></i> ${label}</span>
                    <span class="legend-value">${valoresGastos[i]}%</span>
                </li>
            `).join('');
        }

        if (canvasDistribuicao) {
            chartDistribuicao = new Chart(canvasDistribuicao, {
                type: 'doughnut',
                data: {
                    labels: labelsGastos,
                    datasets: [{
                        data: valoresGastos,
                        backgroundColor: coresGastos,
                        borderColor: corSurface,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: corSurface,
                            titleColor: textoPrincipal,
                            bodyColor: textoSecundario,
                            borderColor: corGrid,
                            borderWidth: 1
                        }
                    }
                }
            });
        }

        // GRÁFICO 4 - EFICIÊNCIA DOS LOTES
        if (radarCanvas) {
            chartRadar = new Chart(radarCanvas, {
                type: 'radar',
                data: {
                    labels: [
                        'Ganho de Peso',
                        'Consumo',
                        'Conversão',
                        'Saúde',
                        'Custo-Benefício',
                        'Sustentabilidade'
                    ],
                    datasets: datasetsRadar
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: textoSecundario } }
                    },
                    scales: {
                        r: {
                            min: 0,
                            max: 100,
                            grid: { color: corGrid },
                            angleLines: { color: corGrid },
                            pointLabels: { color: textoSecundario },
                            ticks: { display: false }
                        }
                    }
                }
            });
        }

        // GRÁFICO 6 - EVOLUÇÃO HISTÓRICA (6 MESES), sempre a mesma janela,
        // independente do filtro de período ativo (ver
        // RelatorioController::montarEvolucaoHistorica).
        if (canvasEvolucaoHistorica) {
            chartEvolucaoHistorica = new Chart(canvasEvolucaoHistorica, {
                type: 'line',
                data: {
                    labels: labelsEvolucaoHistorica,
                    datasets: [{
                        label: 'Consumo Total (kg)',
                        data: valoresEvolucaoHistorica,
                        borderColor: corSecundaria,
                        backgroundColor: hexParaRgba(corSecundaria, 0.12),
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: corSecundaria,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: corSurface,
                            titleColor: textoPrincipal,
                            bodyColor: textoSecundario,
                            borderColor: corGrid,
                            borderWidth: 1,
                            callbacks: { label: (item) => `${item.formattedValue} kg` }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: textoSecundario } },
                        y: {
                            beginAtZero: true,
                            grid: { color: corGrid },
                            ticks: { color: textoSecundario, callback: value => value + ' kg' }
                        }
                    }
                }
            });
        }
    }

    montarGraficos();

    // Reforço de segurança: força os gráficos a se remedirem depois que a
    // página termina de carregar (fontes, imagens), já com o zoom
    // totalmente aplicado — sem isso, um gráfico criado um instante antes
    // do layout "assentar" pode ficar com o tamanho levemente errado.
    window.addEventListener('load', function () {
        setTimeout(function () {
            if (chartConsumoTempo) chartConsumoTempo.resize();
            if (chartConsumoLote) chartConsumoLote.resize();
            if (chartDistribuicao) chartDistribuicao.resize();
            if (chartRadar) chartRadar.resize();
            if (chartEvolucaoHistorica) chartEvolucaoHistorica.resize();
        }, 150);
    });

    // Correção definitiva do gráfico "quebrando" ao abrir/fechar a sidebar:
    // em vez de tentar prever e caçar toda ação que muda o tamanho do
    // container (botão do menu, overlay no mobile, zoom da acessibilidade,
    // qualquer coisa futura), observa o próprio elemento com ResizeObserver
    // e remede sempre que o tamanho real mudar — funciona não importa a
    // causa, e cobre qualquer ação nova que venha a existir.
    if (window.ResizeObserver) {
        const remedirTodosOsGraficos = function () {
            if (chartConsumoTempo) chartConsumoTempo.resize();
            if (chartConsumoLote) chartConsumoLote.resize();
            if (chartDistribuicao) chartDistribuicao.resize();
            if (chartRadar) chartRadar.resize();
            if (chartEvolucaoHistorica) chartEvolucaoHistorica.resize();
        };

        let remedirAgendado = false;
        const observadorTamanhoGraficos = new ResizeObserver(function () {
            // Debounce via requestAnimationFrame: o ResizeObserver dispara
            // várias vezes durante uma transição CSS (a cada frame) — sem
            // isso seriam dezenas de resize() redundantes por segundo.
            if (remedirAgendado) return;
            remedirAgendado = true;
            window.requestAnimationFrame(function () {
                remedirAgendado = false;
                remedirTodosOsGraficos();
            });
        });

        [canvasConsumoTempo, canvasConsumoLote, canvasDistribuicao, radarCanvas].forEach(function (canvas) {
            if (canvas && canvas.parentElement) {
                observadorTamanhoGraficos.observe(canvas.parentElement);
            }
        });
    }

    // Os gráficos são recriados com as cores certas sempre que o tema
    // (claro/escuro/alto contraste) muda, sem precisar recarregar a página.
    ['chkDarkMode', 'chkHighContrast'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', () => setTimeout(montarGraficos, 60));
    });

    // ==========================================
    // RANKING DE LOTES: ocultar/mostrar (lembrado entre acessos, mesmo
    // padrão de preferência salva já usado pro tema/fonte/zoom).
    // ==========================================
    const CHAVE_RANKING_OCULTO = 'rankingLotesOculto';

    function aplicarEstadoRanking(oculto) {
        const conteudo = document.getElementById('conteudoRankingLotes');
        const botao = document.getElementById('btnToggleRanking');
        if (!conteudo || !botao) return;

        conteudo.hidden = oculto;
        botao.setAttribute('aria-expanded', oculto ? 'false' : 'true');
        botao.querySelector('span').textContent = oculto ? 'Mostrar ranking' : 'Ocultar ranking';
        botao.querySelector('i').className = oculto ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
    }

    function alternarRankingLotes() {
        const conteudo = document.getElementById('conteudoRankingLotes');
        const oculto = !conteudo.hidden;
        aplicarEstadoRanking(oculto);
        localStorage.setItem(CHAVE_RANKING_OCULTO, oculto ? '1' : '0');
    }

    aplicarEstadoRanking(localStorage.getItem(CHAVE_RANKING_OCULTO) === '1');

    // ==========================================
    // RANKING DE LOTES: busca no backend as linhas que ficaram de fora da
    // carga inicial — preservando os filtros (fazenda/lote/ração/período)
    // ativos na URL atual. Mesmo padrão do "Consultar Lotes" do Plano
    // Alimentar.
    // ==========================================
    async function consultarMaisRanking() {
        const botao = document.getElementById('btnVerMaisRanking');
        const textoOriginal = botao.textContent;
        botao.disabled = true;
        botao.textContent = 'Carregando...';

        try {
            const resposta = await fetch('{{ route('relatorio.rankingJson') }}' + window.location.search, {
                headers: { 'Accept': 'application/json' },
            });

            if (resposta.status === 401) {
                window.location.href = '{{ route('login.index') }}';
                return;
            }

            const dados = await resposta.json();

            if (dados.success) {
                document.getElementById('corpoRankingLotes').insertAdjacentHTML('beforeend', dados.html);
                botao.remove();
            } else {
                botao.disabled = false;
                botao.textContent = textoOriginal;
            }
        } catch (erro) {
            botao.disabled = false;
            botao.textContent = textoOriginal;
        }
    }
</script>
@endpush