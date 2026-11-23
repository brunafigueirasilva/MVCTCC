@extends('layouts.app')

@section('title', 'AgriFarm | Tipos de Rações')

@push('styles')
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




html.ui-movimento-reduzido { /* antes: @media (prefers-reduced-motion) — agora só a opção do painel desliga */
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

body.high-contrast .alerta-estoque-racao::before {
    background: #ffff00 !important;
}

body.high-contrast .alerta-estoque-racao.critico {
    border-color: #ff6b6b !important;
}

body.high-contrast .alerta-estoque-racao.critico::before {
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

body.high-contrast .alerta-estoque-racao.critico .alerta-estoque-icone,
body.high-contrast .alerta-estoque-racao.critico .alerta-estoque-eyebrow,
body.high-contrast .alerta-estoque-racao.critico .alerta-estoque-texto strong,
body.high-contrast .alerta-estoque-racao.critico .alerta-estoque-lista li span {
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
    padding-top: 20px;
}

/* Classe disparada pelo JS para expandir o conteúdo quando a sidebar recolhe */

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
    border-color: var(--verde-ativo, #087a47);
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
        .alerta-sucesso { background: var(--verde-claro, #e8f5ee); border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28)); color: var(--verde-texto, #0e4a27); }

        body.dark-theme .alerta-erro { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 30%, transparent); color: var(--verde-texto, #0e4a27); }

        body.high-contrast .alerta-erro,
        body.high-contrast .alerta-sucesso {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* Alerta "comprar ração" — mesma regra de estoque baixo/esgotado
           usada nos alertas do Dashboard (Racao::DIAS_SEGURANCA_ESTOQUE). */
        .alerta-estoque-racao {
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

        .alerta-estoque-racao::before {
            content: '';
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: 0;
            width: 4px;
            border-radius: 0 4px 4px 0;
        }

        .alerta-estoque-racao:hover { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08); }
        .alerta-estoque-racao:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }

        .alerta-estoque-racao.baixo::before { background: #d97706; }
        .alerta-estoque-racao.critico::before { background: #dc2626; }

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

        .alerta-estoque-racao.baixo .alerta-estoque-icone { background: rgba(217, 119, 6, 0.12); color: #b45309; }
        .alerta-estoque-racao.critico .alerta-estoque-icone { background: rgba(220, 38, 38, 0.12); color: #dc2626; }

        .alerta-estoque-texto { flex: 1; min-width: 0; }

        .alerta-estoque-eyebrow {
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .alerta-estoque-racao.baixo .alerta-estoque-eyebrow { color: #b45309; }
        .alerta-estoque-racao.critico .alerta-estoque-eyebrow { color: #dc2626; }

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

        .alerta-estoque-racao:hover .alerta-estoque-chevron { background: var(--surface-muted-border); }
        .alerta-estoque-racao.aberto .alerta-estoque-chevron { transform: rotate(180deg); }

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

        .alerta-estoque-racao.baixo .alerta-estoque-lista li span { color: #b45309; background: rgba(217, 119, 6, 0.12); }
        .alerta-estoque-racao.critico .alerta-estoque-lista li span { color: #dc2626; background: rgba(220, 38, 38, 0.12); }

        body.dark-theme .alerta-estoque-racao.baixo::before { background: #fbbf24; }
        body.dark-theme .alerta-estoque-racao.critico::before { background: #f87171; }
        body.dark-theme .alerta-estoque-racao.baixo .alerta-estoque-icone { background: rgba(251, 191, 36, 0.14); color: #fbbf24; }
        body.dark-theme .alerta-estoque-racao.critico .alerta-estoque-icone { background: rgba(248, 113, 113, 0.16); color: #f87171; }
        body.dark-theme .alerta-estoque-racao.baixo .alerta-estoque-eyebrow,
        body.dark-theme .alerta-estoque-racao.baixo .alerta-estoque-texto strong { color: #fbbf24; }
        body.dark-theme .alerta-estoque-racao.critico .alerta-estoque-eyebrow,
        body.dark-theme .alerta-estoque-racao.critico .alerta-estoque-texto strong { color: #f87171; }
        body.dark-theme .alerta-estoque-racao.baixo .alerta-estoque-lista li span { color: #fbbf24; background: rgba(251, 191, 36, 0.16); }
        body.dark-theme .alerta-estoque-racao.critico .alerta-estoque-lista li span { color: #f87171; background: rgba(248, 113, 113, 0.16); }

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
        .stat-pill-estoque .stat-pill-icone { background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27)); box-shadow: 0 6px 13px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16); }
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

        .status-badge.status-ok { background: var(--verde-claro, #e8f5ee); color: var(--verde-texto, #0e4a27); border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28)); }
        .status-badge.status-baixo { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-badge.status-esgotado { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        body.dark-theme .status-badge.status-ok { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 25%, transparent); color: var(--verde-texto, #0e4a27); }
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

        .btn-ver-detalhes:hover { border-color: var(--verde-hover, #0b3d21); color: var(--verde-texto, #0e4a27); }
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
            width: 450px;
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
            position: relative;
        }

         .modal-top img,
        .modal-m-top img {
            width: 110px;
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
        .modal-top h2 { font-size: 1.25rem; font-weight: 700; color: var(--text-principal); text-align: center; }
        .modal-top h2 + p { font-size: 0.875rem; font-weight: 400; color: var(--text-secundario); margin-top: 4px; text-align: center; }

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
            border-color: var(--verde-ativo, #087a47);
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
            color: var(--verde-texto, #0e4a27);
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

        /* RESPONSIVO */
        @media (max-width: 992px) {
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 20px; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .main { padding: 18px; padding-top: 20px; }
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

        /* ==========================================================================
        CATÁLOGO NO PADRÃO DO DASHBOARD — cada ração é um card com degradê
        suave na cor da espécie (--esp) e selo 3D redondo, em grade.
        ========================================================================== */
        .racoes-lista {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(300px, 100%), 1fr));
            gap: 20px;
            align-items: start;
            background: none;
            border: none;
            box-shadow: none;
        }

        .racao-row,
        .racao-row:first-child,
        .racao-row:last-child {
            background: linear-gradient(160deg, color-mix(in srgb, var(--esp, var(--verde-principal, #0e4a27)) 12%, transparent), transparent 60%), var(--card-bg);
            border: var(--card-border);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.015);
            transition: box-shadow 0.3s ease;
        }

        .racao-row:hover {
            background: linear-gradient(160deg, color-mix(in srgb, var(--esp, var(--verde-hover, #0b3d21)) 12%, transparent), transparent 60%), var(--card-bg);
            box-shadow: 0 14px 28px -10px rgba(15, 23, 42, 0.12);
        }

        /* Menu "⋮" aberto fica por cima dos cards vizinhos. */
        .racao-row:focus-within { position: relative; z-index: 5; }

        .racao-row-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 16px;
            padding: 20px;
            align-items: start;
        }

        .racao-row-id {
            grid-column: 1 / -1;
            min-width: 0;
            gap: 16px;
        }

        .racao-row-id .icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            font-size: 1.2rem;
        }

        .racao-row-nome { font-size: 1rem; }

        .racao-row-col {
            min-width: 0;
            background: color-mix(in srgb, var(--card-bg) 70%, transparent);
            border: 1px solid var(--surface-muted-border);
            border-radius: 12px;
            padding: 10px 12px;
        }

        .racao-row-col-valor { font-size: 1rem; font-weight: 700; }

        .racao-row-actions {
            grid-column: 1 / -1;
            margin-left: 0;
            justify-content: space-between;
        }

        .racao-row-actions .btn-ver-detalhes { flex: 1; justify-content: center; }

        .racao-row-detalhes {
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 14px 20px 16px;
            border-radius: 0 0 16px 16px;
        }

        .racao-row-detalhe-item { flex: 1 1 100%; }

        body.dark-theme .racao-row { border: 1px solid rgba(255, 255, 255, 0.06); }

        @media (max-width: 640px) {
            .racoes-lista { grid-template-columns: 1fr; }
        }

        @media (max-width: 576px) {
            .racao-row-main { padding: 16px; }
            .racao-row-detalhes { flex-direction: row; padding: 14px 16px 16px; }
        }

        body.high-contrast .racao-row,
        body.high-contrast .racao-row:first-child,
        body.high-contrast .racao-row:last-child {
            background: #000 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }

        body.high-contrast .racao-row-col { background: #000 !important; border-color: #ffff00 !important; }
    </style>
    @include('partials.page-identity')
@endpush

@push('styles')
<style>
    /* ==========================================================================
       TIPOS DE RAÇÃO (catálogo em cards — App\Support\CatalogoRacoes)
       Verdes: paleta oficial (partials/paleta-verde). Cor do ícone: a da
       espécie, igual ao resto da tela.
       ========================================================================== */
    .tr-secao {
        background: var(--card-bg);
        border: var(--card-border);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        padding: 24px;
        margin-bottom: 26px;
    }

    .tr-topo {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px 24px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .tr-topo-texto h2 {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.0625rem;
        font-weight: 700;
        color: var(--text-principal);
    }

    .tr-topo-texto h2 i {
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        border-radius: 9px;
        font-size: 0.875rem;
        background: var(--verde-claro, #e8f5ee);
        color: var(--verde-texto, #0e4a27);
    }

    .tr-topo-texto p {
        margin-top: 6px;
        color: var(--text-secundario);
        font-size: 0.875rem;
        line-height: 1.5;
        max-width: 62ch;
    }

    .tr-filtros { display: flex; gap: 8px; flex-wrap: wrap; }

    .tr-grade {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(258px, 100%), 1fr));
        gap: 18px;
    }

    .tr-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 14px;
        min-width: 0;
        padding: 20px;
        border-radius: 18px;
        background: linear-gradient(180deg, color-mix(in srgb, var(--verde-claro, #e8f5ee) 70%, transparent), transparent 46%), var(--card-bg);
        border: 1px solid var(--surface-border);
        box-shadow: 0 4px 14px -6px rgba(15, 23, 42, 0.08);
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .tr-card:hover,
    .tr-card:focus-within {
        transform: translateY(-4px);
        border-color: var(--verde-borda, rgba(14, 74, 39, 0.28));
        box-shadow: 0 18px 32px -16px color-mix(in srgb, var(--verde-principal, #0e4a27) 45%, transparent);
    }

    .tr-card[hidden] { display: none; }

    .tr-card-topo { display: flex; align-items: center; gap: 14px; min-width: 0; }

    .tr-icone {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #ffffff;
        font-size: 1.2rem;
        background: linear-gradient(145deg, var(--tr-claro), var(--tr-escuro));
        box-shadow: 0 6px 14px -4px var(--tr-glow), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        transition: transform 0.22s ease;
    }

    .tr-card:hover .tr-icone { transform: scale(1.06) rotate(-4deg); }

    .tr-nome {
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.25;
        color: var(--text-principal);
    }

    .tr-ja {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--verde-texto, #0e4a27);
    }

    .tr-tags { display: flex; flex-wrap: wrap; gap: 6px; }

    .tr-tag {
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.71875rem;
        font-weight: 600;
        background: var(--verde-claro, #e8f5ee);
        color: var(--verde-texto, #0e4a27);
    }

    .tr-tag.neutra {
        background: var(--surface-muted-bg);
        color: var(--text-secundario);
        border: 1px solid var(--surface-muted-border);
    }

    .tr-desc {
        font-size: 0.84375rem;
        line-height: 1.5;
        color: var(--text-secundario);
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .tr-nutri { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }

    .tr-nutri div {
        padding: 8px 6px;
        border-radius: 10px;
        text-align: center;
        background: var(--surface-muted-bg);
        border: 1px solid var(--surface-muted-border);
    }

    .tr-nutri strong { display: block; font-size: 0.875rem; font-weight: 800; color: var(--text-principal); }
    .tr-nutri span { font-size: 0.65625rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; color: var(--text-secundario); }

    .tr-rodape { margin-top: auto; display: flex; flex-direction: column; gap: 10px; }

    .tr-compat {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78125rem;
        font-weight: 600;
        color: var(--verde-texto, #0e4a27);
    }

    .tr-compat.nenhum { color: var(--text-secundario); }

    .tr-btn-adicionar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px 16px;
        border: none;
        border-radius: 12px;
        background: var(--verde-principal, #0e4a27);
        color: #ffffff;
        font-family: inherit;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    }

    .tr-btn-adicionar:hover {
        background: var(--verde-hover, #0b3d21);
        box-shadow: 0 8px 18px -8px color-mix(in srgb, var(--verde-principal, #0e4a27) 70%, transparent);
    }

    .tr-btn-adicionar:active { background: var(--verde-ativo, #087a47); transform: scale(0.98); }
    .tr-btn-adicionar:focus-visible { outline: 2px solid var(--verde-ativo, #087a47); outline-offset: 2px; }
    .tr-btn-adicionar i { transition: transform 0.2s ease; }
    .tr-btn-adicionar:hover i { transform: rotate(90deg); }

    .tr-vazio { display: none; padding: 26px 10px; text-align: center; color: var(--text-secundario); font-size: 0.875rem; }
    .tr-vazio.visivel { display: block; }

    /* ---------- Modal "Adicionar" a partir de um tipo ---------- */
    .tr-modal-perfil {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        margin-bottom: 18px;
        border-radius: 14px;
        background: var(--verde-claro, #e8f5ee);
    }

    .tr-modal-perfil .tr-icone { width: 46px; height: 46px; font-size: 1.05rem; }
    .tr-modal-perfil strong { display: block; color: var(--text-principal); font-size: 0.9375rem; }
    .tr-modal-perfil .tr-tags { margin-top: 6px; }
    .tr-modal-perfil .tr-tag { background: var(--card-bg); }

    .tr-lotes-compat {
        margin: 4px 0 14px;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 0.8125rem;
        line-height: 1.45;
        background: var(--surface-muted-bg);
        border: 1px dashed var(--verde-borda, rgba(14, 74, 39, 0.28));
        color: var(--text-secundario);
    }

    .tr-lotes-compat strong { color: var(--verde-texto, #0e4a27); }

    @media (max-width: 768px) {
        .tr-secao { padding: 18px; }
        .tr-topo { align-items: stretch; }
        .tr-filtros { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px; scrollbar-width: thin; }
        .tr-filtros .periodo-chip { flex-shrink: 0; }
    }

    @media (max-width: 560px) {
        .tr-grade { grid-template-columns: 1fr; gap: 14px; }
        .tr-card { padding: 18px; }
    }

    html.ui-movimento-reduzido { /* antes: @media (prefers-reduced-motion) — agora só a opção do painel desliga */
        .tr-card, .tr-icone, .tr-btn-adicionar, .tr-btn-adicionar i { transition: none; }
        .tr-card:hover, .tr-card:focus-within { transform: none; }
        .tr-card:hover .tr-icone, .tr-btn-adicionar:hover i { transform: none; }
    }

    body.dark-theme .tr-card { box-shadow: 0 4px 14px -6px rgba(0, 0, 0, 0.5); }

    body.high-contrast .tr-secao,
    body.high-contrast .tr-card,
    body.high-contrast .tr-nutri div,
    body.high-contrast .tr-lotes-compat,
    body.high-contrast .tr-modal-perfil {
        background: #000000 !important;
        border: 2px solid #ffff00 !important;
        box-shadow: none !important;
    }

    body.high-contrast .tr-icone,
    body.high-contrast .tr-topo-texto h2 i {
        background: #000000 !important;
        border: 2px solid #ffff00;
        color: #ffff00 !important;
        box-shadow: none !important;
    }

    body.high-contrast .tr-nome,
    body.high-contrast .tr-desc,
    body.high-contrast .tr-tag,
    body.high-contrast .tr-compat,
    body.high-contrast .tr-ja,
    body.high-contrast .tr-nutri strong,
    body.high-contrast .tr-nutri span { color: #ffff00 !important; background: transparent !important; }

    body.high-contrast .tr-btn-adicionar { background: #ffff00 !important; color: #000000 !important; }
</style>
@endpush

@section('content')
    <div class="main" data-pg="racoes">
        <div class="pg-top">
            <div class="pg-top-text">
                <h1 class="pg-title"><i class="fa-solid fa-wheat-awn titulo-icone" aria-hidden="true"></i> Tipos de Rações</h1>
                <p class="pg-sub">Acompanhe os tipos de rações cadastrados e garanta a nutrição de precisão para o seu rebanho.</p>
            </div>
            <div class="top-bar-actions pg-top-actions">
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
                    <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
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
            {{-- O cartão inteiro abre com o mouse; para teclado e leitor de tela
                 o controle é o <button> da seta (antes o div inteiro era
                 role="button" e a lista de rações virava o "nome" do botão). --}}
            <div class="alerta-estoque-racao {{ $temEsgotada ? 'critico' : 'baixo' }}"
                onclick="alternarAlertaEstoqueRacao(this)">
                <div class="alerta-estoque-icone">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                </div>
                <div class="alerta-estoque-texto">
                    <span class="alerta-estoque-eyebrow">{{ $temEsgotada ? 'Estoque crítico' : 'Atenção ao estoque' }}</span>
                    <strong>
                        {{ $racoesParaRepor->count() }} {{ $racoesParaRepor->count() == 1 ? 'ração precisa' : 'rações precisam' }} de reposição
                    </strong>
                    <div hidden class="alerta-estoque-lista-wrap" id="alertaRacaoLista">
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
                <button type="button" class="alerta-estoque-chevron" aria-expanded="false" aria-controls="alertaRacaoLista"
                    aria-label="Mostrar as rações que precisam de reposição"
                    onclick="event.stopPropagation(); alternarAlertaEstoqueRacao(this.closest('.alerta-estoque-racao'))">
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @php
            $racoesPorTipo = $racaos->groupBy(fn ($r) => $r->tipo ?: 'Outros')->map->count()->sortDesc();
            $comValor = $racaos->whereNotNull('valor_kg');
            $valorMedioKg = $comValor->count() ? $comValor->avg('valor_kg') : null;
        @endphp

        <!-- CARDS DE RESUMO (mesmo visual dos cards do Dashboard) -->
        <section class="pg-cards" aria-label="Resumo do catálogo de rações">
            <div class="pg-card pg-verde">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-wheat-awn" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Rações Cadastradas</span>
                        <span class="pg-card-value">{{ $totalRacoes }}</span>
                        <span class="pg-card-sub">Para {{ $racoesPorTipo->count() }} {{ $racoesPorTipo->count() == 1 ? 'espécie' : 'espécies' }}</span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-azul">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-box" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Estoque Total</span>
                        <span class="pg-card-value">{{ number_format($estoqueTotalKg, 0, ',', '.') }} kg</span>
                        <span class="pg-card-sub">Somando todas as rações</span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-laranja">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-tag" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Valor Médio do kg</span>
                        <span class="pg-card-value">{{ $valorMedioKg !== null ? 'R$ ' . number_format($valorMedioKg, 2, ',', '.') : '—' }}</span>
                        <span class="pg-card-sub">Entre as rações com preço</span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-roxo">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-truck-ramp-box" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Rações para repor</span>
                        <span class="pg-card-value">{{ $racoesParaRepor->count() }}</span>
                        <span class="pg-card-sub">
                            @if ($racoesParaRepor->isNotEmpty())
                                <i class="fa-solid fa-triangle-exclamation" style="color:#ea580c;" aria-hidden="true"></i> Estoque baixo ou esgotado
                            @else
                                Nenhuma reposição pendente
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </section>

        @if ($racoesPorTipo->isNotEmpty())
            <section class="pg-box" aria-labelledby="tituloEspecies">
                <h2 class="pg-box-title" id="tituloEspecies"><i class="fa-solid fa-paw" aria-hidden="true"></i> Rações por espécie</h2>
                <div class="pg-chips">
                    @foreach ($racoesPorTipo as $tipoRacao => $qtdTipo)
                        @php $icTipo = $iconesPorAnimal[$tipoRacao] ?? $iconePadrao; @endphp
                        <span class="pg-chip">
                            <span class="pg-chip-ico" style="background: linear-gradient(145deg, {{ $icTipo['claro'] }}, {{ $icTipo['escuro'] }});"><i class="fa-solid {{ $icTipo['icone'] }}" aria-hidden="true"></i></span>
                            {{ $tipoRacao }}
                            <span class="pg-chip-count">{{ $qtdTipo }}</span>
                        </span>
                    @endforeach
                </div>
            </section>
        @endif

        @php
            // Primeiro os tipos que já servem a algum lote do usuário; dentro
            // disso, a ordem do catálogo (por espécie).
            $catalogoOrdenado = collect($catalogo ?? [])->values()
                ->map(fn ($m, $i) => $m + ['ordem' => $i])
                ->sortBy([['lotes_compativeis', 'desc'], ['ordem', 'asc']])
                ->values();
            $especiesCatalogo = $catalogoOrdenado->pluck('tipo')->unique()->values();
            $rotuloSexo = fn ($m) => $m['sexo'] ?? 'Todos os sexos';
        @endphp

        @if ($catalogoOrdenado->isNotEmpty())
            <section class="tr-secao" aria-labelledby="tituloTiposRacao">
                <div class="tr-topo">
                    <div class="tr-topo-texto">
                        <h2 id="tituloTiposRacao"><i class="fa-solid fa-seedling" aria-hidden="true"></i> Tipos de ração</h2>
                        <p>Escolha um tipo pronto para cada espécie e finalidade e clique em <strong>Adicionar</strong>. O perfil do animal já vem preenchido e só os lotes compatíveis poderão usar a ração.</p>
                    </div>
                    <div class="tr-filtros" role="group" aria-label="Filtrar tipos de ração por espécie">
                        <button type="button" class="periodo-chip active" data-tr-filtro="" aria-pressed="true">Todas</button>
                        @foreach ($especiesCatalogo as $especie)
                            <button type="button" class="periodo-chip" data-tr-filtro="{{ $especie }}" aria-pressed="false">{{ $especie }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="tr-grade" id="trGrade">
                    @foreach ($catalogoOrdenado as $modelo)
                        @php $icModelo = $iconesPorAnimal[$modelo['tipo']] ?? $iconePadrao; @endphp
                        <article class="tr-card" data-tipo="{{ $modelo['tipo'] }}"
                            style="--tr-claro: {{ $icModelo['claro'] }}; --tr-escuro: {{ $icModelo['escuro'] }}; --tr-glow: {{ $icModelo['glow'] }};"
                            aria-labelledby="tr-nome-{{ $modelo['chave'] }}">
                            <div class="tr-card-topo">
                                <span class="tr-icone" aria-hidden="true"><i class="fa-solid {{ $modelo['icone'] }}"></i></span>
                                <div>
                                    <h3 class="tr-nome" id="tr-nome-{{ $modelo['chave'] }}">{{ $modelo['nome'] }}</h3>
                                    @if ($modelo['ja_cadastradas'] > 0)
                                        <span class="tr-ja"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> {{ $modelo['ja_cadastradas'] }} já {{ $modelo['ja_cadastradas'] == 1 ? 'cadastrada' : 'cadastradas' }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="tr-tags">
                                <span class="tr-tag">{{ $modelo['tipo'] }}</span>
                                <span class="tr-tag">{{ $modelo['finalidade'] ?? 'Uso geral' }}</span>
                                <span class="tr-tag neutra">{{ $rotuloSexo($modelo) }}</span>
                                @if ($modelo['idade'])
                                    <span class="tr-tag neutra">{{ $modelo['idade'] }}</span>
                                @endif
                            </div>

                            <p class="tr-desc">{{ $modelo['descricao'] }}</p>

                            <div class="tr-nutri" aria-label="Referência nutricional">
                                <div><strong>{{ number_format($modelo['proteina'], 0, ',', '.') }}%</strong><span>Proteína</span></div>
                                <div><strong>{{ number_format($modelo['energia'], 2, ',', '.') }}</strong><span>Mcal/kg</span></div>
                                <div><strong>{{ number_format($modelo['fibra'], 0, ',', '.') }}%</strong><span>Fibra</span></div>
                            </div>

                            <div class="tr-rodape">
                                @if ($modelo['lotes_compativeis'] > 0)
                                    <span class="tr-compat"><i class="fa-solid fa-link" aria-hidden="true"></i> Compatível com {{ $modelo['lotes_compativeis'] }} {{ $modelo['lotes_compativeis'] == 1 ? 'lote seu' : 'lotes seus' }}</span>
                                @else
                                    <span class="tr-compat nenhum"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Nenhum lote compatível ainda</span>
                                @endif
                                <button type="button" class="tr-btn-adicionar" onclick="abrirModalCatalogo('{{ $modelo['chave'] }}')"
                                    aria-label="Adicionar {{ $modelo['nome'] }}">
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Adicionar
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p class="tr-vazio" id="trVazio">Nenhum tipo de ração para esta espécie.</p>
            </section>
        @endif

        <div class="racoes-toolbar">
            <h2 class="pg-list-title"><i class="fa-solid fa-list" aria-hidden="true"></i> Suas rações cadastradas</h2>

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
                    style="--esp: {{ $animalIconeRacao['escuro'] }};"
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
                                        @js($racao->nome_racao),
                                        '{{ $racao->tipo }}',
                                        '{{ $racao->sexo }}',
                                        '{{ $racao->idade }}',
                                        '{{ $racao->finalidade }}',
                                        '{{ $racao->estoque }}',
                                        '{{ $racao->estoque_total }}',
                                        '{{ $racao->consumo }}',
                                        '{{ $racao->valor_kg }}',
                                        @js($racao->fornecedor)
                                        )">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </button>
                                    <form action="{{ route('racao.delete', $racao->id) }}" method="POST" data-confirmar="Deseja realmente excluir esta ração? Esta ação não pode ser desfeita." data-confirmar-titulo="Excluir ração">
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

    <!-- MODAL: ADICIONAR RAÇÃO -->
    <div class="modal" id="modal">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
                <h2>Adicionar Tipo de Ração</h2>
                <p>Cadastre um novo tipo de ração para vincular aos lotes e cochos.</p>
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
                    <input type="number" step="0.01" min="0.01" id="valor_kg" name="valor_kg" placeholder="2,50" required>
                </div>

                <label for="fornecedor">Fornecedor <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></label>
                <div class="input-box">
                    <i class="fa-solid fa-truck-field" aria-hidden="true"></i>
                    <input type="text" maxlength="150" id="fornecedor" name="fornecedor" placeholder="Ex.: Cooperativa Agro Sul" value="{{ old('origem') !== 'catalogo' ? old('fornecedor') : '' }}">
                </div>

                <button type="submit" class="submit-btn">
                   Cadastrar Ração
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: ADICIONAR A PARTIR DE UM TIPO DE RAÇÃO (cards do catálogo).
         Mesmo POST de "racao.add", sem lote: o perfil (tipo, sexo,
         finalidade, idade) e os nutrientes vêm do tipo escolhido e o
         servidor valida tudo de novo (RacaoController::validarPerfilSemLote). -->
    <div class="modal" id="modalCatalogo" role="dialog" aria-modal="true" aria-labelledby="catTitulo">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalCatalogo()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
                <h2 id="catTitulo">Adicionar Ração</h2>
                <p>Complete estoque e preço para cadastrar.</p>
            </div>

            <div class="tr-modal-perfil" id="catPerfil">
                <span class="tr-icone" id="catIcone" aria-hidden="true"><i class="fa-solid fa-wheat-awn"></i></span>
                <div>
                    <strong id="catNomeModelo"></strong>
                    <div class="tr-tags" id="catTags"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('racao.add') }}" id="formCatalogo">
                @csrf
                <input type="hidden" name="origem" value="catalogo">
                <input type="hidden" name="modelo" id="cat_modelo" value="{{ old('modelo') }}">
                <input type="hidden" name="tipo" id="cat_tipo">
                <input type="hidden" name="sexo" id="cat_sexo">
                <input type="hidden" name="finalidade" id="cat_finalidade">
                <input type="hidden" name="idade" id="cat_idade">
                <input type="hidden" name="proteina_percentual" id="cat_proteina">
                <input type="hidden" name="energia_mcal_kg" id="cat_energia">
                <input type="hidden" name="fibra_percentual" id="cat_fibra">
                <input type="hidden" name="observacoes" id="cat_observacoes">

                <label for="cat_nome_racao">Nome da Ração</label>
                <div class="input-box">
                    <i class="fa-solid fa-wheat-awn"></i>
                    <input type="text" id="cat_nome_racao" name="nome_racao" value="{{ old('origem') === 'catalogo' ? old('nome_racao') : '' }}" required maxlength="255">
                </div>

                <label for="cat_fazenda_id">Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-location-dot"></i>
                    <select id="cat_fazenda_id" name="fazenda_id" required onchange="atualizarLotesCompativeisCatalogo()">
                        <option value="" disabled {{ old('origem') === 'catalogo' && old('fazenda_id') ? '' : 'selected' }}>Escolha uma fazenda...</option>
                        @foreach ($fazendas as $fazenda)
                            <option value="{{ $fazenda->id }}" @selected(old('origem') === 'catalogo' && (string) old('fazenda_id') === (string) $fazenda->id)>{{ $fazenda->nome_fazenda }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="tr-lotes-compat" id="catLotesCompat" aria-live="polite">Escolha a fazenda para ver os lotes que poderão usar esta ração.</div>

                <div class="row">
                    <div class="campo">
                        <label for="cat_estoque">Estoque atual (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-box"></i>
                            <input type="number" step="0.01" min="0" id="cat_estoque" name="estoque" placeholder="500" value="{{ old('origem') === 'catalogo' ? old('estoque') : '' }}" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="cat_estoque_total">Estoque Total (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-warehouse"></i>
                            <input type="number" step="0.01" min="1" id="cat_estoque_total" name="estoque_total" placeholder="1000" value="{{ old('origem') === 'catalogo' ? old('estoque_total') : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="campo">
                        <label for="cat_consumo">Consumo (kg/dia)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bowl-food"></i>
                            <input type="number" step="0.01" min="0" id="cat_consumo" name="consumo" placeholder="25" value="{{ old('origem') === 'catalogo' ? old('consumo') : '' }}" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="cat_valor_kg">Valor do kg (R$)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-tag"></i>
                            <input type="number" step="0.01" min="0.01" id="cat_valor_kg" name="valor_kg" placeholder="2,50" value="{{ old('origem') === 'catalogo' ? old('valor_kg') : '' }}" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Cadastrar Ração
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
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
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
                    <input type="number" step="0.01" min="0.01" id="edit_valor_kg" name="valor_kg" required>
                </div>

                <label for="edit_fornecedor">Fornecedor <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></label>
                <div class="input-box">
                    <i class="fa-solid fa-truck-field" aria-hidden="true"></i>
                    <input type="text" maxlength="150" id="edit_fornecedor" name="fornecedor" placeholder="Ex.: Cooperativa Agro Sul">
                </div>

                <button type="submit" class="submit-btn">
                    Atualizar Ração
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
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
        function abrirModalEditar(id, fazendaId, nome, tipo, sexo, idade, finalidade, estoque, estoqueTotal, consumo, valorKg, fornecedor) {
            document.getElementById('modalEditar').classList.add('active');

            document.getElementById('edit_fazenda_id').value = fazendaId;
            document.getElementById('edit_nome_racao').value = nome;
            document.getElementById('edit_estoque').value = estoque;
            document.getElementById('edit_estoque_total').value = estoqueTotal;
            document.getElementById('edit_consumo').value = consumo;
            document.getElementById('edit_valor_kg').value = valorKg;
            document.getElementById('edit_fornecedor').value = fornecedor || '';

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

            document.getElementById('formEditar').action = `/racao/update/${id}`;
        }

        function fecharModalEditar() {
            document.getElementById('modalEditar').classList.remove('active');
        }

        // Alerta "N rações precisam de reposição": clicar mostra/oculta a lista
        function alternarAlertaEstoqueRacao(alerta) {
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
                    const fimVisivel = Math.min(fim, total);
                    const plural = total === 1 ? 'ração' : 'rações';

                    if (total === 0) {
                        infoEl.textContent = 'Nenhuma ração encontrada';
                    } else if (inicio + 1 === fimVisivel) {
                        infoEl.textContent = `Mostrando ${fimVisivel} de ${total} ${plural}`;
                    } else {
                        infoEl.textContent = `Mostrando ${inicio + 1} – ${fimVisivel} de ${total} ${plural}`;
                    }
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

        // ==========================================
        // TIPOS DE RAÇÃO (cards do catálogo)
        // ==========================================
        const CATALOGO_RACOES = @json(collect($catalogo ?? [])->keyBy('chave'));
        const CORES_ESPECIE = @json($iconesPorAnimal);
        let modeloCatalogoAtual = null;

        function tagCatalogo(texto) {
            const tag = document.createElement('span');
            tag.className = 'tr-tag';
            tag.textContent = texto;
            return tag;
        }

        function abrirModalCatalogo(chave, manterValores = false) {
            const modelo = CATALOGO_RACOES[chave];
            if (!modelo) return;
            modeloCatalogoAtual = modelo;

            document.getElementById('catTitulo').textContent = 'Adicionar ' + modelo.nome;
            document.getElementById('catNomeModelo').textContent = modelo.nome;

            const cor = CORES_ESPECIE[modelo.tipo] || {};
            const icone = document.getElementById('catIcone');
            icone.style.setProperty('--tr-claro', cor.claro || '#94a3b8');
            icone.style.setProperty('--tr-escuro', cor.escuro || '#64748b');
            icone.style.setProperty('--tr-glow', cor.glow || 'transparent');
            icone.innerHTML = '';
            const i = document.createElement('i');
            i.className = 'fa-solid ' + modelo.icone;
            icone.appendChild(i);

            const tags = document.getElementById('catTags');
            tags.replaceChildren(
                tagCatalogo(modelo.tipo),
                tagCatalogo(modelo.finalidade || 'Uso geral'),
                tagCatalogo(modelo.sexo || 'Todos os sexos'),
                ...(modelo.idade ? [tagCatalogo(modelo.idade)] : []),
            );

            // Perfil e nutrientes do tipo (o servidor valida de novo).
            const campos = {
                cat_modelo: modelo.chave,
                cat_tipo: modelo.tipo,
                cat_sexo: modelo.sexo || '',
                cat_finalidade: modelo.finalidade || '',
                cat_idade: modelo.idade || '',
                cat_proteina: modelo.proteina,
                cat_energia: modelo.energia,
                cat_fibra: modelo.fibra,
                cat_observacoes: modelo.descricao,
            };
            Object.entries(campos).forEach(([id, valor]) => { document.getElementById(id).value = valor; });

            if (!manterValores) {
                document.getElementById('formCatalogo').querySelectorAll('input[type=number]')
                    .forEach((campo) => { campo.value = ''; });
                document.getElementById('cat_nome_racao').value = modelo.nome;
                const fazenda = document.getElementById('cat_fazenda_id');
                // Uma fazenda só: já vem escolhida.
                fazenda.value = fazenda.options.length === 2 ? fazenda.options[1].value : '';
            }

            atualizarLotesCompativeisCatalogo();
            document.getElementById('modalCatalogo').classList.add('active');
            setTimeout(() => document.getElementById('cat_nome_racao').focus(), 50);
        }

        function fecharModalCatalogo() {
            document.getElementById('modalCatalogo').classList.remove('active');
        }

        // Quais lotes da fazenda escolhida poderão usar esta ração (mesma
        // regra do servidor: CompatibilidadeRacao).
        function atualizarLotesCompativeisCatalogo() {
            const caixa = document.getElementById('catLotesCompat');
            const fazendaId = document.getElementById('cat_fazenda_id').value;
            if (!modeloCatalogoAtual || !fazendaId) {
                caixa.textContent = 'Escolha a fazenda para ver os lotes que poderão usar esta ração.';
                return;
            }

            const lotes = (modeloCatalogoAtual.lotes || []).filter((l) => String(l.fazenda_id) === String(fazendaId));
            caixa.replaceChildren();
            if (!lotes.length) {
                caixa.textContent = 'Nenhum lote desta fazenda é compatível com este tipo ainda. Você pode cadastrar a ração mesmo assim e vinculá-la quando tiver um lote compatível.';
                return;
            }
            const destaque = document.createElement('strong');
            destaque.textContent = lotes.length === 1 ? '1 lote compatível: ' : lotes.length + ' lotes compatíveis: ';
            caixa.append(destaque, lotes.map((l) => l.nome_lote).join(', ') + '. Vincule no Plano Alimentar.');
        }

        // Filtro por espécie dos cards.
        document.querySelectorAll('[data-tr-filtro]').forEach((botao) => {
            botao.addEventListener('click', () => {
                const especie = botao.dataset.trFiltro;
                document.querySelectorAll('[data-tr-filtro]').forEach((b) => {
                    const ativo = b === botao;
                    b.classList.toggle('active', ativo);
                    b.setAttribute('aria-pressed', ativo ? 'true' : 'false');
                });
                let visiveis = 0;
                document.querySelectorAll('#trGrade .tr-card').forEach((card) => {
                    const mostra = !especie || card.dataset.tipo === especie;
                    card.hidden = !mostra;
                    if (mostra) visiveis++;
                });
                document.getElementById('trVazio').classList.toggle('visivel', visiveis === 0);
            });
        });

        window.addEventListener('click', (event) => {
            if (event.target === document.getElementById('modalCatalogo')) fecharModalCatalogo();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') fecharModalCatalogo();
        });

        // ABRIR MODAL AUTOMATICAMENTE SE HOUVER ERRO (o mesmo de onde veio)
        @if($errors->any())
            @if (old('origem') === 'catalogo' && old('modelo'))
                abrirModalCatalogo(@json(old('modelo')), true);
            @else
                abrirModal();
            @endif
        @endif
    </script>
@endpush