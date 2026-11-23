@extends('layouts.app')

@section('title', 'AgriFarm | Gestão de Lotes')

@push('styles')
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

        .sidebar, .navbar, .main, .lote-row {
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




        html.ui-movimento-reduzido { /* antes: @media (prefers-reduced-motion) — agora só a opção do painel desliga */
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
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-title :is(h1, h2) {
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
            background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
            box-shadow: 0 6px 13px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
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

        .status-badge.status-ativo { background: var(--verde-claro, #e8f5ee); color: var(--verde-texto, #0e4a27); border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28)); }
        .status-badge.status-inativo { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-badge.status-encerrado { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        body.dark-theme .status-badge.status-ativo { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 25%, transparent); color: var(--verde-texto, #0e4a27); }
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

        .btn-ver-detalhes:hover { border-color: var(--verde-hover, #0b3d21); color: var(--verde-texto, #0e4a27); }
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
            grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 1fr));
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

        /* Só os rótulos "Raça:", "Peso médio:"... — antes valia pra QUALQUER
           span dos detalhes e enchia a ordenha/histórico de ":" e caixa alta. */
        .lote-row-detalhe-texto > span::after { content: ':'; }

        .lote-row-detalhe-texto > span {
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
            background: var(--verde-principal, #0e4a27);
            box-shadow: 0 4px 12px color-mix(in srgb, var(--verde-principal, #0e4a27) 20%, transparent);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-confirmar-recuperacao:hover {
            background: var(--verde-hover, #0b3d21);
            box-shadow: 0 10px 25px color-mix(in srgb, var(--verde-principal, #0e4a27) 35%, transparent);
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

        /* ==========================================================================
           MODAIS DE OCORRÊNCIA (parto, eclosão, postura, óbito, doença,
           devolver ao lote, ordenha) — cabeçalho com ícone e cor por tipo,
           lote em destaque, quantidade + data lado a lado, dica em caixa e
           rodapé "Cancelar" + "Confirmar". Cada cor tem contraste ≥ 4,5:1
           com texto branco (botão) e é a mesma usada no ícone.
           ========================================================================== */
        .ocorr-card {
            --ocorr-cor: var(--verde-principal, #0e4a27);
            width: 540px;
            padding: 0 0 24px;
            overflow-x: hidden;
        }
        .ocorr--parto       { --ocorr-cor: #be185d; }
        .ocorr--eclosao     { --ocorr-cor: #b45309; }
        .ocorr--postura     { --ocorr-cor: #a16207; }
        .ocorr--morte       { --ocorr-cor: #475569; }
        .ocorr--doenca      { --ocorr-cor: #c2410c; }
        .ocorr--recuperacao { --ocorr-cor: #15803d; }
        .ocorr--ordenha     { --ocorr-cor: #1d4ed8; }

        .ocorr-card > form { padding: 0 28px; }

        .ocorr-topo {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 24px 64px 20px 28px;
            margin-bottom: 18px;
            background: linear-gradient(135deg, color-mix(in srgb, var(--ocorr-cor) 12%, transparent), transparent 75%);
            border-bottom: 1px solid color-mix(in srgb, var(--ocorr-cor) 18%, var(--surface-muted-border, #e2e8f0));
            border-top: 4px solid var(--ocorr-cor);
            border-radius: 18px 18px 0 0;
        }
        .ocorr-topo .close { top: 22px; right: 20px; }

        .ocorr-icone {
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #ffffff;
            background: var(--ocorr-cor);
            box-shadow: 0 8px 18px -6px color-mix(in srgb, var(--ocorr-cor) 70%, transparent);
        }
        .ocorr-titulos { min-width: 0; }
        .ocorr-titulo {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-principal);
            line-height: 1.25;
            letter-spacing: -0.01em;
        }
        .ocorr-sub {
            margin-top: 3px;
            font-size: 0.8125rem;
            color: var(--text-secundario);
            line-height: 1.4;
        }

        /* Lote do registro: chip logo abaixo do cabeçalho. */
        .acao-lote-nome.ocorr-lote {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            margin: 0 28px 18px;
            padding: 10px 14px;
            border-radius: 12px;
            background: var(--surface-muted-bg, #f8fafc);
            border: 1px solid var(--surface-muted-border, #e2e8f0);
            text-align: left;
            font-size: 0.875rem;
        }
        .acao-lote-nome.ocorr-lote::before {
            content: "\f5fd"; /* fa-layer-group */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--ocorr-cor);
        }
        .acao-lote-nome.ocorr-lote:empty { display: none; }
        .ocorr-card .recuperacao-destino-fixo { margin: -6px 28px 18px; }

        .ocorr-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 14px;
        }

        .ocorr-info {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: -4px 0 18px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.8125rem;
            line-height: 1.45;
            color: var(--text-principal);
            background: color-mix(in srgb, var(--ocorr-cor) 9%, transparent);
            border-left: 3px solid var(--ocorr-cor);
        }
        .ocorr-info i { color: var(--ocorr-cor); margin-top: 2px; }
        .ocorr-info:empty { display: none; }

        .ocorr-acoes {
            display: flex;
            gap: 12px;
            margin-top: 6px;
        }
        .ocorr-cancelar {
            flex: 0 0 auto;
            min-width: 120px;
            padding: 14px 18px;
            border-radius: 10px;
            border: 1px solid var(--surface-muted-border, #e2e8f0);
            background: var(--card-bg);
            color: var(--text-principal);
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
        }
        .ocorr-cancelar:hover { background: var(--surface-muted-bg, #f8fafc); }
        .ocorr-card .submit-btn.ocorr-confirmar {
            flex: 1;
            width: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--ocorr-cor);
            box-shadow: 0 6px 16px -6px color-mix(in srgb, var(--ocorr-cor) 65%, transparent);
        }
        .ocorr-card .submit-btn.ocorr-confirmar:hover {
            background: color-mix(in srgb, var(--ocorr-cor) 85%, #000000);
            box-shadow: 0 10px 22px -6px color-mix(in srgb, var(--ocorr-cor) 70%, transparent);
        }

        body.dark-theme .ocorr-topo { background: linear-gradient(135deg, color-mix(in srgb, var(--ocorr-cor) 22%, transparent), transparent 75%); }
        body.dark-theme .ocorr-info { background: color-mix(in srgb, var(--ocorr-cor) 16%, transparent); }
        body.dark-theme .ocorr-info i,
        body.dark-theme .acao-lote-nome.ocorr-lote::before { color: color-mix(in srgb, var(--ocorr-cor) 55%, #ffffff); }

        body.high-contrast .ocorr-topo { background: #000000 !important; border-color: #ffff00 !important; }
        body.high-contrast .ocorr-icone { background: #000000 !important; color: #ffff00 !important; border: 2px solid #ffff00; box-shadow: none; }
        body.high-contrast .ocorr-titulo,
        body.high-contrast .ocorr-sub,
        body.high-contrast .ocorr-info,
        body.high-contrast .acao-lote-nome.ocorr-lote { color: #ffffff !important; background: #000000 !important; border-color: #ffff00 !important; }
        body.high-contrast .ocorr-cancelar { background: #000000 !important; color: #ffffff !important; border: 2px solid #ffffff !important; }

        @media (max-width: 576px) {
            /* Vence o padding genérico de .modal-card no celular: o
               cabeçalho colorido encosta nas bordas do card. */
            .modal-card.ocorr-card { padding: 0 0 20px; }
            .ocorr-card > form { padding: 0 18px; }
            .ocorr-topo { padding: 20px 56px 16px 18px; gap: 12px; }
            .ocorr-icone { width: 44px; height: 44px; font-size: 1.15rem; border-radius: 13px; }
            .ocorr-titulo { font-size: 1.125rem; }
            .acao-lote-nome.ocorr-lote { margin: 0 18px 16px; }
            .ocorr-card .recuperacao-destino-fixo { margin: -6px 18px 16px; }
            .ocorr-grid { grid-template-columns: 1fr; }
            .ocorr-acoes { flex-direction: column-reverse; }
            .ocorr-cancelar { width: 100%; }
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
        .lote-historico-item .ponto-recuperacao { color: var(--verde-texto, #0e4a27); }
        .lote-historico-item .ponto-parto { color: #2563eb; }
        .lote-historico-item .ponto-postura { color: #0891b2; }
        .lote-historico-item strong { color: var(--text-principal); font-weight: 700; }
        .lote-historico-item .variacao { color: #dc2626; font-weight: 700; }
        .lote-historico-item .variacao-doenca { color: #d97706; font-weight: 700; }
        .lote-historico-item .variacao-recuperacao { color: var(--verde-texto, #0e4a27); font-weight: 700; }
        .lote-historico-item .variacao-parto { color: #2563eb; font-weight: 700; }
        .lote-historico-item .variacao-postura { color: #0891b2; font-weight: 700; }
        .lote-historico-item .destino { color: var(--text-principal); font-style: normal; }
        .lote-historico-item .obs { font-style: italic; }

        /* "Ordenha e Leite do Lote" (dentro de "Ver detalhes") — registro
           rápido de produção de leite do lote inteiro, sem sair da tela de
           Lotes. Produção é sempre por LOTE, nunca por animal individual. */
        .ordenhas-lote {
            grid-column: 1 / -1;
            border-top: 1px dashed var(--surface-muted-border);
            margin-top: 4px;
            padding-top: 12px;
        }

        .ordenhas-lote-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .ordenhas-lote-total {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secundario);
        }
        .ordenhas-lote-total strong { color: var(--text-principal); }

        /* Lista compacta: uma linha fina por ordenha (data · hora · turno ·
           litros · ações), num bloco só com divisórias — antes cada registro
           era uma "caixa" separada com ~47px de altura. */
        .ordenhas-lote-lista {
            border: 1px solid var(--surface-muted-border);
            border-radius: 10px;
            overflow: hidden;
        }

        .ordenha-lote-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 5px 6px 5px 12px;
            background: var(--card-bg);
        }
        .ordenha-lote-item + .ordenha-lote-item { border-top: 1px solid var(--surface-muted-border); }
        .ordenha-lote-item:nth-child(even) { background: var(--surface-muted-bg); }

        .ordenha-lote-info {
            display: grid;
            grid-template-columns: 82px 42px 84px minmax(64px, auto) minmax(0, 1fr);
            align-items: center;
            column-gap: 10px;
            font-size: 0.8125rem;
            color: var(--text-secundario);
            min-width: 0;
        }
        .ordenha-lote-info strong { color: var(--text-principal); font-weight: 700; }
        .ordenha-lote-hora { font-variant-numeric: tabular-nums; }
        .ordenha-lote-litros { color: var(--text-principal); font-weight: 700; font-variant-numeric: tabular-nums; text-align: right; }
        .ordenha-lote-obs { font-style: italic; overflow-wrap: anywhere; }

        /* Turno: ícone + texto (nunca só cor). */
        .ordenha-turno {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            width: fit-content;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.71875rem;
            font-weight: 700;
        }
        .ordenha-turno-manha { background: #fef3c7; color: #92400e; }
        .ordenha-turno-tarde { background: #ffedd5; color: #9a3412; }
        .ordenha-turno-noite { background: #e0e7ff; color: #3730a3; }
        body.dark-theme .ordenha-turno-manha { background: rgba(251, 191, 36, 0.16); color: #fcd34d; }
        body.dark-theme .ordenha-turno-tarde { background: rgba(249, 115, 22, 0.16); color: #fdba74; }
        body.dark-theme .ordenha-turno-noite { background: rgba(129, 140, 248, 0.18); color: #c7d2fe; }

        .ordenha-lote-acoes { display: flex; align-items: center; gap: 4px; }

        .btn-acao-ordenha {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            border-radius: 8px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            cursor: pointer;
        }
        .btn-acao-ordenha:hover { border-color: var(--verde-hover, #0b3d21); color: var(--verde-texto, #0e4a27); }
        .btn-acao-ordenha-excluir:hover { border-color: #dc2626; color: #dc2626; }

        /* Celular: data/hora/turno numa linha, litros e observação embaixo. */
        @media (max-width: 576px) {
            .ordenha-lote-info {
                grid-template-columns: auto auto auto;
                justify-content: start;
                row-gap: 3px;
            }
            .ordenha-lote-litros { text-align: left; }
            .ordenha-lote-obs { grid-column: 1 / -1; }
            .main .btn-acao-ordenha { min-height: 36px; width: 36px; }
        }

        .ordenhas-lote-vazio { font-size: 0.8125rem; color: var(--text-secundario); font-style: italic; }

        .ordenha-turno-dica {
            margin: -6px 0 14px;
            font-size: 0.75rem;
            line-height: 1.4;
            color: var(--text-secundario);
        }
        .ordenha-turno-dica strong { color: var(--text-principal); }

        .btn-ver-mais-ordenhas-lote {
            display: block;
            margin: 8px auto 0;
            padding: 6px 16px;
            border-radius: 8px;
            border: 1px solid var(--surface-muted-border);
            background: var(--card-bg);
            color: var(--text-principal);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-ver-mais-ordenhas-lote:hover { border-color: #0891b2; color: #0891b2; }

        body.high-contrast .ordenhas-lote { border-top: 1px dashed #ffff00 !important; }
        body.high-contrast .ordenha-lote-item { background: #000000 !important; }
        body.high-contrast .ordenha-lote-item + .ordenha-lote-item { border-top-color: #ffff00 !important; }
        body.high-contrast .btn-acao-ordenha { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
        body.high-contrast .ordenhas-lote-lista { border-color: #ffff00 !important; }
        body.high-contrast .ordenha-turno { background: #000000 !important; border: 1px solid #ffff00; color: #ffff00 !important; }

        /* ==========================================================================
        8. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard/Fazendas
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
            color: var(--verde-texto, #0e4a27) !important;
        }
        body.high-contrast .lote-historico-item .ponto-parto,
        body.high-contrast .lote-historico-item .variacao-parto {
            color: #7dd3fc !important;
        }
        body.high-contrast .lote-historico-item .ponto-postura,
        body.high-contrast .lote-historico-item .variacao-postura {
            color: #67e8f9 !important;
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
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 20px; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .main { padding: 18px; padding-top: 20px; }
            .header-title :is(h1, h2) { font-size: 1.5rem; }

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
@endpush

@section('content')
    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <main class="main" id="mainArea">
        <div class="header">
            <div class="header-title">
                <h1><i class="fa-solid fa-layer-group titulo-icone" aria-hidden="true"></i> Gestão de Lotes</h1>
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
                    <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
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
            @php
                $tituloErro = match (old('origem_formulario')) {
                    'edit' => 'Não foi possível salvar as alterações do lote:',
                    'morte' => 'Não foi possível registrar o óbito:',
                    'doenca' => 'Não foi possível registrar a doença:',
                    'recuperacao' => 'Não foi possível devolver os animais ao lote:',
                    'parto' => 'Não foi possível registrar o parto:',
                    'eclosao' => 'Não foi possível registrar a eclosão:',
                    'postura' => 'Não foi possível registrar a postura:',
                    'ordenha_lote' => 'Não foi possível registrar a ordenha:',
                    default => 'Não foi possível salvar o lote:',
                };
            @endphp
            <div class="alerta-flash alerta-erro" role="alert" aria-live="assertive">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                <div>
                    <strong>{{ $tituloErro }}</strong>
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

                    // Ordenha, Parto, Eclosão e Postura são sempre por LOTE,
                    // nunca por animal individual. Nem toda espécie produz
                    // leite (só Bovino, Ovino e Caprino têm ordenha) e
                    // Avicultura é a única espécie sem parto — ela põe ovos
                    // (Postura) que chocam (Eclosão) no lugar de parir.
                    // Dentro de cada espécie, só lotes com fêmeas (sexo
                    // Fêmea ou Misto) podem registrar qualquer um desses.
                    $loteTemOrdenha = in_array($lote->tipo_animal, $tiposComOrdenha, true);
                    $loteTemParto = in_array($lote->tipo_animal, $tiposComParto, true);
                    $loteTemEclosao = in_array($lote->tipo_animal, $tiposComEclosao, true);
                    $loteTemPostura = in_array($lote->tipo_animal, $tiposComPostura, true);
                    $loteTemFemea = in_array($lote->sexo, $sexosComFemea, true);
                    // Lote de machos: NADA de ordenha na tela (nem botão
                    // desabilitado, nem a seção "Ordenha e Leite") — só lote
                    // com fêmeas de espécie leiteira mostra o fluxo.
                    $mostraOrdenha = $loteTemOrdenha && $loteTemFemea;
                    $elegivelOrdenha = $mostraOrdenha && !$motivoBloqueio;
                    $motivoBloqueioOrdenha = $motivoBloqueio;
                    $elegivelParto = $loteTemParto && $loteTemFemea && !$motivoBloqueio && !$ehIsolamento;
                    $motivoBloqueioParto = $ehIsolamento
                        ? 'Lotes de isolamento não registram parto — devolva os animais ao lote de origem primeiro.'
                        : ($motivoBloqueio ?: (!$loteTemFemea ? 'Parto só é possível em lotes com fêmeas (sexo Fêmea ou Misto).' : (!$loteTemParto ? "Lotes de {$lote->tipo_animal} não têm parto registrado." : '')));
                    $elegivelEclosao = $loteTemEclosao && $loteTemFemea && !$motivoBloqueio && !$ehIsolamento;
                    $motivoBloqueioEclosao = $ehIsolamento
                        ? 'Lotes de isolamento não registram eclosão — devolva os animais ao lote de origem primeiro.'
                        : ($motivoBloqueio ?: (!$loteTemFemea ? 'Eclosão só é possível em lotes com fêmeas (sexo Fêmea ou Misto).' : ''));
                    $elegivelPostura = $loteTemPostura && $loteTemFemea && !$motivoBloqueio && !$ehIsolamento;
                    $motivoBloqueioPostura = $ehIsolamento
                        ? 'Lotes de isolamento não registram postura — devolva os animais ao lote de origem primeiro.'
                        : ($motivoBloqueio ?: (!$loteTemFemea ? 'Postura só é possível em lotes com fêmeas (sexo Fêmea ou Misto).' : ''));
                    $ordenhasDoLote = $lote->ordenhas;
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
                                        @js($lote->nome_lote),
                                        '{{ $lote->fazenda_id }}',
                                        '{{ $lote->tipo_animal }}',
                                        '{{ $lote->finalidade }}',
                                        '{{ $lote->sexo }}',
                                        '{{ $lote->idade_media }}',
                                        '{{ $lote->quantidade_animais }}',
                                        '{{ $lote->peso_medio }}',
                                        '{{ $statusAtual }}',
                                        @js($lote->raca),
                                        '{{ $lote->quantidade_cochos ?? 0 }}',
                                        @js(optional($lote->data_entrada)->format('Y-m-d'))
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
                                    @unless ($ehIsolamento)
                                        @if ($loteTemEclosao)
                                            <button type="button" onclick="abrirModalEclosao(this)"
                                                data-id="{{ $lote->id }}"
                                                data-nome="{{ $lote->nome_lote }}"
                                                data-quantidade="{{ $lote->quantidade_animais }}"
                                                @if (!$elegivelEclosao) disabled title="{{ $motivoBloqueioEclosao }}" @endif>
                                                <i class="fa-solid fa-egg"></i> Registrar Eclosão
                                            </button>
                                        @else
                                            <button type="button" onclick="abrirModalParto(this)"
                                                data-id="{{ $lote->id }}"
                                                data-nome="{{ $lote->nome_lote }}"
                                                data-quantidade="{{ $lote->quantidade_animais }}"
                                                @if (!$elegivelParto) disabled title="{{ $motivoBloqueioParto }}" @endif>
                                                <i class="fa-solid fa-baby"></i> Registrar Parto
                                            </button>
                                        @endif
                                        @if ($loteTemPostura)
                                            <button type="button" onclick="abrirModalPostura(this)"
                                                data-id="{{ $lote->id }}"
                                                data-nome="{{ $lote->nome_lote }}"
                                                data-quantidade="{{ $lote->quantidade_animais }}"
                                                @if (!$elegivelPostura) disabled title="{{ $motivoBloqueioPostura }}" @endif>
                                                <i class="fa-solid fa-egg"></i> Registrar Postura
                                            </button>
                                        @endif
                                        @if ($mostraOrdenha)
                                            <button type="button" onclick="abrirModalOrdenhaLote({{ $lote->id }}, @js($lote->nome_lote))"
                                                data-ordenha-lote-add
                                                data-id="{{ $lote->id }}"
                                                data-nome="{{ $lote->nome_lote }}"
                                                @if (!$elegivelOrdenha) disabled title="{{ $motivoBloqueioOrdenha }}" @endif>
                                                <i class="fa-solid fa-droplet"></i> Registrar Ordenha
                                            </button>
                                        @endif
                                    @endunless
                                    <button type="button" onclick="abrirModalMorte(this)"
                                        data-id="{{ $lote->id }}"
                                        data-nome="{{ $lote->nome_lote }}"
                                        data-quantidade="{{ $lote->quantidade_animais }}"
                                        data-isolamento="{{ $ehIsolamento ? '1' : '0' }}"
                                        data-estoque='@json($estoquePorDoenca)'
                                        {{ $motivoBloqueio ? 'disabled title="' . $motivoBloqueio . '"' : '' }}>
                                        <i class="fa-solid fa-heart-crack"></i> Registrar Óbito
                                    </button>
                                    <form action="{{ route('lote.delete', $lote->id) }}" method="POST" data-confirmar="Deseja realmente excluir este lote? Esta ação não pode ser desfeita." data-confirmar-titulo="Excluir lote">
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
                            <div class="lote-row-detalhe-icone"><i class="fa-solid fa-dna"></i></div>
                            <div class="lote-row-detalhe-texto">
                                <span>Raça</span>
                                <strong>{{ $lote->raca ?? '—' }}</strong>
                            </div>
                        </div>
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

                        @if ($mostraOrdenha)
                            <div class="ordenhas-lote">
                                <div class="ordenhas-lote-header">
                                    <span class="lote-historico-titulo" style="margin-bottom:0;">Ordenha e Leite do Lote ({{ $ordenhasDoLote->count() }})</span>
                                    @if ($ordenhasDoLote->isNotEmpty())
                                        <span class="ordenhas-lote-total">Total: <strong>{{ number_format($ordenhasDoLote->sum('quantidade_litros'), 1, ',', '.') }} L</strong></span>
                                    @endif
                                </div>

                                @if ($ordenhasDoLote->isEmpty())
                                    <p class="ordenhas-lote-vazio">Nenhuma ordenha registrada neste lote ainda.</p>
                                @else
                                    <div id="ordenhasLote_{{ $lote->id }}" class="ordenhas-lote-lista">
                                        @include('partials.ordenhas-lote-linhas', ['ordenhas' => $ordenhasDoLote->take(10), 'lote' => $lote])
                                    </div>
                                @endif
                                @if ($ordenhasDoLote->count() > 10)
                                    <button type="button" class="btn-ver-mais-ordenhas-lote"
                                        data-lote-id="{{ $lote->id }}"
                                        onclick="verMaisOrdenhasLote(this)">
                                        Ver mais ordenhas
                                    </button>
                                @endif
                            </div>
                        @endif

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
                                        @elseif ($ocorrencia->tipo === 'parto')
                                            <span class="ponto-parto"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i></span>
                                            <strong>Parto registrado</strong>
                                            <span>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                                            <span class="variacao-parto">{{ $ocorrencia->quantidade_antes }} → {{ $ocorrencia->quantidade_depois }} animais</span>
                                        @elseif ($ocorrencia->tipo === 'eclosao')
                                            <span class="ponto-parto"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i></span>
                                            <strong>Eclosão registrada</strong>
                                            <span>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                                            <span class="variacao-parto">{{ $ocorrencia->quantidade_antes }} → {{ $ocorrencia->quantidade_depois }} animais</span>
                                        @elseif ($ocorrencia->tipo === 'postura')
                                            <span class="ponto-postura"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i></span>
                                            <strong>Postura registrada</strong>
                                            <span>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                                            <span class="variacao-postura">{{ $ocorrencia->quantidade }} {{ \Illuminate\Support\Str::plural('ovo', $ocorrencia->quantidade) }}</span>
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
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
                <h2>Adicionar Lote</h2>
                <p>Cadastre um novo lote e defina espécie, sexo e quantidade de animais.</p>
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

                <label for="tipo_animal">Selecione um tipo de animal</label>
                <div class="input-box">
                    <i class="fa-solid fa-cow"></i>
                    <select id="tipo_animal" name="tipo_animal" required>
                        <option value="" disabled @selected(!old('tipo_animal'))>Escolha um tipo de animal...</option>
                        @foreach ($tiposAnimais as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo_animal') === $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="raca">Raça <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></label>
                <div class="input-box">
                    <i class="fa-solid fa-dna"></i>
                    <input type="text" id="raca" name="raca" value="{{ old('raca') }}" placeholder="Ex.: Nelore, Holandês, Duroc..." maxlength="100">
                </div>

                <div class="row">
                    <div class="campo">
                        <label for="sexo">Sexo</label>
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
                        <label for="finalidade">Finalidade</label>
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
                        <label for="peso_medio">Peso Médio (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" id="peso_medio" name="peso_medio" value="{{ old('peso_medio') }}" placeholder="380.50" required>
                        </div>
                        <small class="dica-campo" id="dica_peso">Selecione o tipo de animal para ver a faixa de peso esperada.</small>
                    </div>
                </div>

                <label for="data_entrada">Data de Entrada na Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
                    <input type="date" id="data_entrada" name="data_entrada" value="{{ old('data_entrada', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
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

                <label for="quantidade_cochos">Quantidade de Cochos</label>
                <div class="input-box">
                    <i class="fa-solid fa-cube"></i>
                    <input type="number" id="quantidade_cochos" name="quantidade_cochos" min="0" max="100" value="{{ old('quantidade_cochos', 0) }}" placeholder="0" required>
                </div>
                <small class="dica-campo">Cria automaticamente esse número de cochos pro lote.</small>

                <div class="row">
                    <div class="campo">
                        <label for="cocho_capacidade">Capacidade dos Cochos (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bowl-food"></i>
                            <input type="number" step="0.01" min="0.01" id="cocho_capacidade" name="cocho_capacidade" value="{{ old('cocho_capacidade') }}" placeholder="Ex.: 100" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="cocho_nivel_atual">Nível Atual (kg) <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" min="0" id="cocho_nivel_atual" name="cocho_nivel_atual" value="{{ old('cocho_nivel_atual') }}" placeholder="Ex.: 100">
                        </div>
                    </div>
                </div>
                <small class="dica-campo">Já aplica essa capacidade/nível a todos os cochos criados acima.</small>

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
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo">
                <p>Atualizar Lote</p>
            </div>

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="origem_formulario" value="edit">
                <input type="hidden" id="lote_id_edit" name="lote_id_edit" value="{{ old('lote_id_edit') }}">

                <label for="edit_nome_lote">Nome do Lote</label>
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

                <label for="edit_raca">Raça <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></label>
                <div class="input-box">
                    <i class="fa-solid fa-dna"></i>
                    <input type="text" id="edit_raca" name="raca" placeholder="Ex.: Nelore, Holandês, Duroc..." maxlength="100">
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
                        <label for="edit_quantidade_animais">Quantidade</label>
                        <div class="input-box">
                            <i class="fa-solid fa-paw"></i>
                            <input type="number" id="edit_quantidade_animais" name="quantidade_animais" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="edit_peso_medio">Peso Médio (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" id="edit_peso_medio" name="peso_medio" required>
                        </div>
                        <small class="dica-campo" id="edit_dica_peso">Selecione o tipo de animal para ver a faixa de peso esperada.</small>
                    </div>
                </div>

                <label for="edit_data_entrada">Data de Entrada na Fazenda</label>
                <div class="input-box">
                    <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
                    <input type="date" id="edit_data_entrada" name="data_entrada" max="{{ now()->toDateString() }}">
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

                <label for="edit_quantidade_cochos">Quantidade de Cochos</label>
                <div class="input-box">
                    <i class="fa-solid fa-cube"></i>
                    <input type="number" id="edit_quantidade_cochos" name="quantidade_cochos" min="0" max="100" required>
                </div>
                <small class="dica-campo">Aumentar cria cochos novos; diminuir remove os últimos — os que sobrarem mantêm capacidade/nível/sensor já cadastrados.</small>

                <div class="row">
                    <div class="campo">
                        <label for="edit_cocho_capacidade">Capacidade dos Cochos Novos (kg)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-bowl-food"></i>
                            <input type="number" step="0.01" min="0.01" id="edit_cocho_capacidade" name="cocho_capacidade" placeholder="Ex.: 100" required>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="edit_cocho_nivel_atual">Nível Atual (kg) <span class="dica-campo" style="display:inline; margin:0;">(opcional)</span></label>
                        <div class="input-box">
                            <i class="fa-solid fa-weight-scale"></i>
                            <input type="number" step="0.01" min="0" id="edit_cocho_nivel_atual" name="cocho_nivel_atual" placeholder="Ex.: 100">
                        </div>
                    </div>
                </div>
                <small class="dica-campo">Já aplica essa capacidade/nível a todos os cochos criados acima.</small>

                <button type="submit" class="submit-btn">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR PARTO (por lote — aumenta a quantidade de animais) -->
    <div class="modal" id="modalParto">
        <div class="modal-card ocorr-card ocorr--parto" role="dialog" aria-modal="true" aria-labelledby="tituloParto">
            <div class="ocorr-topo">
                <span class="ocorr-icone" aria-hidden="true"><i class="fa-solid fa-baby"></i></span>
                <div class="ocorr-titulos">
                    <h2 class="ocorr-titulo" id="tituloParto">Registrar Parto</h2>
                    <p class="ocorr-sub">As novas crias entram na contagem do lote.</p>
                </div>
                <button type="button" class="close" onclick="fecharModalParto()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <p class="acao-lote-nome ocorr-lote" id="parto_lote_nome"></p>

            <form id="formParto" method="POST">
                @csrf
                <input type="hidden" name="origem_formulario" value="parto">
                <input type="hidden" id="parto_lote_id" name="lote_id" value="">

                <div class="ocorr-grid">
                    <div class="campo">
                        <label for="parto_quantidade">Quantidade de crias nascidas</label>
                        <div class="input-box">
                            <i class="fa-solid fa-baby"></i>
                            <input type="number" id="parto_quantidade" name="quantidade" min="1" step="1" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="parto_data">Data do Parto</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="parto_data" name="data_ocorrencia" max="{{ $hoje }}" required>
                        </div>
                    </div>
                </div>
                <p class="ocorr-info"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> A quantidade de animais do lote é aumentada automaticamente.</p>

                <label for="parto_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="parto_observacao" name="observacao" maxlength="255">
                </div>

                <div class="ocorr-acoes">
                    <button type="button" class="ocorr-cancelar" onclick="fecharModalParto()">Cancelar</button>
                    <button type="submit" class="submit-btn ocorr-confirmar">
                        <i class="fa-solid fa-check"></i> Confirmar Parto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR ECLOSÃO (só Avicultura — aumenta a quantidade de animais) -->
    <div class="modal" id="modalEclosao">
        <div class="modal-card ocorr-card ocorr--eclosao" role="dialog" aria-modal="true" aria-labelledby="tituloEclosao">
            <div class="ocorr-topo">
                <span class="ocorr-icone" aria-hidden="true"><i class="fa-solid fa-egg"></i></span>
                <div class="ocorr-titulos">
                    <h2 class="ocorr-titulo" id="tituloEclosao">Registrar Eclosão</h2>
                    <p class="ocorr-sub">Os pintinhos eclodidos entram na contagem do lote.</p>
                </div>
                <button type="button" class="close" onclick="fecharModalEclosao()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <p class="acao-lote-nome ocorr-lote" id="eclosao_lote_nome"></p>

            <form id="formEclosao" method="POST">
                @csrf
                <input type="hidden" name="origem_formulario" value="eclosao">
                <input type="hidden" id="eclosao_lote_id" name="lote_id" value="">

                <div class="ocorr-grid">
                    <div class="campo">
                        <label for="eclosao_quantidade">Quantidade de pintinhos eclodidos</label>
                        <div class="input-box">
                            <i class="fa-solid fa-egg"></i>
                            <input type="number" id="eclosao_quantidade" name="quantidade" min="1" step="1" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="eclosao_data">Data da Eclosão</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="eclosao_data" name="data_ocorrencia" max="{{ $hoje }}" required>
                        </div>
                    </div>
                </div>
                <p class="ocorr-info"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> A quantidade de animais do lote é aumentada automaticamente.</p>

                <label for="eclosao_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="eclosao_observacao" name="observacao" maxlength="255">
                </div>

                <div class="ocorr-acoes">
                    <button type="button" class="ocorr-cancelar" onclick="fecharModalEclosao()">Cancelar</button>
                    <button type="submit" class="submit-btn ocorr-confirmar">
                        <i class="fa-solid fa-check"></i> Confirmar Eclosão
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR POSTURA (só Avicultura — não altera a quantidade de animais) -->
    <div class="modal" id="modalPostura">
        <div class="modal-card ocorr-card ocorr--postura" role="dialog" aria-modal="true" aria-labelledby="tituloPostura">
            <div class="ocorr-topo">
                <span class="ocorr-icone" aria-hidden="true"><i class="fa-solid fa-egg"></i></span>
                <div class="ocorr-titulos">
                    <h2 class="ocorr-titulo" id="tituloPostura">Registrar Postura</h2>
                    <p class="ocorr-sub">Produção de ovos do dia — não altera a quantidade de aves.</p>
                </div>
                <button type="button" class="close" onclick="fecharModalPostura()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <p class="acao-lote-nome ocorr-lote" id="postura_lote_nome"></p>

            <form id="formPostura" method="POST">
                @csrf
                <input type="hidden" name="origem_formulario" value="postura">
                <input type="hidden" id="postura_lote_id" name="lote_id" value="">

                <div class="ocorr-grid">
                    <div class="campo">
                        <label for="postura_quantidade">Quantidade de ovos do dia</label>
                        <div class="input-box">
                            <i class="fa-solid fa-egg"></i>
                            <input type="number" id="postura_quantidade" name="quantidade" min="1" step="1" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="postura_data">Data da Postura</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="postura_data" name="data_ocorrencia" max="{{ $hoje }}" required>
                        </div>
                    </div>
                </div>

                <label for="postura_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="postura_observacao" name="observacao" maxlength="255">
                </div>

                <div class="ocorr-acoes">
                    <button type="button" class="ocorr-cancelar" onclick="fecharModalPostura()">Cancelar</button>
                    <button type="submit" class="submit-btn ocorr-confirmar">
                        <i class="fa-solid fa-check"></i> Confirmar Postura
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR ÓBITO -->
    <div class="modal" id="modalMorte">
        <div class="modal-card ocorr-card ocorr--morte" role="dialog" aria-modal="true" aria-labelledby="tituloMorte">
            <div class="ocorr-topo">
                <span class="ocorr-icone" aria-hidden="true"><i class="fa-solid fa-heart-crack"></i></span>
                <div class="ocorr-titulos">
                    <h2 class="ocorr-titulo" id="tituloMorte">Registrar Óbito</h2>
                    <p class="ocorr-sub">Os animais saem da contagem ativa do lote.</p>
                </div>
                <button type="button" class="close" onclick="fecharModalMorte()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <p class="acao-lote-nome ocorr-lote" id="morte_lote_nome"></p>

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
                    <p class="ocorr-info" id="morte_doenca_dica"></p>
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

                <div class="ocorr-acoes">
                    <button type="button" class="ocorr-cancelar" onclick="fecharModalMorte()">Cancelar</button>
                    <button type="submit" class="submit-btn ocorr-confirmar btn-confirmar-morte">
                        <i class="fa-solid fa-check"></i> Confirmar Óbito
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR DOENÇA -->
    <div class="modal" id="modalDoenca">
        <div class="modal-card ocorr-card ocorr--doenca" role="dialog" aria-modal="true" aria-labelledby="tituloDoenca">
            <div class="ocorr-topo">
                <span class="ocorr-icone" aria-hidden="true"><i class="fa-solid fa-virus"></i></span>
                <div class="ocorr-titulos">
                    <h2 class="ocorr-titulo" id="tituloDoenca">Registrar Doença</h2>
                    <p class="ocorr-sub">Os animais doentes vão para um lote de isolamento.</p>
                </div>
                <button type="button" class="close" onclick="fecharModalDoenca()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <p class="acao-lote-nome ocorr-lote" id="doenca_lote_nome"></p>

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

                <p class="ocorr-info" id="doenca_destino_dica"></p>

                <label for="doenca_observacao">Observação (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="doenca_observacao" name="observacao" placeholder="Ex.: já isolados desde ontem" maxlength="255">
                </div>

                <div class="ocorr-acoes">
                    <button type="button" class="ocorr-cancelar" onclick="fecharModalDoenca()">Cancelar</button>
                    <button type="submit" class="submit-btn ocorr-confirmar btn-confirmar-doenca">
                        <i class="fa-solid fa-check"></i> Confirmar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: DEVOLVER AO LOTE (recuperação — só pro lote de origem) -->
    <div class="modal" id="modalRecuperacao">
        <div class="modal-card ocorr-card ocorr--recuperacao" role="dialog" aria-modal="true" aria-labelledby="tituloRecuperacao">
            <div class="ocorr-topo">
                <span class="ocorr-icone" aria-hidden="true"><i class="fa-solid fa-house-medical"></i></span>
                <div class="ocorr-titulos">
                    <h2 class="ocorr-titulo" id="tituloRecuperacao">Devolver ao Lote</h2>
                    <p class="ocorr-sub">Animais recuperados voltam para o lote de origem.</p>
                </div>
                <button type="button" class="close" onclick="fecharModalRecuperacao()" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <p class="acao-lote-nome ocorr-lote" id="recuperacao_lote_nome"></p>
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

                <div class="ocorr-acoes">
                    <button type="button" class="ocorr-cancelar" onclick="fecharModalRecuperacao()">Cancelar</button>
                    <button type="submit" class="submit-btn ocorr-confirmar btn-confirmar-recuperacao">
                        <i class="fa-solid fa-check"></i> Confirmar Retorno
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR/EDITAR ORDENHA (por lote) -->
    <div class="modal" id="modalOrdenhaLote">
        <div class="modal-card ocorr-card ocorr--ordenha" role="dialog" aria-modal="true" aria-labelledby="ordenhaLote_titulo">
            <div class="ocorr-topo">
                <span class="ocorr-icone" aria-hidden="true"><i class="fa-solid fa-droplet"></i></span>
                <div class="ocorr-titulos">
                    <h2 class="ocorr-titulo" id="ordenhaLote_titulo">Registrar Ordenha</h2>
                    <p class="ocorr-sub">Registre a produção de leite do lote neste turno.</p>
                </div>
                <button type="button" class="close" onclick="fecharModalPorId('modalOrdenhaLote')" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <p class="acao-lote-nome ocorr-lote" id="ordenhaLote_nome"></p>

            <form id="formOrdenhaLote" method="POST">
                @csrf
                <input type="hidden" name="_method" id="ordenhaLote_metodo" value="POST">
                <input type="hidden" name="origem_formulario" value="ordenha_lote">
                <input type="hidden" id="ordenhaLote_lote_id" name="lote_id" value="">
                <input type="hidden" id="ordenhaLote_id" name="ordenha_id" value="">

                <div class="row">
                    <div class="campo">
                        <label for="ordenhaLote_data">Data</label>
                        <div class="input-box">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" id="ordenhaLote_data" name="data" max="{{ $hoje }}" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="ordenhaLote_horario">Horário</label>
                        <div class="input-box">
                            <i class="fa-solid fa-clock"></i>
                            <input type="time" id="ordenhaLote_horario" name="horario" required>
                        </div>
                    </div>
                </div>

                <label for="ordenhaLote_turno">Turno</label>
                <div class="input-box">
                    <i class="fa-solid fa-sun"></i>
                    <select id="ordenhaLote_turno" name="turno" required aria-describedby="ordenhaLote_turnoDica">
                        <option value="" disabled selected>Selecione...</option>
                        @foreach ($turnosOrdenha as $turno)
                            <option value="{{ $turno }}">{{ $turno }} (a partir das {{ \App\Support\LoteRegras::INICIO_TURNOS_ORDENHA[$turno] }})</option>
                        @endforeach
                    </select>
                </div>
                <p class="ordenha-turno-dica" id="ordenhaLote_turnoDica" aria-live="polite">
                    O turno acompanha o horário: Manhã a partir das 04:00, Tarde a partir das 12:00, Noite a partir das 18:00.
                </p>

                <label for="ordenhaLote_litros">Quantidade de Leite (litros)</label>
                <div class="input-box">
                    <i class="fa-solid fa-droplet"></i>
                    <input type="number" step="0.01" min="0.01" id="ordenhaLote_litros" name="quantidade_litros" placeholder="Ex.: 120.5" required>
                </div>

                <label for="ordenhaLote_observacoes">Observações (opcional)</label>
                <div class="input-box">
                    <i class="fa-solid fa-note-sticky"></i>
                    <input type="text" id="ordenhaLote_observacoes" name="observacoes" maxlength="255">
                </div>

                <div class="ocorr-acoes">
                    <button type="button" class="ocorr-cancelar" onclick="fecharModalPorId('modalOrdenhaLote')">Cancelar</button>
                    <button type="submit" class="submit-btn ocorr-confirmar" id="ordenhaLote_submit">
                        <i class="fa-solid fa-check"></i> Confirmar Ordenha
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
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

        function abrirModalEditar(id, nome, fazendaId, tipo, finalidade, sexo, idadeMedia, quantidade, peso, status, raca, quantidadeCochos, dataEntrada) {
            document.getElementById('modalEditar').classList.add('active');

            document.getElementById('edit_nome_lote').value = nome;
            document.getElementById('edit_fazenda_id').value = fazendaId;
            document.getElementById('edit_tipo_animal').value = tipo;
            document.getElementById('edit_raca').value = raca || '';
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
            document.getElementById('edit_data_entrada').value = dataEntrada || '';
            document.getElementById('edit_status').value = status || 'Ativo';
            document.getElementById('edit_quantidade_cochos').value = quantidadeCochos || 0;
            // Capacidade/Nível Atual não têm um valor "do lote" pra mostrar
            // (cada cocho já existente pode ter o seu próprio) — sempre
            // reabre em branco, valendo só se a Quantidade de Cochos acima
            // for aumentada nesta edição.
            document.getElementById('edit_cocho_capacidade').value = '';
            document.getElementById('edit_cocho_nivel_atual').value = '';
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
                AgriUI.toast('Informe ao menos 1 óbito de alguma doença.', { tipo: 'erro', titulo: 'Faltam dados' });
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

        function abrirModalParto(botao) {
            const id = botao.dataset.id;
            const nome = botao.dataset.nome;
            const quantidadeAtual = parseInt(botao.dataset.quantidade, 10) || 0;

            document.getElementById('modalParto').classList.add('active');
            document.getElementById('parto_lote_nome').textContent = nome + ' — ' + quantidadeAtual + (quantidadeAtual == 1 ? ' animal ativo' : ' animais ativos');
            document.getElementById('parto_quantidade').value = 1;
            document.getElementById('parto_data').value = '{{ $hoje }}';
            document.getElementById('parto_observacao').value = '';
            document.getElementById('parto_lote_id').value = id;

            document.getElementById('formParto').action = '/lote/' + id + '/parto';
        }

        function fecharModalParto() {
            document.getElementById('modalParto').classList.remove('active');
        }

        function abrirModalEclosao(botao) {
            const id = botao.dataset.id;
            const nome = botao.dataset.nome;
            const quantidadeAtual = parseInt(botao.dataset.quantidade, 10) || 0;

            document.getElementById('modalEclosao').classList.add('active');
            document.getElementById('eclosao_lote_nome').textContent = nome + ' — ' + quantidadeAtual + (quantidadeAtual == 1 ? ' animal ativo' : ' animais ativos');
            document.getElementById('eclosao_quantidade').value = 1;
            document.getElementById('eclosao_data').value = '{{ $hoje }}';
            document.getElementById('eclosao_observacao').value = '';
            document.getElementById('eclosao_lote_id').value = id;

            document.getElementById('formEclosao').action = '/lote/' + id + '/eclosao';
        }

        function fecharModalEclosao() {
            document.getElementById('modalEclosao').classList.remove('active');
        }

        function abrirModalPostura(botao) {
            const id = botao.dataset.id;
            const nome = botao.dataset.nome;
            const quantidadeAtual = parseInt(botao.dataset.quantidade, 10) || 0;

            document.getElementById('modalPostura').classList.add('active');
            document.getElementById('postura_lote_nome').textContent = nome + ' — ' + quantidadeAtual + (quantidadeAtual == 1 ? ' animal ativo' : ' animais ativos');
            document.getElementById('postura_quantidade').value = '';
            document.getElementById('postura_data').value = '{{ $hoje }}';
            document.getElementById('postura_observacao').value = '';
            document.getElementById('postura_lote_id').value = id;

            document.getElementById('formPostura').action = '/lote/' + id + '/postura';
        }

        function fecharModalPostura() {
            document.getElementById('modalPostura').classList.remove('active');
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
        // ORDENHA E LEITE DO LOTE — produção de leite é sempre por LOTE,
        // nunca por animal individual (ver seção "Ordenha e Leite do Lote"
        // dentro de "Ver detalhes"). O mesmo modal serve para registrar uma
        // ordenha nova e para editar uma já existente.
        // ------------------------------------------------------------
        function fecharModalPorId(id) {
            document.getElementById(id).classList.remove('active');
        }

        // ------------------------------------------------------------
        // TURNO x HORÁRIO — mesma regra do back-end e do banco
        // (App\Support\LoteRegras::INICIO_TURNOS_ORDENHA): Manhã a partir
        // das 04:00, Tarde a partir das 12:00, Noite a partir das 18:00 (até
        // 03:59). Mudar o horário ajusta o turno; escolher um turno que não
        // bate com o horário leva o horário para o início desse turno.
        // ------------------------------------------------------------
        const INICIO_TURNOS_ORDENHA = @json(\App\Support\LoteRegras::INICIO_TURNOS_ORDENHA);

        function turnoPorHorario(hhmm) {
            if (!hhmm) return '';
            if (hhmm >= INICIO_TURNOS_ORDENHA['Noite'] || hhmm < INICIO_TURNOS_ORDENHA['Manhã']) return 'Noite';
            return hhmm >= INICIO_TURNOS_ORDENHA['Tarde'] ? 'Tarde' : 'Manhã';
        }

        function horaAtual() {
            const agora = new Date();
            return String(agora.getHours()).padStart(2, '0') + ':' + String(agora.getMinutes()).padStart(2, '0');
        }

        function atualizarDicaTurno() {
            const turno = document.getElementById('ordenhaLote_turno').value;
            const horario = document.getElementById('ordenhaLote_horario').value;
            const dica = document.getElementById('ordenhaLote_turnoDica');
            const certo = turnoPorHorario(horario);
            const faixas = '(Manhã a partir das 04:00, Tarde a partir das 12:00, Noite a partir das 18:00.)';
            if (!horario || !turno) {
                dica.innerHTML = 'O turno acompanha o horário: ' + faixas.slice(1, -2) + '.';
            } else if (turno !== certo) {
                // Só acontece com dados devolvidos pelo servidor após erro.
                dica.innerHTML = '⚠ O horário ' + horario + ' é do turno da <strong>' + certo + '</strong>, não da ' + turno + '. ' + faixas;
            } else {
                dica.innerHTML = 'Horário ' + horario + ' → turno da <strong>' + turno + '</strong>. ' + faixas;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const campoHorario = document.getElementById('ordenhaLote_horario');
            const campoTurno = document.getElementById('ordenhaLote_turno');
            if (!campoHorario || !campoTurno) return;

            campoHorario.addEventListener('input', function () {
                const sugerido = turnoPorHorario(campoHorario.value);
                if (sugerido) campoTurno.value = sugerido;
                atualizarDicaTurno();
            });

            campoTurno.addEventListener('change', function () {
                if (turnoPorHorario(campoHorario.value) !== campoTurno.value) {
                    campoHorario.value = INICIO_TURNOS_ORDENHA[campoTurno.value] || campoHorario.value;
                }
                atualizarDicaTurno();
            });
        });

        function abrirModalOrdenhaLote(loteId, nomeLote, ordenhaId, data, horario, turno, litros, observacoes) {
            const editando = !!ordenhaId;

            document.getElementById('ordenhaLote_nome').textContent = nomeLote;
            document.getElementById('ordenhaLote_titulo').textContent = editando ? 'Editar Ordenha' : 'Registrar Ordenha';
            document.getElementById('ordenhaLote_submit').innerHTML =
                '<i class="fa-solid fa-check"></i> ' + (editando ? 'Salvar Alterações' : 'Confirmar Ordenha');

            // Nova ordenha: já vem com a hora de agora e o turno certo dela.
            const horarioInicial = editando ? horario : horaAtual();
            document.getElementById('ordenhaLote_data').value = editando ? data : '{{ $hoje }}';
            document.getElementById('ordenhaLote_horario').value = horarioInicial;
            document.getElementById('ordenhaLote_turno').value = editando ? turno : turnoPorHorario(horarioInicial);
            atualizarDicaTurno();
            document.getElementById('ordenhaLote_litros').value = editando ? litros : '';
            document.getElementById('ordenhaLote_observacoes').value = editando ? (observacoes || '') : '';
            document.getElementById('ordenhaLote_lote_id').value = loteId;
            document.getElementById('ordenhaLote_id').value = editando ? ordenhaId : '';

            const form = document.getElementById('formOrdenhaLote');
            document.getElementById('ordenhaLote_metodo').value = editando ? 'PUT' : 'POST';
            form.action = editando ? ('/lote/' + loteId + '/ordenha/' + ordenhaId) : ('/lote/' + loteId + '/ordenha');

            document.getElementById('modalOrdenhaLote').classList.add('active');
        }

        function verMaisOrdenhasLote(botao) {
            const loteId = botao.dataset.loteId;
            const textoOriginal = botao.textContent;
            botao.disabled = true;
            AgriUI.indicarCarregando(botao, 'Carregando…');

            fetch('/lote/' + loteId + '/ordenha/historico', {
                    headers: { 'Accept': 'application/json' },
                })
                .then(function(resposta) {
                    if (resposta.status === 401) {
                        window.location.href = '{{ route('login.index') }}';
                        return Promise.reject('nao-autenticado');
                    }
                    return resposta.json();
                })
                .then(function(dados) {
                    if (dados.success) {
                        document.getElementById('ordenhasLote_' + loteId).insertAdjacentHTML('beforeend', dados.html);
                        botao.remove();
                    } else {
                        botao.disabled = false;
                        botao.textContent = textoOriginal;
                    }
                })
                .catch(function() {
                    botao.disabled = false;
                    botao.textContent = textoOriginal;
                });
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
                AgriUI.toast('Informe ao menos 1 animal recuperado de alguma doença.', { tipo: 'erro', titulo: 'Faltam dados' });
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
            const modalOrdenhaLote = document.getElementById('modalOrdenhaLote');
            const modalParto = document.getElementById('modalParto');
            const modalEclosao = document.getElementById('modalEclosao');
            const modalPostura = document.getElementById('modalPostura');

            if (event.target === modal) fecharModal();
            if (event.target === modalEditar) fecharModalEditar();
            if (event.target === modalMorte) fecharModalMorte();
            if (event.target === modalDoenca) fecharModalDoenca();
            if (event.target === modalRecuperacao) fecharModalRecuperacao();
            if (event.target === modalOrdenhaLote) fecharModalPorId('modalOrdenhaLote');
            if (event.target === modalParto) fecharModalParto();
            if (event.target === modalEclosao) fecharModalEclosao();
            if (event.target === modalPostura) fecharModalPostura();
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
                document.getElementById('edit_raca').value = OLD_INPUT.raca || '';
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
                document.getElementById('edit_quantidade_cochos').value = OLD_INPUT.quantidade_cochos || 0;
                document.getElementById('edit_cocho_capacidade').value = OLD_INPUT.cocho_capacidade || '';
                document.getElementById('edit_cocho_nivel_atual').value = OLD_INPUT.cocho_nivel_atual || '';
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
            } else if (ORIGEM_FORMULARIO_ERRO === 'parto') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('button[onclick="abrirModalParto(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalParto(botao);

                document.getElementById('parto_quantidade').value = OLD_INPUT.quantidade || 1;
                document.getElementById('parto_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('parto_observacao').value = OLD_INPUT.observacao || '';
            } else if (ORIGEM_FORMULARIO_ERRO === 'eclosao') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('button[onclick="abrirModalEclosao(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalEclosao(botao);

                document.getElementById('eclosao_quantidade').value = OLD_INPUT.quantidade || 1;
                document.getElementById('eclosao_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('eclosao_observacao').value = OLD_INPUT.observacao || '';
            } else if (ORIGEM_FORMULARIO_ERRO === 'postura') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('button[onclick="abrirModalPostura(this)"][data-id="' + loteId + '"]');
                if (!botao) return;

                abrirModalPostura(botao);

                document.getElementById('postura_quantidade').value = OLD_INPUT.quantidade || '';
                document.getElementById('postura_data').value = OLD_INPUT.data_ocorrencia || '{{ $hoje }}';
                document.getElementById('postura_observacao').value = OLD_INPUT.observacao || '';
            } else if (ORIGEM_FORMULARIO_ERRO === 'ordenha_lote') {
                const loteId = OLD_INPUT.lote_id;
                const botao = loteId && document.querySelector('button[data-ordenha-lote-add][data-id="' + loteId + '"]');
                if (!botao) return;

                const ordenhaId = OLD_INPUT.ordenha_id || null;
                abrirModalOrdenhaLote(
                    loteId, botao.dataset.nome, ordenhaId,
                    OLD_INPUT.data, OLD_INPUT.horario, OLD_INPUT.turno, OLD_INPUT.quantidade_litros, OLD_INPUT.observacoes
                );

                document.getElementById('ordenhaLote_data').value = OLD_INPUT.data || '{{ $hoje }}';
                document.getElementById('ordenhaLote_horario').value = OLD_INPUT.horario || '';
                document.getElementById('ordenhaLote_turno').value = OLD_INPUT.turno || '';
                document.getElementById('ordenhaLote_litros').value = OLD_INPUT.quantidade_litros || '';
                document.getElementById('ordenhaLote_observacoes').value = OLD_INPUT.observacoes || '';
                atualizarDicaTurno();
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
@endpush
