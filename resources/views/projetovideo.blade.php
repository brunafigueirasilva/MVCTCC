<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | Gestão Inteligente de Ração</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

    <!-- ============================================================
         INTRO / SPLASH SCREEN — AGRI FARM (Projeto Vídeo)
         Tudo escopado em #splash: sobrescreve o #splash antigo de
         partials/landing-styles e não vaza estilo pro resto da página.
    ============================================================ -->
    <style>
        #splash {
            --s-verde: #3fbf5f;
            --s-verde-claro: #8fe05a;
            --s-verde-escuro: #0b2a14;
            --s-terra: #c98a3a;
            --s-terra-claro: #e8b45c;
            --s-texto: #eaf7ec;

            /* "inset: 0" (sem 100vh): a landing usa zoom de 90% e as
               unidades vh não acompanham o zoom. */
            position: fixed;
            inset: 0;
            width: auto;
            height: auto;
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--s-texto);
            background:
                radial-gradient(ellipse at 50% 45%, rgba(40, 120, 60, .35) 0%, rgba(8, 32, 15, .9) 45%, #020905 80%),
                #020905;
            transition: transform 1.7s cubic-bezier(.76, 0, .24, 1), opacity 1.7s ease;
        }

        #splash.splash-exit {
            transform: translateY(-100%);
            opacity: .6;
        }

        /* ---------- Fundo tecnológico ---------- */
        #splash .splash-background {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        #splash .splash-grid {
            position: absolute;
            inset: -80px;
            background:
                linear-gradient(rgba(111, 211, 79, .06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(111, 211, 79, .06) 1px, transparent 1px);
            background-size: 60px 60px;
            -webkit-mask-image: radial-gradient(circle at center, #000 0%, transparent 70%);
            mask-image: radial-gradient(circle at center, #000 0%, transparent 70%);
            animation: sGrade 20s linear infinite;
        }

        @keyframes sGrade {
            to { transform: translate(60px, 60px); }
        }

        #splash .splash-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: .55;
            animation: sRespira 6s ease-in-out infinite;
        }

        #splash .splash-glow-1 {
            width: 520px; height: 520px;
            top: 50%; left: 50%;
            margin: -260px 0 0 -260px;
            background: radial-gradient(circle, rgba(63, 191, 95, .45), transparent 70%);
        }

        #splash .splash-glow-2 {
            width: 380px; height: 380px;
            top: -120px; left: -100px;
            background: radial-gradient(circle, rgba(143, 224, 90, .3), transparent 70%);
            animation-delay: -2s;
        }

        #splash .splash-glow-3 {
            width: 360px; height: 360px;
            bottom: -120px; right: -80px;
            background: radial-gradient(circle, rgba(201, 138, 58, .28), transparent 70%);
            animation-delay: -4s;
        }

        @keyframes sRespira {
            0%, 100% { transform: scale(.9); opacity: .4; }
            50% { transform: scale(1.1); opacity: .7; }
        }

        /* Linhas de "varredura" de dados cruzando a tela */
        #splash .tech-lines span {
            position: absolute;
            left: -40%;
            height: 1px;
            width: 40%;
            background: linear-gradient(90deg, transparent, rgba(143, 224, 90, .35), transparent);
            opacity: 0;
            animation: sVarredura 7s linear infinite;
        }

        /* Só duas linhas, bem discretas, longe da marca. */
        #splash .tech-lines span:nth-child(1) { top: 18%; animation-delay: .5s; width: 30%; }
        #splash .tech-lines span:nth-child(2) { top: 84%; animation-delay: 4s; width: 25%; }

        @keyframes sVarredura {
            0% { transform: translateX(0); opacity: 0; }
            15% { opacity: 1; }
            85% { opacity: 1; }
            100% { transform: translateX(350%); opacity: 0; }
        }

        /* Partículas subindo (sementes/dados) */
        #splash .particles i {
            position: absolute;
            bottom: -10px;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--s-verde-claro);
            box-shadow: 0 0 8px rgba(143, 224, 90, .9);
            opacity: 0;
            animation: sSobe 7s linear infinite;
        }

        #splash .particles i:nth-child(odd) { background: var(--s-verde); width: 3px; height: 3px; }
        #splash .particles i:nth-child(5n) { background: var(--s-terra-claro); box-shadow: 0 0 8px rgba(232, 180, 92, .8); }
        #splash .particles i:nth-child(1)  { left: 4%;  animation-delay: 0s;   animation-duration: 8s; }
        #splash .particles i:nth-child(2)  { left: 9%;  animation-delay: 2.1s; }
        #splash .particles i:nth-child(3)  { left: 15%; animation-delay: 4.3s; animation-duration: 9s; }
        #splash .particles i:nth-child(4)  { left: 21%; animation-delay: 1.2s; }
        #splash .particles i:nth-child(5)  { left: 26%; animation-delay: 3.4s; animation-duration: 6s; }
        #splash .particles i:nth-child(6)  { left: 32%; animation-delay: .6s; }
        #splash .particles i:nth-child(7)  { left: 37%; animation-delay: 5.1s; animation-duration: 8.5s; }
        #splash .particles i:nth-child(8)  { left: 43%; animation-delay: 2.8s; }
        #splash .particles i:nth-child(9)  { left: 48%; animation-delay: 1.7s; animation-duration: 9.5s; }
        #splash .particles i:nth-child(10) { left: 53%; animation-delay: 4.6s; }
        #splash .particles i:nth-child(11) { left: 58%; animation-delay: .3s;  animation-duration: 7.5s; }
        #splash .particles i:nth-child(12) { left: 63%; animation-delay: 3.9s; }
        #splash .particles i:nth-child(13) { left: 68%; animation-delay: 2.2s; animation-duration: 8s; }
        #splash .particles i:nth-child(14) { left: 73%; animation-delay: 5.6s; }
        #splash .particles i:nth-child(15) { left: 78%; animation-delay: 1s;   animation-duration: 6.5s; }
        #splash .particles i:nth-child(16) { left: 83%; animation-delay: 3.1s; }
        #splash .particles i:nth-child(17) { left: 87%; animation-delay: 4.9s; animation-duration: 9s; }
        #splash .particles i:nth-child(18) { left: 91%; animation-delay: 1.5s; }
        #splash .particles i:nth-child(19) { left: 95%; animation-delay: 3.6s; animation-duration: 7s; }
        #splash .particles i:nth-child(20) { left: 98%; animation-delay: .9s; }

        @keyframes sSobe {
            0% { transform: translate(0, 0) scale(.6); opacity: 0; }
            12% { opacity: .9; }
            80% { opacity: .5; }
            100% { transform: translate(24px, -105vh) scale(1.1); opacity: 0; }
        }

        /* ---------- Conteúdo central ---------- */
        #splash .splash-content {
            position: relative;
            z-index: 5;
            width: min(680px, 100% - 32px);
            text-align: center;
        }

        #splash .agrifarm-logo {
            position: relative;
            display: inline-block;
            overflow: hidden;
            padding: 4px 10px;
        }

        #splash .logo-word {
            display: flex;
            align-items: baseline;
            justify-content: center;
            font-size: clamp(3.2rem, 11vw, 7.5rem);
            font-weight: 900;
            line-height: 1;
            letter-spacing: .04em;
        }

        #splash .logo-agri,
        #splash .logo-farm {
            display: inline-flex;
            perspective: 600px;
        }

        #splash .logo-agri { filter: drop-shadow(0 0 22px rgba(63, 191, 95, .45)); }
        #splash .logo-farm { filter: drop-shadow(0 0 22px rgba(201, 138, 58, .35)); }

        /* Cada letra entra sozinha, devagar (1,6 s), uma depois da outra
           (--i = posição, 0 a 7): sobe girando do fundo, saindo do desfoque
           — e depois fica "respirando" (flutua de leve, em onda). */
        #splash .letra {
            display: inline-block;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0;
            transform-origin: 50% 100%;
            animation:
                sLetraEntra 1.6s cubic-bezier(.22, 1, .36, 1) forwards calc(.25s + var(--i) * .18s),
                sLetraFlutua 3.2s ease-in-out infinite calc(2.2s + var(--i) * .18s);
        }

        #splash .logo-agri .letra {
            background-image: linear-gradient(180deg, #b5f08a 0%, var(--s-verde) 55%, #1f8a3e 100%);
        }

        #splash .logo-farm .letra {
            background-image: linear-gradient(180deg, #f5d38c 0%, var(--s-terra-claro) 45%, var(--s-terra) 100%);
        }

        @keyframes sLetraEntra {
            0% { opacity: 0; transform: translateY(60px) rotateX(85deg) scale(.7); filter: blur(14px); }
            55% { opacity: 1; filter: blur(0); }
            80% { transform: translateY(-6px) rotateX(-8deg) scale(1.03); }
            100% { opacity: 1; transform: none; filter: blur(0); }
        }

        @keyframes sLetraFlutua {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* Feixe de luz atravessando a marca */
        #splash .logo-light {
            position: absolute;
            top: -20%;
            left: -40%;
            width: 25%;
            height: 140%;
            background: linear-gradient(90deg, transparent, rgba(220, 255, 210, .55), transparent);
            transform: skewX(-20deg);
            filter: blur(3px);
            animation: sLuz 2.6s ease-in-out infinite 3.2s;
            opacity: 0;
        }

        @keyframes sLuz {
            0% { left: -40%; opacity: 0; }
            15% { opacity: 1; }
            60%, 100% { left: 130%; opacity: 0; }
        }

        #splash .logo-subtitle {
            margin-top: 16px;
            font-size: clamp(.78rem, 1.6vw, 1.05rem);
            font-weight: 600;
            letter-spacing: .3em;
            color: var(--s-texto);
            opacity: 0;
            animation: sSobeSuave 1.2s ease forwards 2s;
        }

        #splash .logo-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 20px auto 18px;
            opacity: 0;
            animation: sSobeSuave .8s ease forwards 2.4s;
        }

        #splash .logo-divider span {
            width: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--s-verde-claro));
            animation: sLinha 1.2s ease forwards 2.5s;
        }

        #splash .logo-divider span:last-child {
            background: linear-gradient(90deg, var(--s-verde-claro), transparent);
        }

        #splash .logo-divider b {
            width: 8px;
            height: 8px;
            transform: rotate(45deg);
            background: var(--s-verde-claro);
            box-shadow: 0 0 12px rgba(143, 224, 90, .9);
        }

        @keyframes sLinha {
            to { width: min(140px, 22vw); }
        }

        @keyframes sSobeSuave {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }

        /* ---------- Carregamento ---------- */
        #splash .splash-loader {
            width: min(420px, 100%);
            margin: 34px auto 0;
            opacity: 0;
            animation: sSobeSuave .6s ease forwards .3s;
        }

        #splash .loader-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .68rem;
            letter-spacing: .18em;
        }

        #splash .loader-header {
            margin-bottom: 9px;
            color: rgba(234, 247, 236, .8);
        }

        #splash .loader-header strong {
            color: var(--s-verde-claro);
            font-variant-numeric: tabular-nums;
            letter-spacing: .08em;
        }

        #splash .loader-track {
            position: relative;
            height: 4px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .08);
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(143, 224, 90, .12);
        }

        #splash .loader-progress {
            width: 0;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #1f8a3e, var(--s-verde), var(--s-verde-claro));
            box-shadow: 0 0 14px rgba(143, 224, 90, .8);
            transition: width .12s linear;
        }

        /* ---------- Cantos tecnológicos ---------- */
        #splash .corner {
            position: absolute;
            z-index: 4;
            width: 54px;
            height: 54px;
            border: 0 solid rgba(143, 224, 90, .55);
            opacity: 0;
            animation: sCanto .9s ease forwards .4s;
        }

        #splash .corner-top-left { top: 26px; left: 26px; border-top-width: 2px; border-left-width: 2px; }
        #splash .corner-top-right { top: 26px; right: 26px; border-top-width: 2px; border-right-width: 2px; }
        #splash .corner-bottom-left { bottom: 26px; left: 26px; border-bottom-width: 2px; border-left-width: 2px; }
        #splash .corner-bottom-right { bottom: 26px; right: 26px; border-bottom-width: 2px; border-right-width: 2px; }

        @keyframes sCanto {
            from { opacity: 0; transform: scale(1.4); }
            to { opacity: 1; transform: none; }
        }

        /* ---------- Responsivo ---------- */
        @media (max-width: 600px) {
            #splash .logo-subtitle { letter-spacing: .16em; }
            #splash .corner { width: 32px; height: 32px; }
            #splash .corner-top-left, #splash .corner-top-right { top: 14px; }
            #splash .corner-bottom-left, #splash .corner-bottom-right { bottom: 14px; }
            #splash .corner-top-left, #splash .corner-bottom-left { left: 14px; }
            #splash .corner-top-right, #splash .corner-bottom-right { right: 14px; }
            #splash .splash-glow-1 { width: 320px; height: 320px; margin: -160px 0 0 -160px; }
        }

        #splash .splash-pular {
            position: absolute;
            z-index: 5;
            left: 50%;
            bottom: calc(28px + env(safe-area-inset-bottom, 0px));
            transform: translateX(-50%);
            min-height: 44px;
            padding: 10px 22px;
            border-radius: 999px;
            border: 1px solid rgba(143, 224, 90, .55);
            background: rgba(6, 20, 11, .55);
            color: #eaf7ec;
            font: 600 .875rem/1.2 inherit;
            letter-spacing: .04em;
            cursor: pointer;
            white-space: nowrap;
            transition: background-color .2s ease, border-color .2s ease;
        }

        #splash .splash-pular:hover {
            background: rgba(31, 138, 62, .45);
            border-color: var(--s-verde-claro);
        }

        /* A intro roda completa mesmo com "movimento reduzido" ligado no
           sistema (ver o :not(#splash...) em partials/landing-styles e o
           comentário no script) — Esc pula. */
    </style>

    <div id="splash" role="dialog" aria-label="Abertura AgriFarm">

        <!-- Fundo tecnológico -->
        <div class="splash-background" aria-hidden="true">

            <div class="splash-grid"></div>

            <div class="splash-glow splash-glow-1"></div>
            <div class="splash-glow splash-glow-2"></div>
            <div class="splash-glow splash-glow-3"></div>

            <div class="tech-lines">
                @for ($i = 0; $i < 2; $i++)
                    <span></span>
                @endfor
            </div>

            <!-- Partículas -->
            <div class="particles">
                @for ($i = 0; $i < 20; $i++)
                    <i></i>
                @endfor
            </div>

        </div>

        <!-- Conteúdo central -->
        <div class="splash-content">

            <!-- LOGO PRINCIPAL — cada letra entra sozinha (ver --i no CSS) -->
            <div class="agrifarm-logo" role="img" aria-label="AGRI FARM">
                <div class="logo-word" aria-hidden="true">
                    <span class="logo-agri">
                        @foreach (str_split('AGRI') as $i => $letra)<span class="letra" style="--i: {{ $i }}">{{ $letra }}</span>@endforeach
                    </span>
                    <span class="logo-farm">
                        @foreach (str_split('FARM') as $i => $letra)<span class="letra" style="--i: {{ $i + 4 }}">{{ $letra }}</span>@endforeach
                    </span>
                </div>
                <div class="logo-light"></div>
            </div>

            <div class="logo-subtitle">
                GERENCIAMENTO INTELIGENTE DE RAÇÃO
            </div>

            <div class="logo-divider" aria-hidden="true">
                <span></span>
                <b></b>
                <span></span>
            </div>

            <!-- Carregamento -->
            <div class="splash-loader">
                <div class="loader-header">
                    <span id="loader-status">INICIALIZANDO SISTEMA</span>
                    <strong id="loader-percent">0%</strong>
                </div>
                <div class="loader-track">
                    <div class="loader-progress"></div>
                </div>
            </div>

        </div>

        <!-- Pular a intro: antes só existia o atalho Esc, sem nada na tela
             avisando — e o foco ficava preso na vinheta (diálogo) até ela
             terminar. Recebe o foco ao abrir (a11y-global foca o 1º botão). -->
        <button type="button" class="splash-pular" id="splashPular">
            Pular introdução <span aria-hidden="true">(Esc)</span>
        </button>

        <!-- Cantos tecnológicos -->
        <div class="corner corner-top-left" aria-hidden="true"></div>
        <div class="corner corner-top-right" aria-hidden="true"></div>
        <div class="corner corner-bottom-left" aria-hidden="true"></div>
        <div class="corner corner-bottom-right" aria-hidden="true"></div>

    </div>


