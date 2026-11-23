{{-- ==========================================================================
     UI CORE DO AGRIFARM — camada de experiência compartilhada por TODAS as
     telas (layout interno, landing, login/cadastro). Incluído por
     partials/a11y-global (que toda página já carrega) e no <head> do
     layouts/app; o @once garante uma cópia só.

     O que entrega, sem mudar nenhuma regra de negócio:
      1. Transição suave entre páginas (View Transitions entre documentos):
         menu lateral, header/navegação do celular e botões flutuantes ficam
         parados — só o conteúdo troca, com fade discreto. Navegador sem
         suporte recebe um fade de entrada leve. Sem atraso na navegação.
      2. Barra de progresso no topo enquanto a próxima página carrega.
      3. Toasts (AgriUI.toast) — também mostram as mensagens de sucesso que
         vinham do servidor e avisam queda/volta da conexão.
      4. Diálogo de confirmação próprio (AgriUI.confirmar e
         <form data-confirmar="...">) no lugar do confirm() do navegador.
      5. Skeleton nos gráficos enquanto o Chart.js monta + animação padrão.
      6. Contagem animada nos indicadores (cards de métricas).
      7. Filtros de cada tela lembrados ao voltar pelo menu.
      8. Microinterações padronizadas (botões, linhas de tabela, modais).
     Tudo respeita prefers-reduced-motion e o alto contraste.
     ========================================================================== --}}
