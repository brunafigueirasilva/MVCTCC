{{-- ==========================================================================
     UI MOTION DO AGRIFARM — sistema único de movimento, profundidade e
     carregamento. Incluído por partials/ui-core (uma cópia por página).

     Princípios:
      - Curvas naturais: saída "expo-out" (rápido no começo, pousa suave),
        entrada acelerada, mola leve só em detalhes pequenos (ícones).
      - Durações curtas: 120–200 ms para microinterações, 300–600 ms para
        entrada de conteúdo. Nada fica em loop chamando atenção.
      - Só transform/opacity (aceleradas por GPU); nada que force layout.
      - Tudo depende de html.ui-motion, que só existe quando NEM o sistema
        operacional NEM o painel de acessibilidade pedem movimento reduzido.
        Sem a classe, a interface fica estática e 100% funcional.
      - Conteúdo nunca fica escondido esperando script: a entrada da página
        é só CSS (com "fill: backwards"), e o que entra pela rolagem só é
        ocultado quando já está fora da tela.
     ========================================================================== --}}
<style>
    :root {
        --ui-ease-out: cubic-bezier(0.16, 1, 0.3, 1);      /* expo-out */
        --ui-ease-out-suave: cubic-bezier(0.25, 1, 0.5, 1); /* quart-out */
        --ui-ease-in: cubic-bezier(0.4, 0, 1, 1);
        --ui-ease-in-out: cubic-bezier(0.65, 0, 0.35, 1);
        --ui-ease-mola: cubic-bezier(0.34, 1.36, 0.64, 1);  /* overshoot leve */
        --ui-dur-micro: 140ms;
        --ui-dur-curta: 220ms;
        --ui-dur-media: 340ms;
        --ui-dur-longa: 580ms;
        --ui-sombra-elevada: 0 22px 44px -20px rgba(15, 23, 42, 0.32), 0 8px 16px -10px rgba(15, 23, 42, 0.14);
    }
    body.dark-theme {
        --ui-sombra-elevada: 0 24px 48px -20px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(22, 167, 101, 0.16);
    }

    /* ======================================================================
       1. ENTRADA DA PÁGINA — cascata (staggered), só CSS
       ====================================================================== */
    @keyframes ui-entrada { from { opacity: 0; transform: translate3d(0, 14px, 0); } }
    @keyframes ui-entrada-item { from { opacity: 0; transform: translate3d(0, 12px, 0) scale(0.985); } }
    @keyframes ui-entrada-linha { from { opacity: 0; transform: translate3d(0, 6px, 0); } }
    @keyframes ui-fade { from { opacity: 0; } }

    html.ui-motion .main > :not(script, style, template, link, .modal, .modal-m, [class*="modal"], .a11y-sr-only) {
        animation: ui-entrada var(--ui-dur-longa) var(--ui-ease-out) backwards;
        animation-delay: calc(var(--ui-i, 0) * 55ms);
    }
    .main > :nth-child(2) { --ui-i: 1; } .main > :nth-child(3) { --ui-i: 2; }
    .main > :nth-child(4) { --ui-i: 3; } .main > :nth-child(5) { --ui-i: 4; }
    .main > :nth-child(6) { --ui-i: 5; } .main > :nth-child(n+7) { --ui-i: 6; }

    /* Itens de grades e listas: cada um um pouco depois do anterior. */
    html.ui-motion :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid,
        .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista,
        .resumo-categoria-list, .smart-list, .alerts-wrapper) > * {
        animation: ui-entrada-item var(--ui-dur-longa) var(--ui-ease-out) backwards;
        animation-delay: calc(90ms + var(--ui-j, 0) * 45ms);
    }
    :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista, .resumo-categoria-list, .smart-list, .alerts-wrapper) > :nth-child(2) { --ui-j: 1; }
    :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista, .resumo-categoria-list, .smart-list, .alerts-wrapper) > :nth-child(3) { --ui-j: 2; }
    :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista, .resumo-categoria-list, .smart-list, .alerts-wrapper) > :nth-child(4) { --ui-j: 3; }
    :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista, .resumo-categoria-list, .smart-list, .alerts-wrapper) > :nth-child(5) { --ui-j: 4; }
    :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista, .resumo-categoria-list, .smart-list, .alerts-wrapper) > :nth-child(6) { --ui-j: 5; }
    :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista, .resumo-categoria-list, .smart-list, .alerts-wrapper) > :nth-child(7) { --ui-j: 6; }
    :is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .ca-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista, .resumo-categoria-list, .smart-list, .alerts-wrapper) > :nth-child(n+8) { --ui-j: 7; }

    /* Linhas de tabela: cascata bem curta (as 12 primeiras). */
    html.ui-motion .main table tbody tr:nth-child(-n+12) {
        animation: ui-entrada-linha var(--ui-dur-media) var(--ui-ease-out) backwards;
        animation-delay: calc(160ms + var(--ui-l, 0) * 28ms);
    }
    .main tbody tr:nth-child(2) { --ui-l: 1; } .main tbody tr:nth-child(3) { --ui-l: 2; } .main tbody tr:nth-child(4) { --ui-l: 3; }
    .main tbody tr:nth-child(5) { --ui-l: 4; } .main tbody tr:nth-child(6) { --ui-l: 5; } .main tbody tr:nth-child(7) { --ui-l: 6; }
    .main tbody tr:nth-child(n+8) { --ui-l: 7; }

    /* ======================================================================
       2. REVELAR NA ROLAGEM (uma vez só, depois para de observar)
       ====================================================================== */
    html.ui-motion .ui-aguardando {
        opacity: 0 !important;
        transform: translate3d(0, 22px, 0) !important;
        animation: none !important;
    }
    html.ui-motion .ui-revelando {
        transition: opacity 0.6s var(--ui-ease-out-suave), transform 0.75s var(--ui-ease-out) !important;
        transition-delay: var(--ui-atraso, 0ms) !important;
    }

    /* Conteúdo que chega depois (fetch, "ver mais", trocas de skeleton). */
    html.ui-motion .ui-novo { animation: ui-entrada-item var(--ui-dur-media) var(--ui-ease-out) backwards; animation-delay: var(--ui-atraso, 0ms); }
    html.ui-motion .ui-novo-fade { animation: ui-fade var(--ui-dur-curta) ease-out backwards; }

    /* Estados vazios: surgem com calma, ícone "pousa" levemente. */
    @keyframes ui-vazio-icone { from { opacity: 0; transform: translate3d(0, 6px, 0) scale(0.9); } }
    html.ui-motion :is(.no-data-message, .cards-empty, .det-tabela-vazia, .det-chart-vazio, .chart-empty-overlay.visible, .alertas-modal-empty) {
        animation: ui-fade 0.5s var(--ui-ease-out-suave) 0.15s backwards;
    }
    html.ui-motion :is(.no-data-message, .cards-empty, .alertas-modal-empty) i {
        animation: ui-vazio-icone 0.6s var(--ui-ease-mola) 0.25s backwards;
    }

    /* ======================================================================
       3. CARTÕES — elevação suave no hover (só CSS)
       Nada aqui depende da POSIÇÃO do mouse: o cartão só reage ao fato de
       estar sob o ponteiro (hover) — sem inclinação, luz ou parallax que
       acompanhem a seta.
       ====================================================================== */
    html.ui-motion :is(.premium-card, .stat-card, .pg-card, .stat-pill, .ocorr-card, .ca-card) {
        transition: transform 0.35s var(--ui-ease-out), box-shadow 0.35s var(--ui-ease-out), border-color 0.25s ease;
    }
    @media (hover: hover) {
        html.ui-motion :is(.premium-card, .stat-card, .pg-card, .stat-pill, .ocorr-card, .ca-card):hover {
            transform: translate3d(0, -4px, 0);
            box-shadow: var(--ui-sombra-elevada);
        }
        /* O selo do cartão cresce de leve, com mola. */
        html.ui-motion :is(.premium-card-icon-zone, .stat-card-icon, .pg-card-icon, .stat-pill-icone, .ca-icon-wrapper) {
            transition: scale 0.4s var(--ui-ease-mola), rotate 0.4s var(--ui-ease-mola);
        }
        html.ui-motion :is(.premium-card, .stat-card, .pg-card, .stat-pill, .ocorr-card, .ca-card):hover :is(.premium-card-icon-zone, .stat-card-icon, .pg-card-icon, .stat-pill-icone, .ca-icon-wrapper) {
            scale: 1.07;
            rotate: -4deg;
        }
    }

    /* ======================================================================
       4. BOTÕES — hover, elevação, pressão, ripple, ícones, estados
       ====================================================================== */
    html.ui-motion :where(button, .btn, a[class*="btn"], [role="button"], input[type="submit"]) {
        transition-property: background-color, border-color, color, box-shadow, opacity, scale, translate, transform, filter;
        transition-duration: var(--ui-dur-curta);
        transition-timing-function: var(--ui-ease-out);
    }
    html.ui-motion :where(button, .btn, a[class*="btn"], input[type="submit"]):where(:hover:not(:disabled, [aria-disabled="true"], [aria-busy="true"], [role="switch"])) {
        translate: 0 -1px;
    }
    html.ui-motion :where(button, .btn, a[class*="btn"], [role="button"], input[type="submit"]):where(:active:not(:disabled, [aria-disabled="true"], [role="switch"])) {
        translate: 0 0;
        scale: 0.97;
        transition-duration: 80ms;
    }
    /* Botões de ação principal ganham sombra verde ao subir. */
    html.ui-motion :where(.btn-add, .submit-btn, .btn-aplicar, .btn-primary, [class*="btn-salvar"], .ui-dialogo-btn--confirmar):where(:hover:not(:disabled)) {
        box-shadow: 0 10px 22px -10px color-mix(in srgb, var(--verde-principal, #0e4a27) 70%, transparent);
    }

    /* Ripple discreto: cor do próprio texto do botão, some em 600 ms. */
    .ui-ripple-camada {
        position: absolute;
        inset: 0;
        border-radius: inherit;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }
    .ui-ripple {
        position: absolute;
        border-radius: 50%;
        background: currentColor;
        opacity: 0;
        transform: scale(0);
        animation: ui-ripple 0.6s var(--ui-ease-out-suave) forwards;
    }
    @keyframes ui-ripple {
        0% { opacity: 0.2; transform: scale(0); }
        100% { opacity: 0; transform: scale(1); }
    }

    /* Ícones dentro de botões/links respondem ao hover (propriedades
       independentes: não brigam com o transform do Font Awesome). */
    html.ui-motion :is(button, a, [role="button"]) > :is(i, svg) {
        transition: translate var(--ui-dur-media) var(--ui-ease-out), rotate 0.45s var(--ui-ease-mola), scale var(--ui-dur-media) var(--ui-ease-mola);
    }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-plus, .fa-circle-plus) { rotate: 90deg; }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-arrow-right, .fa-chevron-right, .fa-angle-right, .fa-arrow-right-long, .fa-right-from-bracket, .fa-arrow-up-right-from-square) { translate: 3px 0; }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-arrow-left, .fa-chevron-left, .fa-angle-left) { translate: -3px 0; }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-pen, .fa-pencil, .fa-pen-to-square) { rotate: -14deg; }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-download, .fa-file-arrow-down, .fa-file-pdf, .fa-file-export) { translate: 0 2px; }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-floppy-disk, .fa-check, .fa-circle-check, .fa-eye, .fa-magnifying-glass, .fa-bell) { scale: 1.14; }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-rotate, .fa-arrows-rotate, .fa-right-left, .fa-sliders, .fa-filter) { rotate: 180deg; }
    html.ui-motion :is(button, a, [role="button"]):hover > :is(.fa-trash, .fa-trash-can, .fa-xmark) { animation: ui-icone-alerta 0.45s var(--ui-ease-out) 1; }
    @keyframes ui-icone-alerta { 25% { rotate: -10deg; } 60% { rotate: 8deg; } 100% { rotate: 0deg; } }

    /* Spinner elegante (anel), usado nos botões e indicadores. */
    .ui-spinner {
        display: inline-block;
        width: 1em;
        height: 1em;
        flex-shrink: 0;
        vertical-align: -0.15em;
        border-radius: 50%;
        border: 2px solid color-mix(in srgb, currentColor 25%, transparent);
        border-top-color: currentColor;
        animation: ui-girar 0.7s linear infinite;
    }
    @keyframes ui-girar { to { transform: rotate(360deg); } }
    /* Mesmo com movimento reduzido o anel precisa girar devagar — é o único
       sinal de "processando" além do texto. */
    html:not(.ui-motion) .ui-spinner { animation: ui-girar 1.6s linear infinite !important; }

    :where(button, a)[aria-busy="true"] { cursor: progress; }
    .ui-btn-carregando { pointer-events: none; }

    /* Sucesso: o botão fica verde por um instante com um "check" desenhado. */
    .ui-btn-sucesso {
        background-color: var(--verde-secundario, #16a765) !important;
        border-color: var(--verde-secundario, #16a765) !important;
        color: #ffffff !important;
    }
    .ui-check { width: 1.1em; height: 1.1em; vertical-align: -0.2em; }
    .ui-check path { stroke-dasharray: 24; stroke-dashoffset: 24; animation: ui-desenhar 0.45s var(--ui-ease-out) 0.05s forwards; }
    html:not(.ui-motion) .ui-check path { stroke-dashoffset: 0; animation: none; }
    @keyframes ui-desenhar { to { stroke-dashoffset: 0; } }

    /* Erro: tremida curta (3 oscilações amortecidas) + anel vermelho. */
    @keyframes ui-tremer {
        0%, 100% { translate: 0 0; }
        20% { translate: -6px 0; }
        40% { translate: 5px 0; }
        60% { translate: -3px 0; }
        80% { translate: 2px 0; }
    }
    .ui-btn-erro, .ui-campo-invalido { box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.28) !important; border-color: #dc2626 !important; }
    html.ui-motion :is(.ui-btn-erro, .ui-campo-invalido, .ui-tremer) { animation: ui-tremer 0.45s var(--ui-ease-out) 1; }

    /* Ícones dos toasts: check se desenha; erro dá uma tremida. */
    html.ui-motion .ui-toast--sucesso .ui-toast-icone path { stroke-dasharray: 24; stroke-dashoffset: 24; animation: ui-desenhar 0.5s var(--ui-ease-out) 0.18s forwards; }
    html.ui-motion .ui-toast--erro .ui-toast-icone { animation: ui-tremer 0.45s var(--ui-ease-out) 0.2s 1; }
    html.ui-motion .ui-toast { animation-timing-function: var(--ui-ease-out); animation-duration: 0.45s; }

    /* Link clicado: resposta imediata até a próxima página aparecer. */
    .ui-navegando { opacity: 0.72; }
    .menu a.ui-navegando { background: var(--menu-hover-bg); color: var(--menu-text-hover); opacity: 1; }

    /* ======================================================================
       5. CARREGAMENTO — skeletons, barra de API, transição para o conteúdo
       ====================================================================== */
    .ui-skeleton-grupo { display: flex; flex-direction: column; gap: 10px; padding: 6px 0; }
    .ui-skeleton-linha { display: flex; gap: 12px; align-items: center; }
    td .ui-skeleton-grupo { padding: 4px 0; }
    /* Requisição em andamento (fetch): barra fina "indeterminada". */
    .ui-barra-navegacao.api {
        width: 100% !important;
        background: linear-gradient(90deg, transparent, var(--verde-secundario, #16a765), #4ade80, transparent);
        background-size: 40% 100%;
        background-repeat: no-repeat;
        animation: ui-barra-api 1.1s var(--ui-ease-in-out) infinite;
        box-shadow: none;
    }
    @keyframes ui-barra-api { from { background-position: -40% 0; } to { background-position: 140% 0; } }
    html:not(.ui-motion) .ui-barra-navegacao.api { animation: none; background: var(--verde-secundario, #16a765); }

    /* Barras de progresso/nível crescem da esquerda ao aparecer. */
    @keyframes ui-barra-crescer { from { transform: scaleX(0); } }
    html.ui-motion .ui-barra-animada {
        transform-origin: left center;
        animation: ui-barra-crescer 1s var(--ui-ease-out) backwards;
        animation-delay: var(--ui-atraso, 0ms);
    }

    /* Valor de indicador que mudou depois de carregado: destaque breve. */
    @keyframes ui-valor-mudou {
        0% { opacity: 0.35; translate: 0 6px; }
        100% { opacity: 1; translate: 0 0; }
    }
    html.ui-motion .ui-valor-atualizado { animation: ui-valor-mudou 0.45s var(--ui-ease-out); }

    /* ======================================================================
       6. INDICADORES DE STATUS
       ====================================================================== */
    @keyframes ui-surgir-pop { from { opacity: 0; scale: 0.85; } }
    html.ui-motion .main :is([class*="badge"]:not([class*="badges"]), .alert-item-icone) {
        animation: ui-surgir-pop 0.45s var(--ui-ease-mola) 0.38s backwards;
    }
    /* "Online": duas ondas discretas ao carregar e para (sem loop). */
    .user-online-dot::after {
        content: "";
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        border: 2px solid var(--verde-secundario, #16a765);
        opacity: 0;
    }
    html.ui-motion .user-online-dot::after { animation: ui-onda 1.8s var(--ui-ease-out) 0.6s 2; }
    @keyframes ui-onda { 0% { opacity: 0.7; transform: scale(1); } 100% { opacity: 0; transform: scale(2.4); } }

    /* ======================================================================
       7. MENU LATERAL E MODAIS
       ====================================================================== */
    html.ui-motion .sidebar,
    html.ui-motion .main { transition-timing-function: var(--ui-ease-out); transition-duration: 0.42s; }

    /* Indicador do item ativo: é ele que desliza entre os itens ao trocar
       de página (View Transition "ui-menu-ativo" em ui-core). */
    .menu a { isolation: isolate; }
    .ui-menu-indicador { display: none; }
    body:not(.high-contrast) .menu a.active { background: transparent; border-left-color: transparent; box-shadow: none; }
    body:not(.high-contrast) .menu a.active > .ui-menu-indicador {
        display: block;
        position: absolute;
        top: 0;
        bottom: 0;
        left: -4px;
        right: 0;
        z-index: -1;
        border-radius: 0 10px 10px 0;
        background: var(--menu-active-bg);
        border-left: 4px solid var(--menu-active-accent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent);
        view-transition-name: ui-menu-ativo;
    }

    /* Hover do menu: ícone sobe 2px com mola leve (substitui o "quique"). */
    @keyframes ui-icone-menu { 0% { transform: none; } 45% { transform: translate3d(0, -2px, 0) scale(1.1); } 100% { transform: none; } }
    html.ui-motion .menu a:hover i { animation: ui-icone-menu 0.5s var(--ui-ease-mola); }
    html.ui-motion .menu a { transition-timing-function: var(--ui-ease-out); transition-duration: var(--ui-dur-curta); }

    /* Gaveta do celular: itens entram em cascata ao abrir. */
    @keyframes ui-menu-item { from { opacity: 0; transform: translate3d(-14px, 0, 0); } }
    html.ui-motion body.menu-mobile-aberto .sidebar .menu a {
        animation: ui-menu-item 0.42s var(--ui-ease-out) backwards;
        animation-delay: calc(70ms + var(--ui-k, 0) * 28ms);
    }
    .menu a:nth-child(2) { --ui-k: 1; } .menu a:nth-child(3) { --ui-k: 2; } .menu a:nth-child(4) { --ui-k: 3; }
    .menu a:nth-child(5) { --ui-k: 4; } .menu a:nth-child(6) { --ui-k: 5; } .menu a:nth-child(7) { --ui-k: 6; }
    .menu a:nth-child(8) { --ui-k: 7; } .menu a:nth-child(n+9) { --ui-k: 8; }

    /* Modais das telas: caixa entra subindo (com leve escala) e sai
       descendo enquanto o fundo escurecido some. */
    @keyframes ui-modal-entrar { from { opacity: 0; transform: translate3d(0, 16px, 0) scale(0.97); } }
    html.ui-motion :where(.modal, .modal-m) :where(.modal-card, .modal-m-card) {
        transition: transform 0.28s var(--ui-ease-in), opacity 0.2s ease;
    }
    html.ui-motion :where(.modal:not(.active), .modal-m:not(.active)) :where(.modal-card, .modal-m-card) {
        transform: translate3d(0, 10px, 0) scale(0.97);
    }
    html.ui-motion :where(.modal.active, .modal-m.active) :where(.modal-card, .modal-m-card) {
        animation: ui-modal-entrar 0.42s var(--ui-ease-out) backwards;
    }
    html.ui-motion .alertas-modal,
    html.ui-motion .accessibility-modal { transition-timing-function: var(--ui-ease-out) !important; transition-duration: 0.36s !important; }
    html.ui-motion .alertas-modal-item { animation: ui-entrada-item 0.45s var(--ui-ease-out) backwards; }
    html.ui-motion .alertas-modal-item:nth-child(2) { animation-delay: 40ms; }
    html.ui-motion .alertas-modal-item:nth-child(3) { animation-delay: 80ms; }
    html.ui-motion .alertas-modal-item:nth-child(n+4) { animation-delay: 120ms; }


    /* Dropdowns (menu "⋮" das listas) e painéis de expandir ("Ver
       detalhes", lista de rações em alerta): entram suaves sempre que abrem
       — a animação CSS reinicia quando o elemento volta a ser exibido. */
    @keyframes ui-dropdown { from { opacity: 0; transform: translate3d(0, -6px, 0) scale(0.96); } }
    @keyframes ui-expandir { from { opacity: 0; transform: translate3d(0, -8px, 0); } }
    html.ui-motion :is(.lote-row-dropdown, .cocho-row-dropdown, .racao-row-dropdown).aberto {
        transform-origin: top right;
        animation: ui-dropdown 0.22s var(--ui-ease-out) both;
    }
    html.ui-motion :is(.lote-row-detalhes, .cocho-row-detalhes, .racao-row-detalhes, .alerta-estoque-lista-wrap):not([hidden]) {
        animation: ui-expandir 0.34s var(--ui-ease-out) backwards;
    }
    /* Setas de abrir/fechar giram com a mesma curva. */
    html.ui-motion :is(.btn-ver-detalhes, .box-chevron) i,
    html.ui-motion .btn-ver-detalhes i { transition: transform 0.32s var(--ui-ease-out); }

    /* PERFORMANCE: o conteúdo (.main) nunca anima margin/width (cada tela
       repetia essa transição — recalculava o layout da página a cada quadro
       ao abrir/recolher o menu). O deslize agora é por transform (FLIP, no
       script abaixo). Especificidade maior que o ".sidebar, .main" das telas. */
    html body .main { transition-property: background-color, color, border-color, box-shadow, filter, transform; }

    /* Números com largura fixa por dígito: a contagem animada não faz o
       texto "tremer" nem empurrar o que está ao lado. */
    :is(.premium-card-value, .stat-card-valor, .pg-card-value, .metric-value, .stat-pill-valor, [data-contador]) { font-variant-numeric: tabular-nums; }

    /* Impressão: nada pode ficar escondido esperando a rolagem. */
    @media print {
        .ui-aguardando, .ui-revelando, .ui-novo { opacity: 1 !important; transform: none !important; animation: none !important; }
        .ui-ripple-camada, .ui-barra-navegacao { display: none !important; }
    }
</style>

<script>
(function () {
    'use strict';
    if (window.__agriMotion) return;
    window.__agriMotion = true;

    var raiz = document.documentElement;
    var movimento = function () { return raiz.classList.contains('ui-motion'); };

    function garantirPosicao(el) {
        if (getComputedStyle(el).position === 'static') el.style.position = 'relative';
    }

    // (Cartões: a elevação no hover é só CSS — seção 3. Nenhum listener de
    // movimento do mouse em lugar nenhum desta camada.)

    // ==================================================================
    // 4. Ripple nos botões
    // ==================================================================
    var SEL_RIPPLE = 'button, .btn, a[class*="btn"], [role="button"], .menu a, .mobile-bottom-nav a, .periodo-chip, .sidebar-search-result-item, .premium-card-link';
    var SEM_RIPPLE = '[role="switch"], .switch-btn, .accessibility-trigger, [vw-access-button], [vw], .ui-toast-fechar, [data-sem-ripple], .slider-switch, :disabled, [aria-busy="true"]';

    document.addEventListener('pointerdown', function (e) {
        if (!movimento() || e.button !== 0) return;
        var alvo = e.target.closest && e.target.closest(SEL_RIPPLE);
        if (!alvo || alvo.matches(SEM_RIPPLE) || alvo.closest('[vw]')) return;
        var r = alvo.getBoundingClientRect();
        if (r.width < 24 || r.height < 18) return;
        var camada = alvo.querySelector(':scope > .ui-ripple-camada');
        if (!camada) {
            garantirPosicao(alvo);
            camada = document.createElement('span');
            camada.className = 'ui-ripple-camada';
            camada.setAttribute('aria-hidden', 'true');
            alvo.appendChild(camada);
        }
        // Sempre do CENTRO do botão (independente de onde o clique caiu):
        // a onda é resposta ao clique, não à posição do mouse.
        var tamanho = Math.hypot(r.width, r.height) * 1.05;
        var onda = document.createElement('span');
        onda.className = 'ui-ripple';
        onda.style.width = onda.style.height = tamanho + 'px';
        onda.style.left = (r.width / 2 - tamanho / 2) + 'px';
        onda.style.top = (r.height / 2 - tamanho / 2) + 'px';
        camada.appendChild(onda);
        onda.addEventListener('animationend', function () { onda.remove(); });
        setTimeout(function () { onda.remove(); }, 900);
    }, { passive: true });

    // ==================================================================
    // Botões: carregando / sucesso / erro (API pública)
    // ==================================================================
    var CHECK_SVG = '<svg class="ui-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>';

    function carregando(botao, texto) {
        if (!botao || botao.dataset.uiHtmlOriginal) return;
        botao.dataset.uiHtmlOriginal = botao.innerHTML;
        botao.style.minWidth = botao.offsetWidth + 'px'; // largura não "pula"
        botao.setAttribute('aria-busy', 'true');
        botao.classList.add('ui-btn-carregando');
        botao.innerHTML = '<span class="ui-spinner" aria-hidden="true"></span> ' + (texto || 'Carregando…');
        // Se a tela restaurar o botão por conta própria (textContent =
        // original), limpa o estado de carregamento junto.
        var obs = new MutationObserver(function () {
            if (!botao.querySelector('.ui-spinner')) { obs.disconnect(); limparEstado(botao, false); }
        });
        obs.observe(botao, { childList: true });
        botao._uiObs = obs;
    }
    function limparEstado(botao, restaurar) {
        if (botao._uiObs) { botao._uiObs.disconnect(); delete botao._uiObs; }
        if (restaurar && botao.dataset.uiHtmlOriginal) botao.innerHTML = botao.dataset.uiHtmlOriginal;
        delete botao.dataset.uiHtmlOriginal;
        botao.removeAttribute('aria-busy');
        botao.classList.remove('ui-btn-carregando');
        botao.style.minWidth = '';
    }
    function sucesso(botao, texto) {
        if (!botao) return;
        var original = botao.dataset.uiHtmlOriginal || botao.innerHTML;
        limparEstado(botao, false);
        botao.style.minWidth = botao.offsetWidth + 'px';
        botao.classList.add('ui-btn-sucesso');
        botao.innerHTML = CHECK_SVG + ' ' + (texto || 'Concluído');
        setTimeout(function () {
            botao.classList.remove('ui-btn-sucesso');
            botao.innerHTML = original;
            botao.style.minWidth = '';
        }, 1500);
    }
    function erro(el) {
        if (!el) return;
        if (el.dataset.uiHtmlOriginal) limparEstado(el, true);
        el.classList.remove('ui-btn-erro');
        void el.offsetWidth; // reinicia a animação
        el.classList.add('ui-btn-erro');
        setTimeout(function () { el.classList.remove('ui-btn-erro'); }, 900);
    }

    // ==================================================================
    // Skeleton sob demanda
    // ==================================================================
    function skeleton(container, opcoes) {
        if (!container) return;
        opcoes = opcoes || {};
        var linhas = opcoes.linhas || 3;
        var html = '<div class="ui-skeleton-grupo" aria-hidden="true">';
        for (var i = 0; i < linhas; i++) {
            html += '<div class="ui-skeleton-linha">'
                + (opcoes.avatar ? '<span class="ui-skeleton" style="width:36px;height:36px;border-radius:10px;flex-shrink:0"></span>' : '')
                + '<span class="ui-skeleton" style="height:12px;flex:1;max-width:' + [92, 74, 84, 66][i % 4] + '%"></span></div>';
        }
        html += '</div><span class="a11y-sr-only">' + (opcoes.texto || 'Carregando…') + '</span>';
        container.setAttribute('aria-busy', 'true');
        container.innerHTML = html;
    }

    // ==================================================================
    // 5. Indicador de requisições (fetch) — só se demorar > 250 ms
    // ==================================================================
    var SEGUNDO_PLANO = /\/(alertas\/resumo|acessibilidade|animais-por-lote)(\?|$)/;
    var emAndamento = 0;
    var timerApi = null;
    function atualizarBarraApi() {
        var barra = document.querySelector('.ui-barra-navegacao');
        if (emAndamento > 0) {
            if (timerApi) return;
            timerApi = setTimeout(function () {
                if (!window.AgriUI) return;
                barra = document.querySelector('.ui-barra-navegacao');
                if (!barra) { AgriUI.iniciarCarregamento(); AgriUI.pararCarregamento(); barra = document.querySelector('.ui-barra-navegacao'); }
                if (barra && emAndamento > 0 && !barra.classList.contains('ativa')) barra.classList.add('ativa', 'api');
            }, 250);
        } else {
            clearTimeout(timerApi);
            timerApi = null;
            if (barra && barra.classList.contains('api')) barra.classList.remove('ativa', 'api');
        }
    }
    if (window.fetch) {
        var fetchOriginal = window.fetch;
        window.fetch = function (recurso, init) {
            var url = '';
            try { url = new URL(typeof recurso === 'string' ? recurso : recurso.url, location.href); } catch (e) { /* segue */ }
            var rastrear = url && url.origin === location.origin && !SEGUNDO_PLANO.test(url.pathname);
            if (!rastrear) return fetchOriginal.apply(this, arguments);
            emAndamento++;
            atualizarBarraApi();
            return fetchOriginal.apply(this, arguments).finally(function () {
                emAndamento = Math.max(0, emAndamento - 1);
                atualizarBarraApi();
            });
        };
    }

    // ==================================================================
    // Formulários: campo inválido e mensagens de erro chamam atenção
    // ==================================================================
    var ultimoInvalido = 0;
    document.addEventListener('invalid', function (e) {
        var campo = e.target;
        campo.classList.add('ui-campo-invalido');
        var limpar = function () { campo.classList.remove('ui-campo-invalido'); };
        campo.addEventListener('input', limpar, { once: true });
        campo.addEventListener('change', limpar, { once: true });
        // Um aviso por tentativa de envio (não um por campo).
        var agora = Date.now();
        if (agora - ultimoInvalido > 500 && campo.form) {
            var botao = campo.form.querySelector('button[type="submit"], button:not([type]), input[type="submit"]');
            if (botao) erro(botao);
        }
        ultimoInvalido = agora;
    }, true);

    // ==================================================================
    // Inicialização (DOM pronto)
    // ==================================================================
    // ------------------------------------------------------------------
    // FLIP do conteúdo ao abrir/recolher o menu: a margem muda de uma vez
    // (um único cálculo de layout) e o conteúdo "desliza" do lugar antigo
    // para o novo só com transform — em vez de recalcular o layout da
    // página inteira a cada quadro animando margin/width.
    // ------------------------------------------------------------------
    function ligarFlipDoConteudo(main) {
        var esquerdaAnterior = main.getBoundingClientRect().left;
        new MutationObserver(function () {
            var esquerdaNova = main.getBoundingClientRect().left;
            var dx = esquerdaAnterior - esquerdaNova;
            esquerdaAnterior = esquerdaNova;
            if (!dx || !movimento()) return;
            main.style.transition = 'none';
            main.style.transform = 'translate3d(' + dx + 'px, 0, 0)';
            main.getBoundingClientRect(); // aplica o ponto de partida
            main.style.transition = 'transform 0.42s cubic-bezier(0.16, 1, 0.3, 1)';
            main.style.transform = '';
            var limpar = function () { main.style.transition = ''; main.removeEventListener('transitionend', limpar); };
            main.addEventListener('transitionend', limpar);
            setTimeout(limpar, 600);
        }).observe(main, { attributes: true, attributeFilter: ['class'] });
    }

    function iniciar() {
        var main = document.querySelector('.main');
        if (main) ligarFlipDoConteudo(main);


        // Mensagens de erro do servidor: uma tremida ao aparecer.
        document.querySelectorAll('.alerta-flash.alerta-erro, .alerta-erro:not(.alerta-flash)').forEach(function (m) {
            if (m.offsetParent) m.classList.add('ui-tremer');
        });

        // "Carregando…" estático em tabelas/textos → skeleton.
        document.querySelectorAll('.main [data-ui-skeleton]').forEach(function (el) {
            skeleton(el, { linhas: parseInt(el.dataset.uiSkeleton, 10) || 3 });
        });

        if (!main) return;

        // ---- Barras de progresso/nível com largura inline (%) crescem.
        var barras = Array.prototype.filter.call(main.querySelectorAll('[style*="width"]'), function (el) {
            return /width:\s*[\d.]+%/.test(el.getAttribute('style') || '') && el.offsetHeight > 0 && el.offsetHeight <= 16 && !el.closest('.modal, .modal-m');
        });

        // ---- Revelar na rolagem: o que está ABAIXO da tela ao carregar.
        var candidatos = main.querySelectorAll(':scope > :not(script, style, template, .modal, .modal-m, [class*="modal"]), '
            + ':is(.cards-row, .stat-cards-row, .destaque-cards-row, .pg-cards, .content-graphics-grid, .dashboard-grid, .ocorr-grid, .ca-cards-grid, .lotes-lista, .racoes-lista, .estoques-lista, .cochos-lista) > *, '
            + 'table tbody tr:nth-child(-n+40)');
        var alturaTela = window.innerHeight;

        if (movimento() && 'IntersectionObserver' in window) {
            var observador = new IntersectionObserver(function (entradas) {
                var visiveis = entradas.filter(function (en) { return en.isIntersecting; })
                    .sort(function (a, b) { return (a.boundingClientRect.top - b.boundingClientRect.top) || (a.boundingClientRect.left - b.boundingClientRect.left); });
                visiveis.forEach(function (en, i) {
                    var el = en.target;
                    observador.unobserve(el);
                    el.style.setProperty('--ui-atraso', Math.min(i, 6) * 60 + 'ms');
                    el.classList.add('ui-revelando');
                    requestAnimationFrame(function () { el.classList.remove('ui-aguardando'); });
                    var fim = function () {
                        el.classList.remove('ui-revelando');
                        el.style.removeProperty('--ui-atraso');
                    };
                    el.addEventListener('transitionend', function (ev) { if (ev.target === el && ev.propertyName === 'transform') fim(); });
                    setTimeout(fim, 1400);
                });
            }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

            candidatos.forEach(function (el) {
                var r = el.getBoundingClientRect();
                if (r.height && r.top > alturaTela) {
                    el.classList.add('ui-aguardando');
                    observador.observe(el);
                }
            });

            // Barras: animam quando entram na tela (uma vez).
            var obsBarras = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (en, i) {
                    if (!en.isIntersecting) return;
                    obsBarras.unobserve(en.target);
                    en.target.style.setProperty('--ui-atraso', (250 + i * 50) + 'ms');
                    en.target.classList.add('ui-barra-animada');
                });
            }, { threshold: 0.3 });
            barras.forEach(function (b) { obsBarras.observe(b); });
        }

        // ---- Valores de indicadores atualizados pela própria tela.
        var obsValores = new MutationObserver(function (mutacoes) {
            mutacoes.forEach(function (m) {
                var el = m.target.nodeType === 1 ? m.target : m.target.parentElement;
                el = el && el.closest('.premium-card-value, .stat-card-valor, .pg-card-value, .metric-value, .stat-pill-valor, [data-contador]');
                if (!el || el.dataset.uiContando || !movimento()) return;
                el.classList.remove('ui-valor-atualizado');
                void el.offsetWidth;
                el.classList.add('ui-valor-atualizado');
            });
        });
        main.querySelectorAll('.premium-card-value, .stat-card-valor, .pg-card-value, .metric-value, .stat-pill-valor, [data-contador]').forEach(function (el) {
            obsValores.observe(el, { childList: true, characterData: true, subtree: true });
        });
    }

    // ---- Conteúdo inserido depois do carregamento (fetch, "ver mais"):
    // entra suave, em cascata curta. Começa só após o "load".
    function observarConteudoNovo() {
        var main = document.querySelector('.main');
        if (!main) return;
        var IGNORAR = /^(SCRIPT|STYLE|OPTION|OPTGROUP|I|SVG|PATH|BR|CANVAS|TEMPLATE|LINK)$/;
        new MutationObserver(function (mutacoes) {
            if (!movimento()) return;
            var novos = [];
            mutacoes.forEach(function (m) {
                m.addedNodes.forEach(function (n) {
                    if (n.nodeType !== 1 || IGNORAR.test(n.tagName)) return;
                    if (n.matches('.ui-ripple-camada, .ui-ripple, .a11y-sr-only, [class*="ui-skeleton"], .ui-spinner, .ui-check')) return;
                    if (n.closest('.ui-ripple-camada, button, [aria-busy="true"]')) return;
                    novos.push(n);
                });
            });
            novos.slice(0, 40).forEach(function (n, i) {
                var estilo = getComputedStyle(n);
                if (estilo.display === 'none' || estilo.display === 'inline') return;
                var classe = /fixed|absolute/.test(estilo.position) ? 'ui-novo-fade' : 'ui-novo';
                n.style.setProperty('--ui-atraso', Math.min(i, 10) * 30 + 'ms');
                n.classList.add(classe);
                n.addEventListener('animationend', function () { n.classList.remove(classe); n.style.removeProperty('--ui-atraso'); }, { once: true });
            });
            // Quem estava carregando terminou.
            mutacoes.forEach(function (m) {
                if (m.target.nodeType === 1 && m.target.getAttribute('aria-busy') === 'true' && !m.target.querySelector('.ui-skeleton')) {
                    m.target.removeAttribute('aria-busy');
                }
            });
        }).observe(main, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
    else iniciar();
    if (document.readyState === 'complete') observarConteudoNovo();
    else window.addEventListener('load', observarConteudoNovo);

    // API pública (junta-se ao AgriUI do ui-core).
    var publicar = function () {
        if (!window.AgriUI) return setTimeout(publicar, 0);
        AgriUI.botao = { carregando: carregando, sucesso: sucesso, erro: erro, restaurar: function (b) { limparEstado(b, true); } };
        AgriUI.indicarCarregando = carregando;
        AgriUI.skeleton = skeleton;
    };
    publicar();
})();
</script>