@include('partials.landing-nav', ['paginaAtiva' => 'inicio'])

    <section id="hero">
        <video id="hero-video" autoplay muted loop playsinline aria-hidden="true"
            poster="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=1920&q=80">
            <source src="vacas.mp4" type="video/mp4" />
        </video>
        {{-- WCAG 2.2.2: vídeo em loop precisa de um jeito de pausar. --}}
        <button type="button" class="hero-video-controle" id="heroVideoControle" aria-pressed="false">
            <i class="fa-solid fa-pause" aria-hidden="true"></i>
            <span class="hero-video-controle-texto">Pausar vídeo de fundo</span>
        </button>
        <div class="hero-overlay"></div>
        <div class="hero-glow"></div>
        <div class="hero-orb hero-orb--green" aria-hidden="true"></div>
        <div class="hero-content">
          <div class="hero-texto">
            <div class="hero-tag">
                <div class="tag-dot"></div>
                Tecnologia e IoT para Pecuária de Precisão
            </div>
            <h1 class="hero-title">
                <i class="fa-solid fa-seedling accent titulo-icone" aria-hidden="true"></i> Gerenciamento Inteligente<br>
                <span class="accent">de Ração em Fazendas</span>
            </h1>
            <p class="hero-sub">
                Sensores e automação monitoram o consumo alimentar do rebanho em tempo real,
                reduzindo desperdícios, otimizando custos e incrementando a eficiência operacional
                da produção agropecuária.
            </p>
            <div class="hero-btns">
                <a href="{{ route('landing.contato') }}" class="btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width:16px;height:16px;stroke-width:2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Solicitar Demonstração
                </a>
                <a href="{{ route('landing.comoFunciona') }}" class="btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width:16px;height:16px;stroke-width:2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Conhecer a Solução
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-card">
                    <div class="stat-topo">
                        <span class="stat-icone" aria-hidden="true"><i class="fa-solid fa-leaf"></i></span>
                        <div class="stat-value">15%</div>
                    </div>
                    <div class="stat-label">Redução de desperdício</div>
                </div>
                <div class="stat-card">
                    <div class="stat-topo">
                        <span class="stat-icone" aria-hidden="true"><i class="fa-regular fa-clock"></i></span>
                        <div class="stat-value">24/7</div>
                    </div>
                    <div class="stat-label">Monitoramento contínuo</div>
                </div>
                <div class="stat-card">
                    <div class="stat-topo">
                        <span class="stat-icone" aria-hidden="true"><i class="fa-solid fa-cloud"></i></span>
                        <div class="stat-value">100%</div>
                    </div>
                    <div class="stat-label">Dados em tempo real</div>
                </div>
            </div>
          </div>

            <!-- GRÁFICO "ANIMAIS POR LOTE" — dados reais (CadastroController::animaisPorLote),
                 desenhado em SVG e animado pelo script no fim da página. -->
            <aside class="lote-chart" aria-labelledby="loteChartTitulo">
                <div class="lote-chart-head">
                    <span class="lote-chart-icone" aria-hidden="true"><i class="fa-solid fa-chart-pie"></i></span>
                    <div>
                        <h2 id="loteChartTitulo">Animais por Lote <span class="lote-ao-vivo" title="Atualiza sozinho" aria-hidden="true"></span></h2>
                        <p>Distribuição do total de animais entre os lotes cadastrados.</p>
                    </div>
                </div>
                <div class="lote-chart-body">
                    <div class="lote-donut" id="loteDonut">
                        <div class="lote-donut-aura" aria-hidden="true"></div>
                        <svg viewBox="0 0 120 120" aria-hidden="true">
                            <defs id="loteDefs"></defs>
                            <circle class="lote-donut-anel" cx="60" cy="60" r="58" />
                            <g id="loteMarcas"></g>
                            <circle class="lote-donut-trilha" cx="60" cy="60" r="44" />
                            <g id="loteDonutFatias" transform="rotate(-90 60 60)"></g>
                            <g id="loteRotulos"></g>
                        </svg>
                        <div class="lote-donut-centro" aria-live="polite">
                            <div class="centro-total">
                                <span>Total</span>
                                <strong id="loteTotal">0</strong>
                                <span>animais</span>
                            </div>
                            <div class="centro-lote">
                                <span id="loteCentroNome"></span>
                                <strong id="loteCentroPct"></strong>
                                <span id="loteCentroQtd"></span>
                            </div>
                        </div>
                        <div class="lote-tooltip" id="loteTooltip" aria-hidden="true"></div>
                    </div>
                    <ul class="lote-legenda" id="loteLegenda"></ul>
                    <p class="lote-dica" id="loteVazio" role="status" hidden></p>
                </div>
                <p class="lote-dica"><i class="fa-solid fa-hand-pointer" aria-hidden="true"></i> Clique em uma fatia ou na legenda para ver os detalhes do lote.</p>

                <!-- Detalhes do lote clicado (preenchido pelo script do gráfico) -->
                <div class="lote-detalhe" id="loteDetalhe" aria-live="polite">
                    <div class="lote-detalhe-interno" inert>
                        <div class="lote-detalhe-cartao">
                            <div class="lote-detalhe-topo">
                                <span class="ponto" id="loteDetalhePonto"></span>
                                <div>
                                    <h3 id="loteDetalheTitulo"></h3>
                                    <p id="loteDetalheSub"></p>
                                </div>
                                <button type="button" class="lote-detalhe-fechar" id="loteDetalheFechar" aria-label="Fechar detalhes">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <div class="lote-detalhe-participacao">
                                <div class="barra"><i id="loteDetalheBarra"></i></div>
                                <span id="loteDetalheBarraTexto"></span>
                            </div>
                            <dl class="lote-detalhe-grade" id="loteDetalheGrade"></dl>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
        <a href="{{ route('landing.problema') }}" class="scroll-hint" aria-label="Continuar: conhecer o problema que o AgriFarm resolve">
            <span>Continuar</span>
            <svg fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </a>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')

