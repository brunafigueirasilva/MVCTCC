<!-- ==========================================================================
     PAINEL DE ACESSIBILIDADE (COMPONENTE PADRONIZADO E REUTILIZÁVEL)
     Inclua com @@include('partials.acessibilidade') em qualquer página que
     tenha uma navbar fixa. O componente é autossuficiente (CSS + HTML + JS).
     ========================================================================== -->
@include('partials.a11y-global')
<style>
    html {
        /* Usamos "zoom" em vez de "transform: scale()" de propósito: um
           transform no <html> cria um novo containing block para todo
           elemento position:fixed da página (navbar, sidebar, modais),
           fazendo-os "descolar" do viewport e sumir em páginas com pouco
           conteúdo. A propriedade zoom escala sem esse efeito colateral. */
        /* Tamanho do texto: aplicado por elemento no script abaixo (A+/A-). */
        /* Tamanho natural do sistema: 100% (sem redução) — o controle
           "Zoom do Sistema" deste painel continua funcionando normalmente
           a partir daqui (pra mais ou pra menos); "Padrão" volta pra 100%. */
        zoom: var(--zoom-scale, 1);
    }

    .accessibility-trigger {
        /* Posição padrão (fallback): usada apenas em páginas sem .navbar-right,
           como as telas de autenticação. Em páginas com navbar, o script abaixo
           move este botão para dentro de .navbar-right, junto ao "Sair", para que
           ele nunca sobreponha outros elementos do topo. */
        position: fixed;
        top: 20px;
        right: 20px;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: var(--brand-green-light, var(--verde-texto, #0e4a27));
        border: 1px solid rgba(209, 161, 54, 0.25);
        backdrop-filter: blur(6px);
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        z-index: 1005;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        transition: transform 0.3s ease, background-color 0.2s, border-color 0.2s;
    }

    /* Variante "pill" com texto, usada quando o botão é movido para dentro
       de .navbar-right (ver script no fim deste arquivo) — nas telas sem
       navbar (login, etc.) o botão continua um círculo só com ícone. */
    .accessibility-trigger.acc-trigger-inline {
        position: static;
        width: auto;
        height: auto;
        font-size: 17px;
        box-shadow: none;
        border-radius: 999px;
        padding: 9px 18px 9px 14px;
        gap: 9px;
        background: var(--sidebar-bg, #123f22);
        color: #ffffff;
        border-color: transparent;
    }

    .acc-trigger-label {
        display: none;
        font-size: 0.8125rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .accessibility-trigger.acc-trigger-inline .acc-trigger-label {
        display: inline;
    }

    .accessibility-trigger:hover {
        transform: scale(1.1) rotate(45deg);
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    .accessibility-trigger.acc-trigger-inline:hover {
        transform: none;
        filter: brightness(1.12);
        background: var(--sidebar-bg, #123f22);
        color: #ffffff;
    }

    @media (max-width: 480px) {
        .acc-trigger-label { display: none; }
        .accessibility-trigger.acc-trigger-inline { padding: 9px; border-radius: 50%; }
    }

    body.high-contrast .accessibility-trigger {
        /* Antes era um fundo amarelo cheio com ícone preto e borda branca —
           três cores diferentes no mesmo botão pequeno, fora do padrão
           preto/amarelo usado no resto do alto contraste. Unificado com o
           mesmo estilo dos demais controles (fundo preto, ícone e borda
           amarelos), com o preenchimento amarelo reservado para o hover. */
        background: #000000;
        color: #ffff00;
        border: 2px solid #ffff00;
    }

    body.high-contrast .accessibility-trigger:hover {
        background: #ffff00;
        color: #000000;
    }

    /* A página que inclui este painel (ex.: dashboard) costuma ter uma
       regra genérica tipo "body.high-contrast i { color: #ffff00 !important }"
       para deixar todo ícone amarelo. Ela é mais específica/importante que a
       cor herdada do botão no hover, então o ícone ficava amarelo sobre um
       fundo que também vira amarelo no hover — sumindo. Estas regras miram
       o <i> diretamente, com especificidade maior, para garantir que o
       ícone sempre acompanhe a cor de texto correta do próprio botão. */
    body.high-contrast .accessibility-trigger:hover i {
        color: #000000 !important;
    }

    .accessibility-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) perspective(1000px) rotateX(6deg) scale(0.92);
        width: 90%;
        max-width: 550px;
        background: var(--panel-bg, #ffffff);
        color: var(--panel-text, #1f2937);
        border: 1px solid rgba(209, 161, 54, 0.18);
        border-radius: 24px;
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(18px) saturate(140%);
        -webkit-backdrop-filter: blur(18px) saturate(140%);
        z-index: 1002;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
    }

    .accessibility-modal.open {
        opacity: 1;
        pointer-events: auto;
        transform: translate(-50%, -50%) perspective(1000px) rotateX(0deg) scale(1);
    }

    .accessibility-modal .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .accessibility-modal .modal-header .acc-modal-titulo {
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .accessibility-modal .modal-close {
        background: transparent;
        border: none;
        font-size: 30px;
        color: var(--text-muted, #64748b);
        cursor: pointer;
        line-height: 1;
        transition: color 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .accessibility-modal .modal-close:hover {
        color: var(--text-main, #1e293b);
        transform: rotate(90deg) scale(1.1);
    }

    .accessibility-modal .modal-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .accessibility-modal .modal-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .accessibility-modal .modal-section .acc-secao-titulo {
        font-size: 0.88rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted, #64748b);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .accessibility-modal .control-grid {
        display: grid;
        /* Fluido em vez de "1fr 1fr" fixo: o próprio "Zoom do Sistema" deste
           painel amplia o painel junto com o resto da página (já que o
           efeito é global, via <html>{zoom}) — com 2 colunas fixas, os
           cartões de controle apertavam/cortavam texto e botões quando o
           zoom subia, pois a media query de largura da tela não percebia
           esse espaço extra necessário. Com auto-fit cada cartão nunca fica
           menor que 170px e a grade se ajusta sozinha em qualquer zoom. */
        grid-template-columns: repeat(auto-fit, minmax(min(170px, 100%), 1fr));
        gap: 12px;
    }

    .accessibility-modal .control-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--panel-item-bg, #f9fafb);
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border-color, #e2e8f0);
    }

    .accessibility-modal .control-item-vertical {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: var(--panel-item-bg, #f9fafb);
        padding: 14px;
        border-radius: 12px;
        border: 1px solid var(--border-color, #e2e8f0);
    }

    .accessibility-modal .control-label {
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .accessibility-modal .switch-btn {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }

    .accessibility-modal .switch-btn input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .accessibility-modal .slider-switch {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        border: 2px solid transparent;
        transition: .3s;
        border-radius: 24px;
    }

    .accessibility-modal .slider-switch:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    .accessibility-modal input:checked + .slider-switch {
        background: linear-gradient(135deg, #6aaa2e, #4F7724);
    }

    .accessibility-modal input:checked + .slider-switch:before {
        transform: translateX(22px);
    }

    /* Hover/Focus/Active — o <input> em si fica invisível (opacity:0), então
       sem estas regras o controle nunca dava NENHUM feedback visual ao
       passar o mouse, focar via teclado ou pressionar. */
    .accessibility-modal .switch-btn:hover .slider-switch {
        filter: brightness(0.94);
    }

    .accessibility-modal .switch-btn input:focus-visible + .slider-switch {
        outline: 2px solid #4F7724;
        outline-offset: 3px;
    }

    .accessibility-modal .switch-btn input:active + .slider-switch {
        filter: brightness(0.88);
    }

    .accessibility-modal .btn-group-row {
        display: flex;
        gap: 6px;
    }

    .accessibility-modal .acc-btn {
        flex: 1;
        padding: 8px;
        border: 1px solid var(--border-color, #e2e8f0);
        background: var(--panel-btn-bg, #e5e7eb);
        color: var(--panel-btn-text, #374151);
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    }

    .accessibility-modal .acc-btn:hover {
        background: var(--panel-btn-hover, #d1d5db);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
    }

    .accessibility-modal .acc-btn.btn-secondary {
        background: transparent;
    }

    .accessibility-modal .voice-box {
        background: var(--panel-item-bg, #f9fafb);
        border: 1px solid var(--border-color, #e2e8f0);
        padding: 16px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .accessibility-modal .voice-controls {
        display: flex;
        gap: 10px;
    }

    .accessibility-modal .acc-btn-voice {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: white;
        transition: background 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    }

    .accessibility-modal .acc-btn-voice:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, .25);
    }

    .accessibility-modal .voice-start { background: var(--verde-principal, #0e4a27); }
    .accessibility-modal .voice-start:hover { background: var(--verde-hover, #0b3d21); }
    .accessibility-modal .voice-stop { background: #b91c1c; }
    .accessibility-modal .voice-stop:hover { background: #dc2626; }

    .accessibility-modal .voice-tip {
        font-size: 0.78rem;
        color: var(--text-muted, #64748b);
        line-height: 1.4;
    }

    .modal-overlay {
        position: fixed;
        /* "top/left/right/bottom: 0" em vez de "width: 100vw; height: 100vh":
           quando a página está com o "Zoom do Sistema" deste mesmo painel
           aplicado (via zoom no <html>), unidades de viewport (vw/vh)
           podem ficar dessincronizadas do tamanho real já escalado do
           elemento fixo, deixando uma faixa da tela sem o fundo escurecido.
           Fixando pelas 4 bordas o overlay sempre cobre 100% do seu próprio
           containing block, em qualquer zoom. */
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1001;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    body.dark-theme .accessibility-modal .select-fazenda,
    body.dark-theme .accessibility-modal { color: #f8fafc; }

    body.high-contrast .accessibility-modal {
        background: #000000 !important;
        color: #ffffff !important;
        border: 3px solid #ffffff !important;
        box-shadow: 0 0 0 5px #000000, 0 25px 60px rgba(0, 0, 0, 0.9) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    body.high-contrast .accessibility-modal .modal-header {
        border-bottom: 2px solid #ffffff !important;
    }

    body.high-contrast .accessibility-modal .modal-header .acc-modal-titulo,
    body.high-contrast .accessibility-modal .modal-close,
    body.high-contrast .accessibility-modal .modal-section .acc-secao-titulo {
        color: #ffff00 !important;
    }

    body.high-contrast .accessibility-modal .control-item,
    body.high-contrast .accessibility-modal .control-item-vertical,
    body.high-contrast .accessibility-modal .voice-box {
        background: #000000 !important;
        border: 2px solid #ffffff !important;
        color: #ffffff !important;
    }

    body.high-contrast .accessibility-modal .control-label,
    body.high-contrast .accessibility-modal .voice-tip {
        color: #ffffff !important;
    }

    body.high-contrast .accessibility-modal .acc-btn {
        background: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
    }

    body.high-contrast .accessibility-modal .acc-btn:hover {
        background: #ffffff !important;
        color: #000000 !important;
    }

    /* Mesmo motivo do botão de acessibilidade: sem isso, o ícone (seta de
       "+"/"-") ficava amarelo (regra genérica da página) sobre o fundo
       branco do hover, em vez de acompanhar o texto preto do botão. */
    body.high-contrast .accessibility-modal .acc-btn:hover i {
        color: #000000 !important;
    }

    body.high-contrast .accessibility-modal .acc-btn.btn-secondary {
        color: #ffff00 !important;
        border-color: #ffff00 !important;
    }

    body.high-contrast .accessibility-modal .voice-start {
        background: #000000 !important;
        color: #ffff00 !important;
        border: 2px solid #ffff00 !important;
    }

    body.high-contrast .accessibility-modal .voice-stop {
        background: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
    }

    /* O estado "ligado" normal (verde, regra acima) usa `background` como
       atalho pra um GRADIENTE (background-image), não uma cor sólida. Uma
       sobrescrita que só define `background-color` (como esta tinha antes)
       fica escondida atrás desse gradiente — ele continua pintado por
       cima, e o botão "Alto Contraste" continuava aparecendo verde mesmo
       com esta regra "vencendo" pela especificidade/!important. Por isso
       aqui precisa ser `background` (o atalho completo): ao dar só uma cor
       sólida, ele também zera o background-image herdado, removendo o
       gradiente verde de vez — e não só empilhando uma cor atrás dele. */
    /* Trilho "desligado" (Modo Escuro, quando quem está ativo é o Alto
       Contraste): preto/branco em vez do cinza neutro padrão, pra seguir a
       mesma paleta do resto do alto contraste mesmo no estado inativo. */
    body.high-contrast .accessibility-modal .slider-switch {
        background: #000000 !important;
        border-color: #ffffff !important;
    }

    body.high-contrast .accessibility-modal .slider-switch:before {
        background: #ffffff !important;
    }

    body.high-contrast .accessibility-modal input:checked + .slider-switch {
        background: #ffff00 !important;
        border-color: #ffff00 !important;
    }

    body.high-contrast .accessibility-modal input:checked + .slider-switch:before {
        background: #000000 !important;
    }

    body.high-contrast .accessibility-modal .switch-btn:hover .slider-switch {
        filter: brightness(1.15);
    }

    body.high-contrast .accessibility-modal .switch-btn input:focus-visible + .slider-switch {
        outline: 3px solid #ffffff !important;
        outline-offset: 3px;
    }

    body.high-contrast .accessibility-modal input:checked:focus-visible + .slider-switch {
        outline-color: #ffffff !important;
    }

    body.high-contrast .modal-overlay {
        background: rgba(0, 0, 0, 0.5) !important;
    }

    @media (max-width: 576px) {
        .accessibility-modal { width: 95%; border-radius: 16px; }
        .control-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- BOTÃO FIXO DE ACESSIBILIDADE -->
<button type="button" class="accessibility-trigger" id="accTrigger" aria-label="Abrir configurações de acessibilidade" aria-controls="accModal" aria-haspopup="dialog" aria-expanded="false">
    <i class="fa-solid fa-universal-access" aria-hidden="true"></i>
    <span class="acc-trigger-label">Acessibilidade</span>
</button>

<!-- QUADRADO CENTRAL DO PAINEL DE ACESSIBILIDADE -->
<div class="accessibility-modal" id="accModal" role="dialog" aria-modal="true" aria-labelledby="accModalTitulo">
    <div class="modal-header">
        <h2 id="accModalTitulo" class="acc-modal-titulo"><i class="fa-solid fa-universal-access" aria-hidden="true"></i> Painel de Acessibilidade</h2>
        <button type="button" class="modal-close" id="accClose" aria-label="Fechar painel de acessibilidade">&times;</button>
    </div>

    <div class="modal-body">
        <div class="modal-section">
            <h3 class="acc-secao-titulo"><i class="fa-solid fa-palette" aria-hidden="true"></i> Preferências de Tema</h3>
            <div class="control-grid">
                <div class="control-item">
                    <span class="control-label" id="lblDarkMode"><i class="fa-solid fa-moon" aria-hidden="true"></i> Modo Escuro</span>
                    <label class="switch-btn">
                        <input type="checkbox" id="chkDarkMode" role="switch" aria-labelledby="lblDarkMode">
                        <span class="slider-switch" aria-hidden="true"></span>
                    </label>
                </div>
                <div class="control-item">
                    <span class="control-label" id="lblHighContrast"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i> Alto Contraste</span>
                    <label class="switch-btn">
                        <input type="checkbox" id="chkHighContrast" role="switch" aria-labelledby="lblHighContrast">
                        <span class="slider-switch" aria-hidden="true"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-section">
            <h3 class="acc-secao-titulo"><i class="fa-solid fa-sliders" aria-hidden="true"></i> Dimensionamento</h3>
            <div class="control-grid">
                <div class="control-item-vertical" role="group" aria-labelledby="lblFonte">
                    <span class="control-label" id="lblFonte"><i class="fa-solid fa-font" aria-hidden="true"></i> Tamanho do Texto <span class="acc-valor" id="accValorFonte">100%</span></span>
                    <div class="btn-group-row">
                        <button type="button" class="acc-btn" id="btnDecreaseFont" title="Diminuir texto" aria-label="Diminuir o tamanho do texto (A-)"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
                        <button type="button" class="acc-btn btn-secondary" id="btnResetFont" title="Restaurar texto" aria-label="Restaurar o tamanho padrão do texto">Padrão</button>
                        <button type="button" class="acc-btn" id="btnIncreaseFont" title="Aumentar texto" aria-label="Aumentar o tamanho do texto (A+)"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="control-item-vertical" role="group" aria-labelledby="lblZoom">
                    <span class="control-label" id="lblZoom"><i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i> Zoom do Sistema <span class="acc-valor" id="accValorZoom">100%</span></span>
                    <div class="btn-group-row">
                        <button type="button" class="acc-btn" id="btnDecreaseZoom" title="Diminuir zoom" aria-label="Diminuir o zoom"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
                        <button type="button" class="acc-btn btn-secondary" id="btnResetZoom" title="Restaurar zoom" aria-label="Restaurar o zoom padrão">Padrão</button>
                        <button type="button" class="acc-btn" id="btnIncreaseZoom" title="Aumentar zoom" aria-label="Aumentar o zoom"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
            <p class="a11y-sr-only" id="accAnuncio" role="status" aria-live="polite"></p>
        </div>

        <div class="modal-section">
            <h3 class="acc-secao-titulo"><i class="fa-solid fa-volume-high" aria-hidden="true"></i> Leitura de Tela (Voz)</h3>
            <div class="voice-box">
                <div class="voice-controls">
                    <button type="button" class="acc-btn-voice voice-start" id="btnStartVoice">
                        <i class="fa-solid fa-play" aria-hidden="true"></i> Ouvir Página
                    </button>
                    <button type="button" class="acc-btn-voice voice-stop" id="btnStopVoice">
                        <i class="fa-solid fa-stop" aria-hidden="true"></i> Parar Áudio
                    </button>
                </div>
                <p class="voice-tip">Clique em "Ouvir Página" para escutar de forma automatizada o resumo do conteúdo desta tela.</p>
            </div>
        </div>
    </div>
</div>

<!-- Overlay escuro ao fundo -->
<div class="modal-overlay" id="accOverlay"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const accTrigger = document.getElementById('accTrigger');
        const accModal = document.getElementById('accModal');
        const accClose = document.getElementById('accClose');
        const accOverlay = document.getElementById('accOverlay');

        const chkDarkMode = document.getElementById('chkDarkMode');
        const chkHighContrast = document.getElementById('chkHighContrast');
        const btnDecreaseFont = document.getElementById('btnDecreaseFont');
        const btnResetFont = document.getElementById('btnResetFont');
        const btnIncreaseFont = document.getElementById('btnIncreaseFont');
        const btnDecreaseZoom = document.getElementById('btnDecreaseZoom');
        const btnResetZoom = document.getElementById('btnResetZoom');
        const btnIncreaseZoom = document.getElementById('btnIncreaseZoom');
        const btnStartVoice = document.getElementById('btnStartVoice');
        const btnStopVoice = document.getElementById('btnStopVoice');

        if (!accTrigger || !accModal) {
            return;
        }

        // Se a página tiver uma navbar padrão, o botão passa a viver dentro
        // de .navbar-right (como mais um item, ao lado do "Sair"), em vez de
        // flutuar sobre o conteúdo — assim nunca sobrepõe botões da página.
        const navbarRight = document.querySelector('.navbar-right');
        if (navbarRight) {
            const divider = document.createElement('div');
            divider.className = 'navbar-divider';
            navbarRight.appendChild(divider);
            navbarRight.appendChild(accTrigger);
            accTrigger.classList.add('acc-trigger-inline');
        }

        let currentFontScale = 100;
        // Zoom inicial definido pela própria página (a landing usa 0.9): é o
        // "Padrão" do botão e o valor mostrado antes de qualquer ajuste —
        // antes o painel dizia "100%" com a página em 90%, e "Padrão" não
        // voltava ao estado inicial.
        const zoomPadrao = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--zoom-scale')) || 1;
        let currentZoomScale = zoomPadrao;
        let speechUtterance = null;

        // O zoom (CSS "zoom" no <html>) não aciona media queries: a tela
        // "encolhe" sem o CSS saber. A classe deixa as telas trocarem layouts
        // largos (ex.: tabela de 8 colunas do Estoque) pela versão que quebra
        // linha enquanto o zoom estiver ampliado.
        function aplicarZoomNoHtml(escala) {
            document.documentElement.style.setProperty('--zoom-scale', escala);
            document.documentElement.classList.toggle('zoom-sistema-ampliado', escala > 1);
        }

        // ==========================================
        // TAMANHO DO TEXTO (A+ / A-)
        // Antes só mudava o font-size do <html>, o que só afeta medidas em
        // "rem" — a maior parte das telas usa "px", então o texto não mudava.
        // Agora cada elemento com texto guarda o tamanho original (já
        // calculado pelo navegador, venha de px, rem ou media query) e recebe
        // esse tamanho × escala. "Padrão" devolve o estilo original intacto.
        // ==========================================
        const SEL_TEXTO_FORMULARIO = 'input, select, textarea, button, label';
        const textoEscalado = new Set();
        let observadorFonte = null;

        const temTextoProprio = (el) => {
            if (el.matches(SEL_TEXTO_FORMULARIO)) return true;
            for (const no of el.childNodes) {
                if (no.nodeType === 3 && no.nodeValue.trim()) return true;
            }
            return false;
        };

        // O próprio painel não cresce (continua cabendo na tela para voltar
        // ao padrão); a barra inferior e o header do celular também não —
        // têm altura fixa e 6 itens lado a lado: ampliados, os rótulos viravam
        // "Faze…", "Coc…". O zoom do sistema/navegador continua valendo neles.
        const ignorar = (el) => el.closest('script, style, noscript, canvas, .accessibility-modal, .accessibility-trigger, [vw], .mobile-bottom-nav, .mobile-header')
            || [...el.classList].some((c) => c.startsWith('fa-'));

        const ehAlvo = (el) => el instanceof HTMLElement && !el.dataset.a11yFonteOriginal && !ignorar(el) && temTextoProprio(el);

        // Duas fases: MEDE todos antes de MUDAR qualquer um. Antes media e
        // aplicava elemento a elemento — o filho (<strong> dentro de <p>,
        // <span> dentro de <button>) herdava o tamanho já aumentado do pai e
        // era aumentado de novo (150% virava 225%).
        const escalarLote = (elementos, escala) => {
            const medidas = elementos.filter(ehAlvo).map((el) => [el, getComputedStyle(el).fontSize, el.style.fontSize || '']);
            medidas.forEach(([el, original, inline]) => {
                el.dataset.a11yFonteOriginal = original;
                el.dataset.a11yFonteInline = inline;
                el.style.fontSize = (parseFloat(original) * escala).toFixed(2) + 'px';
                textoEscalado.add(el);
            });
        };

        const restaurarFonte = () => {
            textoEscalado.forEach((el) => {
                el.style.fontSize = el.dataset.a11yFonteInline || '';
                delete el.dataset.a11yFonteOriginal;
                delete el.dataset.a11yFonteInline;
            });
            textoEscalado.clear();
        };

        const aplicarEscalaFonte = (percentual) => {
            // Mede sempre a partir do tamanho original (sem escala anterior).
            restaurarFonte();
            if (observadorFonte) { observadorFonte.disconnect(); observadorFonte = null; }
            if (percentual === 100) return;

            const escala = percentual / 100;
            escalarLote([...document.body.querySelectorAll('*')], escala);

            // Conteúdo que chega depois (listas via fetch, modais montados):
            // mede com os ancestrais momentaneamente no tamanho original,
            // senão o novo elemento herdaria o tamanho já ampliado.
            let pendentes = [];
            let agendado = false;
            const escalarPendentes = () => {
                agendado = false;
                const novos = pendentes.filter((el) => document.contains(el) && ehAlvo(el));
                pendentes = [];
                if (!novos.length) return;
                textoEscalado.forEach((el) => { el.style.fontSize = el.dataset.a11yFonteInline || ''; });
                const medidas = novos.map((el) => [el, getComputedStyle(el).fontSize, el.style.fontSize || '']);
                textoEscalado.forEach((el) => { el.style.fontSize = (parseFloat(el.dataset.a11yFonteOriginal) * escala).toFixed(2) + 'px'; });
                medidas.forEach(([el, original, inline]) => {
                    el.dataset.a11yFonteOriginal = original;
                    el.dataset.a11yFonteInline = inline;
                    el.style.fontSize = (parseFloat(original) * escala).toFixed(2) + 'px';
                    textoEscalado.add(el);
                });
            };
            observadorFonte = new MutationObserver((mutacoes) => {
                mutacoes.forEach((m) => m.addedNodes.forEach((no) => {
                    if (no.nodeType !== 1) return;
                    pendentes.push(no, ...no.querySelectorAll('*'));
                }));
                if (pendentes.length && !agendado) { agendado = true; requestAnimationFrame(escalarPendentes); }
            });
            observadorFonte.observe(document.body, { childList: true, subtree: true });
        };

        // Media queries mudam o tamanho "original" ao girar a tela/redimensionar.
        let reescalarTimer = null;
        window.addEventListener('resize', () => {
            if (currentFontScale === 100) return;
            clearTimeout(reescalarTimer);
            reescalarTimer = setTimeout(() => aplicarEscalaFonte(currentFontScale), 250);
        });

        const accAnuncio = document.getElementById('accAnuncio');
        const accValorFonte = document.getElementById('accValorFonte');
        const accValorZoom = document.getElementById('accValorZoom');
        const mostrarValores = (anunciar) => {
            const fonte = `${currentFontScale}%`;
            const zoom = `${Math.round(currentZoomScale * 100)}%`;
            if (accValorFonte) accValorFonte.textContent = fonte;
            if (accValorZoom) accValorZoom.textContent = zoom;
            if (anunciar && accAnuncio) accAnuncio.textContent = anunciar === 'fonte' ? `Tamanho do texto: ${fonte}` : `Zoom: ${zoom}`;
        };

        // ==========================================
        // SINCRONIZAÇÃO COM O SERVIDOR (entre dispositivos)
        // localStorage continua sendo a fonte instantânea (evita flash de
        // conteúdo sem estilo ao carregar a página); o servidor é só
        // consultado depois, pra sincronizar com o que foi salvo em outro
        // aparelho — e atualizado a cada mudança, pra quem estiver logado.
        // ==========================================
        const csrfToken = '{{ csrf_token() }}';
        const urlAcessibilidade = '{{ route('acessibilidade.show') }}';
        let salvandoNoServidor = null;

        const temaAtual = () => {
            if (document.body.classList.contains('dark-theme')) return 'dark';
            if (document.body.classList.contains('high-contrast')) return 'contrast';
            return 'light';
        };

        function salvarPreferenciasNoServidor() {
            // Debounce simples: várias mudanças seguidas (ex.: segurar o
            // "+" do zoom) viram uma única chamada, disparada só quando o
            // usuário para de mexer.
            clearTimeout(salvandoNoServidor);
            salvandoNoServidor = setTimeout(() => {
                fetch(urlAcessibilidade, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        tema: temaAtual(),
                        tamanho_fonte: currentFontScale,
                        zoom: currentZoomScale,
                        voz_velocidade: 1,
                    }),
                }).catch(() => {
                    // Falha ao sincronizar não deve travar a experiência —
                    // localStorage já aplicou a preferência neste aparelho.
                });
            }, 500);
        }

        function aplicarPreferenciasDoServidor(preferencias) {
            if (!preferencias) return;

            const temaAntes = temaAtual();
            document.body.classList.remove('dark-theme', 'high-contrast');
            chkDarkMode.checked = false;
            chkHighContrast.checked = false;

            if (preferencias.tema === 'dark') {
                document.body.classList.add('dark-theme');
                chkDarkMode.checked = true;
            } else if (preferencias.tema === 'contrast') {
                document.body.classList.add('high-contrast');
                chkHighContrast.checked = true;
            }
            localStorage.setItem('theme', preferencias.tema ?? 'light');

            // Tema mudou por causa do servidor (salvo em outro aparelho): avisa
            // as telas como se o usuário tivesse clicado no interruptor — os
            // gráficos se redesenham com as cores do tema novo (antes ficavam
            // com eixos/legendas do tema anterior, ilegíveis).
            // Só nas telas internas (data-mobile-shell): a landing tem tema
            // próprio ("landingTheme") e não pode ser afetada.
            if (temaAtual() !== temaAntes && document.body.hasAttribute('data-mobile-shell')) {
                const alvo = temaAtual() === 'contrast' ? chkHighContrast : chkDarkMode;
                alvo.dispatchEvent(new Event('change'));
            }

            if (preferencias.tamanho_fonte) {
                const escalaServidor = parseInt(preferencias.tamanho_fonte, 10);
                if (escalaServidor !== currentFontScale) {
                    currentFontScale = escalaServidor;
                    aplicarEscalaFonte(currentFontScale);
                }
                localStorage.setItem('fontScale', currentFontScale);
            }

            if (preferencias.zoom) {
                currentZoomScale = parseFloat(preferencias.zoom);
                aplicarZoomNoHtml(currentZoomScale);
                localStorage.setItem('zoomScale', currentZoomScale);
            }
            mostrarValores();
        }

        function carregarPreferenciasDoServidor() {
            fetch(urlAcessibilidade, { headers: { 'Accept': 'application/json' } })
                .then((resposta) => resposta.ok ? resposta.json() : null)
                .then((dados) => {
                    if (dados?.autenticado && dados.preferencias) {
                        aplicarPreferenciasDoServidor(dados.preferencias);
                    }
                })
                .catch(() => {
                    // Sem servidor disponível, localStorage (já aplicado)
                    // continua valendo normalmente.
                });
        }

        const toggleModal = () => {
            accModal.classList.toggle('open');
            accOverlay.classList.toggle('active');
            // Classe usada por páginas com navbar/sidebar fixas (ex.:
            // dashboard) para desfocar esses elementos diretamente — o
            // backdrop-filter do overlay sozinho não desfoca de forma
            // confiável vários elementos "position: fixed" empilhados.
            document.body.classList.toggle('acc-modal-open');
        };

        accTrigger.addEventListener('click', toggleModal);
        accClose.addEventListener('click', toggleModal);
        accOverlay.addEventListener('click', toggleModal);

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && accModal.classList.contains('open')) {
                toggleModal();
            }
        });

        chkDarkMode.addEventListener('change', (e) => {
            if (e.target.checked) {
                document.body.classList.add('dark-theme');
                document.body.classList.remove('high-contrast');
                chkHighContrast.checked = false;
                localStorage.setItem('theme', 'dark');
            } else {
                document.body.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
            }
            salvarPreferenciasNoServidor();
        });

        chkHighContrast.addEventListener('change', (e) => {
            if (e.target.checked) {
                document.body.classList.add('high-contrast');
                document.body.classList.remove('dark-theme');
                chkDarkMode.checked = false;
                localStorage.setItem('theme', 'contrast');
            } else {
                document.body.classList.remove('high-contrast');
                localStorage.setItem('theme', 'light');
            }
            salvarPreferenciasNoServidor();
        });

        const updateFontScale = (newScale) => {
            currentFontScale = Math.max(80, Math.min(newScale, 150));
            aplicarEscalaFonte(currentFontScale);
            localStorage.setItem('fontScale', currentFontScale);
            mostrarValores('fonte');
            salvarPreferenciasNoServidor();
        };
        btnIncreaseFont.addEventListener('click', () => updateFontScale(currentFontScale + 10));
        btnDecreaseFont.addEventListener('click', () => updateFontScale(currentFontScale - 10));
        btnResetFont.addEventListener('click', () => updateFontScale(100));

        const updateZoomScale = (newZoom) => {
            currentZoomScale = Math.round(Math.max(0.5, Math.min(newZoom, 1.3)) * 100) / 100;
            aplicarZoomNoHtml(currentZoomScale);
            localStorage.setItem('zoomScale', currentZoomScale);
            mostrarValores('zoom');
            salvarPreferenciasNoServidor();
        };
        btnIncreaseZoom.addEventListener('click', () => updateZoomScale(currentZoomScale + 0.05));
        btnDecreaseZoom.addEventListener('click', () => updateZoomScale(currentZoomScale - 0.05));
        btnResetZoom.addEventListener('click', () => updateZoomScale(zoomPadrao));

        // ==========================================
        // LEITURA DA PÁGINA (VOZ)
        // Antes lia só o título + a descrição do topo (ex.: em Gestão de
        // Lotes, 83 caracteres e nenhum lote). Agora percorre o conteúdo
        // principal VISÍVEL na ordem da tela — títulos, cards, textos,
        // tabelas e o nome dos gráficos —, pulando menu, modais fechados e
        // este painel. Fala em trechos curtos: o Chrome corta uma fala
        // longa no meio (~15 s) sem avisar.
        // ==========================================
        const IGNORAR_NA_LEITURA = '.accessibility-modal, .accessibility-trigger, #accOverlay, .sidebar, .mobile-header, '
            + '.mobile-bottom-nav, .mobile-menu, nav, footer, script, style, noscript, template, select, '
            + '[aria-hidden="true"], [inert], .a11y-pular, .ai-chat-panel, .ai-chat-trigger, [vw], #splash';

        const coletarTextoDaPagina = () => {
            const raiz = document.querySelector('main, [role="main"]') || document.body;
            const blocos = [];
            let blocoAtual = null;
            let texto = '';
            const fecharBloco = () => {
                const t = texto.replace(/\s+/g, ' ').trim();
                if (t && blocos[blocos.length - 1] !== t) blocos.push(t);
                texto = '';
            };
            const walker = document.createTreeWalker(raiz, NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT, {
                acceptNode(no) {
                    const el = no.nodeType === 1 ? no : no.parentElement;
                    if (!el || el.closest(IGNORAR_NA_LEITURA)) return NodeFilter.FILTER_REJECT;
                    if (no.nodeType === 1) {
                        // Gráfico (canvas com role="img"): lê o nome dele.
                        return el.getAttribute('role') === 'img' && el.getAttribute('aria-label')
                            ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
                    }
                    if (!no.nodeValue.trim() || !el.getClientRects().length) return NodeFilter.FILTER_REJECT;
                    const estilo = getComputedStyle(el);
                    return estilo.visibility === 'hidden' ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
                },
            });
            while (walker.nextNode()) {
                const no = walker.currentNode;
                if (no.nodeType === 1) {
                    fecharBloco();
                    blocos.push(no.getAttribute('aria-label'));
                    blocoAtual = null;
                    continue;
                }
                // Agrupa o texto pelo bloco (div/p/li/td...) mais próximo:
                // "Estoque" + "120 kg" do mesmo card viram uma frase só.
                let bloco = no.parentElement;
                while (bloco && bloco !== raiz && getComputedStyle(bloco).display.startsWith('inline')) bloco = bloco.parentElement;
                if (bloco !== blocoAtual) { fecharBloco(); blocoAtual = bloco; }
                texto += ' ' + no.nodeValue;
            }
            fecharBloco();
            return blocos;
        };

        // Junta blocos curtos em falas de até ~220 caracteres.
        const montarFalas = (blocos) => {
            const falas = [];
            let atual = '';
            blocos.forEach((b) => {
                const frase = /[.!?:]$/.test(b) ? b : b + '.';
                if (atual && (atual.length + frase.length) > 220) { falas.push(atual); atual = ''; }
                atual = atual ? atual + ' ' + frase : frase;
            });
            if (atual) falas.push(atual);
            return falas;
        };

        const ROTULO_OUVIR = '<i class="fa-solid fa-play" aria-hidden="true"></i> Ouvir Página';
        const voltarBotaoOuvir = () => { btnStartVoice.innerHTML = ROTULO_OUVIR; };

        btnStartVoice.addEventListener('click', () => {
            if (!('speechSynthesis' in window)) {
                if (accAnuncio) accAnuncio.textContent = 'A leitura em voz não é suportada neste navegador.';
                btnStartVoice.title = 'Leitura em voz não suportada neste navegador';
                return;
            }
            window.speechSynthesis.cancel();
            const falas = montarFalas(coletarTextoDaPagina());
            if (!falas.length) return;

            btnStartVoice.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Lendo...';
            if (accAnuncio) accAnuncio.textContent = 'Lendo a página.';

            falas.forEach((texto, i) => {
                speechUtterance = new SpeechSynthesisUtterance(texto);
                speechUtterance.lang = 'pt-BR';
                if (i === falas.length - 1) speechUtterance.onend = voltarBotaoOuvir;
                speechUtterance.onerror = voltarBotaoOuvir;
                window.speechSynthesis.speak(speechUtterance);
            });
        });

        btnStopVoice.addEventListener('click', () => {
            if ('speechSynthesis' in window) window.speechSynthesis.cancel();
            voltarBotaoOuvir();
            if (accAnuncio) accAnuncio.textContent = 'Leitura interrompida.';
        });

        const loadPreferences = () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                chkDarkMode.checked = true;
            } else if (savedTheme === 'contrast') {
                document.body.classList.add('high-contrast');
                chkHighContrast.checked = true;
            } else if (!savedTheme && document.body.dataset.temaPadrao === 'escuro') {
                // Sem preferência salva ainda (nem localStorage, nem
                // servidor): esta página específica (Dashboard, via
                // data-tema-padrao="escuro" no <body>) começa em modo
                // escuro. Não grava no localStorage — o padrão não pode
                // "vazar" pras outras telas, que continuam claras por
                // padrão até o usuário escolher um tema de verdade.
                document.body.classList.add('dark-theme');
                chkDarkMode.checked = true;
            }

            const savedFont = localStorage.getItem('fontScale');
            if (savedFont) {
                currentFontScale = parseInt(savedFont, 10);
                aplicarEscalaFonte(currentFontScale);
            }

            const savedZoom = localStorage.getItem('zoomScale');
            if (savedZoom) {
                currentZoomScale = parseFloat(savedZoom);
                aplicarZoomNoHtml(currentZoomScale);
            }
        };
        loadPreferences();
        mostrarValores();

        // localStorage já pintou a página instantaneamente; agora consulta
        // o servidor (se logado) pra sincronizar com o que foi salvo em
        // outro dispositivo.
        carregarPreferenciasDoServidor();
    });
</script>
