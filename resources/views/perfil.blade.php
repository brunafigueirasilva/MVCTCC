<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | Meu Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ==========================================================================
        1. RESET, VARIÁVEIS DE SISTEMA (mesmo design system do Dashboard)
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
            --card-verde-texto: #15803d;

            --card-azul-bg: #f0f7ff;
            --card-azul-border: #e0f2fe;
            --card-azul-texto: #0369a1;

            --navbar-bg: #123f22;
            --sidebar-bg: #123f22;
            --surface-border: rgba(255, 255, 255, 0.15);

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

        .sidebar, .navbar, .main, .box {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, filter 0.25s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease;
        }

        /* Mesmo mecanismo de desfoque do painel de acessibilidade usado no
           Dashboard: aplicado direto nos elementos fixos (navbar/sidebar),
           não só no overlay, para cobrir a tela inteira de forma confiável. */
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
            font-size: 17px;
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
            font-size: 16px;
            border: 1px solid var(--surface-border);
            flex-shrink: 0;
        }

        .user-info { display: flex; flex-direction: column; }
        .user-name { color: #ffffff; font-size: 14px; font-weight: 600; }
        .user-role { color: #a7f3d0; font-size: 11px; font-weight: 500; }

        .navbar-divider { width: 1px; height: 28px; background: var(--surface-border); }

        .navbar-logout {
            text-decoration: none;
            color: #fca5a5;
            font-weight: 600;
            font-size: 14px;
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

        /* Fundo escurecido do menu mobile (fecha a sidebar ao clicar fora),
           igual ao Dashboard. */
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
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
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
            font-size: 14px;
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

        .menu i { width: 20px; font-size: 16px; text-align: center; transition: transform 0.18s ease; }

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
            font-size: 12px;
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

        .header { margin-bottom: 28px; }
        .header h2 { font-size: 28px; font-weight: 700; color: var(--text-principal); margin-bottom: 6px; letter-spacing: -0.5px; }
        .header p { color: var(--text-secundario); font-size: 15px; }

        .box {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 26px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
        }

        /* ==========================================================================
        5. RESUMO DO PERFIL
        ========================================================================== */
        .profile-summary {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .avatar-wrap { position: relative; width: 96px; height: 96px; flex-shrink: 0; }

        .avatar-box {
            width: 96px; height: 96px; border-radius: 50%; overflow: hidden;
            border: 3px solid var(--card-verde-border); background: var(--card-verde-bg);
            display: flex; align-items: center; justify-content: center;
            color: var(--card-verde-texto); font-size: 2.1rem;
        }

        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }

        .avatar-edit-btn {
            position: absolute; bottom: 2px; right: 2px; width: 32px; height: 32px;
            border-radius: 50%; background: #15803d; color: #fff; border: 3px solid var(--card-bg);
            display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem;
        }
        .avatar-edit-btn:hover { background: #166534; }

        .profile-summary-info h1 { font-size: 1.4rem; color: var(--text-principal); margin-bottom: 2px; }
        .profile-summary-info p { color: var(--text-secundario); font-size: 0.9rem; margin-bottom: 10px; }

        .security-badges { display: flex; flex-wrap: wrap; gap: 8px; }

        .badge-info {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 0.78rem; font-weight: 600; padding: 5px 12px; border-radius: 20px;
            background: var(--card-azul-bg); color: var(--card-azul-texto); border: 1px solid var(--card-azul-border);
        }

        .badge-info.ok { background: var(--card-verde-bg); color: var(--card-verde-texto); border-color: var(--card-verde-border); }

        /* ==========================================================================
        6. AVISO DE SEGURANÇA + ALERTAS
        ========================================================================== */
        .alerta {
            padding: 14px 18px; border-radius: 12px; margin-bottom: 22px; font-size: 0.9rem;
            display: flex; align-items: flex-start; gap: 10px; line-height: 1.5;
        }
        .alerta-info { background: var(--card-azul-bg); color: var(--card-azul-texto); border: 1px solid var(--card-azul-border); }

        /* Mensagem de cadastrar/editar/excluir — mesmo estilo usado em
           Lote/Plano/Estoque/Ração/Fazenda/Cocho (antes esta página tinha
           cores levemente diferentes das outras e nenhuma reação ao tema
           escuro/alto contraste). */
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

        .alerta-erro { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alerta-sucesso { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }

        body.dark-theme .alerta-erro { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: rgba(74, 222, 128, 0.12); border-color: rgba(74, 222, 128, 0.3); color: #4ade80; }

        body.high-contrast .alerta-erro,
        body.high-contrast .alerta-sucesso {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* ==========================================================================
        7. FORMULÁRIO
        ========================================================================== */
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; align-items: start; margin-bottom: 22px; }

        .box h3 {
            font-size: 15px; font-weight: 700; color: var(--text-principal);
            margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
        }
        .box h3 i { color: #16a34a; }

        .campos-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .campo { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .campo.full { grid-column: 1 / -1; }
        .campo label { font-size: 0.85rem; font-weight: 600; color: var(--text-principal); }
        .campo small { font-size: 0.74rem; color: var(--text-secundario); }

        .campo .input-wrap { position: relative; }

        .campo input {
            width: 100%; padding: 11px 14px; border-radius: 10px;
            border: 1px solid #d1d5db; font-size: 0.92rem;
            background: #fff; color: #1f2937;
        }
        .campo input:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12); }

        .campo input.has-toggle { padding-right: 42px; }

        /* O Edge (e navegadores baseados em Chromium com essa opção) injeta
           seu próprio ícone nativo de "revelar senha" dentro de qualquer
           input[type=password], colado na borda direita — bem em cima do
           nosso botão .toggle-password (também absolute, right:12px). Sem
           bloquear o nativo, os dois ícones ficam sobrepostos e um clique
           pode acionar a revelação do navegador sem passar pelo nosso botão
           (e sem atualizar o ícone de olho pra refletir o estado), dando a
           impressão de que a senha "aparece sozinha" ao digitar. */
        .campo input[type="password"]::-ms-reveal,
        .campo input[type="password"]::-ms-clear {
            display: none;
        }

        .toggle-password {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--text-secundario); cursor: pointer; font-size: 0.9rem;
        }
        .toggle-password:hover { color: var(--text-principal); }

        .metadados-seguranca {
            margin-top: 4px; padding-top: 16px; border-top: 1px dashed var(--card-border);
            display: flex; flex-direction: column; gap: 6px; font-size: 0.78rem; color: var(--text-secundario);
        }
        .metadados-seguranca i { width: 16px; color: #94a3b8; }

        .btn-salvar {
            width: 100%; padding: 14px; border-radius: 12px; border: none;
            background: linear-gradient(90deg, #14532d, #166534); color: #fff;
            font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-salvar:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(22, 101, 52, 0.3); }

        /* ==========================================================================
        8. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard
        ========================================================================== */
        body.dark-theme {
            --bg-principal: #0a1810;
            --text-principal: #f1f9f4;
            --text-secundario: #9db8a8;
            --card-bg: #12251a;
            --card-border: 1px solid rgba(74, 222, 128, 0.16);
            --card-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);
            --card-verde-bg: rgba(74, 222, 128, 0.1);
            --card-verde-border: rgba(74, 222, 128, 0.3);
            --card-verde-texto: #4ade80;
            --card-azul-bg: rgba(56, 189, 248, 0.1);
            --card-azul-border: rgba(56, 189, 248, 0.3);
            --card-azul-texto: #38bdf8;
            --navbar-bg: #0c1b12;
            --sidebar-bg: #0c1b12;
            --surface-border: rgba(74, 222, 128, 0.14);
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
        body.dark-theme .campo input { background: #0f1712; color: #ededec; border-color: #374151; }

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
        body.high-contrast .box,
        body.high-contrast .navbar,
        body.high-contrast .sidebar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }
        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }
        body.high-contrast .navbar-logo img { filter: brightness(0) invert(1); }
        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3,
        body.high-contrast p, body.high-contrast span, body.high-contrast label,
        body.high-contrast small, body.high-contrast i {
            color: #ffff00 !important;
        }
        body.high-contrast .menu a.active,
        body.high-contrast .menu a:hover { background: #1a1a00 !important; }
        body.high-contrast .toggle-menu-btn,
        body.high-contrast .user-avatar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }
        body.high-contrast .campo input {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }
        body.high-contrast .badge-info {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }
        /* Faltava aqui (o Dashboard já tem): sem isso o botão "Sair" ficava
           com o fundo vermelho translúcido de sempre no alto contraste, em
           vez do preto/branco usado no resto da navbar. */
        body.high-contrast .navbar-logout {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
        }
        body.high-contrast .btn-salvar {
            background: #ffff00 !important;
            color: #000000 !important;
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
            .form-grid-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .campos-grid { grid-template-columns: 1fr; }
            .navbar-logout span { display: none; }
        }
    </style>
</head>

<body>

    <!-- Fundo escurecido do menu mobile (fecha a sidebar ao clicar fora) -->
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
            <a href="{{ route('perfil') }}" class="active">
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
                    @if ($cadastro->foto ?? false)
                        <img src="{{ asset('storage/' . $cadastro->foto) }}" alt="Foto de perfil" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
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
            <h2>Meu Perfil</h2>
            <p>Gerencie seus dados pessoais, sua foto e a segurança da sua conta.</p>
        </div>

        @if (session('perfil_success'))
            <div class="alerta-flash alerta-sucesso" role="status" aria-live="polite">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <span>{{ session('perfil_success') }}</span>
            </div>
        @endif
        @if ($errors->perfil->any())
            <div class="alerta-flash alerta-erro" role="alert" aria-live="assertive">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                <div>
                    <strong>Não foi possível salvar o perfil:</strong>
                    <ul>
                        @foreach ($errors->perfil->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="box">
            <div class="profile-summary">
                <div class="avatar-wrap">
                    <div class="avatar-box" id="avatarPreviewBox">
                        @if($cadastro->foto ?? false)
                            <img id="avatarPreviewImg" src="{{ asset('storage/' . $cadastro->foto) }}" alt="Foto de perfil">
                        @else
                            <i class="fa-solid fa-user" id="avatarPreviewIcon"></i>
                            <img id="avatarPreviewImg" style="display:none;width:100%;height:100%;object-fit:cover;">
                        @endif
                    </div>
                    <label class="avatar-edit-btn" for="fotoInput" aria-label="Alterar foto de perfil">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                </div>
                <div class="profile-summary-info">
                    <h1>{{ $cadastro->responsavel }}</h1>
                    <p>{{ $cadastro->email }} &middot; Membro desde {{ optional($cadastro->created_at)->format('d/m/Y') ?? '—' }}</p>
                    <div class="security-badges">
                        <span class="badge-info {{ $cadastro->cpf ? 'ok' : '' }}">
                            <i class="fa-solid {{ $cadastro->cpf ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                            {{ $cadastro->cpf ? 'CPF cadastrado' : 'CPF não cadastrado' }}
                        </span>
                        <span class="badge-info ok">
                            <i class="fa-solid fa-phone"></i>
                            {{ $cadastro->telefone_formatado }}
                        </span>
                        <span class="badge-info">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Senha alterada {{ $cadastro->senha_alterada_em ? $cadastro->senha_alterada_em->diffForHumans() : 'nunca' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="alerta alerta-info">
            <i class="fa-solid fa-shield-halved"></i>
            Por segurança, toda alteração de perfil exige sua senha atual e gera um e-mail de confirmação
            para <strong>{{ $cadastro->email }}</strong> — inclusive para o endereço antigo, se você trocar o e-mail.
        </div>

        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" id="formPerfil">
            @csrf
            @method('PUT')
            <input type="file" id="fotoInput" name="foto" accept="image/png,image/jpeg,image/webp" style="display:none;">

            <div class="form-grid-2">
                <div class="box">
                    <h3><i class="fa-solid fa-id-card"></i> Dados pessoais</h3>
                    <div class="campos-grid">
                        <div class="campo full">
                            <label for="responsavel">Nome completo</label>
                            <input type="text" id="responsavel" name="responsavel" value="{{ old('responsavel', $cadastro->responsavel) }}" required>
                        </div>
                        <div class="campo">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $cadastro->cpf_formatado) }}" placeholder="000.000.000-00" maxlength="14" inputmode="numeric">
                            <small>Exige maioridade e data de nascimento.</small>
                        </div>
                        <div class="campo">
                            <label for="data_nascimento">Data de nascimento</label>
                            <input type="date" id="data_nascimento" name="data_nascimento"
                                   value="{{ old('data_nascimento', optional($cadastro->data_nascimento)->format('Y-m-d')) }}"
                                   max="{{ now()->subYears(18)->format('Y-m-d') }}">
                            <small>Obrigatória apenas se um CPF for informado.</small>
                        </div>
                        <div class="campo">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $cadastro->telefone_formatado) }}" placeholder="(00) 00000-0000" maxlength="15" inputmode="numeric" required>
                        </div>
                        <div class="campo">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $cadastro->email) }}" required>
                            <small>Trocar dispara um alerta para o e-mail antigo.</small>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <h3><i class="fa-solid fa-lock"></i> Segurança</h3>
                    <div class="campo">
                        <label for="password">Nova senha</label>
                        <div class="input-wrap">
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="Deixe em branco para manter a atual" autocomplete="new-password">
                            <button type="button" class="toggle-password" data-target="password" aria-label="Mostrar senha"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        <small>Mínimo 8 caracteres, com maiúscula, minúscula, número e @.</small>
                    </div>
                    <div class="campo">
                        <label for="password_confirmation">Confirmar nova senha</label>
                        <div class="input-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="has-toggle" placeholder="Repita a nova senha" autocomplete="new-password">
                            <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Mostrar senha"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="senha_atual">Senha atual <span style="color:#b91c1c;">*</span></label>
                        <div class="input-wrap">
                            <input type="password" id="senha_atual" name="senha_atual" placeholder="Obrigatória para confirmar as alterações" autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="metadados-seguranca">
                        <span><i class="fa-solid fa-key"></i> Senha alterada em: {{ $cadastro->senha_alterada_em ? $cadastro->senha_alterada_em->format('d/m/Y \à\s H:i') : 'nunca alterada' }}</span>
                        <span><i class="fa-solid fa-envelope"></i> E-mail alterado em: {{ $cadastro->email_alterado_em ? $cadastro->email_alterado_em->format('d/m/Y \à\s H:i') : 'nunca alterado' }}</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-salvar"><i class="fa-solid fa-floppy-disk"></i> Salvar alterações</button>
        </form>
    </main>

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
        document.addEventListener('DOMContentLoaded', function () {
            /* --- SIDEBAR RECOLHÍVEL (mesmo comportamento do Dashboard) --- */
            const toggleBtn = document.getElementById('toggleMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (toggleBtn && sidebar && mainContent) {
                toggleBtn.addEventListener('click', function () {
                    if (window.innerWidth > 992) {
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');
                    } else {
                        sidebar.classList.toggle('active-mobile');
                        if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
                    }
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function () {
                    sidebar.classList.remove('active-mobile');
                    sidebarOverlay.classList.remove('active');
                });
            }

            /* --- MÁSCARAS --- */
            function aplicarMascara(input, mascara) {
                if (!input) return;
                input.addEventListener('input', function () {
                    let digitos = input.value.replace(/\D/g, '');
                    let resultado = '';
                    let posicaoDigito = 0;
                    for (let i = 0; i < mascara.length && posicaoDigito < digitos.length; i++) {
                        if (mascara[i] === '0') {
                            resultado += digitos[posicaoDigito];
                            posicaoDigito++;
                        } else {
                            resultado += mascara[i];
                        }
                    }
                    input.value = resultado;
                });
            }
            aplicarMascara(document.getElementById('cpf'), '000.000.000-00');
            aplicarMascara(document.getElementById('telefone'), '(00) 00000-0000');

            /* --- PRÉ-VISUALIZAÇÃO DA FOTO --- */
            document.getElementById('fotoInput').addEventListener('change', function (e) {
                var file = e.target.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (ev) {
                    var img = document.getElementById('avatarPreviewImg');
                    var icon = document.getElementById('avatarPreviewIcon');
                    img.src = ev.target.result;
                    img.style.display = 'block';
                    if (icon) icon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            });

            /* --- MOSTRAR/OCULTAR SENHA --- */
            document.querySelectorAll('.toggle-password').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const target = document.getElementById(btn.dataset.target);
                    if (!target) return;
                    const icon = btn.querySelector('i');
                    const isHidden = target.type === 'password';
                    target.type = isHidden ? 'text' : 'password';
                    icon.classList.toggle('fa-eye', !isHidden);
                    icon.classList.toggle('fa-eye-slash', isHidden);
                });
            });

            /* --- CPF só é aceito para maiores de 18 anos (checagem real é no servidor) --- */
            var campoCpf = document.getElementById('cpf');
            var campoNascimento = document.getElementById('data_nascimento');

            function alternarObrigatoriedadeNascimento() {
                var temCpf = campoCpf.value.replace(/\D/g, '').length > 0;
                campoNascimento.required = temCpf;
            }
            campoCpf.addEventListener('input', alternarObrigatoriedadeNascimento);
            alternarObrigatoriedadeNascimento();

            document.getElementById('formPerfil').addEventListener('submit', function (e) {
                var temCpf = campoCpf.value.replace(/\D/g, '').length > 0;
                if (temCpf && campoNascimento.value) {
                    var hoje = new Date();
                    var nascimento = new Date(campoNascimento.value);
                    var idade = hoje.getFullYear() - nascimento.getFullYear();
                    var aniversarioJaPassou = (hoje.getMonth() > nascimento.getMonth()) ||
                        (hoje.getMonth() === nascimento.getMonth() && hoje.getDate() >= nascimento.getDate());
                    if (!aniversarioJaPassou) idade--;

                    if (idade < 18) {
                        e.preventDefault();
                        alert('Só é permitido cadastrar CPF para maiores de 18 anos.');
                    }
                }
            });
        });
    </script>
</body>
</html>