<!-- O botãozinho do assistente de IA só aparece depois que a vinheta
     (#splash) termina de subir e sair da tela — antes disso fica
     invisível pra não competir com a animação de abertura. -->
<style>
    .ai-chat-trigger {
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
    }

    .ai-chat-trigger.assistente-visivel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        transition: opacity 0.5s ease, transform 0.5s ease, visibility 0.5s;
    }
</style>

<!-- HERO DA PÁGINA INICIAL: vídeo em tela cheia como protagonista, texto à
     esquerda e o gráfico "Animais por Lote" (glassmorphism) à direita. Fica
     aqui e não em partials/landing-styles porque só esta página tem #hero. -->
<style>
    #hero {
        --verde-1: #2fbf6b;
        --verde-2: #6fd34f;
        --verde-escuro: #1d6b3a;
        align-items: stretch;
        /* Anula o "section { padding: var(--section-py) 24px }" genérico da
           landing — era ele que deixava um vão grande abaixo da navbar. O
           espaço do topo fica só no padding do .hero-content. */
        padding: 0;
    }

    /* Camada verde translúcida sobre o vídeo: escurece pra leitura sem
       esconder o vídeo, mais densa à esquerda (onde fica o texto). */
    #hero .hero-overlay {
        background:
            linear-gradient(90deg, rgba(3, 20, 9, .82) 0%, rgba(5, 30, 13, .58) 45%, rgba(6, 34, 15, .38) 100%),
            linear-gradient(180deg, rgba(2, 12, 5, .55) 0%, transparent 30%, transparent 70%, rgba(2, 14, 6, .7) 100%);
    }

    /* Botão Pausar/Reproduzir do vídeo: canto inferior esquerdo (o direito
       já tem VLibras, assistente e acessibilidade). */
    #hero .hero-video-controle {
        position: absolute;
        left: clamp(16px, 4vw, 56px);
        bottom: 24px;
        z-index: 5;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .45);
        background: rgba(3, 20, 9, .72);
        color: #ffffff;
        font: inherit;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
    }
    #hero .hero-video-controle:hover { background: rgba(3, 20, 9, .9); }
    body.high-contrast #hero .hero-video-controle { display: none; }

    #hero .hero-glow {
        opacity: .5;
        background:
            radial-gradient(circle at 15% 45%, rgba(47, 191, 107, .18) 0%, transparent 45%),
            radial-gradient(circle at 80% 50%, rgba(111, 211, 79, .14) 0%, transparent 40%);
    }

    #hero .hero-content {
        width: 100%;
        max-width: 1320px;
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr);
        align-items: center;
        /* Conteúdo encostado logo abaixo da navbar (barra de contato 40px +
           nav 66px), em vez de centralizado na altura toda da tela. */
        align-content: start;
        gap: clamp(32px, 5vw, 80px);
        padding: 146px clamp(16px, 4vw, 56px) 90px; /* 106px das barras + 40px */
        text-align: left;
    }

    #hero .hero-tag {
        background: rgba(47, 191, 107, .12);
        border-color: rgba(111, 211, 79, .4);
        color: #9be58a;
    }

    #hero .tag-dot { background: var(--verde-2); }

    #hero .hero-title {
        font-size: clamp(2.1rem, 4vw, 4.2rem);
    }

    #hero .hero-title .accent {
        background: linear-gradient(90deg, #4fd06a, #8fe05a);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    #hero .hero-sub {
        margin: 0 0 36px;
        max-width: 600px;
        color: rgba(235, 247, 238, .85);
        text-shadow: 0 1px 6px rgba(0, 0, 0, .45);
    }

    #hero .hero-btns { justify-content: flex-start; }

    #hero .btn-primary {
        background: linear-gradient(135deg, #3fae4f 0%, #237a3a 100%);
        border: 1px solid rgba(143, 224, 90, .45);
        box-shadow: 0 10px 30px rgba(47, 191, 107, .3), inset 0 1px 0 rgba(255, 255, 255, .18);
    }

    #hero .btn-primary:hover,
    #hero .btn-primary:focus-visible {
        background: linear-gradient(135deg, #52c763 0%, #2b8d44 100%);
        box-shadow: 0 14px 38px rgba(47, 191, 107, .45), inset 0 1px 0 rgba(255, 255, 255, .22);
    }

    #hero .btn-primary:focus-visible { outline-color: #9be58a; }

    #hero .btn-secondary {
        background: rgba(10, 40, 18, .35);
        border-color: rgba(111, 211, 79, .45);
    }

    #hero .btn-secondary:hover,
    #hero .btn-secondary:focus-visible {
        background: rgba(47, 191, 107, .2);
        border-color: rgba(143, 224, 90, .8);
        box-shadow: 0 10px 28px rgba(47, 191, 107, .25);
    }

    #hero .hero-stats {
        max-width: 620px;
        margin: 48px 0 0;
    }

    #hero .stat-card {
        padding: 18px 16px;
        background: rgba(8, 36, 16, .4);
        border-color: rgba(111, 211, 79, .3);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    #hero .stat-topo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #hero .stat-icone {
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        color: #9be58a;
        border: 1px solid rgba(111, 211, 79, .45);
        background: rgba(47, 191, 107, .12);
    }

    #hero .stat-value {
        color: #5fd06b;
        font-size: clamp(1.5rem, 2.2vw, 2rem);
        line-height: 1;
    }

    #hero .stat-label { margin-top: 10px; }

    /* ── Painel do gráfico (glassmorphism + iluminação verde) ── */
    .lote-chart {
        position: relative;
        isolation: isolate;
        /* Card compacto: largura limitada e alinhado à direita da coluna,
           pra não competir com o texto do hero. */
        width: 100%;
        max-width: 440px;
        justify-self: end;
        padding: 18px;
        border-radius: 20px;
        background: linear-gradient(145deg, rgba(20, 70, 34, .45), rgba(6, 28, 13, .6));
        border: 1px solid rgba(111, 211, 79, .3);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        box-shadow:
            0 0 0 1px rgba(255, 255, 255, .04) inset,
            0 24px 60px rgba(0, 0, 0, .35);
        color: #eef8f0;
        /* Escondido até a vinheta subir — aí ganha "entrou" (ver o script
           do gráfico) e sobe crescendo. Só opacity/transform: leve. */
        opacity: 0;
        transform: translateY(70px) scale(.9);
    }

    .lote-chart.entrou {
        animation: loteCardEntra 1.3s cubic-bezier(.16, 1, .3, 1) forwards;
    }

    @keyframes loteCardEntra {
        0% { opacity: 0; transform: translateY(70px) scale(.9); }
        60% { opacity: 1; transform: translateY(-8px) scale(1.02); }
        100% { opacity: 1; transform: none; }
    }

    /* Brilho verde que "acende" uma vez quando o card chega. */
    .lote-chart.entrou::after {
        animation: loteCardBrilho 1.8s ease-out .35s both;
    }

    @keyframes loteCardBrilho {
        0% { opacity: 0; box-shadow: 0 0 0 rgba(47, 191, 107, 0); }
        35% { opacity: 1; box-shadow: 0 0 90px rgba(111, 211, 79, .65); }
        100% { opacity: .8; box-shadow: 0 0 60px rgba(47, 191, 107, .3); }
    }

    /* Feixe de luz verde percorrendo a borda do painel. */
    @property --lote-ang {
        syntax: '<angle>';
        initial-value: 0deg;
        inherits: false;
    }

    .lote-chart::before {
        content: "";
        position: absolute;
        inset: -1px;
        border-radius: inherit;
        padding: 1.5px;
        background: conic-gradient(from var(--lote-ang), transparent 0deg 250deg, rgba(143, 224, 90, .95) 310deg, #d6ffb8 330deg, transparent 360deg);
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
        /* Luz fixa na borda (girar a cada quadro, por cima do vídeo com
           desfoque, deixava a página travando). */
        --lote-ang: 300deg;
        pointer-events: none;
    }

    .sem-transicao, .sem-transicao * { transition: none !important; }

    .lote-chart::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        border-radius: inherit;
        box-shadow: 0 0 60px rgba(47, 191, 107, .3);
        opacity: .8;
        pointer-events: none;
    }

    @keyframes loteRespira {
        0%, 100% { opacity: .45; }
        50% { opacity: 1; }
    }

    .lote-chart-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .lote-chart-icone {
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        color: #9be58a;
        border: 1px solid rgba(111, 211, 79, .45);
        background: rgba(47, 191, 107, .14);
    }

    .lote-chart-head h2 {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .lote-chart-head p {
        font-size: .8rem;
        color: rgba(235, 247, 238, .72);
        margin: 2px 0 0;
    }

    .lote-ao-vivo {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin-left: 6px;
        vertical-align: middle;
        border-radius: 50%;
        background: #6fd34f;
        animation: loteAoVivo 1.8s ease-out infinite;
    }

    @keyframes loteAoVivo {
        0% { box-shadow: 0 0 0 0 rgba(111, 211, 79, .7); }
        100% { box-shadow: 0 0 0 10px rgba(111, 211, 79, 0); }
    }

    .lote-chart-body {
        display: grid;
        grid-template-columns: minmax(130px, 160px) minmax(0, 1fr);
        align-items: center;
        gap: 16px;
    }

    /* ── Rosca ── */
    .lote-donut {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
    }

    .lote-donut svg {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: visible;
    }

    .lote-donut-aura {
        position: absolute;
        inset: 10%;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(111, 211, 79, .32) 0%, rgba(47, 191, 107, .1) 45%, transparent 70%);
        filter: blur(14px);
        opacity: .85;
    }

    .lote-donut-anel {
        fill: none;
        stroke: rgba(143, 224, 90, .35);
        stroke-width: .6;
        stroke-dasharray: 2 4;
        transform-origin: 60px 60px;
        animation: loteGira 40s linear infinite;
    }

    @keyframes loteGira { to { transform: rotate(360deg); } }

    #loteMarcas line {
        stroke: rgba(235, 247, 238, .18);
        stroke-width: .5;
    }

    #loteMarcas line.forte { stroke: rgba(143, 224, 90, .5); }

    .lote-donut-trilha {
        fill: none;
        stroke: rgba(255, 255, 255, .06);
        stroke-width: 22;
    }

    .lote-fatia {
        fill: none;
        stroke-width: 22;
        cursor: pointer;
        transition:
            stroke-dasharray 1.1s cubic-bezier(.22, 1, .36, 1),
            stroke-dashoffset 1.1s cubic-bezier(.22, 1, .36, 1),
            stroke-width .35s cubic-bezier(.22, 1, .36, 1),
            transform .35s cubic-bezier(.22, 1, .36, 1),
            opacity .35s ease,
            filter .35s ease;
    }

    .lote-fatia.ativa {
        stroke-width: 26;
        filter: drop-shadow(0 0 5px rgba(143, 224, 90, .85));
    }

    .lote-fatia.selecionada {
        stroke-width: 28;
        filter: drop-shadow(0 0 8px rgba(214, 255, 184, .95));
    }

    #loteDonutFatias.tem-ativa .lote-fatia:not(.ativa):not(.selecionada) { opacity: .4; }

    /* % escrito em cima de cada fatia (só nas que têm espaço). */
    #loteRotulos text {
        font-size: 5.2px;
        font-weight: 800;
        fill: #fff;
        text-anchor: middle;
        dominant-baseline: central;
        pointer-events: none;
        paint-order: stroke;
        stroke: rgba(4, 25, 10, .55);
        stroke-width: 1.2px;
        opacity: 0;
        transition: opacity .6s ease, transform .35s cubic-bezier(.22, 1, .36, 1);
    }

    #loteRotulos text.visivel { opacity: 1; }

    .lote-donut-centro {
        position: absolute;
        inset: 27%;
        display: grid;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(8, 38, 17, .95), rgba(6, 30, 13, .7));
        box-shadow: inset 0 0 18px rgba(47, 191, 107, .25);
        text-align: center;
        line-height: 1.15;
        overflow: hidden;
        pointer-events: none;
    }

    .centro-total,
    .centro-lote {
        grid-area: 1 / 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        min-width: 0;
        transition: opacity .35s ease, transform .35s cubic-bezier(.22, 1, .36, 1);
    }

    .centro-lote { opacity: 0; transform: translateY(8px) scale(.92); }
    .lote-donut-centro.mostrando-lote .centro-total { opacity: 0; transform: translateY(-8px) scale(.92); }
    .lote-donut-centro.mostrando-lote .centro-lote { opacity: 1; transform: none; }

    .lote-donut-centro span {
        font-size: .7rem;
        color: rgba(235, 247, 238, .75);
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lote-donut-centro strong {
        font-size: clamp(1.05rem, 1.6vw, 1.35rem);
        font-weight: 800;
        color: #fff;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 0 14px rgba(111, 211, 79, .55);
    }

    #loteCentroPct { color: #9be58a; }

    .lote-tooltip {
        position: absolute;
        z-index: 5;
        left: 0;
        top: 0;
        min-width: 150px;
        padding: 10px 12px;
        border-radius: 10px;
        background: rgba(6, 28, 13, .94);
        border: 1px solid rgba(143, 224, 90, .5);
        box-shadow: 0 10px 30px rgba(0, 0, 0, .4), 0 0 18px rgba(47, 191, 107, .3);
        color: #eef8f0;
        font-size: .78rem;
        line-height: 1.45;
        pointer-events: none;
        opacity: 0;
        transform: translate(-50%, calc(-100% - 14px)) scale(.95);
        transition: opacity .18s ease, transform .18s ease;
        white-space: nowrap;
    }

    .lote-tooltip.visivel {
        opacity: 1;
        transform: translate(-50%, calc(-100% - 14px)) scale(1);
    }

    .lote-tooltip b { display: block; color: #fff; font-size: .85rem; margin-bottom: 2px; }
    .lote-tooltip em { font-style: normal; color: #9be58a; font-weight: 700; }
    .lote-tooltip small { display: block; margin-top: 4px; color: rgba(235, 247, 238, .55); font-size: .7rem; }

    /* ── Legenda ── */
    .lote-legenda {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .lote-legenda li {
        display: grid;
        grid-template-columns: 12px minmax(0, 1fr) auto;
        align-items: center;
        column-gap: 10px;
        row-gap: 6px;
        font-size: .85rem;
        padding: 7px 9px;
        border-radius: 10px;
        border: 1px solid transparent;
        cursor: pointer;
        opacity: 0;
        transform: translateX(14px);
        transition: opacity .5s ease, transform .5s cubic-bezier(.22, 1, .36, 1), background .25s ease, border-color .25s ease;
    }

    .lote-legenda li.visivel { opacity: 1; transform: none; }

    .lote-legenda li.ativa,
    .lote-legenda li:focus-visible {
        background: rgba(47, 191, 107, .14);
        border-color: rgba(143, 224, 90, .35);
        outline: none;
    }

    .lote-legenda li.selecionada {
        background: rgba(47, 191, 107, .22);
        border-color: rgba(143, 224, 90, .7);
        box-shadow: 0 0 16px rgba(47, 191, 107, .25);
    }

    .lote-legenda li.visivel.ativa,
    .lote-legenda li.visivel.selecionada { transform: translateX(-4px); }

    .lote-legenda .ponto {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        transition: background .6s ease, box-shadow .3s ease;
    }

    .lote-legenda li.ativa .ponto,
    .lote-legenda li.selecionada .ponto { box-shadow: 0 0 0 4px rgba(143, 224, 90, .25), 0 0 10px currentColor; }

    /* Nome inteiro do lote (quebra linha se precisar) em vez de "…". */
    .lote-legenda .nome {
        overflow-wrap: anywhere;
        line-height: 1.25;
        color: rgba(240, 250, 242, .92);
    }

    .lote-legenda .valor {
        font-weight: 700;
        color: #fff;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .lote-legenda .barra {
        grid-column: 2 / -1;
        height: 4px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .08);
        overflow: hidden;
    }

    .lote-legenda .barra i {
        display: block;
        height: 100%;
        width: 0;
        border-radius: inherit;
        transition: width 1.1s cubic-bezier(.22, 1, .36, 1), background .6s ease;
    }

    .lote-dica {
        margin: 14px 0 0;
        font-size: .74rem;
        color: rgba(235, 247, 238, .6);
        text-align: center;
    }

    .lote-dica i { color: #9be58a; margin-right: 4px; }

    /* ── Painel de detalhes (abre ao clicar numa fatia) ── */
    .lote-detalhe {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows .5s cubic-bezier(.22, 1, .36, 1), margin-top .5s ease;
    }

    .lote-detalhe.aberto {
        grid-template-rows: 1fr;
        margin-top: 14px;
    }

    .lote-detalhe-interno { overflow: hidden; min-height: 0; }

    .lote-detalhe-cartao {
        padding: 16px;
        border-radius: 16px;
        background: linear-gradient(160deg, rgba(47, 191, 107, .14), rgba(6, 28, 13, .55));
        border: 1px solid rgba(143, 224, 90, .35);
        opacity: 0;
        transform: translateY(-8px);
        transition: opacity .4s ease .1s, transform .45s cubic-bezier(.22, 1, .36, 1) .1s;
    }

    .lote-detalhe.aberto .lote-detalhe-cartao { opacity: 1; transform: none; }

    .lote-detalhe-topo {
        display: grid;
        grid-template-columns: 14px minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
    }

    .lote-detalhe-topo .ponto {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        box-shadow: 0 0 10px currentColor;
    }

    .lote-detalhe-topo h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #fff;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lote-detalhe-topo p {
        margin: 1px 0 0;
        font-size: .76rem;
        color: rgba(235, 247, 238, .65);
    }

    .lote-detalhe-fechar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid rgba(143, 224, 90, .35);
        background: rgba(6, 28, 13, .5);
        color: #d6ffb8;
        cursor: pointer;
        transition: background .2s ease, transform .2s ease;
    }

    .lote-detalhe-fechar:hover,
    .lote-detalhe-fechar:focus-visible {
        background: rgba(47, 191, 107, .3);
        transform: rotate(90deg);
        outline: none;
    }

    .lote-detalhe-participacao {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        gap: 10px;
        margin: 12px 0;
        font-size: .76rem;
        color: rgba(235, 247, 238, .75);
    }

    .lote-detalhe-participacao .barra {
        height: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .08);
        overflow: hidden;
    }

    .lote-detalhe-participacao .barra i {
        display: block;
        height: 100%;
        width: 0;
        border-radius: inherit;
        box-shadow: 0 0 12px rgba(143, 224, 90, .6);
        transition: width .9s cubic-bezier(.22, 1, .36, 1);
    }

    .lote-detalhe-grade {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(118px, 100%), 1fr));
        gap: 8px;
        margin: 0;
    }

    .lote-detalhe-grade div {
        padding: 9px 10px;
        border-radius: 10px;
        background: rgba(6, 28, 13, .45);
        border: 1px solid rgba(143, 224, 90, .14);
        opacity: 0;
        transform: translateY(6px);
        animation: loteItemEntra .45s cubic-bezier(.22, 1, .36, 1) forwards;
        animation-delay: calc(var(--i, 0) * 45ms + 150ms);
    }

    @keyframes loteItemEntra { to { opacity: 1; transform: none; } }

    .lote-detalhe-grade dt {
        font-size: .66rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: rgba(235, 247, 238, .55);
    }

    .lote-detalhe-grade dt i { color: #9be58a; margin-right: 4px; }

    .lote-detalhe-grade dd {
        margin: 3px 0 0;
        font-size: .92rem;
        font-weight: 700;
        color: #fff;
    }

    .lote-detalhe-grade .especies { grid-column: 1 / -1; }

    .lote-detalhe-grade .especies dd { font-weight: 500; font-size: .82rem; }

    .lote-detalhe-grade .especie-linha {
        display: grid;
        grid-template-columns: 90px 1fr auto;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }

    .lote-detalhe-grade .especie-linha b {
        height: 6px;
        border-radius: 999px;
        background: linear-gradient(90deg, #1f9d55, #8fe05a);
    }

    body.high-contrast .lote-chart {
        background: #000;
        border-color: #fff;
        box-shadow: none;
        backdrop-filter: none;
    }

    body.high-contrast .lote-chart::before,
    body.high-contrast .lote-chart::after,
    body.high-contrast .lote-donut-aura { display: none; }

    body.high-contrast .lote-detalhe-cartao,
    body.high-contrast .lote-detalhe-grade div { background: #000; border-color: #fff; }

    /* Desktop estreito / tablet: gráfico vai pra baixo do texto. */
    @media (max-width: 1024px) {
        #hero .hero-content {
            grid-template-columns: 1fr;
            padding-top: 146px;
        }

        .lote-chart {
            max-width: 440px;
            width: 100%;
            justify-self: start;
        }
    }

    @media (max-width: 768px) {
        #hero .hero-content {
            text-align: center;
            padding: 146px 16px 90px;
        }

        #hero .hero-sub { margin-left: auto; margin-right: auto; }
        #hero .hero-btns { justify-content: center; }
        #hero .hero-stats { margin: 36px auto 0; }
        #hero .stat-topo { justify-content: center; }

        .lote-chart {
            margin: 0 auto;
            padding: 16px 14px;
            text-align: left;
            justify-self: center;
        }
    }

    /* Abaixo de 576px a barra de contato some e a nav sobe pro topo (66px). */
    @media (max-width: 576px) {
        #hero .hero-content { padding-top: 106px; }
    }

    @media (max-width: 520px) {
        .lote-chart-body { grid-template-columns: 1fr; }
        .lote-donut { max-width: 170px; margin: 0 auto; }
    }
</style>

@include('partials.landing-scripts')

<script>
    // Intro (#splash): barra de carregamento de 0 a 100% com mensagens de
    // status; ao terminar, a intro sobe, libera a rolagem, mostra o
    // assistente de IA e avisa o gráfico "Animais por Lote" (vinheta:fim).
    document.addEventListener("DOMContentLoaded", function () {
        const splash = document.getElementById("splash");
        if (!splash) return;

        const progressBar = splash.querySelector(".loader-progress");
        const percent = document.getElementById("loader-percent");
        const status = document.getElementById("loader-status");
        let progresso = 0;
        let finalizada = false;

        const mensagens = [
            { limite: 15, texto: "INICIALIZANDO SISTEMA" },
            { limite: 30, texto: "CONECTANDO SENSORES IoT" },
            { limite: 50, texto: "SINCRONIZANDO DADOS" },
            { limite: 70, texto: "ANALISANDO INFORMAÇÕES" },
            { limite: 85, texto: "OTIMIZANDO PRODUÇÃO" },
            { limite: 100, texto: "SISTEMA PRONTO" }
        ];

        function atualizarStatus(valor) {
            let mensagemAtual = mensagens[0].texto;
            mensagens.forEach(function (item) {
                if (valor >= item.limite) mensagemAtual = item.texto;
            });
            if (status) status.textContent = mensagemAtual;
        }

        function finalizarSplash() {
            if (finalizada) return;
            finalizada = true;
            clearInterval(intervalo);
            splash.classList.add("splash-exit");
            // O foco estava na vinheta (botão "Pular"): passa pro conteúdo,
            // senão ele se perde no <body> quando a vinheta some.
            if (splash.contains(document.activeElement)) {
                const conteudo = document.getElementById("conteudo-principal");
                if (conteudo) conteudo.focus({ preventScroll: true });
            }
            splash.setAttribute("aria-hidden", "true");
            // Avisa que a intro começou a subir (o card do gráfico entra junto).
            document.dispatchEvent(new Event("vinheta:saindo"));

            setTimeout(function () {
                splash.style.display = "none";
                document.body.style.overflow = "auto";

                const trigger = document.querySelector(".ai-chat-trigger");
                if (trigger) trigger.classList.add("assistente-visivel");

                document.dispatchEvent(new Event("vinheta:fim"));
            }, 1800);
        }

        // A barra começa logo (0,5 s), enquanto as letras ainda estão entrando.
        let intervalo = null;
        const inicio = setTimeout(function () {
            intervalo = setInterval(function () {
                progresso++;
                if (progressBar) progressBar.style.width = progresso + "%";
                if (percent) percent.textContent = progresso + "%";
                atualizarStatus(progresso);

                if (progresso >= 100) {
                    clearInterval(intervalo);
                    setTimeout(finalizarSplash, 700); // segura o "SISTEMA PRONTO" na tela
                }
            }, 28); // 100 passos x 28 ms = 2,8 s de carregamento
        }, 500);

        document.body.style.overflow = "hidden";

        // Esc ou o botão "Pular introdução" pulam a intro (teclado/toque).
        const pularIntro = function () {
            clearTimeout(inicio);
            finalizarSplash();
        };
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") pularIntro();
        });
        const botaoPular = document.getElementById("splashPular");
        if (botaoPular) botaoPular.addEventListener("click", pularIntro);

        // Sem atalho para "movimento reduzido": em muitos computadores (ex.:
        // Windows com "Mostrar animações" desligado) essa preferência vem
        // ligada sem a pessoa saber, e a intro fechava em 0,3 s sem ser vista.
        // Quem quiser pular, usa Esc.
    });
