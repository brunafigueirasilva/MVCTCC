@extends('layouts.app')

@section('title', 'AgriFarm | Gestão de Cochos')

@push('styles')
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

            --card-verde-bg: var(--verde-claro, #e8f5ee);
            --card-verde-border: var(--verde-claro, #e8f5ee);
            --card-verde-icon-bg: var(--verde-claro, #e8f5ee);
            --card-verde-texto: var(--verde-texto, #0e4a27);

            --surface-muted-bg: #f8fafc;
            --surface-muted-border: #cbd5e1;


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

        /* ==========================================================================
        2. NAVBAR SUPERIOR
        (blur do modal de acessibilidade e estrutura de navbar/sidebar/main
        já vêm de layouts/app.blade.php — fonte única, não duplicar aqui)
        ========================================================================== */
















        .navbar-right .accessibility-trigger {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid var(--surface-border);
        }
        .navbar-right .accessibility-trigger:hover { background: #e2e8f0; color: #0f172a; }

        /* ==========================================================================
        3. SIDEBAR LATERAL
        ========================================================================== */



        @media (max-width: 992px) {
            .sidebar-overlay.active { display: block; opacity: 1; }
        }



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
        4. ÁREA PRINCIPAL
        ========================================================================== */
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
            padding-top: 20px;
        }


        .header {
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* "nowrap" por padrão: título e os botões (Filtros/Definir
               cochos) ficam sempre na mesma linha em telas de desktop/
               tablet. Só quebra em telas bem estreitas (ver media query
               de 768px mais abaixo). */
            flex-wrap: nowrap;
            gap: 20px;
        }

        /* Encolhe o texto do título (não os botões) quando faltar espaço,
           em vez de empurrar os botões pra uma segunda linha. */
        .header-title { min-width: 0; flex: 1 1 auto; }
        /* Subtítulo inteiro (quebra linha) — antes era cortado com "…". */
        .header-title p { overflow-wrap: anywhere; }

        .top-bar-actions { flex-shrink: 0; }

        .header-title h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-principal);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .header-title p { color: var(--text-secundario); font-size: 0.9375rem; }

        .btn-add {
            background: var(--verde-principal, #0e4a27);
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
            /* É um <a> aqui ("Definir cochos pelo Lote"): sem sublinhado. */
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 20%, transparent);
        }

        .btn-add:hover {
            background: var(--verde-hover, #0b3d21);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px color-mix(in srgb, var(--verde-principal, #0e4a27) 35%, transparent);
        }

        .btn-add:hover i { transform: rotate(90deg); }
        .btn-add i { transition: transform 0.3s ease; }

        .btn-add:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px color-mix(in srgb, var(--verde-principal, #0e4a27) 25%, transparent);
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

        .btn-abrir-filtros:hover { border-color: var(--verde-hover, #0b3d21); color: var(--verde-texto, #0e4a27); }

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

        .filtros-form select:focus { border-color: var(--verde-ativo, #087a47); }

        .filtros-form small {
            color: var(--text-secundario);
            font-size: 0.71875rem;
        }

        .filtros-modal-footer { display: flex; gap: 12px; margin-top: 10px; }

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

        .btn-aplicar:hover { background: var(--verde-hover, #0b3d21); }

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
            background: var(--verde-claro, #e8f5ee);
            border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28));
            color: var(--verde-texto, #0e4a27);
        }

        body.dark-theme .alerta-erro { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 30%, transparent); color: var(--verde-texto, #0e4a27); }

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
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            box-shadow: 0 6px 13px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
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

        /* Cada LOTE tem seu próprio cartão (.lote-cochos-card). Em telas
           largas, 2 cartões de lote ficam lado a lado (mesma linha); em
           telas menores cai pra 1 coluna (ver responsividade no final do
           arquivo). "align-items: start" evita que um lote com poucos
           cochos fique esticado pra ter a mesma altura do vizinho. */
        .cochos-lista {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            align-items: start;
            gap: 16px;
        }

        .lote-cochos-card {
            background: var(--card-bg);
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            border-radius: 16px;
            /* Sem "overflow: hidden" aqui de propósito — ele cortava o menu
               "⋮" (Editar), que é position:absolute e abre pra baixo da
               linha. As bordas arredondadas ficam por conta das regras
               abaixo, aplicadas só na primeira/última linha. */
        }

        .lote-cochos-card-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 14px;
            background: none;
            border: none;
            border-bottom: 1px solid var(--surface-muted-border);
            cursor: pointer;
            text-align: left;
            font: inherit;
            color: inherit;
        }

        .lote-cochos-card.fechado .lote-cochos-card-header { border-bottom: none; }

        .lote-cochos-card-id { display: flex; align-items: center; gap: 10px; min-width: 160px; flex: 1.4 1 160px; }

        .lote-cochos-card-nome { color: var(--text-principal); font-size: 0.8125rem; font-weight: 700; }

        .lote-cochos-card-fazenda {
            font-size: 0.6875rem;
            color: var(--text-secundario);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .lote-cochos-card-contador {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.6875rem;
            font-weight: 700;
            background: var(--surface-muted-bg);
            color: var(--text-principal);
            border: 1px solid var(--surface-muted-border);
            white-space: nowrap;
        }

        .lote-cochos-card-chevron {
            margin-left: auto;
            color: var(--text-secundario);
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }

        .lote-cochos-card.fechado .lote-cochos-card-chevron { transform: rotate(-90deg); }

        /* Corpo do cartão do lote: grade de mini-cards de cocho (antes era
           uma lista de linhas cheias, ocupando bem mais altura de tela).
           Colunas de largura FIXA (não "1fr") + justify-content:start —
           assim os cards não esticam pra preencher a linha inteira quando
           tem poucos cochos no lote. */
        .lote-cochos-card-body {
            display: grid;
            grid-template-columns: repeat(auto-fill, 190px);
            justify-content: start;
            gap: 12px;
            padding: 16px;
        }

        .lote-cochos-card-body[hidden] { display: none; }

        .lote-cochos-card-vazio {
            padding: 20px;
            font-size: 0.84375rem;
            color: var(--text-secundario);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .lote-cochos-card-vazio i { color: #94a3b8; }

        /* Cada cocho agora é um mini-card dentro da grade do corpo do
           cartão do lote (.lote-cochos-card-body), no lugar da linha cheia
           de antes — ocupa bem menos espaço de tela. */
        .cocho-row {
            border: 1px solid var(--surface-muted-border);
            border-radius: 12px;
            background: var(--card-bg);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .cocho-row:hover { border-color: var(--verde-hover, #0b3d21); box-shadow: var(--card-shadow); }

        .cocho-row-main {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 14px;
        }

        .cocho-row-id { display: flex; align-items: center; gap: 8px; min-width: 0; }

        .icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.84375rem;
            flex-shrink: 0;
            box-shadow: 0 6px 14px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
        }

        /* Ícone menor, só para o mini-card de cocho — o ícone grande (.icon)
           continua no cabeçalho do lote. */
        .cocho-mini-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.84375rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px -3px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent);
        }

        .cocho-row-nome { color: var(--text-principal); font-size: 0.875rem; font-weight: 700; overflow-wrap: break-word; flex: 1; min-width: 0; }

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

        /* Agrupa Capacidade/Nível/Sensor/Status do mini-card num grid 2x2
           (rótulo em cima, valor embaixo, como no cabeçalho do lote). O
           cabeçalho do lote continua usando .cocho-row-col solto, direto no
           flex do próprio cabeçalho. */
        .cocho-row-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 12px;
        }

        .cocho-row-col {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }

        .cocho-row-col-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
            font-weight: 700;
        }

        .cocho-row-col-valor { font-size: 0.84375rem; color: var(--text-principal); font-weight: 600; overflow-wrap: break-word; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 7px;
            border-radius: 20px;
            /* 11px: era 9px — o status é a informação principal da linha. */
            font-size: 0.6875rem;
            font-weight: 700;
            width: fit-content;
            max-width: 100%;
            white-space: normal;
        }

        .status-badge.status-ok { background: var(--verde-claro, #e8f5ee); color: var(--verde-texto, #0e4a27); border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28)); }
        .status-badge.status-baixo { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-badge.status-vazio { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .status-badge.status-indefinido { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        body.dark-theme .status-badge.status-ok { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 25%, transparent); color: var(--verde-texto, #0e4a27); }
        body.dark-theme .status-badge.status-baixo { background: rgba(251, 191, 36, 0.12); border-color: rgba(251, 191, 36, 0.25); color: #fbbf24; }
        body.dark-theme .status-badge.status-vazio { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .status-badge.status-indefinido { background: rgba(148, 163, 184, 0.14); border-color: rgba(148, 163, 184, 0.3); color: #cbd5e1; }


        /* Coluna (não mais linha): agora são 2 botões (Editar + Abastecer)
           e o card do cocho é estreito — lado a lado ficaria espremido. */
        .cocho-row-actions { display: flex; flex-direction: column; gap: 6px; margin-top: 2px; }

        .btn-ver-detalhes {
            width: 100%;
            background: var(--card-bg);
            color: var(--text-principal);
            border: 1px solid var(--surface-muted-border);
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8125rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-ver-detalhes:hover { border-color: var(--verde-hover, #0b3d21); color: var(--verde-texto, #0e4a27); }
        .btn-ver-detalhes i { transition: transform 0.2s ease; font-size: 0.6875rem; }
        .btn-ver-detalhes.aberto i { transform: rotate(180deg); }

        /* Botão "Abastecer" — sólido, pra se destacar como a ação principal
           do cocho (é o que desconta do Estoque de Rações). */
        .btn-abastecer-cocho {
            width: 100%;
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8125rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 10px -3px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent);
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        .btn-abastecer-cocho:hover { transform: translateY(-1px); box-shadow: 0 6px 14px -3px color-mix(in srgb, var(--verde-principal, #0e4a27) 60%, transparent); }

        .btn-abastecer-cocho:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Barra "Abastecer todos os cochos" no topo do corpo do cartão do
           lote — fica FORA do cabeçalho (que já é um <button> clicável pra
           abrir/fechar o cartão; um botão dentro de outro botão é HTML
           inválido e quebraria o clique). */
        .lote-abastecer-bar {
            padding: 12px 16px 0;
        }

        .btn-abastecer-lote {
            width: 100%;
            background: var(--card-bg);
            color: var(--verde-texto, #0e4a27);
            border: 1px dashed var(--verde-secundario, #16a765);
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.8125rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .btn-abastecer-lote:hover { background: var(--verde-claro, #e8f5ee); border-color: var(--verde-hover, #0b3d21); }
        body.dark-theme .btn-abastecer-lote:hover { background: color-mix(in srgb, var(--verde-principal, #0e4a27) 12%, transparent); }

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
            grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 1fr));
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
        .modal-top img { width: 110px; }
        .modal-top p { font-size: 0.9375rem; font-weight: 600; color: var(--text-principal); }

        /* Título em destaque (bold, maior) usado pelo modal de Filtros
           Avançados — os demais modais deste arquivo continuam só com o
           <p> acima, sem <h2>, então esta regra não afeta nenhum deles. */
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
            border-color: var(--verde-ativo, #087a47);
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
            font-size: 0.9375rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 15%, transparent);
        }

        .submit-btn:hover {
            background: var(--verde-hover, #0b3d21);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px color-mix(in srgb, var(--verde-principal, #0e4a27) 35%, transparent);
        }

        .submit-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* ==========================================================================
        7.1 MODAL "ABASTECER" — resumo (estoque atual / quantidade / estoque
        restante) e aviso de estoque insuficiente, atualizados ao vivo pelo
        JS conforme a ração e a quantidade escolhidas.
        ========================================================================== */
        .abastecer-info {
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            border-radius: 12px;
            padding: 12px 14px;
            margin: -4px 0 18px;
            font-size: 0.8125rem;
            color: var(--text-principal);
        }

        .abastecer-info div { display: flex; align-items: center; gap: 6px; }
        .abastecer-info i { color: var(--verde-texto, #0e4a27); width: 16px; text-align: center; flex-shrink: 0; }

        .abastecer-preview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px 14px;
            background: var(--surface-muted-bg);
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            padding: 12px 14px;
            margin: -4px 0 18px;
        }

        .abastecer-preview-item { display: flex; flex-direction: column; gap: 3px; min-width: 0; }

        .abastecer-preview-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secundario);
            font-weight: 700;
        }

        .abastecer-preview-valor { font-size: 0.9375rem; font-weight: 700; color: var(--text-principal); overflow-wrap: break-word; }
        .abastecer-preview-valor.negativo { color: #dc2626; }

        .abastecer-aviso {
            display: none;
            align-items: flex-start;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 10px;
            padding: 12px 14px;
            margin: -4px 0 18px;
            font-size: 0.8125rem;
        }

        .abastecer-aviso.visivel { display: flex; }
        .abastecer-aviso i { margin-top: 2px; flex-shrink: 0; }

        .abastecer-aviso-acoes { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }

        .btn-aviso-acao {
            background: #ffffff;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-aviso-acao:hover { background: #fee2e2; }

        body.dark-theme .abastecer-aviso { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .btn-aviso-acao { background: transparent; color: #fca5a5; border-color: rgba(239, 68, 68, 0.4); }
        body.dark-theme .btn-aviso-acao:hover { background: rgba(239, 68, 68, 0.18); }

        /* ==========================================================================
        8. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard/Fazendas/Lotes
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

        body.high-contrast .lote-cochos-card,
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

        body.high-contrast .lote-cochos-card-header { border-color: #ffff00 !important; }
        body.high-contrast .lote-cochos-card-contador {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }
        body.high-contrast .lote-cochos-card-chevron { color: #ffff00 !important; }

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

        body.high-contrast .btn-abastecer-cocho,
        body.high-contrast .btn-abastecer-lote {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
        }

        body.high-contrast .abastecer-info,
        body.high-contrast .abastecer-preview,
        body.high-contrast .abastecer-aviso {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .btn-aviso-acao {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

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
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 20px; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .main { padding: 18px; padding-top: 20px; }
            .cochos-lista { grid-template-columns: 1fr; }
            .header { flex-wrap: wrap; }
            .header-title p { white-space: normal; }
            .header-title h2 { font-size: 1.5rem; }

            .cochos-toolbar { align-items: stretch; }
            .toolbar-ordenar { justify-content: space-between; }

            .cocho-row-detalhes { padding-left: 20px; }
        }

        @media (max-width: 576px) {
            .navbar { padding: 0 14px; gap: 10px; }
            .navbar-logo img { height: 30px; }
            .header-title p { font-size: 0.844rem; }
            .top-bar-actions { flex-direction: column; align-items: stretch; width: 100%; }

            .stat-pills { flex-direction: column; }
            .stat-pill { width: 100%; }

            .lote-cochos-card-body { grid-template-columns: repeat(auto-fit, minmax(min(140px, 100%), 1fr)); }
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

        /* ==========================================================================
        VISUAL NO PADRÃO DO DASHBOARD — cards de lote com degradê azul suave,
        selo 3D redondo e o nível de ração do lote no cabeçalho.
        ========================================================================== */
        .lote-cochos-card {
            background: linear-gradient(160deg, rgba(37, 99, 235, 0.07), transparent 45%), var(--card-bg);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.015);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .lote-cochos-card:hover { box-shadow: 0 14px 28px -10px rgba(15, 23, 42, 0.12); }

        .lote-cochos-card-id .icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            font-size: 1rem;
            background: linear-gradient(145deg, #60a5fa, #2563eb);
            box-shadow: 0 6px 16px -4px rgba(37, 99, 235, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
        }

        .cocho-mini-icon { border-radius: 50%; }

        .lote-cochos-nivel {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
        }

        .lote-cochos-nivel .pg-meter { width: 80px; height: 6px; }

        .lote-cochos-nivel-pct {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-secundario);
            font-variant-numeric: tabular-nums;
        }

        body.dark-theme .lote-cochos-card { border: 1px solid rgba(255, 255, 255, 0.06); }

        @media (max-width: 640px) {
            .lote-cochos-nivel { min-width: 0; }
        }

        /* Monitoramento IoT: status do sensor na linha do cocho, link para
           os detalhes e campos de sensores no modal Editar. */
        .sensor-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.71875rem;
            font-weight: 700;
            width: fit-content;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .sensor-badge i { font-size: 0.625rem; }
        .sensor-badge.sensor-online { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
        .sensor-badge.sensor-offline { background: #fef3c7; color: #b45309; border-color: #fde68a; }
        .sensor-badge.sensor-erro { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
        body.dark-theme .sensor-badge { background: rgba(148, 163, 184, 0.12); color: #cbd5e1; border-color: rgba(148, 163, 184, 0.25); }
        body.dark-theme .sensor-badge.sensor-online { background: rgba(74, 222, 128, 0.14); color: #4ade80; border-color: rgba(74, 222, 128, 0.3); }
        body.dark-theme .sensor-badge.sensor-offline { background: rgba(245, 158, 11, 0.14); color: #fbbf24; border-color: rgba(245, 158, 11, 0.3); }
        body.dark-theme .sensor-badge.sensor-erro { background: rgba(239, 68, 68, 0.14); color: #fca5a5; border-color: rgba(239, 68, 68, 0.3); }
        body.high-contrast .sensor-badge { background: #000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }

        a.btn-detalhes-cocho { text-decoration: none; justify-content: center; }

        .sensores-iot {
            border: 1px dashed var(--surface-muted-border);
            border-radius: 12px;
            padding: 12px 14px 4px;
            margin: 6px 0 16px;
        }

        .sensores-iot legend {
            padding: 0 6px;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-principal);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sensores-iot legend i { color: var(--verde-texto, #0e4a27); }
        .sensores-iot > .dica-campo { display: block; margin: 2px 0 10px; }
    </style>
    @include('partials.page-identity')
@endpush

@section('content')
    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <main class="main" id="mainArea" data-pg="cochos">
        <div class="pg-top">
            <div class="pg-top-text">
                <h1 class="pg-title"><i class="fa-solid fa-cube titulo-icone" aria-hidden="true"></i> Gestão de Cochos</h1>
                <p class="pg-sub">Cochos são criados automaticamente pelos lotes — acompanhe o nível de ração e abasteça antes que esvaziem.</p>
            </div>

            <div class="top-bar-actions pg-top-actions">
                <button class="btn-abrir-filtros" id="openFiltrosBtn">
                    <i class="fa-solid fa-sliders"></i> Filtros Avançados
                    <span class="filtros-badge {{ count($filtrosAplicados ?? []) ? 'visivel' : '' }}">{{ count($filtrosAplicados ?? []) }}</span>
                </button>
                <a href="{{ route('lote.listar') }}" class="btn-add">
                    <i class="fa-solid fa-layer-group" aria-hidden="true"></i> Definir cochos pelo Lote
                </a>
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
                    <strong>Não foi possível concluir a ação:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $totalOk = 0;
            $totalBaixo = 0;
            $totalVazio = 0;
            $capacidadeTotal = 0;
            $nivelTotal = 0;
            foreach ($gruposPorLote as $grupo) {
                foreach ($grupo->cochos as $cocho) {
                    if ($cocho->status_abastecimento === 'OK') $totalOk++;
                    elseif ($cocho->status_abastecimento === 'Vazio') $totalVazio++;
                    else $totalBaixo++;
                    $capacidadeTotal += (float) $cocho->capacidade;
                    $nivelTotal += min((float) $cocho->nivel_atual, (float) $cocho->capacidade);
                }
            }
            $totalAlerta = $totalBaixo + $totalVazio;
            $totalContados = $totalOk + $totalAlerta;
            $pctOk = $totalContados > 0 ? round($totalOk / $totalContados * 100) : 0;
            $pctNivelGeral = $capacidadeTotal > 0 ? round($nivelTotal / $capacidadeTotal * 100) : 0;
            $corNivel = fn ($pct) => $pct < 25 ? '#dc2626' : ($pct < 50 ? '#f59e0b' : '#16a34a');
        @endphp

        <!-- CARDS DE RESUMO (mesmo visual dos cards do Dashboard) -->
        <section class="pg-cards pg-cards--destaque" aria-label="Resumo do abastecimento dos cochos">
            <div class="pg-card pg-verde">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Cochos OK</span>
                        <span class="pg-card-value">{{ $totalOk }} <small>de {{ $totalContados }}</small></span>
                        <span class="pg-card-sub">
                            @if ($totalAlerta > 0)
                                {{ $totalAlerta }} precisando de atenção
                            @else
                                Todos abastecidos
                            @endif
                        </span>
                    </div>
                    <div class="pg-card-visual">
                        <div class="pg-ring" style="--v: {{ $pctOk }}; --ring-cor: var(--verde-principal, #0e4a27);" role="img" aria-label="{{ $pctOk }}% dos cochos com abastecimento OK">
                            <div class="pg-ring-text"><strong>{{ $pctOk }}%</strong><span>OK</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-azul">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-cube" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Total de Cochos</span>
                        <span class="pg-card-value">{{ $totalCochos }}</span>
                        <span class="pg-card-sub">Em {{ count($gruposPorLote) }} {{ count($gruposPorLote) == 1 ? 'lote' : 'lotes' }}</span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-laranja">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Com Alerta</span>
                        <span class="pg-card-value">{{ $totalAlerta }}</span>
                        <span class="pg-card-sub">{{ $totalAlerta > 0 ? 'Precisam de abastecimento' : 'Nenhum cocho em alerta' }}</span>
                    </div>
                </div>
                <div class="pg-card-foot">
                    <div class="pg-segbar" role="img" aria-label="{{ $totalOk }} OK, {{ $totalBaixo }} baixos, {{ $totalVazio }} vazios">
                        @if ($totalOk) <span style="--v: {{ $totalOk }}; --seg-cor: var(--verde-principal, #0e4a27);"></span> @endif
                        @if ($totalBaixo) <span style="--v: {{ $totalBaixo }}; --seg-cor: #f59e0b;"></span> @endif
                        @if ($totalVazio) <span style="--v: {{ $totalVazio }}; --seg-cor: #dc2626;"></span> @endif
                    </div>
                    <div class="pg-legend">
                        <span class="pg-legend-item" style="--seg-cor: #f59e0b;">Baixo <strong>{{ $totalBaixo }}</strong></span>
                        <span class="pg-legend-item" style="--seg-cor: #dc2626;">Vazio <strong>{{ $totalVazio }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="pg-card pg-roxo">
                <div class="pg-card-main">
                    <div class="pg-card-icon"><i class="fa-solid fa-bowl-food" aria-hidden="true"></i></div>
                    <div class="pg-card-data">
                        <span class="pg-card-label">Ração nos Cochos</span>
                        <span class="pg-card-value">{{ number_format($nivelTotal, 0, ',', '.') }} kg</span>
                        <span class="pg-card-sub">de {{ number_format($capacidadeTotal, 0, ',', '.') }} kg · nível de <strong>{{ $pctNivelGeral }}%</strong></span>
                    </div>
                </div>
                <div class="pg-card-foot">
                    <span class="pg-meter" style="--meter-cor: {{ $corNivel($pctNivelGeral) }};" role="img" aria-label="Nível geral dos cochos: {{ $pctNivelGeral }}%"><span style="--v: {{ $pctNivelGeral }}%;"></span></span>
                </div>
            </div>
        </section>

        <div class="cochos-lista" id="cardsCochos">
            @forelse ($gruposPorLote as $indice => $grupo)
                @php
                    $lote = $grupo->lote;
                    $espacoLivreLote = $grupo->cochos->sum(fn ($c) => max(0, $c->capacidade - $c->nivel_atual));
                    $capacidadeLote = (float) $grupo->cochos->sum('capacidade');
                    $nivelLote = (float) $grupo->cochos->sum(fn ($c) => min((float) $c->nivel_atual, (float) $c->capacidade));
                    $pctLote = $capacidadeLote > 0 ? round($nivelLote / $capacidadeLote * 100) : null;
                @endphp
                <div class="lote-cochos-card fechado">
                    <button type="button" class="lote-cochos-card-header" onclick="alternarCardLote(this)" aria-expanded="false">
                        <div class="lote-cochos-card-id">
                            <div class="icon"><i class="fa-solid fa-layer-group"></i></div>
                            <div>
                                <div class="lote-cochos-card-nome">{{ $lote->nome_lote }}</div>
                                <div class="lote-cochos-card-fazenda"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $lote->fazenda->nome_fazenda ?? 'Sem fazenda' }}</div>
                            </div>
                        </div>

                        <div class="cocho-row-col">
                            <span class="cocho-row-col-label">Tipo de animal</span>
                            <span class="cocho-row-col-valor">{{ $lote->tipo_animal ?? '—' }}</span>
                        </div>

                        <div class="cocho-row-col">
                            <span class="cocho-row-col-label">Quantidade</span>
                            <span class="cocho-row-col-valor">{{ $lote->quantidade_animais ?? 0 }} {{ ($lote->quantidade_animais ?? 0) == 1 ? 'animal' : 'animais' }}</span>
                        </div>

                        <span class="lote-cochos-card-contador">
                            <i class="fa-solid fa-cube" aria-hidden="true"></i>
                            {{ $grupo->cochos->count() }} {{ $grupo->cochos->count() == 1 ? 'cocho' : 'cochos' }}
                        </span>

                        <span class="lote-cochos-nivel" title="Nível de ração somando os cochos deste lote">
                            @if ($pctLote !== null)
                                <span class="pg-meter" style="--meter-cor: {{ $corNivel($pctLote) }};"><span style="--v: {{ $pctLote }}%;"></span></span>
                                <span class="lote-cochos-nivel-pct">{{ $pctLote }}%</span>
                            @else
                                <span class="lote-cochos-nivel-pct">—</span>
                            @endif
                        </span>

                        <i class="fa-solid fa-chevron-down lote-cochos-card-chevron" aria-hidden="true"></i>
                    </button>

                    <div class="lote-cochos-card-body" hidden>
                        @if ($grupo->cochos->isNotEmpty())
                            <div class="lote-abastecer-bar">
                                <button type="button" class="btn-abastecer-lote"
                                    onclick="abrirModalAbastecerLote('{{ $lote->id }}', {{ Illuminate\Support\Js::from($lote->nome_lote) }}, '{{ $lote->id }}', '{{ $espacoLivreLote }}')">
                                    <i class="fa-solid fa-truck-ramp-box" aria-hidden="true"></i> Abastecer todos os cochos deste lote
                                </button>
                            </div>
                        @endif
                        @forelse ($grupo->cochos as $cocho)
                            @php
                                $espacoLivre = max(0, $cocho->capacidade - $cocho->nivel_atual);
                                $sensorDist = $cocho->sensores->firstWhere('tipo', \App\Models\Sensor::TIPO_DISTANCIA);
                                $sensorBal = $cocho->sensores->firstWhere('tipo', \App\Models\Sensor::TIPO_BALANCA);
                                $statusSensor = $cocho->statusSensor();
                                $rotuloStatusSensor = ['online' => 'Online', 'offline' => 'Sem comunicação', 'erro' => 'Erro na leitura', 'sem_leitura' => 'Sem leitura'][$statusSensor];
                                $sensoresAtivos = $cocho->sensores->where('ativo', true);
                                $codigosSensores = $sensoresAtivos->pluck('codigo')->implode(' · ');
                                // Sensores "SIM-" vêm do simulador (iot:simular), não de um ESP32.
                                $sensorSimulado = $sensoresAtivos->contains(fn ($s) => str_starts_with(strtoupper($s->codigo), 'SIM-'));
                                $ultimaLeituraSensor = $sensoresAtivos->max('ultima_leitura_em');
                                $dadosEdicao = [
                                    'id' => $cocho->id,
                                    'nome' => $cocho->nome_cocho,
                                    'localizacao' => $cocho->localizacao,
                                    'capacidade' => $cocho->capacidade,
                                    'nivel_atual' => $cocho->nivel_atual,
                                    'sensor_distancia' => $sensorDist?->ativo ? $sensorDist->codigo : '',
                                    'sensor_balanca' => $sensorBal?->ativo ? $sensorBal->codigo : '',
                                    'distancia_vazio_cm' => $sensorDist?->distancia_vazio_cm,
                                    'distancia_cheio_cm' => $sensorDist?->distancia_cheio_cm,
                                ];
                            @endphp
                            <div class="cocho-row">
                                <div class="cocho-row-main">
                                    <div class="cocho-row-id">
                                        <div class="cocho-mini-icon"><i class="fa-solid fa-cube" aria-hidden="true"></i></div>
                                        <div class="cocho-row-nome">{{ $cocho->nome_cocho }}@if ($cocho->localizacao)<small class="dica-campo" style="display:block; margin:2px 0 0; font-weight:500;"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $cocho->localizacao }}</small>@endif</div>
                                    </div>

                                    <div class="cocho-row-info">
                                        <div class="cocho-row-col">
                                            <span class="cocho-row-col-label">Capacidade</span>
                                            <span class="cocho-row-col-valor">{{ number_format($cocho->capacidade, 2, ',', '.') }} kg</span>
                                        </div>

                                        <div class="cocho-row-col">
                                            <span class="cocho-row-col-label">Nível atual</span>
                                            <span class="cocho-row-col-valor">{{ number_format($cocho->nivel_atual, 2, ',', '.') }} kg</span>
                                        </div>

                                        <div class="cocho-row-col">
                                            <span class="cocho-row-col-label">Sensor IoT</span>
                                            <span class="sensor-badge sensor-{{ $statusSensor }}" title="{{ ($codigosSensores ?: 'Nenhum sensor cadastrado') . ($ultimaLeituraSensor ? ' · última leitura ' . $ultimaLeituraSensor->format('d/m/Y H:i') : '') }}">
                                                <i class="fa-solid fa-wifi" aria-hidden="true"></i> {{ $rotuloStatusSensor }}@if ($sensorSimulado) · simulado @endif
                                            </span>
                                        </div>

                                        <div class="cocho-row-col">
                                            <span class="cocho-row-col-label">Status</span>
                                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($cocho->status_abastecimento) }}">
                                                <i class="fa-solid fa-circle" style="font-size:0.375rem;"></i> {{ $cocho->status_abastecimento }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="cocho-row-actions">
                                        <a href="{{ route('cocho.detalhes', $cocho->id) }}" class="btn-ver-detalhes btn-detalhes-cocho">
                                            <i class="fa-solid fa-chart-area" aria-hidden="true"></i>
                                            <span>Detalhes</span>
                                        </a>
                                        <button type="button" class="btn-ver-detalhes" data-cocho='@json($dadosEdicao)' onclick="abrirModalEditar(JSON.parse(this.dataset.cocho))">
                                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                            <span>Editar</span>
                                        </button>
                                        <button type="button" class="btn-abastecer-cocho"
                                            onclick="abrirModalAbastecer('{{ $cocho->id }}', {{ Illuminate\Support\Js::from($cocho->nome_cocho) }}, '{{ $lote->id }}', '{{ $espacoLivre }}')"
                                            @if ($espacoLivre <= 0) disabled title="{{ $cocho->capacidade > 0 ? 'Cocho cheio' : 'Defina a capacidade deste cocho primeiro (botão Editar)' }}" @endif>
                                            <i class="fa-solid fa-truck-ramp-box" aria-hidden="true"></i>
                                            <span>Abastecer</span>
                                        </button>
                                        <button type="button" class="btn-ver-detalhes"
                                            onclick="abrirModalConsumo('{{ $cocho->id }}', {{ Illuminate\Support\Js::from($cocho->nome_cocho) }}, '{{ $cocho->nivel_atual }}')"
                                            @if ($cocho->nivel_atual <= 0) disabled title="Cocho vazio" @endif>
                                            <i class="fa-solid fa-cow" aria-hidden="true"></i>
                                            <span>Consumo</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="lote-cochos-card-vazio">
                                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                                Nenhum cocho neste lote ainda — defina a "Quantidade de Cochos" ao editar o lote.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="cards-empty">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                    <p>
                        @if (collect($filtros)->filter()->isNotEmpty())
                            Nenhum lote encontrado com esses filtros.<br>Tente ajustar ou limpar os filtros.
                        @else
                            Você ainda não cadastrou nenhum lote.<br><a href="{{ route('lote.listar') }}">Cadastre um lote</a> pra começar a ter cochos.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- MODAL: ATUALIZAR COCHO -->
    <div class="modal" id="modalEditar">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalEditar()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <h2 id="edit_tituloCocho">Editar Cocho</h2>
                <p>Identificação e lote são geridos automaticamente — ajuste só os dados deste cocho.</p>
            </div>

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')

                <label for="edit_localizacao">Localização <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></label>
                <div class="input-box">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                    <input type="text" maxlength="150" id="edit_localizacao" name="localizacao" placeholder="Ex.: Piquete 3, lado norte">
                </div>

                <div class="row">
                    <div class="campo">
                        <label for="edit_capacidade">Capacidade (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bowl-food"></i>
                            <input type="number" step="0.01" min="0.01" id="edit_capacidade" name="capacidade" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="edit_nivel_atual">Nível Atual (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" min="0" id="edit_nivel_atual" name="nivel_atual" required aria-describedby="edit_nivel_dica">
                        </div>
                        <small class="dica-campo" id="edit_nivel_dica">Só pode diminuir (vira consumo registrado). Para colocar ração, use "Abastecer".</small>
                    </div>
                </div>

                <fieldset class="sensores-iot">
                    <legend><i class="fa-solid fa-microchip" aria-hidden="true"></i> Sensores IoT <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></legend>
                    <small class="dica-campo">Use o mesmo código que o controlador (ESP32) envia em "sensor_id". Deixar em branco desativa o sensor — o histórico é mantido.</small>

                    <div class="row">
                        <div class="campo">
                            <label for="edit_sensor_balanca">Balança / célula de carga</label>
                            <div class="input-box">
                                <i class="fa-solid fa-weight-scale"></i>
                                <input type="text" id="edit_sensor_balanca" name="sensor_balanca" maxlength="50" placeholder="Ex.: BAL-001">
                            </div>
                        </div>

                        <div class="campo">
                            <label for="edit_sensor_distancia">Sensor de distância</label>
                            <div class="input-box">
                                <i class="fa-solid fa-ruler-vertical"></i>
                                <input type="text" id="edit_sensor_distancia" name="sensor_distancia" maxlength="50" placeholder="Ex.: DIST-001">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="campo">
                            <label for="edit_distancia_vazio_cm">Distância com o cocho vazio (cm)</label>
                            <div class="input-box">
                                <i class="fa-solid fa-arrows-up-down"></i>
                                <input type="number" step="0.1" min="0.1" id="edit_distancia_vazio_cm" name="distancia_vazio_cm" placeholder="Ex.: 50">
                            </div>
                        </div>

                        <div class="campo">
                            <label for="edit_distancia_cheio_cm">Distância com o cocho cheio (cm)</label>
                            <div class="input-box">
                                <i class="fa-solid fa-arrows-up-down"></i>
                                <input type="number" step="0.1" min="0" id="edit_distancia_cheio_cm" name="distancia_cheio_cm" placeholder="Ex.: 5">
                            </div>
                        </div>
                    </div>
                    <small class="dica-campo">A calibração transforma a distância medida em nível (%). Sem balança, o nível também estima a quantidade em kg.</small>
                </fieldset>

                <button type="submit" class="submit-btn">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR CONSUMO (diminui o cocho; o armazém não muda) -->
    <div class="modal" id="modalConsumo">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalConsumo()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <h2 id="consumo_titulo">Registrar Consumo</h2>
                <p>Quanto os animais comeram deste cocho. O nível do cocho diminui e o registro fica no histórico — o estoque do armazém não muda (essa ração já saiu dele no abastecimento).</p>
            </div>

            <form id="formConsumo" method="POST">
                @csrf

                <div class="abastecer-info">
                    <div><i class="fa-solid fa-cube" aria-hidden="true"></i> <span id="consumo_nivel"></span></div>
                </div>

                <label for="consumo_quantidade">Quantidade consumida (kg)</label>
                <div class="input-box">
                    <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
                    <input type="number" step="0.01" min="0.01" id="consumo_quantidade" name="quantidade" required>
                </div>

                <label for="consumo_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky" aria-hidden="true"></i>
                    <input type="text" maxlength="255" id="consumo_observacao" name="observacao">
                </div>

                <div class="filtros-modal-footer">
                    <button type="submit" class="btn-aplicar">
                        <i class="fa-solid fa-check" aria-hidden="true"></i> Registrar consumo
                    </button>
                    <button type="button" class="btn-limpar" onclick="fecharModalConsumo()">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ABASTECER COCHO (desconta do Estoque de Rações) -->
    <div class="modal" id="modalAbastecer">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalAbastecer()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <h2 id="abastecer_titulo">Abastecer Cocho</h2>
                <p>Escolha a ração e a quantidade — o valor é descontado automaticamente do Estoque de Rações.</p>
            </div>

            <form id="formAbastecer" method="POST">
                @csrf

                <div class="abastecer-info">
                    <div><i class="fa-solid fa-cube" aria-hidden="true"></i> <span id="abastecer_espacoLivre"></span></div>
                </div>

                <label for="abastecer_racao_id">Ração</label>
                <div class="input-box">
                    <i class="fa-solid fa-wheat-awn"></i>
                    <select id="abastecer_racao_id" name="racao_id" required>
                        <option value="" disabled selected>Escolha uma ração...</option>
                        @foreach ($racoes as $racao)
                            <option value="{{ $racao->id }}" data-fazenda="{{ $racao->fazenda_id }}" data-estoque="{{ $racao->estoque }}">
                                {{ $racao->nome_racao }} ({{ number_format($racao->estoque, 2, ',', '.') }} kg em estoque)
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="dica-campo" id="abastecer_semRacao" hidden>Nenhuma ração compatível encontrada para este lote (tipo de animal, sexo e finalidade) — cadastre uma em "Gestão de Rações".</small>

                <label for="abastecer_quantidade">Quantidade a Abastecer (kg)</label>
                <div class="input-box">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <input type="number" step="0.01" min="0.01" id="abastecer_quantidade" name="quantidade" required>
                </div>

                <div class="abastecer-preview" id="abastecer_preview" hidden>
                    <div class="abastecer-preview-item">
                        <span class="abastecer-preview-label">Estoque atual</span>
                        <span class="abastecer-preview-valor" id="abastecer_previewAtual">—</span>
                    </div>
                    <div class="abastecer-preview-item">
                        <span class="abastecer-preview-label">Será usado</span>
                        <span class="abastecer-preview-valor" id="abastecer_previewUsado">—</span>
                    </div>
                    <div class="abastecer-preview-item">
                        <span class="abastecer-preview-label">Estoque restante</span>
                        <span class="abastecer-preview-valor" id="abastecer_previewRestante">—</span>
                    </div>
                </div>

                <div class="abastecer-aviso" id="abastecer_aviso">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    <div>
                        <span id="abastecer_avisoTexto"></span>
                        <div class="abastecer-aviso-acoes">
                            <button type="button" class="btn-aviso-acao" id="abastecer_btnReduzir">Reduzir quantidade</button>
                        </div>
                    </div>
                </div>

                <div class="filtros-modal-footer">
                    <button type="submit" class="btn-aplicar" id="abastecer_submit">
                        <i class="fa-solid fa-truck-ramp-box"></i> Abastecer agora
                    </button>
                    <button type="button" class="btn-limpar" onclick="fecharModalAbastecer()">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ABASTECER TODOS OS COCHOS DE UM LOTE (distribui e desconta do Estoque de Rações) -->
    <div class="modal" id="modalAbastecerLote">
        <div class="modal-card">
            <div class="modal-top">
                <button type="button" class="close" onclick="fecharModalAbastecerLote()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <h2 id="abastecerLote_titulo">Abastecer Cochos do Lote</h2>
                <p>A quantidade é distribuída entre os cochos do lote que ainda têm espaço, e descontada do Estoque de Rações.</p>
            </div>

            <form id="formAbastecerLote" method="POST">
                @csrf

                <div class="abastecer-info">
                    <div><i class="fa-solid fa-cube" aria-hidden="true"></i> <span id="abastecerLote_espacoLivre"></span></div>
                </div>

                <label for="abastecerLote_racao_id">Ração</label>
                <div class="input-box">
                    <i class="fa-solid fa-wheat-awn"></i>
                    <select id="abastecerLote_racao_id" name="racao_id" required>
                        <option value="" disabled selected>Escolha uma ração...</option>
                        @foreach ($racoes as $racao)
                            <option value="{{ $racao->id }}" data-fazenda="{{ $racao->fazenda_id }}" data-estoque="{{ $racao->estoque }}">
                                {{ $racao->nome_racao }} ({{ number_format($racao->estoque, 2, ',', '.') }} kg em estoque)
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="dica-campo" id="abastecerLote_semRacao" hidden>Nenhuma ração compatível encontrada para este lote (tipo de animal, sexo e finalidade) — cadastre uma em "Gestão de Rações".</small>

                <label for="abastecerLote_quantidade">Quantidade Total a Distribuir (kg)</label>
                <div class="input-box">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <input type="number" step="0.01" min="0.01" id="abastecerLote_quantidade" name="quantidade_total" required>
                </div>

                <div class="abastecer-preview" id="abastecerLote_preview" hidden>
                    <div class="abastecer-preview-item">
                        <span class="abastecer-preview-label">Estoque atual</span>
                        <span class="abastecer-preview-valor" id="abastecerLote_previewAtual">—</span>
                    </div>
                    <div class="abastecer-preview-item">
                        <span class="abastecer-preview-label">Será usado</span>
                        <span class="abastecer-preview-valor" id="abastecerLote_previewUsado">—</span>
                    </div>
                    <div class="abastecer-preview-item">
                        <span class="abastecer-preview-label">Estoque restante</span>
                        <span class="abastecer-preview-valor" id="abastecerLote_previewRestante">—</span>
                    </div>
                </div>

                <div class="abastecer-aviso" id="abastecerLote_aviso">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    <div>
                        <span id="abastecerLote_avisoTexto"></span>
                        <div class="abastecer-aviso-acoes">
                            <button type="button" class="btn-aviso-acao" id="abastecerLote_btnReduzir">Reduzir quantidade</button>
                        </div>
                    </div>
                </div>

                <div class="filtros-modal-footer">
                    <button type="submit" class="btn-aplicar" id="abastecerLote_submit">
                        <i class="fa-solid fa-truck-ramp-box"></i> Distribuir entre os cochos
                    </button>
                    <button type="button" class="btn-limpar" onclick="fecharModalAbastecerLote()">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Editar um cocho só mexe em capacidade/nível/sensor — nome, numero
        // e lote são geridos por LoteController::sincronizarCochos() a
        // partir da "Quantidade de Cochos" do lote, não têm campo aqui.
        // Dados vêm de data-cocho (JSON gerado no servidor), incluindo os
        // sensores IoT cadastrados e a calibração do sensor de distância.
        function abrirModalEditar(cocho) {
            document.getElementById('modalEditar').classList.add('active');

            document.getElementById('edit_tituloCocho').textContent = 'Editar ' + cocho.nome;
            document.getElementById('edit_capacidade').value = cocho.capacidade;
            document.getElementById('edit_localizacao').value = cocho.localizacao || '';
            document.getElementById('edit_nivel_atual').value = cocho.nivel_atual;
            document.getElementById('edit_nivel_atual').max = cocho.nivel_atual;
            document.getElementById('edit_sensor_balanca').value = cocho.sensor_balanca || '';
            document.getElementById('edit_sensor_distancia').value = cocho.sensor_distancia || '';
            document.getElementById('edit_distancia_vazio_cm').value = cocho.distancia_vazio_cm ?? '';
            document.getElementById('edit_distancia_cheio_cm').value = cocho.distancia_cheio_cm ?? '';

            document.getElementById('formEditar').action = '/cocho/update/' + cocho.id;
        }

        function fecharModalEditar() {
            document.getElementById('modalEditar').classList.remove('active');
        }

        // ==========================================
        // ABASTECER (desconta automaticamente do Estoque de Rações) — um
        // cocho só ("abastecer") ou distribuído entre todos os cochos de um
        // lote ("abastecerLote"). Os dois modais usam o mesmo padrão de ids
        // (ex.: "abastecer_racao_id"/"abastecerLote_racao_id"), então a
        // lógica de prévia/aviso abaixo é compartilhada pelos dois.
        // ==========================================
        function formatarKg(valor) {
            const numero = Number.isFinite(valor) ? valor : 0;
            return numero.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' kg';
        }

        // Só mostra, no <select> de Ração, as opções COMPATÍVEIS com o lote
        // do cocho (mesma fazenda, tipo de animal, sexo e finalidade). A lista
        // vem pronta do servidor (App\Support\CompatibilidadeRacao), que
        // valida de novo ao abastecer.
        const RACOES_COMPATIVEIS_POR_LOTE = @json($racoesCompativeisPorLote);

        function filtrarRacaoPorLote(prefixo, loteId) {
            const select = document.getElementById(prefixo + '_racao_id');
            const semRacao = document.getElementById(prefixo + '_semRacao');
            const compativeis = (RACOES_COMPATIVEIS_POR_LOTE[loteId] || []).map(String);
            let algumaVisivel = false;

            Array.from(select.options).forEach((opcao) => {
                if (!opcao.value) return; // "Escolha uma ração..."
                const pertence = compativeis.includes(opcao.value);
                opcao.hidden = !pertence;
                if (pertence) algumaVisivel = true;
            });

            select.value = '';
            select.disabled = !algumaVisivel;
            if (semRacao) semRacao.hidden = algumaVisivel;
        }

        // Recalcula, ao vivo, o painel "Estoque atual / Será usado / Estoque
        // restante" e o aviso de "não cabe no cocho" / "estoque insuficiente"
        // — mesmas duas regras validadas de verdade no back-end
        // (CochoController::abastecer/abastecerLote), só que aqui é feedback
        // instantâneo pro usuário, antes mesmo de tentar enviar o formulário.
        function atualizarPreviewAbastecimento(prefixo, espacoLivre, espacoLabel) {
            const select = document.getElementById(prefixo + '_racao_id');
            const inputQuantidade = document.getElementById(prefixo + '_quantidade');
            const preview = document.getElementById(prefixo + '_preview');
            const aviso = document.getElementById(prefixo + '_aviso');
            const avisoTexto = document.getElementById(prefixo + '_avisoTexto');
            const submit = document.getElementById(prefixo + '_submit');

            const opcao = select.selectedOptions[0];
            const temRacao = !!(opcao && opcao.value);
            const estoque = temRacao ? parseFloat(opcao.dataset.estoque) : 0;
            const quantidade = parseFloat(inputQuantidade.value);
            const temQuantidade = !isNaN(quantidade) && quantidade > 0;

            let bloqueado = false;
            let mensagem = '';

            if (temRacao && temQuantidade) {
                preview.hidden = false;
                document.getElementById(prefixo + '_previewAtual').textContent = formatarKg(estoque);
                document.getElementById(prefixo + '_previewUsado').textContent = formatarKg(quantidade);

                const restante = estoque - quantidade;
                const previewRestante = document.getElementById(prefixo + '_previewRestante');
                previewRestante.textContent = formatarKg(restante);
                previewRestante.classList.toggle('negativo', restante < 0);

                if (quantidade > espacoLivre + 0.005) {
                    bloqueado = true;
                    mensagem = 'Não cabem ' + formatarKg(quantidade) + ' ' + espacoLabel + ' — o espaço livre é de ' + formatarKg(espacoLivre) + '.';
                } else if (quantidade > estoque + 0.005) {
                    bloqueado = true;
                    const nomeRacao = opcao.textContent.split(' (')[0].trim();
                    mensagem = 'Estoque insuficiente: há apenas ' + formatarKg(estoque) + ' de "' + nomeRacao + '" disponíveis. Reduza a quantidade ou escolha outra ração.';
                }
            } else {
                preview.hidden = true;
            }

            aviso.classList.toggle('visivel', bloqueado);
            avisoTexto.textContent = mensagem;
            submit.disabled = bloqueado || !temRacao || !temQuantidade;
        }

        function abrirModalAbastecer(id, nome, loteId, espacoLivre) {
            espacoLivre = Math.round((parseFloat(espacoLivre) || 0) * 100) / 100;

            document.getElementById('modalAbastecer').classList.add('active');
            document.getElementById('abastecer_titulo').textContent = 'Abastecer ' + nome;
            document.getElementById('abastecer_espacoLivre').textContent = 'Espaço livre neste cocho: ' + formatarKg(espacoLivre);
            document.getElementById('formAbastecer').action = '/cocho/' + id + '/abastecer';

            const inputQuantidade = document.getElementById('abastecer_quantidade');
            inputQuantidade.value = '';
            inputQuantidade.max = espacoLivre;

            filtrarRacaoPorLote('abastecer', loteId);
            atualizarPreviewAbastecimento('abastecer', espacoLivre, 'neste cocho');
        }

        function fecharModalAbastecer() {
            document.getElementById('modalAbastecer').classList.remove('active');
        }

        function abrirModalConsumo(id, nome, nivelAtual) {
            nivelAtual = Math.round((parseFloat(nivelAtual) || 0) * 100) / 100;

            document.getElementById('modalConsumo').classList.add('active');
            document.getElementById('consumo_titulo').textContent = 'Registrar Consumo — ' + nome;
            document.getElementById('consumo_nivel').textContent = 'Ração neste cocho agora: ' + formatarKg(nivelAtual);
            document.getElementById('formConsumo').action = '/cocho/' + id + '/consumo';

            const inputQuantidade = document.getElementById('consumo_quantidade');
            inputQuantidade.value = '';
            inputQuantidade.max = nivelAtual;
            document.getElementById('consumo_observacao').value = '';
        }

        function fecharModalConsumo() {
            document.getElementById('modalConsumo').classList.remove('active');
        }

        function abrirModalAbastecerLote(id, nome, loteId, espacoLivreLote) {
            espacoLivreLote = Math.round((parseFloat(espacoLivreLote) || 0) * 100) / 100;

            document.getElementById('modalAbastecerLote').classList.add('active');
            document.getElementById('abastecerLote_titulo').textContent = 'Abastecer Cochos — ' + nome;
            document.getElementById('abastecerLote_espacoLivre').textContent = 'Espaço livre somado nos cochos deste lote: ' + formatarKg(espacoLivreLote);
            document.getElementById('formAbastecerLote').action = '/lote/' + id + '/cochos/abastecer';

            const inputQuantidade = document.getElementById('abastecerLote_quantidade');
            inputQuantidade.value = '';
            inputQuantidade.max = espacoLivreLote;

            filtrarRacaoPorLote('abastecerLote', loteId);
            atualizarPreviewAbastecimento('abastecerLote', espacoLivreLote, 'nos cochos deste lote');
        }

        function fecharModalAbastecerLote() {
            document.getElementById('modalAbastecerLote').classList.remove('active');
        }

        ['abastecer', 'abastecerLote'].forEach((prefixo) => {
            const select = document.getElementById(prefixo + '_racao_id');
            const inputQuantidade = document.getElementById(prefixo + '_quantidade');
            if (!select || !inputQuantidade) return;

            const espacoLabel = prefixo === 'abastecer' ? 'neste cocho' : 'nos cochos deste lote';
            const recalcular = () => atualizarPreviewAbastecimento(prefixo, parseFloat(inputQuantidade.max) || 0, espacoLabel);

            select.addEventListener('change', recalcular);
            inputQuantidade.addEventListener('input', recalcular);

            const btnReduzir = document.getElementById(prefixo + '_btnReduzir');
            if (btnReduzir) {
                btnReduzir.addEventListener('click', () => {
                    const opcao = select.selectedOptions[0];
                    const estoque = (opcao && opcao.value) ? parseFloat(opcao.dataset.estoque) : 0;
                    const espacoLivre = parseFloat(inputQuantidade.max) || 0;
                    const maximo = Math.max(0, Math.min(estoque || 0, espacoLivre));
                    inputQuantidade.value = maximo.toFixed(2);
                    recalcular();
                });
            }
        });

        // Cartão do lote: expande/recolhe a lista de cochos dele.
        function alternarCardLote(botao) {
            const card = botao.closest('.lote-cochos-card');
            const corpo = card.querySelector('.lote-cochos-card-body');
            const fechando = !card.classList.contains('fechado');

            card.classList.toggle('fechado', fechando);
            corpo.hidden = fechando;
            botao.setAttribute('aria-expanded', fechando ? 'false' : 'true');
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
            const modalAbastecer = document.getElementById('modalAbastecer');
            const modalAbastecerLote = document.getElementById('modalAbastecerLote');

            if (event.target === modal) fecharModal();
            if (event.target === modalEditar) fecharModalEditar();
            if (event.target === modalAbastecer) fecharModalAbastecer();
            if (event.target === modalAbastecerLote) fecharModalAbastecerLote();
        });

        window.addEventListener('keydown', function(event) {
            if (event.key !== 'Escape') return;
            if (document.getElementById('modalAbastecer')?.classList.contains('active')) fecharModalAbastecer();
            if (document.getElementById('modalAbastecerLote')?.classList.contains('active')) fecharModalAbastecerLote();
        });
    </script>
@endpush
