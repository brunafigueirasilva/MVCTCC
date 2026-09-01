<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | Gestão de Cochos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ==========================================================================
        1. RESET, VARIÁVEIS DE SISTEMA (idêntico ao Dashboard/Fazendas/Lotes)
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

        .sidebar, .navbar, .main, .cocho-row {
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
        5. FILTROS AVANÇADOS (mesmo padrão do Relatório/Ração/Estoque/Lote)
        ========================================================================== */
        .top-bar-actions { display: flex; align-items: center; gap: 12px; }

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

        .filtros-form small {
            color: var(--text-secundario);
            font-size: 0.71875rem;
        }

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
        6. LISTAGEM DE COCHOS (barra de estatísticas + linhas expansíveis)
        ========================================================================== */
        .cochos-toolbar {
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
            font-size: 0.9375rem;
            flex-shrink: 0;
            color: #ffffff;
        }

        .stat-pill-total .stat-pill-icone {
            background: linear-gradient(145deg, #60a5fa, #2563eb);
            box-shadow: 0 6px 13px -4px rgba(37, 99, 235, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }
        .stat-pill-ok .stat-pill-icone {
            background: linear-gradient(145deg, #4ade80, #059669);
            box-shadow: 0 6px 13px -4px rgba(5, 150, 105, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }
        .stat-pill-alerta .stat-pill-icone {
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

        .cochos-lista {
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 16px;
            /* Sem "overflow: hidden" aqui de propósito — ele cortava o menu
               "⋮" (Editar/Excluir), que é position:absolute e abre pra
               baixo da linha. As bordas arredondadas ficam por conta das
               regras abaixo, aplicadas só na primeira/última linha. */
        }

        .cocho-row { border-bottom: 1px solid var(--surface-muted-border); transition: background 0.15s ease; }
        .cocho-row:last-child { border-bottom: none; }
        .cocho-row:first-child { border-radius: 16px 16px 0 0; }
        .cocho-row:last-child { border-radius: 0 0 16px 16px; }
        .cocho-row:hover { background: var(--surface-muted-bg); }

        .cocho-row-main {
            display: flex;
            align-items: center;
            gap: 22px;
            padding: 16px 20px;
            flex-wrap: wrap;
        }

        .cocho-row-id { display: flex; align-items: center; gap: 14px; min-width: 200px; flex: 1.4 1 200px; }

        .icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(145deg, #4ade80, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.0625rem;
            flex-shrink: 0;
            box-shadow: 0 6px 14px -4px rgba(5, 150, 105, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        .cocho-row-nome { color: var(--text-principal); font-size: 0.9375rem; font-weight: 700; overflow-wrap: break-word; }

        .cocho-row-lote {
            color: var(--text-secundario);
            font-size: 0.78125rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .cocho-row-lote::before {
            content: '\f247';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.6875rem;
            color: var(--card-verde-texto);
            opacity: 0.7;
        }

        .cocho-row-col {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 100px;
        }

        .cocho-row-col-label {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secundario);
            font-weight: 700;
        }

        .cocho-row-col-valor { font-size: 0.90625rem; color: var(--text-principal); font-weight: 600; }

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

        .status-badge.status-ok { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .status-badge.status-baixo { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-badge.status-vazio { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .status-badge.status-indefinido { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        body.dark-theme .status-badge.status-ok { background: rgba(74, 222, 128, 0.12); border-color: rgba(74, 222, 128, 0.25); color: #4ade80; }
        body.dark-theme .status-badge.status-baixo { background: rgba(251, 191, 36, 0.12); border-color: rgba(251, 191, 36, 0.25); color: #fbbf24; }
        body.dark-theme .status-badge.status-vazio { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .status-badge.status-indefinido { background: rgba(148, 163, 184, 0.14); border-color: rgba(148, 163, 184, 0.3); color: #cbd5e1; }


        .cocho-row-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

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

        .cocho-row-menu { position: relative; }

        .btn-menu-cocho {
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

        .btn-menu-cocho:hover { background: var(--surface-muted-bg); }

        .cocho-row-dropdown {
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

        .cocho-row-dropdown.aberto { display: block; }

        .cocho-row-dropdown button {
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

        .cocho-row-dropdown button:hover { background: var(--surface-muted-bg); }
        .cocho-row-dropdown button.excluir { color: #dc2626; }

        .cocho-row-detalhes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 10px;
            padding: 16px 18px 18px 76px;
            background: var(--surface-muted-bg);
            border-top: 1px dashed var(--surface-muted-border);
            animation: detalhesLinhaAparecer 0.2s ease;
        }

        @keyframes detalhesLinhaAparecer {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cocho-row-detalhes[hidden] { display: none; }

        /* Acompanha o arredondamento da última linha (agora que a lista não
           usa mais "overflow: hidden") quando o painel de detalhes dela
           está aberto. */
        .cocho-row:last-child .cocho-row-detalhes { border-radius: 0 0 16px 16px; }

        /* Lista simples (sem cartão nem ícone grande por item) — só um
           ícone pequeno e "rótulo: valor" numa linha só. */
        .cocho-row-detalhe-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 3px 0;
        }

        .cocho-row-detalhe-icone {
            width: 16px;
            color: var(--text-secundario);
            font-size: 0.75rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cocho-row-detalhe-texto { display: flex; align-items: baseline; gap: 5px; min-width: 0; }

        .cocho-row-detalhes span::after { content: ':'; }

        .cocho-row-detalhes span {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
            font-weight: 600;
        }

        .cocho-row-detalhes strong {
            font-size: 0.875rem;
            color: var(--text-principal);
            font-weight: 700;
            overflow-wrap: break-word;
        }

        .cochos-paginacao {
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
        7. MODAIS (Adicionar / Atualizar Cocho)
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

        /* Select de Lote fica desabilitado até uma Fazenda ser escolhida —
           evita listar/selecionar lotes de fazendas diferentes por engano
           (ex.: dois lotes chamados "A1" em fazendas distintas). */
        .input-box select:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .row { display: flex; gap: 12px; }
        .campo { flex: 1; }

        .dica-campo {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secundario);
            margin: -12px 0 16px;
        }

        /* Prévia do lote (tipo/sexo/idade/finalidade) puxado do lote
           escolhido — mesmo padrão do cadastro de Ração, confirma
           visualmente pro usuário quem é aquele lote antes de salvar. */
        .lote-preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            padding: 12px 14px;
            margin: -4px 0 18px;
            font-size: 0.8125rem;
            color: var(--text-principal);
        }

        .lote-preview i {
            color: #16a34a;
            width: 16px;
        }

        .lote-preview strong {
            font-weight: 600;
            margin-right: 4px;
        }

        /* Sem isso, o `hidden` do HTML perde pro `display: grid` acima
           (mesma especificidade, mas regra de autor vence a do navegador) e
           o painel de prévia ficava sempre visível, vazio, antes de
           escolher um lote. */
        .lote-preview[hidden] { display: none; }

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

        /* ==========================================================================
        8. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard/Fazendas/Lotes
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

        body.high-contrast .cochos-lista,
        body.high-contrast .cocho-row,
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

        body.high-contrast .menu a.active,
        body.high-contrast .menu a:hover { background: #1a1a00 !important; }

        body.high-contrast .toggle-menu-btn,
        body.high-contrast .user-avatar,
        body.high-contrast .icon {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }

        /* Ícones dentro de "pílulas"/cartões (estatísticas do topo, itens do
           painel de detalhes) usam fundos pastel via variável — a variável
           não muda no alto contraste, então esses círculos ficavam
           coloridos e ilegíveis num fundo totalmente preto. */
        body.high-contrast .stat-pill-icone,
        body.high-contrast .cocho-row-detalhe-icone {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .cocho-row-detalhe-item {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* Painel do "Ver detalhes": o fundo dele usa var(--surface-muted-bg),
           que o alto contraste não redefine, então ficava cinza-claro atrás
           dos cartõezinhos pretos. */
        body.high-contrast .cocho-row-detalhes {
            background: #000000 !important;
            border-top: 1px dashed #ffff00 !important;
        }

        body.high-contrast .cocho-row-lote::before { color: #ffff00 !important; opacity: 1; }

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
        body.high-contrast .btn-menu-cocho,
        body.high-contrast .cocho-row-dropdown {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .cocho-row-dropdown button { color: #ffffff !important; }
        body.high-contrast .cocho-row-dropdown button.excluir { color: #ff6b6b !important; }

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
        body.high-contrast .paginacao-itens select {
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

            .cochos-toolbar { align-items: stretch; }
            .toolbar-ordenar { justify-content: space-between; }

            .cocho-row-main { align-items: flex-start; }
            .cocho-row-id { flex: 1 1 100%; }
            .cocho-row-actions { margin-left: 0; width: 100%; justify-content: space-between; }
            .cocho-row-detalhes { padding-left: 20px; }
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header-title p { font-size: 0.844rem; }
            .top-bar-actions { flex-direction: column; align-items: stretch; width: 100%; }

            .stat-pills { flex-direction: column; }
            .stat-pill { width: 100%; }

            .cocho-row-main { padding: 14px; gap: 12px; }
            .cocho-row-col { min-width: 0; flex: 1 1 45%; }
            .cocho-row-detalhes { grid-template-columns: 1fr; padding: 14px 14px 16px; }

            .cochos-paginacao { flex-direction: column; align-items: stretch; text-align: center; }
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
            <a href="{{ route('lote.listar') }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>Gestão de Lotes</span>
            </a>
            <a href="{{ route('cocho.listar') }}" class="active">
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
                <h2>Gestão de Cochos</h2>
                <p>Acompanhe a eficiência e o status de abastecimento dos cochos de cada lote.</p>
            </div>

            <div class="top-bar-actions">
                <button class="btn-abrir-filtros" id="openFiltrosBtn">
                    <i class="fa-solid fa-sliders"></i> Filtros Avançados
                    <span class="filtros-badge {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}">{{ count($filtrosAplicados ?? []) }}</span>
                </button>
                <button class="btn-add" onclick="abrirModal()">
                    <i class="fa-solid fa-plus"></i> Adicionar Cocho
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
                    <p>Escolha o que exibir no painel de Cochos</p>
                </div>

                <form action="{{ route('cocho.listar') }}" method="GET" class="filtros-form">
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
                        <label for="filtro_lote_id">Lote</label>
                        <select id="filtro_lote_id" name="lote_id">
                            <option value="">Todos os Lotes</option>
                            @foreach ($lotes as $lote)
                                <option value="{{ $lote->id }}" data-fazenda="{{ $lote->fazenda_id }}" @selected(($filtros['lote_id'] ?? '') == $lote->id)>{{ $lote->nome_lote }}</option>
                            @endforeach
                        </select>
                        <small id="loteFiltroHint" style="display:none;">Mostrando apenas os lotes da fazenda selecionada.</small>
                    </div>

                    <div class="form-group">
                        <label for="filtro_status">Status</label>
                        <select id="filtro_status" name="status">
                            <option value="">Todos</option>
                            <option value="OK" @selected(($filtros['status'] ?? '') === 'OK')>OK</option>
                            <option value="Baixo" @selected(($filtros['status'] ?? '') === 'Baixo')>Baixo</option>
                            <option value="Vazio" @selected(($filtros['status'] ?? '') === 'Vazio')>Vazio</option>
                        </select>
                    </div>

                    <div class="filtros-modal-footer">
                        <button type="submit" class="btn-aplicar">
                            <i class="fa-solid fa-check"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('cocho.listar') }}" class="btn-limpar">
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
                    <strong>Não foi possível salvar o cocho:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $totalCochos = $cochos->count();
            $totalOk = $cochos->where('status_abastecimento', 'OK')->count();
            $totalAlerta = $totalCochos - $totalOk;
        @endphp

        <div class="cochos-toolbar">
            <div class="stat-pills">
                <div class="stat-pill stat-pill-total">
                    <div class="stat-pill-icone"><i class="fa-solid fa-cube"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalCochos }}</div>
                        <div class="stat-pill-label">Total de cochos</div>
                    </div>
                </div>
                <div class="stat-pill stat-pill-ok">
                    <div class="stat-pill-icone"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalOk }}</div>
                        <div class="stat-pill-label">Cochos OK</div>
                    </div>
                </div>
                <div class="stat-pill stat-pill-alerta">
                    <div class="stat-pill-icone"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalAlerta }}</div>
                        <div class="stat-pill-label">Com alerta</div>
                    </div>
                </div>
            </div>

            @if ($totalCochos > 0)
                <div class="toolbar-ordenar">
                    <label for="ordenarCochos">Ordenar por:</label>
                    <select id="ordenarCochos">
                        <option value="recentes">Mais recentes</option>
                        <option value="nome">Nome (A-Z)</option>
                        <option value="nivel">Maior nível atual</option>
                        <option value="capacidade">Maior capacidade</option>
                    </select>
                </div>
            @endif
        </div>

        <div class="cochos-lista" id="cardsCochos">
            @forelse ($cochos as $cocho)
                <div class="cocho-row"
                    data-nome="{{ strtolower($cocho->nome_cocho) }}"
                    data-nivel="{{ (float) $cocho->nivel_atual }}"
                    data-capacidade="{{ (float) $cocho->capacidade }}">
                    <div class="cocho-row-main">
                        <div class="cocho-row-id">
                            <div class="icon"><i class="fa-solid fa-cube"></i></div>
                            <div>
                                <div class="cocho-row-nome">{{ $cocho->nome_cocho }}</div>
                                <div class="cocho-row-lote">{{ $cocho->lote->nome_lote ?? 'Sem lote' }}</div>
                            </div>
                        </div>

                        <div class="cocho-row-col">
                            <span class="cocho-row-col-label">Tipo de animal</span>
                            <span class="cocho-row-col-valor">{{ $cocho->lote->tipo_animal ?? '—' }}</span>
                        </div>

                        <div class="cocho-row-col">
                            <span class="cocho-row-col-label">Quantidade</span>
                            <span class="cocho-row-col-valor">{{ $cocho->lote->quantidade_animais ?? 0 }} {{ ($cocho->lote->quantidade_animais ?? 0) == 1 ? 'animal' : 'animais' }}</span>
                        </div>

                        <div class="cocho-row-col">
                            <span class="cocho-row-col-label">Capacidade</span>
                            <span class="cocho-row-col-valor">{{ number_format($cocho->capacidade, 2, ',', '.') }} kg</span>
                        </div>

                        <div class="cocho-row-col">
                            <span class="cocho-row-col-label">Nível atual</span>
                            <span class="cocho-row-col-valor">{{ number_format($cocho->nivel_atual, 2, ',', '.') }} kg</span>
                        </div>

                        <div class="cocho-row-col">
                            <span class="cocho-row-col-label">Status</span>
                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($cocho->status_abastecimento) }}">
                                <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> {{ $cocho->status_abastecimento }}
                            </span>
                        </div>

                        <div class="cocho-row-actions">
                            <button type="button" class="btn-ver-detalhes" onclick="alternarDetalhesCocho(this)">
                                <span class="texto-btn-detalhes">Ver detalhes</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <div class="cocho-row-menu">
                                <button type="button" class="btn-menu-cocho" onclick="alternarMenuCocho(this)" title="Mais ações">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="cocho-row-dropdown">
                                    <button type="button" onclick="abrirModalEditar('{{ $cocho->id }}', '{{ addslashes($cocho->nome_cocho) }}', '{{ $cocho->capacidade }}', '{{ $cocho->nivel_atual }}', '{{ addslashes($cocho->sensor) }}', '{{ $cocho->lote_id }}', '{{ $cocho->lote->fazenda_id ?? '' }}')">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </button>
                                    <form action="{{ route('cocho.delete', $cocho->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este cocho?')">
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

                    <div class="cocho-row-detalhes" hidden>
                        <div class="cocho-row-detalhe-item">
                            <div class="cocho-row-detalhe-icone"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="cocho-row-detalhe-texto">
                                <span>Fazenda</span>
                                <strong>{{ $cocho->lote->fazenda->nome_fazenda ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="cocho-row-detalhe-item">
                            <div class="cocho-row-detalhe-icone"><i class="fa-solid fa-layer-group"></i></div>
                            <div class="cocho-row-detalhe-texto">
                                <span>Lote</span>
                                <strong>{{ $cocho->lote->nome_lote ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="cocho-row-detalhe-item">
                            <div class="cocho-row-detalhe-icone"><i class="fa-solid fa-microchip"></i></div>
                            <div class="cocho-row-detalhe-texto">
                                <span>Sensor</span>
                                <strong>{{ $cocho->sensor ?? '—' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="cards-empty">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                    <p>
                        @if (collect($filtros)->filter()->isNotEmpty())
                            Nenhum cocho encontrado com esses filtros.<br>Tente ajustar ou limpar os filtros.
                        @else
                            Você ainda não cadastrou nenhum cocho.<br>Clique em "Adicionar Cocho" para começar.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if ($totalCochos > 0)
            <div class="cochos-paginacao" id="cochosPaginacao">
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

    <!-- MODAL: ADICIONAR COCHO -->
    <div class="modal" id="modal">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Adicionar Cocho</p>
            </div>

            <form action="{{ route('cocho.add') }}" method="POST">
                @csrf

                <label>Nome do Cocho</label>
                <div class="input-box">
                    <i class="fa-solid fa-cube"></i>
                    <input type="text" name="nome_cocho" placeholder="Cocho B1" required>
                </div>

                <div class="row">
                    <div class="campo">
                        <label>Capacidade (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bowl-food"></i>
                            <input type="number" step="0.01" name="capacidade" placeholder="500" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label>Nível Atual (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" name="nivel_atual" placeholder="380.50" required>
                        </div>
                    </div>
                </div>

                <label for="cocho_fazenda_id">Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <select id="cocho_fazenda_id" onchange="aplicarFiltroFazendaLoteCocho('cocho_fazenda_id', 'lote_id')">
                        <option value="">Escolha a fazenda...</option>
                        @foreach ($fazendas as $fazenda)
                            <option value="{{ $fazenda->id }}">{{ $fazenda->nome_fazenda }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="lote_id">Selecione o Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-layer-group"></i>
                    <select id="lote_id" name="lote_id" required onchange="atualizarPreviewLoteCocho(this, '')">
                        <option value="" disabled selected>Escolha primeiro a fazenda...</option>
                        @foreach ($lotes as $lote)
                            <option value="{{ $lote->id }}"
                                data-fazenda="{{ $lote->fazenda_id }}"
                                data-tipo="{{ $lote->tipo_animal }}"
                                data-sexo="{{ $lote->sexo }}"
                                data-idade="{{ $lote->idade_media }}"
                                data-finalidade="{{ $lote->finalidade }}">
                                {{ $lote->nome_lote }} · {{ $lote->fazenda->nome_fazenda ?? 'Sem fazenda' }} · {{ $lote->tipo_animal ?? '—' }} · {{ $lote->quantidade_animais ?? 0 }} {{ ($lote->quantidade_animais ?? 0) == 1 ? 'animal' : 'animais' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="dica-campo">Tipo, sexo, idade e finalidade vêm automaticamente do lote escolhido.</small>

                <div class="lote-preview" id="lotePreview" hidden>
                    <div><i class="fa-solid fa-cow"></i> <strong>Tipo:</strong> <span id="previewTipo"></span></div>
                    <div><i class="fa-solid fa-venus-mars"></i> <strong>Sexo:</strong> <span id="previewSexo"></span></div>
                    <div><i class="fa-solid fa-hourglass-half"></i> <strong>Idade:</strong> <span id="previewIdade"></span></div>
                    <div><i class="fa-solid fa-bullseye"></i> <strong>Finalidade:</strong> <span id="previewFinalidade"></span></div>
                </div>

                <label>Sensor</label>
                <div class="input-box">
                    <i class="fa-solid fa-microchip"></i>
                    <input type="text" name="sensor" placeholder="Código do Sensor" required>
                </div>

                <button type="submit" class="submit-btn">Cadastrar Cocho</button>
            </form>
        </div>
    </div>

    <!-- MODAL: ATUALIZAR COCHO -->
    <div class="modal" id="modalEditar">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalEditar()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Atualizar Cocho</p>
            </div>

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')

                <label>Nome do Cocho</label>
                <div class="input-box">
                    <i class="fa-solid fa-cube"></i>
                    <input type="text" id="edit_nome_cocho" name="nome_cocho" required>
                </div>

                <div class="row">
                    <div class="campo">
                        <label>Capacidade (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bowl-food"></i>
                            <input type="number" step="0.01" id="edit_capacidade" name="capacidade" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label>Nível Atual (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" id="edit_nivel_atual" name="nivel_atual" required>
                        </div>
                    </div>
                </div>

                <label for="edit_cocho_fazenda_id">Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <select id="edit_cocho_fazenda_id" onchange="aplicarFiltroFazendaLoteCocho('edit_cocho_fazenda_id', 'edit_lote_id')">
                        <option value="">Escolha a fazenda...</option>
                        @foreach ($fazendas as $fazenda)
                            <option value="{{ $fazenda->id }}">{{ $fazenda->nome_fazenda }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="edit_lote_id">Selecione o Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-layer-group"></i>
                    <select id="edit_lote_id" name="lote_id" required onchange="atualizarPreviewLoteCocho(this, 'edit_')">
                        <option value="" disabled>Escolha primeiro a fazenda...</option>
                        @foreach ($lotes as $lote)
                            <option value="{{ $lote->id }}"
                                data-fazenda="{{ $lote->fazenda_id }}"
                                data-tipo="{{ $lote->tipo_animal }}"
                                data-sexo="{{ $lote->sexo }}"
                                data-idade="{{ $lote->idade_media }}"
                                data-finalidade="{{ $lote->finalidade }}">
                                {{ $lote->nome_lote }} · {{ $lote->fazenda->nome_fazenda ?? 'Sem fazenda' }} · {{ $lote->tipo_animal ?? '—' }} · {{ $lote->quantidade_animais ?? 0 }} {{ ($lote->quantidade_animais ?? 0) == 1 ? 'animal' : 'animais' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="dica-campo">Tipo, sexo, idade e finalidade vêm automaticamente do lote escolhido.</small>

                <div class="lote-preview" id="edit_lotePreview" hidden>
                    <div><i class="fa-solid fa-cow"></i> <strong>Tipo:</strong> <span id="edit_previewTipo"></span></div>
                    <div><i class="fa-solid fa-venus-mars"></i> <strong>Sexo:</strong> <span id="edit_previewSexo"></span></div>
                    <div><i class="fa-solid fa-hourglass-half"></i> <strong>Idade:</strong> <span id="edit_previewIdade"></span></div>
                    <div><i class="fa-solid fa-bullseye"></i> <strong>Finalidade:</strong> <span id="edit_previewFinalidade"></span></div>
                </div>

                <label>Sensor</label>
                <div class="input-box">
                    <i class="fa-solid fa-microchip"></i>
                    <input type="text" id="edit_sensor" name="sensor" required>
                </div>

                <button type="submit" class="submit-btn">Salvar Alterações</button>
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
            /* --- SIDEBAR RECOLHÍVEL (mesmo comportamento do Dashboard/Fazendas/Lotes) --- */
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
        // Cada lote já é de uma única fazenda — exige escolher a Fazenda
        // primeiro e só então libera/filtra o select de Lote pra mostrar
        // apenas os lotes daquela fazenda. Evita confundir lotes com o
        // mesmo nome (ex.: "A1") cadastrados em fazendas diferentes.
        function aplicarFiltroFazendaLoteCocho(idSelectFazenda, idSelectLote) {
            const selectFazenda = document.getElementById(idSelectFazenda);
            const selectLote = document.getElementById(idSelectLote);
            if (!selectFazenda || !selectLote) return;

            const fazendaEscolhida = selectFazenda.value;
            let opcaoSelecionadaAindaValida = false;

            Array.from(selectLote.options).forEach((opcao) => {
                if (!opcao.dataset.fazenda) {
                    opcao.hidden = false;
                    return;
                }
                const pertenceAFazenda = fazendaEscolhida !== '' && opcao.dataset.fazenda === fazendaEscolhida;
                opcao.hidden = !pertenceAFazenda;
                if (pertenceAFazenda && opcao.selected) opcaoSelecionadaAindaValida = true;
            });

            selectLote.disabled = fazendaEscolhida === '';
            if (!opcaoSelecionadaAindaValida) {
                selectLote.value = '';
                const prefixo = idSelectLote.startsWith('edit_') ? 'edit_' : '';
                esconderPreviewLoteCocho(prefixo);
            }
        }

        // Lê tipo/sexo/idade/finalidade do <option> escolhido e mostra na
        // prévia — mesmo padrão do cadastro de Ração. O cocho não guarda
        // essas informações (ele só referencia o lote_id), são só
        // feedback visual pra confirmar de qual lote se trata.
        function atualizarPreviewLoteCocho(selectLote, prefixo) {
            const opcao = selectLote.options[selectLote.selectedIndex];
            const preview = document.getElementById(prefixo + 'lotePreview');
            if (!opcao || !opcao.dataset.tipo) {
                preview.hidden = true;
                return;
            }

            document.getElementById(prefixo + 'previewTipo').textContent = opcao.dataset.tipo || '—';
            document.getElementById(prefixo + 'previewSexo').textContent = opcao.dataset.sexo || '—';
            document.getElementById(prefixo + 'previewIdade').textContent = opcao.dataset.idade || '—';
            document.getElementById(prefixo + 'previewFinalidade').textContent = opcao.dataset.finalidade || '—';
            preview.hidden = false;
        }

        function esconderPreviewLoteCocho(prefixo) {
            const preview = document.getElementById(prefixo + 'lotePreview');
            if (preview) preview.hidden = true;
        }

        function abrirModal() {
            document.getElementById('modal').classList.add('active');
        }

        function fecharModal() {
            document.getElementById('modal').classList.remove('active');

            // Reseta fazenda/lote ao fechar, senão o formulário reabriria
            // "vazio" mas com o lote da última vez ainda liberado/marcado.
            const selectFazenda = document.getElementById('cocho_fazenda_id');
            if (selectFazenda) {
                selectFazenda.value = '';
                aplicarFiltroFazendaLoteCocho('cocho_fazenda_id', 'lote_id');
            }
            esconderPreviewLoteCocho('');
        }

        function abrirModalEditar(id, nome, capacidade, nivelAtual, sensor, loteId, fazendaId) {
            document.getElementById('modalEditar').classList.add('active');

            document.getElementById('edit_nome_cocho').value = nome;
            document.getElementById('edit_capacidade').value = capacidade;
            document.getElementById('edit_nivel_atual').value = nivelAtual;
            document.getElementById('edit_sensor').value = sensor;

            const selectFazenda = document.getElementById('edit_cocho_fazenda_id');
            selectFazenda.value = fazendaId || '';
            aplicarFiltroFazendaLoteCocho('edit_cocho_fazenda_id', 'edit_lote_id');

            const selectLote = document.getElementById('edit_lote_id');
            selectLote.value = loteId;
            atualizarPreviewLoteCocho(selectLote, 'edit_');

            document.getElementById('formEditar').action = '/cocho/update/' + id;
        }

        // Estado inicial: sem fazenda escolhida, o select de Lote começa
        // desabilitado nas duas telas (cadastrar e editar).
        document.addEventListener('DOMContentLoaded', () => {
            aplicarFiltroFazendaLoteCocho('cocho_fazenda_id', 'lote_id');
            aplicarFiltroFazendaLoteCocho('edit_cocho_fazenda_id', 'edit_lote_id');
        });

        function fecharModalEditar() {
            document.getElementById('modalEditar').classList.remove('active');
        }

        // ==========================================
        // FILTROS AVANÇADOS (mesmo padrão do Relatório/Ração/Estoque/Lote)
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

        const PARAMS_POR_FILTRO = {
            fazenda_id: ['fazenda_id'],
            lote_id: ['lote_id'],
            status: ['status']
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
                window.location.href = '{{ route('cocho.listar') }}';
            });
        }

        // Cascata Fazenda → Lote dentro do modal de filtros: cada lote já é
        // de uma única fazenda, então escolher a fazenda primeiro evita
        // filtrar por um lote que não é dela.
        const filtroFazenda = document.getElementById('filtro_fazenda_id');
        const filtroLote = document.getElementById('filtro_lote_id');
        const loteFiltroHint = document.getElementById('loteFiltroHint');

        function aplicarFiltroCascataLote() {
            if (!filtroFazenda || !filtroLote) return;

            const fazendaEscolhida = filtroFazenda.value;
            let opcaoSelecionadaAindaValida = false;

            Array.from(filtroLote.options).forEach((opcao) => {
                if (!opcao.dataset.fazenda) {
                    opcao.hidden = false;
                    return;
                }
                const pertenceAFazenda = !fazendaEscolhida || opcao.dataset.fazenda === fazendaEscolhida;
                opcao.hidden = !pertenceAFazenda;
                if (pertenceAFazenda && opcao.selected) opcaoSelecionadaAindaValida = true;
            });

            if (!opcaoSelecionadaAindaValida) filtroLote.value = '';
            if (loteFiltroHint) loteFiltroHint.style.display = fazendaEscolhida ? 'block' : 'none';
        }

        if (filtroFazenda) {
            filtroFazenda.addEventListener('change', aplicarFiltroCascataLote);
            aplicarFiltroCascataLote();
        }

        // FECHAR AO CLICAR FORA (soma-se ao listener de modal/modalEditar já
        // existente mais abaixo no arquivo).
        window.addEventListener('click', (event) => {
            if (event.target === modalFiltros) modalFiltros.classList.remove('active');
        });

        // ==========================================
        // LINHA DO COCHO: "Ver detalhes" (expande/recolhe) e menu "⋮"
        // ==========================================
        function alternarDetalhesCocho(botao) {
            const linha = botao.closest('.cocho-row');
            const painel = linha.querySelector('.cocho-row-detalhes');
            const rotulo = botao.querySelector('.texto-btn-detalhes');
            const abrindo = painel.hidden;

            painel.hidden = !abrindo;
            botao.classList.toggle('aberto', abrindo);
            rotulo.textContent = abrindo ? 'Ocultar detalhes' : 'Ver detalhes';
        }

        function fecharTodosMenusCocho(exceto) {
            document.querySelectorAll('.cocho-row-dropdown.aberto').forEach((dropdown) => {
                if (dropdown !== exceto) dropdown.classList.remove('aberto');
            });
        }

        function alternarMenuCocho(botao) {
            const dropdown = botao.nextElementSibling;
            const jaAberto = dropdown.classList.contains('aberto');
            fecharTodosMenusCocho(null);
            dropdown.classList.toggle('aberto', !jaAberto);
        }

        document.addEventListener('click', (evento) => {
            if (!evento.target.closest('.cocho-row-menu')) fecharTodosMenusCocho(null);
        });

        // ==========================================
        // ORDENAÇÃO E PAGINAÇÃO DA LISTA DE COCHOS (client-side, sobre os
        // cochos já filtrados pelo backend)
        // ==========================================
        (function () {
            const container = document.getElementById('cardsCochos');
            if (!container) return;

            const linhas = Array.from(container.querySelectorAll(':scope > .cocho-row'));
            if (!linhas.length) return;

            const selectOrdenar = document.getElementById('ordenarCochos');
            const selectItensPorPagina = document.getElementById('itensPorPagina');
            const infoEl = document.getElementById('paginacaoInfo');
            const paginasEl = document.getElementById('paginacaoPaginas');

            let paginaAtual = 1;
            let itensPorPagina = selectItensPorPagina ? parseInt(selectItensPorPagina.value, 10) : 10;

            function ordenarLinhas(criterio) {
                const comparadores = {
                    nome: (a, b) => a.dataset.nome.localeCompare(b.dataset.nome, 'pt-BR'),
                    nivel: (a, b) => Number(b.dataset.nivel) - Number(a.dataset.nivel),
                    capacidade: (a, b) => Number(b.dataset.capacidade) - Number(a.dataset.capacidade),
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
                        ? 'Nenhum cocho encontrado'
                        : `Mostrando ${inicio + 1} a ${Math.min(fim, total)} de ${total} cocho${total === 1 ? '' : 's'}`;
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

        /* --- CAPACIDADE NUNCA MENOR QUE O NÍVEL ATUAL ---
           Só ajuda visual no envio; o backend também valida capacidade e
           nível numa faixa coerente (gt:0 / min:0). */
        document.addEventListener('DOMContentLoaded', function() {
            const formCadastro = document.querySelector('#modal form');
            if (formCadastro) {
                formCadastro.addEventListener('submit', function(e) {
                    const capacidade = parseFloat(formCadastro.querySelector('[name="capacidade"]').value);
                    const nivelAtual = parseFloat(formCadastro.querySelector('[name="nivel_atual"]').value);

                    if (capacidade < nivelAtual) {
                        e.preventDefault();
                        alert('Erro: A capacidade do cocho não pode ser menor do que o nível atual de ração!');
                    }
                });
            }

            const formEditar = document.getElementById('formEditar');
            if (formEditar) {
                formEditar.addEventListener('submit', function(e) {
                    const capacidade = parseFloat(document.getElementById('edit_capacidade').value);
                    const nivelAtual = parseFloat(document.getElementById('edit_nivel_atual').value);

                    if (capacidade < nivelAtual) {
                        e.preventDefault();
                        alert('Erro: A capacidade do cocho não pode ser menor do que o nível atual de ração!');
                    }
                });
            }
        });

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modal');
            const modalEditar = document.getElementById('modalEditar');

            if (event.target === modal) fecharModal();
            if (event.target === modalEditar) fecharModalEditar();
        });
    </script>
</body>
</html>
