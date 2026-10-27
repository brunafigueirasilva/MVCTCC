<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AgriFarm')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ==========================================================================
        NAVBAR + DRAWER GLOBAIS — fonte única usada por todas as páginas internas.
        Cada página só deve trazer CSS do próprio conteúdo (cards, tabelas,
        gráficos, formulários); a estrutura de navegação vive só aqui.
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

            --navbar-bg: #ffffff;
            --sidebar-bg: #123f22;
            --surface-border: rgba(15, 23, 42, 0.08);
            --sidebar-divider: rgba(255, 255, 255, 0.28);

            --surface-muted-bg: #f8fafc;
            --surface-muted-border: #cbd5e1;

            --menu-text: #b2cbb9;
            --menu-text-hover: #ffffff;
            --menu-hover-bg: rgba(255, 255, 255, 0.05);
            --menu-active-bg: rgba(255, 255, 255, 0.08);
            --menu-active-text: #ffffff;
            --menu-active-accent: var(--verde-secundario, #16a765);

            /* Painel de Acessibilidade (componente compartilhado) */
            --panel-bg: #ffffff;
            --panel-text: #1f2937;
            --panel-item-bg: #f9fafb;
            --panel-btn-bg: #e5e7eb;
            --panel-btn-text: #374151;
            --panel-btn-hover: #d1d5db;
        }

        html {
            overflow-x: hidden;
            width: 100%;
        }

        body {
            background: var(--bg-principal);
            color: var(--text-principal);
            overflow-x: hidden;
            width: 100%;
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar, .main {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease, filter 0.25s ease;
        }

        /* Ver partials/acessibilidade.blade.php: com o painel aberto, o
           desfoque é aplicado direto na sidebar/main (o backdrop do
           overlay sozinho não desfoca de forma confiável vários elementos
           position:fixed empilhados). */
        body.acc-modal-open .sidebar,
        body.acc-modal-open .main {
            filter: blur(6px);
            pointer-events: none;
        }

        /* ==========================================================================
        BOTÃO FLUTUANTE DE NOTIFICAÇÕES (substitui a navbar removida)
        Fica no canto superior direito, ao lado do botão de acessibilidade
        (que já se posiciona sozinho em partials/acessibilidade.blade.php
        quando não há .navbar-right na página — ou seja, sempre, agora).
        Continua no "mesmo lugar" de antes, só que sem a barra branca por
        trás — flutua sobre o conteúdo graças ao z-index. */
        .floating-notifications-btn {
            position: fixed;
            /* Mesma linha e mesmo tamanho do botão de acessibilidade
               (partials/acessibilidade.blade.php: top/right:20px, 46x46). */
            top: 20px;
            /* 20 (margem) + 46 (botão de acessibilidade) + 10 (respiro) */
            right: 76px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 1px solid var(--surface-border);
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.05rem;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.12);
            z-index: 999;
        }

        .floating-notifications-btn:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        /* Contador vermelho de alertas novos (1, 2, 3...) — escondido por
           padrão; JS mostra e preenche o número quando há alerta. */
        .floating-notifications-btn .icon-dot {
            display: none;
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            background: #ef4444;
            border: 2px solid #f1f5f9;
            color: #ffffff;
            font-size: 0.6875rem;
            font-weight: 700;
            line-height: 1;
            align-items: center;
            justify-content: center;
        }

        .floating-notifications-btn .icon-dot.show {
            display: flex;
        }

        /* Sem encolher em telas pequenas: o botão de acessibilidade (que
           fica ao lado) também não encolhe, e os dois precisam continuar do
           mesmo tamanho e na mesma linha em qualquer largura. */

        /* ==========================================================================
        MODAL DE ALERTAS DO SISTEMA (aberto pelo sino de notificações)
        ========================================================================== */
        .alertas-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1001;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .alertas-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .alertas-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.94);
            width: 90%;
            max-width: 480px;
            max-height: 80vh;
            background: var(--card-bg);
            color: var(--text-principal);
            border-radius: 20px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.35);
            z-index: 1002;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            transition: transform 0.3s ease, opacity 0.25s ease;
        }

        .alertas-modal.open {
            opacity: 1;
            pointer-events: auto;
            transform: translate(-50%, -50%) scale(1);
        }

        .alertas-modal .modal-header {
            padding: 20px 20px 20px 22px;
            background: linear-gradient(135deg, #fef2f2, #fff7ed);
            border-bottom: 1px solid var(--surface-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-shrink: 0;
        }

        body.dark-theme .alertas-modal .modal-header {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.14), rgba(249, 115, 22, 0.10));
        }

        .alertas-modal-header-info { display: flex; align-items: center; gap: 12px; min-width: 0; }

        /* Mesmo estilo de "ícone em círculo com gradiente" usado nos cards
           de lote/cocho/etc. pelo resto do sistema — só que em tom de
           alerta (vermelho), pra combinar com o sino. */
        .alertas-modal-header-icone {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(145deg, #f87171, #dc2626);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.125rem;
            flex-shrink: 0;
            box-shadow: 0 6px 14px -4px rgba(220, 38, 38, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        .alertas-modal .modal-header h3 {
            font-size: 1.0625rem;
            font-weight: 700;
            color: var(--text-principal);
        }

        .alertas-modal-subtitulo {
            font-size: 0.78125rem;
            color: var(--text-secundario);
            margin-top: 2px;
        }

        .alertas-modal .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--card-bg);
            border: 1px solid var(--surface-muted-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9375rem;
            color: var(--text-secundario);
            cursor: pointer;
            flex-shrink: 0;
            line-height: 1;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .alertas-modal .modal-close:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
        body.dark-theme .alertas-modal .modal-close:hover { background: rgba(239, 68, 68, 0.14); border-color: rgba(239, 68, 68, 0.3); }

        .alertas-modal .modal-body {
            padding: 16px 20px 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .alertas-modal-item {
            background: var(--card-bg);
            border: 1px solid var(--surface-muted-border);
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }

        .alertas-modal-item:hover {
            box-shadow: var(--card-shadow);
            transform: translateY(-1px);
        }

        .alertas-modal-item.critico { background: #fef2f2; border-color: #fecaca; }
        .alertas-modal-item.baixo { background: #fff7ed; border-color: #fed7aa; }

        body.dark-theme .alertas-modal-item.critico { background: rgba(239, 68, 68, 0.10); border-color: rgba(239, 68, 68, 0.25); }
        body.dark-theme .alertas-modal-item.baixo { background: rgba(249, 115, 22, 0.10); border-color: rgba(249, 115, 22, 0.25); }

        .alertas-modal-item-icone {
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

        .alertas-modal-item.critico .alertas-modal-item-icone {
            background: linear-gradient(145deg, #f87171, #dc2626);
            box-shadow: 0 4px 10px -3px rgba(220, 38, 38, 0.5);
        }

        .alertas-modal-item.baixo .alertas-modal-item-icone {
            background: linear-gradient(145deg, #fb923c, #ea580c);
            box-shadow: 0 4px 10px -3px rgba(234, 88, 12, 0.5);
        }

        .alertas-modal-item-corpo { min-width: 0; flex: 1; }

        .alertas-modal-item-topo {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .alertas-modal-item h4 {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-principal);
            overflow-wrap: break-word;
        }

        .alertas-modal-item p {
            font-size: 0.8125rem;
            color: var(--text-secundario);
            line-height: 1.45;
            margin-top: 3px;
        }

        .alertas-modal-item-badge {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .alertas-modal-item.critico .alertas-modal-item-badge { background: #fee2e2; color: #dc2626; }
        .alertas-modal-item.baixo .alertas-modal-item-badge { background: #ffedd5; color: #c2410c; }
        body.dark-theme .alertas-modal-item.critico .alertas-modal-item-badge { background: rgba(239, 68, 68, 0.18); color: #fca5a5; }
        body.dark-theme .alertas-modal-item.baixo .alertas-modal-item-badge { background: rgba(249, 115, 22, 0.18); color: #fdba74; }

        .alertas-modal-empty,
        .alertas-modal-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
            padding: 44px 20px;
            color: var(--text-secundario);
            font-size: 0.875rem;
        }

        .alertas-modal-empty i {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--verde-claro, #e8f5ee);
            color: var(--verde-texto, #0e4a27);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.375rem;
        }

        body.dark-theme .alertas-modal-empty i { background: color-mix(in srgb, var(--verde-secundario, #16a765) 14%, transparent); color: var(--verde-texto, #0e4a27); }

        .alertas-modal-empty.erro i { background: #fee2e2; color: #dc2626; }
        body.dark-theme .alertas-modal-empty.erro i { background: rgba(239, 68, 68, 0.14); color: #fca5a5; }

        .alertas-modal-loading i { font-size: 1.5rem; color: var(--text-secundario); }

        @media (max-width: 480px) {
            .alertas-modal { width: 94%; }
        }

        /* ALTO CONTRASTE — este modal fica na layout compartilhada (some em
           toda página), então ganha suas próprias regras aqui em vez de
           depender das regras genéricas de alto contraste de cada tela. */
        body.high-contrast .alertas-modal {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }
        body.high-contrast .alertas-modal .modal-header {
            background: #000000 !important;
            border-bottom: 2px solid #ffff00 !important;
        }
        body.high-contrast .alertas-modal .modal-header h3,
        body.high-contrast .alertas-modal-subtitulo,
        body.high-contrast .alertas-modal-item h4,
        body.high-contrast .alertas-modal-item p,
        body.high-contrast .alertas-modal-empty,
        body.high-contrast .alertas-modal-loading {
            color: #ffff00 !important;
        }
        body.high-contrast .alertas-modal-header-icone,
        body.high-contrast .alertas-modal-item-icone,
        body.high-contrast .alertas-modal-empty i {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }
        body.high-contrast .alertas-modal-item {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }
        body.high-contrast .alertas-modal-item-badge {
            background: #000000 !important;
            border: 1px solid #ffff00 !important;
            color: #ffff00 !important;
        }
        body.high-contrast .alertas-modal .modal-close {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* ==========================================================================
        DRAWER LATERAL (SIDEBAR) — única navegação da aplicação. Um único
        botão flutuante (.sidebar-toggle-btn), sempre no mesmo lugar, abre e
        fecha (ícone alterna ☰ / X via JS) — evita ter dois botões diferentes
        em posições diferentes para a mesma ação. Rola por dentro só quando
        a tela é mais baixa que o menu; fecha também com ESC.
        ========================================================================== */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--surface-border);
            box-shadow: 2px 0 12px rgba(15, 23, 42, 0.03);
            color: var(--menu-text);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            padding: 18px 16px;
            /* Rola por dentro quando a tela é mais baixa que o menu (~770px:
               notebook 1366x768, celular deitado, zoom 150%/200%). Antes era
               "overflow: hidden" e os últimos itens — inclusive "Sair" —
               ficavam cortados e inalcançáveis. */
            overflow-x: hidden;
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
            display: flex;
            flex-direction: column;
            z-index: 998;
        }

        /* Itens do menu não "espremem" quando falta altura: a drawer rola. */
        .sidebar > * { flex-shrink: 0; }

        .sidebar.collapsed {
            left: -260px;
        }

        .sidebar-toggle-btn {
            position: fixed;
            top: 26px;
            left: 18px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 1px solid var(--surface-border);
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.12);
            z-index: 999;
        }

        .sidebar-toggle-btn:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .sidebar-toggle-btn { width: 30px; height: 30px; top: 28px; left: 15px; }
        }

        /* Reserva, dentro da própria drawer, o canto onde o botão flutuante
           fica por cima — assim a logo nunca fica atrás dele. */
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px 0 14px 46px;
            margin-bottom: 12px;
            border-bottom: 1px solid var(--sidebar-divider);
            min-height: 48px;
        }

        .sidebar-logo {
            height: 48px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, .5)) drop-shadow(0 0 12px rgba(0, 0, 0, .3));
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            font-size: 1rem;
            border: 1px solid var(--surface-border);
            flex-shrink: 0;
            position: relative;
        }

        .user-online-dot {
            position: absolute;
            bottom: -1px;
            right: -1px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: var(--verde-secundario, #16a765);
            border: 2px solid var(--sidebar-bg);
        }

        /* --- Busca de páginas/itens do sistema, dentro do drawer --- */
        .sidebar-search {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 10px;
            padding: 9px 12px;
            margin-bottom: 14px;
        }

        /* Foco no campo de busca: borda verde clara (sem o anel âmbar dos
           demais controles — mesmo padrão dos campos de escrita). */
        .sidebar-search:focus-within {
            border-color: #4ade80;
            background: rgba(255, 255, 255, 0.11);
            box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.35);
        }

        .sidebar-search-icon {
            color: var(--menu-text);
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .sidebar-search-input {
            border: none;
            outline: none;
            background: transparent;
            color: #ffffff;
            font-size: 0.8125rem;
            font-family: inherit;
            width: 100%;
            min-width: 0;
        }

        .sidebar-search-input::placeholder {
            color: var(--menu-text);
            opacity: 0.9;
        }

        .sidebar-search-results {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: var(--card-bg);
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            box-shadow: 0 16px 30px -10px rgba(15, 23, 42, 0.35);
            max-height: 320px;
            overflow-y: auto;
            display: none;
            z-index: 5;
        }

        .sidebar-search-results.open {
            display: block;
        }

        .sidebar-search-result-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-principal);
            text-decoration: none;
        }

        .sidebar-search-result-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
            line-height: 1.25;
        }

        .sidebar-search-result-text small {
            font-size: 0.6875rem;
            font-weight: 500;
            color: var(--text-secundario, #64748b);
        }

        .busca-destaque {
            outline: 3px solid var(--verde-principal, #0e4a27);
            outline-offset: 4px;
            border-radius: 14px;
        }

        .sidebar-search-result-item:hover,
        .sidebar-search-result-item.active {
            background: var(--surface-muted-bg, #f1f5f9);
        }

        .sidebar-search-empty {
            padding: 10px 12px;
            font-size: 0.8125rem;
            color: var(--text-secundario);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            padding: 12px 10px;
            margin: -6px -10px 8px;
            border-radius: 12px;
            cursor: pointer;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--sidebar-divider);
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.08);
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

        .menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .menu a {
            text-decoration: none;
            color: var(--menu-text);
            padding: 10px 14px;
            border-radius: 0 10px 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            border-left: 4px solid transparent;
            position: relative;
            transition: background-color 0.18s ease, color 0.18s ease, border-left-color 0.18s ease, transform 0.18s ease;
        }

        .menu a:hover {
            background: var(--menu-hover-bg);
            color: var(--menu-text-hover);
            transform: translateX(4px);
        }

        @keyframes menuIconBounce {
            0% { transform: translateY(0) scale(1); }
            30% { transform: translateY(-9px) scale(1.16); }
            50% { transform: translateY(1px) scale(0.95); }
            70% { transform: translateY(-4px) scale(1.06); }
            100% { transform: translateY(0) scale(1); }
        }

        .menu a:hover i {
            animation: menuIconBounce 0.55s cubic-bezier(.34,1.56,.64,1);
        }

        .menu a.active {
            background: var(--menu-active-bg);
            color: var(--menu-active-text);
            font-weight: 700;
            border-left-color: var(--menu-active-accent);
            box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent);
        }

        .menu i {
            width: 20px;
            font-size: 1rem;
            text-align: center;
            transition: transform 0.18s ease;
        }

        @media (prefers-reduced-motion: reduce) {
            .menu a, .menu i { transition: background-color 0.18s ease, color 0.18s ease; }
            .menu a:hover { transform: none; }
            .menu a:hover i { animation: none; }
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid var(--sidebar-divider);
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .sidebar-logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            border: 1px solid rgba(248, 113, 113, 0.38);
            background: rgba(239, 68, 68, 0.12);
            color: #fecaca;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.2px;
            text-decoration: none;
            cursor: pointer;
            font-family: inherit;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease,
                        box-shadow 0.2s ease, transform 0.2s ease;
        }

        .sidebar-logout-btn i {
            font-size: 0.95rem;
            transition: transform 0.2s ease;
        }

        .sidebar-logout-btn:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
            transform: translateY(-1px);
        }

        .sidebar-logout-btn:hover i {
            transform: translateX(3px);
        }

        .sidebar-logout-btn:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .sidebar-logout-btn:focus-visible {
            outline: 2px solid #fecaca;
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            .sidebar-logout-btn, .sidebar-logout-btn i { transition: none; }
            .sidebar-logout-btn:hover, .sidebar-logout-btn:hover i { transform: none; }
        }

        .footer-right {
            text-align: center;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            letter-spacing: 0.5px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        /* ==========================================================================
        ÁREA PRINCIPAL (conteúdo de cada página)
        ========================================================================== */
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
            /* Só o suficiente pra limpar os botões flutuantes de notificações
               e acessibilidade (top:20 + 46px de altura = termina em y:66px)
               + uma folga pequena — valores maiores deixavam uma faixa em
               branco entre os botões e o título da página. */
            padding-top: 20px;
        }

        .main.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* Primeira linha de cada página: título alinhado aos botões
           flutuantes de acessibilidade e notificações (centro em y:43). */
        .main > .header:first-child,
        .main > .top-bar:first-child {
            align-items: flex-start;
            padding-right: 132px;
        }

        .main > .header:first-child :is(h1, h2),
        .main > .top-bar:first-child :is(h1, h2) {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 46px;
            margin-top: 0;
        }

        /* Com o menu recolhido, o botão de abrir a sidebar (x:18→52) fica
           sobre a margem esquerda — abre espaço para ele. */
        .main.expanded > .header:first-child,
        .main.expanded > .top-bar:first-child {
            padding-left: 44px;
        }

        @media (max-width: 992px) {
            .main {
                padding: 24px;
                padding-top: 20px;
            }
        }

        @media (max-width: 768px) {
            .main { padding: 18px; padding-top: 20px; }
        }

        /* ==========================================================================
        MODO ESCURO — mesma paleta verde do projeto para navbar/drawer.
        ========================================================================== */
        body.dark-theme {
            --bg-principal: #0a1810;
            --text-principal: #f1f9f4;
            --text-secundario: #9db8a8;
            --card-bg: #12251a;
            --card-border: 1px solid color-mix(in srgb, var(--verde-secundario, #16a765) 16%, transparent);
            --card-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);

            --navbar-bg: #1e293b;
            --sidebar-bg: #0c1b12;
            --surface-border: rgba(255, 255, 255, 0.12);
            --surface-muted-bg: #0a1810;
            --surface-muted-border: #2a4d38;

            --menu-text: #9db8a8;
            --menu-text-hover: var(--verde-secundario, #16a765);
            --menu-hover-bg: color-mix(in srgb, var(--verde-secundario, #16a765) 8%, transparent);
            --menu-active-bg: color-mix(in srgb, var(--verde-secundario, #16a765) 14%, transparent);
            --menu-active-text: #ffffff;
            --menu-active-accent: var(--verde-secundario, #16a765);

            --panel-bg: #12251a;
            --panel-text: #f1f9f4;
            --panel-item-bg: #0a1810;
            --panel-btn-bg: #1e3a2a;
            --panel-btn-text: #d9ede1;
            --panel-btn-hover: #2a4d38;
        }

        /* ==========================================================================
        BOTÕES FLUTUANTES PADRONIZADOS — acessibilidade, notificações e abrir/
        fechar menu compartilham o mesmo visual (verde da sidebar, ícone
        branco, borda dourada sutil) e o mesmo comportamento de hover/foco.
        O prefixo "body" garante que vençam o CSS do partial de acessibilidade.
        ========================================================================== */
        body .floating-notifications-btn,
        body .sidebar-toggle-btn,
        body .accessibility-trigger:not(.acc-trigger-inline) {
            background: var(--sidebar-bg);
            color: #ffffff;
            border: 1px solid rgba(209, 161, 54, 0.4);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.28);
            backdrop-filter: none;
            transition: transform 0.25s ease, background-color 0.2s ease, color 0.2s ease,
                        border-color 0.2s ease, box-shadow 0.2s ease;
        }

        body .floating-notifications-btn:hover,
        body .sidebar-toggle-btn:hover,
        body .accessibility-trigger:not(.acc-trigger-inline):hover {
            background: #1c5a30;
            color: #ffffff;
            border-color: rgba(209, 161, 54, 0.75);
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.38);
        }

        /* O de acessibilidade mantém seu giro de 45° (partial); os outros dois
           ganham o mesmo "crescer" para a interação ser igual. */
        body .floating-notifications-btn:hover,
        body .sidebar-toggle-btn:hover {
            transform: scale(1.1);
        }

        body .floating-notifications-btn:active,
        body .sidebar-toggle-btn:active {
            transform: scale(0.96);
        }

        body .floating-notifications-btn:focus-visible,
        body .sidebar-toggle-btn:focus-visible,
        body .accessibility-trigger:focus-visible {
            outline: 3px solid var(--verde-ativo, #087a47);
            outline-offset: 3px;
        }

        body .floating-notifications-btn .icon-dot { border-color: var(--sidebar-bg); }

        body.high-contrast .floating-notifications-btn,
        body.high-contrast .sidebar-toggle-btn {
            background: #000000;
            color: #ffff00;
            border: 2px solid #ffff00;
            box-shadow: none;
        }

        body.high-contrast .floating-notifications-btn:hover,
        body.high-contrast .sidebar-toggle-btn:hover {
            background: #ffff00;
            color: #000000;
        }

        body.high-contrast .floating-notifications-btn:hover i,
        body.high-contrast .sidebar-toggle-btn:hover i {
            color: #000000 !important;
        }

        @media (prefers-reduced-motion: reduce) {
            body .floating-notifications-btn,
            body .sidebar-toggle-btn { transition: none; }
            body .floating-notifications-btn:hover,
            body .sidebar-toggle-btn:hover { transform: none; }
        }
        body.dark-theme .user-avatar { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.16); color: #f1f5f9; }
        body.dark-theme .user-profile:hover { background: rgba(255, 255, 255, 0.06); }
        body.dark-theme .user-name { color: #f1f9f4; }
        body.dark-theme .user-role { color: #9db8a8; }
        body.dark-theme .sidebar-search-results { background: var(--card-bg); border-color: var(--surface-muted-border); }
        body.dark-theme .footer-right { color: rgba(255, 255, 255, 0.7); }

        /* ==========================================================================
        SHELL MOBILE — opcional, por página: @@section('mobile-shell', '1').
        No celular (até 767px): header verde com ☰ / logo / 🔔 / ♿, perfil e
        busca no topo do conteúdo, menu lateral como gaveta (com fundo
        escurecido) e navegação inferior fixa. Os botões ☰, 🔔 e ♿ são os
        mesmos de sempre — só mudam de lugar —, então nada perde função.
        Páginas sem a seção continuam exatamente como antes.
        ========================================================================== */
        .mobile-header,
        .mobile-topo,
        .mobile-bottom-nav,
        .mobile-menu-overlay {
            display: none;
        }

        /* Gaveta: até 992px o menu abre POR CIMA do conteúdo. (Algumas telas
           forçavam ".sidebar { left: -260px }" nessa faixa, e aí o ☰ não
           abria mais o menu — com mais especificidade, esta regra vence.) */
        @media (max-width: 992px) {
            body[data-mobile-shell] .sidebar {
                left: -300px;
                width: min(280px, 86vw);
                z-index: 1003;
                box-shadow: none;
            }

            body[data-mobile-shell] .sidebar:not(.collapsed) {
                left: 0;
                box-shadow: 12px 0 32px rgba(0, 0, 0, 0.28);
            }

            body[data-mobile-shell] .main {
                margin-left: 0;
                width: 100%;
            }

            body[data-mobile-shell] .mobile-menu-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.55);
                backdrop-filter: blur(2px);
                z-index: 1002;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.25s ease;
            }

            body[data-mobile-shell].menu-mobile-aberto { overflow: hidden; }
            body[data-mobile-shell].menu-mobile-aberto .mobile-menu-overlay { opacity: 1; pointer-events: auto; }
            /* O ☰ vira o "X" de fechar, por cima da gaveta. */
            body[data-mobile-shell].menu-mobile-aberto .sidebar-toggle-btn { z-index: 1004; }
            /* Com o menu aberto, os botões flutuantes não ficam por cima dele. */
            body[data-mobile-shell].menu-mobile-aberto .floating-notifications-btn,
            body[data-mobile-shell].menu-mobile-aberto .accessibility-trigger,
            body[data-mobile-shell].menu-mobile-aberto .ai-chat-trigger {
                opacity: 0;
                pointer-events: none;
            }
        }

        @media (max-width: 767px) {
            body[data-mobile-shell] {
                --mobile-header-altura: 64px;
                --mobile-nav-altura: 64px;
                padding-top: calc(var(--mobile-header-altura) + env(safe-area-inset-top, 0px));
                padding-bottom: calc(var(--mobile-nav-altura) + env(safe-area-inset-bottom, 0px));
            }

            /* --- Header --- */
            body[data-mobile-shell] .mobile-header {
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: calc(var(--mobile-header-altura) + env(safe-area-inset-top, 0px));
                padding-top: env(safe-area-inset-top, 0px);
                background: linear-gradient(180deg, var(--sidebar-bg), #0e3a1f);
                border-radius: 0 0 18px 18px;
                box-shadow: 0 6px 18px rgba(15, 23, 42, 0.18);
                z-index: 990;
            }

            /* A logo fica centralizada no espaço ENTRE o ☰ (até 54px) e o
               🔔 + ♿ (últimos ~120px) — antes era centralizada na tela com
               largura mínima de 96px e, em 320px ou com zoom, ficava por
               baixo do sino. */
            body[data-mobile-shell] .mobile-header {
                padding-left: 62px;
                padding-right: 128px;
            }

            body[data-mobile-shell] .mobile-header-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                max-width: 100%;
                min-width: 0;
            }

            body[data-mobile-shell] .mobile-header-logo img {
                height: 36px;
                width: auto;
                max-width: 100%;
                object-fit: contain;
                filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.45));
            }

            /* Os três botões de sempre, agora dentro do header (44x44). */
            body[data-mobile-shell] .sidebar-toggle-btn,
            body[data-mobile-shell] .floating-notifications-btn,
            body[data-mobile-shell] .accessibility-trigger:not(.acc-trigger-inline) {
                top: calc(10px + env(safe-area-inset-top, 0px));
                width: 44px;
                height: 44px;
            }

            body[data-mobile-shell] .sidebar-toggle-btn {
                left: 10px;
                border-radius: 12px;
                background: transparent;
                border-color: transparent;
                box-shadow: none;
                color: #ffffff;
                font-size: 1.25rem;
            }

            body[data-mobile-shell] .floating-notifications-btn {
                right: 64px;
                font-size: 1.05rem;
            }

            body[data-mobile-shell] .accessibility-trigger:not(.acc-trigger-inline) {
                right: 12px;
                font-size: 19px;
                border: 2px solid var(--verde-secundario, #16a765);
            }

            body[data-mobile-shell] .floating-notifications-btn,
            body[data-mobile-shell] .accessibility-trigger:not(.acc-trigger-inline) {
                background: rgba(255, 255, 255, 0.06);
                box-shadow: none;
            }

            body[data-mobile-shell] .floating-notifications-btn { border: 1px solid rgba(255, 255, 255, 0.18); }

            /* --- Perfil + busca, no topo do conteúdo --- */
            body[data-mobile-shell] .mobile-topo {
                display: block;
                padding: 14px 16px 6px;
            }

            .mobile-perfil {
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
                padding: 2px 0 12px;
                border-radius: 12px;
            }

            .mobile-perfil-avatar {
                position: relative;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--verde-claro, #e8f5ee);
                color: var(--verde-texto, #0e4a27);
                font-size: 1.15rem;
                border: 2px solid var(--verde-principal, #0e4a27);
            }

            .mobile-perfil-avatar img {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
            }

            .mobile-perfil-avatar .user-online-dot {
                border-color: var(--bg-principal);
                width: 12px;
                height: 12px;
            }

            .mobile-perfil-texto {
                display: flex;
                flex-direction: column;
                min-width: 0;
            }

            .mobile-perfil-nome {
                font-size: 1rem;
                font-weight: 700;
                color: var(--text-principal);
                line-height: 1.3;
                overflow-wrap: anywhere;
            }

            .mobile-perfil-cargo {
                font-size: 0.8125rem;
                font-weight: 600;
                color: var(--verde-texto, #0e4a27);
            }

            .mobile-busca {
                position: relative;
                display: flex;
                align-items: center;
                gap: 10px;
                height: 46px;
                padding: 0 16px;
                background: var(--card-bg);
                border: 1px solid var(--surface-muted-border);
                border-radius: 999px;
                box-shadow: var(--card-shadow);
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .mobile-busca:focus-within {
                border-color: var(--verde-ativo, #087a47);
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--verde-principal, #0e4a27) 15%, transparent);
            }

            .mobile-busca > i {
                color: var(--text-secundario);
                font-size: 0.95rem;
                flex-shrink: 0;
            }

            .mobile-busca input {
                border: none;
                outline: none;
                background: transparent;
                color: var(--text-principal);
                /* 16px: abaixo disso o iPhone dá zoom ao focar o campo. */
                font-size: 16px;
                font-family: inherit;
                width: 100%;
                min-width: 0;
                /* Área de toque = altura toda da barra (antes ~21px). */
                align-self: stretch;
            }

            .mobile-busca input::placeholder { color: var(--text-secundario); }
            .mobile-busca .sidebar-search-results { z-index: 20; }

            /* --- Conteúdo: sem a margem reservada aos botões flutuantes de
               desktop, e com folga embaixo para o robô não cobrir o fim. --- */
            body[data-mobile-shell] .main {
                padding: 8px 16px 88px;
            }

            body[data-mobile-shell] .main > .header:first-child,
            body[data-mobile-shell] .main > .top-bar:first-child,
            body[data-mobile-shell] .main.expanded > .header:first-child,
            body[data-mobile-shell] .main.expanded > .top-bar:first-child {
                padding-right: 0;
                padding-left: 0;
            }

            /* --- Navegação inferior --- */
            body[data-mobile-shell] .mobile-bottom-nav {
                display: flex;
                align-items: stretch;
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                height: calc(var(--mobile-nav-altura) + env(safe-area-inset-bottom, 0px));
                padding: 6px 4px calc(6px + env(safe-area-inset-bottom, 0px));
                background: linear-gradient(0deg, var(--sidebar-bg), #0e3a1f);
                border-radius: 18px 18px 0 0;
                box-shadow: 0 -6px 18px rgba(15, 23, 42, 0.18);
                z-index: 990;
            }

            .mobile-bottom-nav a,
            .mobile-bottom-nav button {
                flex: 1 1 0;
                min-width: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 3px;
                color: #b2cbb9;
                background: none;
                border: none;
                border-radius: 12px;
                font-family: inherit;
                font-size: 0.6875rem;
                font-weight: 600;
                text-decoration: none;
                cursor: pointer;
                transition: color 0.2s ease;
            }

            .mobile-bottom-nav i {
                font-size: 1.1rem;
                padding: 4px 14px;
                border-radius: 999px;
                transition: background-color 0.2s ease, transform 0.2s ease;
            }

            .mobile-bottom-nav span {
                max-width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mobile-bottom-nav a:active i,
            .mobile-bottom-nav button:active i { transform: scale(0.92); }

            /* Ativo: branco + "pílula" verde atrás do ícone (forma, não só cor).
               Antes usava --verde-texto, que no tema claro é #0e4a27 — verde
               escuro sobre a barra verde escura, o "Início" sumia. */
            .mobile-bottom-nav .ativo { color: #ffffff; font-weight: 700; }
            .mobile-bottom-nav .ativo i { background: color-mix(in srgb, var(--verde-secundario, #16a765) 38%, transparent); }

            .mobile-bottom-nav a:focus-visible,
            .mobile-bottom-nav button:focus-visible {
                outline: 2px solid var(--verde-ativo, #087a47);
                outline-offset: -2px;
            }

            /* --- Robô do assistente: acima da navegação inferior --- */
            body[data-mobile-shell] .ai-chat-trigger {
                right: 16px;
                bottom: calc(var(--mobile-nav-altura) + 14px + env(safe-area-inset-bottom, 0px));
                width: 52px;
                height: 52px;
            }

            body[data-mobile-shell] .ai-chat-panel {
                right: 12px;
                bottom: calc(var(--mobile-nav-altura) + 76px + env(safe-area-inset-bottom, 0px));
                width: calc(100vw - 24px);
                height: min(480px, calc(100vh - 230px));
            }

            /* --- Modo escuro / alto contraste --- */
            body.dark-theme .mobile-perfil-avatar { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); color: var(--verde-texto, #0e4a27); }
            body.dark-theme .mobile-perfil-cargo { color: var(--verde-texto, #0e4a27); }

            body.high-contrast .mobile-header,
            body.high-contrast .mobile-bottom-nav {
                background: #000000 !important;
                box-shadow: none !important;
            }
            body.high-contrast .mobile-header { border-bottom: 2px solid #ffff00; }
            body.high-contrast .mobile-bottom-nav { border-top: 2px solid #ffff00; }
            body.high-contrast .mobile-bottom-nav a,
            body.high-contrast .mobile-bottom-nav button,
            body.high-contrast .mobile-perfil-nome,
            body.high-contrast .mobile-perfil-cargo { color: #ffff00 !important; }
            body.high-contrast .mobile-bottom-nav .ativo i { background: #ffff00; color: #000000; }
            body.high-contrast .mobile-busca,
            body.high-contrast .mobile-perfil-avatar { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
            body.high-contrast .mobile-busca input { color: #ffff00; }
        }

        @media (prefers-reduced-motion: reduce) {
            body[data-mobile-shell] .mobile-menu-overlay,
            .mobile-bottom-nav i { transition: none; }
        }

    </style>

    @include('partials.paleta-verde')
    @stack('styles')

    {{-- RESPONSIVIDADE GLOBAL das páginas internas — rede de segurança que
         vale pra toda tela que usa este layout (vem depois dos estilos de
         cada página de propósito). Achado pela auditoria em 5 larguras:
         cards de grid "esticavam" até a largura da tabela de dentro e
         vazavam da tela no celular. --}}
    <style>
        /* Itens de grid/flex podem encolher (o padrão "min-width: auto"
           faz o card crescer até o conteúdo mais largo). */
        .main [class*="grid"] > *,
        .main [class*="-row"] > *,
        .main .dashboard-card,
        .main .card,
        .main .box {
            min-width: 0;
        }

        .main img,
        .main video,
        .main canvas,
        .main svg:not(.icone-fixo) {
            max-width: 100%;
        }

        .main img,
        .main video { height: auto; }

        /* Cabeçalhos de card (título + filtro/botão) quebram linha em vez
           de empurrar o card pra fora. */
        .main .card-header-flex,
        .main .card-header,
        .main .box-header {
            flex-wrap: wrap;
            row-gap: 10px;
        }

        /* Card com tabela mais larga que ele (em qualquer largura de tela):
           a tabela rola por dentro do card em vez de ser cortada. */
        .main .dashboard-card:has(table),
        .main .card:has(table),
        .main .box:has(table),
        .main .table-container,
        .main .table-wrapper,
        .main .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .main select,
        .main input:not([type="checkbox"]):not([type="radio"]),
        .main textarea {
            max-width: 100%;
        }

        /* Linhas de rótulo + controles quebram linha em vez de sair da tela
           quando o texto é ampliado (A+) ou com o zoom do sistema — que não
           aciona as media queries das telas. Na largura normal cabem numa
           linha só, como antes. */
        .main .toolbar-ordenar,
        .main .top-bar-actions {
            flex-wrap: wrap;
        }

        /* Algumas telas fixam os botões do topo com flex-shrink:0 — o wrap
           acima só funciona se o grupo puder ficar mais estreito que a soma
           dos botões. */
        .main .top-bar-actions {
            min-width: 0;
            max-width: 100%;
        }

        .main .fazenda-selector-bar-label,
        .main .btn-toggle-ranking {
            white-space: normal;
            max-width: 100%;
        }

        /* Título da página com símbolo: ícone verde alinhado ao texto, igual
           em todas as telas internas (algumas já tinham o próprio estilo). */
        .main :is(h1, h2):has(> .titulo-icone) {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .main :is(h1, h2) > .titulo-icone {
            margin-inline-end: 0;
            font-size: 0.8em;
            color: var(--verde-texto, #0e4a27);
            flex-shrink: 0;
        }

        /* Rodapé da sidebar: Rações, Estoque e Plano ainda traziam um
           .footer-right antigo com texto a 35% (2,8:1 sobre o verde). */
        .sidebar .footer-right { color: rgba(255, 255, 255, 0.7); }

        /* ALTO CONTRASTE — base comum de TODA página interna. Cada tela
           repetia esta base no próprio CSS e as que esqueciam (ex.: detalhe
           do cocho) ficavam com fundo claro + texto amarelo (1,02:1) ou
           títulos escuros sobre preto. Tokens em :where() (especificidade
           zero): a regra de alto contraste da própria página, quando
           existe, continua valendo. */
        :where(body.high-contrast) {
            --bg-principal: #000000;
            --text-principal: #ffff00;
            --text-secundario: #ffff00;
            --card-bg: #000000;
            --surface-muted-bg: #000000;
            --surface-muted-border: #ffff00;
            --surface-border: rgba(255, 255, 0, 0.6);
            background: #000000;
            color: #ffff00;
        }

        /* Campos e botões sem estilo próprio de alto contraste (seletor de
           itens por página, datas do filtro de período, "Filtrar"...)
           ficavam brancos com texto amarelo. Especificidade (0,2,0) de
           propósito: vence o estilo base das telas, mas NUNCA as regras
           "body.high-contrast ..." delas (ex.: filtro ativo em amarelo). */
        :where(body).high-contrast .main :where(input:not([type="checkbox"]):not([type="radio"]):not([type="range"]), select, textarea) {
            background-color: #000000;
            color: #ffff00;
            border: 2px solid #ffff00;
        }

        :where(body).high-contrast .main :where(input, textarea)::placeholder { color: #ffff99; opacity: 1; }

        :where(body).high-contrast .main :where(button:not([role="switch"])) {
            background: #000000;
            color: #ffff00;
            border-color: #ffff00;
        }

        :where(body).high-contrast .main :where(h1, h2, h3, h4, h5, h6) { color: #ffff00; }
        body.high-contrast .sidebar .footer-right { color: #ffff00; }

        /* Campos nativos (ícone do calendário, seta do select, listas) no
           esquema escuro do navegador — senão o ícone preto some no fundo. */
        body.dark-theme .main :is(input, select, textarea),
        body.high-contrast .main :is(input, select, textarea) { color-scheme: dark; }

        @media (max-width: 768px) {
            /* Tabelas: rolagem horizontal só DENTRO da tabela, nunca da
               página inteira. */
            .main table:not(.tabela-fixa) {
                display: block;
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .main table:not(.tabela-fixa) th,
            .main table:not(.tabela-fixa) td {
                white-space: nowrap;
            }

            /* Áreas de toque confortáveis no celular. */
            .main button,
            .main .btn,
            .main a.btn,
            .main select {
                min-height: 40px;
            }

            /* Modais: nunca maiores que a tela; conteúdo longo rola por dentro. */
            .modal-content,
            .modal-box,
            .modal-dialog,
            [class*="modal"][class*="-card"] {
                max-width: calc(100vw - 24px);
                max-height: calc(100dvh - 24px);
                overflow-y: auto;
            }
        }

        /* Celular/tablet DEITADO (largura de desktop, pouca altura): os
           modais usavam 90vh, que conta a barra do navegador e cortava o
           rodapé com os botões. */
        @media (max-height: 560px) {
            .modal-content,
            .modal-box,
            .modal-dialog,
            [class*="modal"][class*="-card"],
            .alertas-modal {
                max-height: calc(100dvh - 16px);
                overflow-y: auto;
            }
        }
    </style>
</head>

@php
    // Shell mobile (header verde, menu em gaveta e navegação inferior no
    // celular) vale para TODAS as páginas internas por padrão — antes era
    // opcional e só o Estoque usava; nas demais o menu lateral ficava aberto
    // por cima do conteúdo no celular. Uma página pode desligar com
    // @@section('sem-mobile-shell', '1').
    if (! $__env->hasSection('mobile-shell') && ! $__env->hasSection('sem-mobile-shell')) {
        $__env->startSection('mobile-shell', '1');
    }
@endphp
<body @yield('body-attrs') @hasSection('mobile-shell') data-mobile-shell @endif>
    @include('partials.a11y-global')

    @hasSection('mobile-shell')
        <!-- ================= HEADER MOBILE (até 767px) =================
             Só o fundo e a logo: ☰, 🔔 e ♿ são os botões globais de
             sempre, reposicionados aqui dentro via CSS. -->
        <header class="mobile-header">
            <a href="{{ route('cadastro.index') }}" class="mobile-header-logo" aria-label="AgriFarm — ir para o Dashboard">
                {{-- width/height = proporção real: reserva o espaço antes de a
                     imagem carregar (sem "pulo" do layout e sem o link com
                     largura 0, que deixava o foco do teclado invisível). --}}
                <img src="{{ asset('agrifarm_oficial.png') }}" alt="AgriFarm" width="1252" height="362">
            </a>
        </header>
        <!-- Fundo escurecido atrás do menu lateral aberto (clicar fecha). -->
        <div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>
    @endif

    <!-- Único botão para abrir/fechar a drawer — sempre no mesmo lugar,
         fora da drawer, então continua clicável mesmo com ela fechada. -->
    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Fechar menu" aria-label="Fechar menu" aria-expanded="true">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- ================= DRAWER LATERAL (verde, global) ================= -->
    <div class="sidebar" id="sidebar" role="navigation" aria-label="Menu principal">
        <div class="sidebar-brand">
            <img src="{{ asset('agrifarm_oficial.png') }}" alt="AgriFarm" class="sidebar-logo" width="1252" height="362">
        </div>

        <a href="{{ route('perfil') }}" class="user-profile" title="Meu perfil">
            <div class="user-avatar">
                @if ($cadastroLogado?->foto_url)
                    <img src="{{ $cadastroLogado->foto_url }}" alt="Foto de perfil" referrerpolicy="no-referrer" onerror="this.remove()" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                @else
                    <i class="fa-solid fa-user-gear"></i>
                @endif
                <span class="user-online-dot" aria-hidden="true" title="Online"></span>
            </div>
            <div class="user-info">
                <span class="user-name">Olá, {{ session('usuario_nome') }}</span>
                <span class="user-role">Administrador</span>
            </div>
        </a>

        <div class="sidebar-search" role="search">
            <i class="fa-solid fa-magnifying-glass sidebar-search-icon"></i>
            <input type="text" id="sidebarSearchInput" class="sidebar-search-input" placeholder="Pesquisar páginas, gráficos..." autocomplete="off" aria-label="Pesquisar páginas e gráficos do sistema">
            <div class="sidebar-search-results" id="sidebarSearchResults"></div>
        </div>

        <div class="menu">
            <a href="{{ route('cadastro.index') }}" class="{{ request()->routeIs('cadastro.index') ? 'active' : '' }}" @if (request()->routeIs('cadastro.index')) aria-current="page" @endif>
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard Avançado</span>
            </a>
            <a href="{{ route('fazenda.listar') }}" class="{{ request()->routeIs('fazenda.*') ? 'active' : '' }}" @if (request()->routeIs('fazenda.*')) aria-current="page" @endif>
                <i class="fa-solid fa-location-dot"></i>
                <span>Gestão de Fazendas</span>
            </a>
            <a href="{{ route('lote.listar') }}" class="{{ request()->routeIs('lote.*') ? 'active' : '' }}" @if (request()->routeIs('lote.*')) aria-current="page" @endif>
                <i class="fa-solid fa-layer-group"></i>
                <span>Gestão de Lotes</span>
            </a>
            <a href="{{ route('ordenha.listar') }}" class="{{ request()->routeIs('ordenha.*') ? 'active' : '' }}" @if (request()->routeIs('ordenha.*')) aria-current="page" @endif>
                <i class="fa-solid fa-droplet"></i>
                <span>Ordenha e Leite</span>
            </a>
            <a href="{{ route('cocho.listar') }}" class="{{ request()->routeIs('cocho.*') ? 'active' : '' }}" @if (request()->routeIs('cocho.*')) aria-current="page" @endif>
                <i class="fa-solid fa-cube"></i>
                <span>Gestão de Cochos</span>
            </a>
            <a href="{{ route('racao.listar') }}" class="{{ request()->routeIs('racao.*') ? 'active' : '' }}" @if (request()->routeIs('racao.*')) aria-current="page" @endif>
                <i class="fa-solid fa-wheat-awn"></i>
                <span>Gestão de Rações</span>
            </a>
            <a href="{{ route('estoque.listar') }}" class="{{ request()->routeIs('estoque.*') ? 'active' : '' }}" @if (request()->routeIs('estoque.*')) aria-current="page" @endif>
                <i class="fa-solid fa-box"></i>
                <span>Nível de Estoque</span>
            </a>
            <a href="{{ route('plano.listar') }}" class="{{ request()->routeIs('plano.*') ? 'active' : '' }}" @if (request()->routeIs('plano.*')) aria-current="page" @endif>
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Plano Alimentar</span>
            </a>
            <a href="{{ route('relatorio.listar') }}" class="{{ request()->routeIs('relatorio.*') ? 'active' : '' }}" @if (request()->routeIs('relatorio.*')) aria-current="page" @endif>
                <i class="fa-regular fa-file-lines"></i>
                <span>Relatório Avançado</span>
            </a>
            <a href="{{ route('perfil') }}" class="{{ request()->routeIs('perfil*') ? 'active' : '' }}" @if (request()->routeIs('perfil*')) aria-current="page" @endif>
                <i class="fa-solid fa-user-gear"></i>
                <span>Meu Perfil</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('logout') }}" class="sidebar-logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Sair</span>
            </a>
            <div class="footer-right">© 2026 AgriFarm</div>
        </div>
    </div>

    <!-- ================= BOTÃO FLUTUANTE (sem navbar) ================= -->
    <button type="button" class="floating-notifications-btn" id="notificationsBtn" title="Notificações" aria-label="Notificações" aria-haspopup="dialog">
        <i class="fa-solid fa-bell"></i>
        <span class="icon-dot" id="notifBadge" aria-hidden="true"></span>
    </button>

    <!-- ================= MODAL DE ALERTAS DO SISTEMA ================= -->
    <div class="alertas-modal-overlay" id="alertasModalOverlay"></div>
    <div class="alertas-modal" id="alertasModal" role="dialog" aria-modal="true" aria-labelledby="alertasModalTitulo">
        <div class="modal-header">
            <div class="alertas-modal-header-info">
                <div class="alertas-modal-header-icone"><i class="fa-solid fa-bell" aria-hidden="true"></i></div>
                <div>
                    <h3 id="alertasModalTitulo">Alertas do Sistema</h3>
                    <p class="alertas-modal-subtitulo" id="alertasModalSubtitulo">Carregando...</p>
                </div>
            </div>
            <button type="button" class="modal-close" id="alertasModalClose" aria-label="Fechar">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="modal-body" id="alertasModalBody">
            <div class="alertas-modal-loading">
                <i class="fa-solid fa-circle-notch fa-spin" aria-hidden="true"></i>
                Carregando alertas...
            </div>
        </div>
    </div>

    @hasSection('mobile-shell')
        <!-- ================= PERFIL + BUSCA (celular) ================= -->
        <div class="mobile-topo" role="region" aria-label="Perfil e busca">
            <a href="{{ route('perfil') }}" class="mobile-perfil" title="Meu perfil">
                <span class="mobile-perfil-avatar">
                    @if ($cadastroLogado?->foto_url)
                        <img src="{{ $cadastroLogado->foto_url }}" alt="Foto de perfil" referrerpolicy="no-referrer" onerror="this.remove()">
                    @else
                        <i class="fa-solid fa-user" aria-hidden="true"></i>
                    @endif
                    <span class="user-online-dot" aria-hidden="true"></span>
                </span>
                <span class="mobile-perfil-texto">
                    <span class="mobile-perfil-nome">Olá, {{ session('usuario_nome') }}</span>
                    <span class="mobile-perfil-cargo">Administrador</span>
                </span>
            </a>

            <div class="mobile-busca" role="search">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input type="text" id="mobileSearchInput" placeholder="Pesquisar páginas, gráficos..." autocomplete="off" aria-label="Pesquisar páginas e gráficos do sistema">
                <div class="sidebar-search-results" id="mobileSearchResults"></div>
            </div>
        </div>
    @endif

    <!-- ================= CONTEÚDO DA PÁGINA ================= -->
    @yield('content')

    @hasSection('mobile-shell')
        <!-- ================= NAVEGAÇÃO INFERIOR (celular) =================
             "Mais" abre o menu lateral completo (todas as páginas + Sair). -->
        <nav class="mobile-bottom-nav" aria-label="Navegação principal">
            <a href="{{ route('cadastro.index') }}" class="{{ request()->routeIs('cadastro.index') ? 'ativo' : '' }}" @if (request()->routeIs('cadastro.index')) aria-current="page" @endif>
                <i class="fa-solid fa-house" aria-hidden="true"></i><span>Início</span>
            </a>
            <a href="{{ route('fazenda.listar') }}" class="{{ request()->routeIs('fazenda.*') ? 'ativo' : '' }}" @if (request()->routeIs('fazenda.*')) aria-current="page" @endif>
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>Fazendas</span>
            </a>
            <a href="{{ route('lote.listar') }}" class="{{ request()->routeIs('lote.*') ? 'ativo' : '' }}" @if (request()->routeIs('lote.*')) aria-current="page" @endif>
                <i class="fa-solid fa-layer-group" aria-hidden="true"></i><span>Lotes</span>
            </a>
            <a href="{{ route('cocho.listar') }}" class="{{ request()->routeIs('cocho.*') ? 'ativo' : '' }}" @if (request()->routeIs('cocho.*')) aria-current="page" @endif>
                <i class="fa-solid fa-cube" aria-hidden="true"></i><span>Cochos</span>
            </a>
            {{-- Rações também fica ativo no Nível de Estoque (estoque = das rações). --}}
            <a href="{{ route('racao.listar') }}" class="{{ request()->routeIs('racao.*', 'estoque.*') ? 'ativo' : '' }}" @if (request()->routeIs('racao.*')) aria-current="page" @endif>
                <i class="fa-solid fa-wheat-awn" aria-hidden="true"></i><span>Rações</span>
            </a>
            <button type="button" id="mobileNavMais" aria-controls="sidebar" aria-label="Mais opções: abrir o menu completo">
                <i class="fa-solid fa-ellipsis" aria-hidden="true"></i><span>Mais</span>
            </button>
        </nav>
    @endif

    @include('partials.acessibilidade')
    @include('partials.assistente-ia')

    <!-- WIDGET VLIBRAS -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        if (window.VLibras) new window.VLibras.Widget('https://vlibras.gov.br/app'); // CDN do governo fora do ar/bloqueado: segue sem o widget
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* --- Abrir/fechar a drawer: um único botão, sempre no mesmo
               lugar (fora da drawer, então continua clicável com ela
               fechada), que alterna o ícone (☰ / X) conforme o estado. --- */
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main');
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const sidebarToggleIcon = sidebarToggleBtn ? sidebarToggleBtn.querySelector('i') : null;

            const atualizarBotaoToggle = function (aberta) {
                if (!sidebarToggleBtn) return;
                sidebarToggleBtn.setAttribute('aria-expanded', aberta ? 'true' : 'false');
                sidebarToggleBtn.setAttribute('title', aberta ? 'Fechar menu' : 'Abrir menu');
                sidebarToggleBtn.setAttribute('aria-label', aberta ? 'Fechar menu' : 'Abrir menu');
                if (sidebarToggleIcon) {
                    sidebarToggleIcon.className = aberta ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
                }
                // "Mais" (navegação inferior do celular) abre o mesmo menu.
                const mais = document.getElementById('mobileNavMais');
                if (mais) mais.setAttribute('aria-expanded', aberta ? 'true' : 'false');
            };

            /* Shell mobile (páginas com @@section('mobile-shell')): até 992px o
               menu é uma gaveta por cima do conteúdo, com fundo escurecido,
               e começa fechado. */
            const shellMobile = document.body.hasAttribute('data-mobile-shell');
            const mqGaveta = window.matchMedia('(max-width: 992px)');
            const modoGaveta = function () { return shellMobile && mqGaveta.matches; };
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

            // Drawer fechada fica fora da tela: "inert" tira os links dela da
            // ordem do Tab e do leitor de tela (antes o foco "sumia" nela).
            let origemFocoSidebar = null;

            const fecharSidebar = function () {
                const focoDentro = sidebar.contains(document.activeElement);
                sidebar.classList.add('collapsed');
                sidebar.inert = true;
                if (mainContent) mainContent.classList.add('expanded');
                atualizarBotaoToggle(false);
                document.body.classList.remove('menu-mobile-aberto');
                if (focoDentro) (origemFocoSidebar && document.contains(origemFocoSidebar) ? origemFocoSidebar : sidebarToggleBtn)?.focus();
            };

            const abrirSidebar = function () {
                sidebar.classList.remove('collapsed');
                sidebar.inert = false;
                if (mainContent) mainContent.classList.remove('expanded');
                atualizarBotaoToggle(true);
                if (modoGaveta()) {
                    document.body.classList.add('menu-mobile-aberto');
                    // Gaveta por cima do conteúdo (celular/tablet): o foco
                    // entra nela e volta para quem abriu ao fechar.
                    origemFocoSidebar = document.activeElement;
                    const primeiroLink = sidebar.querySelector('.sidebar-search-input, .menu a');
                    if (primeiroLink) setTimeout(function () { primeiroLink.focus({ preventScroll: true }); }, 50);
                }
            };

            if (sidebar && modoGaveta()) fecharSidebar();

            if (sidebar && shellMobile) {
                // Girou a tela / redimensionou: no celular fecha a gaveta; de
                // volta ao desktop, o menu volta aberto como sempre foi.
                const aoMudarTamanho = function () {
                    if (mqGaveta.matches) fecharSidebar(); else abrirSidebar();
                };
                if (mqGaveta.addEventListener) mqGaveta.addEventListener('change', aoMudarTamanho);
                else if (mqGaveta.addListener) mqGaveta.addListener(aoMudarTamanho);

                if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', fecharSidebar);

                const mobileNavMais = document.getElementById('mobileNavMais');
                if (mobileNavMais) mobileNavMais.addEventListener('click', abrirSidebar);

                // Tocar num item do menu fecha a gaveta (a página navega em seguida).
                sidebar.querySelectorAll('.menu a').forEach(function (link) {
                    link.addEventListener('click', function () { if (modoGaveta()) fecharSidebar(); });
                });
            }

            if (sidebar && sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function () {
                    if (sidebar.classList.contains('collapsed')) {
                        abrirSidebar();
                    } else {
                        fecharSidebar();
                    }
                });
            }

            // Fase de captura: registra se havia um diálogo aberto ANTES de
            // qualquer script da página fechá-lo com este mesmo Esc.
            let dialogoAbertoNoEsc = false;
            window.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                dialogoAbertoNoEsc = !!document.querySelector('.modal.active, .modal-m.active, .accessibility-modal.open, .alertas-modal.open, .ai-chat-panel:not([hidden])');
            }, true);

            window.addEventListener('keydown', function (e) {
                // Esc dentro de um modal fecha só o modal (antes também
                // recolhia o menu lateral no desktop).
                if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('collapsed') && !dialogoAbertoNoEsc) {
                    fecharSidebar();
                }

                // Gaveta aberta por cima do conteúdo: Tab circula só entre ela
                // e o botão de fechar (não "vaza" para a página escurecida).
                if (e.key === 'Tab' && sidebar && modoGaveta() && !sidebar.classList.contains('collapsed')) {
                    const itens = [sidebarToggleBtn].concat(Array.from(sidebar.querySelectorAll('a[href], button, input'))).filter(function (el) {
                        return el && el.getClientRects().length;
                    });
                    if (!itens.length) return;
                    const primeiro = itens[0];
                    const ultimo = itens[itens.length - 1];
                    if (!itens.includes(document.activeElement)) { e.preventDefault(); primeiro.focus(); }
                    else if (e.shiftKey && document.activeElement === primeiro) { e.preventDefault(); ultimo.focus(); }
                    else if (!e.shiftKey && document.activeElement === ultimo) { e.preventDefault(); primeiro.focus(); }
                }
            });

            /* --- Busca de páginas/itens do sistema, dentro do drawer —
               indexa os próprios links do menu lateral, então nunca fica
               desatualizada em relação às páginas reais. --- */
            // Campo da gaveta + campo do topo no celular (shell mobile):
            // mesma busca, mesmo índice.
            const camposBusca = [
                [document.getElementById('sidebarSearchInput'), document.getElementById('sidebarSearchResults')],
                [document.getElementById('mobileSearchInput'), document.getElementById('mobileSearchResults')],
            ].filter(function (par) { return par[0] && par[1]; });

            if (camposBusca.length) {
                const normalizar = function (texto) {
                    return String(texto).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
                };

                const indicePaginas = Array.from(document.querySelectorAll('.menu a')).map(function (link) {
                    const iconEl = link.querySelector('i');
                    const labelEl = link.querySelector('span');
                    return {
                        label: (labelEl ? labelEl.textContent : link.textContent).trim(),
                        grupo: 'Página',
                        href: link.getAttribute('href'),
                        icon: iconEl ? iconEl.className : 'fa-solid fa-circle',
                        extra: '',
                    };
                });

                /* Gráficos e seções de cada página: abrem a página já rolando
                   até o título (âncora "#id" no próprio título). "extra" são
                   sinônimos que também acham o item. */
                const urlDashboard = '{{ route('cadastro.index') }}';
                const urlRelatorio = '{{ route('relatorio.listar') }}';
                const urlOrdenha   = '{{ route('ordenha.listar') }}';
                const urlPlano     = '{{ route('plano.listar') }}';
                const secao = function (label, grupo, url, ancora, icon, extra) {
                    return { label: label, grupo: grupo, href: url + '#' + ancora, icon: icon, extra: extra || '' };
                };
                const indiceSecoes = [
                    secao('Histórico de Consumo (7 dias)', 'Dashboard Avançado', urlDashboard, 'tituloGraficoBarras', 'fa-solid fa-chart-bar', 'grafico barras consumo semana'),
                    secao('Animais por Tipo', 'Dashboard Avançado', urlDashboard, 'tituloGraficoTipos', 'fa-solid fa-chart-pie', 'grafico pizza rosca rebanho categoria'),
                    secao('Alertas do Sistema', 'Dashboard Avançado', urlDashboard, 'alertasSistema', 'fa-solid fa-bell', 'notificacoes avisos'),

                    secao('Produção de Leite por Dia (7 dias)', 'Ordenha e Leite', urlOrdenha, 'sec-producao-dia', 'fa-solid fa-droplet', 'grafico litros semana'),
                    secao('Evolução da Produção (6 meses)', 'Ordenha e Leite', urlOrdenha, 'sec-evolucao-producao', 'fa-solid fa-chart-line', 'grafico historico leite mensal'),
                    secao('Produção por Lote (mês atual)', 'Ordenha e Leite', urlOrdenha, 'sec-producao-lote', 'fa-solid fa-ranking-star', 'grafico leite lotes'),
                    secao('Ordenhas Registradas por Período', 'Ordenha e Leite', urlOrdenha, 'sec-ordenhas-periodo', 'fa-solid fa-clock-rotate-left', 'historico tabela registros'),

                    secao('Consumo de Ração ao Longo do Tempo', 'Relatório Avançado', urlRelatorio, 'sec-consumo-tempo', 'fa-solid fa-wave-square', 'grafico diario racao'),
                    secao('Consumo e Custo por Lote', 'Relatório Avançado', urlRelatorio, 'sec-consumo-custo-lote', 'fa-solid fa-chart-bar', 'grafico gasto financeiro'),
                    secao('Evolução Histórica (6 meses)', 'Relatório Avançado', urlRelatorio, 'sec-evolucao-historica', 'fa-solid fa-chart-line', 'grafico mensal'),
                    secao('Distribuição de Gastos por Ração', 'Relatório Avançado', urlRelatorio, 'sec-distribuicao-gastos', 'fa-solid fa-chart-pie', 'grafico pizza custo'),
                    secao('Eficiência por Lote', 'Relatório Avançado', urlRelatorio, 'sec-eficiencia-lote', 'fa-solid fa-bullseye', 'grafico desempenho'),
                    secao('Resumo por Categoria', 'Relatório Avançado', urlRelatorio, 'sec-resumo-categoria', 'fa-solid fa-layer-group', 'tipo de animal'),
                    secao('Ranking de Lotes por Desempenho', 'Relatório Avançado', urlRelatorio, 'sec-ranking-lotes', 'fa-solid fa-ranking-star', 'melhores piores lotes'),

                    secao('Consumo por Lote - Plano Diário', 'Plano Alimentar', urlPlano, 'sec-plano-diario', 'fa-solid fa-clipboard-list', 'refeicoes tabela'),
                    secao('Consumo dos Últimos 7 Dias', 'Plano Alimentar', urlPlano, 'sec-consumo-7dias', 'fa-solid fa-chart-bar', 'grafico semana racao'),
                    secao('Resumo Mensal', 'Plano Alimentar', urlPlano, 'sec-resumo-mensal', 'fa-solid fa-calendar-days', 'grafico mes semanas'),
                    secao('Distribuição de Ração', 'Plano Alimentar', urlPlano, 'sec-distribuicao-racao', 'fa-solid fa-chart-pie', 'grafico pizza rosca'),
                    secao('Análise Inteligente', 'Plano Alimentar', urlPlano, 'sec-analise-inteligente', 'fa-solid fa-wand-magic-sparkles', 'ia sugestoes'),
                    secao('Conversão Alimentar por Lote', 'Plano Alimentar', urlPlano, 'sec-conversao-lote', 'fa-solid fa-scale-balanced', 'peso pesagem ganho'),
                    secao('Evolução da Conversão Alimentar', 'Plano Alimentar', urlPlano, 'sec-evolucao-conversao', 'fa-solid fa-chart-line', 'grafico pesagens'),
                ];

                const indiceBusca = indicePaginas.concat(indiceSecoes).map(function (item) {
                    item.busca = normalizar(item.label + ' ' + item.grupo + ' ' + item.extra);
                    return item;
                });

                // Leitor de tela: anuncia quantos resultados apareceram (antes a
                // lista surgia em silêncio) e o campo diz se a lista está aberta.
                const statusBusca = document.createElement('p');
                statusBusca.className = 'a11y-sr-only';
                statusBusca.setAttribute('role', 'status');
                document.body.appendChild(statusBusca);

                camposBusca.forEach(function (par) {
                const sidebarSearchInput = par[0];
                const sidebarSearchResults = par[1];
                sidebarSearchInput.setAttribute('aria-controls', sidebarSearchResults.id);

                const fecharResultados = function () {
                    sidebarSearchResults.classList.remove('open');
                };

                const renderizarResultados = function (itens) {
                    if (!itens.length) {
                        sidebarSearchResults.innerHTML = '<div class="sidebar-search-empty">Nada encontrado</div>';
                    } else {
                        sidebarSearchResults.innerHTML = itens.map(function (item) {
                            return '<a href="' + item.href + '" class="sidebar-search-result-item"><i class="' + item.icon + '" aria-hidden="true"></i>'
                                + '<span class="sidebar-search-result-text"><span>' + item.label + '</span>'
                                + '<small>' + item.grupo + '</small></span></a>';
                        }).join('');
                    }
                    sidebarSearchResults.classList.add('open');
                    statusBusca.textContent = itens.length
                        ? itens.length + (itens.length === 1 ? ' resultado. Use Tab para chegar nele.' : ' resultados. Use Tab para percorrê-los.')
                        : 'Nada encontrado.';
                };

                sidebarSearchInput.addEventListener('input', function () {
                    const palavras = normalizar(sidebarSearchInput.value.trim()).split(/\s+/).filter(Boolean);
                    if (!palavras.length) {
                        fecharResultados();
                        return;
                    }
                    const encontrados = indiceBusca.filter(function (item) {
                        return palavras.every(function (p) { return item.busca.includes(p); });
                    });
                    renderizarResultados(encontrados);
                });

                sidebarSearchInput.addEventListener('focus', function () {
                    if (sidebarSearchInput.value.trim()) {
                        sidebarSearchResults.classList.add('open');
                    }
                });

                sidebarSearchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        const primeiroResultado = sidebarSearchResults.querySelector('.sidebar-search-result-item');
                        if (primeiroResultado) {
                            e.preventDefault();
                            window.location.href = primeiroResultado.getAttribute('href');
                        }
                    } else if (e.key === 'Escape') {
                        fecharResultados();
                        sidebarSearchInput.blur();
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!sidebarSearchResults.contains(e.target) && e.target !== sidebarSearchInput) {
                        fecharResultados();
                    }
                });

                // Clique em resultado da página em que já estamos: só rola.
                sidebarSearchResults.addEventListener('click', function (e) {
                    const link = e.target.closest('a.sidebar-search-result-item');
                    if (!link) return;
                    const destino = new URL(link.href, window.location.href);
                    if (destino.hash && destino.pathname === window.location.pathname) {
                        e.preventDefault();
                        history.replaceState(null, '', destino.pathname + destino.search + destino.hash);
                        irParaSecao(destino.hash);
                        fecharResultados();
                    }
                });
                });
            }

            /* Rola até a seção pedida em "#id" e a destaca por um instante. */
            function irParaSecao(hash) {
                if (!hash || hash.length < 2) return;
                let alvo = null;
                try { alvo = document.getElementById(decodeURIComponent(hash.slice(1))); } catch (e) { return; }
                if (!alvo) return;
                const cartao = alvo.closest('.box, .card-grafico, .dashboard-card') || alvo;
                cartao.scrollIntoView({ behavior: 'smooth', block: 'center' });
                cartao.classList.add('busca-destaque');
                setTimeout(function () { cartao.classList.remove('busca-destaque'); }, 2400);
            }
            window.addEventListener('load', function () {
                // Espera um pouco: os gráficos ainda estão montando e mudam a altura.
                setTimeout(function () { irParaSecao(window.location.hash); }, 350);
            });
            window.addEventListener('hashchange', function () { irParaSecao(window.location.hash); });

            /* --- Alertas do sistema: sino de notificações abre um modal
               (em vez de levar ao Dashboard) e mostra um contador vermelho
               quando existe alerta novo — mesmos alertas reais calculados
               no back-end (estoque baixo, cocho vazio, etc.), disponíveis
               em qualquer página via /alertas/resumo. --- */
            const notificationsBtn = document.getElementById('notificationsBtn');
            const notifBadge = document.getElementById('notifBadge');
            const alertasModal = document.getElementById('alertasModal');
            const alertasModalOverlay = document.getElementById('alertasModalOverlay');
            const alertasModalClose = document.getElementById('alertasModalClose');
            const alertasModalBody = document.getElementById('alertasModalBody');
            const alertasModalSubtitulo = document.getElementById('alertasModalSubtitulo');

            const fecharAlertasModal = function () {
                if (!alertasModal) return;
                alertasModal.classList.remove('open');
                if (alertasModalOverlay) alertasModalOverlay.classList.remove('active');
            };

            const abrirAlertasModal = function () {
                if (!alertasModal) return;
                alertasModal.classList.add('open');
                if (alertasModalOverlay) alertasModalOverlay.classList.add('active');
            };

            // Título/descrição do alerta reaproveitam nomes cadastrados pelo
            // próprio usuário (ração, cocho) — escapa antes de injetar no
            // innerHTML pra nunca interpretar HTML/script vindo desses nomes.
            const escaparHtml = function (texto) {
                const div = document.createElement('div');
                div.textContent = String(texto);
                return div.innerHTML;
            };

            const renderizarAlertasModal = function (alertas) {
                if (!alertasModalBody) return;

                if (!alertas.length) {
                    alertasModalBody.innerHTML = '<div class="alertas-modal-empty">'
                        + '<i class="fa-solid fa-check" aria-hidden="true"></i>'
                        + '<div><strong>Tudo em dia</strong><br>Nenhum alerta no momento.</div>'
                        + '</div>';
                    if (alertasModalSubtitulo) alertasModalSubtitulo.textContent = 'Tudo em dia por aqui';
                    return;
                }

                if (alertasModalSubtitulo) {
                    alertasModalSubtitulo.textContent = alertas.length === 1
                        ? '1 alerta ativo'
                        : alertas.length + ' alertas ativos';
                }

                alertasModalBody.innerHTML = alertas.map(function (alerta) {
                    const critico = alerta.prioridade === 'critico' || alerta.prioridade === 'alta';
                    const classe = critico ? 'critico' : 'baixo';
                    const icone = critico ? 'fa-triangle-exclamation' : 'fa-circle-exclamation';
                    const rotulo = critico ? 'Crítico' : 'Atenção';
                    return '<div class="alertas-modal-item ' + classe + '">'
                        + '<div class="alertas-modal-item-icone"><i class="fa-solid ' + icone + '" aria-hidden="true"></i></div>'
                        + '<div class="alertas-modal-item-corpo">'
                        + '<div class="alertas-modal-item-topo">'
                        + '<h4>' + escaparHtml(alerta.titulo) + '</h4>'
                        + '<span class="alertas-modal-item-badge">' + rotulo + '</span>'
                        + '</div>'
                        + '<p>' + escaparHtml(alerta.descricao) + '</p>'
                        + '</div>'
                        + '</div>';
                }).join('');
            };

            if (notificationsBtn) {
                // A fazenda selecionada fica salva em sessão no back-end
                // (ver App\Http\Controllers\Concerns\ResolveFazendaSelecionada)
                // — então o sino já mostra os alertas certos em qualquer
                // página, mesmo sem "?fazenda_id=" na URL atual. Repassamos
                // aqui só quando a própria URL já tiver o parâmetro (ex.:
                // acabou de trocar no seletor desta página), pra refletir a
                // troca sem esperar o próximo carregamento de página.
                const fazendaIdAtual = new URLSearchParams(window.location.search).get('fazenda_id');
                const urlResumo = '{{ route('alertas.resumo') }}' + (fazendaIdAtual ? ('?fazenda_id=' + encodeURIComponent(fazendaIdAtual)) : '');

                fetch(urlResumo, { headers: { 'Accept': 'application/json' } })
                    .then(function (resposta) { return resposta.ok ? resposta.json() : null; })
                    .then(function (dados) {
                        if (!dados) return;

                        renderizarAlertasModal(dados.alertas || []);

                        if (notifBadge && dados.count > 0) {
                            notifBadge.textContent = dados.count > 9 ? '9+' : String(dados.count);
                            notifBadge.classList.add('show');
                            // O número vermelho também no nome do botão — antes o
                            // leitor de tela dizia só "Notificações".
                            const rotuloSino = 'Notificações: ' + dados.count + (dados.count === 1 ? ' alerta ativo' : ' alertas ativos');
                            notificationsBtn.setAttribute('aria-label', rotuloSino);
                            notificationsBtn.setAttribute('title', rotuloSino);
                        }
                    })
                    .catch(function () {
                        if (alertasModalSubtitulo) alertasModalSubtitulo.textContent = 'Erro ao carregar';
                        if (alertasModalBody) {
                            alertasModalBody.innerHTML = '<div class="alertas-modal-empty erro">'
                                + '<i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>'
                                + '<div>Não foi possível carregar os alertas agora.</div>'
                                + '</div>';
                        }
                    });

                notificationsBtn.addEventListener('click', abrirAlertasModal);
            }

            if (alertasModalClose) alertasModalClose.addEventListener('click', fecharAlertasModal);
            if (alertasModalOverlay) alertasModalOverlay.addEventListener('click', fecharAlertasModal);

            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && alertasModal && alertasModal.classList.contains('open')) {
                    fecharAlertasModal();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
