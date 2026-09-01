<!-- ==========================================================================
     PAINEL DE ACESSIBILIDADE (COMPONENTE PADRONIZADO E REUTILIZÁVEL)
     Inclua com @@include('partials.acessibilidade') em qualquer página que
     tenha uma navbar fixa. O componente é autossuficiente (CSS + HTML + JS).
     ========================================================================== -->
<style>
    html {
        /* Usamos "zoom" em vez de "transform: scale()" de propósito: um
           transform no <html> cria um novo containing block para todo
           elemento position:fixed da página (navbar, sidebar, modais),
           fazendo-os "descolar" do viewport e sumir em páginas com pouco
           conteúdo. A propriedade zoom escala sem esse efeito colateral. */
        font-size: var(--font-scale, 100%);
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
        color: var(--brand-green-light, #4ade80);
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

    .accessibility-trigger.acc-trigger-inline {
        position: static;
        width: 38px;
        height: 38px;
        font-size: 17px;
        box-shadow: none;
    }

    .accessibility-trigger:hover {
        transform: scale(1.1) rotate(45deg);
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
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

    .accessibility-modal .modal-header h3 {
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

    .accessibility-modal .modal-section h4 {
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
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
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

    .accessibility-modal .voice-start { background: #14532d; }
    .accessibility-modal .voice-start:hover { background: #166534; }
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

    body.high-contrast .accessibility-modal .modal-header h3,
    body.high-contrast .accessibility-modal .modal-close,
    body.high-contrast .accessibility-modal .modal-section h4 {
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

    body.high-contrast .accessibility-modal input:checked + .slider-switch {
        background-color: #ffff00 !important;
    }

    body.high-contrast .accessibility-modal input:checked + .slider-switch:before {
        background-color: #000000 !important;
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
<button class="accessibility-trigger" id="accTrigger" aria-label="Abrir configurações de acessibilidade">
    <i class="fa-solid fa-universal-access"></i>
</button>

<!-- QUADRADO CENTRAL DO PAINEL DE ACESSIBILIDADE -->
<div class="accessibility-modal" id="accModal">
    <div class="modal-header">
        <h3><i class="fa-solid fa-universal-access"></i> Painel de Acessibilidade</h3>
        <button class="modal-close" id="accClose" aria-label="Fechar painel">&times;</button>
    </div>

    <div class="modal-body">
        <div class="modal-section">
            <h4><i class="fa-solid fa-palette"></i> Preferências de Tema</h4>
            <div class="control-grid">
                <div class="control-item">
                    <span class="control-label"><i class="fa-solid fa-moon"></i> Modo Escuro</span>
                    <label class="switch-btn">
                        <input type="checkbox" id="chkDarkMode">
                        <span class="slider-switch"></span>
                    </label>
                </div>
                <div class="control-item">
                    <span class="control-label"><i class="fa-solid fa-circle-half-stroke"></i> Alto Contraste</span>
                    <label class="switch-btn">
                        <input type="checkbox" id="chkHighContrast">
                        <span class="slider-switch"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-section">
            <h4><i class="fa-solid fa-sliders"></i> Dimensionamento</h4>
            <div class="control-grid">
                <div class="control-item-vertical">
                    <span class="control-label"><i class="fa-solid fa-font"></i> Tamanho do Texto</span>
                    <div class="btn-group-row">
                        <button class="acc-btn" id="btnDecreaseFont" title="Diminuir texto"><i class="fa-solid fa-minus"></i></button>
                        <button class="acc-btn btn-secondary" id="btnResetFont" title="Restaurar texto">Padrão</button>
                        <button class="acc-btn" id="btnIncreaseFont" title="Aumentar texto"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
                <div class="control-item-vertical">
                    <span class="control-label"><i class="fa-solid fa-magnifying-glass-plus"></i> Zoom do Sistema</span>
                    <div class="btn-group-row">
                        <button class="acc-btn" id="btnDecreaseZoom" title="Diminuir zoom"><i class="fa-solid fa-minus"></i></button>
                        <button class="acc-btn btn-secondary" id="btnResetZoom" title="Restaurar zoom">Padrão</button>
                        <button class="acc-btn" id="btnIncreaseZoom" title="Aumentar zoom"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-section">
            <h4><i class="fa-solid fa-volume-high"></i> Leitura de Tela (Voz)</h4>
            <div class="voice-box">
                <div class="voice-controls">
                    <button class="acc-btn-voice voice-start" id="btnStartVoice">
                        <i class="fa-solid fa-play"></i> Ouvir Página
                    </button>
                    <button class="acc-btn-voice voice-stop" id="btnStopVoice">
                        <i class="fa-solid fa-stop"></i> Parar Áudio
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
        let currentZoomScale = 1.0;
        let speechUtterance = null;

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

            if (preferencias.tamanho_fonte) {
                currentFontScale = parseInt(preferencias.tamanho_fonte, 10);
                document.documentElement.style.setProperty('--font-scale', `${currentFontScale}%`);
                localStorage.setItem('fontScale', currentFontScale);
            }

            if (preferencias.zoom) {
                currentZoomScale = parseFloat(preferencias.zoom);
                document.documentElement.style.setProperty('--zoom-scale', currentZoomScale);
                localStorage.setItem('zoomScale', currentZoomScale);
            }
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
            document.documentElement.style.setProperty('--font-scale', `${currentFontScale}%`);
            localStorage.setItem('fontScale', currentFontScale);
            salvarPreferenciasNoServidor();
        };
        btnIncreaseFont.addEventListener('click', () => updateFontScale(currentFontScale + 10));
        btnDecreaseFont.addEventListener('click', () => updateFontScale(currentFontScale - 10));
        btnResetFont.addEventListener('click', () => updateFontScale(100));

        const updateZoomScale = (newZoom) => {
            currentZoomScale = Math.max(0.5, Math.min(newZoom, 1.3));
            document.documentElement.style.setProperty('--zoom-scale', currentZoomScale);
            localStorage.setItem('zoomScale', currentZoomScale);
            salvarPreferenciasNoServidor();
        };
        btnIncreaseZoom.addEventListener('click', () => updateZoomScale(currentZoomScale + 0.05));
        btnDecreaseZoom.addEventListener('click', () => updateZoomScale(currentZoomScale - 0.05));
        btnResetZoom.addEventListener('click', () => updateZoomScale(1.0));

        btnStartVoice.addEventListener('click', () => {
            window.speechSynthesis.cancel();
            let textToRead = "";

            const heading = document.querySelector('.header-title h2, .header h1');
            const description = document.querySelector('.header-title p, .header p');

            if (heading) textToRead += heading.innerText + ". ";
            if (description) textToRead += description.innerText + ". ";

            document.querySelectorAll('.premium-card, .card-title').forEach(el => {
                const label = el.querySelector ? el.querySelector('.premium-card-label') : null;
                const value = el.querySelector ? el.querySelector('.premium-card-value') : null;
                if (label && value) {
                    textToRead += label.innerText + ": " + value.innerText + ". ";
                }
            });

            document.querySelectorAll('.alert-item-text h4').forEach(alerta => {
                textToRead += "Alerta: " + alerta.innerText + ". ";
            });

            // Fallback genérico: páginas sem os elementos específicos acima
            // (ex.: a landing page pública) caem aqui, lendo os títulos e
            // parágrafos do conteúdo principal na ordem em que aparecem.
            if (!textToRead.trim()) {
                const raiz = document.querySelector('main, #mainArea, #mainContent') || document.body;
                raiz.querySelectorAll('h1, h2, h3, h4, p, li').forEach(el => {
                    if (el.closest('.accessibility-modal, .accessibility-trigger, nav, .mobile-menu, footer, script, style')) {
                        return;
                    }
                    const texto = el.innerText.trim();
                    if (texto) {
                        textToRead += texto + ". ";
                    }
                });
            }

            if (!textToRead.trim()) return;

            speechUtterance = new SpeechSynthesisUtterance(textToRead);
            speechUtterance.lang = 'pt-BR';

            btnStartVoice.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Lendo...';

            speechUtterance.onend = () => btnStartVoice.innerHTML = '<i class="fa-solid fa-play"></i> Ouvir Página';
            speechUtterance.onerror = () => btnStartVoice.innerHTML = '<i class="fa-solid fa-play"></i> Ouvir Página';

            window.speechSynthesis.speak(speechUtterance);
        });

        btnStopVoice.addEventListener('click', () => {
            window.speechSynthesis.cancel();
            btnStartVoice.innerHTML = '<i class="fa-solid fa-play"></i> Ouvir Página';
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
                document.documentElement.style.setProperty('--font-scale', `${currentFontScale}%`);
            }

            const savedZoom = localStorage.getItem('zoomScale');
            if (savedZoom) {
                currentZoomScale = parseFloat(savedZoom);
                document.documentElement.style.setProperty('--zoom-scale', currentZoomScale);
            }
        };
        loadPreferences();

        // localStorage já pintou a página instantaneamente; agora consulta
        // o servidor (se logado) pra sincronizar com o que foi salvo em
        // outro dispositivo.
        carregarPreferenciasDoServidor();
    });
</script>
