@extends('layouts.app')

@section('title', 'AgriFarm | Gestão de Estoque')

{{-- Liga o layout de celular (header, menu em gaveta, navegação inferior) — ver layouts/app. --}}
@section('mobile-shell', '1')

@push('styles')
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
            --card-verde-icon-bg: var(--verde-claro, #e8f5ee);
            --card-verde-texto: var(--verde-texto, #0e4a27);

            --surface-muted-bg: #f9fafb;
            --surface-muted-border: #e5e7eb;


        }

        body {
            background: var(--bg-principal);
            color: var(--text-principal);
            transition: background 0.3s, color 0.3s;
        }

        /* Transições suaves globais para a interatividade do menu */
        .sidebar, .navbar, .main {
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

        /* Lado Esquerdo: Hambúrguer + Logo */




        /* Logo diretamente sobre o fundo escuro da navbar, sem caixa/quadrado
           atrás — igual ao Dashboard. */


        /* Lado Direito: Perfil do Usuário + Sair */











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

        /* Classe disparada pelo JS para esconder a sidebar */




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
           Mesma paleta verde do Dashboard, para a página de Estoque reagir do
           mesmo jeito ao modo escuro e ao alto contraste.
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


        }
        body.dark-theme .navbar-right .accessibility-trigger:hover { background: rgba(255, 255, 255, 0.16); color: #ffffff; }
        body.dark-theme

        body.dark-theme .navbar-right .accessibility-trigger { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.16); color: #f1f5f9; }
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
        body.high-contrast .filtros-form input,
        body.high-contrast .editar-estoque-erros,
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

        body.high-contrast .stat-pill-icone {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .estoque-row-fazenda::before { color: #ffff00 !important; opacity: 1; }

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
            padding-top: 20px;
        }

        /* Classe disparada pelo JS para expandir o conteúdo quando a sidebar recolhe */

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
    background: var(--verde-principal, #0e4a27);
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
    box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 20%, transparent);
}

.btn-add:hover {
    background: var(--verde-hover, #0b3d21);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px color-mix(in srgb, var(--verde-principal, #0e4a27) 35%, transparent);
}

.btn-add:hover i {
    transform: rotate(90deg);
}

.btn-add i {
    transition: transform 0.3s ease;
}

.btn-add:active {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px color-mix(in srgb, var(--verde-principal, #0e4a27) 25%, transparent);
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
    border-color: var(--verde-hover, #0b3d21);
    color: var(--verde-texto, #0e4a27);
}

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

.filtros-form select,
.filtros-form input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--surface-muted-border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--text-principal);
    background-color: var(--surface-muted-bg);
    outline: none;
}

.filtros-form select:focus,
.filtros-form input:focus {
    border-color: var(--verde-ativo, #087a47);
}

/* Modal "Editar Estoque" */
#modalEditarEstoque .modal-card { width: 520px; }

.editar-estoque-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px 12px;
}

.editar-estoque-erros {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 16px;
    padding: 12px 14px;
    border-radius: 10px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-size: 13px;
    font-weight: 600;
}

body.dark-theme .editar-estoque-erros {
    background: rgba(185, 28, 28, 0.15);
    border-color: rgba(248, 113, 113, 0.35);
    color: #fca5a5;
}

@media (max-width: 576px) {
    .editar-estoque-grid { grid-template-columns: 1fr; }
    .filtros-form input { font-size: 16px; min-height: 46px; }
}

.filtros-modal-footer {
    display: flex;
    gap: 12px;
    margin-top: 10px;
}

.btn-aplicar {
    flex: 1;
    background: var(--verde-principal, #0e4a27);
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
    background: var(--verde-hover, #0b3d21);
}

.btn-limpar {
    flex: 1;
    background: var(--card-bg);
    color: var(--verde-texto, #0e4a27);
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
        .alerta-sucesso { background: var(--verde-claro, #e8f5ee); border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28)); color: var(--verde-texto, #0e4a27); }

        body.dark-theme .alerta-erro { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 30%, transparent); color: var(--verde-texto, #0e4a27); }

        body.high-contrast .alerta-erro,
        body.high-contrast .alerta-sucesso {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* Alerta "repor estoque" — mesmo critério (Racao::statusEstoque())
           usado no alerta equivalente da tela de Rações. */
        .alerta-estoque {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px 16px 22px;
            border-radius: 14px;
            margin-bottom: 20px;
            cursor: pointer;
            user-select: none;
            background: var(--card-bg);
            border: 1px solid var(--surface-muted-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.15s ease;
        }

        .alerta-estoque::before {
            content: '';
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: 0;
            width: 4px;
            border-radius: 0 4px 4px 0;
        }

        .alerta-estoque:hover { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08); }
        .alerta-estoque:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }

        .alerta-estoque.baixo::before { background: #d97706; }
        .alerta-estoque.critico::before { background: #dc2626; }

        .alerta-estoque-icone {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .alerta-estoque.baixo .alerta-estoque-icone { background: rgba(217, 119, 6, 0.12); color: #b45309; }
        .alerta-estoque.critico .alerta-estoque-icone { background: rgba(220, 38, 38, 0.12); color: #dc2626; }

        .alerta-estoque-texto { flex: 1; min-width: 0; }

        .alerta-estoque-eyebrow {
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .alerta-estoque.baixo .alerta-estoque-eyebrow { color: #b45309; }
        .alerta-estoque.critico .alerta-estoque-eyebrow { color: #dc2626; }

        .alerta-estoque-texto strong {
            display: block;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-principal);
        }

        .alerta-estoque-texto p {
            font-size: 0.8125rem;
            color: var(--text-secundario);
            line-height: 1.5;
            margin: 0;
        }

        .main .alerta-estoque-chevron {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            min-height: 32px;
            padding: 0;
            border: none;
            cursor: pointer;
            font-family: inherit;
            margin-top: 2px;
            border-radius: 8px;
            background: var(--surface-muted-bg);
            color: var(--text-secundario);
            font-size: 0.75rem;
            transition: transform 0.2s ease, background 0.15s ease;
        }

        .alerta-estoque:hover .alerta-estoque-chevron { background: var(--surface-muted-border); }
        .alerta-estoque.aberto .alerta-estoque-chevron { transform: rotate(180deg); }

        .alerta-estoque-lista-wrap {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--surface-muted-border);
        }

        .alerta-estoque-lista {
            display: flex;
            flex-direction: column;
            gap: 6px;
            list-style: none;
            margin: 0 0 10px;
            padding: 0;
        }

        .alerta-estoque-lista li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .alerta-estoque-lista li strong {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-principal);
        }

        .alerta-estoque-lista li span {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 3px 8px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .alerta-estoque.baixo .alerta-estoque-lista li span { color: #b45309; background: rgba(217, 119, 6, 0.12); }
        .alerta-estoque.critico .alerta-estoque-lista li span { color: #dc2626; background: rgba(220, 38, 38, 0.12); }

        body.dark-theme .alerta-estoque.baixo::before { background: #fbbf24; }
        body.dark-theme .alerta-estoque.critico::before { background: #f87171; }
        body.dark-theme .alerta-estoque.baixo .alerta-estoque-icone { background: rgba(251, 191, 36, 0.14); color: #fbbf24; }
        body.dark-theme .alerta-estoque.critico .alerta-estoque-icone { background: rgba(248, 113, 113, 0.16); color: #f87171; }
        body.dark-theme .alerta-estoque.baixo .alerta-estoque-eyebrow,
        body.dark-theme .alerta-estoque.baixo .alerta-estoque-texto strong { color: #fbbf24; }
        body.dark-theme .alerta-estoque.critico .alerta-estoque-eyebrow,
        body.dark-theme .alerta-estoque.critico .alerta-estoque-texto strong { color: #f87171; }
        body.dark-theme .alerta-estoque.baixo .alerta-estoque-lista li span { color: #fbbf24; background: rgba(251, 191, 36, 0.16); }
        body.dark-theme .alerta-estoque.critico .alerta-estoque-lista li span { color: #f87171; background: rgba(248, 113, 113, 0.16); }

        body.high-contrast .alerta-estoque {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .alerta-estoque::before {
            background: #ffff00 !important;
        }

        body.high-contrast .alerta-estoque.critico {
            border-color: #ff6b6b !important;
        }

        body.high-contrast .alerta-estoque.critico::before {
            background: #ff6b6b !important;
        }

        body.high-contrast .alerta-estoque-icone,
        body.high-contrast .alerta-estoque-eyebrow,
        body.high-contrast .alerta-estoque-texto strong,
        body.high-contrast .alerta-estoque-lista li span {
            background: transparent !important;
            color: #ffff00 !important;
        }

        body.high-contrast .alerta-estoque-chevron {
            background: #000000 !important;
            border: 1px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .alerta-estoque.critico .alerta-estoque-icone,
        body.high-contrast .alerta-estoque.critico .alerta-estoque-eyebrow,
        body.high-contrast .alerta-estoque.critico .alerta-estoque-texto strong,
        body.high-contrast .alerta-estoque.critico .alerta-estoque-lista li span {
            color: #ff6b6b !important;
        }

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
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            color: #ffffff;
            box-shadow: 0 6px 13px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
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

        .status-badge.status-ok { background: var(--verde-claro, #e8f5ee); color: var(--verde-texto, #0e4a27); border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28)); }
        .status-badge.status-baixo { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-badge.status-esgotado { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        body.dark-theme .status-badge.status-ok { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 25%, transparent); color: var(--verde-texto, #0e4a27); }
        body.dark-theme .status-badge.status-baixo { background: rgba(251, 191, 36, 0.12); border-color: rgba(251, 191, 36, 0.25); color: #fbbf24; }
        body.dark-theme .status-badge.status-esgotado { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }

        /* ==========================================================================
           NÍVEL DE ESTOQUE POR RAÇÃO — percentual (estoque atual / estoque
           total cadastrado) + barrinha visual, dentro da própria linha da
           ração (verde = ok, amarelo = baixo, vermelho = esgotado — mesmo
           critério do alerta "para repor").
           ========================================================================== */
        .estoque-row-nivel-valor.status-ok { color: var(--verde-texto, #0e4a27); }
        .estoque-row-nivel-valor.status-baixo { color: #b45309; }
        .estoque-row-nivel-valor.status-esgotado { color: #dc2626; }
        .estoque-row-nivel-valor.status-indefinido { color: var(--text-secundario); font-weight: 500; }

        body.dark-theme .estoque-row-nivel-valor.status-ok { color: var(--verde-texto, #0e4a27); }
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

        .estoque-row-nivel-fill.status-ok { background: linear-gradient(90deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27)); }
        .estoque-row-nivel-fill.status-baixo { background: linear-gradient(90deg, #fbbf24, #d97706); }
        .estoque-row-nivel-fill.status-esgotado { background: linear-gradient(90deg, #f87171, #dc2626); }

        body.high-contrast .estoque-row-nivel-barra { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .estoque-row-nivel-fill { background: #ffff00 !important; }
        body.high-contrast .estoque-row-nivel-valor.status-ok,
        body.high-contrast .estoque-row-nivel-valor.status-baixo,
        body.high-contrast .estoque-row-nivel-valor.status-esgotado,
        body.high-contrast .estoque-row-nivel-valor.status-indefinido { color: #ffff00 !important; }

        .estoque-row-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

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
            width: 110px;
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
            border-color: var(--verde-ativo, #087a47);
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
    background: var(--verde-principal, #0e4a27);
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 15%, transparent);
}

.submit-btn:hover {
    background: var(--verde-hover, #0b3d21);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px color-mix(in srgb, var(--verde-principal, #0e4a27) 35%, transparent);
}

.submit-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px color-mix(in srgb, var(--verde-principal, #0e4a27) 25%, transparent);
}


/* COPYRIGHT */
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
           MEDIA QUERIES (RESPONSIVIDADE)
           ========================================================================== */
        @media (max-width: 992px) {
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 20px; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .main { padding: 18px; padding-top: 20px; }
            .header h1 { font-size: 1.5rem; }

            .estoques-toolbar { align-items: stretch; }
            .toolbar-ordenar { justify-content: space-between; }

            .estoque-row-main { align-items: flex-start; }
            .estoque-row-id { flex: 1 1 100%; }
            .estoque-row-actions { margin-left: 0; width: 100%; justify-content: flex-end; }

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
        .racao-preview i { color: var(--verde-texto, #0e4a27); width: 16px; }
        .racao-preview strong { font-weight: 600; margin-right: 4px; }
        /* Sem isso, o `hidden` do HTML perde pro `display: grid` acima
           (mesma especificidade, mas regra de autor vence a do navegador) e
           o painel de prévia ficava sempre visível, vazio, antes de
           escolher uma ração. */
        .racao-preview[hidden] { display: none; }

        /* ==========================================================================
           CELULAR (até 767px) — layout de aplicativo. A estrutura Blade e o
           JS (filtros, ordenação, paginação) são os mesmos;
           só a apresentação muda: cada ração vira um card e os blocos são
           reordenados (título → alerta → resumo → filtros → ordenar → lista).
           ========================================================================== */
        @media (max-width: 767px) {
            .main { display: flex; flex-direction: column; }
            .main > * { order: 20; }
            /* Os filhos destes dois wrappers entram direto na coluna, para
               poderem ser reordenados um a um. */
            .top-bar,
            .estoques-toolbar { display: contents; }

            .header { order: 1; margin-bottom: 18px; }
            .alerta-flash { order: 2; margin-bottom: 14px; }
            .alerta-estoque { order: 3; margin-bottom: 14px; }
            .stat-pills { order: 4; margin-bottom: 14px; }
            .top-bar-actions { order: 5; margin-bottom: 12px; }
            .toolbar-ordenar { order: 6; margin-bottom: 16px; }
            .filtros-ativos { order: 7; }
            .estoques-lista { order: 8; }
            .estoques-paginacao { order: 9; }

            /* --- Título --- */
            .header h1 { font-size: 28px; line-height: 1.2; margin-bottom: 6px; }
            .header p { font-size: 14px; line-height: 1.55; }

            /* --- Cards de resumo: um por linha --- */
            .stat-pills { flex-direction: column; gap: 12px; width: 100%; }

            .stat-pill {
                width: 100%;
                min-height: 84px;
                padding: 16px 18px;
                gap: 14px;
                border-radius: 16px;
            }

            .stat-pill-icone { width: 48px; height: 48px; font-size: 1.15rem; }
            .stat-pill-valor { font-size: 1.375rem; overflow-wrap: anywhere; }
            .stat-pill-label { font-size: 0.875rem; font-weight: 500; margin-top: 2px; }

            /* --- Filtros e ordenação: largura total --- */
            .top-bar-actions { width: 100%; flex-direction: column; align-items: stretch; }

            .btn-abrir-filtros {
                width: 100%;
                min-height: 48px;
                justify-content: center;
                font-size: 15px;
                border-radius: 12px;
            }

            .toolbar-ordenar {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
                background: var(--card-bg);
                border: var(--card-border);
                box-shadow: var(--card-shadow);
                border-radius: 14px;
                padding: 12px 14px;
            }

            .toolbar-ordenar label { font-size: 13px; }

            .toolbar-ordenar select {
                width: 100%;
                min-height: 46px;
                font-size: 16px; /* evita zoom automático no iPhone */
                border-radius: 10px;
                padding: 10px 12px;
            }

            .filtros-ativos { margin-bottom: 14px; }

            /* --- Lista: cada ração vira um card --- */
            .estoques-lista {
                background: transparent;
                border: none;
                box-shadow: none;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .estoque-row,
            .estoque-row:first-child,
            .estoque-row:last-child {
                background: var(--card-bg);
                border: var(--card-border);
                border-radius: 16px;
                box-shadow: var(--card-shadow);
                overflow: hidden;
            }

            .estoque-row:hover { background: var(--card-bg); }

            .estoque-row-main {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px 12px;
                padding: 16px;
                align-items: start;
            }

            .estoque-row-id { grid-column: 1 / -1; min-width: 0; gap: 12px; }
            .estoque-row-id > div:last-child { min-width: 0; }

            .estoque-row-main .icon { width: 46px; height: 46px; border-radius: 14px; font-size: 1.15rem; }
            .estoque-row-nome { font-size: 17px; line-height: 1.3; }
            .estoque-row-fazenda { font-size: 13px; margin-top: 2px; }

            .estoque-row-col { min-width: 0; gap: 4px; }
            .estoque-row-col-label { font-size: 11px; }
            .estoque-row-col-valor { font-size: 15px; overflow-wrap: anywhere; }

            .status-badge { font-size: 13px; padding: 6px 12px; }

            /* Lápis à direita, com área de toque de 44px. */
            .estoque-row-actions {
                grid-column: 1 / -1;
                width: 100%;
                margin-left: 0;
                justify-content: flex-end;
                padding-top: 12px;
                border-top: 1px solid var(--surface-muted-border);
            }

            .btn-menu-estoque {
                width: 44px;
                height: 44px;
                flex-shrink: 0;
                border-radius: 12px;
                font-size: 15px;
            }

            /* --- Paginação --- */
            .estoques-paginacao { margin-top: 16px; gap: 10px; }
            .paginacao-paginas button { min-width: 40px; min-height: 40px; }
            .paginacao-itens select { font-size: 16px; }

            /* --- Modal de filtros --- */
            .modal-card {
                width: calc(100% - 24px);
                max-width: none;
                padding: 22px 18px;
                border-radius: 20px;
                max-height: calc(100vh - 32px);
            }

            .filtros-form select { font-size: 16px; min-height: 46px; }
        }

        /* ==========================================================================
        INVENTÁRIO NO PADRÃO DO DASHBOARD — cards de resumo iguais aos do
        Dashboard e, no desktop, a lista vira uma tabela de inventário dentro
        de uma caixa única (cabeçalho de colunas + linhas alinhadas).
        ========================================================================== */
        .estoque-row-id .icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
        }

        .estoque-ledger-head { display: none; }

        /* Tabela só a partir de 1280px: abaixo disso (notebook/tablet com o
           menu lateral aberto) as 8 colunas não cabem e estouravam a tela —
           ali fica o layout em linhas com rótulo em cada campo. (Era 1240px,
           mas as larguras mínimas somam ~912px e o conteúdo em 1240 tem
           900px.) Com o "Zoom do Sistema" ampliado (classe no <html>, ver
           partials/acessibilidade) a tela "encolhe" sem a media query
           perceber — aí também fica o layout em linhas. Regras aninhadas:
           navegador sem CSS aninhado simplesmente usa o layout em linhas. */
        @media (min-width: 1280px) {
          html:not(.zoom-sistema-ampliado) {
            .estoque-ledger-head,
            .estoque-row-main {
                display: grid;
                /* Última coluna (ações) com largura fixa: com "auto" ela
                   ficava com 0px no cabeçalho (vazio) e ~170px nas linhas,
                   e as demais colunas se desalinhavam dos títulos. */
                grid-template-columns: minmax(170px, 1.7fr) minmax(70px, 0.8fr) minmax(90px, 0.9fr) minmax(110px, 1.2fr) minmax(80px, 0.8fr) minmax(90px, 0.9fr) minmax(120px, 1fr) 44px;
                align-items: center;
                gap: 14px;
            }

            .estoque-ledger-head {
                padding: 14px 20px;
                background: linear-gradient(160deg, rgba(249, 115, 22, 0.10), transparent 70%), var(--card-bg);
                border: var(--card-border);
                border-bottom: 1px solid var(--surface-muted-border);
                border-radius: 16px 16px 0 0;
                font-size: 0.6875rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #c2410c;
            }

            .estoques-lista { border-top: none; border-radius: 0 0 16px 16px; }
            .estoque-row:first-child { border-radius: 0; }
            .estoque-row-main { padding: 14px 20px; }
            .estoque-row-id,
            .estoque-row-col { min-width: 0; }
            .estoque-row-col-label { display: none; }
            .estoque-row-actions { margin-left: 0; justify-content: flex-end; }
            .estoque-row:nth-child(even) { background: color-mix(in srgb, var(--surface-muted-bg) 60%, transparent); }
            .estoque-row:hover { background: rgba(249, 115, 22, 0.06); }

            & body.dark-theme .estoque-ledger-head { color: #fb923c; border-color: rgba(255, 255, 255, 0.06); }
            & body.high-contrast .estoque-ledger-head { background: #000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
          }
        }

        /* Celular: os blocos novos entram na mesma ordem do layout de app
           (título → alerta → resumo → filtros → ordenar → lista). */
        @media (max-width: 767px) {
            .pg-top { display: contents; }
            .pg-top-text { order: 1; margin-bottom: 18px; }
            .pg-cards { order: 4; margin-bottom: 14px; }
            .estoques-lista-titulo { order: 6; margin-bottom: 10px; }
        }
    </style>
    @include('partials.page-identity')
@endpush

@section('content')
    <div class="main" data-pg="estoque">

        <div class="pg-top">
            <div class="pg-top-text">
                <h1 class="pg-title"><i class="fa-solid fa-box titulo-icone" aria-hidden="true"></i> Estoque de Rações</h1>
                <p class="pg-sub">Acompanhe o estoque de cada ração cadastrada — os dados vêm direto da tela de Rações; para repor ou ajustar, edite a ração por lá.</p>
            </div>

            <div class="top-bar-actions pg-top-actions">
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
                    <img src="{{ asset('logo.png') }}" alt="Logo">
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

        {{-- EDITAR ESTOQUE: salva na própria ração (RacaoController::update —
             mesma validação e o ajuste de saldo vira histórico) e volta pra
             esta tela, sem trocar de página. --}}
        <div id="modalEditarEstoque" class="modal">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="tituloModalEditarEstoque">
                <button type="button" class="close" id="closeEditarEstoqueBtn" aria-label="Fechar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <div class="modal-top">
                    <img src="{{ asset('logo.png') }}" alt="Logo">
                    <h2 id="tituloModalEditarEstoque">Editar Estoque</h2>
                    <p>Atualize o estoque, o consumo e o preço da ração</p>
                </div>

                @if ($errors->any() && old('origem_tela') !== 'movimentacao')
                    <div class="editar-estoque-erros" role="alert">
                        @foreach ($errors->all() as $erro)
                            <div><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> {{ $erro }}</div>
                        @endforeach
                    </div>
                @endif

                <form id="formEditarEstoque" method="POST" action="{{ old('racao_id') ? route('racao.update', old('racao_id')) : '' }}" class="filtros-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="origem" value="estoque">
                    <input type="hidden" name="racao_id" id="editarEstoqueId" value="{{ old('racao_id') }}">
                    <input type="hidden" name="fazenda_id" id="editarEstoqueFazenda" value="{{ old('fazenda_id') }}">

                    <div class="form-group">
                        <label for="editarEstoqueNome">Nome da ração</label>
                        <input type="text" name="nome_racao" id="editarEstoqueNome" value="{{ old('nome_racao') }}" maxlength="255" required>
                    </div>

                    <div class="editar-estoque-grid">
                        <div class="form-group">
                            <label for="editarEstoqueQuantidade">Quantidade em estoque (kg)</label>
                            <input type="number" name="estoque" id="editarEstoqueQuantidade" value="{{ old('estoque') }}" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="editarEstoqueTotal">Estoque total / capacidade (kg)</label>
                            <input type="number" name="estoque_total" id="editarEstoqueTotal" value="{{ old('estoque_total') }}" min="1" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="editarEstoqueConsumo">Consumo diário (kg/dia)</label>
                            <input type="number" name="consumo" id="editarEstoqueConsumo" value="{{ old('consumo') }}" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="editarEstoqueValor">Valor do kg (R$)</label>
                            <input type="number" name="valor_kg" id="editarEstoqueValor" value="{{ old('valor_kg') }}" min="0.01" step="0.01" required>
                        </div>
                    </div>

                    <div class="filtros-modal-footer">
                        <button type="submit" class="btn-aplicar">
                            <i class="fa-solid fa-floppy-disk"></i> Salvar
                        </button>
                        <button type="button" class="btn-limpar" id="cancelarEditarEstoqueBtn">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ENTRADA / SAÍDA: movimentação real no histórico (EstoqueController
             ::entrada/saida, mesmas regras da API). Saída nunca deixa negativo. --}}
        <div id="modalMovimentacao" class="modal">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="tituloModalMovimentacao">
                <button type="button" class="close" id="closeMovimentacaoBtn" aria-label="Fechar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                <div class="modal-top">
                    <img src="{{ asset('logo.png') }}" alt="Logo">
                    <h2 id="tituloModalMovimentacao">Entrada / Saída de Ração</h2>
                    <p id="movimentacaoSaldo">Registre uma compra (entrada) ou uma retirada (saída) — fica no histórico do estoque.</p>
                </div>

                @if ($errors->any() && old('origem_tela') === 'movimentacao')
                    <div class="editar-estoque-erros" role="alert">
                        @foreach ($errors->all() as $erro)
                            <div><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> {{ $erro }}</div>
                        @endforeach
                    </div>
                @endif

                <form id="formMovimentacao" method="POST" action="{{ old('tipo_mov') === 'saida' ? route('estoque.saida') : route('estoque.entrada') }}" class="filtros-form">
                    @csrf
                    <input type="hidden" name="origem_tela" value="movimentacao">
                    <input type="hidden" name="racao_id" id="movimentacaoRacaoId" value="{{ old('racao_id') }}">

                    <div class="form-group">
                        <label for="movimentacaoTipo">Tipo</label>
                        <select name="tipo_mov" id="movimentacaoTipo" required>
                            <option value="entrada" @selected(old('tipo_mov', 'entrada') === 'entrada')>Entrada (compra / reposição)</option>
                            <option value="saida" @selected(old('tipo_mov') === 'saida')>Saída (uso, perda, venda)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="movimentacaoQuantidade">Quantidade (kg)</label>
                        <input type="number" name="quantidade" id="movimentacaoQuantidade" value="{{ old('quantidade') }}" min="0.01" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="movimentacaoObservacao">Observação (opcional)</label>
                        <input type="text" name="observacao" id="movimentacaoObservacao" value="{{ old('observacao') }}" maxlength="255" placeholder="Ex.: Nota fiscal 1234">
                    </div>

                    <div class="filtros-modal-footer">
                        <button type="submit" class="btn-aplicar">
                            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Registrar
                        </button>
                        <button type="button" class="btn-limpar" id="cancelarMovimentacaoBtn">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
                        </button>
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
            {{-- O cartão inteiro abre com o mouse; para teclado e leitor de tela
                 o controle é o <button> da seta (antes o div inteiro era
                 role="button" e a lista de rações virava o "nome" do botão). --}}
            <div class="alerta-estoque {{ $temEsgotado ? 'critico' : 'baixo' }}"
                onclick="alternarAlertaEstoque(this)">
                <div class="alerta-estoque-icone">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                </div>
                <div class="alerta-estoque-texto">
                    <span class="alerta-estoque-eyebrow">{{ $temEsgotado ? 'Estoque crítico' : 'Atenção ao estoque' }}</span>
                    <strong>
                        {{ $racoesParaRepor->count() }} {{ $racoesParaRepor->count() == 1 ? 'ração precisa' : 'rações precisam' }} de reposição
                    </strong>
                    <div hidden class="alerta-estoque-lista-wrap" id="alertaEstoqueLista">
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
                <button type="button" class="alerta-estoque-chevron" aria-expanded="false" aria-controls="alertaEstoqueLista"
                    aria-label="Mostrar as rações que precisam de reposição"
                    onclick="event.stopPropagation(); alternarAlertaEstoque(this.closest('.alerta-estoque'))">
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @php
            $comCapacidade = $racaos->filter(fn ($r) => (float) $r->estoque_total > 0);
            $capacidadeArmazem = (float) $comCapacidade->sum('estoque_total');
            $ocupacaoArmazem = $capacidadeArmazem > 0
                ? (int) round(min(100, $comCapacidade->sum('estoque') / $capacidadeArmazem * 100))
                : null;
            $corOcupacao = $ocupacaoArmazem === null ? '#94a3b8' : ($ocupacaoArmazem < 25 ? '#dc2626' : ($ocupacaoArmazem < 50 ? '#f59e0b' : '#16a34a'));
            $qtdStatus = ['ok' => 0, 'baixo' => 0, 'esgotado' => 0];
            foreach ($racaos as $racaoStatus) {
                $qtdStatus[$racaoStatus->statusEstoque()]++;
            }
            $menorAutonomia = $racaos->filter(fn ($r) => $r->diasRestantesEstoque() !== null)
                ->sortBy(fn ($r) => $r->diasRestantesEstoque())
                ->first();
        @endphp

        <!-- CARDS DE RESUMO (mesmo visual dos cards do Dashboard) -->
        <section class="pg-cards pg-cards--destaque" aria-label="Resumo do estoque de rações">
            <div class="pg-card pg-laranja">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-warehouse" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Quantidade no Armazém</span>
                        <span class="pg-card-value">{{ number_format($quantidadeTotalKg, 0, ',', '.') }} kg</span>
                        <span class="pg-card-sub">
                            @if ($capacidadeArmazem > 0)
                                de {{ number_format($capacidadeArmazem, 0, ',', '.') }} kg de capacidade
                            @else
                                Cadastre o Estoque Total das rações
                            @endif
                        </span>
                    </div>
                    <div class="pg-card-visual">
                        <div class="pg-tank" style="--v: {{ $ocupacaoArmazem ?? 0 }}%; --tank-cor: {{ $corOcupacao }};" role="img" aria-label="Ocupação do armazém: {{ $ocupacaoArmazem !== null ? $ocupacaoArmazem . '%' : 'indisponível' }}">
                            <div class="pg-tank-fill"></div>
                            <div class="pg-tank-label">{{ $ocupacaoArmazem !== null ? $ocupacaoArmazem . '%' : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-verde">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-sack-dollar" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Valor em Estoque</span>
                        <span class="pg-card-value">R$ {{ number_format($valorTotalEstoque, 2, ',', '.') }}</span>
                        <span class="pg-card-sub">Quantidade × valor do kg</span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-azul">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Menor Autonomia</span>
                        @if ($menorAutonomia)
                            <span class="pg-card-value">{{ $menorAutonomia->diasRestantesEstoque() }} {{ $menorAutonomia->diasRestantesEstoque() == 1 ? 'dia' : 'dias' }}</span>
                            <span class="pg-card-sub">{{ $menorAutonomia->nome_racao }}</span>
                        @else
                            <span class="pg-card-value">—</span>
                            <span class="pg-card-sub">Sem consumo para estimar</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pg-card pg-roxo">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Rações em Estoque</span>
                        <span class="pg-card-value">{{ $totalRacoesEstoque }}</span>
                        <span class="pg-card-sub">Situação de cada uma abaixo</span>
                    </div>
                </div>
                <div class="pg-card-foot">
                    <div class="pg-segbar" role="img" aria-label="{{ $qtdStatus['ok'] }} com estoque OK, {{ $qtdStatus['baixo'] }} com estoque baixo, {{ $qtdStatus['esgotado'] }} esgotadas">
                        @if ($qtdStatus['ok']) <span style="--v: {{ $qtdStatus['ok'] }}; --seg-cor: var(--verde-principal, #0e4a27);"></span> @endif
                        @if ($qtdStatus['baixo']) <span style="--v: {{ $qtdStatus['baixo'] }}; --seg-cor: #f59e0b;"></span> @endif
                        @if ($qtdStatus['esgotado']) <span style="--v: {{ $qtdStatus['esgotado'] }}; --seg-cor: #dc2626;"></span> @endif
                    </div>
                    <div class="pg-legend">
                        <span class="pg-legend-item" style="--seg-cor: var(--verde-principal, #0e4a27);">OK <strong>{{ $qtdStatus['ok'] }}</strong></span>
                        <span class="pg-legend-item" style="--seg-cor: #f59e0b;">Baixo <strong>{{ $qtdStatus['baixo'] }}</strong></span>
                        <span class="pg-legend-item" style="--seg-cor: #dc2626;">Esgotado <strong>{{ $qtdStatus['esgotado'] }}</strong></span>
                    </div>
                </div>
            </div>
        </section>

        <div class="estoques-toolbar">
            <h2 class="pg-list-title estoques-lista-titulo"><i class="fa-solid fa-table-list" aria-hidden="true"></i> Inventário por ração</h2>

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

        @if ($totalRacoesEstoque > 0)
            <!-- Cabeçalho de colunas do inventário (só desktop): os rótulos
                 aparecem uma vez aqui, em vez de repetidos em cada linha. -->
            <div class="estoque-ledger-head" aria-hidden="true">
                <span>Ração</span>
                <span>Tipo</span>
                <span>Quantidade</span>
                <span>Nível</span>
                <span>Valor do kg</span>
                <span>Dias restantes</span>
                <span>Status</span>
                <span></span>
            </div>
        @endif

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
                            <span class="estoque-row-col-label">Dias restantes de estoque</span>
                            <span class="estoque-row-col-valor">{{ $diasRestantesItem !== null ? $diasRestantesItem . ($diasRestantesItem == 1 ? ' dia' : ' dias') : '—' }}</span>
                        </div>

                        <div class="estoque-row-col">
                            <span class="estoque-row-col-label">Status</span>
                            <span class="status-badge status-{{ $statusEstoqueItem }}">
                                <i class="fa-solid fa-circle" style="font-size:0.5rem;"></i>
                                {{ $statusLabelItem }}
                            </span>
                        </div>

                        <div class="estoque-row-actions">
                            <button type="button" class="btn-menu-estoque" title="Editar estoque desta ração" aria-label="Editar estoque de {{ $racao->nome_racao }}"
                                data-racao="{{ json_encode($racao->only(['id', 'fazenda_id', 'nome_racao', 'estoque', 'estoque_total', 'consumo', 'valor_kg'])) }}"
                                onclick="abrirModalEditarEstoque(JSON.parse(this.dataset.racao))">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" class="btn-menu-estoque" title="Registrar entrada ou saída desta ração" aria-label="Registrar entrada ou saída de {{ $racao->nome_racao }}"
                                data-racao="{{ json_encode($racao->only(['id', 'nome_racao', 'estoque'])) }}"
                                onclick="abrirModalMovimentacao(JSON.parse(this.dataset.racao))">
                                <i class="fa-solid fa-right-left" aria-hidden="true"></i>
                            </button>
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

@endsection

@push('scripts')
    <script>
        // ==========================================
        // LISTA EM LINHAS: editar/excluir acontece na tela de Rações,
        // fonte única desses dados.
        // ==========================================
        // Alerta "N rações precisam de reposição": clicar mostra/oculta a lista
        function alternarAlertaEstoque(alerta) {
            const conteudo = alerta.querySelector('.alerta-estoque-lista-wrap');
            const abrindo = conteudo.hidden;

            conteudo.hidden = !abrindo;
            alerta.classList.toggle('aberto', abrindo);
            const botao = alerta.querySelector('.alerta-estoque-chevron');
            if (botao) {
                botao.setAttribute('aria-expanded', abrindo ? 'true' : 'false');
                botao.setAttribute('aria-label', abrindo ? 'Esconder as rações que precisam de reposição' : 'Mostrar as rações que precisam de reposição');
            }
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

        // ==========================================
        // EDITAR ESTOQUE (modal — salva via racao.update e volta pra cá)
        // ==========================================
        const modalEditarEstoque = document.getElementById('modalEditarEstoque');
        const formEditarEstoque = document.getElementById('formEditarEstoque');
        const urlAtualizarRacao = "{{ route('racao.update', '__ID__') }}";

        function abrirModalEditarEstoque(racao) {
            const campos = {
                editarEstoqueId: racao.id,
                editarEstoqueFazenda: racao.fazenda_id,
                editarEstoqueNome: racao.nome_racao,
                editarEstoqueQuantidade: racao.estoque,
                editarEstoqueTotal: racao.estoque_total,
                editarEstoqueConsumo: racao.consumo,
                editarEstoqueValor: racao.valor_kg,
            };
            Object.entries(campos).forEach(([id, valor]) => {
                document.getElementById(id).value = valor ?? '';
            });

            formEditarEstoque.action = urlAtualizarRacao.replace('__ID__', racao.id);
            // Erros de uma tentativa anterior não valem para outra ração.
            modalEditarEstoque.querySelector('.editar-estoque-erros')?.remove();
            modalEditarEstoque.classList.add('active');
            document.getElementById('editarEstoqueQuantidade').focus();
        }

        function fecharModalEditarEstoque() {
            modalEditarEstoque.classList.remove('active');
        }

        document.getElementById('closeEditarEstoqueBtn').onclick = fecharModalEditarEstoque;
        document.getElementById('cancelarEditarEstoqueBtn').onclick = fecharModalEditarEstoque;

        // Validação recusada no servidor: reabre o modal com o que foi digitado.
        @if ($errors->any() && old('origem') === 'estoque')
            modalEditarEstoque.classList.add('active');
        @endif

        // ==========================================
        // ENTRADA / SAÍDA (modal — EstoqueController::entrada/saida)
        // ==========================================
        const modalMovimentacao = document.getElementById('modalMovimentacao');
        const formMovimentacao = document.getElementById('formMovimentacao');
        const tipoMovimentacao = document.getElementById('movimentacaoTipo');
        const urlsMovimentacao = { entrada: "{{ route('estoque.entrada') }}", saida: "{{ route('estoque.saida') }}" };

        tipoMovimentacao.addEventListener('change', () => {
            formMovimentacao.action = urlsMovimentacao[tipoMovimentacao.value];
        });

        function abrirModalMovimentacao(racao) {
            document.getElementById('movimentacaoRacaoId').value = racao.id;
            document.getElementById('tituloModalMovimentacao').textContent = 'Entrada / Saída — ' + racao.nome_racao;
            document.getElementById('movimentacaoSaldo').textContent = 'Estoque atual: '
                + Number(racao.estoque || 0).toLocaleString('pt-BR', { maximumFractionDigits: 2 }) + ' kg. A saída não pode passar disso.';
            document.getElementById('movimentacaoQuantidade').value = '';
            document.getElementById('movimentacaoObservacao').value = '';
            tipoMovimentacao.value = 'entrada';
            formMovimentacao.action = urlsMovimentacao.entrada;
            modalMovimentacao.querySelector('.editar-estoque-erros')?.remove();
            modalMovimentacao.classList.add('active');
            document.getElementById('movimentacaoQuantidade').focus();
        }

        function fecharModalMovimentacao() {
            modalMovimentacao.classList.remove('active');
        }

        document.getElementById('closeMovimentacaoBtn').onclick = fecharModalMovimentacao;
        document.getElementById('cancelarMovimentacaoBtn').onclick = fecharModalMovimentacao;

        @if ($errors->any() && old('origem_tela') === 'movimentacao')
            modalMovimentacao.classList.add('active');
        @endif

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                modalFiltros?.classList.remove('active');
                fecharModalEditarEstoque();
                fecharModalMovimentacao();
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
            if (event.target == modalEditarEstoque) {
                fecharModalEditarEstoque();
            }
        }
    </script>
@endpush