</script>

<script>
    // Gráfico "Animais por Lote": rosca em SVG (uma <circle> por fatia,
    // desenhada com stroke-dasharray/dashoffset — as transições CSS de
    // .lote-fatia animam a entrada e qualquer atualização). Dados reais do
    // backend (CadastroController::animaisPorLote), reconsultados a cada 30s.
    //
    // Interação:
    //  - passar o mouse numa fatia/legenda: fatia salta, centro mostra o lote
    //    e um tooltip segue o mouse;
    //  - CLICAR numa fatia/legenda: abre o painel de detalhes do lote abaixo
    //    do gráfico (clicar de novo, no X ou Esc fecha);
    //  - sem interação, o destaque percorre os lotes sozinho.
    (function () {
        const $ = (id) => document.getElementById(id);
        const grupo = $('loteDonutFatias');
        const legenda = $('loteLegenda');
        const totalEl = $('loteTotal');
        const donut = $('loteDonut');
        if (!grupo || !legenda || !totalEl || !donut) return;

        const defs = $('loteDefs');
        const marcas = $('loteMarcas');
        const rotulos = $('loteRotulos');
        const centro = donut.querySelector('.lote-donut-centro');
        const tooltip = $('loteTooltip');
        const detalhe = $('loteDetalhe');

        const SVG_NS = 'http://www.w3.org/2000/svg';
        const RAIO = 44;
        const CIRC = 2 * Math.PI * RAIO;
        const SALTO = 3.5;
        const SALTO_SELECIONADO = 5.5;
        // Pares [cor base, cor clara] — tons de verde (e um terra) da AgriFarm.
        const CORES = [
            ['#1f9d55', '#5fe08a'],
            ['#5cbf2a', '#b8f27a'],
            ['#137a3c', '#36b866'],
            ['#8fcf3c', '#dcf7a0'],
            ['#a8742c', '#e8b45c'],
            ['#2fa38a', '#8fe3c8'],
        ];
        const URL_DADOS = @json(route('projeto.animaisPorLote'));
        const fmt = new Intl.NumberFormat('pt-BR');
        const fmtDec = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 });

        let dados = @json($animaisPorLote);
        let assinaturaAtual = JSON.stringify(dados);
        let iniciado = false;
        let instantaneo = false;
        let hover = -1;
        let selecionado = -1;
        let indiceCiclo = -1;
        let meios = [];

        // Degradê de cada cor (claro → base) e marcas de régua em volta.
        CORES.forEach(([base, clara], i) => {
            const g = document.createElementNS(SVG_NS, 'linearGradient');
            g.id = 'loteGrad' + i;
            ['x1', 'y1', 'x2', 'y2'].forEach((a, k) => g.setAttribute(a, k < 2 ? '0' : '1'));
            g.innerHTML = `<stop offset="0%" stop-color="${clara}"/><stop offset="100%" stop-color="${base}"/>`;
            defs.appendChild(g);
        });

        for (let k = 0; k < 60; k++) {
            const ang = (k / 60) * 2 * Math.PI;
            const forte = k % 5 === 0;
            const r1 = forte ? 51.5 : 52.5;
            const l = document.createElementNS(SVG_NS, 'line');
            l.setAttribute('x1', 60 + r1 * Math.sin(ang));
            l.setAttribute('y1', 60 - r1 * Math.cos(ang));
            l.setAttribute('x2', 60 + 54.5 * Math.sin(ang));
            l.setAttribute('y2', 60 - 54.5 * Math.cos(ang));
            if (forte) l.setAttribute('class', 'forte');
            marcas.appendChild(l);
        }

        function animarNumero(el, de, ate, sufixo) {
            if (instantaneo) {
                el.textContent = fmt.format(ate) + (sufixo ? sufixo(1) : '');
                return;
            }
            const inicio = performance.now();
            const passo = (agora) => {
                const p = Math.min((agora - inicio) / 1100, 1);
                const e = 1 - Math.pow(1 - p, 3);
                el.textContent = fmt.format(Math.round(de + (ate - de) * e)) + (sufixo ? sufixo(e) : '');
                if (p < 1) requestAnimationFrame(passo);
            };
            requestAnimationFrame(passo);
        }

        const lotes = () => dados.lotes || [];

        // No primeiro desenho (instantaneo) roda na hora; depois, sem atraso.
        const agendar = (fn, atraso) => (instantaneo ? fn() : setTimeout(fn, iniciado ? 0 : atraso));

        /** Aplica destaque (hover) e seleção (clique) às fatias/legenda/centro. */
        function pintar() {
            const foco = hover >= 0 ? hover : selecionado;

            Array.from(grupo.children).forEach((fatia, i) => {
                const sel = i === selecionado;
                const ativa = i === hover;
                fatia.classList.toggle('ativa', ativa);
                fatia.classList.toggle('selecionada', sel);
                const d = sel ? SALTO_SELECIONADO : (ativa ? SALTO : 0);
                const ang = meios[i] || 0;
                // Dentro do <g> girado -90°: ângulo 0 = direita, sentido horário.
                fatia.style.transform = d ? `translate(${(Math.cos(ang) * d).toFixed(2)}px, ${(Math.sin(ang) * d).toFixed(2)}px)` : '';
                const rot = rotulos.children[i];
                if (rot) rot.style.transform = d ? `translate(${(Math.sin(ang) * d).toFixed(2)}px, ${(-Math.cos(ang) * d).toFixed(2)}px)` : '';
            });
            grupo.classList.toggle('tem-ativa', foco >= 0);

            Array.from(legenda.children).forEach((item, i) => {
                item.classList.toggle('ativa', i === hover);
                item.classList.toggle('selecionada', i === selecionado);
                item.setAttribute('aria-pressed', i === selecionado ? 'true' : 'false');
            });

            const lote = lotes()[foco];
            if (lote) {
                $('loteCentroNome').textContent = lote.nome;
                $('loteCentroPct').textContent = lote.percentual + '%';
                $('loteCentroQtd').textContent = fmt.format(lote.quantidade) + ' animais';
            }
            centro.classList.toggle('mostrando-lote', !!lote);
        }

        function mostrarTooltip(i, evento) {
            const lote = lotes()[i];
            if (!lote) return;
            tooltip.innerHTML = '';
            const b = document.createElement('b'); b.textContent = lote.nome;
            const q = document.createElement('div'); q.textContent = fmt.format(lote.quantidade) + ' animais';
            const p = document.createElement('em'); p.textContent = lote.percentual + '% do total';
            const s = document.createElement('small'); s.textContent = i === selecionado ? 'Clique para fechar' : 'Clique para ver detalhes';
            tooltip.append(b, q, p, s);

            const caixa = donut.getBoundingClientRect();
            const escala = caixa.width / donut.offsetWidth || 1; // página tem zoom de 90%
            tooltip.style.left = ((evento.clientX - caixa.left) / escala) + 'px';
            tooltip.style.top = ((evento.clientY - caixa.top) / escala) + 'px';
            tooltip.classList.add('visivel');
        }

        function esconderTooltip() { tooltip.classList.remove('visivel'); }

        function entrar(i) { hover = i; pintar(); }
        function sair() { hover = -1; esconderTooltip(); pintar(); }

        function selecionar(i) {
            selecionado = selecionado === i ? -1 : i;
            pintar();
            if (selecionado >= 0) abrirDetalhe(selecionado); else fecharDetalhe();
        }

        // ---------- Painel de detalhes ----------
        function item(icone, rotulo, valor, i, classe) {
            const div = document.createElement('div');
            if (classe) div.className = classe;
            div.style.setProperty('--i', i);
            const dt = document.createElement('dt');
            dt.innerHTML = `<i class="fa-solid ${icone}" aria-hidden="true"></i>`;
            dt.append(rotulo);
            const dd = document.createElement('dd');
            if (valor instanceof Node) dd.append(valor); else dd.textContent = valor;
            div.append(dt, dd);
            return div;
        }

        function abrirDetalhe(i) {
            const lote = lotes()[i];
            if (!lote) return;
            const d = lote.detalhes || {};
            const [base, clara] = CORES[i % CORES.length];
            const ponto = $('loteDetalhePonto');
            ponto.style.background = `linear-gradient(135deg, ${clara}, ${base})`;
            ponto.style.color = clara;
            $('loteDetalheBarra').style.width = '0';
            $('loteDetalheBarra').style.background = `linear-gradient(90deg, ${base}, ${clara})`;

            const grade = $('loteDetalheGrade');
            grade.innerHTML = '';
            const itens = [];

            // Fatia agregada: "Outros" ou, para visitantes, uma espécie
            // (somando lotes de todas as contas, sem identificar nenhum).
            if (lote.nome === 'Outros' || d.agrupados !== undefined) {
                $('loteDetalheTitulo').textContent = lote.nome === 'Outros' ? 'Outros' : lote.nome;
                $('loteDetalheSub').textContent = `${fmt.format(d.agrupados || 0)} lote(s) somados`;
                itens.push(['fa-layer-group', 'Lotes agrupados', fmt.format(d.agrupados || 0)]);
                itens.push(['fa-cow', 'Animais', fmt.format(lote.quantidade)]);
                itens.push(['fa-weight-scale', 'Peso médio', fmtDec.format(d.peso_medio || 0) + ' kg']);
                itens.push(['fa-bowl-food', 'Cochos', fmt.format(d.cochos || 0)]);

                const lista = document.createElement('div');
                const maior = Math.max(1, ...(d.especies || []).map((e) => e.animais));
                (d.especies || []).forEach((e) => {
                    const linha = document.createElement('div');
                    linha.className = 'especie-linha';
                    const nome = document.createElement('span'); nome.textContent = e.especie || '—';
                    const barra = document.createElement('b'); barra.style.width = Math.max(4, (e.animais / maior) * 100) + '%';
                    const qtd = document.createElement('span'); qtd.textContent = fmt.format(e.animais);
                    linha.append(nome, barra, qtd);
                    lista.append(linha);
                });
                itens.push(['fa-paw', 'Espécies', lista, 'especies']);
            } else {
                $('loteDetalheTitulo').textContent = d.lote || lote.nome;
                $('loteDetalheSub').textContent = [d.especie, d.finalidade, d.sexo].filter(Boolean).join(' · ');
                const pesoTotal = (d.peso_medio || 0) * lote.quantidade;
                itens.push(['fa-cow', 'Animais', fmt.format(lote.quantidade)]);
                itens.push(['fa-paw', 'Espécie', d.especie || '—']);
                if (d.raca) itens.push(['fa-dna', 'Raça', d.raca]);
                itens.push(['fa-bullseye', 'Finalidade', d.finalidade || '—']);
                itens.push(['fa-venus-mars', 'Sexo', d.sexo || '—']);
                itens.push(['fa-hourglass-half', 'Idade média', d.idade_media || '—']);
                itens.push(['fa-weight-scale', 'Peso médio', fmtDec.format(d.peso_medio || 0) + ' kg']);
                itens.push(['fa-scale-balanced', 'Peso do lote', pesoTotal >= 1000 ? fmtDec.format(pesoTotal / 1000) + ' t' : fmtDec.format(pesoTotal) + ' kg']);
                itens.push(['fa-bowl-food', 'Cochos', fmt.format(d.cochos || 0)]);
                if (d.cochos > 0) itens.push(['fa-users', 'Animais por cocho', fmtDec.format(lote.quantidade / d.cochos)]);
                if (d.dias_no_sistema !== null && d.dias_no_sistema !== undefined) {
                    itens.push(['fa-calendar-days', 'No sistema há', d.dias_no_sistema === 1 ? '1 dia' : fmt.format(d.dias_no_sistema) + ' dias']);
                }
            }

            itens.forEach(([icone, rotulo, valor, classe], k) => grade.append(item(icone, rotulo, valor, k, classe)));
            $('loteDetalheBarraTexto').textContent = `${lote.percentual}% de ${fmt.format(dados.total || 0)} animais`;

            detalhe.classList.add('aberto');
            // Recolhido, o cartão fica "inert" (o "Fechar" invisível não
            // recebe Tab nem é lido); aberto, volta a ser navegável.
            detalhe.querySelector('.lote-detalhe-interno').inert = false;
            requestAnimationFrame(() => requestAnimationFrame(() => {
                $('loteDetalheBarra').style.width = lote.percentual + '%';
            }));
        }

        function fecharDetalhe() {
            detalhe.classList.remove('aberto');
            detalhe.querySelector('.lote-detalhe-interno').inert = true;
        }

        $('loteDetalheFechar').addEventListener('click', () => {
            selecionado = -1; pintar(); fecharDetalhe();
            // O botão some junto com o cartão: o foco volta pra legenda.
            $('loteLegenda').querySelector('button, [tabindex="0"], a')?.focus();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && selecionado >= 0) { selecionado = -1; pintar(); fecharDetalhe(); }
        });

        // ---------- Desenho ----------
        function render(novos) {
            const lista = novos.lotes || [];
            const total = novos.total || 0;
            meios = [];

            // Sem dados: diz o porquê (sem fazenda escolhida ou sem animais).
            const vazio = document.getElementById('loteVazio');
            if (vazio) {
                vazio.hidden = lista.length > 0;
                vazio.textContent = novos.fazenda === null
                    ? 'Selecione uma fazenda no Dashboard para ver os lotes dela aqui.'
                    : 'Nenhum animal cadastrado nos lotes ativos.';
            }

            let acumulado = 0;
            lista.forEach((lote, i) => {
                let fatia = grupo.children[i];
                if (!fatia || fatia.dataset.saindo) {
                    fatia = document.createElementNS(SVG_NS, 'circle');
                    fatia.setAttribute('class', 'lote-fatia');
                    fatia.setAttribute('cx', 60);
                    fatia.setAttribute('cy', 60);
                    fatia.setAttribute('r', RAIO);
                    fatia.style.strokeDasharray = `0 ${CIRC}`;
                    fatia.style.strokeDashoffset = -CIRC * (acumulado / (total || 1));
                    fatia.addEventListener('mouseenter', (e) => { entrar(i); mostrarTooltip(i, e); });
                    fatia.addEventListener('mousemove', (e) => mostrarTooltip(i, e));
                    fatia.addEventListener('mouseleave', sair);
                    fatia.addEventListener('click', (e) => { selecionar(i); mostrarTooltip(i, e); });
                    grupo.insertBefore(fatia, grupo.children[i] || null);
                    fatia.getBoundingClientRect();
                }
                fatia.style.stroke = `url(#loteGrad${i % CORES.length})`;

                const fracao = total > 0 ? lote.quantidade / total : 0;
                const tamanho = fracao * CIRC;
                const folga = lista.length > 1 && tamanho > 4 ? 2 : 0;
                const inicio = total > 0 ? acumulado / total : 0;
                meios[i] = (inicio + fracao / 2) * 2 * Math.PI;
                acumulado += lote.quantidade;

                // % em cima da fatia (posição fora do <g> girado: 0 = topo).
                let rot = rotulos.children[i];
                if (!rot) {
                    rot = document.createElementNS(SVG_NS, 'text');
                    rotulos.appendChild(rot);
                }
                const meio = meios[i];
                rot.setAttribute('x', (60 + RAIO * Math.sin(meio)).toFixed(2));
                rot.setAttribute('y', (60 - RAIO * Math.cos(meio)).toFixed(2));
                rot.textContent = lote.percentual + '%';

                agendar(() => {
                    fatia.style.strokeDasharray = `${Math.max(tamanho - folga, 0)} ${CIRC}`;
                    fatia.style.strokeDashoffset = -(inicio * CIRC);
                    rot.classList.toggle('visivel', lote.percentual >= 7);
                }, 120 + i * 160);
            });

            Array.from(grupo.children).slice(lista.length).forEach((fatia) => {
                fatia.dataset.saindo = '1';
                fatia.style.strokeDasharray = `0 ${CIRC}`;
                setTimeout(() => fatia.remove(), 1100);
            });
            Array.from(rotulos.children).slice(lista.length).forEach((r) => r.remove());

            lista.forEach((lote, i) => {
                let li = legenda.children[i];
                if (!li) {
                    li = document.createElement('li');
                    li.tabIndex = 0;
                    li.setAttribute('role', 'button');
                    li.innerHTML = '<span class="ponto"></span><span class="nome"></span><span class="valor"></span><span class="barra"><i></i></span>';
                    li.dataset.qtd = '0';
                    li.dataset.pct = '0';
                    li.addEventListener('mouseenter', () => entrar(i));
                    li.addEventListener('mouseleave', sair);
                    li.addEventListener('focus', () => entrar(i));
                    li.addEventListener('blur', sair);
                    li.addEventListener('click', () => selecionar(i));
                    li.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selecionar(i); }
                    });
                    legenda.appendChild(li);
                }
                const [base, clara] = CORES[i % CORES.length];
                const ponto = li.querySelector('.ponto');
                ponto.style.background = `linear-gradient(135deg, ${clara}, ${base})`;
                ponto.style.color = clara;
                li.querySelector('.barra i').style.background = `linear-gradient(90deg, ${base}, ${clara})`;
                li.querySelector('.nome').textContent = lote.nome;
                li.title = lote.nome + ' — clique para ver detalhes';

                const valorEl = li.querySelector('.valor');
                const barra = li.querySelector('.barra i');
                const qtdAnt = Number(li.dataset.qtd);
                const pctAnt = Number(li.dataset.pct);
                li.dataset.qtd = lote.quantidade;
                li.dataset.pct = lote.percentual;

                agendar(() => {
                    li.classList.add('visivel');
                    barra.style.width = lote.percentual + '%';
                    animarNumero(valorEl, qtdAnt, lote.quantidade, (e) => ` (${Math.round(pctAnt + (lote.percentual - pctAnt) * e)}%)`);
                }, 300 + i * 160);
            });

            Array.from(legenda.children).slice(lista.length).forEach((li) => {
                li.classList.remove('visivel');
                setTimeout(() => li.remove(), 500);
            });

            animarNumero(totalEl, Number(totalEl.dataset.valor || 0), total);
            totalEl.dataset.valor = total;

            if (selecionado >= lista.length) { selecionado = -1; fecharDetalhe(); }
            pintar();
            if (selecionado >= 0) abrirDetalhe(selecionado);

            iniciado = true;
        }

        // Destaque automático enquanto ninguém interage (e nada aberto).
        function ciclo() {
            if (hover >= 0 || selecionado >= 0 || document.hidden) return;
            const qtd = lotes().length;
            if (!qtd) return;
            indiceCiclo = indiceCiclo + 1 > qtd ? 0 : indiceCiclo + 1;
            if (indiceCiclo === qtd) {
                centro.classList.remove('mostrando-lote');
                Array.from(grupo.children).forEach((f) => { f.classList.remove('ativa'); f.style.transform = ''; });
                Array.from(rotulos.children).forEach((r) => { r.style.transform = ''; });
                Array.from(legenda.children).forEach((li) => li.classList.remove('ativa'));
                grupo.classList.remove('tem-ativa');
                return;
            }
            hover = indiceCiclo;
            pintar();
            hover = -1; // o ciclo não "prende" o mouse
        }

        async function atualizar() {
            if (document.hidden) return;
            try {
                const resposta = await fetch(URL_DADOS, { headers: { 'Accept': 'application/json' } });
                if (!resposta.ok) return;
                const novos = await resposta.json();
                const assinatura = JSON.stringify(novos);
                if (assinatura === assinaturaAtual) return;
                assinaturaAtual = assinatura;
                dados = novos;
                render(dados);
            } catch (e) {
                // Sem rede: mantém o gráfico com os últimos dados válidos.
            }
        }

        // Entrada do card: começa escondido e entra quando a vinheta começa a
        // subir (evento "vinheta:saindo") — o card sobe crescendo com um
        // brilho, e logo depois as fatias se desenham, a legenda entra e os
        // números contam. Só transform/opacity (leve, não trava a página).
        const card = donut.closest('.lote-chart');
        let entrou = false;

        function iniciar() {
            if (entrou) return;
            entrou = true;
            card.classList.add('entrou');
            setTimeout(() => render(dados), 450);
            setInterval(atualizar, 15000);
            // Voltou para a página (histórico do navegador ou outra aba):
            // busca de novo na hora — lote criado/editado/excluído aparece já.
            window.addEventListener('pageshow', (e) => { if (e.persisted) atualizar(); });
            document.addEventListener('visibilitychange', () => { if (!document.hidden) atualizar(); });
            setTimeout(() => setInterval(ciclo, 3200), 3600);
        }

        if (document.getElementById('splash')) {
            // A vinheta leva ~1,7 s subindo: o card entra quando a metade de
            // baixo da tela (onde ele fica) já está aparecendo.
            document.addEventListener('vinheta:saindo', () => setTimeout(iniciar, 650), { once: true });
            document.addEventListener('vinheta:fim', iniciar, { once: true });
        } else {
            iniciar();
        }
    })();

    // Vídeo de fundo: Pausar/Reproduzir. Com "reduzir movimento" ligado no
    // sistema, já começa pausado. A escolha fica salva entre as páginas.
    (function () {
        const video = document.getElementById('hero-video');
        const botao = document.getElementById('heroVideoControle');
        if (!video || !botao) return;
        const icone = botao.querySelector('i');
        const texto = botao.querySelector('.hero-video-controle-texto');
        const CHAVE = 'agrifarm_video_fundo_pausado';

        function mostrar(pausado) {
            botao.setAttribute('aria-pressed', pausado ? 'true' : 'false');
            icone.className = 'fa-solid ' + (pausado ? 'fa-play' : 'fa-pause');
            texto.textContent = pausado ? 'Reproduzir vídeo de fundo' : 'Pausar vídeo de fundo';
        }

        function definir(pausado, salvar) {
            if (pausado) video.pause();
            else video.play().catch(() => {});
            mostrar(pausado);
            if (salvar) {
                try { localStorage.setItem(CHAVE, pausado ? '1' : '0'); } catch (e) { /* sem armazenamento */ }
            }
        }

        let salvo = null;
        try { salvo = localStorage.getItem(CHAVE); } catch (e) { /* sem armazenamento */ }
        const reduzir = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (salvo === '1' || (salvo === null && reduzir)) {
            video.removeAttribute('autoplay');
            definir(true, false);
        } else {
            mostrar(false);
        }

        botao.addEventListener('click', () => definir(!video.paused, true));
    })();
</script>