@once
<style>
    /* ---------- 1. Transição entre páginas ---------- */
    @view-transition { navigation: auto; }

    /* Elementos persistentes: mesmo nome nas duas páginas = ficam parados
       (sem piscar) enquanto só o conteúdo faz o fade. */
    #sidebar { view-transition-name: ui-sidebar; }
    #sidebarToggleBtn { view-transition-name: ui-btn-menu; }
    #notificationsBtn { view-transition-name: ui-btn-sino; }
    #accTrigger { view-transition-name: ui-btn-acessibilidade; }
    #aiChatTrigger { view-transition-name: ui-btn-assistente; }
    .mobile-header { view-transition-name: ui-header-mobile; }
    .mobile-bottom-nav { view-transition-name: ui-nav-mobile; }
    body > nav { view-transition-name: ui-nav-landing; }

    ::view-transition-group(ui-sidebar),
    ::view-transition-group(ui-btn-menu),
    ::view-transition-group(ui-btn-sino),
    ::view-transition-group(ui-btn-acessibilidade),
    ::view-transition-group(ui-btn-assistente),
    ::view-transition-group(ui-header-mobile),
    ::view-transition-group(ui-nav-mobile),
    ::view-transition-group(ui-nav-landing) {
        animation-duration: 0s;
    }
    ::view-transition-old(ui-sidebar), ::view-transition-new(ui-sidebar),
    ::view-transition-old(ui-nav-mobile), ::view-transition-new(ui-nav-mobile),
    ::view-transition-old(ui-nav-landing), ::view-transition-new(ui-nav-landing) {
        animation-duration: 0.12s;
    }

    /* Saída e entrada coordenadas (≈180 ms + 300 ms, sobrepostas): a tela
       antiga sobe 4px e some com aceleração; a nova chega de 8px abaixo
       desacelerando (expo-out). Nas telas internas o conteúdo já entra em
       cascata (ui-motion), então a raiz só faz o fade — sem movimento
       duplicado. */
    ::view-transition-old(root) { animation: ui-vt-sair 0.18s cubic-bezier(0.4, 0, 1, 1) both; }
    ::view-transition-new(root) { animation: ui-vt-entrar 0.3s cubic-bezier(0.16, 1, 0.3, 1) 0.04s both; }
    html.ui-shell::view-transition-new(root) { animation-name: ui-vt-fade; }

    /* Indicador do item ativo do menu desliza de um item para o outro. */
    ::view-transition-group(ui-menu-ativo) { animation-duration: 0.34s; animation-timing-function: cubic-bezier(0.16, 1, 0.3, 1); }

    @keyframes ui-vt-sair { to { opacity: 0; transform: translateY(-4px); } }
    @keyframes ui-vt-entrar { from { opacity: 0; transform: translateY(8px); } }
    @keyframes ui-vt-fade { from { opacity: 0; } }

    /* Sem View Transitions (Firefox antigo etc.): só um fade curto de
       entrada no conteúdo. Apenas opacidade — transform criaria um novo
       contêiner para os modais "position: fixed" de dentro do conteúdo. */
    @supports not (view-transition-name: none) {
        .main, body > main { animation: ui-fade-entrada 0.22s ease-out both; }
    }
    @keyframes ui-fade-entrada { from { opacity: 0; } }

    /* ---------- 2. Barra de progresso da navegação ---------- */
    .ui-barra-navegacao {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 100%;
        z-index: 100001;
        /* Progresso via transform (GPU) — sem animar "width", que recalcula layout. */
        transform: scaleX(0);
        transform-origin: left center;
        pointer-events: none;
        background: linear-gradient(90deg, var(--verde-secundario, #16a765), #4ade80);
        box-shadow: 0 0 8px rgba(22, 167, 101, 0.6);
        opacity: 0;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
    }
    .ui-barra-navegacao.ativa { opacity: 1; }
    .ui-barra-navegacao.api { transform: none; }
    body.high-contrast .ui-barra-navegacao { background: #ffff00; box-shadow: none; }

    /* ---------- 2b. Vaquinha de carregamento entre páginas ----------
       Fundo verde da identidade (sem cartão branco); a vaquinha come ração
       no cocho: abaixa a cabeça, belisca, levanta mastigando, grãos pulam. */
    .ui-vaca-overlay {
        position: fixed;
        inset: 0;
        z-index: 100000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background:
            /* Verde em camadas: brilho esmeralda atrás da vaquinha, "chão"
               de pasto iluminado embaixo, luz suave vindo do alto e base
               profunda — com vinheta escura nas bordas (box-shadow). */
            radial-gradient(38% 30% at 50% 46%, rgba(74, 222, 128, 0.20), transparent 70%),
            radial-gradient(70% 34% at 50% 100%, rgba(34, 160, 88, 0.45), transparent 70%),
            radial-gradient(60% 50% at 50% -10%, rgba(52, 168, 98, 0.35), transparent 70%),
            radial-gradient(45% 45% at 0% 100%, rgba(13, 110, 62, 0.35), transparent 70%),
            radial-gradient(45% 45% at 100% 100%, rgba(13, 110, 62, 0.35), transparent 70%),
            linear-gradient(170deg, #0f5531 0%, #0a4024 45%, #062c18 100%);
        box-shadow: inset 0 0 180px rgba(2, 20, 10, 0.55);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), visibility 0s linear 0.25s;
    }
    .ui-vaca-overlay.ativa { opacity: 1; visibility: visible; transition: opacity 0.12s ease-out; }

    /* Na troca de página a vaquinha "atravessa" para a tela nova e some
       devagar por cima dela (a tela nova não tem esses nomes, então só a
       imagem antiga sai, com a animação abaixo). */
    .ui-vaca-overlay.ativa { view-transition-name: ui-vaca-fundo; }
    .ui-vaca-overlay.ativa .ui-vaca-cartao { view-transition-name: ui-vaca; }
    ::view-transition-old(ui-vaca) { animation: ui-vaca-sair 0.4s cubic-bezier(0.4, 0, 0.2, 1) 0.08s both; }
    ::view-transition-old(ui-vaca-fundo) { animation: ui-vt-sair-fundo 0.32s ease-out 0.04s both; }
    ::view-transition-group(ui-vaca), ::view-transition-group(ui-vaca-fundo) { animation-duration: 0.48s; }
    @keyframes ui-vaca-sair { to { opacity: 0; transform: translateY(-6px) scale(0.96); } }
    @keyframes ui-vt-sair-fundo { to { opacity: 0; } }

    /* Sem cartão: só a cena e o texto, centralizados sobre o verde. */
    .ui-vaca-cartao {
        width: min(380px, 100%);
        text-align: center;
        color: #ffffff;
        transform: translateY(10px) scale(0.96);
        transition: transform 0.45s cubic-bezier(0.34, 1.36, 0.64, 1);
    }
    .ui-vaca-overlay.ativa .ui-vaca-cartao { transform: none; }

    .ui-vaca { width: min(320px, 86vw); height: auto; aspect-ratio: 230 / 150; display: block; margin: 0 auto; overflow: visible; }
    /* Texto fora da tela (só a vaquinha aparece), mas continua sendo lido
       pelos leitores de tela ("Carregando" / "Salvando"). */
    .ui-vaca-texto, .ui-vaca-sub {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
    }
    .ui-vaca-pontos span { animation: ui-vaca-ponto 1.2s ease-in-out infinite; opacity: 0.2; }
    .ui-vaca-pontos span:nth-child(2) { animation-delay: 0.2s; }
    .ui-vaca-pontos span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes ui-vaca-ponto { 0%, 100% { opacity: 0.2; } 40% { opacity: 1; } }

    /* ---- Desenho ---- */
    .ui-vaca .vc-sombra { fill: rgba(0, 0, 0, 0.28); }
    .ui-vaca .vc-capim { fill: #2f8f55; }
    .ui-vaca .vc-pele { fill: #ffffff; stroke: #17241c; stroke-width: 2.4; stroke-linejoin: round; }
    .ui-vaca .vc-mancha { fill: #22302a; }
    .ui-vaca .vc-casco { fill: #3a3f3c; }
    .ui-vaca .vc-rosa { fill: #f6a9bb; stroke: #17241c; stroke-width: 2.2; }
    .ui-vaca .vc-rosa-interno { fill: #f6a9bb; }
    .ui-vaca .vc-narina { fill: #b44d68; }
    .ui-vaca .vc-chifre { fill: #f1e2bd; stroke: #a98c52; stroke-width: 1.5; }
    .ui-vaca .vc-olho { fill: #17241c; }
    .ui-vaca .vc-brilho { fill: #ffffff; }
    .ui-vaca .vc-rabo { fill: none; stroke: #17241c; stroke-width: 3.2; stroke-linecap: round; }
    .ui-vaca .vc-tufo { fill: #22302a; }
    .ui-vaca .vc-cocho { fill: #9a6431; stroke: #5b3817; stroke-width: 2.2; stroke-linejoin: round; }
    .ui-vaca .vc-cocho-tabua { fill: none; stroke: #7b4c22; stroke-width: 1.6; }
    .ui-vaca .vc-racao { fill: #e0ab47; }
    .ui-vaca .vc-grao-a { fill: #c98a2c; }
    .ui-vaca .vc-grao-b { fill: #f2c46a; }

    /* ---- Movimentos (ciclo de 2,4 s: belisca, levanta mastigando, volta) ---- */
    .ui-vaca .vc-cabeca { transform-box: view-box; transform-origin: 136px 60px; animation: vc-comer 2.4s cubic-bezier(0.45, 0, 0.25, 1) infinite; }
    @keyframes vc-comer {
        0%, 30% { transform: rotate(0deg); }
        8% { transform: rotate(3deg); }
        16% { transform: rotate(0deg); }
        24% { transform: rotate(3deg); }
        44%, 74% { transform: rotate(-17deg); }
        90%, 100% { transform: rotate(0deg); }
    }
    .ui-vaca .vc-focinho { transform-box: fill-box; transform-origin: 50% 20%; animation: vc-mastigar 2.4s ease-in-out infinite; }
    @keyframes vc-mastigar {
        0%, 44%, 74%, 100% { transform: scaleY(1); }
        50%, 62% { transform: scaleY(0.88); }
        56%, 68% { transform: scaleY(1); }
    }
    .ui-vaca .vc-graos circle { transform-box: fill-box; transform-origin: center; opacity: 0; animation: vc-grao 2.4s ease-out infinite; }
    .ui-vaca .vc-graos circle:nth-child(2) { animation-delay: 0.08s; }
    .ui-vaca .vc-graos circle:nth-child(3) { animation-delay: 0.16s; }
    @keyframes vc-grao {
        0%, 30% { opacity: 0; transform: translateY(0); }
        34% { opacity: 1; transform: translateY(-2px); }
        48% { opacity: 0; transform: translateY(-14px) translateX(4px); }
        100% { opacity: 0; }
    }
    .ui-vaca .vc-corpo { animation: vc-respirar 2.4s ease-in-out infinite; transform-box: fill-box; transform-origin: 50% 100%; }
    @keyframes vc-respirar { 50% { transform: scaleY(1.018); } }
    .ui-vaca .vc-rabo-grupo { transform-box: view-box; transform-origin: 30px 60px; animation: vc-rabo 1.7s ease-in-out infinite alternate; }
    @keyframes vc-rabo { from { transform: rotate(-12deg); } to { transform: rotate(14deg); } }
    .ui-vaca .vc-orelha { transform-box: fill-box; transform-origin: 100% 50%; animation: vc-orelha 3.6s ease-in-out infinite; }
    @keyframes vc-orelha { 0%, 80%, 100% { transform: rotate(0deg); } 85% { transform: rotate(-18deg); } 90% { transform: rotate(4deg); } }
    .ui-vaca .vc-olho-grupo { transform-box: fill-box; transform-origin: center; animation: vc-piscar 3.8s ease-in-out infinite; }
    @keyframes vc-piscar { 0%, 92%, 100% { transform: scaleY(1); } 95% { transform: scaleY(0.1); } }

    body.high-contrast .ui-vaca-overlay { background: #000000; box-shadow: none; }

    /* Barrinha de carregamento embaixo da vaquinha: enche desacelerando
       enquanto ela come (reinicia a cada carregamento), com um brilho
       correndo por dentro. Só transform (leve). */
    .ui-vaca-barra {
        position: relative;
        width: min(220px, 64vw);
        height: 8px;
        margin: 14px auto 0;
        border-radius: 999px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.10);
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(143, 224, 90, 0.18);
    }
    .ui-vaca-barra span {
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(90deg, #2fbf6b, #8fe05a 70%, #d6ffb8);
        box-shadow: 0 0 12px rgba(143, 224, 90, 0.6);
        transform-origin: left center;
        transform: scaleX(0.06);
    }
    .ui-vaca-barra span::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.65), transparent);
        transform: translateX(-100%);
        animation: ui-vaca-barra-brilho 1.3s ease-in-out infinite;
    }
    .ui-vaca-overlay.ativa .ui-vaca-barra span { animation: ui-vaca-barra-encher 4s cubic-bezier(0.1, 0.8, 0.2, 1) forwards; }
    @keyframes ui-vaca-barra-encher {
        0% { transform: scaleX(0.06); }
        35% { transform: scaleX(0.55); }
        70% { transform: scaleX(0.8); }
        100% { transform: scaleX(0.94); }
    }
    @keyframes ui-vaca-barra-brilho { to { transform: translateX(100%); } }
    body.high-contrast .ui-vaca-barra { background: #000000; box-shadow: 0 0 0 2px #ffff00; }
    body.high-contrast .ui-vaca-barra span { background: #ffff00; box-shadow: none; }
    body.high-contrast .ui-vaca-texto, body.high-contrast .ui-vaca-sub { color: #ffff00; }

    /* ---------- 3. Toasts ---------- */
    .ui-toasts {
        position: fixed;
        top: 78px;
        right: 20px;
        z-index: 100000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: min(380px, calc(100vw - 32px));
        pointer-events: none;
    }
    @media (max-width: 767px) {
        .ui-toasts { top: calc(72px + env(safe-area-inset-top, 0px)); right: 16px; left: 16px; width: auto; }
    }

    .ui-toast {
        --ui-toast-cor: var(--verde-secundario, #16a765);
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 14px 16px 16px;
        border-radius: 14px;
        background: var(--card-bg, #ffffff);
        color: var(--text-principal, #0f172a);
        border: 1px solid var(--surface-border, rgba(15, 23, 42, 0.08));
        border-left: 4px solid var(--ui-toast-cor);
        box-shadow: 0 18px 40px -12px rgba(15, 23, 42, 0.28), 0 4px 12px -4px rgba(15, 23, 42, 0.12);
        pointer-events: auto;
        overflow: hidden;
        animation: ui-toast-entrar 0.32s cubic-bezier(0.2, 0.9, 0.25, 1.15) both;
    }
    .ui-toast.saindo { animation: ui-toast-sair 0.22s ease-in both; }
    .ui-toast--erro { --ui-toast-cor: #dc2626; }
    .ui-toast--aviso { --ui-toast-cor: #d97706; }
    .ui-toast--info { --ui-toast-cor: #2563eb; }

    .ui-toast-icone {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: var(--ui-toast-cor);
    }
    .ui-toast-icone svg { width: 16px; height: 16px; }

    .ui-toast-corpo { flex: 1; min-width: 0; padding-top: 3px; }
    .ui-toast-titulo { display: block; font-size: 0.875rem; font-weight: 700; line-height: 1.3; }
    .ui-toast-texto { font-size: 0.8125rem; line-height: 1.45; color: var(--text-secundario, #64748b); margin-top: 2px; overflow-wrap: anywhere; }
    .ui-toast-titulo + .ui-toast-texto { margin-top: 3px; }
    .ui-toast-corpo > .ui-toast-texto:first-child { color: var(--text-principal, #0f172a); font-weight: 500; font-size: 0.875rem; }

    .ui-toast-acao {
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        min-height: 32px;
        border-radius: 8px;
        border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28));
        background: transparent;
        color: var(--verde-texto, #0e4a27);
        font: inherit;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .ui-toast-acao:hover { background: var(--verde-claro, #e8f5ee); }

    .ui-toast-fechar {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        margin: -4px -4px 0 0;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: var(--text-secundario, #64748b);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .ui-toast-fechar:hover { background: var(--surface-muted-bg, #f1f5f9); color: var(--text-principal, #0f172a); }
    .ui-toast-fechar svg { width: 14px; height: 14px; }

    .ui-toast-progresso {
        position: absolute;
        left: 0;
        bottom: 0;
        height: 3px;
        width: 100%;
        background: var(--ui-toast-cor);
        opacity: 0.35;
        transform-origin: left;
        animation: ui-toast-tempo linear forwards;
    }
    .ui-toast:hover .ui-toast-progresso,
    .ui-toast:focus-within .ui-toast-progresso { animation-play-state: paused; }

    @keyframes ui-toast-entrar { from { opacity: 0; transform: translateX(24px) scale(0.98); } }
    @keyframes ui-toast-sair { to { opacity: 0; transform: translateX(24px) scale(0.98); } }
    @keyframes ui-toast-tempo { to { transform: scaleX(0); } }

    body.high-contrast .ui-toast { background: #000000; color: #ffff00; border: 2px solid #ffff00; box-shadow: none; }
    body.high-contrast .ui-toast-texto,
    body.high-contrast .ui-toast-corpo > .ui-toast-texto:first-child,
    body.high-contrast .ui-toast-fechar,
    body.high-contrast .ui-toast-acao { color: #ffff00; }
    body.high-contrast .ui-toast-icone { background: #000000; border: 2px solid #ffff00; color: #ffff00; }
    body.high-contrast .ui-toast-acao { border-color: #ffff00; }

    /* ---------- 4. Diálogo de confirmação ---------- */
    .ui-dialogo {
        margin: auto;
        width: min(420px, calc(100vw - 32px));
        max-height: calc(100dvh - 32px);
        padding: 0;
        border: var(--card-border, 1px solid rgba(226, 232, 240, 0.8));
        border-radius: 20px;
        background: var(--card-bg, #ffffff);
        color: var(--text-principal, #0f172a);
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.35);
        overflow: auto;
    }
    .ui-dialogo[open] { animation: ui-dialogo-entrar 0.24s cubic-bezier(0.2, 0.9, 0.25, 1.1) both; }
    .ui-dialogo::backdrop { background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); }
    .ui-dialogo[open]::backdrop { animation: ui-fade-entrada 0.2s ease-out both; }
    @keyframes ui-dialogo-entrar { from { opacity: 0; transform: translateY(10px) scale(0.97); } }

    .ui-dialogo-conteudo { padding: 26px 24px 20px; display: flex; gap: 16px; align-items: flex-start; }
    .ui-dialogo-icone {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27));
    }
    .ui-dialogo--perigo .ui-dialogo-icone { background: linear-gradient(145deg, #f87171, #dc2626); }
    .ui-dialogo-icone svg { width: 22px; height: 22px; }
    .ui-dialogo-titulo { font-size: 1.0625rem; font-weight: 700; line-height: 1.3; margin: 2px 0 6px; }
    .ui-dialogo-texto { font-size: 0.875rem; line-height: 1.5; color: var(--text-secundario, #64748b); overflow-wrap: anywhere; }

    .ui-dialogo-acoes {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 24px 22px;
        flex-wrap: wrap;
    }
    .ui-dialogo-btn {
        min-height: 42px;
        padding: 0 18px;
        border-radius: 10px;
        font: inherit;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--surface-muted-border, #cbd5e1);
        background: var(--card-bg, #ffffff);
        color: var(--text-principal, #0f172a);
        transition: background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }
    .ui-dialogo-btn:hover { background: var(--surface-muted-bg, #f1f5f9); }
    .ui-dialogo-btn--confirmar { background: var(--verde-principal, #0e4a27); border-color: var(--verde-principal, #0e4a27); color: #ffffff; }
    .ui-dialogo-btn--confirmar:hover { background: var(--verde-hover, #0b3d21); }
    .ui-dialogo--perigo .ui-dialogo-btn--confirmar { background: #dc2626; border-color: #dc2626; }
    .ui-dialogo--perigo .ui-dialogo-btn--confirmar:hover { background: #b91c1c; box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3); }
    @media (max-width: 480px) {
        .ui-dialogo-acoes { flex-direction: column-reverse; }
        .ui-dialogo-btn { width: 100%; }
    }

    body.high-contrast .ui-dialogo { background: #000000; color: #ffff00; border: 2px solid #ffff00; box-shadow: none; }
    body.high-contrast .ui-dialogo-texto { color: #ffff00; }
    body.high-contrast .ui-dialogo-icone { background: #000000; border: 2px solid #ffff00; color: #ffff00; }
    body.high-contrast .ui-dialogo-btn { background: #000000; color: #ffff00; border: 2px solid #ffff00; }
    body.high-contrast .ui-dialogo-btn--confirmar { background: #ffff00; color: #000000; }

    /* ---------- 5. Skeleton (gráficos, listas carregando) ---------- */
    .ui-skeleton {
        display: block;
        border-radius: 8px;
        background: linear-gradient(90deg,
            color-mix(in srgb, var(--text-secundario, #64748b) 10%, transparent) 25%,
            color-mix(in srgb, var(--text-secundario, #64748b) 20%, transparent) 37%,
            color-mix(in srgb, var(--text-secundario, #64748b) 10%, transparent) 63%);
        background-size: 400% 100%;
        animation: ui-shimmer 1.4s ease infinite;
    }
    @keyframes ui-shimmer { from { background-position: 100% 50%; } to { background-position: 0 50%; } }

    /* Contêiner do gráfico enquanto o Chart.js ainda não desenhou. O
       shimmer só aparece se demorar (>150 ms) — em carga rápida não pisca. */
    .ui-grafico-carregando { position: relative; }
    .ui-grafico-carregando::after {
        content: "";
        position: absolute;
        inset: 6px;
        border-radius: 12px;
        pointer-events: none;
        background: linear-gradient(90deg,
            color-mix(in srgb, var(--text-secundario, #64748b) 8%, transparent) 25%,
            color-mix(in srgb, var(--text-secundario, #64748b) 16%, transparent) 37%,
            color-mix(in srgb, var(--text-secundario, #64748b) 8%, transparent) 63%);
        background-size: 400% 100%;
        opacity: 0;
        animation: ui-surgir 0.2s ease 0.15s forwards, ui-shimmer 1.4s ease infinite;
    }
    @keyframes ui-surgir { to { opacity: 1; } }
    body.high-contrast .ui-skeleton,
    body.high-contrast .ui-grafico-carregando::after { background: none; border: 2px dashed #ffff00; animation: none; opacity: 1; }

    /* ---------- 8. Microinterações padronizadas ----------
       Tudo em :where() (especificidade zero): o estilo próprio de cada tela
       continua mandando; aqui só se preenche o que faltava. "scale" (e não
       "transform") para não brigar com botões posicionados por translate. */
    :where(button, .btn, a[class*="btn"], [role="button"], input[type="submit"]) {
        transition-property: background-color, border-color, color, box-shadow, opacity, scale, transform;
        transition-duration: 0.18s;
        transition-timing-function: ease;
    }
    :where(button, .btn, a[class*="btn"], [role="button"], input[type="submit"]):where(:active:not(:disabled, [aria-disabled="true"], [role="switch"])) {
        scale: 0.97;
    }
    :where(button, input[type="submit"]):disabled { cursor: not-allowed; }
    :where(button, input[type="submit"])[aria-busy="true"],
    :where(form[aria-busy="true"]) :where(button[type="submit"], button:not([type])) { cursor: progress; }

    :where(.main) :where(table) :where(tbody tr) { transition: background-color 0.15s ease; }
    :where(.main) :where(table) :where(tbody tr):hover { background-color: color-mix(in srgb, var(--verde-secundario, #16a765) 6%, transparent); }

    /* Modais das telas (.modal/.modal-m): a caixa entra com um leve
       deslize a cada abertura (a animação reinicia quando ".active" volta).
       Funciona tanto nos modais escondidos por opacidade quanto por
       display:none; telas que já animam a própria caixa mantêm a delas. */
    /* (entrada e saída da caixa: ver partials/ui-motion) */

    /* Contador do sino: "pula" ao aparecer. */
    .floating-notifications-btn .icon-dot.show { animation: ui-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both; }
    @keyframes ui-pop { from { transform: scale(0); } }

    /* Menu lateral recolhido salvo (desktop): aplicado antes do primeiro
       desenho, sem o menu "abrir e fechar" ao trocar de página. */
    @media (min-width: 993px) {
        html.ui-sidebar-recolhida #sidebar { left: -260px; }
        html.ui-sidebar-recolhida .main { margin-left: 0; width: 100%; }
        html.ui-sidebar-recolhida .main > .header:first-child,
        html.ui-sidebar-recolhida .main > .top-bar:first-child { padding-left: 44px; }
    }

    /* ---------- Modo de leitura (painel de acessibilidade) ----------
       Espaçamentos dentro do recomendado pela WCAG 1.4.12, só no texto
       corrido do conteúdo — menus, botões e cartões de altura fixa não
       mudam (não quebram layout). */
    html.ui-modo-leitura :is(.main, main, .modal-card, .modal-m-card) :is(p, li, dd, dt, td, th, blockquote, figcaption, label) {
        line-height: 1.75 !important;
        letter-spacing: 0.02em !important;
        word-spacing: 0.08em !important;
    }
    html.ui-modo-leitura :is(.main, main) :is(p, li) { max-width: 75ch; }
    html.ui-modo-leitura :is(.main, main) :is(h1, h2, h3, h4) { line-height: 1.35 !important; letter-spacing: 0.01em; }
    html.ui-modo-leitura :is(.main, main) a:not([class]) { text-decoration: underline !important; text-underline-offset: 3px; }
    html.ui-modo-leitura :is(.main, main) :is(p, li, td) { font-weight: 500; }

    /* ---------- Reduzir animações (painel) = mesmo efeito do sistema ---------- */
    html.ui-movimento-reduzido *,
    html.ui-movimento-reduzido *::before,
    html.ui-movimento-reduzido *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        animation-delay: 0s !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
    html.ui-movimento-reduzido { scroll-behavior: auto; }
    html.ui-movimento-reduzido::view-transition-group(*),
    html.ui-movimento-reduzido::view-transition-old(*),
    html.ui-movimento-reduzido::view-transition-new(*) { animation: none !important; }
    html.ui-movimento-reduzido .ui-toast-progresso { display: none; }
    html.ui-movimento-reduzido :where(button, .btn, a[class*="btn"], [role="button"], input[type="submit"]):active { scale: none; }

    /* ---------- Movimento reduzido (opção "Reduzir Animações" do painel) ----------
       As animações vêm LIGADAS por padrão, independente da configuração do
       Windows/celular (pedido do projeto); só esta opção do painel desliga.
       (Transições de página: ver a regra html.ui-movimento-reduzido acima.) */
    html.ui-movimento-reduzido {
        .ui-toast, .ui-toast.saindo, .ui-dialogo[open], .main, body > main,
        :where(.modal.active, .modal-m.active) :where(.modal-card, .modal-m-card) { animation: none !important; }
        .ui-toast-progresso { display: none; }
        .ui-skeleton, .ui-grafico-carregando::after { animation: none !important; opacity: 1; }
        :where(button, .btn, a[class*="btn"], [role="button"], input[type="submit"]):active { scale: none; }
    }
</style>

<script>
(function () {
    'use strict';
    if (window.AgriUI) return;

    // Preferências do painel de acessibilidade que mudam o desenho da página
    // inteira: aplicadas aqui (no <head>), antes do primeiro desenho.
    var raizHtml = document.documentElement;
    try {
        if (localStorage.getItem('modoLeitura') === '1') raizHtml.classList.add('ui-modo-leitura');
        if (localStorage.getItem('movimentoReduzido') === '1') raizHtml.classList.add('ui-movimento-reduzido');
    } catch (e) { /* armazenamento bloqueado: segue o padrão */ }

    // "ui-motion" liga TODA a camada de animação (partials/ui-motion). Vem
    // ligada AUTOMATICAMENTE em qualquer aparelho — mesmo com as animações
    // do Windows desligadas; só a opção "Reduzir Animações" do painel de
    // acessibilidade desliga.
    var sincronizarMotion = function () {
        raizHtml.classList.toggle('ui-motion', !raizHtml.classList.contains('ui-movimento-reduzido'));
    };
    sincronizarMotion();

    var reduzMovimento = function () {
        return document.documentElement.classList.contains('ui-movimento-reduzido');
    };
    var armazenamento = {
        ler: function (chave, tipo) { try { return window[tipo || 'localStorage'].getItem(chave); } catch (e) { return null; } },
        gravar: function (chave, valor, tipo) { try { window[tipo || 'localStorage'].setItem(chave, valor); } catch (e) { /* bloqueado */ } },
        remover: function (chave, tipo) { try { window[tipo || 'localStorage'].removeItem(chave); } catch (e) { /* bloqueado */ } },
    };

    // Ícones em SVG próprio: funcionam mesmo em telas sem o Font Awesome.
    var SVG = {
        sucesso: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>',
        erro: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M7 7l10 10M17 7L7 17"/></svg>',
        aviso: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M12 6v8M12 18.5v.01"/></svg>',
        info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M12 11v7M12 6.5v.01"/></svg>',
        fechar: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>',
        lixeira: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 002 2h6a2 2 0 002-2l1-12M9 7V4h6v3"/></svg>',
        pergunta: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.1 9a3 3 0 015.8 1c0 2-3 2.5-3 4.5M12 18v.01"/></svg>',
    };

    // ==================================================================
    // 3. TOASTS
    // ==================================================================
    var pilhaToasts = null;
    function containerToasts() {
        if (pilhaToasts && document.body.contains(pilhaToasts)) return pilhaToasts;
        pilhaToasts = document.createElement('div');
        pilhaToasts.className = 'ui-toasts';
        pilhaToasts.setAttribute('aria-live', 'polite');
        pilhaToasts.setAttribute('aria-relevant', 'additions');
        pilhaToasts.setAttribute('role', 'region');
        pilhaToasts.setAttribute('aria-label', 'Notificações');
        document.body.appendChild(pilhaToasts);
        return pilhaToasts;
    }

    var TITULOS = { sucesso: 'Tudo certo', erro: 'Algo deu errado', aviso: 'Atenção', info: 'Informação' };

    function toast(mensagem, opcoes) {
        opcoes = opcoes || {};
        var tipo = SVG[opcoes.tipo] && opcoes.tipo !== 'fechar' ? opcoes.tipo : 'sucesso';
        var duracao = opcoes.duracao != null ? opcoes.duracao : (tipo === 'erro' ? 8000 : 5000);
        var raiz = containerToasts();

        // Não empilha a mesma mensagem repetida (ex.: clique duplo).
        var repetido = Array.prototype.find.call(raiz.children, function (t) { return t.dataset.mensagem === String(mensagem) && !t.classList.contains('saindo'); });
        if (repetido) return repetido;

        var el = document.createElement('div');
        el.className = 'ui-toast ui-toast--' + tipo;
        el.dataset.mensagem = String(mensagem);
        if (tipo === 'erro') el.setAttribute('role', 'alert');

        var icone = document.createElement('span');
        icone.className = 'ui-toast-icone';
        icone.innerHTML = SVG[tipo];

        var corpo = document.createElement('div');
        corpo.className = 'ui-toast-corpo';
        if (opcoes.titulo) {
            var titulo = document.createElement('strong');
            titulo.className = 'ui-toast-titulo';
            titulo.textContent = opcoes.titulo;
            corpo.appendChild(titulo);
        }
        var texto = document.createElement('p');
        texto.className = 'ui-toast-texto';
        texto.textContent = String(mensagem);
        corpo.appendChild(texto);

        if (opcoes.acao && opcoes.acao.rotulo) {
            var acao = document.createElement(opcoes.acao.href ? 'a' : 'button');
            acao.className = 'ui-toast-acao';
            acao.textContent = opcoes.acao.rotulo;
            if (opcoes.acao.href) acao.href = opcoes.acao.href;
            else acao.type = 'button';
            acao.addEventListener('click', function (e) {
                if (typeof opcoes.acao.aoClicar === 'function') opcoes.acao.aoClicar(e);
                fechar();
            });
            corpo.appendChild(acao);
        }

        var botaoFechar = document.createElement('button');
        botaoFechar.type = 'button';
        botaoFechar.className = 'ui-toast-fechar';
        botaoFechar.setAttribute('aria-label', 'Fechar notificação');
        botaoFechar.innerHTML = SVG.fechar;

        el.appendChild(icone);
        el.appendChild(corpo);
        el.appendChild(botaoFechar);

        var timer = null;
        var fechado = false;
        function fechar() {
            if (fechado) return;
            fechado = true;
            clearTimeout(timer);
            if (reduzMovimento()) { el.remove(); return; }
            el.classList.add('saindo');
            // Só a animação de saída do próprio toast (o "animationend" da
            // barra de tempo também sobe até aqui).
            el.addEventListener('animationend', function (ev) { if (ev.animationName === 'ui-toast-sair') el.remove(); });
            setTimeout(function () { el.remove(); }, 400);
        }
        botaoFechar.addEventListener('click', fechar);

        if (duracao > 0) {
            var barra = document.createElement('span');
            barra.className = 'ui-toast-progresso';
            barra.style.animationDuration = duracao + 'ms';
            el.appendChild(barra);
            // Fecha quando a barra termina: pausa junto com ela no hover/foco
            // (tempo suficiente para ler — WCAG 2.2.1).
            barra.addEventListener('animationend', fechar);
            if (reduzMovimento()) timer = setTimeout(fechar, duracao);
        }

        // Máximo de 4 visíveis: o mais antigo sai.
        while (raiz.children.length >= 4) raiz.firstElementChild.remove();
        raiz.appendChild(el);
        return el;
    }

    // ==================================================================
    // 4. DIÁLOGO DE CONFIRMAÇÃO (substitui o confirm() do navegador)
    // ==================================================================
    function confirmar(mensagem, opcoes) {
        opcoes = opcoes || {};
        var perigo = opcoes.perigo != null ? opcoes.perigo : /exclu|remov|apag|delet/i.test(String(mensagem) + (opcoes.confirmar || ''));

        if (typeof HTMLDialogElement !== 'function') {
            return Promise.resolve(window.confirm(mensagem));
        }

        return new Promise(function (resolver) {
            var origem = document.activeElement;
            var dlg = document.createElement('dialog');
            dlg.className = 'ui-dialogo' + (perigo ? ' ui-dialogo--perigo' : '');
            var idTitulo = 'uiDialogoTitulo' + Date.now();
            var idTexto = idTitulo + 'Texto';
            dlg.setAttribute('aria-labelledby', idTitulo);
            dlg.setAttribute('aria-describedby', idTexto);
            if (perigo) dlg.setAttribute('role', 'alertdialog');

            dlg.innerHTML = '<div class="ui-dialogo-conteudo">'
                + '<span class="ui-dialogo-icone">' + (perigo ? SVG.lixeira : SVG.pergunta) + '</span>'
                + '<div><h2 class="ui-dialogo-titulo" id="' + idTitulo + '"></h2><p class="ui-dialogo-texto" id="' + idTexto + '"></p></div>'
                + '</div>'
                + '<div class="ui-dialogo-acoes">'
                + '<button type="button" class="ui-dialogo-btn" data-resposta="nao"></button>'
                + '<button type="button" class="ui-dialogo-btn ui-dialogo-btn--confirmar" data-resposta="sim"></button>'
                + '</div>';
            dlg.querySelector('.ui-dialogo-titulo').textContent = opcoes.titulo || (perigo ? 'Confirmar exclusão' : 'Confirmar ação');
            dlg.querySelector('.ui-dialogo-texto').textContent = String(mensagem);
            dlg.querySelector('[data-resposta="nao"]').textContent = opcoes.cancelar || 'Cancelar';
            dlg.querySelector('[data-resposta="sim"]').textContent = opcoes.confirmar || (perigo ? 'Sim, excluir' : 'Confirmar');

            var respondido = false;
            function responder(valor) {
                if (respondido) return;
                respondido = true;
                if (dlg.open) dlg.close();
                dlg.remove();
                if (origem && typeof origem.focus === 'function' && document.contains(origem)) origem.focus({ preventScroll: true });
                if (!valor && opcoes.avisarCancelamento !== false) toast('Operação cancelada.', { tipo: 'info', duracao: 2500 });
                resolver(valor);
            }

            dlg.addEventListener('click', function (e) {
                var botao = e.target.closest('[data-resposta]');
                if (botao) responder(botao.dataset.resposta === 'sim');
                else if (e.target === dlg) responder(false); // clique no fundo
            });
            dlg.addEventListener('cancel', function (e) { e.preventDefault(); responder(false); }); // Esc

            document.body.appendChild(dlg);
            dlg.showModal();
            // Ação destrutiva: o foco começa no "Cancelar" (mais seguro).
            dlg.querySelector(perigo ? '[data-resposta="nao"]' : '[data-resposta="sim"]').focus();
        });
    }

    // <form data-confirmar="Mensagem"> — confirma antes de enviar. Captura
    // (roda antes da trava de envio duplicado do layout); depois de "sim"
    // reenvia com o mesmo botão (name/value preservados).
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirmar')) return;
        if (form.dataset.uiConfirmado === '1') { delete form.dataset.uiConfirmado; return; }
        e.preventDefault();
        e.stopImmediatePropagation();
        var botao = e.submitter || null;
        confirmar(form.getAttribute('data-confirmar'), {
            titulo: form.getAttribute('data-confirmar-titulo') || undefined,
            confirmar: form.getAttribute('data-confirmar-botao') || undefined,
        }).then(function (ok) {
            if (!ok) return;
            form.dataset.uiConfirmado = '1';
            if (typeof form.requestSubmit === 'function') form.requestSubmit(botao && botao.form === form ? botao : undefined);
            else { delete form.dataset.uiConfirmado; form.submit(); }
        });
    }, true);

    // ==================================================================
    // 2. BARRA DE PROGRESSO DA NAVEGAÇÃO
    // ==================================================================
    var barra = null, barraTimer = null, barraLimite = null;
    function iniciarBarra(texto) {
        if (!document.body) return;
        if (!barra) {
            barra = document.createElement('div');
            barra.className = 'ui-barra-navegacao';
            barra.setAttribute('aria-hidden', 'true');
            document.body.appendChild(barra);
        }
        clearTimeout(barraTimer);
        clearTimeout(barraLimite);
        // Resposta imediata ao clique; avança em etapas desacelerando
        // (nunca chega a 100% sozinha — quem termina é a página nova).
        barra.classList.remove('api');
        barra.classList.add('ativa');
        barra.style.transform = 'scaleX(0.18)';
        barraTimer = setTimeout(function () {
            barra.style.transform = 'scaleX(0.42)';
            setTimeout(function () { if (barra.classList.contains('ativa')) barra.style.transform = 'scaleX(0.72)'; }, 500);
            setTimeout(function () { if (barra.classList.contains('ativa')) barra.style.transform = 'scaleX(0.88)'; }, 1700);
        }, 60);
        // Download/arquivo não troca de página: some sozinha.
        barraLimite = setTimeout(pararBarra, 10000);
        agendarVaca(texto);
    }
    function pararBarra() {
        clearTimeout(barraTimer);
        clearTimeout(barraLimite);
        esconderVaca();
        if (!barra) return;
        barra.classList.remove('ativa');
        barra.style.transform = '';
    }

    // ==================================================================
    // 2b. VAQUINHA DE CARREGAMENTO entre uma página e outra
    // Aparece se a próxima tela levar mais que ~150 ms (navegação instantânea
    // não "pisca"); some sozinha quando a página nova é desenhada.
    // ==================================================================
    // Vaquinha comendo ração no cocho (desenho próprio, SVG). Camadas: cocho
    // e ração atrás, cabeça (que abaixa/levanta), frente do cocho por cima
    // do focinho — assim ele "entra" na ração ao comer.
    var VACA_SVG = '<svg class="ui-vaca" viewBox="0 0 230 150" aria-hidden="true">'
        + '<ellipse class="vc-sombra" cx="115" cy="135" rx="102" ry="6"/>'
        + '<path class="vc-capim" d="M6 135c1-6 3-10 6-12-1 5-1 9 0 12zM13 135c0-7 2-11 5-13-2 5-2 9-1 13zM221 135c1-5 2-8 5-10-1 4-1 7 0 10z"/>'
        // pernas do lado de lá (um pouco mais escuras = profundidade)
        + '<rect class="vc-pele" x="48" y="86" width="11" height="42" rx="5" style="fill:#e6ece7"/><rect class="vc-casco" x="47" y="124" width="13" height="9" rx="2.5"/>'
        + '<rect class="vc-pele" x="114" y="86" width="11" height="42" rx="5" style="fill:#e6ece7"/><rect class="vc-casco" x="113" y="124" width="13" height="9" rx="2.5"/>'
        // rabo
        + '<g class="vc-rabo-grupo"><path class="vc-rabo" d="M28 58C16 66 14 84 18 100"/><ellipse class="vc-tufo" cx="18" cy="103" rx="4.5" ry="6.5"/></g>'
        // corpo
        + '<g class="vc-corpo">'
        + '<rect class="vc-pele" x="24" y="44" width="124" height="54" rx="27"/>'
        + '<path class="vc-mancha" d="M48 46C62 44 72 54 66 64C60 73 44 70 40 61C37 54 41 47 48 46Z"/>'
        + '<path class="vc-mancha" d="M96 70C108 66 120 72 117 82C114 91 100 92 94 85C89 79 90 72 96 70Z"/>'
        + '<path class="vc-mancha" d="M120 47C128 46 134 51 131 57C128 62 120 61 117 56C115 51 116 48 120 47Z"/>'
        + '<path class="vc-mancha" d="M34 78c4-1 8 3 6 7-2 3-7 3-8 0-1-3 0-6 2-7z"/>'
        + '<ellipse class="vc-rosa" cx="86" cy="99" rx="11" ry="6"/>'
        + '</g>'
        // pernas do lado de cá
        + '<rect class="vc-pele" x="64" y="88" width="11" height="40" rx="5"/><rect class="vc-casco" x="63" y="124" width="13" height="9" rx="2.5"/>'
        + '<rect class="vc-pele" x="129" y="88" width="11" height="40" rx="5"/><rect class="vc-casco" x="128" y="124" width="13" height="9" rx="2.5"/>'
        // ração no cocho (parte de trás)
        + '<path class="vc-racao" d="M162 110Q189 92 214 110Z"/>'
        + '<circle class="vc-grao-a" cx="174" cy="106" r="2.4"/><circle class="vc-grao-b" cx="182" cy="102" r="2.4"/><circle class="vc-grao-a" cx="191" cy="100" r="2.4"/>'
        + '<circle class="vc-grao-b" cx="200" cy="102" r="2.4"/><circle class="vc-grao-a" cx="207" cy="106" r="2.2"/><circle class="vc-grao-b" cx="188" cy="106" r="2.2"/><circle class="vc-grao-a" cx="197" cy="107" r="2.2"/>'
        // cabeça (abaixa para comer, levanta mastigando)
        + '<g class="vc-cabeca">'
        + '<path d="M118 46C140 42 158 56 165 76L151 89C145 76 136 72 118 74Z" style="fill:#ffffff"/>'
        + '<path class="vc-rabo" style="stroke-width:2.4" d="M134 46C148 46 159 59 165 76M151 89C145 77 138 73 132 73"/>'
        + '<g class="vc-orelha"><ellipse class="vc-pele" cx="152" cy="73" rx="9.5" ry="4.6" transform="rotate(-18 152 73)"/><ellipse class="vc-rosa-interno" cx="153" cy="73" rx="5.5" ry="2.1" transform="rotate(-18 153 73)"/></g>'
        + '<path class="vc-chifre" d="M157 64c-2-6 0-11 4-12-1 4 0 8 2 10z"/>'
        + '<ellipse class="vc-pele" cx="164" cy="86" rx="14" ry="19" transform="rotate(-32 164 86)"/>'
        + '<path class="vc-mancha" d="M156 73c5-3 10 0 9 5-1 4-6 5-9 2z"/>'
        + '<g class="vc-olho-grupo"><circle class="vc-olho" cx="167" cy="82" r="2.8"/><circle class="vc-brilho" cx="168" cy="81" r="0.9"/></g>'
        + '<g class="vc-focinho"><ellipse class="vc-rosa" cx="175" cy="102" rx="11" ry="8" transform="rotate(-32 175 102)"/><circle class="vc-narina" cx="174" cy="99" r="1.5"/><circle class="vc-narina" cx="180" cy="103" r="1.5"/></g>'
        + '</g>'
        // frente do cocho (por cima do focinho)
        + '<path class="vc-cocho" d="M158 108L218 108L211 133L165 133Z"/>'
        + '<path class="vc-cocho-tabua" d="M161 117L215 117M163 125L213 125"/>'
        // grãos que pulam quando ela levanta a cabeça
        + '<g class="vc-graos"><circle class="vc-grao-b" cx="182" cy="103" r="2.2"/><circle class="vc-grao-a" cx="189" cy="101" r="2"/><circle class="vc-grao-b" cx="177" cy="102" r="1.8"/></g>'
        + '</svg>';

    var vaca = null, vacaTimer = null;
    function criarVaca() {
        if (vaca || !document.body) return vaca;
        vaca = document.createElement('div');
        vaca.className = 'ui-vaca-overlay';
        vaca.setAttribute('role', 'status');
        vaca.setAttribute('aria-live', 'polite');
        vaca.innerHTML = '<div class="ui-vaca-cartao">' + VACA_SVG
            + '<div class="ui-vaca-barra" aria-hidden="true"><span></span></div>'
            + '<p class="ui-vaca-texto"><span class="ui-vaca-rotulo">Carregando</span><span class="ui-vaca-pontos" aria-hidden="true"><span>.</span><span>.</span><span>.</span></span></p>'
            + '<p class="ui-vaca-sub">A vaquinha está comendo enquanto seus dados chegam</p></div>';
        document.body.appendChild(vaca);
        return vaca;
    }
    // Aparece no mesmo instante do clique. Se a página nova chegar rápido,
    // a vaquinha não some de repente: a View Transition a leva junto e ela
    // desaparece suavemente POR CIMA da tela nova (que já fica utilizável
    // por baixo — nenhum atraso artificial nos dados).
    function agendarVaca(texto) {
        clearTimeout(vacaTimer);
        if (!criarVaca()) return;
        var salvando = texto === 'Salvando';
        vaca.querySelector('.ui-vaca-rotulo').textContent = salvando ? 'Salvando' : 'Carregando';
        vaca.querySelector('.ui-vaca-sub').textContent = salvando
            ? 'A vaquinha está guardando suas alterações no cocho'
            : 'A vaquinha está comendo enquanto seus dados chegam';
        vaca.classList.add('ativa');
    }
    function esconderVaca() {
        clearTimeout(vacaTimer);
        if (vaca) vaca.classList.remove('ativa');
    }

    document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        var link = e.target.closest && e.target.closest('a[href]');
        if (!link || link.target && link.target !== '_self' || link.hasAttribute('download')) return;
        var url;
        try { url = new URL(link.href, location.href); } catch (err) { return; }
        if (url.origin !== location.origin) return;
        if (url.pathname === location.pathname && url.search === location.search && url.hash) return; // âncora
        if (/\.(pdf|csv|xlsx?|zip)$|\/(pdf|exportar|download)/i.test(url.pathname)) return;
        setTimeout(function () {
            if (e.defaultPrevented) return;
            iniciarBarra();
            link.classList.add('ui-navegando'); // estado "pressionado" até a troca
        }, 0);
    });
    // Navegações por script (ex.: trocar a fazenda no seletor muda
    // location.href): a Navigation API avisa — onde existir.
    if (window.navigation && typeof window.navigation.addEventListener === 'function') {
        window.navigation.addEventListener('navigate', function (e) {
            try {
                if (e.hashChange || e.downloadRequest || (e.destination && e.destination.sameDocument)) return;
                if (e.navigationType === 'traverse' || e.navigationType === 'reload') return;
                if (new URL(e.destination.url).origin !== location.origin) return;
                iniciarBarra(e.formData ? 'Salvando' : null);
            } catch (err) { /* segue sem barra */ }
        });
    }
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('.ui-navegando').forEach(function (el) { el.classList.remove('ui-navegando'); });
    });
    document.addEventListener('submit', function (e) {
        var form = e.target;
        setTimeout(function () {
            if (e.defaultPrevented || !(form instanceof HTMLFormElement) || form.target === '_blank') return;
            iniciarBarra((form.getAttribute('method') || 'get').toLowerCase() === 'post' ? 'Salvando' : null);
        }, 0);
    });
    window.addEventListener('pageshow', pararBarra);
    window.addEventListener('pagehide', function () { clearTimeout(barraLimite); });

    // ==================================================================
    // 5. GRÁFICOS: skeleton enquanto montam + animação padrão
    // ==================================================================
    // ------------------------------------------------------------------
    // TEMA DOS GRÁFICOS (Chart.js 4) — acabamento moderno e consistente.
    // Só preenche o que cada tela NÃO definiu: as cores continuam vindo do
    // tema da própria tela (claro/escuro/alto contraste) e os dados do banco.
    // ------------------------------------------------------------------
    function aplicarTemaGraficos(C) {
        var d = C.defaults;
        var cssVar = function (nome, padrao) {
            var v = getComputedStyle(document.body).getPropertyValue(nome).trim();
            return v || padrao;
        };
        var altoContraste = function () { return document.body.classList.contains('high-contrast'); };

        // Números no padrão brasileiro em dicas e eixos (antes "1,234.5").
        d.locale = 'pt-BR';
        d.font.family = "'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif";
        d.font.size = 12;

        // Dicas (tooltips): cartão arredondado, respiro e marcador redondo.
        var tt = d.plugins.tooltip;
        tt.padding = { top: 10, right: 14, bottom: 10, left: 12 };
        tt.cornerRadius = 12;
        tt.caretSize = 6;
        tt.caretPadding = 8;
        tt.boxPadding = 6;
        tt.usePointStyle = true;
        tt.titleFont = { weight: '700', size: 13 };
        tt.bodyFont = { weight: '500', size: 12 };
        tt.titleMarginBottom = 6;
        tt.animation = { duration: 160, easing: 'easeOutQuart' };

        // Legendas com bolinhas.
        var lg = d.plugins.legend.labels;
        lg.usePointStyle = true;
        lg.pointStyle = 'circle';
        lg.boxWidth = 8;
        lg.boxHeight = 8;
        lg.padding = 16;
        lg.font = { weight: '600', size: 12 };

        // Eixos: grade tracejada e discreta, sem a moldura do eixo.
        d.scale.border.display = false;
        d.scale.border.dash = [3, 4];
        d.scale.grid.drawTicks = false;
        d.scale.ticks.padding = 8;

        // Barras: topo arredondado e largura máxima (sem "tijolões").
        d.elements.bar.borderRadius = 8;
        d.elements.bar.borderSkipped = 'start';
        d.datasets.bar.maxBarThickness = 44;
        d.datasets.bar.categoryPercentage = 0.72;
        d.datasets.bar.barPercentage = 0.85;

        // Linhas: curva suave, traço arredondado, pontos que crescem no hover.
        d.elements.line.tension = 0.38;
        // "monotone": curva suave que NUNCA passa dos valores reais (a curva
        // comum criava vales/picos falsos entre dois pontos).
        d.datasets.line.cubicInterpolationMode = 'monotone';
        d.elements.line.borderWidth = 2.5;
        d.elements.line.borderCapStyle = 'round';
        d.elements.line.borderJoinStyle = 'round';
        d.elements.point.radius = 3;
        d.elements.point.hoverRadius = 6;
        d.elements.point.hitRadius = 14;
        d.elements.point.borderWidth = 2;
        d.elements.point.hoverBorderWidth = 3;

        // Roscas/pizzas: fatias separadas pela cor do cartão + destaque no hover.
        d.elements.arc.borderWidth = 3;
        d.elements.arc.borderColor = function () { return altoContraste() ? '#000000' : cssVar('--card-bg', '#ffffff'); };
        d.elements.arc.borderRadius = 6;
        d.elements.arc.hoverOffset = 8;
        if (C.overrides && C.overrides.doughnut) C.overrides.doughnut.cutout = '70%';

        var cor = C.helpers && C.helpers.color;

        C.register({
            id: 'uiTemaGraficos',
            // Linhas com preenchimento: degradê vertical da própria cor da
            // linha (forte em cima, sumindo embaixo) no lugar da cor chapada.
            beforeInit: function (chart) {
                if (!cor || altoContraste()) return;
                (chart.data.datasets || []).forEach(function (ds) {
                    var tipo = ds.type || chart.config.type;
                    if (tipo !== 'line' || !ds.fill || typeof ds.borderColor !== 'string' || ds.__uiDegrade) return;
                    var base;
                    try { base = cor(ds.borderColor); } catch (e) { return; }
                    if (!base || !base.valid) return;
                    var original = ds.backgroundColor;
                    ds.__uiDegrade = true;
                    ds.backgroundColor = function (ctx) {
                        var area = ctx.chart.chartArea;
                        if (!area) return original;
                        var g = ctx.chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
                        g.addColorStop(0, cor(ds.borderColor).alpha(0.28).rgbString());
                        g.addColorStop(0.65, cor(ds.borderColor).alpha(0.06).rgbString());
                        g.addColorStop(1, cor(ds.borderColor).alpha(0).rgbString());
                        return g;
                    };
                });
            },
            // Leve sombra colorida sob as linhas (profundidade sem peso).
            beforeDatasetDraw: function (chart, args) {
                var meta = args.meta;
                if (meta.type !== 'line' || altoContraste() || !cor) return;
                var c = chart.ctx;
                c.save();
                try {
                    c.shadowColor = cor(meta.dataset && meta.dataset.options && meta.dataset.options.borderColor || '#16a765').alpha(0.35).rgbString();
                } catch (e) { c.shadowColor = 'rgba(22, 167, 101, 0.3)'; }
                c.shadowBlur = 10;
                c.shadowOffsetY = 5;
            },
            afterDatasetDraw: function (chart, args) {
                if (args.meta.type !== 'line' || altoContraste() || !cor) return;
                chart.ctx.restore();
            },
        });
    }

    function prepararGraficos() {
        var C = window.Chart;
        var canvases = document.querySelectorAll('.main canvas, main canvas');
        if (!canvases.length) return;

        if (C && !C.__uiCore) {
            C.__uiCore = true;
            try {
                // Animação completa (crescendo em sequência) só na PRIMEIRA
                // visita a esta tela na sessão; depois — voltar à tela, trocar
                // filtro, mudar o tema — uma transição curta, sem repetir o
                // "show" toda hora.
                var chaveVista = 'agrifarm:graficos-vistos:' + location.pathname;
                var primeiraVez = armazenamento.ler(chaveVista, 'sessionStorage') !== '1';
                armazenamento.gravar(chaveVista, '1', 'sessionStorage');

                if (reduzMovimento()) {
                    C.defaults.animation = false;
                } else if (C.defaults.animation) {
                    C.defaults.animation.duration = primeiraVez ? 900 : 380;
                    C.defaults.animation.easing = 'easeOutQuart';
                    if (primeiraVez) {
                        // Barras/pontos surgem em cascata (40 ms entre si).
                        C.defaults.animation.delay = function (ctx) {
                            if (ctx.type !== 'data' || ctx.mode !== 'default') return 0;
                            // Só no primeiro desenho de cada gráfico.
                            var grafico = ctx.chart;
                            if (!grafico.$uiInicio) grafico.$uiInicio = Date.now();
                            if (Date.now() - grafico.$uiInicio > 1200) return 0;
                            return (ctx.dataIndex * 40) + (ctx.datasetIndex * 90) + 120;
                        };
                    }
                    if (C.defaults.transitions && C.defaults.transitions.active) {
                        C.defaults.transitions.active.animation.duration = 220; // hover nas barras
                    }
                }
                aplicarTemaGraficos(C);
                C.register({
                    id: 'uiSkeleton',
                    afterRender: function (chart) {
                        var pai = chart.canvas && chart.canvas.parentElement;
                        if (pai) pai.classList.remove('ui-grafico-carregando');
                    },
                });
            } catch (err) { /* versão antiga do Chart.js: segue sem */ }
        }

        Array.prototype.forEach.call(canvases, function (cv) {
            var pai = cv.parentElement;
            var jaDesenhado = C && C.getChart && C.getChart(cv);
            if (pai && !jaDesenhado) pai.classList.add('ui-grafico-carregando');
        });
        // Gráfico que a tela decidiu não montar (sem dados → estado vazio).
        window.addEventListener('load', function () {
            setTimeout(function () {
                document.querySelectorAll('.ui-grafico-carregando').forEach(function (el) { el.classList.remove('ui-grafico-carregando'); });
            }, 600);
        });
    }

    // ==================================================================
    // 6. CONTAGEM ANIMADA NOS INDICADORES
    // ==================================================================
    var SEL_INDICADORES = '.premium-card-value, .stat-card-valor, .pg-card-value, .metric-value, .stat-pill-valor, [data-contador]';
    var NUMERO_PT = /-?\d{1,3}(?:\.\d{3})+(?:,\d+)?|-?\d+(?:,\d+)?/;

    function animarIndicador(el) {
        if (el.children.length || el.dataset.uiContado) return;
        el.dataset.uiContado = '1';
        var original = el.textContent;
        if (/[\/:]/.test(original)) return; // datas e horários ficam como estão
        var achado = original.match(NUMERO_PT);
        if (!achado) return;
        var bruto = achado[0];
        var decimais = (bruto.split(',')[1] || '').length;
        var alvo = parseFloat(bruto.replace(/\./g, '').replace(',', '.'));
        if (!isFinite(alvo) || Math.abs(alvo) < 2) return;
        var agrupar = /\d\.\d{3}/.test(bruto) || Math.abs(alvo) >= 10000;
        var fmt = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: decimais, maximumFractionDigits: decimais, useGrouping: agrupar });
        var antes = original.slice(0, achado.index);
        var depois = original.slice(achado.index + bruto.length);
        var duracao = 1100;
        var inicio = null;
        var ultimoEscrito = null;

        // Começa junto com a entrada em cascata do próprio cartão (o atraso
        // dela vem do CSS de ui-motion), para número e cartão chegarem juntos.
        var cartao = el.closest('.premium-card, .stat-card, .pg-card, .stat-pill, .metric-row, .dashboard-card');
        var atraso = 0;
        if (cartao) {
            var d = getComputedStyle(cartao).animationDelay;
            atraso = (parseFloat(d) || 0) * (/ms$/.test(d) ? 1 : 1000);
        }

        function quadro(t) {
            // A tela atualizou o valor por conta própria: para aqui.
            if (ultimoEscrito !== null && el.textContent !== ultimoEscrito) { delete el.dataset.uiContando; return; }
            if (inicio === null) inicio = t;
            var p = Math.min(1, (t - inicio) / duracao);
            var suave = p === 1 ? 1 : 1 - Math.pow(2, -10 * p); // expo-out: rápido no início, pousa suave
            ultimoEscrito = p < 1 ? antes + fmt.format(alvo * suave) + depois : original;
            el.textContent = ultimoEscrito;
            if (p < 1) requestAnimationFrame(quadro);
            else delete el.dataset.uiContando;
        }
        el.dataset.uiContando = '1';
        el.textContent = antes + fmt.format(0) + depois;
        ultimoEscrito = el.textContent;
        setTimeout(function () { requestAnimationFrame(quadro); }, Math.min(atraso, 600));
    }

    function prepararIndicadores() {
        if (reduzMovimento()) return;
        var itens = document.querySelectorAll(SEL_INDICADORES);
        if (!itens.length) return;
        if (!('IntersectionObserver' in window)) { itens.forEach(animarIndicador); return; }
        var obs = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (en) {
                if (en.isIntersecting) { obs.unobserve(en.target); animarIndicador(en.target); }
            });
        }, { threshold: 0.4 });
        itens.forEach(function (el) { obs.observe(el); });
    }

    // ==================================================================
    // Mensagens do servidor viram toast (telas internas)
    // ==================================================================
    function converterMensagensDoServidor() {
        if (!document.body.hasAttribute('data-mobile-shell')) return;
        document.querySelectorAll('.alerta-flash.alerta-sucesso').forEach(function (msg) {
            var texto = msg.textContent.replace(/\s+/g, ' ').trim();
            if (!texto) return;
            msg.hidden = true;
            // Um tique depois: a região "aria-live" precisa existir antes de
            // receber o texto, senão o leitor de tela não anuncia.
            containerToasts();
            setTimeout(function () { toast(texto, { tipo: 'sucesso', titulo: 'Tudo certo' }); }, 60);
        });
    }

    // ==================================================================
    // 7. FILTROS LEMBRADOS AO VOLTAR PELO MENU
    // Guarda (nesta aba) os filtros de busca da última visita a cada tela e
    // faz o link do menu apontar para eles. A fazenda NÃO entra: ela já é
    // lembrada no servidor e trocar por aqui sobrescreveria a escolha feita
    // em outra tela.
    // ==================================================================
    var IGNORAR_PARAMS = ['fazenda_id', 'page', '_token'];
    var CHAVE_FILTROS = 'agrifarm:filtros:';
    var CHAVE_RESTAURADO = 'agrifarm:filtros-restaurados';

    function lembrarFiltros() {
        var main = document.querySelector('.main');
        if (!main) return;
        var nomes = {};
        main.querySelectorAll('form').forEach(function (f) {
            if ((f.getAttribute('method') || 'get').toLowerCase() !== 'get') return;
            Array.prototype.forEach.call(f.elements, function (campo) { if (campo.name) nomes[campo.name] = true; });
        });
        if (!Object.keys(nomes).length) return;

        var atuais = new URLSearchParams(location.search);
        var guardar = new URLSearchParams();
        atuais.forEach(function (valor, nome) {
            if (nomes[nome] && IGNORAR_PARAMS.indexOf(nome) === -1 && valor !== '') guardar.append(nome, valor);
        });
        var chave = CHAVE_FILTROS + location.pathname;
        var consulta = guardar.toString();
        if (consulta) armazenamento.gravar(chave, consulta, 'sessionStorage');
        else armazenamento.remover(chave, 'sessionStorage');

        // Chegou por um link do menu com filtros restaurados: avisa e
        // oferece limpar com um toque (transparente, nada "escondido").
        if (consulta && armazenamento.ler(CHAVE_RESTAURADO, 'sessionStorage') === location.pathname) {
            setTimeout(function () {
                toast('Mantivemos os filtros da sua última visita a esta tela.', {
                    tipo: 'info',
                    duracao: 6000,
                    acao: { rotulo: 'Limpar filtros', href: location.pathname },
                });
            }, 250);
        }
        armazenamento.remover(CHAVE_RESTAURADO, 'sessionStorage');
    }

    function aplicarFiltrosNosLinks() {
        document.querySelectorAll('#sidebar .menu a[href], .mobile-bottom-nav a[href]').forEach(function (link) {
            var url;
            try { url = new URL(link.href, location.href); } catch (e) { return; }
            if (url.origin !== location.origin || url.search) return;
            var consulta = armazenamento.ler(CHAVE_FILTROS + url.pathname, 'sessionStorage');
            if (!consulta) return;
            link.href = url.pathname + '?' + consulta;
            link.addEventListener('click', function () { armazenamento.gravar(CHAVE_RESTAURADO, url.pathname, 'sessionStorage'); });
        });
    }

    // ==================================================================
    // Conexão: avisa quando cai e quando volta
    // ==================================================================
    window.addEventListener('offline', function () {
        toast('Sem conexão com a internet. As alterações só serão salvas quando a conexão voltar.', { tipo: 'aviso', titulo: 'Você está offline', duracao: 0 });
    });
    window.addEventListener('online', function () {
        document.querySelectorAll('.ui-toast--aviso .ui-toast-fechar').forEach(function (b) { b.click(); });
        toast('Conexão restabelecida.', { tipo: 'sucesso', duracao: 3000 });
    });

    function iniciar() {
        try { prepararGraficos(); } catch (e) { /* não bloqueia a página */ }
        try { converterMensagensDoServidor(); } catch (e) { /* idem */ }
        try { lembrarFiltros(); aplicarFiltrosNosLinks(); } catch (e) { /* idem */ }
        try { prepararIndicadores(); } catch (e) { /* idem */ }
    }

    window.AgriUI = {
        toast: toast,
        confirmar: confirmar,
        iniciarCarregamento: iniciarBarra,
        pararCarregamento: pararBarra,
        // Vaquinha de carregamento sob demanda (ex.: operação demorada).
        mostrarVaca: function () { if (criarVaca()) vaca.classList.add('ativa'); },
        esconderVaca: esconderVaca,
        reduzMovimento: reduzMovimento,
        sincronizarMovimento: sincronizarMotion,
        animarIndicador: animarIndicador,
        // Utilitário para buscas/filtros que disparam a cada tecla.
        debounce: function (fn, espera) {
            var t = null;
            return function () {
                var ctx = this, args = arguments;
                clearTimeout(t);
                t = setTimeout(function () { fn.apply(ctx, args); }, espera || 250);
            };
        },
    };

    // Primeiro ouvinte do DOMContentLoaded (este arquivo vem antes dos
    // scripts das telas): o plugin/defaults do Chart.js valem para os
    // gráficos que as telas montam logo em seguida.
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
    else iniciar();
})();
</script>
@include('partials.ui-motion')
@endonce
