<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | Gestão de Lotes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ==========================================================================
        1. RESET, VARIÁVEIS DE SISTEMA (idêntico ao Dashboard/Fazendas)
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

            --card-verde-bg: #f0fdf4;
            --card-verde-border: #dcfce7;
            --card-verde-icon-bg: #e2fbe8;
            --card-verde-texto: #15803d;

            --navbar-bg: #123f22;
            --sidebar-bg: #123f22;
            --surface-border: rgba(255, 255, 255, 0.15);
            --surface-muted-bg: #f8fafc;
            --surface-muted-border: #cbd5e1;

            --menu-text: #b2cbb9;
            --menu-text-hover: #ffffff;
            --menu-hover-bg: rgba(255, 255, 255, 0.05);
            --menu-active-bg: rgba(255, 255, 255, 0.08);
            --menu-active-text: #4ade80;
            --menu-active-accent: #4ade80;

            --panel-bg: #ffffff;
            --panel-text: #1f2937;
            --panel-item-bg: #f9fafb;
            --panel-btn-bg: #e5e7eb;
            --panel-btn-text: #374151;
            --panel-btn-hover: #d1d5db;
        }

        body {
            background: var(--bg-principal);
            color: var(--text-principal);
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar, .navbar, .main, .lote-row {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, filter 0.25s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease;
        }

        body.acc-modal-open .sidebar,
        body.acc-modal-open .navbar,
        body.acc-modal-open .main {
            filter: blur(6px);
            pointer-events: none;
        }

        /* ==========================================================================
        2. NAVBAR SUPERIOR
        ========================================================================== */
        .navbar {
            height: 70px;
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--surface-border);
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            z-index: 999;
        }

        .navbar-left { display: flex; align-items: center; gap: 22px; min-width: 0; }

        .toggle-menu-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--surface-border);
            color: #4ade80;
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
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .navbar-logo { display: flex; align-items: center; min-width: 0; }

        .navbar-logo img {
            height: 44px;
            width: auto;
            max-width: 220px;
            object-fit: contain;
            display: block;
        }

        .navbar-right { display: flex; align-items: center; gap: 18px; }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            padding: 6px 10px;
            margin: -6px -10px;
            border-radius: 12px;
            cursor: pointer;
        }

        .user-profile:hover { background: rgba(255, 255, 255, 0.08); }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4ade80;
            font-size: 1rem;
            border: 1px solid var(--surface-border);
            flex-shrink: 0;
        }

        .user-info { display: flex; flex-direction: column; }
        .user-name { color: #ffffff; font-size: 0.875rem; font-weight: 600; }
        .user-role { color: #a7f3d0; font-size: 0.6875rem; font-weight: 500; }

        .navbar-divider { width: 1px; height: 28px; background: var(--surface-border); }

        .navbar-logout {
            text-decoration: none;
            color: #fca5a5;
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

        .navbar-logout:hover {
            background: linear-gradient(135deg, #f87171, #dc2626);
            border-color: transparent;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px -6px rgba(220, 38, 38, 0.55);
        }

        .navbar-logout:active { transform: translateY(0); box-shadow: none; }

        .navbar-right .accessibility-trigger {
            background: rgba(255, 255, 255, 0.1);
            color: #4ade80;
            border: 1px solid var(--surface-border);
        }
        .navbar-right .accessibility-trigger:hover { background: rgba(255, 255, 255, 0.2); color: #ffffff; }

        /* ==========================================================================
        3. SIDEBAR LATERAL
        ========================================================================== */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--surface-border);
            box-shadow: 2px 0 12px rgba(15, 23, 42, 0.03);
            color: var(--menu-text);
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            padding: 0 16px 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 998;
        }

        .sidebar.collapsed { left: -260px; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 997;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 992px) {
            .sidebar-overlay.active { display: block; opacity: 1; }
        }

        .menu { display: flex; flex-direction: column; gap: 10px; min-height: 100%; padding-top: 20px; }

        .menu a {
            text-decoration: none;
            color: var(--menu-text);
            padding: 12px 14px;
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

        .menu a:hover { background: var(--menu-hover-bg); color: var(--menu-text-hover); transform: translateX(4px); }
        @keyframes menuIconBounce {
            0% { transform: translateY(0) scale(1); }
            30% { transform: translateY(-9px) scale(1.16); }
            50% { transform: translateY(1px) scale(0.95); }
            70% { transform: translateY(-4px) scale(1.06); }
            100% { transform: translateY(0) scale(1); }
        }

        .menu a:hover i { animation: menuIconBounce 0.55s cubic-bezier(.34,1.56,.64,1); }

        .menu a.active {
            background: var(--menu-active-bg);
            color: var(--menu-active-text);
            font-weight: 700;
            border-left-color: var(--menu-active-accent);
            box-shadow: inset 0 0 0 1px rgba(74, 222, 128, 0.12);
        }

        .menu i { width: 20px; font-size: 1rem; text-align: center; transition: transform 0.18s ease; }

        @media (prefers-reduced-motion: reduce) {
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
        4. ÁREA PRINCIPAL
        ========================================================================== */
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
            padding-top: 90px;
        }

        .main.expanded { margin-left: 0; width: 100%; }

        .header {
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-title h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-principal);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .header-title p { color: var(--text-secundario); font-size: 0.9375rem; }

        .btn-add {
            background: #0f602e;
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
            box-shadow: 0 4px 12px rgba(15, 96, 46, 0.20);
        }

        .btn-add:hover {
            background: #14753a;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(15, 96, 46, 0.35);
        }

        .btn-add:hover i { transform: rotate(90deg); }
        .btn-add i { transition: transform 0.3s ease; }

        .btn-add:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(15, 96, 46, 0.25);
        }

        /* ==========================================================================
        5. FILTROS AVANÇADOS (mesmo padrão do Relatório/Ração/Estoque)
        ========================================================================== */
        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-abrir-filtros {
            background: var(--card-bg);
            color: var(--text-principal);
            border: 1px solid var(--surface-muted-border);
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            transition: background 0.2s, border-color 0.2s;
        }

        .btn-abrir-filtros:hover { border-color: #16a34a; color: #16a34a; }

        .filtros-badge {
            background: #123f22;
            color: #4ade80;
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

        .filtros-ativos {
            display: none;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .filtros-ativos.visivel { display: flex; }

        .filtros-ativos .chip {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--card-bg);
            border: 1px solid var(--surface-muted-border);
            color: var(--text-principal);
            font-size: 0.78125rem;
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

        .filtros-ativos .chip button:hover { background: #fee2e2; color: #dc2626; }

        .filtros-ativos .btn-limpar-tudo {
            background: none;
            border: none;
            color: #dc2626;
            font-size: 0.78125rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
        }

        .filtros-form { display: flex; flex-direction: column; gap: 16px; }

        .filtros-form .form-group { display: flex; flex-direction: column; gap: 6px; }

        .filtros-form .form-group label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-secundario);
        }

        .filtros-form select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            font-size: 0.875rem;
            color: var(--text-principal);
            background-color: var(--surface-muted-bg);
            outline: none;
            cursor: pointer;
        }

        .filtros-form select:focus { border-color: #16a34a; }

        .filtros-modal-footer { display: flex; gap: 12px; margin-top: 10px; }

        .btn-aplicar {
            flex: 1;
            background: #117943;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-aplicar:hover { background: #0d6135; }

        .btn-limpar {
            flex: 1;
            background: var(--card-bg);
            color: #117943;
            border: 1px solid var(--surface-muted-border);
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-limpar:hover { background: var(--surface-muted-bg); }

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
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        body.dark-theme .alerta-erro { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: rgba(74, 222, 128, 0.12); border-color: rgba(74, 222, 128, 0.3); color: #4ade80; }

        body.high-contrast .alerta-erro,
        body.high-contrast .alerta-sucesso {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* ==========================================================================
        6. LISTAGEM DE LOTES (barra de estatísticas + linhas expansíveis)
        ========================================================================== */
        .lotes-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 16px;
        }

        .stat-pills { display: flex; flex-wrap: wrap; gap: 12px; }

        .stat-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 14px;
            padding: 12px 18px;
        }

        .stat-pill-icone {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.9375rem;
            flex-shrink: 0;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        .stat-pill-total .stat-pill-icone {
            background: linear-gradient(145deg, #60a5fa, #2563eb);
            box-shadow: 0 6px 13px -4px rgba(37, 99, 235, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }
        .stat-pill-ativos .stat-pill-icone {
            background: linear-gradient(145deg, #4ade80, #059669);
            box-shadow: 0 6px 13px -4px rgba(5, 150, 105, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }
        .stat-pill-inativos .stat-pill-icone {
            background: linear-gradient(145deg, #fb7185, #e11d48);
            box-shadow: 0 6px 13px -4px rgba(225, 29, 72, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        .stat-pill-valor { font-size: 1.25rem; font-weight: 700; color: var(--text-principal); line-height: 1.1; }
        .stat-pill-label { font-size: 0.75rem; color: var(--text-secundario); font-weight: 600; }

        .toolbar-ordenar { display: flex; align-items: center; gap: 8px; }

        .toolbar-ordenar label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-secundario);
            white-space: nowrap;
        }

        .toolbar-ordenar select {
            padding: 9px 12px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 8px;
            background: var(--surface-muted-bg);
            color: var(--text-principal);
            font-size: 0.8125rem;
            cursor: pointer;
        }

        .lotes-lista {
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 16px;
            /* Sem "overflow: hidden" aqui de propósito — ele cortava o menu
               "⋮" (Editar/Excluir), que é position:absolute e abre pra
               baixo da linha. As bordas arredondadas ficam por conta das
               regras abaixo, aplicadas só na primeira/última linha. */
        }

        .lote-row { border-bottom: 1px solid var(--surface-muted-border); transition: background 0.15s ease; }
        .lote-row:last-child { border-bottom: none; }
        .lote-row:first-child { border-radius: 16px 16px 0 0; }
        .lote-row:last-child { border-radius: 0 0 16px 16px; }
        .lote-row:hover { background: var(--surface-muted-bg); }

        .lote-row-main {
            display: flex;
            align-items: center;
            gap: 22px;
            padding: 16px 20px;
            flex-wrap: wrap;
        }

        .lote-row-id { display: flex; align-items: center; gap: 14px; min-width: 200px; flex: 1.4 1 200px; }

        /* Cor de fundo vem por espécie (ver $iconesPorAnimal no PHP, mesma
           paleta usada em Rações/Estoque/partials/lote-consumo-linhas) —
           aqui só o formato/tamanho, comuns a todas. */
        .icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.0625rem;
            flex-shrink: 0;
        }

        .lote-row-nome { color: var(--text-principal); font-size: 0.9375rem; font-weight: 700; overflow-wrap: break-word; }

        .lote-row-fazenda {
            color: var(--text-secundario);
            font-size: 0.78125rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .lote-row-fazenda::before {
            content: '\f3c5';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.6875rem;
            color: var(--card-verde-texto);
            opacity: 0.7;
        }

        .lote-row-col {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 100px;
        }

        .lote-row-col-label {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secundario);
            font-weight: 700;
        }

        .lote-row-col-valor { font-size: 0.90625rem; color: var(--text-principal); font-weight: 600; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.71875rem;
            font-weight: 700;
            width: fit-content;
        }

        .status-badge.status-ativo { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .status-badge.status-inativo { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-badge.status-encerrado { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        body.dark-theme .status-badge.status-ativo { background: rgba(74, 222, 128, 0.12); border-color: rgba(74, 222, 128, 0.25); color: #4ade80; }
        body.dark-theme .status-badge.status-inativo { background: rgba(251, 191, 36, 0.12); border-color: rgba(251, 191, 36, 0.25); color: #fbbf24; }
        body.dark-theme .status-badge.status-encerrado { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }

        .lote-row-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

        .btn-ver-detalhes {
            background: var(--card-bg);
            color: var(--text-principal);
            border: 1px solid var(--surface-muted-border);
            padding: 9px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8125rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-ver-detalhes:hover { border-color: #16a34a; color: #16a34a; }
        .btn-ver-detalhes i { transition: transform 0.2s ease; font-size: 0.6875rem; }
        .btn-ver-detalhes.aberto i { transform: rotate(180deg); }

        .lote-row-menu { position: relative; }

        .btn-menu-lote {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-secundario);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-menu-lote:hover { background: var(--surface-muted-bg); }

        .lote-row-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            border-radius: 10px;
            padding: 6px;
            min-width: 150px;
            z-index: 20;
        }

        .lote-row-dropdown.aberto { display: block; }

        .lote-row-dropdown button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            padding: 9px 10px;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-principal);
            cursor: pointer;
            text-align: left;
        }

        .lote-row-dropdown button:hover { background: var(--surface-muted-bg); }
        .lote-row-dropdown button.excluir { color: #dc2626; }
        .lote-row-dropdown button:disabled { opacity: 0.45; cursor: not-allowed; }
        .lote-row-dropdown button:disabled:hover { background: none; }

        .lote-row-detalhes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 10px;
            padding: 16px 18px 18px 76px;
            background: var(--surface-muted-bg);
            border-top: 1px dashed var(--surface-muted-border);
            animation: detalhesLoteAparecer 0.2s ease;
        }

        @keyframes detalhesLoteAparecer {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* O atributo HTML "hidden" só some por padrão porque o navegador
           aplica "display:none" via folha de estilo interna — como
           ".lote-row-detalhes" acima já define "display:grid" com mais
           especificidade, ela vencia o "hidden" e o painel ficava visível
           mesmo fechado. Esta regra devolve a prioridade pro atributo. */
        .lote-row-detalhes[hidden] {
            display: none;
        }

        /* Acompanha o arredondamento da última linha (agora que a lista não
           usa mais "overflow: hidden") quando o painel de detalhes dela
           está aberto. */
        .lote-row:last-child .lote-row-detalhes { border-radius: 0 0 16px 16px; }

        /* Lista simples (sem cartão nem ícone grande por item) — só um
           ícone pequeno e "rótulo: valor" numa linha só. */
        .lote-row-detalhe-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 3px 0;
        }

        .lote-row-detalhe-icone {
            width: 16px;
            color: var(--text-secundario);
            font-size: 0.75rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lote-row-detalhe-texto { display: flex; align-items: baseline; gap: 5px; min-width: 0; }

        .lote-row-detalhes span::after { content: ':'; }

        .lote-row-detalhes span {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
            font-weight: 600;
        }

        .lote-row-detalhes strong {
            font-size: 0.875rem;
            color: var(--text-principal);
            font-weight: 700;
            overflow-wrap: break-word;
        }

        .lotes-paginacao {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
            font-size: 0.8125rem;
            color: var(--text-secundario);
        }

        .paginacao-paginas { display: flex; align-items: center; gap: 6px; }

        .paginacao-paginas button {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            cursor: pointer;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .paginacao-paginas button:hover { background: var(--surface-muted-bg); }
        .paginacao-paginas button.ativo { background: #123f22; border-color: #123f22; color: #ffffff; }
        .paginacao-paginas button:disabled { opacity: 0.4; cursor: not-allowed; }

        .paginacao-itens { display: flex; align-items: center; gap: 8px; }

        .paginacao-itens select {
            padding: 6px 10px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 8px;
            background: var(--surface-muted-bg);
            color: var(--text-principal);
            font-size: 0.8125rem;
            cursor: pointer;
        }

        .cards-empty {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-secundario);
        }

        .cards-empty i {
            display: block;
            font-size: 2rem;
            margin-bottom: 12px;
            color: #cbd5e1;
        }

        /* ==========================================================================
        7. MODAIS (Adicionar / Atualizar Lote)
        ========================================================================== */
        .modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 1200;
        }

        .modal.active { opacity: 1; visibility: visible; }

        .modal-card {
            background: var(--card-bg);
            color: var(--text-principal);
            width: 450px;
            max-width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: modalShow 0.3s ease;
        }

        @keyframes modalShow {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-top { display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; position: relative; }
        .modal-top img { width: 80px; }
        .modal-top p { font-size: 0.9375rem; font-weight: 600; color: var(--text-principal); }

        /* Título em destaque (bold, maior) usado pelo modal de Filtros
           Avançados — os demais modais deste arquivo continuam só com o
           <p> acima, sem <h2>, então esta regra não afeta nenhum deles. */
        .modal-top h2 { font-size: 1.25rem; font-weight: 700; color: var(--text-principal); }
        .modal-top h2 + p { font-size: 0.875rem; font-weight: 400; color: var(--text-secundario); margin-top: 4px; }

        .close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--surface-muted-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            color: var(--text-secundario);
            transition: 0.2s;
        }

        .close:hover {
            background: var(--surface-muted-border);
            color: var(--text-principal);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-principal);
        }

        .input-box { position: relative; margin-bottom: 18px; }

        .input-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secundario);
            z-index: 2;
        }

        .input-box input, .input-box select {
            width: 100%;
            padding: 12px 12px 12px 42px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            background: var(--surface-muted-bg);
            color: var(--text-principal);
            outline: none;
            transition: 0.2s;
            font-size: 0.9375rem;
            appearance: none;
            -webkit-appearance: none;
        }

        .input-box select { cursor: pointer; }

        .input-box input:focus, .input-box select:focus {
            border-color: #16a34a;
            background: var(--card-bg);
        }

        .row { display: flex; gap: 12px; }
        .campo { flex: 1; }

        .dica-campo {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secundario);
            margin: -12px 0 16px;
        }

        .submit-btn {
            width: 100%;
            background: #163a24;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.9375rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(22, 58, 36, 0.15);
        }

        .submit-btn:hover {
            background: #1d4d2f;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(22, 58, 36, 0.35);
        }

        .acao-lote-nome {
            text-align: center;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-principal);
            margin: -8px 0 18px;
        }

        .btn-confirmar-morte {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(185, 28, 28, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-confirmar-morte:hover {
            background: #991b1b;
            box-shadow: 0 10px 25px rgba(185, 28, 28, 0.35);
        }

        .btn-confirmar-doenca {
            background: #b45309;
            box-shadow: 0 4px 12px rgba(180, 83, 9, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-confirmar-doenca:hover {
            background: #92400e;
            box-shadow: 0 10px 25px rgba(180, 83, 9, 0.35);
        }

        .btn-confirmar-recuperacao {
            background: #15803d;
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-confirmar-recuperacao:hover {
            background: #166534;
            box-shadow: 0 10px 25px rgba(21, 128, 61, 0.35);
        }

        /* Deixa claro, dentro do próprio modal, que o retorno só pode ir
           pro lote de origem — não existe escolha de destino aqui. */
        .recuperacao-destino-fixo {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--card-verde-icon-bg);
            color: var(--card-verde-texto);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.8125rem;
            font-weight: 600;
            margin: -8px 0 18px;
        }

        .label-opcional {
            font-size: 0.75rem;
            font-weight: 400;
            color: var(--text-secundario);
            text-transform: none;
            letter-spacing: normal;
        }

        /* ==========================================================================
        LISTA "ANIMAIS DOENTES" (modal Registrar Doença — uma linha por
        animal, cada linha com sua própria doença)
        ========================================================================== */
        #doencaAnimaisLista {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 8px 0 12px;
        }

        .doenca-animal-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .doenca-animal-row .campos-linha {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .doenca-animal-row .input-box { margin-bottom: 0; }

        /* Campo "Outra doença" de cada linha só some/aparece via JS — some
           por padrão pra não ocupar espaço antes do usuário escolher
           "Outra" no select daquela linha. */
        .doenca-outra-linha[hidden] { display: none; }

        .doenca-animal-row .numero-animal {
            flex-shrink: 0;
            width: 26px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--text-secundario);
        }

        .btn-remover-linha-doenca {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border: 1px solid var(--surface-muted-border);
            background: var(--surface-muted-bg);
            color: var(--text-secundario);
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .btn-remover-linha-doenca:hover {
            background: #fee2e2;
            border-color: #dc2626;
            color: #dc2626;
        }

        .btn-adicionar-animal-doente {
            width: 100%;
            background: none;
            border: 1.5px dashed var(--surface-muted-border);
            color: var(--card-verde-texto);
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-adicionar-animal-doente:hover {
            background: var(--card-verde-icon-bg);
            border-color: var(--card-verde-texto);
        }

        .btn-adicionar-animal-doente:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-adicionar-animal-doente:disabled:hover {
            background: none;
            border-color: var(--surface-muted-border);
        }

        /* .dica-campo tem margin-top negativo pensado pra colar embaixo de
           um <input> — logo depois do botão "Adicionar animal" isso puxava
           o texto pra cima e sobrepunha a borda do botão. */
        .btn-adicionar-animal-doente + .dica-campo { margin-top: 8px; }

        /* Mesmo problema: a lista de doenças (recuperação/óbito por
           doença) termina num card com padding próprio, e o dica-campo
           logo depois herdava a mesma margin-top negativa pensada pra
           colar embaixo de um <input> — encostava/sobrepunha o card. */
        #recuperacaoDoencasLista + .dica-campo,
        #morteDoencasLista + .dica-campo {
            margin-top: 8px;
        }

        /* ==========================================================================
        LISTA "QUANTOS DE CADA DOENÇA" — reaproveitada em dois modais:
        "Devolver ao Lote" (quantos recuperaram de cada doença) e
        "Registrar Óbito" quando aplicado a um lote de isolamento (quantos
        morreram de cada doença). Uma linha fixa por doença hoje ativa no
        lote — não dá pra digitar uma doença nova aqui, só a quantidade.
        ========================================================================== */
        #recuperacaoDoencasLista,
        #morteDoencasLista {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 8px 0 4px;
        }

        .doenca-estoque-row {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface-muted-bg);
            border-radius: 10px;
            padding: 10px 14px;
        }

        .doenca-estoque-row .doenca-estoque-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .doenca-estoque-row .doenca-estoque-nome {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-principal);
        }

        .doenca-estoque-row .doenca-estoque-max {
            font-size: 0.71875rem;
            color: var(--text-secundario);
        }

        .doenca-estoque-row input[type="number"] {
            width: 70px;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            font-size: 0.9375rem;
            text-align: center;
        }

        /* ==========================================================================
        HISTÓRICO DE OCORRÊNCIAS (óbitos e doenças, dentro do "Ver detalhes")
        ========================================================================== */
        .lote-historico {
            grid-column: 1 / -1;
            border-top: 1px dashed var(--surface-muted-border);
            margin-top: 4px;
            padding-top: 12px;
        }

        .lote-historico-titulo {
            font-size: 0.71875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secundario);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .lote-historico-item {
            display: flex;
            align-items: baseline;
            gap: 8px;
            flex-wrap: wrap;
            padding: 6px 0;
            font-size: 0.8125rem;
            color: var(--text-secundario);
        }

        .lote-historico-item .ponto-morte { color: #dc2626; }
        .lote-historico-item .ponto-doenca { color: #d97706; }
        .lote-historico-item .ponto-recuperacao { color: #15803d; }
        .lote-historico-item strong { color: var(--text-principal); font-weight: 700; }
        .lote-historico-item .variacao { color: #dc2626; font-weight: 700; }
        .lote-historico-item .variacao-doenca { color: #d97706; font-weight: 700; }
        .lote-historico-item .variacao-recuperacao { color: #15803d; font-weight: 700; }
        .lote-historico-item .destino { color: var(--text-principal); font-style: normal; }
        .lote-historico-item .obs { font-style: italic; }

        /* ==========================================================================
        8. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard/Fazendas
        ========================================================================== */
        body.dark-theme {
            --bg-principal: #0a1810;
            --text-principal: #f1f9f4;
            --text-secundario: #9db8a8;
            --card-bg: #12251a;
            --card-border: 1px solid rgba(74, 222, 128, 0.16);
            --card-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);

            --navbar-bg: #0c1b12;
            --sidebar-bg: #0c1b12;
            --surface-border: rgba(74, 222, 128, 0.14);
            --surface-muted-bg: #0a1810;
            --surface-muted-border: #2a4d38;

            --menu-text: #9db8a8;
            --menu-text-hover: #4ade80;
            --menu-hover-bg: rgba(74, 222, 128, 0.08);
            --menu-active-bg: rgba(74, 222, 128, 0.12);
            --menu-active-text: #4ade80;
            --menu-active-accent: #4ade80;

            --panel-bg: #12251a;
            --panel-text: #f1f9f4;
            --panel-item-bg: #0a1810;
            --panel-btn-bg: #1e3a2a;
            --panel-btn-text: #d9ede1;
            --panel-btn-hover: #2a4d38;
        }

        body.dark-theme .toggle-menu-btn { background: #16281c; border-color: var(--surface-border); color: #d9ede1; }
        body.dark-theme .user-avatar { background: rgba(74, 222, 128, 0.12); border-color: rgba(74, 222, 128, 0.2); color: #4ade80; }
        body.dark-theme .navbar-right .accessibility-trigger { background: rgba(74, 222, 128, 0.12); border-color: rgba(74, 222, 128, 0.2); color: #4ade80; }

        body.high-contrast {
            background: #000000 !important;
            color: #ffff00 !important;
            /* Sem isso, textos que usam --text-principal/--text-secundario
               ficavam com a cor cinza-escura do tema claro sobre o fundo
               preto forçado — quase ilegível. */
            --text-principal: #ffff00;
            --text-secundario: #ffff00;
            --panel-bg: #000000;
            --panel-text: #ffffff;
            --panel-item-bg: #111111;
            --panel-btn-bg: #ffffff;
            --panel-btn-text: #000000;
            --panel-btn-hover: #ffff00;
        }

        body.high-contrast .lotes-lista,
        body.high-contrast .lote-row,
        body.high-contrast .stat-pill,
        body.high-contrast .navbar,
        body.high-contrast .sidebar,
        body.high-contrast .modal-card {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }
        body.high-contrast .navbar-logo img { filter: brightness(0) invert(1); }

        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3, body.high-contrast h4,
        body.high-contrast p, body.high-contrast span, body.high-contrast i, body.high-contrast label,
        body.high-contrast div, body.high-contrast strong {
            color: #ffff00 !important;
        }

        /* Ícones dentro de "pílulas"/cartões (estatísticas do topo, itens do
           painel de detalhes) usam fundos pastel via variável — a variável
           não muda no alto contraste, então esses círculos ficavam
           coloridos e ilegíveis num fundo totalmente preto. */
        body.high-contrast .stat-pill-icone,
        body.high-contrast .lote-row-detalhe-icone {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* Painel do "Ver detalhes": o fundo dele usa var(--surface-muted-bg),
           que o alto contraste não redefine, então ficava cinza-claro atrás
           dos cartõezinhos pretos — visual quebrado dentro de uma tela toda
           preto/amarelo. */
        body.high-contrast .lote-row-detalhes {
            background: #000000 !important;
            border-top: 1px dashed #ffff00 !important;
        }

        body.high-contrast .lote-row-detalhe-item {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .lote-row-fazenda::before { color: #ffff00 !important; opacity: 1; }

        /* Histórico de óbitos dentro do "Ver detalhes": a borda tracejada
           usa var(--surface-muted-border) (não redefinida no alto contraste)
           e os destaques em vermelho não têm contraste suficiente no
           esquema preto/amarelo — trocamos por um vermelho mais claro,
           igual ao já usado no botão "Excluir". */
        body.high-contrast .lote-historico { border-top: 1px dashed #ffff00 !important; }
        body.high-contrast .lote-historico-item .ponto-morte,
        body.high-contrast .lote-historico-item .variacao {
            color: #ff6b6b !important;
        }
        body.high-contrast .lote-historico-item .ponto-doenca,
        body.high-contrast .lote-historico-item .variacao-doenca {
            color: #ffa500 !important;
        }
        body.high-contrast .lote-historico-item .ponto-recuperacao,
        body.high-contrast .lote-historico-item .variacao-recuperacao {
            color: #4ade80 !important;
        }
        /* Aviso fixo do modal "Devolver ao Lote" usa var(--card-verde-*),
           que o alto contraste não redefine. */
        body.high-contrast .recuperacao-destino-fixo {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* Lista "Animais doentes" (modal Registrar Doença): botão de
           remover linha e botão tracejado de adicionar animal usam cores
           próprias (var/hover sutil) que não existem no esquema
           preto/amarelo do alto contraste. */
        body.high-contrast .btn-remover-linha-doenca {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
        }

        body.high-contrast .btn-adicionar-animal-doente {
            background: #000000 !important;
            border: 2px dashed #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .numero-animal { color: #ffff00 !important; }

        body.high-contrast .menu a.active,
        body.high-contrast .menu a:hover { background: #1a1a00 !important; }

        body.high-contrast .toggle-menu-btn,
        body.high-contrast .user-avatar,
        body.high-contrast .icon {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .navbar-logout {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
        }

        body.high-contrast .btn-add,
        body.high-contrast .btn-aplicar,
        body.high-contrast .submit-btn {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }

        body.high-contrast .btn-ver-detalhes,
        body.high-contrast .btn-menu-lote,
        body.high-contrast .lote-row-dropdown {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .lote-row-dropdown button { color: #ffffff !important; }
        body.high-contrast .lote-row-dropdown button.excluir { color: #ff6b6b !important; }

        body.high-contrast .input-box input,
        body.high-contrast .input-box select {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .btn-abrir-filtros,
        body.high-contrast .filtros-form select,
        body.high-contrast .filtros-ativos .chip {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .btn-limpar {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .status-badge,
        body.high-contrast .paginacao-paginas button,
        body.high-contrast .toolbar-ordenar select,
        body.high-contrast .itens-por-pagina select {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast a:focus-visible,
        body.high-contrast button:focus-visible {
            outline: 3px solid #ffff00 !important;
            outline-offset: 2px !important;
        }

        /* ==========================================================================
        9. RESPONSIVIDADE
        ========================================================================== */
        @media (max-width: 992px) {
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 90px; }
            .sidebar { left: -260px; }
            .sidebar.active-mobile { left: 0; box-shadow: 10px 0 30px rgba(0, 0, 0, 0.18); }
            .user-info { display: none; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .main { padding: 18px; padding-top: 86px; }
            .header-title h2 { font-size: 1.5rem; }

            .lotes-toolbar { align-items: stretch; }
            .toolbar-ordenar { justify-content: space-between; }

            .lote-row-main { align-items: flex-start; }
            .lote-row-id { flex: 1 1 100%; }
            .lote-row-actions { margin-left: 0; width: 100%; justify-content: space-between; }
            .lote-row-detalhes { padding-left: 20px; }
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header-title p { font-size: 0.844rem; }
            .top-bar-actions { flex-direction: column; align-items: stretch; width: 100%; }

            .stat-pills { flex-direction: column; }
            .stat-pill { width: 100%; }

            .lote-row-main { padding: 14px; gap: 12px; }
            .lote-row-col { min-width: 0; flex: 1 1 45%; }
            .lote-row-detalhes { grid-template-columns: 1fr; padding: 14px 14px 16px; }

            .lotes-paginacao { flex-direction: column; align-items: stretch; text-align: center; }
            .paginacao-paginas { justify-content: center; flex-wrap: wrap; }
            .paginacao-itens { justify-content: center; }
        }

        @media (max-width: 400px) {
            .navbar-right { gap: 10px; }
            .navbar-logout span { display: none; }
        }

        @media (max-width: 480px) {
            .row { flex-direction: column; }
            .modal-card { padding: 22px; }
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR LATERAL -->
    <div class="sidebar" id="sidebar">
        <div class="menu">
            <a href="{{ route('cadastro.index') }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('fazenda.listar') }}">
                <i class="fa-solid fa-location-dot"></i>
                <span>Gestão de Fazendas</span>
            </a>
            <a href="{{ route('lote.listar') }}" class="active">
                <i class="fa-solid fa-layer-group"></i>
                <span>Gestão de Lotes</span>
            </a>
            <a href="{{ route('ordenha.listar') }}">
                <i class="fa-solid fa-droplet"></i>
                <span>Ordenha e Leite</span>
            </a>
            <a href="{{ route('cocho.listar') }}">
                <i class="fa-solid fa-cube"></i>
                <span>Gestão de Cochos</span>
            </a>
            <a href="{{ route('racao.listar') }}">
                <i class="fa-solid fa-wheat-awn"></i>
                <span>Gestão de Rações</span>
            </a>
            <a href="{{ route('estoque.listar') }}">
                <i class="fa-solid fa-box"></i>
                <span>Nível de Estoque</span>
            </a>
            <a href="{{ route('plano.listar') }}">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Plano Alimentar</span>
            </a>
            <a href="{{ route('relatorio.listar') }}">
                <i class="fa-regular fa-file-lines"></i>
                <span>Relatório Avançado</span>
            </a>
            <a href="{{ route('perfil') }}">
                <i class="fa-solid fa-user-gear"></i>
                <span>Meu Perfil</span>
            </a>
            <div class="footer-right">© 2026 AgriFarm</div>
        </div>
    </div>

    <!-- NAVBAR SUPERIOR -->
    <div class="navbar" id="navbar">
        <div class="navbar-left">
            <button class="toggle-menu-btn" id="toggleMenuBtn" title="Menu Lateral">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="navbar-logo">
                <img src="{{ asset('agrifarm-removebg-preview.png') }}" alt="AgriFarm">
            </div>
        </div>

        <div class="navbar-right">
            <a href="{{ route('perfil') }}" class="user-profile" title="Meu perfil">
                <div class="user-avatar">
                    @if ($cadastroLogado->foto ?? false)
                        <img src="{{ asset('storage/' . $cadastroLogado->foto) }}" alt="Foto de perfil" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                    @else
                        <i class="fa-solid fa-user-gear"></i>
                    @endif
                </div>
                <div class="user-info">
                    <span class="user-name">Olá, {{ session('usuario_nome') }}</span>
                    <span class="user-role">Administrador</span>
                </div>
            </a>

            <div class="navbar-divider"></div>

            <a href="{{ route('logout') }}" class="navbar-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Sair</span>
            </a>
        </div>
    </div>

    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <main class="main" id="mainArea">
        <div class="header">
            <div class="header-title">
                <h2>Gestão de Lotes</h2>
                <p>Seu rebanho organizado por lotes para você monitorar a evolução.</p>
            </div>

            <div class="top-bar-actions">
                <button class="btn-abrir-filtros" id="openFiltrosBtn">
                    <i class="fa-solid fa-sliders"></i> Filtros Avançados
                    <span class="filtros-badge {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}">{{ count($filtrosAplicados ?? []) }}</span>
                </button>
                <button class="btn-add" onclick="abrirModal()">
                    <i class="fa-solid fa-plus"></i> Adicionar Lote
                </button>
            </div>
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

        <div id="modalFiltros" class="modal">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="tituloModalFiltros">
                <button class="close" id="closeFiltrosBtn" aria-label="Fechar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <div class="modal-top">
                    <img src="{{ asset('Logo.png') }}" alt="Logo">
                    <h2 id="tituloModalFiltros">Filtros Avançados</h2>
                    <p>Escolha o que exibir no painel de Lotes</p>
                </div>

                <form action="{{ route('lote.listar') }}" method="GET" class="filtros-form">
                    <div class="form-group">
                        <label for="filtro_fazenda_id">Fazenda</label>
                        <select id="filtro_fazenda_id" name="fazenda_id">
                            <option value="">Todas as Fazendas</option>
                            @foreach ($fazendas as $fazenda)
                                <option value="{{ $fazenda->id }}" @selected(($filtros['fazenda_id'] ?? '') == $fazenda->id)>{{ $fazenda->nome_fazenda }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro_tipo_animal">Tipo de Animal</label>
                        <select id="filtro_tipo_animal" name="tipo_animal">
                            <option value="">Todos os Tipos</option>
                            @foreach ($tiposAnimais as $tipo)
                                <option value="{{ $tipo }}" @selected(($filtros['tipo_animal'] ?? '') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro_finalidade">Finalidade</label>
                        <select id="filtro_finalidade" name="finalidade">
                            <option value="">Todas as Finalidades</option>
                            @foreach ($todasFinalidades as $finalidade)
                                <option value="{{ $finalidade }}" @selected(($filtros['finalidade'] ?? '') === $finalidade)>{{ $finalidade }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro_sexo">Sexo</label>
                        <select id="filtro_sexo" name="sexo">
                            <option value="">Todos</option>
                            @foreach ($sexos as $sexo)
                                <option value="{{ $sexo }}" @selected(($filtros['sexo'] ?? '') === $sexo)>{{ $sexo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filtros-modal-footer">
                        <button type="submit" class="btn-aplicar">
                            <i class="fa-solid fa-check"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('lote.listar') }}" class="btn-limpar">
                            <i class="fa-solid fa-eraser"></i> Limpar
                        </a>
                    </div>
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
                    <strong>Não foi possível salvar o lote:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            // Ícone que identifica visualmente a espécie no card (requisito:
            // "bovino com ícone de boi, suíno com ícone de porco etc."). O
            // Font Awesome Free não tem ícone dedicado para todo animal de
            // fazenda (ovino/caprino, por exemplo) — nesses casos cai no
            // ícone de pata como alternativa neutra. Mesma paleta (ícone +
            // cor por espécie) usada em partials/lote-consumo-linhas.blade.php,
            // Rações e Estoque, pra bater visualmente em todo o sistema.
            $iconesPorAnimal = [
                'Bovino' => ['icone' => 'fa-cow', 'claro' => '#4ade80', 'escuro' => '#059669', 'glow' => 'rgba(5, 150, 105, 0.5)'],
                'Suíno' => ['icone' => 'fa-piggy-bank', 'claro' => '#f472b6', 'escuro' => '#db2777', 'glow' => 'rgba(219, 39, 119, 0.45)'],
                'Equino' => ['icone' => 'fa-horse', 'claro' => '#fbbf24', 'escuro' => '#b45309', 'glow' => 'rgba(180, 83, 9, 0.45)'],
                'Avicultura' => ['icone' => 'fa-dove', 'claro' => '#60a5fa', 'escuro' => '#0ea5e9', 'glow' => 'rgba(14, 165, 233, 0.45)'],
                'Ovino' => ['icone' => 'fa-paw', 'claro' => '#c084fc', 'escuro' => '#7c3aed', 'glow' => 'rgba(124, 58, 237, 0.45)'],
                'Caprino' => ['icone' => 'fa-paw', 'claro' => '#fb923c', 'escuro' => '#f97316', 'glow' => 'rgba(234, 88, 12, 0.45)'],
            ];
            $iconePadraoAnimal = ['icone' => 'fa-paw', 'claro' => '#94a3b8', 'escuro' => '#64748b', 'glow' => 'rgba(100, 116, 139, 0.4)'];
        @endphp

        @php
            $totalLotes = $lotes->count();
            $totalAtivos = $lotes->where('status', 'Ativo')->count();
            $totalInativos = $totalLotes - $totalAtivos;
        @endphp

        <div class="lotes-toolbar">
            <div class="stat-pills">
                <div class="stat-pill stat-pill-total">
                    <div class="stat-pill-icone"><i class="fa-solid fa-layer-group"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalLotes }}</div>
                        <div class="stat-pill-label">Total de lotes</div>
                    </div>
                </div>
                <div class="stat-pill stat-pill-ativos">
                    <div class="stat-pill-icone"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalAtivos }}</div>
                        <div class="stat-pill-label">Lotes ativos</div>
                    </div>
                </div>
                <div class="stat-pill stat-pill-inativos">
                    <div class="stat-pill-icone"><i class="fa-solid fa-circle-xmark"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalInativos }}</div>
                        <div class="stat-pill-label">Lotes inativos</div>
                    </div>
                </div>
            </div>

            @if ($totalLotes > 0)
                <div class="toolbar-ordenar">
                    <label for="ordenarLotes">Ordenar por:</label>
                    <select id="ordenarLotes">
                        <option value="recentes">Mais recentes</option>
                        <option value="nome">Nome (A-Z)</option>
                        <option value="animais">Mais animais</option>
                        <option value="peso">Maior peso médio</option>
                    </select>
                </div>
            @endif
        </div>

        <div class="lotes-lista" id="cardsLotes">
            @forelse($lotes as $lote)
                @php
                    $animalIconeLote = $iconesPorAnimal[$lote->tipo_animal] ?? $iconePadraoAnimal;
                    $animalIconeLoteEstilo = 'background: linear-gradient(145deg, ' . $animalIconeLote['claro'] . ', ' . $animalIconeLote['escuro'] . '); box-shadow: 0 6px 14px -4px ' . $animalIconeLote['glow'] . ', inset 0 1px 1px rgba(255,255,255,0.4), inset 0 -2px 3px rgba(0,0,0,0.16);';
                    $statusAtual = $lote->status ?? 'Ativo';
                    $loteInativo = $statusAtual !== 'Ativo';
                    $motivoBloqueio = $loteInativo
                        ? "Lote {$statusAtual} — só é possível registrar em lotes ativos"
                        : ($lote->quantidade_animais < 1 ? 'Lote sem animais ativos' : '');

                    // Lote de isolamento (nasceu de um "Registrar Doença") —
                    // não pode gerar outro lote de isolamento; só devolve
                    // animais pro lote original.
                    $ehIsolamento = (bool) $lote->lote_origem_id;
                    $motivoBloqueioRecuperacao = $motivoBloqueio ?: (!$lote->origem ? 'Lote original não existe mais' : '');
                    $estoquePorDoenca = $ehIsolamento ? $lote->estoquePorDoenca() : [];

                    // Parto/Reprodução/Lactação/Ordenha agora são por ANIMAL
                    // individual (ver seção "Animais deste lote" abaixo) —
                    // a elegibilidade de sexo é checada por animal, não mais
                    // pelo sexo agregado do lote. Só a espécie (tipo_animal)
                    // continua sendo uma regra do LOTE: nem toda espécie
                    // produz leite (ex.: galinha põe ovo) — só Bovino, Ovino
                    // e Caprino têm a opção de ordenha nos seus animais.
                    $loteTemOrdenha = in_array($lote->tipo_animal, $tiposComOrdenha, true);
                    $animaisDoLote = $lote->animais;
                @endphp
                <div class="lote-row"
                    data-nome="{{ strtolower($lote->nome_lote) }}"
                    data-animais="{{ (int) $lote->quantidade_animais }}"
                    data-peso="{{ (float) $lote->peso_medio }}">
                    <div class="lote-row-main">
                        <div class="lote-row-id">
                            <div class="icon" style="{{ $animalIconeLoteEstilo }}"><i class="fa-solid {{ $animalIconeLote['icone'] }}"></i></div>
                            <div>
                                <div class="lote-row-nome">{{ $lote->nome_lote }}</div>
                                <div class="lote-row-fazenda">{{ $lote->fazenda->nome_fazenda ?? 'Sem fazenda' }}</div>
                            </div>
                        </div>

                        <div class="lote-row-col">
                            <span class="lote-row-col-label">Espécie</span>
                            <span class="lote-row-col-valor">{{ $lote->tipo_animal }}</span>
                        </div>

                        <div class="lote-row-col">
                            <span class="lote-row-col-label">Animais</span>
                            <span class="lote-row-col-valor">{{ $lote->quantidade_animais }}</span>
                        </div>

                        <div class="lote-row-col">
                            <span class="lote-row-col-label">Status</span>
                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($statusAtual) }}">
                                <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> {{ $statusAtual }}
                            </span>
                        </div>

                        <div class="lote-row-actions">
                            <button type="button" class="btn-ver-detalhes" onclick="alternarDetalhesLote(this)">
                                <span class="texto-btn-detalhes">Ver detalhes</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <div class="lote-row-menu">
                                <button type="button" class="btn-menu-lote" onclick="alternarMenuLote(this)" title="Mais ações">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="lote-row-dropdown">
                                    <button type="button" onclick="abrirModalEditar(
                                        '{{ $lote->id }}',
                                        '{{ $lote->nome_lote }}',
                                        '{{ $lote->fazenda_id }}',
                                        '{{ $lote->tipo_animal }}',
                                        '{{ $lote->finalidade }}',
                                        '{{ $lote->sexo }}',
                                        '{{ $lote->idade_media }}',
                                        '{{ $lote->quantidade_animais }}',
                                        '{{ $lote->peso_medio }}',
                                        '{{ $lote->data_entrada }}',
                                        '{{ $statusAtual }}'
                                    )">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </button>
                                    @if ($ehIsolamento)
                                        <button type="button" onclick="abrirModalRecuperacao(this)"
                                            data-id="{{ $lote->id }}"
                                            data-nome="{{ $lote->nome_lote }}"
                                            data-quantidade="{{ $lote->quantidade_animais }}"
                                            data-origem="{{ $lote->origem->nome_lote ?? '' }}"
                                            data-estoque='@json($estoquePorDoenca)'
                                            {{ $motivoBloqueioRecuperacao ? 'disabled title="' . $motivoBloqueioRecuperacao . '"' : '' }}>
                                            <i class="fa-solid fa-rotate-left"></i> Devolver ao Lote
                                        </button>
                                    @else
                                        <button type="button" onclick="abrirModalDoenca(this)"
                                            data-id="{{ $lote->id }}"
                                            data-nome="{{ $lote->nome_lote }}"
                                            data-quantidade="{{ $lote->quantidade_animais }}"
                                            data-tipo-animal="{{ $lote->tipo_animal }}"
                                            {{ $motivoBloqueio ? 'disabled title="' . $motivoBloqueio . '"' : '' }}>
                                            <i class="fa-solid fa-virus"></i> Registrar Doença
                                        </button>
                                    @endif
                                    <button type="button" onclick="abrirModalMorte(this)"
                                        data-id="{{ $lote->id }}"
                                        data-nome="{{ $lote->nome_lote }}"
                                        data-quantidade="{{ $lote->quantidade_animais }}"
                                        data-isolamento="{{ $ehIsolamento ? '1' : '0' }}"
                                        data-estoque='@json($estoquePorDoenca)'
                                        {{ $motivoBloqueio ? 'disabled title="' . $motivoBloqueio . '"' : '' }}>
                                        <i class="fa-solid fa-heart-crack"></i> Registrar Óbito
                                    </button>
                                    <form action="{{ route('lote.delete', $lote->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este lote?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="excluir">
                                            <i class="fa-solid fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lote-row-detalhes" hidden>
                        <div class="lote-row-detalhe-item">
                            <div class="lote-row-detalhe-icone"><i class="fa-solid fa-weight-scale"></i></div>
                            <div class="lote-row-detalhe-texto">
                                <span>Peso médio</span>
                                <strong>{{ number_format($lote->peso_medio, 2, ',', '.') }} kg</strong>
                            </div>
                        </div>
                        <div class="lote-row-detalhe-item">
                            <div class="lote-row-detalhe-icone"><i class="fa-solid fa-bullseye"></i></div>
                            <div class="lote-row-detalhe-texto">
                                <span>Finalidade</span>
                                <strong>{{ $lote->finalidade ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="lote-row-detalhe-item">
                            <div class="lote-row-detalhe-icone"><i class="fa-solid fa-venus-mars"></i></div>
                            <div class="lote-row-detalhe-texto">
                                <span>Sexo</span>
                                <strong>{{ $lote->sexo ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="lote-row-detalhe-item">
                            <div class="lote-row-detalhe-icone"><i class="fa-solid fa-hourglass-half"></i></div>
                            <div class="lote-row-detalhe-texto">
                                <span>Idade média</span>
                                <strong>{{ $lote->idade_media ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="lote-row-detalhe-item">
                            <div class="lote-row-detalhe-icone"><i class="fa-solid fa-calendar"></i></div>
                            <div class="lote-row-detalhe-texto">
                                <span>Data de entrada</span>
                                <strong>{{ \Carbon\Carbon::parse($lote->data_entrada)->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        @if ($lote->ocorrencias->isNotEmpty())
                            <div class="lote-historico">
                                <div class="lote-historico-titulo">Histórico de ocorrências</div>
                                @foreach ($lote->ocorrencias as $ocorrencia)
                                    <div class="lote-historico-item">
                                        @if ($ocorrencia->tipo === 'doenca')
                                            <span class="ponto-doenca"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i></span>
                                            <strong>Doença registrada — {{ $ocorrencia->doenca }}</strong>
                                            <span>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                                            <span class="variacao-doenca">{{ $ocorrencia->quantidade_antes }} → {{ $ocorrencia->quantidade_depois }} animais</span>
                                            @if ($ocorrencia->loteDestino)
                                                <span class="destino">movidos para "{{ $ocorrencia->loteDestino->nome_lote }}"</span>
                                            @endif
                                        @elseif ($ocorrencia->tipo === 'entrada')
                                            <span class="ponto-doenca"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i></span>
                                            <strong>Chegada em tratamento — {{ $ocorrencia->doenca }}</strong>
                                            <span>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                                            <span class="variacao-doenca">{{ $ocorrencia->quantidade_antes }} → {{ $ocorrencia->quantidade_depois }} animais</span>
                                            @if ($ocorrencia->loteDestino)
                                                <span class="destino">vindos de "{{ $ocorrencia->loteDestino->nome_lote }}"</span>
                                            @endif
                                        @elseif ($ocorrencia->tipo === 'recuperacao')
                                            <span class="ponto-recuperacao"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i></span>
                                            <strong>Recuperação registrada — {{ $ocorrencia->doenca }}</strong>
                                            <span>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                                            <span class="variacao-recuperacao">{{ $ocorrencia->quantidade_antes }} → {{ $ocorrencia->quantidade_depois }} animais</span>
                                            @if ($ocorrencia->loteDestino)
                                                <span class="destino">vindos de "{{ $ocorrencia->loteDestino->nome_lote }}"</span>
                                            @endif
                                        @else
                                            <span class="ponto-morte"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i></span>
                                            <strong>Óbito registrado @if ($ocorrencia->causa) — {{ $ocorrencia->causa }}@endif{{ $ocorrencia->doenca ? ' (' . $ocorrencia->doenca . ')' : '' }}</strong>
                                            <span>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                                            <span class="variacao">{{ $ocorrencia->quantidade_antes }} → {{ $ocorrencia->quantidade_depois }} animais</span>
                                        @endif
                                        @if ($ocorrencia->observacao)
                                            <span class="obs">"{{ $ocorrencia->observacao }}"</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="cards-empty">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                    <p>
                        @if (collect($filtros)->filter()->isNotEmpty())
                            Nenhum lote encontrado com esses filtros.<br>Tente ajustar ou limpar os filtros.
                        @else
                            Você ainda não cadastrou nenhum lote.<br>Clique em "Adicionar Lote" para começar.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if ($totalLotes > 0)
            <div class="lotes-paginacao" id="lotesPaginacao">
                <span id="paginacaoInfo"></span>
                <div class="paginacao-paginas" id="paginacaoPaginas"></div>
                <div class="paginacao-itens">
                    <label for="itensPorPagina">Itens por página:</label>
                    <select id="itensPorPagina">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        @endif
    </main>

    <!-- MODAL: ADICIONAR LOTE -->
    <div class="modal" id="modal">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Adicionar Lote</p>
            </div>

            <form action="{{ route('lote.add') }}" method="POST">
                @csrf
                <input type="hidden" name="origem_formulario" value="add">

                <label>Nome do Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-layer-group"></i>
                    <input type="text" name="nome_lote" value="{{ old('nome_lote') }}" placeholder="Lote A1" required>
                </div>

                <label for="fazenda_id">Selecione a Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-industry"></i>
                    <select id="fazenda_id" name="fazenda_id" required>
                        <option value="" disabled @selected(!old('fazenda_id'))>Escolha uma fazenda...</option>
                        @foreach ($fazendas as $fazenda)
                            <option value="{{ $fazenda->id }}" @selected(old('fazenda_id') == $fazenda->id)>{{ $fazenda->nome_fazenda }}</option>
                        @endforeach
                    </select>
                </div>

                <label>Selecione um tipo de animal</label>
                <div class="input-box">
                    <i class="fa-solid fa-cow"></i>
                    <select id="tipo_animal" name="tipo_animal" required>
                        <option value="" disabled @selected(!old('tipo_animal'))>Escolha um tipo de animal...</option>
                        @foreach ($tiposAnimais as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo_animal') === $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="campo">
                        <label>Sexo</label>
                        <div class="input-box">
                            <i class="fa-solid fa-venus-mars"></i>
                            <select id="sexo" name="sexo" required>
                                <option value="" disabled @selected(!old('sexo'))>Selecione...</option>
                                @foreach ($sexos as $sexo)
                                    <option value="{{ $sexo }}" @selected(old('sexo') === $sexo)>{{ $sexo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="campo">
                        <label>Finalidade</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bullseye"></i>
                            <select id="finalidade" name="finalidade" required>
                                <option value="" disabled @selected(!old('finalidade'))>Selecione...</option>
                                @foreach ($todasFinalidades as $finalidade)
                                    <option value="{{ $finalidade }}" @selected(old('finalidade') === $finalidade)>{{ $finalidade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="dica-campo">As opções mudam de acordo com o tipo de animal e o sexo escolhidos.</small>
                    </div>
                </div>

                <label>Idade Média</label>
                <div class="input-box">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <select name="idade_media" required>
                        <option value="" disabled @selected(!old('idade_media'))>Selecione...</option>
                        @foreach ($idadesMedias as $idade)
                            <option value="{{ $idade }}" @selected(old('idade_media') === $idade)>{{ $idade }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="campo">
                        <label>Quantidade de Animais</label>
                        <div class="input-box">
                            <i class="fa-solid fa-paw"></i>
                            <input type="number" name="quantidade_animais" value="{{ old('quantidade_animais') }}" placeholder="500" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label>Peso Médio (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" id="peso_medio" name="peso_medio" value="{{ old('peso_medio') }}" placeholder="380.50" required>
                        </div>
                        <small class="dica-campo" id="dica_peso">Selecione o tipo de animal para ver a faixa de peso esperada.</small>
                    </div>
                </div>

                <label for="status">Status do Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-flag"></i>
                    <select id="status" name="status">
                        @foreach ($statusLote as $status)
                            <option value="{{ $status }}" @selected(old('status', 'Ativo') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="data_entrada">Data Entrada</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="data_entrada" name="data_entrada" value="{{ old('data_entrada') }}" min="{{ $hoje }}" required>
                </div>
                <small class="dica-campo">A data não pode ser anterior a {{ \Carbon\Carbon::parse($hoje)->format('d/m/Y') }} (hoje).</small>

                <button type="submit" class="submit-btn">Cadastrar Lote</button>
            </form>
        </div>
    </div>

    <!-- MODAL: ATUALIZAR LOTE -->
    <div class="modal" id="modalEditar">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalEditar()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Atualizar Lote</p>
            </div>

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="origem_formulario" value="edit">
                <input type="hidden" id="lote_id_edit" name="lote_id_edit" value="{{ old('lote_id_edit') }}">

                <label>Nome do Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-layer-group"></i>
                    <input type="text" id="edit_nome_lote" name="nome_lote" required>
                </div>

                <label for="edit_fazenda_id">Selecione a Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-industry"></i>
                    <select id="edit_fazenda_id" name="fazenda_id" required>
                        <option value="" disabled>Escolha uma fazenda...</option>
                        @foreach ($fazendas as $fazenda)
                            <option value="{{ $fazenda->id }}">{{ $fazenda->nome_fazenda }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="edit_tipo_animal">Tipo de Animal</label>
                <div class="input-box">
                    <i class="fa-solid fa-cow"></i>
                    <select id="edit_tipo_animal" name="tipo_animal" required>
                        <option value="" disabled>Escolha um tipo de animal...</option>
                        @foreach ($tiposAnimais as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="campo">
                        <label for="edit_sexo">Sexo</label>
                        <div class="input-box">
                            <i class="fa-solid fa-venus-mars"></i>
                            <select id="edit_sexo" name="sexo" required>
                                <option value="" disabled>Selecione...</option>
                                @foreach ($sexos as $sexo)
                                    <option value="{{ $sexo }}">{{ $sexo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="edit_finalidade">Finalidade</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bullseye"></i>
                            <select id="edit_finalidade" name="finalidade" required>
                                <option value="" disabled>Selecione...</option>
                                @foreach ($todasFinalidades as $finalidade)
                                    <option value="{{ $finalidade }}">{{ $finalidade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="dica-campo">As opções mudam de acordo com o tipo de animal e o sexo escolhidos.</small>
                    </div>
                </div>

                <label for="edit_idade_media">Idade Média</label>
                <div class="input-box">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <select id="edit_idade_media" name="idade_media" required>
                        <option value="" disabled>Selecione...</option>
                        @foreach ($idadesMedias as $idade)
                            <option value="{{ $idade }}">{{ $idade }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="campo">
                        <label>Quantidade</label>
                        <div class="input-box">
                            <i class="fa-solid fa-paw"></i>
                            <input type="number" id="edit_quantidade_animais" name="quantidade_animais" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label>Peso Médio (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" id="edit_peso_medio" name="peso_medio" required>
                        </div>
                        <small class="dica-campo" id="edit_dica_peso">Selecione o tipo de animal para ver a faixa de peso esperada.</small>
                    </div>
                </div>

                <label for="edit_status">Status do Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-flag"></i>
                    <select id="edit_status" name="status">
                        @foreach ($statusLote as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="edit_data_entrada">Data Entrada</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="edit_data_entrada" name="data_entrada" min="{{ $hoje }}" required>
                </div>
                <small class="dica-campo">A data não pode ser anterior a {{ \Carbon\Carbon::parse($hoje)->format('d/m/Y') }} (hoje).</small>

                <button type="submit" class="submit-btn">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR ÓBITO -->
    <div class="modal" id="modalMorte">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalMorte()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Registrar Óbito</p>
            </div>

            <p class="acao-lote-nome" id="morte_lote_nome"></p>

            <form id="formMorte" method="POST" onsubmit="return validarFormMorte()">
                @csrf
                <input type="hidden" name="origem_formulario" value="morte">
                <input type="hidden" id="morte_lote_id" name="lote_id" value="">

                <label for="morte_causa">Causa da morte</label>
                <div class="input-box">
                    <i class="fa-solid fa-notes-medical"></i>
                    <select id="morte_causa" name="causa" required onchange="alternarCamposMorte()">
                        <option value="" disabled selected>Selecione a causa...</option>
                        @foreach ($causasMorte as $causa)
                            <option value="{{ $causa }}" {{ $causa === 'Corte' ? 'id="opcaoCausaCorte"' : '' }}>{{ $causa }}</option>
                        @endforeach
                        <option value="{{ $causaMorteOutra }}">Outra (especificar)</option>
                    </select>
                </div>

                <div class="campo campo-morte-causa-outra" id="campoMorteCausaOutra" hidden>
                    <label for="morte_causa_outra">Qual causa?</label>
                    <div class="input-box">
                        <i class="fa-solid fa-pen"></i>
                        <input type="text" id="morte_causa_outra" name="causa_outra" placeholder="Digite a causa da morte" maxlength="100">
                    </div>
                </div>

                <!-- Lote de isolamento + causa "Doença": pede quantos morreram de cada doença ativa -->
                <div id="blocoMorteDoencas" hidden>
                    <label>Quantos morreram de cada doença?</label>
                    <div id="morteDoencasLista"></div>
                    <small class="dica-campo" id="morte_doenca_dica"></small>
                </div>

                <!-- Demais casos: quantidade única, com doença opcional em lote nunca isolado -->
                <div id="blocoMorteQuantidade">
                    <div class="row">
                        <div class="campo">
                            <label for="morte_quantidade">Quantidade</label>
                            <div class="input-box">
                                <i class="fa-solid fa-heart-crack"></i>
                                <input type="number" id="morte_quantidade" name="quantidade" min="1" value="1">
                            </div>
                            <small class="dica-campo" id="morte_quantidade_dica"></small>
                        </div>
                    </div>

                    <div class="campo campo-morte-doenca-livre" id="campoMorteDoencaLivre" hidden>
                        <label for="morte_doenca_livre">Qual doença? (opcional)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-virus"></i>
                            <input type="text" id="morte_doenca_livre" name="doenca_livre" placeholder="Ex.: pneumonia" maxlength="100">
                        </div>
                    </div>
                </div>

                <label for="morte_data">Data</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="morte_data" name="data_ocorrencia" max="{{ $hoje }}" required>
                </div>

                <label for="morte_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="morte_observacao" name="observacao" placeholder="Detalhes adicionais" maxlength="255">
                </div>

                <button type="submit" class="submit-btn btn-confirmar-morte">
                    <i class="fa-solid fa-check"></i> Confirmar Óbito
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR DOENÇA -->
    <div class="modal" id="modalDoenca">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalDoenca()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Registrar Doença</p>
            </div>

            <p class="acao-lote-nome" id="doenca_lote_nome"></p>

            <form id="formDoenca" method="POST">
                @csrf
                <input type="hidden" name="origem_formulario" value="doenca">
                <input type="hidden" id="doenca_lote_id" name="lote_id" value="">

                <label>Animais doentes <span class="label-opcional">— cada um pode ter uma doença diferente</span></label>
                <div id="doencaAnimaisLista"></div>

                <button type="button" class="btn-adicionar-animal-doente" id="btnAdicionarAnimalDoente" onclick="adicionarLinhaDoenca()">
                    <i class="fa-solid fa-plus"></i> Adicionar animal
                </button>

                <small class="dica-campo" id="doenca_quantidade_dica"></small>

                <div class="row">
                    <div class="campo">
                        <label for="doenca_data">Data</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="doenca_data" name="data_ocorrencia" max="{{ $hoje }}" required>
                        </div>
                    </div>
                </div>

                <small class="dica-campo" id="doenca_destino_dica"></small>

                <label for="doenca_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="doenca_observacao" name="observacao" placeholder="Ex.: já isolados desde ontem" maxlength="255">
                </div>

                <button type="submit" class="submit-btn btn-confirmar-doenca">
                    <i class="fa-solid fa-check"></i> Confirmar Registro
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: DEVOLVER AO LOTE (recuperação — só pro lote de origem) -->
    <div class="modal" id="modalRecuperacao">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalRecuperacao()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Devolver ao Lote</p>
            </div>

            <p class="acao-lote-nome" id="recuperacao_lote_nome"></p>
            <p class="recuperacao-destino-fixo" id="recuperacao_destino_fixo"></p>

            <form id="formRecuperacao" method="POST" onsubmit="return validarFormRecuperacao()">
                @csrf
                <input type="hidden" name="origem_formulario" value="recuperacao">
                <input type="hidden" id="recuperacao_lote_id" name="lote_id" value="">

                <label>Quantos animais recuperaram de cada doença?</label>
                <div id="recuperacaoDoencasLista"></div>
                <small class="dica-campo" id="recuperacao_quantidade_dica"></small>

                <div class="row">
                    <div class="campo">
                        <label for="recuperacao_data">Data</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="recuperacao_data" name="data_ocorrencia" max="{{ $hoje }}" required>
                        </div>
                    </div>
                </div>

                <label for="recuperacao_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="recuperacao_observacao" name="observacao" placeholder="Ex.: liberados pelo veterinário" maxlength="255">
                </div>

                <button type="submit" class="submit-btn btn-confirmar-recuperacao">
                    <i class="fa-solid fa-check"></i> Confirmar Retorno
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: ADICIONAR ANIMAL (dentro de um lote específico) -->
    <div class="modal" id="modalAnimal">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModal('modalAnimal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Adicionar Animal</p>
            </div>

            <p class="acao-lote-nome" id="animal_lote_nome"></p>

            <form action="{{ route('animal.add') }}" method="POST">
                @csrf
                <input type="hidden" id="animal_lote_id" name="lote_id" value="">

                <label for="animal_nome">Nome / Identificação</label>
                <div class="input-box">
                    <i class="fa-solid fa-cow"></i>
                    <input type="text" id="animal_nome" name="nome" placeholder="Ex.: Mimosa" required>
                </div>

                <label for="animal_identificador">Identificador (brinco)</label>
                <div class="input-box">
                    <i class="fa-solid fa-tag"></i>
                    <input type="text" id="animal_identificador" name="identificador" placeholder="Opcional">
                </div>

                <div class="row">
                    <div class="campo">
                        <label for="animal_sexo">Sexo</label>
                        <div class="input-box">
                            <i class="fa-solid fa-venus-mars"></i>
                            <select id="animal_sexo" name="sexo" required>
                                <option value="" disabled selected>Selecione...</option>
                                @foreach ($sexosAnimal as $sexo)
                                    <option value="{{ $sexo }}">{{ $sexo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="animal_raca">Raça</label>
                        <div class="input-box">
                            <i class="fa-solid fa-dna"></i>
                            <input type="text" id="animal_raca" name="raca" placeholder="Opcional">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="campo">
                        <label for="animal_data_nascimento">Data de Nascimento</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="animal_data_nascimento" name="data_nascimento" max="{{ $hoje }}" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="animal_peso">Peso (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" id="animal_peso" name="peso" placeholder="Opcional">
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fa-solid fa-check"></i> Cadastrar Animal
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR PARTO PREVISTO (por animal) -->
    <div class="modal" id="modalPartoAnimal">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModal('modalPartoAnimal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Registrar Parto Previsto</p>
            </div>

            <p class="acao-lote-nome" id="partoAnimal_nome"></p>

            <form id="formPartoAnimal" method="POST">
                @csrf

                <label for="partoAnimal_data_prevista">Data Prevista do Parto</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="partoAnimal_data_prevista" name="data_prevista" required>
                </div>

                <label for="partoAnimal_observacoes">Observações (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="partoAnimal_observacoes" name="observacoes" maxlength="255">
                </div>

                <small class="dica-campo">Depois do parto realizado, uma nova lactação é criada automaticamente — veja o perfil completo do animal.</small>

                <button type="submit" class="submit-btn">
                    <i class="fa-solid fa-check"></i> Registrar
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR ORDENHA (por animal) -->
    <div class="modal" id="modalOrdenhaAnimal">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModal('modalOrdenhaAnimal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Registrar Ordenha</p>
            </div>

            <p class="acao-lote-nome" id="ordenhaAnimal_nome"></p>

            <form id="formOrdenhaAnimal" method="POST">
                @csrf
                <div class="row">
                    <div class="campo">
                        <label for="ordenhaAnimal_data">Data</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="ordenhaAnimal_data" name="data" max="{{ $hoje }}" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="ordenhaAnimal_horario">Horário</label>
                        <div class="input-box">
                            <i class="fa-solid fa-clock"></i>
                            <input type="time" id="ordenhaAnimal_horario" name="horario" required>
                        </div>
                    </div>
                </div>

                <label for="ordenhaAnimal_turno">Turno</label>
                <div class="input-box">
                    <i class="fa-solid fa-sun"></i>
                    <select id="ordenhaAnimal_turno" name="turno" required>
                        <option value="" disabled selected>Selecione...</option>
                        @foreach ($turnosOrdenha as $turno)
                            <option value="{{ $turno }}">{{ $turno }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="ordenhaAnimal_litros">Quantidade de Leite (litros)</label>
                <div class="input-box">
                    <i class="fa-solid fa-droplet"></i>
                    <input type="number" step="0.01" min="0.01" id="ordenhaAnimal_litros" name="quantidade_litros" placeholder="Ex.: 12.5" required>
                </div>

                <label for="ordenhaAnimal_observacoes">Observações (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="ordenhaAnimal_observacoes" name="observacoes" maxlength="255">
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fa-solid fa-check"></i> Confirmar Ordenha
                </button>
            </form>
        </div>
    </div>

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
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            /* --- SIDEBAR RECOLHÍVEL (mesmo comportamento do Dashboard/Fazendas) --- */
            const toggleBtn = document.getElementById('toggleMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (toggleBtn && sidebar && mainContent) {
                toggleBtn.addEventListener('click', function() {
                    if (window.innerWidth > 992) {
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');
                    } else {
                        sidebar.classList.toggle('active-mobile');
                        if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
                    }
                });
            }

            if (sidebarOverlay && sidebar) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active-mobile');
                    sidebarOverlay.classList.remove('active');
                });
            }
        });
    </script>

    <script>
        /* --- FINALIDADE DEPENDE DE TIPO DE ANIMAL + SEXO ---
           Nem toda finalidade cabe em toda espécie (ex.: equino vive de
           reprodução/trabalho/esporte, nunca leite) e nenhum macho tem
           finalidade "Leite" ou "Ovos" em espécie nenhuma — são funções
           biológicas exclusivas da fêmea. Em vez de deixar o usuário
           escolher e só barrar no envio do formulário, as opções que não
           fazem sentido já ficam desabilitadas no próprio select. Isso é
           só uma ajuda visual: quem garante de verdade é o Laravel
           (LoteRequest), que valida tudo de novo no servidor. */
        const FINALIDADES_POR_ANIMAL = @json($finalidadesPorAnimal);
        const TODAS_FINALIDADES = @json($todasFinalidades);
        const FINALIDADES_SO_FEMEA = ['Leite', 'Ovos'];
        const FAIXA_PESO_POR_ANIMAL = @json($faixaPesoPorAnimal);
        const DOENCAS_POR_ANIMAL = @json($doencasPorAnimal);
        const DOENCA_OUTRA = @json($doencaOutra);
        const CAUSA_MORTE_OUTRA = @json($causaMorteOutra);

        function finalidadesPermitidas(tipoAnimal, sexo) {
            // Sem tipo de animal escolhido ainda (ou um valor que não bate
            // com nenhuma espécie conhecida), não faz sentido travar TODAS
            // as opções — isso deixava o campo Finalidade impossível de
            // preencher. Mostra tudo (só filtrando por sexo) até o tipo de
            // animal ser escolhido; a combinação real continua validada no
            // servidor de qualquer forma.
            let permitidas = (tipoAnimal && FINALIDADES_POR_ANIMAL[tipoAnimal])
                ? FINALIDADES_POR_ANIMAL[tipoAnimal]
                : TODAS_FINALIDADES;

            if (sexo === 'Macho') {
                permitidas = permitidas.filter(function(f) { return !FINALIDADES_SO_FEMEA.includes(f); });
            }

            return permitidas;
        }

        function atualizarFinalidades(tipoSelect, sexoSelect, finalidadeSelect) {
            const permitidas = finalidadesPermitidas(tipoSelect.value, sexoSelect.value);

            Array.from(finalidadeSelect.options).forEach(function(option) {
                if (!option.value) return; // placeholder "Selecione..."
                option.disabled = !permitidas.includes(option.value);
            });

            if (finalidadeSelect.value && !permitidas.includes(finalidadeSelect.value)) {
                finalidadeSelect.value = '';
            }
        }

        function ligarFiltroFinalidade(tipoId, sexoId, finalidadeId) {
            const tipoSelect = document.getElementById(tipoId);
            const sexoSelect = document.getElementById(sexoId);
            const finalidadeSelect = document.getElementById(finalidadeId);
            if (!tipoSelect || !sexoSelect || !finalidadeSelect) return;

            const atualizar = function() { atualizarFinalidades(tipoSelect, sexoSelect, finalidadeSelect); };
            tipoSelect.addEventListener('change', atualizar);
            sexoSelect.addEventListener('change', atualizar);
        }

        ligarFiltroFinalidade('tipo_animal', 'sexo', 'finalidade');
        ligarFiltroFinalidade('edit_tipo_animal', 'edit_sexo', 'edit_finalidade');
        ligarFiltroFinalidade('filtro_tipo_animal', 'filtro_sexo', 'filtro_finalidade');

        // ==========================================
        // LINHA DO LOTE: "Ver detalhes" (expande/recolhe) e menu "⋮"
        // ==========================================
        function alternarDetalhesLote(botao) {
            const linha = botao.closest('.lote-row');
            const painel = linha.querySelector('.lote-row-detalhes');
            const rotulo = botao.querySelector('.texto-btn-detalhes');
            const abrindo = painel.hidden;

            painel.hidden = !abrindo;
            botao.classList.toggle('aberto', abrindo);
            rotulo.textContent = abrindo ? 'Ocultar detalhes' : 'Ver detalhes';
        }

        function fecharTodosMenusLote(exceto) {
            document.querySelectorAll('.lote-row-dropdown.aberto').forEach((dropdown) => {
                if (dropdown !== exceto) dropdown.classList.remove('aberto');
            });
        }

        function alternarMenuLote(botao) {
            const dropdown = botao.nextElementSibling;
            const jaAberto = dropdown.classList.contains('aberto');
            fecharTodosMenusLote(null);
            dropdown.classList.toggle('aberto', !jaAberto);
        }

        document.addEventListener('click', (evento) => {
            if (!evento.target.closest('.lote-row-menu')) fecharTodosMenusLote(null);
        });

        // ==========================================
        // ORDENAÇÃO E PAGINAÇÃO DA LISTA DE LOTES (client-side, sobre os
        // lotes já filtrados pelo backend)
        // ==========================================
        (function () {
            const container = document.getElementById('cardsLotes');
            if (!container) return;

            const linhas = Array.from(container.querySelectorAll(':scope > .lote-row'));
            if (!linhas.length) return;

            const selectOrdenar = document.getElementById('ordenarLotes');
            const selectItensPorPagina = document.getElementById('itensPorPagina');
            const infoEl = document.getElementById('paginacaoInfo');
            const paginasEl = document.getElementById('paginacaoPaginas');

            let paginaAtual = 1;
            let itensPorPagina = selectItensPorPagina ? parseInt(selectItensPorPagina.value, 10) : 10;

            function ordenarLinhas(criterio) {
                const comparadores = {
                    nome: (a, b) => a.dataset.nome.localeCompare(b.dataset.nome, 'pt-BR'),
                    animais: (a, b) => Number(b.dataset.animais) - Number(a.dataset.animais),
                    peso: (a, b) => Number(b.dataset.peso) - Number(a.dataset.peso),
                    recentes: () => 0 // já vem do backend na ordem "mais recentes"
                };

                if (criterio !== 'recentes') {
                    linhas.sort(comparadores[criterio] || comparadores.recentes);
                    linhas.forEach((linha) => container.appendChild(linha));
                }
            }

            function renderizarPaginacao() {
                const total = linhas.length;
                const totalPaginas = Math.max(Math.ceil(total / itensPorPagina), 1);
                paginaAtual = Math.min(Math.max(paginaAtual, 1), totalPaginas);

                const inicio = (paginaAtual - 1) * itensPorPagina;
                const fim = inicio + itensPorPagina;

                linhas.forEach((linha, indice) => {
                    linha.style.display = (indice >= inicio && indice < fim) ? '' : 'none';
                });

                if (infoEl) {
                    infoEl.textContent = total === 0
                        ? 'Nenhum lote encontrado'
                        : `Mostrando ${inicio + 1} a ${Math.min(fim, total)} de ${total} lote${total === 1 ? '' : 's'}`;
                }

                if (paginasEl) {
                    let html = `<button type="button" data-pagina="${paginaAtual - 1}" ${paginaAtual === 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>`;
                    for (let p = 1; p <= totalPaginas; p++) {
                        html += `<button type="button" data-pagina="${p}" class="${p === paginaAtual ? 'ativo' : ''}">${p}</button>`;
                    }
                    html += `<button type="button" data-pagina="${paginaAtual + 1}" ${paginaAtual === totalPaginas ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button>`;
                    paginasEl.innerHTML = html;

                    paginasEl.querySelectorAll('button:not(:disabled)').forEach((botao) => {
                        botao.addEventListener('click', () => {
                            paginaAtual = parseInt(botao.dataset.pagina, 10);
                            renderizarPaginacao();
                        });
                    });
                }
            }

            if (selectOrdenar) {
                selectOrdenar.addEventListener('change', () => {
                    ordenarLinhas(selectOrdenar.value);
                    paginaAtual = 1;
                    renderizarPaginacao();
                });
            }

            if (selectItensPorPagina) {
                selectItensPorPagina.addEventListener('change', () => {
                    itensPorPagina = parseInt(selectItensPorPagina.value, 10);
                    paginaAtual = 1;
                    renderizarPaginacao();
                });
            }

            renderizarPaginacao();
        })();

        /* --- DICA DE PESO ESPERADO POR ESPÉCIE ---
           Só texto de apoio; quem bloqueia peso fora da faixa de verdade é
           o LoteRequest (withValidator), não este script. */
        function atualizarDicaPeso(tipoSelect, dicaEl) {
            if (!tipoSelect || !dicaEl) return;
            const faixa = FAIXA_PESO_POR_ANIMAL[tipoSelect.value];

            dicaEl.textContent = faixa
                ? `Peso esperado para ${tipoSelect.value}: ${faixa.min} kg a ${faixa.max} kg por animal.`
                : 'Selecione o tipo de animal para ver a faixa de peso esperada.';
        }

        function ligarDicaPeso(tipoId, dicaId) {
            const tipoSelect = document.getElementById(tipoId);
            const dicaEl = document.getElementById(dicaId);
            if (!tipoSelect || !dicaEl) return;

            tipoSelect.addEventListener('change', function() { atualizarDicaPeso(tipoSelect, dicaEl); });
        }

        ligarDicaPeso('tipo_animal', 'dica_peso');
        ligarDicaPeso('edit_tipo_animal', 'edit_dica_peso');

        function abrirModal() {
            document.getElementById('modal').classList.add('active');
        }

        function fecharModal() {
            document.getElementById('modal').classList.remove('active');
        }

        // ==========================================
        // FILTROS AVANÇADOS (mesmo padrão do Relatório/Ração/Estoque)
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

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modalFiltros) {
                modalFiltros.classList.remove('active');
            }
        });

        window.addEventListener('click', (event) => {
            if (event.target === modalFiltros) {
                modalFiltros.classList.remove('active');
            }
        });

        const PARAMS_POR_FILTRO = {
            fazenda_id: ['fazenda_id'],
            tipo_animal: ['tipo_animal'],
            finalidade: ['finalidade'],
            sexo: ['sexo']
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
                window.location.href = '{{ route('lote.listar') }}';
            });
        }

        function abrirModalEditar(id, nome, fazendaId, tipo, finalidade, sexo, idadeMedia, quantidade, peso, data, status) {
            document.getElementById('modalEditar').classList.add('active');

            document.getElementById('edit_nome_lote').value = nome;
            document.getElementById('edit_fazenda_id').value = fazendaId;
            document.getElementById('edit_tipo_animal').value = tipo;
            document.getElementById('edit_sexo').value = sexo;

            // Recalcula quais finalidades ficam habilitadas pro tipo/sexo
            // deste lote antes de marcar a finalidade salva — assim, mesmo
            // que ela tenha ficado desabilitada por uma mudança nas regras,
            // ela ainda aparece selecionada (só não dá pra escolher de novo
            // sem trocar por uma opção válida).
            atualizarFinalidades(
                document.getElementById('edit_tipo_animal'),
                document.getElementById('edit_sexo'),
                document.getElementById('edit_finalidade')
            );
            document.getElementById('edit_finalidade').value = finalidade;

            document.getElementById('edit_idade_media').value = idadeMedia;
            document.getElementById('edit_quantidade_animais').value = quantidade;
            document.getElementById('edit_peso_medio').value = peso;
            document.getElementById('edit_data_entrada').value = data;
            document.getElementById('edit_status').value = status || 'Ativo';
            document.getElementById('lote_id_edit').value = id;

            atualizarDicaPeso(document.getElementById('edit_tipo_animal'), document.getElementById('edit_dica_peso'));

            document.getElementById('formEditar').action = '/lote/update/' + id;
        }

        function fecharModalEditar() {
            document.getElementById('modalEditar').classList.remove('active');
        }

        const CAUSA_MORTE_DOENCA = 'Doença';
        let morteEhIsolamento = false;
        let morteEstoque = {};

        function atualizarDicaMorteDoenca() {
            const total = totalInformadoNaLista('morteDoencasLista');
            document.getElementById('morte_doenca_dica').textContent =
                total + (total === 1 ? ' animal selecionado no total' : ' animais selecionados no total');
        }

        function alternarCamposMorte() {
            const causa = document.getElementById('morte_causa').value;
            const ehOutra = causa === CAUSA_MORTE_OUTRA;
            const ehDoenca = causa === CAUSA_MORTE_DOENCA;
            const usarBlocoDoencas = morteEhIsolamento && ehDoenca;

            document.getElementById('campoMorteCausaOutra').hidden = !ehOutra;
            if (!ehOutra) document.getElementById('morte_causa_outra').value = '';

            document.getElementById('blocoMorteDoencas').hidden = !usarBlocoDoencas;
            document.getElementById('blocoMorteQuantidade').hidden = usarBlocoDoencas;

            // Os dois blocos moram no mesmo <form> — só esconder um deles
            // com "hidden" não impede que ele seja enviado junto (hidden
            // não é disabled). O campo "quantidade" solto tem o MESMO nome
            // dos campos "quantidade[]" da lista por doença; se os dois
            // forem enviados juntos, o valor solto sobrescreve o array no
            // PHP. "disabled" tira o campo do envio de vez.
            const campoQuantidadeSolta = document.getElementById('morte_quantidade');
            campoQuantidadeSolta.required = !usarBlocoDoencas;
            campoQuantidadeSolta.disabled = usarBlocoDoencas;

            if (usarBlocoDoencas) {
                construirLinhasEstoqueDoenca('morteDoencasLista', morteEstoque, atualizarDicaMorteDoenca);
                atualizarDicaMorteDoenca();
            } else {
                // Limpa linhas antigas (com doenca[]/quantidade[]) que
                // tenham sobrado de uma escolha anterior de "Doença" —
                // senão elas continuam no DOM e são enviadas junto, mesmo
                // com o bloco escondido.
                document.getElementById('morteDoencasLista').innerHTML = '';
            }

            const mostrarDoencaLivre = ehDoenca && !morteEhIsolamento;
            document.getElementById('campoMorteDoencaLivre').hidden = !mostrarDoencaLivre;
            if (!mostrarDoencaLivre) document.getElementById('morte_doenca_livre').value = '';
        }

        function validarFormMorte() {
            const causa = document.getElementById('morte_causa').value;
            if (morteEhIsolamento && causa === CAUSA_MORTE_DOENCA && totalInformadoNaLista('morteDoencasLista') < 1) {
                alert('Informe ao menos 1 óbito de alguma doença.');
                return false;
            }
            return true;
        }

        function abrirModalMorte(botao) {
            const id = botao.dataset.id;
            const nome = botao.dataset.nome;
            const quantidadeAtual = parseInt(botao.dataset.quantidade, 10) || 0;
            morteEhIsolamento = botao.dataset.isolamento === '1';
            morteEstoque = JSON.parse(botao.dataset.estoque || '{}');

            document.getElementById('modalMorte').classList.add('active');

            document.getElementById('morte_lote_nome').textContent = nome + ' — ' + quantidadeAtual + (quantidadeAtual == 1 ? ' animal ativo' : ' animais ativos');

            document.getElementById('morte_causa').value = '';
            document.getElementById('morte_causa_outra').value = '';
            // "Corte" não se aplica a um lote de isolamento — animal em
            // tratamento não está em produção pra abate.
            const opcaoCorte = document.getElementById('opcaoCausaCorte');
            if (opcaoCorte) opcaoCorte.hidden = morteEhIsolamento;
            document.getElementById('morte_doenca_livre').value = '';
            document.getElementById('campoMorteCausaOutra').hidden = true;
            document.getElementById('campoMorteDoencaLivre').hidden = true;
            document.getElementById('blocoMorteDoencas').hidden = true;
            document.getElementById('blocoMorteQuantidade').hidden = false;
            document.getElementById('morteDoencasLista').innerHTML = '';

            const campoQuantidade = document.getElementById('morte_quantidade');
            campoQuantidade.max = quantidadeAtual;
            campoQuantidade.value = 1;
            campoQuantidade.required = true;
            campoQuantidade.disabled = false;
            document.getElementById('morte_quantidade_dica').textContent = 'Máximo: ' + quantidadeAtual + (quantidadeAtual == 1 ? ' animal' : ' animais');

            document.getElementById('morte_data').value = '{{ $hoje }}';
            document.getElementById('morte_observacao').value = '';
            document.getElementById('morte_lote_id').value = id;

            document.getElementById('formMorte').action = '/lote/' + id + '/morte';
        }

        function fecharModalMorte() {
            document.getElementById('modalMorte').classList.remove('active');
        }

        // ------------------------------------------------------------
        // Lista "Animais doentes" (modal Registrar Doença): uma linha por
        // animal, cada uma com seu próprio select de doença — permite
        // registrar vários animais de uma vez, cada um com uma doença
        // diferente, num único envio.
        // ------------------------------------------------------------
        let doencaTipoAnimalAtual = null;
        let doencaQuantidadeMaxima = 1;
        let doencaContadorLinhas = 0;

        function construirSelectDoenca(tipoAnimal) {
            const select = document.createElement('select');
            select.name = 'doenca[]';
            select.required = true;
            select.className = 'doenca-select-linha';
            select.onchange = function() { alternarCampoOutraLinha(select); };

            const opcaoPadrao = document.createElement('option');
            opcaoPadrao.value = '';
            opcaoPadrao.disabled = true;
            opcaoPadrao.selected = true;
            opcaoPadrao.textContent = 'Selecione a doença...';
            select.appendChild(opcaoPadrao);

            (DOENCAS_POR_ANIMAL[tipoAnimal] || []).forEach(function(doenca) {
                const opcao = document.createElement('option');
                opcao.value = doenca;
                opcao.textContent = doenca;
                select.appendChild(opcao);
            });

            const opcaoOutra = document.createElement('option');
            opcaoOutra.value = DOENCA_OUTRA;
            opcaoOutra.textContent = 'Outra (especificar)';
            select.appendChild(opcaoOutra);

            return select;
        }

        function alternarCampoOutraLinha(select) {
            const linha = select.closest('.doenca-animal-row');
            const campoOutra = linha.querySelector('.doenca-outra-linha');
            const inputOutra = campoOutra.querySelector('input');
            const ehOutra = select.value === DOENCA_OUTRA;

            campoOutra.hidden = !ehOutra;
            if (!ehOutra) inputOutra.value = '';
        }

        function atualizarBotaoAdicionarAnimal() {
            const total = document.querySelectorAll('.doenca-animal-row').length;
            const botao = document.getElementById('btnAdicionarAnimalDoente');
            botao.disabled = total >= doencaQuantidadeMaxima;
            document.getElementById('doenca_quantidade_dica').textContent =
                total + ' de ' + doencaQuantidadeMaxima + (doencaQuantidadeMaxima == 1 ? ' animal selecionado' : ' animais selecionados');
        }

        function adicionarLinhaDoenca() {
            const lista = document.getElementById('doencaAnimaisLista');
            if (lista.querySelectorAll('.doenca-animal-row').length >= doencaQuantidadeMaxima) return;

            doencaContadorLinhas++;

            const linha = document.createElement('div');
            linha.className = 'doenca-animal-row';

            const numero = document.createElement('div');
            numero.className = 'numero-animal';
            numero.textContent = '#' + doencaContadorLinhas;

            const campos = document.createElement('div');
            campos.className = 'campos-linha';

            const inputBoxSelect = document.createElement('div');
            inputBoxSelect.className = 'input-box';
            const iconeVirus = document.createElement('i');
            iconeVirus.className = 'fa-solid fa-virus';
            const select = construirSelectDoenca(doencaTipoAnimalAtual);
            inputBoxSelect.appendChild(iconeVirus);
            inputBoxSelect.appendChild(select);

            const campoOutra = document.createElement('div');
            campoOutra.className = 'input-box doenca-outra-linha';
            campoOutra.hidden = true;
            const iconeCaneta = document.createElement('i');
            iconeCaneta.className = 'fa-solid fa-pen';
            const inputOutra = document.createElement('input');
            inputOutra.type = 'text';
            inputOutra.name = 'doenca_outra[]';
            inputOutra.placeholder = 'Digite o nome da doença';
            inputOutra.maxLength = 100;
            campoOutra.appendChild(iconeCaneta);
            campoOutra.appendChild(inputOutra);

            campos.appendChild(inputBoxSelect);
            campos.appendChild(campoOutra);

            const btnRemover = document.createElement('button');
            btnRemover.type = 'button';
            btnRemover.className = 'btn-remover-linha-doenca';
            btnRemover.title = 'Remover este animal';
            btnRemover.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btnRemover.onclick = function() { removerLinhaDoenca(linha); };

            linha.appendChild(numero);
            linha.appendChild(campos);
            linha.appendChild(btnRemover);

            lista.appendChild(linha);
            atualizarBotaoAdicionarAnimal();
        }

        function removerLinhaDoenca(linha) {
            const lista = document.getElementById('doencaAnimaisLista');
            if (lista.querySelectorAll('.doenca-animal-row').length <= 1) return;
            linha.remove();
            atualizarBotaoAdicionarAnimal();
        }

        function abrirModalDoenca(botao) {
            const id = botao.dataset.id;
            const nome = botao.dataset.nome;
            const quantidadeAtual = parseInt(botao.dataset.quantidade, 10) || 0;
            const tipoAnimal = botao.dataset.tipoAnimal;

            document.getElementById('modalDoenca').classList.add('active');

            document.getElementById('doenca_lote_nome').textContent = nome + ' — ' + quantidadeAtual + (quantidadeAtual == 1 ? ' animal ativo' : ' animais ativos');

            doencaTipoAnimalAtual = tipoAnimal;
            doencaQuantidadeMaxima = quantidadeAtual;
            doencaContadorLinhas = 0;

            document.getElementById('doencaAnimaisLista').innerHTML = '';
            adicionarLinhaDoenca();

            document.getElementById('doenca_destino_dica').textContent = 'Os animais doentes serão movidos para o lote "' + nome + ' - Doença" (criado automaticamente, se ainda não existir).';

            document.getElementById('doenca_data').value = '{{ $hoje }}';
            document.getElementById('doenca_observacao').value = '';
            document.getElementById('doenca_lote_id').value = id;

            document.getElementById('formDoenca').action = '/lote/' + id + '/doenca';
        }

        function fecharModalDoenca() {
            document.getElementById('modalDoenca').classList.remove('active');
        }

        // ------------------------------------------------------------
        // ANIMAIS DO LOTE — cadastro de animal, parto previsto e ordenha
        // agora são por animal individual (ver seção "Animais deste Lote"
        // dentro de "Ver detalhes"). Perfil completo (bezerros, marcar
        // parto realizado, encerrar lactação, histórico) fica na tela
        // própria do animal (animal.detalhe).
        // ------------------------------------------------------------
        function fecharModalPorId(id) {
            document.getElementById(id).classList.remove('active');
        }

        function abrirModalAnimal(loteId, nomeLote) {
            document.getElementById('animal_lote_nome').textContent = nomeLote;
            document.getElementById('animal_lote_id').value = loteId;
            document.getElementById('animal_nome').value = '';
            document.getElementById('animal_identificador').value = '';
            document.getElementById('animal_sexo').value = '';
            document.getElementById('animal_raca').value = '';
            document.getElementById('animal_data_nascimento').value = '';
            document.getElementById('animal_peso').value = '';
            document.getElementById('modalAnimal').classList.add('active');
        }

        function abrirModalPartoAnimal(animalId, nomeAnimal) {
            document.getElementById('partoAnimal_nome').textContent = nomeAnimal;
            document.getElementById('partoAnimal_data_prevista').value = '';
            document.getElementById('partoAnimal_observacoes').value = '';
            document.getElementById('formPartoAnimal').action = '/animal/' + animalId + '/parto';
            document.getElementById('modalPartoAnimal').classList.add('active');
        }

        function abrirModalOrdenhaAnimal(animalId, nomeAnimal) {
            document.getElementById('ordenhaAnimal_nome').textContent = nomeAnimal;
            document.getElementById('ordenhaAnimal_data').value = '{{ $hoje }}';
            document.getElementById('ordenhaAnimal_horario').value = '';
            document.getElementById('ordenhaAnimal_turno').value = '';
            document.getElementById('ordenhaAnimal_litros').value = '';
            document.getElementById('ordenhaAnimal_observacoes').value = '';
            document.getElementById('formOrdenhaAnimal').action = '/animal/' + animalId + '/ordenha';
            document.getElementById('modalOrdenhaAnimal').classList.add('active');
        }

        // ------------------------------------------------------------
        // Compartilhado entre "Devolver ao Lote" e "Registrar Óbito" (num
        // lote de isolamento): uma linha fixa por doença hoje ativa,
        // cada uma com um campo numérico limitado ao que ainda resta
        // daquela doença especificamente.
        // ------------------------------------------------------------
        function construirLinhasEstoqueDoenca(idContainer, estoque, aoDigitar) {
            const lista = document.getElementById(idContainer);
            lista.innerHTML = '';

            Object.keys(estoque).forEach(function(doenca) {
                const maximo = estoque[doenca];

                const linha = document.createElement('div');
                linha.className = 'doenca-estoque-row';

                const info = document.createElement('div');
                info.className = 'doenca-estoque-info';

                const nomeEl = document.createElement('span');
                nomeEl.className = 'doenca-estoque-nome';
                nomeEl.textContent = doenca;

                const maxEl = document.createElement('span');
                maxEl.className = 'doenca-estoque-max';
                maxEl.textContent = maximo + (maximo === 1 ? ' animal ainda doente' : ' animais ainda doentes');

                info.appendChild(nomeEl);
                info.appendChild(maxEl);

                const inputDoenca = document.createElement('input');
                inputDoenca.type = 'hidden';
                inputDoenca.name = 'doenca[]';
                inputDoenca.value = doenca;

                const inputQuantidade = document.createElement('input');
                inputQuantidade.type = 'number';
                inputQuantidade.name = 'quantidade[]';
                inputQuantidade.min = 0;
                inputQuantidade.max = maximo;
                inputQuantidade.value = 0;
                inputQuantidade.oninput = aoDigitar;

                linha.appendChild(info);
                linha.appendChild(inputDoenca);
                linha.appendChild(inputQuantidade);

                lista.appendChild(linha);
            });
        }

        /** Depois que construirLinhasEstoqueDoenca() já montou as linhas
         *  (com os máximos atuais), preenche de volta os valores que o
         *  usuário tinha digitado antes de um envio que falhou. */
        function restaurarQuantidadesPorDoenca(idContainer, doencasAntigas, quantidadesAntigas) {
            if (!Array.isArray(doencasAntigas)) return;

            document.querySelectorAll('#' + idContainer + ' .doenca-estoque-row').forEach(function(linha) {
                const doencaValor = linha.querySelector('input[type="hidden"]').value;
                const indice = doencasAntigas.indexOf(doencaValor);
                if (indice !== -1) {
                    linha.querySelector('input[type="number"]').value = quantidadesAntigas[indice] || 0;
                }
            });
        }

        function totalInformadoNaLista(idContainer) {
            const inputs = document.querySelectorAll('#' + idContainer + ' input[type="number"]');
            let total = 0;
            inputs.forEach(function(input) { total += parseInt(input.value, 10) || 0; });
            return total;
        }

        function atualizarDicaRecuperacao() {
            const total = totalInformadoNaLista('recuperacaoDoencasLista');
            document.getElementById('recuperacao_quantidade_dica').textContent =
                total + (total === 1 ? ' animal selecionado no total' : ' animais selecionados no total');
        }

        function validarFormRecuperacao() {
            if (totalInformadoNaLista('recuperacaoDoencasLista') < 1) {
                alert('Informe ao menos 1 animal recuperado de alguma doença.');
                return false;
            }
            return true;
        }

        function abrirModalRecuperacao(botao) {
            const id = botao.dataset.id;
            const nome = botao.dataset.nome;
            const quantidadeAtual = parseInt(botao.dataset.quantidade, 10) || 0;
            const nomeOrigem = botao.dataset.origem;
            const estoque = JSON.parse(botao.dataset.estoque || '{}');

            document.getElementById('modalRecuperacao').classList.add('active');

            document.getElementById('recuperacao_lote_nome').textContent = nome + ' — ' + quantidadeAtual + (quantidadeAtual == 1 ? ' animal em tratamento' : ' animais em tratamento');
            document.getElementById('recuperacao_destino_fixo').innerHTML = '<i class="fa-solid fa-circle-info"></i> Só podem voltar para "' + nomeOrigem + '" (lote de origem)';

            construirLinhasEstoqueDoenca('recuperacaoDoencasLista', estoque, atualizarDicaRecuperacao);
            atualizarDicaRecuperacao();

            document.getElementById('recuperacao_data').value = '{{ $hoje }}';
            document.getElementById('recuperacao_observacao').value = '';
            document.getElementById('recuperacao_lote_id').value = id;

            document.getElementById('formRecuperacao').action = '/lote/' + id + '/recuperacao';
        }

        function fecharModalRecuperacao() {
            document.getElementById('modalRecuperacao').classList.remove('active');
        }

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modal');
            const modalEditar = document.getElementById('modalEditar');
            const modalMorte = document.getElementById('modalMorte');
            const modalDoenca = document.getElementById('modalDoenca');
            const modalRecuperacao = document.getElementById('modalRecuperacao');
            const modalOrdenha = document.getElementById('modalOrdenha');
            const modalParto = document.getElementById('modalParto');

            if (event.target === modal) fecharModal();
            if (event.target === modalEditar) fecharModalEditar();
            if (event.target === modalMorte) fecharModalMorte();
            if (event.target === modalDoenca) fecharModalDoenca();
            if (event.target === modalRecuperacao) fecharModalRecuperacao();
            if (event.target === modalOrdenha) fecharModalOrdenha();
            if (event.target === modalParto) fecharModalParto();
        });

        /* --- REABRIR O MODAL CERTO QUANDO O CADASTRO/EDIÇÃO FALHA ---
           Sem isso, um erro de validação (ex.: "macho não pode ter
           finalidade Leite") faz a página recarregar com o modal fechado —
           o usuário vê o aviso lá em cima, mas perde de vista qual campo
           precisa corrigir. O formulário de Adicionar já vem preenchido de
           novo via old() no Blade; só falta abrir o modal. O de Editar não
           usa old() nos campos (eles são preenchidos por abrirModalEditar),
           então aqui a gente repete os mesmos valores enviados. */
        const TEM_ERROS_VALIDACAO = @json($errors->any());
        const ORIGEM_FORMULARIO_ERRO = @json(old('origem_formulario'));
        const OLD_INPUT = @json(old());

        document.addEventListener('DOMContentLoaded', function() {
            if (!TEM_ERROS_VALIDACAO) return;

            if (ORIGEM_FORMULARIO_ERRO === 'edit') {
                const idEditando = OLD_INPUT.lote_id_edit;
                if (!idEditando) return;

                document.getElementById('edit_nome_lote').value = OLD_INPUT.nome_lote || '';
                document.getElementById('edit_fazenda_id').value = OLD_INPUT.fazenda_id || '';
                document.getElementById('edit_tipo_animal').value = OLD_INPUT.tipo_animal || '';
                document.getElementById('edit_sexo').value = OLD_INPUT.sexo || '';

                atualizarFinalidades(
                    document.getElementById('edit_tipo_animal'),
                    document.getElementById('edit_sexo'),
                    document.getElementById('edit_finalidade')
                );
                document.getElementById('edit_finalidade').value = OLD_INPUT.finalidade || '';

                document.getElementById('edit_idade_media').value = OLD_INPUT.idade_media || '';
                document.getElementById('edit_quantidade_animais').value = OLD_INPUT.quantidade_animais || '';
                document.getElementById('edit_peso_medio').value = OLD_INPUT.peso_medio || '';
                document.getElementById('edit_data_entrada').value = OLD_INPUT.data_entrada || '';
                document.getElementById('edit_status').value = OLD_INPUT.status || 'Ativo';
                document.getElementById('lote_id_edit').value = idEditando;

                atualizarDicaPeso(document.getElementById('edit_tipo_animal'), document.getElementById('edit_dica_peso'));

                document.getElementById('formEditar').action = '/lote/update/' + idEditando;
                abrirModalEditarSemPreencher();
            } else if (ORIGEM_FORMULARIO_ERRO === 'morte') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('.lote-row-dropdown button[onclick="abrirModalMorte(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalMorte(botao);

                document.getElementById('morte_causa').value = OLD_INPUT.causa || '';
                alternarCamposMorte();

                if (OLD_INPUT.causa === CAUSA_MORTE_OUTRA) {
                    document.getElementById('morte_causa_outra').value = OLD_INPUT.causa_outra || '';
                }

                if (morteEhIsolamento && OLD_INPUT.causa === CAUSA_MORTE_DOENCA) {
                    restaurarQuantidadesPorDoenca('morteDoencasLista', OLD_INPUT.doenca, OLD_INPUT.quantidade);
                    atualizarDicaMorteDoenca();
                } else {
                    document.getElementById('morte_quantidade').value = OLD_INPUT.quantidade || 1;
                    document.getElementById('morte_doenca_livre').value = OLD_INPUT.doenca_livre || '';
                }

                document.getElementById('morte_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('morte_observacao').value = OLD_INPUT.observacao || '';
            } else if (ORIGEM_FORMULARIO_ERRO === 'doenca') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('.lote-row-dropdown button[onclick="abrirModalDoenca(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalDoenca(botao);

                const doencasAntigas = OLD_INPUT.doenca;
                const outrasAntigas = OLD_INPUT.doenca_outra;
                if (Array.isArray(doencasAntigas) && doencasAntigas.length > 0) {
                    document.getElementById('doencaAnimaisLista').innerHTML = '';
                    doencaContadorLinhas = 0;
                    doencasAntigas.forEach(function(doencaValor, indice) {
                        adicionarLinhaDoenca();
                        const linhas = document.querySelectorAll('#doencaAnimaisLista .doenca-animal-row');
                        const linhaAtual = linhas[linhas.length - 1];
                        const select = linhaAtual.querySelector('.doenca-select-linha');
                        select.value = doencaValor;
                        alternarCampoOutraLinha(select);
                        if (doencaValor === DOENCA_OUTRA && outrasAntigas && outrasAntigas[indice]) {
                            linhaAtual.querySelector('.doenca-outra-linha input').value = outrasAntigas[indice];
                        }
                    });
                }

                document.getElementById('doenca_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('doenca_observacao').value = OLD_INPUT.observacao || '';
            } else if (ORIGEM_FORMULARIO_ERRO === 'recuperacao') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('.lote-row-dropdown button[onclick="abrirModalRecuperacao(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalRecuperacao(botao);
                restaurarQuantidadesPorDoenca('recuperacaoDoencasLista', OLD_INPUT.doenca, OLD_INPUT.quantidade);
                atualizarDicaRecuperacao();

                document.getElementById('recuperacao_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('recuperacao_observacao').value = OLD_INPUT.observacao || '';
            } else if (ORIGEM_FORMULARIO_ERRO === 'ordenha') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('.lote-row-dropdown button[onclick="abrirModalOrdenha(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalOrdenha(botao);

                document.getElementById('ordenha_quantidade_litros').value = OLD_INPUT.quantidade_litros || '';
                document.getElementById('ordenha_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('ordenha_observacao').value = OLD_INPUT.observacao || '';
            } else if (ORIGEM_FORMULARIO_ERRO === 'parto') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('.lote-row-dropdown button[onclick="abrirModalParto(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalParto(botao);

                document.getElementById('parto_quantidade_nascidos').value = OLD_INPUT.quantidade_nascidos || 1;
                document.getElementById('parto_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('parto_observacao').value = OLD_INPUT.observacao || '';
            } else {
                // Formulário de Adicionar: os campos já vêm preenchidos via
                // old() direto no Blade, só falta atualizar quais opções de
                // finalidade ficam habilitadas e abrir o modal.
                atualizarFinalidades(
                    document.getElementById('tipo_animal'),
                    document.getElementById('sexo'),
                    document.getElementById('finalidade')
                );
                atualizarDicaPeso(document.getElementById('tipo_animal'), document.getElementById('dica_peso'));
                abrirModal();
            }
        });

        function abrirModalEditarSemPreencher() {
            document.getElementById('modalEditar').classList.add('active');
        }
    </script>
</body>
</html>
