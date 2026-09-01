<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | Gestão de Estoque</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ==========================================================================
           RESET & BASE 
           ========================================================================== */
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
            --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            --card-verde-icon-bg: #e2fbe8;
            --card-verde-texto: #15803d;

            --navbar-bg: #123f22;
            --sidebar-bg: #123f22;
            --surface-border: rgba(255, 255, 255, 0.15);
            --surface-muted-bg: #f9fafb;
            --surface-muted-border: #e5e7eb;

            --menu-text: #b2cbb9;
            --menu-text-hover: #ffffff;
            --menu-hover-bg: rgba(255, 255, 255, 0.05);
            --menu-active-bg: rgba(255, 255, 255, 0.08);
            --menu-active-text: #4ade80;
            --menu-active-accent: #4ade80;

            /* Painel de Acessibilidade (componente compartilhado) */
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
            transition: background 0.3s, color 0.3s;
        }

        /* Transições suaves globais para a interatividade do menu */
        .sidebar, .navbar, .main {
            transition: all 0.3s ease-in-out;
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

        /* ==========================================
           2. NAVBAR SUPERIOR (VERDE FIXA)
           ========================================== */
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

        /* Lado Esquerdo: Hambúrguer + Logo */
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 22px;
            min-width: 0;
        }

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
            transform: translateY(-1px);
        }

        /* Logo diretamente sobre o fundo escuro da navbar, sem caixa/quadrado
           atrás — igual ao Dashboard. */
        .navbar-logo {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .navbar-logo img {
            height: 44px;
            width: auto;
            max-width: 220px;
            object-fit: contain;
            display: block;
            image-rendering: -webkit-optimize-contrast;
        }

        /* Lado Direito: Perfil do Usuário + Sair */
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

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

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.08);
        }

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

        .user-info {
            display: flex;
            flex-direction: column;
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

        .navbar-divider {
            width: 1px;
            height: 28px;
            background: var(--surface-border);
        }

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

        /* Botão de acessibilidade inserido dentro da navbar (ver partials/acessibilidade). */
        .navbar-right .accessibility-trigger {
            background: rgba(255, 255, 255, 0.1);
            color: #4ade80;
            border: 1px solid var(--surface-border);
        }
        .navbar-right .accessibility-trigger:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* ==========================================
           3. SIDEBAR LATERAL
           ========================================== */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--surface-border);
            box-shadow: 2px 0 12px rgba(15, 23, 42, 0.03);
            color: var(--menu-text);
            position: fixed;
            top: 70px; /* Começa exatamente abaixo da Navbar */
            left: 0;
            bottom: 0;
            padding: 0 16px 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 998;
        }

        /* Classe disparada pelo JS para esconder a sidebar */
        .sidebar.collapsed {
            left: -260px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 100%;
            padding-top: 20px;
        }

        .menu a {
            text-decoration: none;
            color: var(--menu-text);
            padding: 12px 14px;
            /* Cantos arredondados só do lado direito, com a barra de
               destaque (border-left) saindo como uma linha limpa e
               contínua — igual ao Dashboard. */
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
            box-shadow: inset 0 0 0 1px rgba(74, 222, 128, 0.12);
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

        /* ==========================================================================
           CONTROLES DE ACESSIBILIDADE (TEMAS)
           Mesma paleta verde do Dashboard, para a página de Estoque reagir do
           mesmo jeito ao modo escuro e ao alto contraste.
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
        body.dark-theme .input-box input,
        body.dark-theme .input-box select { color-scheme: dark; }

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
        body.high-contrast .modal-card,
        body.high-contrast .modal-m-card,
        body.high-contrast .navbar,
        body.high-contrast .sidebar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }

        body.high-contrast .navbar-logo img { filter: brightness(0) invert(1); }

        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3, body.high-contrast h4,
        body.high-contrast p, body.high-contrast span, body.high-contrast label,
        body.high-contrast i, body.high-contrast small {
            color: #ffff00 !important;
        }

        body.high-contrast .menu a.active,
        body.high-contrast .menu a:hover {
            background: #1a1a00 !important;
        }

        body.high-contrast .icon,
        body.high-contrast .toggle-menu-btn,
        body.high-contrast .user-avatar,
        body.high-contrast .btn-add,
        body.high-contrast .input-box input,
        body.high-contrast .input-box select,
        body.high-contrast .btn-abrir-filtros,
        body.high-contrast .filtros-form select,
        body.high-contrast .filtros-ativos .chip,
        body.high-contrast .btn-limpar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* ==========================================================================
        LISTA DE ESTOQUE EM LINHAS — alto contraste (mesmo padrão do
        cadastro de Ração/Lote: fundo preto, borda/texto amarelos, com
        vermelho só pro "Excluir").
        ========================================================================== */
        body.high-contrast .estoques-lista,
        body.high-contrast .estoque-row,
        body.high-contrast .stat-pill {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .stat-pill-icone,
        body.high-contrast .estoque-row-detalhe-icone {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .estoque-row-detalhes {
            background: #000000 !important;
            border-top: 1px dashed #ffff00 !important;
        }

        body.high-contrast .estoque-row-detalhe-item {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .estoque-row-fazenda::before { color: #ffff00 !important; opacity: 1; }

        body.high-contrast .btn-ver-detalhes,
        body.high-contrast .btn-menu-estoque,
        body.high-contrast .estoque-row-dropdown {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }

        body.high-contrast .estoque-row-dropdown button { color: #ffffff !important; }
        body.high-contrast .estoque-row-dropdown button.excluir { color: #ff6b6b !important; }

        body.high-contrast .status-badge,
        body.high-contrast .paginacao-paginas button,
        body.high-contrast .toolbar-ordenar select,
        body.high-contrast .paginacao-itens select {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
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
           4. ÁREA PRINCIPAL (MAIN CONTENT)
           ========================================== */
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
            padding-top: 90px; /* Joga os títulos e cards para baixo da Navbar */
        }

        /* Classe disparada pelo JS para expandir o conteúdo quando a sidebar recolhe */
        .main.expanded {
            margin-left: 0;
            width: 100%;
        }

        .header {
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-principal);
            margin-bottom: 4px;
        }

        .header p {
            color: var(--text-secundario);
            font-size: 15px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

  /* BOTÃO ADICIONAR */
.btn-add {
    background: #0f602e;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
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

.btn-add:hover i {
    transform: rotate(90deg);
}

.btn-add i {
    transition: transform 0.3s ease;
}

.btn-add:active {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(15, 96, 46, 0.25);
}

/* ==========================================================================
   FILTROS AVANÇADOS (mesmo padrão do modal de filtros do Relatório/Ração)
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
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    transition: background 0.2s, border-color 0.2s;
}

.btn-abrir-filtros:hover {
    border-color: #16a34a;
    color: #16a34a;
}

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

.filtros-badge.visivel {
    display: flex;
}

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

.filtros-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.filtros-form .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filtros-form .form-group label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secundario);
}

.filtros-form select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--surface-muted-border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--text-principal);
    background-color: var(--surface-muted-bg);
    outline: none;
}

.filtros-form select:focus {
    border-color: #16a34a;
}

.filtros-modal-footer {
    display: flex;
    gap: 12px;
    margin-top: 10px;
}

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

.btn-aplicar:hover {
    background: #0d6135;
}

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

.btn-limpar:hover {
    background: var(--surface-muted-bg);
}

        /* ==========================================================================
           ALERTS (NOTIFICATIONS) — mesmo estilo usado em Lote/Plano/Fazenda/
           Cocho (antes esta página tinha um visual próprio, sem borda,
           ícone ou reação ao tema escuro/alto contraste).
           ========================================================================== */
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

        /* Alerta "repor estoque" — mesmo critério (Racao::statusEstoque())
           usado no alerta equivalente da tela de Rações. */
        .alerta-estoque {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            cursor: pointer;
            user-select: none;
            transition: filter 0.15s ease;
        }

        .alerta-estoque:hover { filter: brightness(0.97); }
        .alerta-estoque:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }

        .alerta-estoque.baixo { background: #fef3c7; border: 1px solid #fde68a; }
        .alerta-estoque.critico { background: #fee2e2; border: 1px solid #fecaca; }

        .alerta-estoque-chevron {
            flex-shrink: 0;
            margin-left: auto;
            margin-top: 2px;
            font-size: 0.8125rem;
            transition: transform 0.2s ease;
        }

        .alerta-estoque.baixo .alerta-estoque-chevron { color: #b45309; }
        .alerta-estoque.critico .alerta-estoque-chevron { color: #dc2626; }
        .alerta-estoque.aberto .alerta-estoque-chevron { transform: rotate(180deg); }

        .alerta-estoque-icone {
            font-size: 1.25rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alerta-estoque.baixo .alerta-estoque-icone { color: #b45309; }
        .alerta-estoque.critico .alerta-estoque-icone { color: #dc2626; }

        .alerta-estoque-texto strong {
            display: block;
            font-size: 0.9375rem;
            margin-bottom: 4px;
        }

        .alerta-estoque.baixo .alerta-estoque-texto strong { color: #b45309; }
        .alerta-estoque.critico .alerta-estoque-texto strong { color: #dc2626; }

        .alerta-estoque-texto p {
            font-size: 0.8125rem;
            color: var(--text-secundario);
            line-height: 1.5;
            margin: 0;
        }

        .alerta-estoque-lista-wrap { margin-top: 10px; }

        .alerta-estoque-lista {
            display: flex;
            flex-direction: column;
            gap: 6px;
            list-style: none;
            margin: 0 0 8px;
            padding: 0;
        }

        .alerta-estoque-lista li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: rgba(255, 255, 255, 0.55);
            border-radius: 8px;
            padding: 6px 10px;
        }

        .alerta-estoque-lista li strong {
            display: inline;
            margin-bottom: 0;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-principal);
        }

        .alerta-estoque-lista li span {
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .alerta-estoque.baixo .alerta-estoque-lista li span { color: #b45309; }
        .alerta-estoque.critico .alerta-estoque-lista li span { color: #dc2626; }

        body.dark-theme .alerta-estoque.baixo { background: rgba(251, 191, 36, 0.1); border-color: rgba(251, 191, 36, 0.25); }
        body.dark-theme .alerta-estoque.critico { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.28); }
        body.dark-theme .alerta-estoque.baixo .alerta-estoque-icone,
        body.dark-theme .alerta-estoque.baixo .alerta-estoque-texto strong { color: #fbbf24; }
        body.dark-theme .alerta-estoque.critico .alerta-estoque-icone,
        body.dark-theme .alerta-estoque.critico .alerta-estoque-texto strong { color: #fca5a5; }
        body.dark-theme .alerta-estoque.baixo .alerta-estoque-chevron { color: #fbbf24; }
        body.dark-theme .alerta-estoque.critico .alerta-estoque-chevron { color: #fca5a5; }
        body.dark-theme .alerta-estoque-lista li { background: rgba(255, 255, 255, 0.06); }
        body.dark-theme .alerta-estoque-lista li strong { color: var(--text-principal); }

        /* ==========================================================================
           ESTOQUE — LISTA EM LINHAS (mesmo padrão do cadastro de Ração/Lote)
           ========================================================================== */
        .estoques-toolbar {
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
        }

        .stat-pill-total .stat-pill-icone {
            background: linear-gradient(145deg, #60a5fa, #2563eb);
            color: #ffffff;
            box-shadow: 0 6px 13px -4px rgba(37, 99, 235, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }
        .stat-pill-estoque .stat-pill-icone {
            background: linear-gradient(145deg, #4ade80, #059669);
            color: #ffffff;
            box-shadow: 0 6px 13px -4px rgba(5, 150, 105, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }
        .stat-pill-financeiro .stat-pill-icone {
            background: linear-gradient(145deg, #fbbf24, #f97316);
            color: #ffffff;
            box-shadow: 0 6px 13px -4px rgba(217, 119, 6, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
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

        .estoques-lista {
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 16px;
            /* Sem "overflow: hidden" de propósito — cortaria o menu "⋮"
               (position:absolute) que abre pra baixo da linha. */
        }

        .estoque-row { border-bottom: 1px solid var(--surface-muted-border); transition: background 0.15s ease; }
        .estoque-row:last-child { border-bottom: none; }
        .estoque-row:first-child { border-radius: 16px 16px 0 0; }
        .estoque-row:last-child { border-radius: 0 0 16px 16px; }
        .estoque-row:hover { background: var(--surface-muted-bg); }

        .estoque-row-main {
            display: flex;
            align-items: center;
            gap: 22px;
            padding: 16px 20px;
            flex-wrap: wrap;
        }

        .estoque-row-id { display: flex; align-items: center; gap: 14px; min-width: 200px; flex: 1.4 1 200px; }

        /* Cor de fundo vem por espécie (ver $iconesPorAnimal no PHP, mesma
           paleta usada em partials/lote-consumo-linhas.blade.php) — aqui só
           o formato/tamanho, comuns a todas. */
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

        .estoque-row-nome { color: var(--text-principal); font-size: 0.9375rem; font-weight: 700; overflow-wrap: break-word; }

        .estoque-row-fazenda {
            color: var(--text-secundario);
            font-size: 0.78125rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .estoque-row-fazenda::before {
            content: '\f3c5';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.6875rem;
            color: var(--card-verde-texto);
            opacity: 0.7;
        }

        .estoque-row-col {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 100px;
        }

        .estoque-row-col-label {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secundario);
            font-weight: 700;
        }

        .estoque-row-col-valor { font-size: 0.90625rem; color: var(--text-principal); font-weight: 600; }

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
        .status-badge.status-esgotado { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        body.dark-theme .status-badge.status-ok { background: rgba(74, 222, 128, 0.12); border-color: rgba(74, 222, 128, 0.25); color: #4ade80; }
        body.dark-theme .status-badge.status-baixo { background: rgba(251, 191, 36, 0.12); border-color: rgba(251, 191, 36, 0.25); color: #fbbf24; }
        body.dark-theme .status-badge.status-esgotado { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }

        /* ==========================================================================
           NÍVEL DE ESTOQUE POR RAÇÃO — percentual (estoque atual / estoque
           total cadastrado) + barrinha visual, dentro da própria linha da
           ração (verde = ok, amarelo = baixo, vermelho = esgotado — mesmo
           critério do alerta "para repor").
           ========================================================================== */
        .estoque-row-nivel-valor.status-ok { color: #15803d; }
        .estoque-row-nivel-valor.status-baixo { color: #b45309; }
        .estoque-row-nivel-valor.status-esgotado { color: #dc2626; }
        .estoque-row-nivel-valor.status-indefinido { color: var(--text-secundario); font-weight: 500; }

        body.dark-theme .estoque-row-nivel-valor.status-ok { color: #4ade80; }
        body.dark-theme .estoque-row-nivel-valor.status-baixo { color: #fbbf24; }
        body.dark-theme .estoque-row-nivel-valor.status-esgotado { color: #fca5a5; }

        .estoque-row-nivel-barra {
            width: 100%;
            height: 6px;
            border-radius: 999px;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            overflow: hidden;
            margin-top: 4px;
        }

        .estoque-row-nivel-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.3s ease;
        }

        .estoque-row-nivel-fill.status-ok { background: linear-gradient(90deg, #4ade80, #16a34a); }
        .estoque-row-nivel-fill.status-baixo { background: linear-gradient(90deg, #fbbf24, #d97706); }
        .estoque-row-nivel-fill.status-esgotado { background: linear-gradient(90deg, #f87171, #dc2626); }

        body.high-contrast .estoque-row-nivel-barra { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .estoque-row-nivel-fill { background: #ffff00 !important; }
        body.high-contrast .estoque-row-nivel-valor.status-ok,
        body.high-contrast .estoque-row-nivel-valor.status-baixo,
        body.high-contrast .estoque-row-nivel-valor.status-esgotado,
        body.high-contrast .estoque-row-nivel-valor.status-indefinido { color: #ffff00 !important; }

        .estoque-row-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

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

        .btn-menu-estoque {
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

        .btn-menu-estoque:hover { background: var(--surface-muted-bg); }

        .estoque-row-detalhes {
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            gap: 10px;
            padding: 16px 18px 18px 76px;
            background: var(--surface-muted-bg);
            border-top: 1px dashed var(--surface-muted-border);
            animation: detalhesEstoqueAparecer 0.2s ease;
        }

        @keyframes detalhesEstoqueAparecer {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* O atributo "hidden" some por padrão via folha de estilo interna
           do navegador — como ".estoque-row-detalhes" já define
           "display:flex" com mais especificidade, ela vencia o "hidden" e
           o painel ficava visível mesmo fechado. Esta regra devolve a
           prioridade pro atributo. */
        .estoque-row-detalhes[hidden] {
            display: none;
        }

        .estoque-row:last-child .estoque-row-detalhes { border-radius: 0 0 16px 16px; }

        /* Lista simples (sem cartão nem ícone grande por item) — só um
           ícone pequeno e "rótulo: valor" numa linha só. */
        .estoque-row-detalhe-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 3px 0;
            flex: 0 1 190px;
            min-width: 0;
        }

        .estoque-row-detalhe-icone {
            width: 16px;
            color: var(--text-secundario);
            font-size: 0.75rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .estoque-row-detalhe-texto { display: flex; align-items: baseline; gap: 5px; min-width: 0; }

        .estoque-row-detalhes span::after { content: ':'; }

        .estoque-row-detalhes span {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
            font-weight: 600;
        }

        .estoque-row-detalhes strong {
            font-size: 0.875rem;
            color: var(--text-principal);
            font-weight: 700;
            overflow-wrap: break-word;
        }

        .estoques-paginacao {
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
           MODALS (PADRONIZADOS - ADICIONAR E EDITAR)
           ========================================================================== */
        .modal, .modal-m {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 999;
        }

        .modal.active, .modal-m.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card, .modal-m-card {
            background: var(--card-bg);
            width: 450px;
            max-width: 95%;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: modalShow 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        @keyframes modalShow {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-top, .modal-m-top {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

       
         .modal-top img,
        .modal-m-top img {
            width: 80px;
        }

        .modal-top p,
        .modal-m-top p {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-principal);
        }

        /* Título em destaque (bold, maior) usado pelo modal de Filtros
           Avançados — os demais modais deste arquivo continuam só com o
           <p> acima, sem <h2>, então esta regra não afeta nenhum deles. */
        .modal-top h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-principal);
        }

        .modal-top h2 + p {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--text-secundario);
            margin-top: 4px;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: var(--surface-muted-bg);
            color: var(--text-secundario);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: 0.2s;
            z-index: 10;
        }

        .close:hover {
            background: var(--surface-muted-border);
            color: var(--text-principal);
        }

         /* FORM */
        label{
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secundario);
        }

        .input-box{
            position: relative;
            margin-bottom: 18px;
        }

        .input-box i{
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secundario);
            z-index: 2;
        }

        .input-box input, .input-box select{
            width: 100%;
            padding: 12px 12px 12px 42px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            background: var(--surface-muted-bg);
            outline: none;
            transition: 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }

        .input-box input:focus, .input-box select:focus{
            border-color: #16a34a;
            background: var(--card-bg);
        }


        /* GRID ROW CONTEXT */
        .row {
            display: flex;
            gap: 12px;
        }

        .campo {
            flex: 1;
        }

    .submit-btn {
    width: 100%;
    background: #163a24;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(22, 58, 36, 0.15);
}

.submit-btn:hover {
    background: #1d4d2f;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(22, 58, 36, 0.35);
}

.submit-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(22, 58, 36, 0.25);
}


/* COPYRIGHT */
.footer-right {
    margin-top: auto;
    padding-top: 20px;

    border-top: 1px solid var(--surface-border);

    text-align: center;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.35);

    letter-spacing: 0.5px;
}

        /* ==========================================================================
           MEDIA QUERIES (RESPONSIVIDADE)
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
            .header h1 { font-size: 1.5rem; }

            .estoques-toolbar { align-items: stretch; }
            .toolbar-ordenar { justify-content: space-between; }

            .estoque-row-main { align-items: flex-start; }
            .estoque-row-id { flex: 1 1 100%; }
            .estoque-row-actions { margin-left: 0; width: 100%; justify-content: space-between; }
            .estoque-row-detalhes { padding-left: 20px; }

            .top-bar { flex-direction: column; gap: 20px; align-items: flex-start; }
            .row { flex-direction: column; gap: 0; }
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header p { font-size: 0.844rem; }
            .top-bar-actions { flex-direction: column; align-items: stretch; width: 100%; }

            .stat-pills { flex-direction: column; }
            .stat-pill { width: 100%; }

            .estoque-row-main { padding: 14px; gap: 12px; }
            .estoque-row-col { min-width: 0; flex: 1 1 45%; }
            .estoque-row-detalhes { flex-direction: column; flex-wrap: wrap; padding: 14px 14px 16px; }
            .estoque-row-detalhe-item { min-width: 0; }

            .estoques-paginacao { flex-direction: column; align-items: stretch; text-align: center; }
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

        .dica-campo {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secundario);
            margin: -12px 0 16px;
        }
        .racao-preview {
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
        .racao-preview i { color: #16a34a; width: 16px; }
        .racao-preview strong { font-weight: 600; margin-right: 4px; }
        /* Sem isso, o `hidden` do HTML perde pro `display: grid` acima
           (mesma especificidade, mas regra de autor vence a do navegador) e
           o painel de prévia ficava sempre visível, vazio, antes de
           escolher uma ração. */
        .racao-preview[hidden] { display: none; }
    </style>
</head>

<body>

    <!-- Fundo escurecido do menu mobile (fecha a sidebar ao clicar fora) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
            <a href="{{ route('estoque.listar') }}" class="active">
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

    <div class="navbar" id="navbar">
        <div class="navbar-left">
            <button class="toggle-menu-btn" id="toggleMenuBtn" title="Menu Lateral">
                <i class="fa-solid fa-bars"></i>
            </button>
            
            <div class="navbar-logo">
                <img src="{{ asset('agrifarm-removebg-preview.png') }}" alt="Logo">
            </div>
        </div>

        <div class="navbar-right">
            <a href="<?= route('perfil') ?>" class="user-profile" title="Meu perfil">
                <div class="user-avatar">
                    @if ($cadastroLogado->foto ?? false)
                        <img src="{{ asset('storage/' . $cadastroLogado->foto) }}" alt="Foto de perfil" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                    @else
                        <i class="fa-solid fa-user-gear"></i>
                    @endif
                </div>
                <div class="user-info">
                    <span class="user-name">Olá, <?= session('usuario_nome') ?></span>
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

    <div class="main">

        <div class="top-bar">
            <div class="header">
                <h1>Estoque de Rações</h1>
                <p>Acompanhe o estoque de cada ração cadastrada — os dados vêm direto da tela de Rações; para repor ou ajustar, edite a ração por lá.</p>
            </div>

            <div class="top-bar-actions">
                <button class="btn-abrir-filtros" id="openFiltrosBtn">
                    <i class="fa-solid fa-sliders"></i> Filtros Avançados
                    <span class="filtros-badge {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}">{{ count($filtrosAplicados ?? []) }}</span>
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
                    <p>Escolha o que exibir no painel de Estoque</p>
                </div>

                <form action="{{ route('estoque.listar') }}" method="GET" class="filtros-form">
                    <div class="form-group">
                        <label for="filtro_fazenda">Fazenda</label>
                        <select name="fazenda" id="filtro_fazenda">
                            <option value="">Todas as Fazendas</option>
                            @foreach ($fazendas as $fazenda)
                                <option value="{{ $fazenda->id }}" {{ request('fazenda') == $fazenda->id ? 'selected' : '' }}>
                                    {{ $fazenda->nome_fazenda }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro_tipo_animal">Tipo de Animal</label>
                        <select name="tipo_animal" id="filtro_tipo_animal">
                            <option value="todos">Todos os Tipos</option>
                            @foreach ($tiposAnimais ?? [] as $tipoAnimal)
                                <option value="{{ $tipoAnimal }}" {{ request('tipo_animal') == $tipoAnimal ? 'selected' : '' }}>
                                    {{ $tipoAnimal }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro_sexo">Sexo</label>
                        <select name="sexo" id="filtro_sexo">
                            <option value="todos">Todos</option>
                            @foreach ($sexos ?? [] as $sexo)
                                <option value="{{ $sexo }}" {{ request('sexo') == $sexo ? 'selected' : '' }}>
                                    {{ $sexo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro_finalidade">Finalidade</label>
                        <select name="finalidade" id="filtro_finalidade">
                            <option value="todos">Todas as Finalidades</option>
                            @foreach ($finalidades ?? [] as $finalidade)
                                <option value="{{ $finalidade }}" {{ request('finalidade') == $finalidade ? 'selected' : '' }}>
                                    {{ $finalidade }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filtros-modal-footer">
                        <button type="submit" class="btn-aplicar">
                            <i class="fa-solid fa-check"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('estoque.listar') }}" class="btn-limpar">
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

        @php
            // Mesma paleta (ícone + cor por espécie) usada em
            // partials/lote-consumo-linhas.blade.php, pra bater visualmente
            // com o Plano Alimentar.
            $iconesPorAnimal = [
                'Bovino' => ['icone' => 'fa-cow', 'claro' => '#4ade80', 'escuro' => '#059669', 'glow' => 'rgba(5, 150, 105, 0.5)'],
                'Suíno' => ['icone' => 'fa-piggy-bank', 'claro' => '#f472b6', 'escuro' => '#db2777', 'glow' => 'rgba(219, 39, 119, 0.45)'],
                'Equino' => ['icone' => 'fa-horse', 'claro' => '#fbbf24', 'escuro' => '#b45309', 'glow' => 'rgba(180, 83, 9, 0.45)'],
                'Avicultura' => ['icone' => 'fa-dove', 'claro' => '#60a5fa', 'escuro' => '#0ea5e9', 'glow' => 'rgba(14, 165, 233, 0.45)'],
                'Ovino' => ['icone' => 'fa-paw', 'claro' => '#c084fc', 'escuro' => '#7c3aed', 'glow' => 'rgba(124, 58, 237, 0.45)'],
                'Caprino' => ['icone' => 'fa-paw', 'claro' => '#fb923c', 'escuro' => '#f97316', 'glow' => 'rgba(234, 88, 12, 0.45)'],
            ];
            $iconePadrao = ['icone' => 'fa-box', 'claro' => '#94a3b8', 'escuro' => '#64748b', 'glow' => 'rgba(100, 116, 139, 0.4)'];

            // Estoque/status vêm direto da Ração (Racao::estoque/
            // statusEstoque()/diasRestantesEstoque()) — mesma fonte usada
            // no Dashboard, Plano Alimentar e na tela de Rações, pra nunca
            // divergir entre telas.
            $totalRacoesEstoque = $racaos->count();
            $quantidadeTotalKg = $racaos->sum('estoque');
            $valorTotalEstoque = $racaos->sum(fn ($r) => $r->estoque * ($r->valor_kg ?? 0));

            $racoesParaRepor = $racaos->filter(fn ($r) => $r->statusEstoque() !== 'ok')->values();
            $temEsgotado = $racoesParaRepor->contains(fn ($r) => $r->statusEstoque() === 'esgotado');
        @endphp

        @if ($racoesParaRepor->isNotEmpty())
            <div class="alerta-estoque {{ $temEsgotado ? 'critico' : 'baixo' }}"
                role="button" tabindex="0" aria-expanded="false"
                onclick="alternarAlertaEstoque(this)"
                onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); alternarAlertaEstoque(this); }">
                <div class="alerta-estoque-icone">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="alerta-estoque-texto">
                    <strong>
                        {{ $racoesParaRepor->count() }} {{ $racoesParaRepor->count() == 1 ? 'ração precisa' : 'rações precisam' }} de reposição
                    </strong>
                    <div hidden class="alerta-estoque-lista-wrap">
                        <ul class="alerta-estoque-lista">
                            @foreach ($racoesParaRepor as $racaoRepor)
                                <li>
                                    <strong>{{ $racaoRepor->nome_racao }}</strong>
                                    <span>{{ $racaoRepor->statusEstoque() === 'esgotado' ? 'Esgotada' : 'Estoque baixo' }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p>Providencie a reposição para não interromper o trato.</p>
                    </div>
                </div>
                <div class="alerta-estoque-chevron">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
        @endif

        <div class="estoques-toolbar">
            <div class="stat-pills">
                <div class="stat-pill stat-pill-total">
                    <div class="stat-pill-icone"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalRacoesEstoque }}</div>
                        <div class="stat-pill-label">Total de rações</div>
                    </div>
                </div>
                <div class="stat-pill stat-pill-estoque">
                    <div class="stat-pill-icone"><i class="fa-solid fa-warehouse"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ number_format($quantidadeTotalKg, 0, ',', '.') }} kg</div>
                        <div class="stat-pill-label">Quantidade total em estoque</div>
                    </div>
                </div>
                <div class="stat-pill stat-pill-financeiro">
                    <div class="stat-pill-icone"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div>
                        <div class="stat-pill-valor">R$ {{ number_format($valorTotalEstoque, 2, ',', '.') }}</div>
                        <div class="stat-pill-label">Valor em estoque</div>
                    </div>
                </div>
            </div>

            @if ($totalRacoesEstoque > 0)
                <div class="toolbar-ordenar">
                    <label for="ordenarEstoques">Ordenar por:</label>
                    <select id="ordenarEstoques">
                        <option value="recentes">Mais recentes</option>
                        <option value="nome">Nome (A-Z)</option>
                        <option value="quantidade">Maior quantidade</option>
                        <option value="dias">Menos dias restantes</option>
                    </select>
                </div>
            @endif
        </div>

        <div class="estoques-lista" id="listaEstoques">
            @forelse($racaos as $racao)
                @php
                    $statusEstoqueItem = $racao->statusEstoque();
                    $statusLabelItem = [
                        'esgotado' => 'Esgotado',
                        'baixo' => 'Estoque baixo',
                        'ok' => 'Estoque OK',
                    ][$statusEstoqueItem];
                    $diasRestantesItem = $racao->diasRestantesEstoque();
                    $percentualEstoqueItem = $racao->percentualEstoque();
                    $nivelStatusItem = $racao->nivelStatus();
                    $animalIconeEstoque = $iconesPorAnimal[$racao->tipo] ?? $iconePadrao;
                    $animalIconeEstoqueEstilo = 'background: linear-gradient(145deg, ' . $animalIconeEstoque['claro'] . ', ' . $animalIconeEstoque['escuro'] . '); box-shadow: 0 6px 14px -4px ' . $animalIconeEstoque['glow'] . ', inset 0 1px 1px rgba(255,255,255,0.4), inset 0 -2px 3px rgba(0,0,0,0.16);';
                @endphp
                <div class="estoque-row"
                    data-nome="{{ strtolower($racao->nome_racao) }}"
                    data-quantidade="{{ (float) $racao->estoque }}"
                    data-dias="{{ $diasRestantesItem ?? 999999 }}">
                    <div class="estoque-row-main">
                        <div class="estoque-row-id">
                            <div class="icon" style="{{ $animalIconeEstoqueEstilo }}"><i class="fa-solid {{ $animalIconeEstoque['icone'] }}"></i></div>
                            <div>
                                <div class="estoque-row-nome">{{ $racao->nome_racao }}</div>
                                <div class="estoque-row-fazenda">{{ $racao->fazenda->nome_fazenda ?? 'Sem fazenda' }}</div>
                            </div>
                        </div>

                        <div class="estoque-row-col">
                            <span class="estoque-row-col-label">Tipo</span>
                            <span class="estoque-row-col-valor">{{ $racao->tipo ?? '—' }}</span>
                        </div>

                        <div class="estoque-row-col">
                            <span class="estoque-row-col-label">Quantidade</span>
                            <span class="estoque-row-col-valor">{{ number_format($racao->estoque, 2, ',', '.') }} kg</span>
                        </div>

                        <div class="estoque-row-col">
                            <span class="estoque-row-col-label">Nível</span>
                            @if ($percentualEstoqueItem !== null)
                                <span class="estoque-row-col-valor estoque-row-nivel-valor status-{{ $nivelStatusItem }}">{{ $percentualEstoqueItem }}%</span>
                                <div class="estoque-row-nivel-barra">
                                    <div class="estoque-row-nivel-fill status-{{ $nivelStatusItem }}" style="width: {{ $percentualEstoqueItem }}%"></div>
                                </div>
                            @else
                                <span class="estoque-row-col-valor estoque-row-nivel-valor status-indefinido" title="Cadastre o Estoque Total desta ração para ver o nível">—</span>
                            @endif
                        </div>

                        <div class="estoque-row-col">
                            <span class="estoque-row-col-label">Valor do kg</span>
                            <span class="estoque-row-col-valor">{{ $racao->valor_kg !== null ? 'R$ ' . number_format($racao->valor_kg, 2, ',', '.') : '—' }}</span>
                        </div>

                        <div class="estoque-row-col">
                            <span class="estoque-row-col-label">Status</span>
                            <span class="status-badge status-{{ $statusEstoqueItem }}">
                                <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i>
                                {{ $statusLabelItem }}
                            </span>
                        </div>

                        <div class="estoque-row-actions">
                            <button type="button" class="btn-ver-detalhes" onclick="alternarDetalhesEstoque(this)">
                                <span class="texto-btn-detalhes">Ver detalhes</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <a href="{{ route('racao.listar') }}" class="btn-menu-estoque" title="Gerenciar esta ração">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </div>
                    </div>

                    <div class="estoque-row-detalhes" hidden>
                        <div class="estoque-row-detalhe-item">
                            <div class="estoque-row-detalhe-icone"><i class="fa-solid fa-truck"></i></div>
                            <div class="estoque-row-detalhe-texto">
                                <span>Fornecedor</span>
                                <strong>{{ $racao->fornecedor ?? 'Não informado' }}</strong>
                            </div>
                        </div>
                        <div class="estoque-row-detalhe-item">
                            <div class="estoque-row-detalhe-icone"><i class="fa-solid fa-hourglass-end"></i></div>
                            <div class="estoque-row-detalhe-texto">
                                <span>Dias restantes de estoque</span>
                                <strong>{{ $diasRestantesItem !== null ? $diasRestantesItem . ($diasRestantesItem == 1 ? ' dia' : ' dias') : '—' }}</strong>
                            </div>
                        </div>
                        <div class="estoque-row-detalhe-item">
                            <div class="estoque-row-detalhe-icone"><i class="fa-solid fa-venus-mars"></i></div>
                            <div class="estoque-row-detalhe-texto">
                                <span>Sexo</span>
                                <strong>{{ $racao->sexo ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="estoque-row-detalhe-item">
                            <div class="estoque-row-detalhe-icone"><i class="fa-solid fa-bullseye"></i></div>
                            <div class="estoque-row-detalhe-texto">
                                <span>Finalidade</span>
                                <strong>{{ $racao->finalidade ?? '—' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="cards-empty">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                    <p>
                        @if (count($filtrosAplicados ?? []))
                            Nenhuma ração encontrada com esses filtros.<br>Tente ajustar ou limpar os filtros.
                        @else
                            Nenhuma ração cadastrada ainda.<br>Cadastre uma ração para ver o estoque aqui.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if ($totalRacoesEstoque > 0)
            <div class="estoques-paginacao" id="estoquesPaginacao">
                <span id="paginacaoInfoEstoque"></span>
                <div class="paginacao-paginas" id="paginacaoPaginasEstoque"></div>
                <div class="paginacao-itens">
                    <label for="itensPorPaginaEstoque">Itens por página:</label>
                    <select id="itensPorPaginaEstoque">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        @endif

    </div>

    @include('partials.acessibilidade')
    @include('partials.assistente-ia')

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
            const toggleBtn = document.getElementById('toggleMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth > 992) {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                } else {
                    sidebar.classList.toggle('active-mobile');
                    if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
                }
            });

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active-mobile');
                    sidebarOverlay.classList.remove('active');
                });
            }
        });

        // ==========================================
        // LISTA EM LINHAS: "Ver detalhes" (mesmo padrão do cadastro de
        // Ração/Lote) — editar/excluir agora acontece na tela de Rações,
        // fonte única desses dados.
        // ==========================================
        // Alerta "N rações precisam de reposição": clicar mostra/oculta a lista
        function alternarAlertaEstoque(alerta) {
            const conteudo = alerta.querySelector('.alerta-estoque-lista-wrap');
            const abrindo = conteudo.hidden;

            conteudo.hidden = !abrindo;
            alerta.classList.toggle('aberto', abrindo);
            alerta.setAttribute('aria-expanded', abrindo ? 'true' : 'false');
        }

        function alternarDetalhesEstoque(botao) {
            const linha = botao.closest('.estoque-row');
            const painel = linha.querySelector('.estoque-row-detalhes');
            const rotulo = botao.querySelector('.texto-btn-detalhes');
            const abrindo = painel.hidden;

            painel.hidden = !abrindo;
            botao.classList.toggle('aberto', abrindo);
            rotulo.textContent = abrindo ? 'Ocultar detalhes' : 'Ver detalhes';
        }

        // ORDENAÇÃO + PAGINAÇÃO (mesmo padrão do cadastro de Ração/Lote)
        (function () {
            const container = document.getElementById('listaEstoques');
            if (!container) return;

            const linhas = Array.from(container.querySelectorAll(':scope > .estoque-row'));
            if (!linhas.length) return;

            const selectOrdenar = document.getElementById('ordenarEstoques');
            const selectItensPorPagina = document.getElementById('itensPorPaginaEstoque');
            const infoEl = document.getElementById('paginacaoInfoEstoque');
            const paginasEl = document.getElementById('paginacaoPaginasEstoque');

            let paginaAtual = 1;
            let itensPorPagina = selectItensPorPagina ? parseInt(selectItensPorPagina.value, 10) : 10;

            function ordenarLinhas(criterio) {
                const comparadores = {
                    nome: (a, b) => a.dataset.nome.localeCompare(b.dataset.nome, 'pt-BR'),
                    quantidade: (a, b) => Number(b.dataset.quantidade) - Number(a.dataset.quantidade),
                    dias: (a, b) => Number(a.dataset.dias) - Number(b.dataset.dias),
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
                        ? 'Nenhuma ração encontrada'
                        : `Mostrando ${inicio + 1} a ${Math.min(fim, total)} de ${total} raç${total === 1 ? 'ão' : 'ões'}`;
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

        // ==========================================
        // FILTROS AVANÇADOS (mesmo padrão do Relatório/Ração)
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
            fazenda: ['fazenda'],
            tipo_animal: ['tipo_animal'],
            sexo: ['sexo'],
            finalidade: ['finalidade']
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
                window.location.href = '{{ route('estoque.listar') }}';
            });
        }

        // FECHAR AO CLICAR FORA
        window.onclick = function (event) {
            if (event.target == modalFiltros) {
                modalFiltros.classList.remove('active');
            }
        }
    </script>
</body>
</html>
