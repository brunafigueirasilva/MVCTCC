<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | Tipos de Rações</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* RESET */
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
    /* Cantos arredondados só do lado direito, com a barra de destaque
       (border-left) saindo como uma linha limpa e contínua — igual ao
       Dashboard. */
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
   Mesma paleta verde do Dashboard, para a página de Rações reagir do mesmo
   jeito ao modo escuro e ao alto contraste.
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
       ficavam com a cor cinza-escura do tema claro sobre o fundo preto
       forçado — quase ilegível. */
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
body.high-contrast i, body.high-contrast div, body.high-contrast strong {
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

body.high-contrast .navbar-logout {
    background: #000000 !important;
    border: 2px solid #ffffff !important;
    color: #ffffff !important;
}

body.high-contrast .alerta-estoque-racao {
    background: #000000 !important;
    border: 2px solid #ffff00 !important;
}

body.high-contrast .alerta-estoque-racao.critico {
    border-color: #ff6b6b !important;
}

body.high-contrast .alerta-estoque-icone,
body.high-contrast .alerta-estoque-texto strong {
    color: #ffff00 !important;
}

body.high-contrast .alerta-estoque-racao.critico .alerta-estoque-icone,
body.high-contrast .alerta-estoque-racao.critico .alerta-estoque-texto strong {
    color: #ff6b6b !important;
}

/* ==========================================================================
LISTA DE RAÇÕES EM LINHAS — alto contraste (mesmo padrão do cadastro de
Lote: fundo preto, borda/texto amarelos, com vermelho só pro "Excluir").
========================================================================== */
body.high-contrast .racoes-lista,
body.high-contrast .racao-row,
body.high-contrast .stat-pill {
    background: #000000 !important;
    border: 2px solid #ffff00 !important;
    color: #ffff00 !important;
}

body.high-contrast .stat-pill-icone,
body.high-contrast .racao-row-detalhe-icone {
    background: #000000 !important;
    border: 2px solid #ffff00 !important;
    color: #ffff00 !important;
}

body.high-contrast .racao-row-detalhes {
    background: #000000 !important;
    border-top: 1px dashed #ffff00 !important;
}

body.high-contrast .racao-row-detalhe-item {
    background: #000000 !important;
    border: 2px solid #ffff00 !important;
    color: #ffff00 !important;
}

body.high-contrast .racao-row-fazenda::before { color: #ffff00 !important; opacity: 1; }

body.high-contrast .btn-ver-detalhes,
body.high-contrast .btn-menu-racao,
body.high-contrast .racao-row-dropdown {
    background: #000000 !important;
    color: #ffffff !important;
    border: 2px solid #ffffff !important;
}

body.high-contrast .racao-row-dropdown button { color: #ffffff !important; }
body.high-contrast .racao-row-dropdown button.excluir { color: #ff6b6b !important; }

body.high-contrast .status-badge,
body.high-contrast .paginacao-paginas button,
body.high-contrast .toolbar-ordenar select,
body.high-contrast .paginacao-itens select {
    background: #000000 !important;
    border: 2px solid #ffff00 !important;
    color: #ffff00 !important;
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

        /* HEADER */
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

        /* TOPO */
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
   FILTROS AVANÇADOS (mesmo padrão do modal de filtros do Relatório)
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

        /* ALERTAS — mesmo estilo usado em Lote/Plano/Fazenda/Cocho (antes
           esta página tinha um visual próprio, sem borda, ícone ou reação
           ao tema escuro/alto contraste). */
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

        /* Alerta "comprar ração" — mesma regra de estoque baixo/esgotado
           usada nos alertas do Dashboard (Racao::DIAS_SEGURANCA_ESTOQUE). */
        .alerta-estoque-racao {
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

        .alerta-estoque-racao:hover { filter: brightness(0.97); }
        .alerta-estoque-racao:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }

        .alerta-estoque-racao.baixo { background: #fef3c7; border: 1px solid #fde68a; }
        .alerta-estoque-racao.critico { background: #fee2e2; border: 1px solid #fecaca; }

        .alerta-estoque-chevron {
            flex-shrink: 0;
            margin-left: auto;
            margin-top: 2px;
            font-size: 0.8125rem;
            transition: transform 0.2s ease;
        }

        .alerta-estoque-racao.baixo .alerta-estoque-chevron { color: #b45309; }
        .alerta-estoque-racao.critico .alerta-estoque-chevron { color: #dc2626; }
        .alerta-estoque-racao.aberto .alerta-estoque-chevron { transform: rotate(180deg); }

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

        .alerta-estoque-racao.baixo .alerta-estoque-lista li span { color: #b45309; }
        .alerta-estoque-racao.critico .alerta-estoque-lista li span { color: #dc2626; }

        .alerta-estoque-texto p { margin: 0; }

        .alerta-estoque-icone {
            font-size: 1.25rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alerta-estoque-racao.baixo .alerta-estoque-icone { color: #b45309; }
        .alerta-estoque-racao.critico .alerta-estoque-icone { color: #dc2626; }

        .alerta-estoque-texto strong {
            display: block;
            font-size: 0.9375rem;
            margin-bottom: 4px;
        }

        .alerta-estoque-racao.baixo .alerta-estoque-texto strong { color: #b45309; }
        .alerta-estoque-racao.critico .alerta-estoque-texto strong { color: #dc2626; }

        .alerta-estoque-texto p {
            font-size: 0.8125rem;
            color: var(--text-secundario);
            line-height: 1.5;
        }

        body.dark-theme .alerta-estoque-racao.baixo { background: rgba(251, 191, 36, 0.1); border-color: rgba(251, 191, 36, 0.25); }
        body.dark-theme .alerta-estoque-racao.critico { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.28); }
        body.dark-theme .alerta-estoque-racao.baixo .alerta-estoque-icone,
        body.dark-theme .alerta-estoque-racao.baixo .alerta-estoque-texto strong { color: #fbbf24; }
        body.dark-theme .alerta-estoque-racao.critico .alerta-estoque-icone,
        body.dark-theme .alerta-estoque-racao.critico .alerta-estoque-texto strong { color: #fca5a5; }
        body.dark-theme .alerta-estoque-racao.baixo .alerta-estoque-chevron { color: #fbbf24; }
        body.dark-theme .alerta-estoque-racao.critico .alerta-estoque-chevron { color: #fca5a5; }
        body.dark-theme .alerta-estoque-lista li { background: rgba(255, 255, 255, 0.06); }
        body.dark-theme .alerta-estoque-lista li strong { color: var(--text-principal); }

        /* ==========================================================================
        RAÇÕES — LISTA EM LINHAS (mesmo padrão do cadastro de Lote)
        ========================================================================== */
        .racoes-toolbar {
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
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        .stat-pill-total .stat-pill-icone { background: linear-gradient(145deg, #60a5fa, #2563eb); box-shadow: 0 6px 13px -4px rgba(37, 99, 235, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }
        .stat-pill-estoque .stat-pill-icone { background: linear-gradient(145deg, #4ade80, #059669); box-shadow: 0 6px 13px -4px rgba(5, 150, 105, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }
        .stat-pill-financeiro .stat-pill-icone { background: linear-gradient(145deg, #fbbf24, #f97316); box-shadow: 0 6px 13px -4px rgba(217, 119, 6, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }

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

        .racoes-lista {
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 16px;
            /* Sem "overflow: hidden" de propósito — cortaria o menu "⋮"
               (position:absolute) que abre pra baixo da linha. As bordas
               arredondadas ficam por conta das regras de primeira/última
               linha abaixo. */
        }

        .racao-row { border-bottom: 1px solid var(--surface-muted-border); transition: background 0.15s ease; }
        .racao-row:last-child { border-bottom: none; }
        .racao-row:first-child { border-radius: 16px 16px 0 0; }
        .racao-row:last-child { border-radius: 0 0 16px 16px; }
        .racao-row:hover { background: var(--surface-muted-bg); }

        .racao-row-main {
            display: flex;
            align-items: center;
            gap: 22px;
            padding: 16px 20px;
            flex-wrap: wrap;
        }

        .racao-row-id { display: flex; align-items: center; gap: 14px; min-width: 200px; flex: 1.4 1 200px; }

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

        .racao-row-nome { color: var(--text-principal); font-size: 0.9375rem; font-weight: 700; overflow-wrap: break-word; }

        .racao-row-fazenda {
            color: var(--text-secundario);
            font-size: 0.78125rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .racao-row-fazenda::before {
            content: '\f3c5';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.6875rem;
            color: var(--card-verde-texto);
            opacity: 0.7;
        }

        .racao-row-col {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 100px;
        }

        .racao-row-col-label {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secundario);
            font-weight: 700;
        }

        .racao-row-col-valor { font-size: 0.90625rem; color: var(--text-principal); font-weight: 600; }

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

        .racao-row-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

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

        .racao-row-menu { position: relative; }

        .btn-menu-racao {
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

        .btn-menu-racao:hover { background: var(--surface-muted-bg); }

        .racao-row-dropdown {
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

        .racao-row-dropdown.aberto { display: block; }

        .racao-row-dropdown button {
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

        .racao-row-dropdown button:hover { background: var(--surface-muted-bg); }
        .racao-row-dropdown button.excluir { color: #dc2626; }

        .racao-row-detalhes {
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            gap: 10px;
            padding: 16px 18px 18px 76px;
            background: var(--surface-muted-bg);
            border-top: 1px dashed var(--surface-muted-border);
            animation: detalhesRacaoAparecer 0.2s ease;
        }

        @keyframes detalhesRacaoAparecer {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* O atributo "hidden" some por padrão via folha de estilo interna
           do navegador — como ".racao-row-detalhes" já define
           "display:flex" com mais especificidade, ela vencia o "hidden" e
           o painel ficava visível mesmo fechado. Esta regra devolve a
           prioridade pro atributo. */
        .racao-row-detalhes[hidden] {
            display: none;
        }

        .racao-row:last-child .racao-row-detalhes { border-radius: 0 0 16px 16px; }

        /* Lista simples (sem cartão nem ícone grande por item) — só um
           ícone pequeno e "rótulo: valor" numa linha só. */
        .racao-row-detalhe-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 3px 0;
            flex: 0 1 190px;
            min-width: 0;
        }

        .racao-row-detalhe-icone {
            width: 16px;
            color: var(--text-secundario);
            font-size: 0.75rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .racao-row-detalhe-texto { display: flex; align-items: baseline; gap: 5px; min-width: 0; }

        .racao-row-detalhes span::after { content: ':'; }

        .racao-row-detalhes span {
            font-size: 0.65625rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
            font-weight: 600;
        }

        .racao-row-detalhes strong {
            font-size: 0.875rem;
            color: var(--text-principal);
            font-weight: 700;
            overflow-wrap: break-word;
        }

        .racoes-paginacao {
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

        /* MODAL */
        .modal,
        .modal-m {
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

        .modal.active,
        .modal-m.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card,
        .modal-m-card {
            background: var(--card-bg);
            width: 440px;
            max-width: 95%;
            max-height: 88vh;
            overflow-y: auto;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: modalShow 0.3s ease;
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

        /* TOPO MODAL */
        .modal-top,
        .modal-m-top {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

         .modal-top img,
        .modal-m-top img {
            width: 80px;
        }

        .modal-top p,
        .modal-m-top p {
            font-size: 19px;
            font-weight: 700;
            color: var(--text-principal);
            margin-top: 4px;
        }

        /* Título em destaque (bold, maior) usado pelo modal de Filtros
           Avançados — os demais modais deste arquivo continuam só com o
           <p> acima, sem <h2>, então esta regra não afeta nenhum deles. A
           regra "h2 + p" torna o subtítulo do Filtros mais discreto do que
           o <p> "solo" (bold/19px) usado como título nos outros modais. */
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
            font-size: 16px;
            color: var(--text-secundario);
            cursor: pointer;
            transition: 0.2s;
        }

        .close:hover {
            background: var(--surface-muted-border);
            color: var(--text-principal);
        }

        /* FORM */
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secundario);
        }

        .input-box {
            position: relative;
            margin-bottom: 18px;
        }

        .input-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secundario);
            z-index: 2;
        }

        .input-box input,
        .input-box select {
            width: 100%;
            padding: 12px 12px 12px 42px;
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            background: var(--surface-muted-bg);
            outline: none;
            transition: 0.2s;
            font-size: 14px;
            color: var(--text-principal);
            appearance: none; /* Remove a seta padrão do select em alguns browsers */
            -webkit-appearance: none;
        }

        .input-box input:focus,
        .input-box select:focus {
            border-color: #16a34a;
            background: var(--card-bg);
        }

        /* ROW */
        .row {
            display: flex;
            gap: 12px;
        }

        .campo {
            flex: 1;
        }

        .dica-campo {
            display: block;
            font-size: 12px;
            color: var(--text-secundario);
            margin: -12px 0 16px;
        }

        /* Prévia do perfil (tipo/sexo/idade/finalidade) puxado do lote
           escolhido — confirma visualmente pro usuário o que vai ser
           salvo, já que esses campos não são mais digitados à mão. */
        .lote-preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
            background: var(--surface-muted-bg, #f4f6f5);
            border: 1px solid var(--surface-muted-border, #e2e8f0);
            border-radius: 10px;
            padding: 12px 14px;
            margin: -4px 0 18px;
            font-size: 13px;
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

        /* RESPONSIVO */
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

            .top-bar { flex-direction: column; gap: 20px; align-items: flex-start; }
            .row { flex-direction: column; }

            .racoes-toolbar { align-items: stretch; }
            .toolbar-ordenar { justify-content: space-between; }

            .racao-row-main { align-items: flex-start; }
            .racao-row-id { flex: 1 1 100%; }
            .racao-row-actions { margin-left: 0; width: 100%; justify-content: space-between; }
            .racao-row-detalhes { padding-left: 20px; }
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header p { font-size: 0.844rem; }
            .top-bar-actions { flex-direction: column; align-items: stretch; width: 100%; }

            .stat-pills { flex-direction: column; }
            .stat-pill { width: 100%; }

            .racao-row-main { padding: 14px; gap: 12px; }
            .racao-row-col { min-width: 0; flex: 1 1 45%; }
            .racao-row-detalhes { flex-direction: column; flex-wrap: wrap; padding: 14px 14px 16px; }
            .racao-row-detalhe-item { min-width: 0; }

            .racoes-paginacao { flex-direction: column; align-items: stretch; text-align: center; }
            .paginacao-paginas { justify-content: center; flex-wrap: wrap; }
            .paginacao-itens { justify-content: center; }
        }

        @media (max-width: 400px) {
            .navbar-right { gap: 10px; }
            .navbar-logout span { display: none; }
        }

        @media (max-width: 480px) {
            .modal-card, .modal-m-card { width: 95%; padding: 25px; }
        }
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
            <a href="{{ route('racao.listar') }}" class="active">
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
                <h1>Tipos de Rações</h1>
                <p>Acompanhe os tipos de rações cadastrados e garanta a nutrição de precisão para o seu rebanho.</p>
            </div>
            <div class="top-bar-actions">
                <button class="btn-abrir-filtros" id="openFiltrosBtn">
                    <i class="fa-solid fa-sliders"></i> Filtros Avançados
                    <span class="filtros-badge {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}">{{ count($filtrosAplicados ?? []) }}</span>
                </button>
                <button class="btn-add" onclick="abrirModal()">
                    <i class="fa-solid fa-plus"></i>
                    Adicionar Tipo de Ração
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
                    <p>Escolha o que exibir no painel de Rações</p>
                </div>

                <form action="{{ route('racao.listar') }}" method="GET" class="filtros-form">
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

                    <div class="filtros-modal-footer">
                        <button type="submit" class="btn-aplicar">
                            <i class="fa-solid fa-check"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('racao.listar') }}" class="btn-limpar">
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
                    <strong>Não foi possível salvar a ração:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
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
            $iconePadrao = ['icone' => 'fa-wheat-awn', 'claro' => '#94a3b8', 'escuro' => '#64748b', 'glow' => 'rgba(100, 116, 139, 0.4)'];

            $totalRacoes = $racaos->count();
            $estoqueTotalKg = $racaos->sum('estoque');

            // Mesma regra usada nos alertas do Dashboard (Racao::DIAS_SEGURANCA_ESTOQUE)
            // — quem precisa de reposição aqui é exatamente quem gera alerta lá.
            $racoesParaRepor = $racaos->filter(fn ($r) => $r->statusEstoque() !== 'ok')->values();
            $temEsgotada = $racoesParaRepor->contains(fn ($r) => $r->statusEstoque() === 'esgotado');
        @endphp

        @if ($racoesParaRepor->isNotEmpty())
            <div class="alerta-estoque-racao {{ $temEsgotada ? 'critico' : 'baixo' }}"
                role="button" tabindex="0" aria-expanded="false"
                onclick="alternarAlertaEstoqueRacao(this)"
                onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); alternarAlertaEstoqueRacao(this); }">
                <div class="alerta-estoque-icone">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="alerta-estoque-texto">
                    <strong>
                        {{ $racoesParaRepor->count() }} {{ $racoesParaRepor->count() == 1 ? 'ração precisa' : 'rações precisam' }} de reposição
                    </strong>
                    <div hidden class="alerta-estoque-lista-wrap">
                        <ul class="alerta-estoque-lista">
                            @foreach ($racoesParaRepor as $racao)
                                <li>
                                    <strong>{{ $racao->nome_racao }}</strong>
                                    <span>{{ $racao->statusEstoque() === 'esgotado' ? 'Esgotada' : $racao->diasRestantesEstoque() . ' dia(s) restante(s)' }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p>Providencie a compra para não interromper a alimentação do rebanho.</p>
                    </div>
                </div>
                <div class="alerta-estoque-chevron">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
        @endif

        <div class="racoes-toolbar">
            <div class="stat-pills">
                <div class="stat-pill stat-pill-total">
                    <div class="stat-pill-icone"><i class="fa-solid fa-wheat-awn"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ $totalRacoes }}</div>
                        <div class="stat-pill-label">Total de rações</div>
                    </div>
                </div>
                <div class="stat-pill stat-pill-estoque">
                    <div class="stat-pill-icone"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <div class="stat-pill-valor">{{ number_format($estoqueTotalKg, 0, ',', '.') }} kg</div>
                        <div class="stat-pill-label">Estoque total</div>
                    </div>
                </div>
            </div>

            @if ($totalRacoes > 0)
                <div class="toolbar-ordenar">
                    <label for="ordenarRacoes">Ordenar por:</label>
                    <select id="ordenarRacoes">
                        <option value="recentes">Mais recentes</option>
                        <option value="nome">Nome (A-Z)</option>
                        <option value="estoque">Maior estoque</option>
                        <option value="valor">Maior valor do kg</option>
                    </select>
                </div>
            @endif
        </div>

        <div class="racoes-lista" id="cardsRacoes">
            @forelse($racaos as $racao)
                @php
                    $animalIconeRacao = $iconesPorAnimal[$racao->tipo] ?? $iconePadrao;
                    $animalIconeRacaoEstilo = 'background: linear-gradient(145deg, ' . $animalIconeRacao['claro'] . ', ' . $animalIconeRacao['escuro'] . '); box-shadow: 0 6px 14px -4px ' . $animalIconeRacao['glow'] . ', inset 0 1px 1px rgba(255,255,255,0.4), inset 0 -2px 3px rgba(0,0,0,0.16);';
                    $diasRestantes = $racao->diasRestantesEstoque();
                @endphp
                <div class="racao-row"
                    data-nome="{{ strtolower($racao->nome_racao) }}"
                    data-estoque="{{ (float) $racao->estoque }}"
                    data-valor="{{ (float) ($racao->valor_kg ?? 0) }}">
                    <div class="racao-row-main">
                        <div class="racao-row-id">
                            <div class="icon" style="{{ $animalIconeRacaoEstilo }}"><i class="fa-solid {{ $animalIconeRacao['icone'] }}"></i></div>
                            <div>
                                <div class="racao-row-nome">{{ $racao->nome_racao }}</div>
                                <div class="racao-row-fazenda">{{ $racao->fazenda->nome_fazenda ?? 'Sem fazenda' }}</div>
                            </div>
                        </div>

                        <div class="racao-row-col">
                            <span class="racao-row-col-label">Tipo</span>
                            <span class="racao-row-col-valor">{{ $racao->tipo }}</span>
                        </div>

                        <div class="racao-row-col">
                            <span class="racao-row-col-label">Valor do kg</span>
                            <span class="racao-row-col-valor">{{ $racao->valor_kg !== null ? 'R$ ' . number_format($racao->valor_kg, 2, ',', '.') : '—' }}</span>
                        </div>

                        <div class="racao-row-actions">
                            <button type="button" class="btn-ver-detalhes" onclick="alternarDetalhesRacao(this)">
                                <span class="texto-btn-detalhes">Ver detalhes</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <div class="racao-row-menu">
                                <button type="button" class="btn-menu-racao" onclick="alternarMenuRacao(this)" title="Mais ações">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="racao-row-dropdown">
                                    <button type="button"
                                        onclick="abrirModalEditar(
                                        '{{ $racao->id }}',
                                        '{{ $racao->fazenda_id }}',
                                        '{{ $racao->nome_racao }}',
                                        '{{ $racao->tipo }}',
                                        '{{ $racao->sexo }}',
                                        '{{ $racao->idade }}',
                                        '{{ $racao->finalidade }}',
                                        '{{ $racao->estoque }}',
                                        '{{ $racao->estoque_total }}',
                                        '{{ $racao->consumo }}',
                                        '{{ $racao->valor_kg }}',
                                        '{{ $racao->fornecedor }}',
                                        '{{ $racao->reposicao }}'
                                        )">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </button>
                                    <form action="{{ route('racao.delete', $racao->id) }}" method="POST" onsubmit="return confirmarExclusao();">
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

                    <div class="racao-row-detalhes" hidden>
                        <div class="racao-row-detalhe-item">
                            <div class="racao-row-detalhe-icone"><i class="fa-solid fa-venus-mars"></i></div>
                            <div class="racao-row-detalhe-texto">
                                <span>Sexo</span>
                                <strong>{{ $racao->sexo ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="racao-row-detalhe-item">
                            <div class="racao-row-detalhe-icone"><i class="fa-solid fa-hourglass-half"></i></div>
                            <div class="racao-row-detalhe-texto">
                                <span>Idade</span>
                                <strong>{{ $racao->idade ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="racao-row-detalhe-item">
                            <div class="racao-row-detalhe-icone"><i class="fa-solid fa-bullseye"></i></div>
                            <div class="racao-row-detalhe-texto">
                                <span>Finalidade</span>
                                <strong>{{ $racao->finalidade ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="racao-row-detalhe-item">
                            <div class="racao-row-detalhe-icone"><i class="fa-solid fa-bowl-food"></i></div>
                            <div class="racao-row-detalhe-texto">
                                <span>Consumo</span>
                                <strong>{{ number_format($racao->consumo, 2, ',', '.') }} kg/dia</strong>
                            </div>
                        </div>
                        <div class="racao-row-detalhe-item">
                            <div class="racao-row-detalhe-icone"><i class="fa-solid fa-truck"></i></div>
                            <div class="racao-row-detalhe-texto">
                                <span>Fornecedor</span>
                                <strong>{{ $racao->fornecedor }}</strong>
                            </div>
                        </div>
                        <div class="racao-row-detalhe-item">
                            <div class="racao-row-detalhe-icone"><i class="fa-solid fa-calendar"></i></div>
                            <div class="racao-row-detalhe-texto">
                                <span>Reposição</span>
                                <strong>{{ \Carbon\Carbon::parse($racao->reposicao)->format('d/m/Y') }}</strong>
                            </div>
                        </div>
                        @if ($diasRestantes !== null)
                            <div class="racao-row-detalhe-item">
                                <div class="racao-row-detalhe-icone"><i class="fa-solid fa-hourglass-end"></i></div>
                                <div class="racao-row-detalhe-texto">
                                    <span>Dias restantes de estoque</span>
                                    <strong>{{ $diasRestantes }} {{ $diasRestantes == 1 ? 'dia' : 'dias' }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="cards-empty">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                    <p>
                        @if (count($filtrosAplicados ?? []))
                            Nenhuma ração encontrada com esses filtros.<br>Tente ajustar ou limpar os filtros.
                        @else
                            Você ainda não cadastrou nenhuma ração.<br>Clique em "Adicionar Tipo de Ração" para começar.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if ($totalRacoes > 0)
            <div class="racoes-paginacao" id="racoesPaginacao">
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
    </div>

    <div class="modal" id="modal">
        <div class="modal-card">
            <button class="close" onclick="fecharModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="modal-top">
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Adicionar Nova Ração</p>
            </div>

            <form method="POST" action="{{ route('racao.add') }}">
                @csrf

                <label for="nome_racao">Nome da Ração</label>
                <div class="input-box">
                    <i class="fa-solid fa-wheat-awn"></i>
                    <input type="text" id="nome_racao" name="nome_racao" placeholder="Ração Premium" required>
                </div>

                <label for="fazenda_id">Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <select id="fazenda_id" name="fazenda_id" required onchange="aplicarFiltroFazendaLoteRacao('fazenda_id', 'lote_id')">
                        <option value="" disabled selected>Escolha uma fazenda...</option>
                        @foreach ($fazendas as $fazenda)
                            <option value="{{ $fazenda->id }}">{{ $fazenda->nome_fazenda }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="lote_id">Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-layer-group"></i>
                    <select id="lote_id" name="lote_id" required disabled onchange="atualizarPreviewLoteRacao(this, '')">
                        <option value="" disabled selected>Escolha primeiro a fazenda...</option>
                        @foreach ($lotes as $lote)
                            <option value="{{ $lote->id }}"
                                data-fazenda="{{ $lote->fazenda_id }}"
                                data-tipo="{{ $lote->tipo_animal }}"
                                data-sexo="{{ $lote->sexo }}"
                                data-idade="{{ $lote->idade_media }}"
                                data-finalidade="{{ $lote->finalidade }}">
                                {{ $lote->nome_lote }} · {{ $lote->tipo_animal }} · {{ $lote->sexo }} · {{ $lote->idade_media }}
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

                <div class="row">
                    <div class="campo">
                        <label for="estoque">Estoque atual (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-box"></i>
                            <input type="number" step="0.01" id="estoque" name="estoque" placeholder="500" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="estoque_total">Estoque Total (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-warehouse"></i>
                            <input type="number" step="0.01" min="1" id="estoque_total" name="estoque_total" placeholder="1000" required>
                        </div>
                        <small class="dica-campo">Capacidade máxima, usada pra calcular o nível de estoque em porcentagem.</small>
                    </div>
                </div>

                <label for="consumo">Consumo (kg/dia)</label>
                <div class="input-box">
                    <i class="fa-solid fa-bowl-food"></i>
                    <input type="number" step="0.01" id="consumo" name="consumo" placeholder="25" required>
                </div>

                <label for="valor_kg">Valor do kg (R$)</label>
                <div class="input-box">
                    <i class="fa-solid fa-tag"></i>
                    <input type="number" step="0.01" min="0" id="valor_kg" name="valor_kg" placeholder="2,50" required>
                </div>

                <label for="fornecedor">Fornecedor</label>
                <div class="input-box">
                    <i class="fa-solid fa-truck"></i>
                    <input type="text" id="fornecedor" name="fornecedor" placeholder="Fornecedor Agro" required>
                </div>

                <label for="reposicao">Reposição</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="reposicao" name="reposicao" required>
                </div>

                <button type="submit" class="submit-btn">
                   Adicionar Ração
                </button>
            </form>
        </div>
    </div>

    <div class="modal-m" id="modalEditar">
        <div class="modal-m-card">
            <button class="close" onclick="fecharModalEditar()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="modal-m-top">
                <img src="{{ asset('Logo.png') }}" alt="Logo">
                <p>Atualizar Ração</p>
            </div>

            <form method="POST" id="formEditar">
                @csrf
                @method('PUT')

                <label for="edit_nome_racao">Nome da Ração</label>
                <div class="input-box">
                    <i class="fa-solid fa-wheat-awn"></i>
                    <input type="text" id="edit_nome_racao" name="nome_racao" required>
                </div>

                <label for="edit_fazenda_id">Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <select id="edit_fazenda_id" name="fazenda_id" required onchange="aplicarFiltroFazendaLoteRacao('edit_fazenda_id', 'edit_lote_id')">
                        <option value="" disabled>Escolha uma fazenda...</option>
                        @foreach ($fazendas as $fazenda)
                            <option value="{{ $fazenda->id }}">{{ $fazenda->nome_fazenda }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="edit_lote_id">Lote</label>
                <div class="input-box">
                    <i class="fa-solid fa-layer-group"></i>
                    <select id="edit_lote_id" name="lote_id" required onchange="atualizarPreviewLoteRacao(this, 'edit_')">
                        <option value="" disabled>Escolha primeiro a fazenda...</option>
                        @foreach ($lotes as $lote)
                            <option value="{{ $lote->id }}"
                                data-fazenda="{{ $lote->fazenda_id }}"
                                data-tipo="{{ $lote->tipo_animal }}"
                                data-sexo="{{ $lote->sexo }}"
                                data-idade="{{ $lote->idade_media }}"
                                data-finalidade="{{ $lote->finalidade }}">
                                {{ $lote->nome_lote }} · {{ $lote->tipo_animal }} · {{ $lote->sexo }} · {{ $lote->idade_media }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="dica-campo">Selecione novamente o lote pra confirmar (ou trocar) o perfil desta ração.</small>

                <div class="lote-preview" id="edit_lotePreview" hidden>
                    <div><i class="fa-solid fa-cow"></i> <strong>Tipo:</strong> <span id="edit_previewTipo"></span></div>
                    <div><i class="fa-solid fa-venus-mars"></i> <strong>Sexo:</strong> <span id="edit_previewSexo"></span></div>
                    <div><i class="fa-solid fa-hourglass-half"></i> <strong>Idade:</strong> <span id="edit_previewIdade"></span></div>
                    <div><i class="fa-solid fa-bullseye"></i> <strong>Finalidade:</strong> <span id="edit_previewFinalidade"></span></div>
                </div>

                <div class="row">
                    <div class="campo">
                        <label for="edit_estoque">Estoque atual (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-box"></i>
                            <input type="number" step="0.01" id="edit_estoque" name="estoque" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="edit_estoque_total">Estoque Total (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-warehouse"></i>
                            <input type="number" step="0.01" min="1" id="edit_estoque_total" name="estoque_total" required>
                        </div>
                        <small class="dica-campo">Capacidade máxima, usada pra calcular o nível de estoque em porcentagem.</small>
                    </div>
                </div>

                <label for="edit_consumo">Consumo (kg/dia)</label>
                <div class="input-box">
                    <i class="fa-solid fa-bowl-food"></i>
                    <input type="number" step="0.01" id="edit_consumo" name="consumo" required>
                </div>

                <label for="edit_valor_kg">Valor do kg (R$)</label>
                <div class="input-box">
                    <i class="fa-solid fa-tag"></i>
                    <input type="number" step="0.01" min="0" id="edit_valor_kg" name="valor_kg" required>
                </div>

                <label for="edit_fornecedor">Fornecedor</label>
                <div class="input-box">
                    <i class="fa-solid fa-truck"></i>
                    <input type="text" id="edit_fornecedor" name="fornecedor" required>
                </div>

                <label for="edit_reposicao">Reposição</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar"></i>
                    <input type="date" id="edit_reposicao" name="reposicao" required>
                </div>

                <button type="submit" class="submit-btn">
                    Atualizar Ração
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
        const toggleBtn = document.getElementById('toggleMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth > 992) {
                // Modo Desktop: Encolhe a barra lateral e expande o dashboard
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            } else {
                // Modo Mobile/Tablet: Abre como um menu flutuante por cima
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

</script>
<script>


        // MODAL CADASTRAR
        function abrirModal() {
            document.getElementById('modal').classList.add('active');
        }

        function fecharModal() {
            document.getElementById('modal').classList.remove('active');
        }

        // FAZENDA -> LOTE (cascata): só mostra lotes da fazenda escolhida,
        // igual ao padrão já usado no cadastro de Cocho.
        function aplicarFiltroFazendaLoteRacao(idSelectFazenda, idSelectLote) {
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
                esconderPreviewLoteRacao(prefixo);
            }
        }

        // Lê tipo/sexo/idade/finalidade do <option> escolhido e mostra na
        // prévia — o que realmente vai ser salvo é decidido pelo servidor
        // a partir do lote_id, isso aqui é só feedback visual.
        function atualizarPreviewLoteRacao(selectLote, prefixo) {
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

        function esconderPreviewLoteRacao(prefixo) {
            document.getElementById(prefixo + 'lotePreview').hidden = true;
        }

        // MODAL EDITAR
        function abrirModalEditar(id, fazendaId, nome, tipo, sexo, idade, finalidade, estoque, estoqueTotal, consumo, valorKg, fornecedor, reposicao) {
            document.getElementById('modalEditar').classList.add('active');

            document.getElementById('edit_fazenda_id').value = fazendaId;
            document.getElementById('edit_nome_racao').value = nome;
            document.getElementById('edit_estoque').value = estoque;
            document.getElementById('edit_estoque_total').value = estoqueTotal;
            document.getElementById('edit_consumo').value = consumo;
            document.getElementById('edit_valor_kg').value = valorKg;
            document.getElementById('edit_fornecedor').value = fornecedor;

            // Mostra o perfil atual da ração de cara (mesmo antes de
            // reselecionar o lote) — só uma referência visual do que já
            // está salvo; pra confirmar ou trocar, é preciso escolher o
            // lote de novo no select abaixo.
            aplicarFiltroFazendaLoteRacao('edit_fazenda_id', 'edit_lote_id');
            document.getElementById('edit_lote_id').value = '';
            document.getElementById('edit_previewTipo').textContent = tipo || '—';
            document.getElementById('edit_previewSexo').textContent = sexo || '—';
            document.getElementById('edit_previewIdade').textContent = idade || '—';
            document.getElementById('edit_previewFinalidade').textContent = finalidade || '—';
            document.getElementById('edit_lotePreview').hidden = false;

            // Tratamento caso a data venha com o formato de data e hora do banco (Y-m-d H:i:s)
            if(reposicao.includes(" ")) {
                reposicao = reposicao.split(" ")[0];
            }
            document.getElementById('edit_reposicao').value = reposicao;

            document.getElementById('formEditar').action = `/racao/update/${id}`;
        }

        function fecharModalEditar() {
            document.getElementById('modalEditar').classList.remove('active');
        }

        // CONFIRMAR EXCLUSÃO
        function confirmarExclusao() {
            return confirm('Deseja realmente excluir esta ração?');
        }

        // Alerta "N rações precisam de reposição": clicar mostra/oculta a lista
        function alternarAlertaEstoqueRacao(alerta) {
            const conteudo = alerta.querySelector('.alerta-estoque-lista-wrap');
            const abrindo = conteudo.hidden;

            conteudo.hidden = !abrindo;
            alerta.classList.toggle('aberto', abrindo);
            alerta.setAttribute('aria-expanded', abrindo ? 'true' : 'false');
        }

        // ==========================================
        // LISTA EM LINHAS: "Ver detalhes" e menu "⋮" (mesmo padrão do
        // cadastro de Lote)
        // ==========================================
        function alternarDetalhesRacao(botao) {
            const linha = botao.closest('.racao-row');
            const painel = linha.querySelector('.racao-row-detalhes');
            const rotulo = botao.querySelector('.texto-btn-detalhes');
            const abrindo = painel.hidden;

            painel.hidden = !abrindo;
            botao.classList.toggle('aberto', abrindo);
            rotulo.textContent = abrindo ? 'Ocultar detalhes' : 'Ver detalhes';
        }

        function fecharTodosMenusRacao(exceto) {
            document.querySelectorAll('.racao-row-dropdown.aberto').forEach((dropdown) => {
                if (dropdown !== exceto) dropdown.classList.remove('aberto');
            });
        }

        function alternarMenuRacao(botao) {
            const dropdown = botao.nextElementSibling;
            const jaAberto = dropdown.classList.contains('aberto');
            fecharTodosMenusRacao(null);
            dropdown.classList.toggle('aberto', !jaAberto);
        }

        document.addEventListener('click', (evento) => {
            if (!evento.target.closest('.racao-row-menu')) fecharTodosMenusRacao(null);
        });

        // ORDENAÇÃO + PAGINAÇÃO (mesmo padrão do cadastro de Lote)
        (function () {
            const container = document.getElementById('cardsRacoes');
            if (!container) return;

            const linhas = Array.from(container.querySelectorAll(':scope > .racao-row'));
            if (!linhas.length) return;

            const selectOrdenar = document.getElementById('ordenarRacoes');
            const selectItensPorPagina = document.getElementById('itensPorPagina');
            const infoEl = document.getElementById('paginacaoInfo');
            const paginasEl = document.getElementById('paginacaoPaginas');

            let paginaAtual = 1;
            let itensPorPagina = selectItensPorPagina ? parseInt(selectItensPorPagina.value, 10) : 10;

            function ordenarLinhas(criterio) {
                const comparadores = {
                    nome: (a, b) => a.dataset.nome.localeCompare(b.dataset.nome, 'pt-BR'),
                    estoque: (a, b) => Number(b.dataset.estoque) - Number(a.dataset.estoque),
                    valor: (a, b) => Number(b.dataset.valor) - Number(a.dataset.valor),
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
                        : `Mostrando ${inicio + 1} a ${Math.min(fim, total)} de ${total} ração${total === 1 ? '' : 'ões'}`;
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
        // FILTROS AVANÇADOS (mesmo padrão do Relatório)
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

        // Mapeia o "chave" de cada chip para o(s) parâmetro(s) de query real(is).
        const PARAMS_POR_FILTRO = {
            fazenda: ['fazenda'],
            tipo_animal: ['tipo_animal']
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
                window.location.href = '{{ route('racao.listar') }}';
            });
        }

        // FECHAR AO CLICAR FORA
        window.onclick = function (event) {
            const modal = document.getElementById('modal');
            const modalEditar = document.getElementById('modalEditar');

            if (event.target == modal) {
                fecharModal();
            }
            if (event.target == modalEditar) {
                fecharModalEditar();
            }
            if (event.target == modalFiltros) {
                modalFiltros.classList.remove('active');
            }
        }

        // ABRIR MODAL AUTOMATICAMENTE SE HOUVER ERRO
        @if($errors->any())
            abrirModal();
        @endif
    </script>
</body>
</html>