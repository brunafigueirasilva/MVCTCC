{{-- ==========================================================================
     COMPONENTES "pg-*" — usados por Gestão de Cochos, Tipos de Rações e
     Nível de Estoque. Os cards seguem a mesma linguagem visual dos cards do
     Dashboard (premium-card): degradê suave da cor da categoria, selo 3D
     redondo com glow, rótulo colorido, valor grande e legenda. Cada página
     acrescenta seu próprio elemento visual (medidor, barra de situação).
     Incluído por página dentro de @push('styles').
     ========================================================================== --}}
<style>
    [data-pg] {
        --pg-verde-texto: var(--verde-texto, #0e4a27);
        --pg-azul-texto: #0369a1;
        --pg-laranja-texto: #c2410c;
        --pg-roxo-texto: #6b21a8;
    }

    [data-pg="cochos"]  { --pg-accent: #2563eb; }
    [data-pg="racoes"]  { --pg-accent: var(--verde-principal, #0e4a27); }
    [data-pg="estoque"] { --pg-accent: #ea580c; }

    .sr-only {
        position: absolute !important;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* ---------- Topo (título + ações) ----------
       Mesmo respiro para os botões flutuantes (☰ / 🔔 / ♿) que o layout
       já aplica em ".header:first-child". */
    .pg-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px 28px;
        flex-wrap: wrap;
        margin-bottom: 26px;
    }

    .main > .pg-top:first-child { padding-right: 132px; }
    .main.expanded > .pg-top:first-child { padding-left: 44px; }

    @media (max-width: 767px) {
        body[data-mobile-shell] .main > .pg-top:first-child,
        body[data-mobile-shell] .main.expanded > .pg-top:first-child { padding: 0; }
    }

    .pg-top-text { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
    .pg-top-actions { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }

    /* Título no mesmo padrão do Dashboard (ícone verde + texto). */
    .pg-title {
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.5px;
        line-height: 1.2;
        color: var(--text-principal);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pg-title > i { color: var(--verde-texto, #0e4a27); font-size: 1.375rem; }

    .pg-sub {
        color: var(--text-secundario);
        font-size: 0.9375rem;
        line-height: 1.55;
        max-width: 70ch;
    }

    /* ---------- Cards (mesma linguagem do premium-card do Dashboard) ---------- */
    .pg-cards {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 26px;
    }

    .pg-card {
        position: relative;
        overflow: hidden;
        min-width: 0;
        background: var(--card-bg);
        border: var(--card-border);
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.015);
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: transform 0.2s ease, box-shadow 0.3s ease;
    }

    .pg-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px -10px rgba(15, 23, 42, 0.12);
    }

    /* Card estreito (zoom do sistema / texto ampliado — o zoom do painel
       não aciona media queries): ícone e gráfico passam para cima/baixo
       do texto em vez de espremer o valor até quebrar letra por letra
       ("5 . 2 9 0 k g"). Na largura normal nada muda (a quebra só acontece
       abaixo de ~194px de espaço). */
    .pg-card-main { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 18px; min-width: 0; }

    /* Selo "3D": gradiente diagonal + brilho interno + glow colorido. */
    .pg-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16);
    }

    .pg-card-data { display: flex; flex-direction: column; gap: 4px; min-width: 0; flex: 1 1 120px; }

    .pg-card-label { font-size: 0.844rem; font-weight: 600; letter-spacing: -0.1px; }

    .pg-card-value {
        font-size: clamp(1.25rem, 1.75vw, 1.688rem);
        font-weight: 700;
        color: var(--text-principal);
        line-height: 1.15;
        /* break-word (não "anywhere"): quebra nos espaços ("R$" / "11.278,50")
           e só parte um número se ele sozinho não couber na linha. */
        overflow-wrap: break-word;
    }

    .pg-card-value small { font-size: 0.6em; font-weight: 600; color: var(--text-secundario); margin-left: 3px; }

    .pg-card-sub {
        font-size: 0.75rem;
        color: var(--text-secundario);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    /* Rodapé do card: o elemento visual próprio de cada página. */
    .pg-card-foot { margin-top: auto; }

    /* Variantes de cor — idênticas às do Dashboard. */
    .pg-verde { background: linear-gradient(160deg, color-mix(in srgb, var(--verde-principal, #0e4a27) 12%, transparent), transparent 60%), var(--card-bg); }
    .pg-verde .pg-card-icon { background: linear-gradient(145deg, var(--verde-secundario, #16a765), var(--verde-principal, #0e4a27)); box-shadow: 0 6px 16px -4px color-mix(in srgb, var(--verde-principal, #0e4a27) 50%, transparent), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16); }
    .pg-verde .pg-card-label { color: var(--pg-verde-texto); }

    .pg-azul { background: linear-gradient(160deg, rgba(37, 99, 235, 0.12), transparent 60%), var(--card-bg); }
    .pg-azul .pg-card-icon { background: linear-gradient(145deg, #60a5fa, #2563eb); box-shadow: 0 6px 16px -4px rgba(37, 99, 235, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16); }
    .pg-azul .pg-card-label { color: var(--pg-azul-texto); }

    .pg-laranja { background: linear-gradient(160deg, rgba(249, 115, 22, 0.12), transparent 60%), var(--card-bg); }
    .pg-laranja .pg-card-icon { background: linear-gradient(145deg, #fb923c, #ea580c); box-shadow: 0 6px 16px -4px rgba(234, 88, 12, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16); }
    .pg-laranja .pg-card-label { color: var(--pg-laranja-texto); }

    .pg-roxo { background: linear-gradient(160deg, rgba(124, 58, 237, 0.12), transparent 60%), var(--card-bg); }
    .pg-roxo .pg-card-icon { background: linear-gradient(145deg, #c084fc, #7c3aed); box-shadow: 0 6px 16px -4px rgba(124, 58, 237, 0.5), inset 0 2px 3px rgba(255, 255, 255, 0.4), inset 0 -3px 5px rgba(0, 0, 0, 0.16); }
    .pg-roxo .pg-card-label { color: var(--pg-roxo-texto); }

    /* ---------- Card de destaque com gráfico (anel / tanque) ----------
       Primeiro card da linha, um pouco mais largo, com o gráfico à direita. */
    .pg-cards--destaque { grid-template-columns: minmax(0, 1.55fr) repeat(3, minmax(0, 1fr)); }

    .pg-card-visual { margin-left: auto; flex-shrink: 0; }

    /* Anel de progresso. */
    .pg-ring {
        --tam: 96px;
        --esp: 11px;
        width: var(--tam);
        height: var(--tam);
        border-radius: 50%;
        background: conic-gradient(var(--ring-cor, var(--pg-accent)) calc(var(--v, 0) * 1%), rgba(148, 163, 184, 0.2) 0);
        display: grid;
        place-items: center;
        position: relative;
        flex-shrink: 0;
    }

    .pg-ring::before {
        content: '';
        position: absolute;
        inset: var(--esp);
        border-radius: 50%;
        background: var(--card-bg);
        box-shadow: inset 0 2px 6px rgba(15, 23, 42, 0.06);
    }

    .pg-ring-text { position: relative; text-align: center; line-height: 1.1; }
    .pg-ring-text strong { display: block; font-size: 1.25rem; font-weight: 800; color: var(--text-principal); letter-spacing: -0.02em; }
    .pg-ring-text span { font-size: 0.6875rem; color: var(--text-secundario); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }

    /* Tanque vertical (quantidade no armazém), com marcas de 25/50/75%. */
    .pg-tank {
        position: relative;
        width: 70px;
        height: 104px;
        border-radius: 14px 14px 18px 18px;
        background: rgba(148, 163, 184, 0.14);
        border: 2px solid var(--surface-muted-border);
        overflow: hidden;
        flex-shrink: 0;
    }

    .pg-tank-fill {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: var(--v, 0%);
        background: linear-gradient(180deg, color-mix(in srgb, var(--tank-cor, var(--pg-accent)) 70%, #fff), var(--tank-cor, var(--pg-accent)));
    }

    .pg-tank::after {
        content: '';
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(0deg, transparent 0 calc(25% - 1px), rgba(15, 23, 42, 0.12) calc(25% - 1px) 25%);
        pointer-events: none;
    }

    .pg-tank-label {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-principal);
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.75);
        z-index: 1;
    }

    body.dark-theme .pg-ring::before { box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.35); }
    body.dark-theme .pg-tank::after { background: repeating-linear-gradient(0deg, transparent 0 calc(25% - 1px), rgba(255, 255, 255, 0.12) calc(25% - 1px) 25%); }
    body.dark-theme .pg-tank-label { text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6); }

    body.high-contrast .pg-ring { background: conic-gradient(#ffff00 calc(var(--v, 0) * 1%), #333 0) !important; }
    body.high-contrast .pg-ring::before { background: #000 !important; }
    body.high-contrast .pg-tank { background: #000 !important; border: 2px solid #ffff00 !important; }
    body.high-contrast .pg-tank-fill { background: #ffff00 !important; }
    body.high-contrast .pg-tank-label,
    body.high-contrast .pg-ring-text strong,
    body.high-contrast .pg-ring-text span { color: #ffff00 !important; text-shadow: 0 0 3px #000; }

    /* ---------- Caixa de seção (mesmo .box do Dashboard) ---------- */
    .pg-box {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 22px 24px;
        box-shadow: var(--card-shadow);
        border: var(--card-border);
        min-width: 0;
        margin-bottom: 26px;
    }

    .pg-box-title {
        margin-bottom: 16px;
        color: var(--text-principal);
        font-size: 0.969rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pg-box-title i {
        font-size: 0.875rem;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: var(--verde-claro, #e8f5ee);
        color: var(--verde-texto, #0e4a27);
        flex-shrink: 0;
    }

    /* Título da lista (acima dos itens). */
    .pg-list-title {
        font-size: 0.969rem;
        font-weight: 600;
        color: var(--text-principal);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pg-list-title i {
        font-size: 0.875rem;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: var(--verde-claro, #e8f5ee);
        color: var(--verde-texto, #0e4a27);
    }

    /* ---------- Medidor horizontal ---------- */
    .pg-meter {
        display: block;
        height: 8px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.18);
        overflow: hidden;
    }

    .pg-meter > span {
        display: block;
        height: 100%;
        width: var(--v, 0%);
        border-radius: inherit;
        background: var(--meter-cor, var(--pg-accent));
    }

    /* ---------- Barra segmentada (distribuição) ---------- */
    .pg-segbar {
        display: flex;
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        background: rgba(148, 163, 184, 0.18);
        gap: 3px;
    }

    .pg-segbar > span {
        flex: var(--v, 0) 1 0;
        min-width: 0;
        background: var(--seg-cor, var(--pg-accent));
    }

    .pg-legend { display: flex; flex-wrap: wrap; gap: 6px 14px; margin-top: 10px; }

    .pg-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--text-secundario);
        font-weight: 600;
    }

    .pg-legend-item::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--seg-cor, var(--pg-accent));
    }

    .pg-legend-item strong { color: var(--text-principal); font-weight: 700; }

    /* ---------- Chips (espécies) ---------- */
    .pg-chips { display: flex; flex-wrap: wrap; gap: 10px; }

    .pg-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 6px 14px 6px 6px;
        border-radius: 999px;
        background: var(--surface-muted-bg);
        border: 1px solid var(--surface-muted-border);
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-principal);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .pg-chip:hover { transform: translateY(-2px); box-shadow: 0 8px 16px -10px rgba(15, 23, 42, 0.3); }

    .pg-chip-ico {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 0.8125rem;
        color: #ffffff;
        box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.4), inset 0 -2px 3px rgba(0, 0, 0, 0.16);
    }

    .pg-chip-count { font-weight: 800; color: var(--text-secundario); font-variant-numeric: tabular-nums; }

    /* ---------- Responsivo ---------- */
    @media (max-width: 1200px) {
        .pg-cards,
        .pg-cards--destaque { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 768px) {
        .pg-title { font-size: 1.5rem; }
    }

    @media (max-width: 560px) {
        .pg-cards,
        .pg-cards--destaque { grid-template-columns: 1fr; gap: 14px; }
        .pg-ring { --tam: 84px; --esp: 10px; }
        .pg-tank { width: 60px; height: 92px; }
        .pg-card { padding: 18px; }
        .pg-card-icon { width: 48px; height: 48px; font-size: 1.1rem; }
        .pg-box { padding: 18px; }
        .pg-top-actions { width: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .pg-card, .pg-chip { transition: none; }
        .pg-card:hover, .pg-chip:hover { transform: none; }
    }

    /* ---------- Modo escuro (mesmo tratamento do Dashboard) ---------- */
    body.dark-theme .pg-card { border: 1px solid rgba(255, 255, 255, 0.06); }
    body.dark-theme .pg-verde .pg-card-label { color: var(--verde-texto, #0e4a27); }
    body.dark-theme .pg-azul .pg-card-label { color: #38bdf8; }
    body.dark-theme .pg-laranja .pg-card-label { color: #fb923c; }
    body.dark-theme .pg-roxo .pg-card-label { color: #c084fc; }
    body.dark-theme .pg-box-title i,
    body.dark-theme .pg-list-title i { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); color: var(--verde-texto, #0e4a27); }

    /* ---------- Alto contraste ---------- */
    body.high-contrast .pg-card,
    body.high-contrast .pg-box,
    body.high-contrast .pg-chip {
        background: #000000 !important;
        border: 2px solid #ffff00 !important;
        box-shadow: none !important;
    }

    body.high-contrast .pg-card-icon,
    body.high-contrast .pg-chip-ico,
    body.high-contrast .pg-box-title i,
    body.high-contrast .pg-list-title i {
        background: #000000 !important;
        border: 2px solid #ffff00 !important;
        color: #ffff00 !important;
        box-shadow: none !important;
    }

    body.high-contrast .pg-card-label,
    body.high-contrast .pg-card-value,
    body.high-contrast .pg-card-sub,
    body.high-contrast .pg-title,
    body.high-contrast .pg-sub { color: #ffff00 !important; }

    body.high-contrast .pg-meter > span,
    body.high-contrast .pg-segbar > span,
    body.high-contrast .pg-legend-item::before { background: #ffff00 !important; }
</style>
