<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | {{ $animal->nome }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
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

        .sidebar, .navbar, .main, .card { transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, filter 0.25s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease; }
        body.acc-modal-open .sidebar, body.acc-modal-open .navbar, body.acc-modal-open .main { filter: blur(6px); pointer-events: none; }

        .navbar {
            height: 70px; background: var(--navbar-bg); border-bottom: 1px solid var(--surface-border);
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04); position: fixed; top: 0; left: 0; width: 100%;
            display: flex; align-items: center; justify-content: space-between; padding: 0 32px; z-index: 999;
        }
        .navbar-left { display: flex; align-items: center; gap: 22px; min-width: 0; }
        .toggle-menu-btn { background: rgba(255,255,255,0.1); border: 1px solid var(--surface-border); color: #4ade80; font-size: 1.063rem; padding: 9px 13px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .toggle-menu-btn:hover { background: rgba(255,255,255,0.18); border-color: rgba(255,255,255,0.25); color: #ffffff; }
        .navbar-logo { display: flex; align-items: center; min-width: 0; }
        .navbar-logo img { height: 44px; width: auto; max-width: 220px; object-fit: contain; display: block; }
        .navbar-right { display: flex; align-items: center; gap: 18px; }
        .user-profile { display: flex; align-items: center; gap: 12px; text-decoration: none; padding: 6px 10px; margin: -6px -10px; border-radius: 12px; cursor: pointer; }
        .user-profile:hover { background: rgba(255,255,255,0.08); }
        .user-avatar { width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4ade80; font-size: 1rem; border: 1px solid var(--surface-border); flex-shrink: 0; }
        .user-info { display: flex; flex-direction: column; }
        .user-name { color: #ffffff; font-size: 0.875rem; font-weight: 600; }
        .user-role { color: #a7f3d0; font-size: 0.6875rem; font-weight: 500; }
        .navbar-divider { width: 1px; height: 28px; background: var(--surface-border); }
        .navbar-logout { text-decoration: none; color: #fca5a5; font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 10px; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.12); transition: 0.2s; }
        .navbar-logout:hover { background: linear-gradient(135deg, #f87171, #dc2626); border-color: transparent; color: #ffffff; }

        .sidebar {
            width: 260px; background: var(--sidebar-bg); border-right: 1px solid var(--surface-border);
            box-shadow: 2px 0 12px rgba(15,23,42,0.03); color: var(--menu-text); position: fixed; top: 70px; left: 0;
            bottom: 0; padding: 0 16px 20px; overflow-y: auto; display: flex; flex-direction: column; z-index: 998;
        }
        .sidebar.collapsed { left: -260px; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 997; opacity: 0; transition: opacity 0.3s ease; }
        @media (max-width: 992px) { .sidebar-overlay.active { display: block; opacity: 1; } }
        .menu { display: flex; flex-direction: column; gap: 10px; min-height: 100%; padding-top: 20px; }
        .menu a { text-decoration: none; color: var(--menu-text); padding: 12px 14px; border-radius: 0 10px 10px 0; display: flex; align-items: center; gap: 12px; font-size: 0.875rem; font-weight: 500; border-left: 4px solid transparent; transition: 0.18s; }
        .menu a:hover { background: var(--menu-hover-bg); color: var(--menu-text-hover); transform: translateX(4px); }
        .menu a.active { background: var(--menu-active-bg); color: var(--menu-active-text); font-weight: 700; border-left-color: var(--menu-active-accent); }
        .menu i { width: 20px; font-size: 1rem; text-align: center; }
        .footer-right { margin-top: auto; padding-top: 20px; border-top: 1px solid var(--surface-border); text-align: center; font-size: 0.75rem; color: rgba(255,255,255,0.35); }

        .main { margin-left: 260px; width: calc(100% - 260px); padding: 40px; padding-top: 90px; }
        .main.expanded { margin-left: 0; width: 100%; }

        .btn-voltar { display: inline-flex; align-items: center; gap: 8px; color: var(--text-secundario); text-decoration: none; font-size: 0.8125rem; font-weight: 600; margin-bottom: 16px; }
        .btn-voltar:hover { color: var(--text-principal); }

        .alerta-flash { display: flex; align-items: flex-start; gap: 12px; padding: 14px 18px; border-radius: 12px; margin-bottom: 18px; font-size: 0.875rem; }
        .alerta-flash i { margin-top: 2px; flex-shrink: 0; }
        .alerta-flash ul { margin: 6px 0 0 18px; }
        .alerta-erro { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alerta-sucesso { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        body.dark-theme .alerta-erro { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: rgba(74,222,128,0.12); border-color: rgba(74,222,128,0.3); color: #4ade80; }

        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 0.71875rem; font-weight: 700; width: fit-content; }
        .status-badge.status-ok { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .status-badge.status-baixo { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-badge.status-esgotado { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .status-badge.status-indefinido { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        body.dark-theme .status-badge.status-ok { background: rgba(74,222,128,0.12); border-color: rgba(74,222,128,0.25); color: #4ade80; }
        body.dark-theme .status-badge.status-baixo { background: rgba(251,191,36,0.12); border-color: rgba(251,191,36,0.25); color: #fbbf24; }
        body.dark-theme .status-badge.status-esgotado { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.3); color: #fca5a5; }

        /* ===== CABEÇALHO DO ANIMAL ===== */
        .animal-header-card { background: var(--card-bg); border: var(--card-border); border-radius: 16px; box-shadow: var(--card-shadow); padding: 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .animal-header-icon { width: 64px; height: 64px; border-radius: 14px; background: linear-gradient(145deg, #4ade80, #059669); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.75rem; flex-shrink: 0; }
        .animal-header-info h2 { font-size: 1.5rem; font-weight: 700; }
        .animal-header-info p { color: var(--text-secundario); font-size: 0.875rem; margin-top: 2px; }
        .animal-header-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .btn-add { background: #0f602e; color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; margin-left: auto; }
        .btn-add:hover { background: #14753a; }
        .btn-add:disabled { background: #cbd5e1; cursor: not-allowed; }

        /* ===== ABAS ===== */
        .animal-tabs { display: flex; gap: 4px; border-bottom: 2px solid var(--surface-muted-border); margin-bottom: 24px; overflow-x: auto; }
        .animal-tab-btn { background: none; border: none; padding: 12px 18px; font-size: 0.875rem; font-weight: 600; color: var(--text-secundario); cursor: pointer; border-bottom: 3px solid transparent; white-space: nowrap; }
        .animal-tab-btn.active { color: #0f602e; border-bottom-color: #0f602e; }
        body.dark-theme .animal-tab-btn.active { color: #4ade80; border-bottom-color: #4ade80; }
        .animal-tab-panel[hidden] { display: none; }

        /* ===== RESUMO ===== */
        .resumo-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
        .resumo-card { background: var(--card-bg); border: var(--card-border); border-radius: 14px; padding: 20px; box-shadow: var(--card-shadow); }
        .resumo-card h4 { font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-secundario); margin-bottom: 14px; }
        .resumo-linha { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 7px 0; font-size: 0.875rem; border-bottom: 1px solid var(--surface-muted-border); }
        .resumo-linha:last-child { border-bottom: none; }
        .resumo-linha span:first-child { color: var(--text-secundario); }
        .resumo-linha span:last-child { font-weight: 700; color: var(--text-principal); text-align: right; }

        /* ===== TABELAS ===== */
        .tabela-wrap { background: var(--card-bg); border: var(--card-border); border-radius: 14px; box-shadow: var(--card-shadow); overflow: hidden; overflow-x: auto; margin-bottom: 20px; }
        table.tabela-padrao { width: 100%; border-collapse: collapse; }
        table.tabela-padrao th { text-align: left; font-size: 0.71875rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--text-secundario); padding: 12px 16px; background: var(--surface-muted-bg); border-bottom: 1px solid var(--surface-muted-border); white-space: nowrap; }
        table.tabela-padrao td { padding: 12px 16px; font-size: 0.8125rem; border-bottom: 1px solid var(--surface-muted-border); color: var(--text-principal); }
        table.tabela-padrao tr:last-child td { border-bottom: none; }
        .tabela-vazia { padding: 30px 16px; text-align: center; color: var(--text-secundario); font-size: 0.875rem; }

        .btn-acao-tabela { background: var(--surface-muted-bg); border: 1px solid var(--surface-muted-border); color: var(--text-principal); padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; margin-right: 6px; }
        .btn-acao-tabela:hover { background: var(--surface-muted-border); }
        .btn-acao-tabela.perigo { background: #fee2e2; color: #dc2626; border-color: #fecaca; }

        /* ===== TIMELINE (HISTÓRICO) ===== */
        .timeline { display: flex; flex-direction: column; gap: 8px; }
        .timeline-item { display: flex; gap: 14px; padding: 12px 16px; background: var(--card-bg); border: var(--card-border); border-radius: 10px; align-items: baseline; flex-wrap: wrap; }
        .timeline-data { font-size: 0.75rem; color: var(--text-secundario); min-width: 90px; font-weight: 600; }
        .timeline-titulo { font-weight: 700; font-size: 0.875rem; color: var(--text-principal); }
        .timeline-desc { font-size: 0.8125rem; color: var(--text-secundario); }
        .timeline-ponto-parto { color: #d97706; }
        .timeline-ponto-ordenha { color: #0891b2; }
        .timeline-ponto-lactacao { color: #15803d; }

        /* ===== MODAIS ===== */
        .modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.55); display: flex; justify-content: center; align-items: center; opacity: 0; visibility: hidden; transition: 0.3s; z-index: 1200; }
        .modal.active { opacity: 1; visibility: visible; }
        .modal-card { background: var(--card-bg); color: var(--text-principal); width: 480px; max-width: 95%; max-height: 90vh; overflow-y: auto; border-radius: 18px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); animation: modalShow 0.3s ease; }
        @keyframes modalShow { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-top { display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; position: relative; }
        .modal-top img { width: 60px; }
        .modal-top p { font-size: 0.9375rem; font-weight: 600; margin-top: 8px; }
        .close { position: absolute; top: 20px; right: 20px; width: 32px; height: 32px; border-radius: 8px; background: var(--surface-muted-bg); display: flex; align-items: center; justify-content: center; border: none; font-size: 1rem; cursor: pointer; color: var(--text-secundario); }
        .close:hover { background: var(--surface-muted-border); color: var(--text-principal); }
        label { display: block; margin-bottom: 8px; font-size: 0.875rem; font-weight: 600; }
        .input-box { position: relative; margin-bottom: 18px; }
        .input-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-secundario); }
        .input-box input, .input-box select { width: 100%; padding: 12px 12px 12px 42px; border: 1px solid var(--surface-muted-border); border-radius: 10px; background: var(--surface-muted-bg); color: var(--text-principal); outline: none; font-size: 0.9375rem; }
        .input-box input:focus, .input-box select:focus { border-color: #16a34a; background: var(--card-bg); }
        .row { display: flex; gap: 12px; }
        .campo { flex: 1; }
        .submit-btn { width: 100%; background: #163a24; color: white; border: none; padding: 14px; border-radius: 10px; cursor: pointer; font-size: 0.9375rem; font-weight: 600; box-shadow: 0 4px 12px rgba(22,58,36,0.15); }
        .submit-btn:hover { background: #1d4d2f; }
        .dica-campo { display: block; font-size: 0.75rem; color: var(--text-secundario); margin: -12px 0 16px; }

        .bezerro-row { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; background: var(--surface-muted-bg); border: 1px solid var(--surface-muted-border); border-radius: 10px; padding: 12px; }
        .bezerro-row .campos { flex: 1; display: flex; flex-direction: column; gap: 10px; }
        .bezerro-row .input-box { margin-bottom: 0; }
        .btn-adicionar-bezerro { background: none; border: 1px dashed var(--surface-muted-border); color: var(--text-secundario); padding: 10px; border-radius: 8px; width: 100%; cursor: pointer; font-weight: 600; font-size: 0.8125rem; margin-bottom: 14px; }
        .btn-remover-bezerro { background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 30px; height: 30px; cursor: pointer; flex-shrink: 0; }

        body.dark-theme {
            --bg-principal: #0a1810; --text-principal: #f1f9f4; --text-secundario: #9db8a8;
            --card-bg: #12251a; --card-border: 1px solid rgba(74,222,128,0.16); --card-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);
            --navbar-bg: #0c1b12; --sidebar-bg: #0c1b12; --surface-border: rgba(74,222,128,0.14);
            --surface-muted-bg: #0a1810; --surface-muted-border: #2a4d38;
            --menu-text: #9db8a8; --menu-text-hover: #4ade80; --menu-hover-bg: rgba(74,222,128,0.08);
            --menu-active-bg: rgba(74,222,128,0.12); --menu-active-text: #4ade80; --menu-active-accent: #4ade80;
            --panel-bg: #12251a; --panel-text: #f1f9f4; --panel-item-bg: #0a1810;
            --panel-btn-bg: #1e3a2a; --panel-btn-text: #d9ede1; --panel-btn-hover: #2a4d38;
        }
        body.dark-theme .toggle-menu-btn { background: #16281c; border-color: var(--surface-border); color: #d9ede1; }
        body.dark-theme .user-avatar { background: rgba(74,222,128,0.12); border-color: rgba(74,222,128,0.2); color: #4ade80; }

        body.high-contrast {
            background: #000000 !important; color: #ffff00 !important;
            --text-principal: #ffff00; --text-secundario: #ffff00;
            --panel-bg: #000000; --panel-text: #ffffff; --panel-item-bg: #111111;
            --panel-btn-bg: #ffffff; --panel-btn-text: #000000; --panel-btn-hover: #ffff00;
        }
        body.high-contrast .animal-header-card, body.high-contrast .resumo-card,
        body.high-contrast .navbar, body.high-contrast .sidebar, body.high-contrast .modal-card,
        body.high-contrast .tabela-wrap, body.high-contrast .timeline-item {
            background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important;
        }
        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }
        body.high-contrast .navbar-logo img { filter: brightness(0) invert(1); }
        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3, body.high-contrast h4,
        body.high-contrast p, body.high-contrast span, body.high-contrast i, body.high-contrast label,
        body.high-contrast td, body.high-contrast th { color: #ffff00 !important; }
        body.high-contrast .menu a.active, body.high-contrast .menu a:hover { background: #1a1a00 !important; }
        body.high-contrast .animal-header-icon { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .navbar-logout { background: #000000 !important; border: 2px solid #ffffff !important; color: #ffffff !important; }
        body.high-contrast .btn-add, body.high-contrast .submit-btn {
            background: #000000 !important; color: #ffff00 !important; border: 2px solid #ffff00 !important; box-shadow: none !important;
        }
        body.high-contrast .input-box input, body.high-contrast .input-box select {
            background: #000000 !important; color: #ffffff !important; border: 2px solid #ffffff !important;
        }
        body.high-contrast .status-badge { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
        body.high-contrast .animal-tab-btn.active { color: #ffff00 !important; border-bottom-color: #ffff00 !important; }

        @media (max-width: 992px) {
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 90px; }
            .sidebar { left: -260px; }
            .sidebar.active-mobile { left: 0; box-shadow: 10px 0 30px rgba(0,0,0,0.18); }
            .user-info { display: none; }
        }
        @media (max-width: 480px) { .row { flex-direction: column; } .modal-card { padding: 22px; } }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="menu">
            <a href="{{ route('cadastro.index') }}"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>
            <a href="{{ route('fazenda.listar') }}"><i class="fa-solid fa-location-dot"></i><span>Gestão de Fazendas</span></a>
            <a href="{{ route('lote.listar') }}" class="active"><i class="fa-solid fa-layer-group"></i><span>Gestão de Lotes</span></a>
            <a href="{{ route('ordenha.listar') }}"><i class="fa-solid fa-droplet"></i><span>Ordenha e Leite</span></a>
            <a href="{{ route('cocho.listar') }}"><i class="fa-solid fa-cube"></i><span>Gestão de Cochos</span></a>
            <a href="{{ route('racao.listar') }}"><i class="fa-solid fa-wheat-awn"></i><span>Gestão de Rações</span></a>
            <a href="{{ route('estoque.listar') }}"><i class="fa-solid fa-box"></i><span>Nível de Estoque</span></a>
            <a href="{{ route('plano.listar') }}"><i class="fa-solid fa-clipboard-list"></i><span>Plano Alimentar</span></a>
            <a href="{{ route('relatorio.listar') }}"><i class="fa-regular fa-file-lines"></i><span>Relatório Avançado</span></a>
            <a href="{{ route('perfil') }}"><i class="fa-solid fa-user-gear"></i><span>Meu Perfil</span></a>
            <div class="footer-right">© 2026 AgriFarm</div>
        </div>
    </div>

    <div class="navbar" id="navbar">
        <div class="navbar-left">
            <button class="toggle-menu-btn" id="toggleMenuBtn" title="Menu Lateral"><i class="fa-solid fa-bars"></i></button>
            <div class="navbar-logo"><img src="{{ asset('agrifarm-removebg-preview.png') }}" alt="AgriFarm"></div>
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
            <a href="{{ route('logout') }}" class="navbar-logout"><i class="fa-solid fa-right-from-bracket"></i><span>Sair</span></a>
        </div>
    </div>

    <main class="main" id="mainArea">
        <a href="{{ route('lote.listar') }}" class="btn-voltar"><i class="fa-solid fa-arrow-left"></i> Voltar para Gestão de Lotes</a>

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
                    <strong>Não foi possível concluir a ação:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="animal-header-card">
            <div class="animal-header-icon"><i class="fa-solid fa-cow"></i></div>
            <div class="animal-header-info">
                <h2>{{ $animal->nome }}{{ $animal->identificador ? ' · '.$animal->identificador : '' }}</h2>
                <p>{{ $animal->lote->nome_lote ?? 'Sem lote' }} — {{ $animal->sexo }}{{ $animal->raca ? ', '.$animal->raca : '' }}</p>
                <div class="animal-header-badges">
                    <span class="status-badge {{ \App\Support\AnimalRegras::corBadge($animal->status) }}">{{ $animal->status }}</span>
                    <span class="status-badge {{ \App\Support\AnimalRegras::corBadge($animal->status_reprodutivo) }}">{{ $animal->status_reprodutivo }}</span>
                </div>
            </div>
        </div>

        <div class="animal-tabs" role="tablist">
            <button type="button" class="animal-tab-btn active" data-tab="resumo" onclick="mudarAba(this)">Resumo</button>
            <button type="button" class="animal-tab-btn" data-tab="reproducao" onclick="mudarAba(this)">Reprodução</button>
            <button type="button" class="animal-tab-btn" data-tab="partos" onclick="mudarAba(this)">Partos</button>
            <button type="button" class="animal-tab-btn" data-tab="ordenhas" onclick="mudarAba(this)">Ordenhas</button>
            <button type="button" class="animal-tab-btn" data-tab="lactacoes" onclick="mudarAba(this)">Lactações</button>
            <button type="button" class="animal-tab-btn" data-tab="historico" onclick="mudarAba(this)">Histórico</button>
        </div>

        {{-- ===== RESUMO ===== --}}
        <div class="animal-tab-panel" data-tab="resumo">
            <div class="resumo-grid">
                <div class="resumo-card">
                    <h4><i class="fa-solid fa-cow"></i> Animal</h4>
                    <div class="resumo-linha"><span>Nome/Identificação</span><span>{{ $animal->nome }}{{ $animal->identificador ? ' ('.$animal->identificador.')' : '' }}</span></div>
                    <div class="resumo-linha"><span>Raça</span><span>{{ $animal->raca ?? '—' }}</span></div>
                    <div class="resumo-linha"><span>Nascimento</span><span>{{ $animal->data_nascimento->format('d/m/Y') }}</span></div>
                    <div class="resumo-linha"><span>Idade</span><span>{{ $animal->idade }} anos</span></div>
                    <div class="resumo-linha"><span>Status</span><span><span class="status-badge {{ \App\Support\AnimalRegras::corBadge($animal->status) }}">{{ $animal->status }}</span></span></div>
                </div>

                <div class="resumo-card">
                    <h4><i class="fa-solid fa-venus"></i> Reprodução</h4>
                    <div class="resumo-linha"><span>Último parto</span><span>{{ $animal->data_ultimo_parto?->format('d/m/Y') ?? '—' }}</span></div>
                    <div class="resumo-linha"><span>Qtd. de partos</span><span>{{ $animal->qtd_partos }}</span></div>
                    <div class="resumo-linha"><span>Próximo parto</span><span>{{ $proximoParto?->data_prevista?->format('d/m/Y') ?? '—' }}</span></div>
                    <div class="resumo-linha"><span>Status reprodutivo</span><span><span class="status-badge {{ \App\Support\AnimalRegras::corBadge($animal->status_reprodutivo) }}">{{ $animal->status_reprodutivo }}</span></span></div>
                </div>

                <div class="resumo-card">
                    <h4><i class="fa-solid fa-droplet"></i> Lactação Atual</h4>
                    @if ($lactacaoAtiva)
                        <div class="resumo-linha"><span>Número</span><span>Lactação nº {{ $lactacaoAtiva->numero_lactacao }}</span></div>
                        <div class="resumo-linha"><span>Início</span><span>{{ $lactacaoAtiva->data_inicio->format('d/m/Y') }}</span></div>
                        <div class="resumo-linha"><span>Produção hoje</span><span>{{ number_format($producaoHoje, 2, ',', '.') }} L</span></div>
                        <div class="resumo-linha"><span>Média diária</span><span>{{ number_format($lactacaoAtiva->media_diaria, 2, ',', '.') }} L</span></div>
                        <div class="resumo-linha"><span>Produção acumulada</span><span>{{ number_format($lactacaoAtiva->producao_total, 2, ',', '.') }} L</span></div>
                        <div class="resumo-linha"><span>Status</span><span><span class="status-badge status-ok">Ativa</span></span></div>
                    @else
                        <p style="color:var(--text-secundario); font-size:0.875rem;">Nenhuma lactação ativa no momento.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== REPRODUÇÃO ===== --}}
        <div class="animal-tab-panel" data-tab="reproducao" hidden>
            <div class="resumo-card" style="max-width:520px;">
                <h4><i class="fa-solid fa-venus"></i> Ciclo Reprodutivo</h4>
                <div class="resumo-linha"><span>Status atual</span><span><span class="status-badge {{ \App\Support\AnimalRegras::corBadge($animal->status_reprodutivo) }}">{{ $animal->status_reprodutivo }}</span></span></div>
                <div class="resumo-linha"><span>Último parto</span><span>{{ $animal->data_ultimo_parto?->format('d/m/Y') ?? '—' }}</span></div>
                <div class="resumo-linha"><span>Total de partos</span><span>{{ $animal->qtd_partos }}</span></div>
                @if ($proximoParto)
                    <div class="resumo-linha"><span>Próximo parto previsto</span><span>{{ $proximoParto->data_prevista->format('d/m/Y') }} — <span class="status-badge {{ \App\Support\AnimalRegras::corBadge($proximoParto->status) }}">{{ $proximoParto->status }}</span></span></div>
                @endif
                @if ($animal->status_reprodutivo === 'Período seco')
                    <p style="margin-top:12px; font-size:0.8125rem; color:var(--text-secundario);">Animal em período seco — a ordenha fica bloqueada até um novo parto ser registrado como realizado.</p>
                @endif
            </div>
        </div>

        {{-- ===== PARTOS ===== --}}
        <div class="animal-tab-panel" data-tab="partos" hidden>
            <div class="animal-header-card" style="padding:16px 20px; margin-bottom:16px;">
                <div>
                    <strong>Registrar novo parto previsto</strong>
                    <p style="color:var(--text-secundario); font-size:0.8125rem;">{{ $motivoBloqueioParto ?? 'Informe a data prevista para acompanhar a gestação.' }}</p>
                </div>
                <button type="button" class="btn-add" onclick="abrirModalParto()" @if($motivoBloqueioParto) disabled title="{{ $motivoBloqueioParto }}" @endif>
                    <i class="fa-solid fa-plus"></i> Registrar Parto Previsto
                </button>
            </div>

            <div class="tabela-wrap">
                <table class="tabela-padrao">
                    <thead>
                        <tr>
                            <th>Data Prevista</th>
                            <th>Data Real</th>
                            <th>Tipo</th>
                            <th>Bezerros</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($animal->partos as $parto)
                            <tr>
                                <td>{{ $parto->data_prevista?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $parto->data_parto?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $parto->tipo_parto ?? '—' }}</td>
                                <td>{{ $parto->quantidade_bezerros }}</td>
                                <td><span class="status-badge {{ \App\Support\AnimalRegras::corBadge($parto->status) }}">{{ $parto->status }}</span></td>
                                <td>
                                    @if (!$parto->data_parto)
                                        <button type="button" class="btn-acao-tabela" onclick="abrirModalPartoRealizado({{ $parto->id }})">Marcar Realizado</button>
                                        <button type="button" class="btn-acao-tabela perigo" onclick="abrirModalCancelarParto({{ $parto->id }})">Cancelar</button>
                                    @else
                                        @if ($parto->bezerros->isNotEmpty())
                                            <small style="color:var(--text-secundario);">{{ $parto->bezerros->pluck('sexo')->implode(', ') }}</small>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="tabela-vazia">Nenhum parto registrado ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== ORDENHAS ===== --}}
        <div class="animal-tab-panel" data-tab="ordenhas" hidden>
            <div class="animal-header-card" style="padding:16px 20px; margin-bottom:16px;">
                <div>
                    <strong>Registrar ordenha</strong>
                    <p style="color:var(--text-secundario); font-size:0.8125rem;">{{ $motivoBloqueioOrdenha ?? 'Lançamento vinculado à lactação ativa nº '.($lactacaoAtiva->numero_lactacao ?? '') }}</p>
                </div>
                <button type="button" class="btn-add" onclick="abrirModalOrdenha()" @if($motivoBloqueioOrdenha) disabled title="{{ $motivoBloqueioOrdenha }}" @endif>
                    <i class="fa-solid fa-droplet"></i> Registrar Ordenha
                </button>
            </div>

            <div class="tabela-wrap">
                <table class="tabela-padrao">
                    <thead>
                        <tr><th>Data</th><th>Horário</th><th>Turno</th><th>Litros</th><th>Lactação</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($animal->ordenhas as $ordenha)
                            <tr>
                                <td>{{ $ordenha->data->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($ordenha->horario)->format('H:i') }}</td>
                                <td>{{ $ordenha->turno }}</td>
                                <td>{{ number_format($ordenha->quantidade_litros, 2, ',', '.') }} L</td>
                                <td>nº {{ $ordenha->lactacao->numero_lactacao ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="tabela-vazia">Nenhuma ordenha registrada ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== LACTAÇÕES ===== --}}
        <div class="animal-tab-panel" data-tab="lactacoes" hidden>
            <div class="tabela-wrap">
                <table class="tabela-padrao">
                    <thead>
                        <tr><th>Nº</th><th>Início</th><th>Fim</th><th>Total</th><th>Média/dia</th><th>Maior</th><th>Menor</th><th>Status</th><th>Ações</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($animal->lactacoes as $lactacao)
                            <tr>
                                <td>{{ $lactacao->numero_lactacao }}</td>
                                <td>{{ $lactacao->data_inicio->format('d/m/Y') }}</td>
                                <td>{{ $lactacao->data_fim?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ number_format($lactacao->producao_total, 2, ',', '.') }} L</td>
                                <td>{{ number_format($lactacao->media_diaria, 2, ',', '.') }} L</td>
                                <td>{{ $lactacao->maior_producao !== null ? number_format($lactacao->maior_producao, 2, ',', '.').' L' : '—' }}</td>
                                <td>{{ $lactacao->menor_producao !== null ? number_format($lactacao->menor_producao, 2, ',', '.').' L' : '—' }}</td>
                                <td><span class="status-badge {{ \App\Support\AnimalRegras::corBadge($lactacao->status) }}">{{ $lactacao->status }}</span></td>
                                <td>
                                    @if ($lactacao->status === 'Ativa')
                                        <button type="button" class="btn-acao-tabela perigo" onclick="abrirModalEncerrarLactacao({{ $lactacao->id }})">Encerrar</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="tabela-vazia">Nenhuma lactação registrada ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== HISTÓRICO ===== --}}
        <div class="animal-tab-panel" data-tab="historico" hidden>
            <div class="timeline">
                @forelse ($historico as $evento)
                    <div class="timeline-item">
                        <span class="timeline-data">{{ $evento->data->format('d/m/Y') }}</span>
                        <div>
                            <div class="timeline-titulo timeline-ponto-{{ $evento->tipo }}">{{ $evento->titulo }}</div>
                            @if ($evento->descricao)
                                <div class="timeline-desc">{{ $evento->descricao }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="tabela-vazia">Nenhum evento registrado ainda para este animal.</div>
                @endforelse
            </div>
        </div>
    </main>

    {{-- ===== MODAL: REGISTRAR PARTO PREVISTO ===== --}}
    <div class="modal" id="modalParto">
        <div class="modal-card">
            <button type="button" class="close" onclick="fecharModal('modalParto')"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-top"><img src="{{ asset('Logo.png') }}" alt="Logo"><p>Registrar Parto Previsto</p></div>
            <form action="{{ route('animal.parto', $animal->id) }}" method="POST">
                @csrf
                <label for="parto_data_prevista">Data Prevista do Parto</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="parto_data_prevista" name="data_prevista" required>
                </div>
                <label for="parto_obs">Observações (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="parto_obs" name="observacoes" maxlength="255">
                </div>
                <button type="submit" class="submit-btn">Registrar</button>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: MARCAR PARTO REALIZADO ===== --}}
    <div class="modal" id="modalPartoRealizado">
        <div class="modal-card">
            <button type="button" class="close" onclick="fecharModal('modalPartoRealizado')"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-top"><img src="{{ asset('Logo.png') }}" alt="Logo"><p>Marcar Parto como Realizado</p></div>
            <form id="formPartoRealizado" method="POST">
                @csrf
                @method('PUT')

                <label for="pr_data_parto">Data do Parto</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="pr_data_parto" name="data_parto" max="{{ $hoje }}" required>
                </div>

                <label for="pr_tipo_parto">Tipo de Parto</label>
                <div class="input-box">
                    <i class="fa-solid fa-notes-medical"></i>
                    <select id="pr_tipo_parto" name="tipo_parto" required>
                        <option value="" disabled selected>Selecione...</option>
                        @foreach ($tiposParto as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <label>Bezerros nascidos <span style="font-weight:400;color:var(--text-secundario);">— deixe vazio em caso de aborto</span></label>
                <div id="listaBezerros"></div>
                <button type="button" class="btn-adicionar-bezerro" onclick="adicionarLinhaBezerro()">
                    <i class="fa-solid fa-plus"></i> Adicionar bezerro
                </button>
                <input type="hidden" id="pr_quantidade_bezerros" name="quantidade_bezerros" value="0">

                <label for="pr_obs">Observações (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="pr_obs" name="observacoes" maxlength="255">
                </div>

                <button type="submit" class="submit-btn">Confirmar Parto Realizado</button>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CANCELAR PARTO ===== --}}
    <div class="modal" id="modalCancelarParto">
        <div class="modal-card">
            <button type="button" class="close" onclick="fecharModal('modalCancelarParto')"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-top"><img src="{{ asset('Logo.png') }}" alt="Logo"><p>Cancelar Parto Previsto</p></div>
            <form id="formCancelarParto" method="POST">
                @csrf
                @method('PUT')
                <label for="cp_motivo">Motivo</label>
                <div class="input-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <select id="cp_motivo" name="motivo" required>
                        <option value="Cancelado">Cancelado</option>
                        <option value="Perdido">Perdido</option>
                    </select>
                </div>
                <label for="cp_obs">Observações (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="cp_obs" name="observacoes" maxlength="255">
                </div>
                <button type="submit" class="submit-btn">Confirmar</button>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: REGISTRAR ORDENHA ===== --}}
    <div class="modal" id="modalOrdenha">
        <div class="modal-card">
            <button type="button" class="close" onclick="fecharModal('modalOrdenha')"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-top"><img src="{{ asset('Logo.png') }}" alt="Logo"><p>Registrar Ordenha</p></div>
            <form action="{{ route('animal.ordenha', $animal->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="campo">
                        <label for="ord_data">Data</label>
                        <div class="input-box"><i class="fa-solid fa-calendar"></i><input type="date" id="ord_data" name="data" max="{{ $hoje }}" required></div>
                    </div>
                    <div class="campo">
                        <label for="ord_horario">Horário</label>
                        <div class="input-box"><i class="fa-solid fa-clock"></i><input type="time" id="ord_horario" name="horario" required></div>
                    </div>
                </div>
                <label for="ord_turno">Turno</label>
                <div class="input-box">
                    <i class="fa-solid fa-sun"></i>
                    <select id="ord_turno" name="turno" required>
                        <option value="" disabled selected>Selecione...</option>
                        @foreach ($turnosOrdenha as $turno)
                            <option value="{{ $turno }}">{{ $turno }}</option>
                        @endforeach
                    </select>
                </div>
                <label for="ord_litros">Quantidade de Leite (litros)</label>
                <div class="input-box"><i class="fa-solid fa-droplet"></i><input type="number" step="0.01" min="0.01" id="ord_litros" name="quantidade_litros" placeholder="Ex.: 12.5" required></div>
                <label for="ord_obs">Observações (opcional)</label>
                <div class="input-box"><i class="fa-solid fa-note-sticky"></i><input type="text" id="ord_obs" name="observacoes" maxlength="255"></div>
                <button type="submit" class="submit-btn">Registrar Ordenha</button>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: ENCERRAR LACTAÇÃO ===== --}}
    <div class="modal" id="modalEncerrarLactacao">
        <div class="modal-card">
            <button type="button" class="close" onclick="fecharModal('modalEncerrarLactacao')"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-top"><img src="{{ asset('Logo.png') }}" alt="Logo"><p>Encerrar Lactação</p></div>
            <p style="font-size:0.8125rem; color:var(--text-secundario); margin-bottom:16px;">O animal entrará em período seco e novas ordenhas ficarão bloqueadas até o próximo parto.</p>
            <form id="formEncerrarLactacao" method="POST">
                @csrf
                @method('PUT')
                <label for="el_data_fim">Data de Término</label>
                <div class="input-box"><i class="fa-solid fa-calendar"></i><input type="date" id="el_data_fim" name="data_fim" max="{{ $hoje }}"></div>
                <button type="submit" class="submit-btn">Confirmar Encerramento</button>
            </form>
        </div>
    </div>

    @include('partials.acessibilidade')
    @include('partials.assistente-ia')

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>new window.VLibras.Widget('https://vlibras.gov.br/app');</script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
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

        /* --- ABAS --- */
        function mudarAba(botao) {
            const alvo = botao.dataset.tab;
            document.querySelectorAll('.animal-tab-btn').forEach(b => b.classList.toggle('active', b === botao));
            document.querySelectorAll('.animal-tab-panel').forEach(p => { p.hidden = p.dataset.tab !== alvo; });
        }

        /* --- MODAIS GENÉRICOS --- */
        function abrirModal(id) { document.getElementById(id).classList.add('active'); }
        function fecharModal(id) { document.getElementById(id).classList.remove('active'); }

        window.addEventListener('click', function(event) {
            document.querySelectorAll('.modal.active').forEach(function(modal) {
                if (event.target === modal) modal.classList.remove('active');
            });
        });

        /* --- PARTO PREVISTO --- */
        function abrirModalParto() {
            document.getElementById('parto_data_prevista').value = '';
            document.getElementById('parto_obs').value = '';
            abrirModal('modalParto');
        }

        /* --- MARCAR PARTO REALIZADO (bezerros dinâmicos) --- */
        let contadorBezerros = 0;

        function construirSelectSexo() {
            const select = document.createElement('select');
            select.name = 'bezerros[__i__][sexo]';
            select.required = true;
            @foreach ($sexosAnimal as $sexo)
                (function() {
                    const opcao = document.createElement('option');
                    opcao.value = '{{ $sexo }}';
                    opcao.textContent = '{{ $sexo }}';
                    select.appendChild(opcao);
                })();
            @endforeach
            return select;
        }

        function adicionarLinhaBezerro() {
            contadorBezerros++;
            const indice = contadorBezerros;

            const linha = document.createElement('div');
            linha.className = 'bezerro-row';

            const numero = document.createElement('div');
            numero.style.fontWeight = '700';
            numero.style.paddingTop = '10px';
            numero.textContent = '#' + indice;

            const campos = document.createElement('div');
            campos.className = 'campos';

            const boxSexo = document.createElement('div');
            boxSexo.className = 'input-box';
            const iconeSexo = document.createElement('i');
            iconeSexo.className = 'fa-solid fa-venus-mars';
            const selectSexo = construirSelectSexo();
            selectSexo.name = 'bezerros[' + indice + '][sexo]';
            boxSexo.appendChild(iconeSexo);
            boxSexo.appendChild(selectSexo);

            const boxData = document.createElement('div');
            boxData.className = 'input-box';
            const iconeData = document.createElement('i');
            iconeData.className = 'fa-solid fa-calendar';
            const inputData = document.createElement('input');
            inputData.type = 'date';
            inputData.name = 'bezerros[' + indice + '][data_nascimento]';
            inputData.max = '{{ $hoje }}';
            inputData.required = true;
            boxData.appendChild(iconeData);
            boxData.appendChild(inputData);

            campos.appendChild(boxSexo);
            campos.appendChild(boxData);

            const btnRemover = document.createElement('button');
            btnRemover.type = 'button';
            btnRemover.className = 'btn-remover-bezerro';
            btnRemover.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btnRemover.onclick = function() {
                linha.remove();
                atualizarQuantidadeBezerros();
            };

            linha.appendChild(numero);
            linha.appendChild(campos);
            linha.appendChild(btnRemover);

            document.getElementById('listaBezerros').appendChild(linha);
            atualizarQuantidadeBezerros();
        }

        function atualizarQuantidadeBezerros() {
            const total = document.querySelectorAll('#listaBezerros .bezerro-row').length;
            document.getElementById('pr_quantidade_bezerros').value = total;
        }

        function abrirModalPartoRealizado(partoId) {
            document.getElementById('listaBezerros').innerHTML = '';
            contadorBezerros = 0;
            atualizarQuantidadeBezerros();
            document.getElementById('pr_data_parto').value = '{{ $hoje }}';
            document.getElementById('pr_tipo_parto').value = '';
            document.getElementById('pr_obs').value = '';
            document.getElementById('formPartoRealizado').action = '/animal/{{ $animal->id }}/parto/' + partoId + '/realizar';
            abrirModal('modalPartoRealizado');
        }

        /* --- CANCELAR PARTO --- */
        function abrirModalCancelarParto(partoId) {
            document.getElementById('cp_obs').value = '';
            document.getElementById('formCancelarParto').action = '/animal/{{ $animal->id }}/parto/' + partoId + '/cancelar';
            abrirModal('modalCancelarParto');
        }

        /* --- ORDENHA --- */
        function abrirModalOrdenha() {
            document.getElementById('ord_data').value = '{{ $hoje }}';
            document.getElementById('ord_horario').value = '';
            document.getElementById('ord_turno').value = '';
            document.getElementById('ord_litros').value = '';
            document.getElementById('ord_obs').value = '';
            abrirModal('modalOrdenha');
        }

        /* --- ENCERRAR LACTAÇÃO --- */
        function abrirModalEncerrarLactacao(lactacaoId) {
            document.getElementById('el_data_fim').value = '{{ $hoje }}';
            document.getElementById('formEncerrarLactacao').action = '/animal/{{ $animal->id }}/lactacao/' + lactacaoId + '/encerrar';
            abrirModal('modalEncerrarLactacao');
        }
    </script>
</body>
</html>
