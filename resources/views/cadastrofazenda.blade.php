<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | Gestão de Fazendas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        /* ==========================================================================
        1. RESET, VARIÁVEIS DE SISTEMA (idêntico ao Dashboard)
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

        .sidebar, .navbar, .main, .card {
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
        5. CARTÕES DE FAZENDA
        ========================================================================== */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 16px;
        }

        .card {
            background: var(--card-bg);
            border: var(--card-border);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--card-shadow);
        }

        .card:hover { transform: translateY(-4px); }

        .card-top { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }

        .icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(145deg, #4ade80, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 6px 14px -4px rgba(5, 150, 105, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        .card-top h4 { color: var(--text-principal); font-size: 1rem; }

        .card-info { border-top: 1px solid var(--surface-muted-border); padding-top: 12px; }

        .card-info p {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            color: var(--text-secundario);
            font-size: 0.8125rem;
        }

        .card-info p:last-child { margin-bottom: 0; }

        .card-info label {
            display: inline;
            margin-bottom: 0;
            font-weight: 600;
            color: var(--text-principal);
        }

        .card-quote {
            font-size: 0.8125rem;
            color: var(--text-secundario);
            font-style: italic;
            margin-top: 8px;
            text-align: left;
        }

        .card-actions { display: flex; gap: 10px; margin-top: 16px; }

        .btn {
            flex: 1;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: 0.2s;
            text-align: center;
            text-decoration: none;
        }

        .edit { background: var(--surface-muted-bg); color: var(--text-principal); }
        .delete { background: #fee2e2; color: #dc2626; }
        .btn:hover { opacity: 0.85; }

        body.dark-theme .delete { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }

        .cards-empty {
            grid-column: 1 / -1;
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
        6. MODAIS (Adicionar / Atualizar Fazenda)
        ========================================================================== */
        .modal, .modal-m {
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

        .modal.active, .modal-m.active { opacity: 1; visibility: visible; }

        .modal-card, .modal-m-card {
            background: var(--card-bg);
            color: var(--text-principal);
            width: 440px;
            max-width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .modal-card { animation: modalShow 0.3s ease; }

        @keyframes modalShow {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-top, .modal-m-top { display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; }
        .modal-top img, .modal-m-top img { width: 80px; }
        .modal-top p, .modal-m-top p { font-size: 0.9375rem; font-weight: 600; color: var(--text-principal); }

        .close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-secundario);
            cursor: pointer;
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
        }

        .input-box input,
        .input-box select {
            width: 100%;
            padding: 12px 12px 12px 42px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            background: var(--surface-muted-bg);
            color: var(--text-principal);
            outline: none;
            transition: 0.2s;
            font-size: 0.9375rem;
        }

        .input-box select { cursor: pointer; }
        .input-box select:disabled,
        .input-box input:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .input-box input:focus,
        .input-box select:focus { border-color: #16a34a; background: var(--card-bg); }

        .row { display: flex; gap: 12px; }
        .campo { flex: 1; }

        /* Mapa interativo (Leaflet) para marcar a localização da fazenda. */
        .mapa-fazenda {
            width: 100%;
            height: 220px;
            border-radius: 10px;
            border: 1px solid var(--surface-muted-border);
            margin-bottom: 6px;
        }

        .mapa-dica {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secundario);
            margin-bottom: 16px;
        }

        body.dark-theme .mapa-fazenda { border-color: var(--surface-muted-border); }
        body.high-contrast .mapa-fazenda { border: 2px solid #ffff00 !important; }

        .btn-geolocalizacao {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: center;
            padding: 10px 14px;
            margin-bottom: 10px;
            border-radius: 10px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, border-color .2s;
        }

        .btn-geolocalizacao:hover { border-color: #16a34a; color: #16a34a; }

        .btn-geolocalizacao:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        body.high-contrast .btn-geolocalizacao {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

        /* Mini-mapa "preso" (travado, sem zoom/arraste) mostrado no card de
           cada fazenda — é só uma foto de localização, não um mapa navegável. */
        .mapa-card-fixo {
            width: 100%;
            height: 130px;
            border-radius: 10px;
            border: 1px solid var(--surface-muted-border);
            margin-bottom: 14px;
            cursor: default;
        }

        body.high-contrast .mapa-card-fixo { border: 2px solid #ffff00 !important; }

        /* Consulta de CEP: feedback do que o ViaCEP encontrou (ou não). */
        .cep-status {
            font-size: 0.8125rem;
            margin: -10px 0 16px;
            min-height: 1.1em;
            color: var(--text-secundario);
        }
        .cep-status.loading { color: var(--text-secundario); }
        .cep-status.ok { color: #15803d; font-weight: 600; }
        .cep-status.erro { color: #dc2626; }
        body.dark-theme .cep-status.ok { color: #4ade80; }

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
        7. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard
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

        body.high-contrast .card,
        body.high-contrast .navbar,
        body.high-contrast .sidebar,
        body.high-contrast .modal-card,
        body.high-contrast .modal-m-card {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }

        body.high-contrast .navbar-logo img { filter: brightness(0) invert(1); }

        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3, body.high-contrast h4,
        body.high-contrast p, body.high-contrast span, body.high-contrast i, body.high-contrast label {
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

        body.high-contrast .navbar-logout {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
        }

        body.high-contrast .btn-add,
        body.high-contrast .submit-btn {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }

        body.high-contrast .btn.edit,
        body.high-contrast .btn.delete {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .input-box input,
        body.high-contrast .input-box select {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .cep-status.ok { color: #4ade80 !important; }
        body.high-contrast .cep-status.erro { color: #ff6b6b !important; }

        body.high-contrast a:focus-visible,
        body.high-contrast button:focus-visible {
            outline: 3px solid #ffff00 !important;
            outline-offset: 2px !important;
        }

        /* ==========================================================================
        8. RESPONSIVIDADE
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
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header-title p { font-size: 0.844rem; }
            .cards { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; }
        }

        /* Com o botão de acessibilidade também morando na navbar (ver
           partials.acessibilidade), avatar + divisor + "Sair" + engrenagem
           de acessibilidade competem por espaço e podiam estourar a largura
           em celulares bem estreitos — o texto "Sair" é o primeiro a sair
           do caminho, sobrando só o ícone. */
        @media (max-width: 400px) {
            .navbar-right { gap: 10px; }
            .navbar-logout span { display: none; }
        }

        @media (max-width: 480px) {
            .row { flex-direction: column; }
            .modal-card, .modal-m-card { padding: 22px; }
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
            <a href="{{ route('fazenda.listar') }}" class="active">
                <i class="fa-solid fa-location-dot"></i>
                <span>Gestão de Fazendas</span>
            </a>
            <a href="{{ route('lote.listar') }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>Gestão de Lotes</span>
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
                <h2>Gestão de Fazendas</h2>
                <p>Conheça o ecossistema produtivo e a extensão das suas propriedades.</p>
            </div>

            <button class="btn-add" onclick="abrirModal()">
                <i class="fa-solid fa-plus"></i> Adicionar Fazenda
            </button>
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
                    <strong>Não foi possível salvar a fazenda:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="cards">
            @forelse($fazendas as $fazenda)
                <div class="card">
                    <div class="card-top">
                        <div class="icon">
                            <i class="fa-regular fa-building"></i>
                        </div>
                        <div>
                            <h4>{{ $fazenda->nome_fazenda }}</h4>
                        </div>
                    </div>

                    @if ($fazenda->latitude && $fazenda->longitude)
                        <div class="mapa-card-fixo" id="mapaCard{{ $fazenda->id }}" data-lat="{{ $fazenda->latitude }}" data-lng="{{ $fazenda->longitude }}"></div>
                    @endif

                    <div class="card-info">
                        @if ($fazenda->cidade)
                            <p>
                                <i class="fa-solid fa-city"></i>
                                <label>Cidade: </label> {{ $fazenda->cidade }}
                            </p>
                            <p>
                                <i class="fa-solid fa-map"></i>
                                <label>Estado: </label> {{ $fazenda->estado }}
                            </p>
                        @else
                            <p>
                                <i class="fa-solid fa-location-dot"></i>
                                <label>Local: </label> {{ $fazenda->localizacao ?: 'Não informado' }}
                            </p>
                        @endif
                        @if ($fazenda->cep)
                            <p>
                                <i class="fa-solid fa-map-pin"></i>
                                <label>CEP: </label> {{ $fazenda->cep }}
                            </p>
                        @endif
                        @if ($fazenda->proprietario)
                            <p>
                                <i class="fa-solid fa-user"></i>
                                <label>Proprietário: </label> {{ $fazenda->proprietario }}
                            </p>
                        @endif
                        <p>
                            <i class="fa-solid fa-seedling"></i>
                            <label>Área: </label> {{ $fazenda->area }} hectares
                        </p>
                        <p class="card-quote">"Foco no aumento da produtividade e manejo sustentável."</p>
                    </div>

                    <div class="card-actions">
                        <button class="btn edit" onclick="abrirModalEditar('{{ $fazenda->id }}', '{{ $fazenda->nome_fazenda }}', '{{ $fazenda->cep }}', '{{ $fazenda->cidade }}', '{{ $fazenda->estado }}', '{{ $fazenda->latitude }}', '{{ $fazenda->longitude }}', '{{ $fazenda->area }}')">
                            <i class="fa-solid fa-pen"></i>
                            Editar
                        </button>

                        <form action="{{ route('fazenda.delete', $fazenda->id) }}" method="POST" style="flex:1;" onsubmit="return confirmarExclusao();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn delete">
                                <i class="fa-solid fa-trash"></i>
                                Excluir
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="cards-empty">
                    <i class="fa-regular fa-building"></i>
                    <p>Você ainda não cadastrou nenhuma fazenda.<br>Clique em "Adicionar Fazenda" para começar.</p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- MODAL: ADICIONAR FAZENDA -->
    <div class="modal" id="modal">
        <div class="modal-card">
            <button class="close" onclick="fecharModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="modal-top">
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Adicionar Fazenda</p>
            </div>

            <form action="{{ route('fazenda.add') }}" method="POST">
                @csrf

                <label>Nome da Fazenda</label>
                <div class="input-box">
                    <i class="fa-regular fa-building"></i>
                    <input type="text" name="nome_fazenda" placeholder="Fazenda São João" required>
                </div>

                <label>Proprietário</label>
                <div class="input-box">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" value="{{ session('usuario_nome') }}" disabled>
                </div>

                <label for="cep">CEP</label>
                <div class="input-box">
                    <i class="fa-solid fa-map-pin"></i>
                    <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9" inputmode="numeric">
                </div>
                <div class="cep-status" id="cepStatus"></div>

                <div class="row">
                    <div class="campo">
                        <label for="estado">Estado</label>
                        <div class="input-box">
                            <i class="fa-solid fa-map"></i>
                            <select id="estado" name="estado">
                                <option value="">Selecione o estado</option>
                            </select>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="cidade">Cidade</label>
                        <div class="input-box">
                            <i class="fa-solid fa-city"></i>
                            <select id="cidade" name="cidade" disabled>
                                <option value="">Selecione o estado primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <label>Localização no mapa</label>
                <button type="button" class="btn-geolocalizacao" id="btnGeoAdd">
                    <i class="fa-solid fa-location-crosshairs"></i> Usar para colocar localização em tempo real
                </button>
                <div class="mapa-fazenda" id="mapaFazenda"></div>
                <small class="mapa-dica">Clique no mapa, arraste o marcador ou use o botão acima para apontar exatamente onde fica a fazenda.</small>

                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">

                <div class="row">
                    <div class="campo">
                        <label>Área</label>
                        <div class="input-box">
                            <i class="fa-solid fa-ruler-combined"></i>
                            <input type="number" step="0.01" name="area" placeholder="10.000" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Cadastrar Fazenda
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: ATUALIZAR FAZENDA -->
    <div class="modal-m" id="modal-m">
        <div class="modal-m-card">
            <button class="close" onclick="fecharModalM()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="modal-m-top">
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Atualizar Fazenda</p>
            </div>

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')

                <label>Nome da Fazenda</label>
                <div class="input-box">
                    <i class="fa-regular fa-building"></i>
                    <input type="text" id="edit_nome_fazenda" name="nome_fazenda" required>
                </div>

                <label>Proprietário</label>
                <div class="input-box">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" value="{{ session('usuario_nome') }}" disabled>
                </div>

                <label for="edit_cep">CEP</label>
                <div class="input-box">
                    <i class="fa-solid fa-map-pin"></i>
                    <input type="text" id="edit_cep" name="cep" placeholder="00000-000" maxlength="9" inputmode="numeric">
                </div>
                <div class="cep-status" id="edit_cepStatus"></div>

                <div class="row">
                    <div class="campo">
                        <label for="edit_estado">Estado</label>
                        <div class="input-box">
                            <i class="fa-solid fa-map"></i>
                            <select id="edit_estado" name="estado">
                                <option value="">Selecione o estado</option>
                            </select>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="edit_cidade">Cidade</label>
                        <div class="input-box">
                            <i class="fa-solid fa-city"></i>
                            <select id="edit_cidade" name="cidade" disabled>
                                <option value="">Selecione o estado primeiro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <label>Localização no mapa</label>
                <button type="button" class="btn-geolocalizacao" id="btnGeoEdit">
                    <i class="fa-solid fa-location-crosshairs"></i> Usar para colocar localização em tempo real
                </button>
                <div class="mapa-fazenda" id="mapaFazendaEdit"></div>
                <small class="mapa-dica">Clique no mapa, arraste o marcador ou use o botão acima para apontar exatamente onde fica a fazenda.</small>

                <input type="hidden" id="edit_latitude" name="latitude">
                <input type="hidden" id="edit_longitude" name="longitude">

                <div class="row">
                    <div class="campo">
                        <label>Área</label>
                        <div class="input-box">
                            <i class="fa-solid fa-ruler-combined"></i>
                            <input type="number" step="0.01" id="edit_area" name="area" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Atualizar Fazenda
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            /* --- SIDEBAR RECOLHÍVEL (mesmo comportamento do Dashboard) --- */
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

            /* --- MINI-MAPAS FIXOS DOS CARDS: só uma prévia visual da
               localização já salva, sem zoom/arraste/clique. --- */
            document.querySelectorAll('.mapa-card-fixo').forEach(function(el) {
                const lat = parseFloat(el.dataset.lat);
                const lng = parseFloat(el.dataset.lng);
                if (isNaN(lat) || isNaN(lng)) return;

                const mapaFixo = L.map(el.id, {
                    zoomControl: false,
                    dragging: false,
                    scrollWheelZoom: false,
                    doubleClickZoom: false,
                    boxZoom: false,
                    keyboard: false,
                    touchZoom: false,
                }).setView([lat, lng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap',
                    maxZoom: 18,
                }).addTo(mapaFixo);

                L.marker([lat, lng]).addTo(mapaFixo);
            });
        });
    </script>

    <script>
        /* --- MAPA INTERATIVO (Leaflet + OpenStreetMap): clicar ou arrastar
           o marcador preenche os campos ocultos de latitude/longitude do
           formulário automaticamente — é a única forma de informar a
           localização, sem campos numéricos visíveis pro usuário digitar. */
        const CENTRO_BRASIL = [-14.235004, -51.925280];

        function criarControleMapa(mapaId, latInputId, lngInputId) {
            const latInput = document.getElementById(latInputId);
            const lngInput = document.getElementById(lngInputId);

            const mapa = L.map(mapaId).setView(CENTRO_BRASIL, 4);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 18,
            }).addTo(mapa);

            let marcador = null;

            function atualizarCampos(lat, lng) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
            }

            function posicionarMarcador(lat, lng) {
                if (marcador) {
                    marcador.setLatLng([lat, lng]);
                } else {
                    marcador = L.marker([lat, lng], { draggable: true }).addTo(mapa);
                    marcador.on('dragend', function() {
                        const pos = marcador.getLatLng();
                        atualizarCampos(pos.lat, pos.lng);
                    });
                }
            }

            mapa.on('click', function(e) {
                atualizarCampos(e.latlng.lat, e.latlng.lng);
                posicionarMarcador(e.latlng.lat, e.latlng.lng);
            });

            function definirPosicao(lat, lng, zoom) {
                mapa.setView([lat, lng], zoom || 14);
                posicionarMarcador(lat, lng);
                atualizarCampos(lat, lng);
            }

            function limpar() {
                if (marcador) {
                    mapa.removeLayer(marcador);
                    marcador = null;
                }
                mapa.setView(CENTRO_BRASIL, 4);
            }

            // Só aproxima/centraliza o mapa (usado pela consulta de CEP) —
            // não mexe no marcador nem nos campos, já que a cidade encontrada
            // é só um ponto de partida; o local exato da fazenda continua
            // sendo definido pelo clique do usuário no mapa.
            function aproximarSemMarcar(lat, lng, zoom) {
                mapa.setView([lat, lng], zoom || 13);
            }

            return {
                mapa: mapa,
                definirPosicao: definirPosicao,
                limpar: limpar,
                aproximarSemMarcar: aproximarSemMarcar,
            };
        }

        let controleMapaAdd = null;
        let controleMapaEdit = null;

        // Botão "Usar para colocar localização em tempo real": pega a
        // posição em tempo real do GPS/rede do dispositivo
        // (navigator.geolocation) e já centraliza o mapa + marca o ponto,
        // sem precisar clicar manualmente.
        function ligarBotaoGeolocalizacao(botaoId, obterControle) {
            const botao = document.getElementById(botaoId);
            if (!botao) return;

            botao.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Seu navegador não suporta geolocalização.');
                    return;
                }

                const controle = obterControle();
                if (!controle) return;

                const textoOriginal = botao.innerHTML;
                botao.disabled = true;
                botao.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Localizando...';

                navigator.geolocation.getCurrentPosition(
                    function(posicao) {
                        controle.definirPosicao(posicao.coords.latitude, posicao.coords.longitude, 16);
                        botao.disabled = false;
                        botao.innerHTML = textoOriginal;
                    },
                    function() {
                        alert('Não foi possível obter sua localização atual. Verifique a permissão de localização do navegador.');
                        botao.disabled = false;
                        botao.innerHTML = textoOriginal;
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });
        }

        ligarBotaoGeolocalizacao('btnGeoAdd', () => controleMapaAdd);
        ligarBotaoGeolocalizacao('btnGeoEdit', () => controleMapaEdit);

        function abrirModal() {
            document.getElementById('modal').classList.add('active');

            if (!controleMapaAdd) {
                controleMapaAdd = criarControleMapa('mapaFazenda', 'latitude', 'longitude');
            }
            setTimeout(function() { controleMapaAdd.mapa.invalidateSize(); }, 150);
        }

        function fecharModal() {
            document.getElementById('modal').classList.remove('active');
        }

        function abrirModalEditar(id, nome, cep, cidade, estado, latitude, longitude, area) {
            document.getElementById('modal-m').classList.add('active');
            document.getElementById('edit_nome_fazenda').value = nome;
            document.getElementById('edit_cep').value = cep;
            document.getElementById('edit_latitude').value = latitude;
            document.getElementById('edit_longitude').value = longitude;
            document.getElementById('edit_area').value = area;
            document.getElementById('edit_cepStatus').textContent = '';
            document.getElementById('edit_cepStatus').className = 'cep-status';
            document.getElementById('formEditar').action = `/fazenda/update/${id}`;

            // Estado primeiro, depois carrega as cidades reais desse estado
            // (via IBGE) e só então seleciona a cidade já cadastrada.
            document.getElementById('edit_estado').value = estado || '';
            carregarCidades(estado || '', document.getElementById('edit_cidade'), cidade || '');

            if (!controleMapaEdit) {
                controleMapaEdit = criarControleMapa('mapaFazendaEdit', 'edit_latitude', 'edit_longitude');
            }

            const lat = parseFloat(latitude);
            const lng = parseFloat(longitude);
            if (!isNaN(lat) && !isNaN(lng)) {
                controleMapaEdit.definirPosicao(lat, lng, 14);
            } else {
                controleMapaEdit.limpar();
            }
            setTimeout(function() { controleMapaEdit.mapa.invalidateSize(); }, 150);
        }

        function fecharModalM() {
            document.getElementById('modal-m').classList.remove('active');
        }

        /* Função de confirmação do botão Excluir */
        function confirmarExclusao() {
            return confirm("Deseja realmente excluir?");
        }

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modal');
            const modalM = document.getElementById('modal-m');

            if (event.target === modal) fecharModal();
            if (event.target === modalM) fecharModalM();
        });

        /* --- ESTADO/CIDADE REAIS (API do IBGE): garante que só dá pra
           escolher uma cidade que exista de verdade no estado selecionado,
           em vez de digitar qualquer texto solto nos dois campos. --- */
        const ESTADOS_BR = [
            ['AC', 'Acre'], ['AL', 'Alagoas'], ['AP', 'Amapá'], ['AM', 'Amazonas'],
            ['BA', 'Bahia'], ['CE', 'Ceará'], ['DF', 'Distrito Federal'], ['ES', 'Espírito Santo'],
            ['GO', 'Goiás'], ['MA', 'Maranhão'], ['MT', 'Mato Grosso'], ['MS', 'Mato Grosso do Sul'],
            ['MG', 'Minas Gerais'], ['PA', 'Pará'], ['PB', 'Paraíba'], ['PR', 'Paraná'],
            ['PE', 'Pernambuco'], ['PI', 'Piauí'], ['RJ', 'Rio de Janeiro'], ['RN', 'Rio Grande do Norte'],
            ['RS', 'Rio Grande do Sul'], ['RO', 'Rondônia'], ['RR', 'Roraima'], ['SC', 'Santa Catarina'],
            ['SP', 'São Paulo'], ['SE', 'Sergipe'], ['TO', 'Tocantins'],
        ];

        const cacheCidadesPorUf = {};

        function preencherEstados(selectEl) {
            selectEl.innerHTML = '<option value="">Selecione o estado</option>' +
                ESTADOS_BR.map(([uf, nome]) => `<option value="${uf}">${nome} (${uf})</option>`).join('');
        }

        async function carregarCidades(uf, selectCidade, cidadeParaSelecionar) {
            if (!uf) {
                selectCidade.innerHTML = '<option value="">Selecione o estado primeiro</option>';
                selectCidade.disabled = true;
                return;
            }

            selectCidade.disabled = true;
            selectCidade.innerHTML = '<option value="">Carregando cidades...</option>';

            try {
                if (!cacheCidadesPorUf[uf]) {
                    const resposta = await fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`);
                    const municipios = await resposta.json();
                    cacheCidadesPorUf[uf] = municipios.map(m => m.nome).sort((a, b) => a.localeCompare(b, 'pt-BR'));
                }

                const cidades = cacheCidadesPorUf[uf];
                selectCidade.innerHTML = '<option value="">Selecione a cidade</option>' +
                    cidades.map(nome => `<option value="${nome}">${nome}</option>`).join('');

                if (cidadeParaSelecionar) {
                    const encontrada = cidades.find(c => c.toLowerCase() === cidadeParaSelecionar.toLowerCase());
                    if (encontrada) {
                        selectCidade.value = encontrada;
                    } else {
                        // Cidade já cadastrada antes desta lista existir (ou vinda
                        // de um nome diferente do oficial) — mantém visível em vez
                        // de simplesmente apagar o dado.
                        const opt = document.createElement('option');
                        opt.value = cidadeParaSelecionar;
                        opt.textContent = `${cidadeParaSelecionar} (não localizada na lista oficial)`;
                        selectCidade.appendChild(opt);
                        selectCidade.value = cidadeParaSelecionar;
                    }
                }
            } catch (erro) {
                selectCidade.innerHTML = '<option value="">Não foi possível carregar as cidades agora</option>';
            } finally {
                selectCidade.disabled = false;
            }
        }

        function configurarEstadoCidade(estadoId, cidadeId) {
            const estadoSelect = document.getElementById(estadoId);
            const cidadeSelect = document.getElementById(cidadeId);
            if (!estadoSelect) return;

            preencherEstados(estadoSelect);
            estadoSelect.addEventListener('change', () => carregarCidades(estadoSelect.value, cidadeSelect));
        }

        configurarEstadoCidade('estado', 'cidade');
        configurarEstadoCidade('edit_estado', 'edit_cidade');

        /* --- CONSULTA DE CEP (ViaCEP): preenche estado/cidade automaticamente --- */
        function aplicarMascaraCep(input) {
            input.addEventListener('input', function() {
                let digitos = input.value.replace(/\D/g, '').slice(0, 8);
                input.value = digitos.length > 5
                    ? digitos.replace(/(\d{5})(\d{0,3})/, '$1-$2')
                    : digitos;
            });
        }

        /* Geocodifica "cidade, UF" (API gratuita Nominatim/OpenStreetMap) e só
           aproxima o mapa dali — não marca o ponto, pois o CEP só indica a
           região, nunca a fazenda exata. */
        async function aproximarMapaDaCidade(cidade, uf, controleMapa) {
            if (!controleMapa || !cidade || !uf) return;

            try {
                const consulta = encodeURIComponent(`${cidade}, ${uf}, Brasil`);
                const resposta = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=br&q=${consulta}`);
                const resultados = await resposta.json();

                if (resultados && resultados[0]) {
                    controleMapa.aproximarSemMarcar(parseFloat(resultados[0].lat), parseFloat(resultados[0].lon), 12);
                }
            } catch (erro) {
                // Não é crítico: sem a aproximação automática, o usuário
                // ainda consegue navegar o mapa manualmente até o local.
            }
        }

        function configurarConsultaCep(cepId, cidadeId, estadoId, statusId, obterControleMapa) {
            const cepInput = document.getElementById(cepId);
            const cidadeSelect = document.getElementById(cidadeId);
            const estadoSelect = document.getElementById(estadoId);
            const statusEl = document.getElementById(statusId);
            if (!cepInput) return;

            aplicarMascaraCep(cepInput);

            async function buscar() {
                const cep = cepInput.value.replace(/\D/g, '');
                if (cep.length !== 8) return;

                statusEl.textContent = 'Buscando endereço pelo CEP...';
                statusEl.className = 'cep-status loading';

                try {
                    const resposta = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const dados = await resposta.json();

                    if (dados.erro) {
                        statusEl.textContent = 'CEP não encontrado — selecione a cidade e o estado manualmente.';
                        statusEl.className = 'cep-status erro';
                        return;
                    }

                    estadoSelect.value = dados.uf || '';
                    await carregarCidades(dados.uf, cidadeSelect, dados.localidade);
                    statusEl.textContent = `Endereço encontrado: ${dados.localidade} - ${dados.uf}`;
                    statusEl.className = 'cep-status ok';

                    aproximarMapaDaCidade(dados.localidade, dados.uf, obterControleMapa());
                } catch (erro) {
                    statusEl.textContent = 'Não foi possível consultar o CEP agora. Selecione manualmente.';
                    statusEl.className = 'cep-status erro';
                }
            }

            cepInput.addEventListener('blur', buscar);
            cepInput.addEventListener('input', function() {
                if (cepInput.value.replace(/\D/g, '').length === 8) buscar();
            });
        }

        configurarConsultaCep('cep', 'cidade', 'estado', 'cepStatus', function() { return controleMapaAdd; });
        configurarConsultaCep('edit_cep', 'edit_cidade', 'edit_estado', 'edit_cepStatus', function() { return controleMapaEdit; });
    </script>

</body>
</html>
