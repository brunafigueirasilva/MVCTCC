    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        :root {
            --green: #4F7724;
            --green-dark: #3d5e1c;
            --green-light: #6aaa2e;
            --bg-dark: #1b241a;
            --bg-card: #22190f;
            --text: #f1f5f9;
            --text-muted: rgba(241, 245, 249, 0.6);
            --brown: #9f5d23;
            --brown-light: #b8712f;

            /* Dourado do trigo/grãos da logo — acento premium usado com
               moderação (linhas, glows, badges, contagem de números),
               nunca como cor de fundo grande. Gunmetal ecoa a engrenagem
               da logo, um cinza-grafite frio para dar peso "tech" ao modo
               escuro sem recorrer a preto puro. */
            --gold: #d1a136;
            --gold-light: #e8c463;
            --gunmetal: #2a332f;

            /* Par tipográfico moderno/profissional: Plus Jakarta Sans (geométrica,
               peso forte) pra títulos e qualquer coisa "heading-like"; Inter
               (alta legibilidade em telas) pro corpo, labels e botões. Troca
               a serif editorial usada antes — lê como mais "tech/corporate",
               pedido explícito de "fonte moderna e profissional". */
            --font-display: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            --font-body: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;

            /* Espaçamento vertical fluido das seções — antes era um
               "120px 24px" fixo em todo breakpoint; agora respira mais em
               telas grandes e comprime suavemente em telas pequenas sem
               precisar de uma media query própria pra cada valor. */
            --section-py: clamp(72px, 10vw, 140px);
            --ease-premium: cubic-bezier(0.16, 1, 0.3, 1);
            /* Curva usada nos hovers (botões/cards/links/ícones) — mesma
               "desaceleração" do --ease-premium, só que num tempo pensado
               pra reação de mouse/teclado (ver --hover-duration abaixo).
               0.5s (dentro da faixa 0.4s–0.6s pedida): rápido o bastante
               pra não parecer travado, lento o bastante pra ler como um
               movimento deliberado/sofisticado em vez de um "flash". */
            --hover-duration: .5s;

            /* Aliases genéricos — mesmos valores acima, com nomes de
               propósito ("qual é o fundo principal", "qual é a cor de
               texto") em vez de nomes específicos da paleta. Facilita
               reaproveitar/auditar a identidade visual em qualquer página
               nova sem precisar redescobrir qual token usar. */
            --bg-primary: var(--bg-dark);
            --bg-secondary: var(--bg-card);
            --surface: rgba(32, 44, 22, 0.65);
            --text-primary: var(--text);
            --border-color: rgba(255, 255, 255, 0.1);

            /* Paleta clara — só entra em uso com body.light-theme (a opção
               "Tema Claro" do painel de acessibilidade). O padrão da
               página continua sendo o design escuro original. Marfim
               sofisticado com leve respiro esverdeado, no lugar do
               verde-menta saturado anterior — lê como "clean/elegante" em
               vez de um tema colorido. */
            --bg-light: #f8f6f1;
            --bg-light-alt: #f0ede4;
            --text-on-light: #1c2620;
            --text-on-light-muted: #5b6b60;
            --card-light-bg: #ffffff;
            --card-light-border: #e7e2d6;
            --card-light-shadow: 0 2px 8px rgba(28, 38, 32, 0.04), 0 20px 40px -16px rgba(28, 38, 32, 0.14);

            /* Painel de Acessibilidade (componente compartilhado, ver
               partials/acessibilidade.blade.php) — sem essas variáveis ele
               caía no fallback claro (fundo branco) fixo, destoando do
               design escuro desta página em qualquer tema. Valores daqui
               são os do tema escuro (padrão); body.light-theme redefine
               pros claros logo abaixo. */
            --panel-bg: #1e2b19;
            --panel-text: #f1f5f9;
            --panel-item-bg: #1b241a;
            --panel-btn-bg: #2c3d20;
            --panel-btn-text: #e8ede0;
            --panel-btn-hover: #3a5028;

            /* Zoom padrão da página: 2 "cliques" a menos do controle de
               zoom do painel de acessibilidade (que anda de 5 em 5%),
               10% mais afastado que o normal. O painel (ver
               partials/acessibilidade.blade.php, regra "html { zoom:
               var(--zoom-scale, 1) }") lê essa variável como ponto de
               partida — quem ainda não salvou uma preferência própria de
               zoom recebe esse valor; quem já ajustou antes continua com o
               que salvou. */
            --zoom-scale: 0.9;
        }

        html {
            /* Pedido: cliques no menu devem ir direto pra seção, sem a
               animação de rolagem. */
            scroll-behavior: auto;
            /* Compensa a altura da barra de contato (40px) + navbar (66px)
               fixas no topo, pro caso de algum link com âncora dentro da
               própria página. */
            scroll-padding-top: 106px;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg-dark);
            color: var(--text);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Acessibilidade: neutraliza parallax/tilt/count-up/entrada 3D pra
           quem pede menos movimento no sistema — cards continuam
           aparecendo (opacity), só sem o deslocamento/rotação. As classes
           JS (.tilt-active, .hero-orb, .nav-scrolled) são as mesmas usadas
           pelos efeitos novos abaixo; ver partials/landing-scripts.blade.php. */
        @media (prefers-reduced-motion: reduce) {
            /* Fora a intro de abertura (#splash), o card do gráfico
               (.lote-chart) e as letras dos títulos (.section-title): são as
               animações principais da landing, pedidas explicitamente, e com
               elas zeradas não apareciam — no Windows com "Mostrar animações"
               desligado essa preferência vem ligada sem a pessoa perceber. */
            :not(#splash, #splash *, .lote-chart, .lote-chart *, .section-title, .section-title *),
            :not(#splash, #splash *, .lote-chart, .lote-chart *, .section-title, .section-title *)::before,
            :not(#splash, #splash *, .lote-chart, .lote-chart *, .section-title, .section-title *)::after {
                animation-duration: 0.001ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.001ms !important;
                scroll-behavior: auto !important;
            }

            .hero-orb,
            #hero-video,
            .hero-glow {
                transform: none !important;
            }
        }

        /* ───────── BARRA SUPERIOR (CONTATO / REDES SOCIAIS) ─────────
           Fina, fixa acima da navbar — telefone e e-mail à esquerda,
           ícones de redes sociais à direita. */
        .topbar {
            height: 40px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            background: linear-gradient(90deg, #16280d 0%, #182f0e 50%, #16280d 100%);
            border-bottom: 1px solid rgba(209, 161, 54, 0.12);
            transition: background 0.4s ease, border-color 0.4s ease;
        }

        .topbar-inner {
            max-width: 1500px;
            height: 100%;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-contact {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .topbar-contact-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: rgba(214, 240, 210, 0.85);
            text-decoration: none;
            font-size: 11.5px;
            letter-spacing: 0.2px;
            transition: color var(--hover-duration) var(--ease-premium);
        }

        .topbar-contact-item svg {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
            transition: transform var(--hover-duration) var(--ease-premium);
        }

        .topbar-contact-item:hover,
        .topbar-contact-item:focus-visible {
            color: #ffffff;
        }

        .topbar-contact-item:hover svg {
            transform: scale(1.12);
        }

        .topbar-social {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 24px; /* alvo de toque mínimo (WCAG 2.5.8) */
            min-height: 24px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            transition: color var(--hover-duration) var(--ease-premium), transform var(--hover-duration) var(--ease-premium);
        }

        .topbar-social a:hover,
        .topbar-social a:focus-visible {
            color: #e8c463;
            transform: translateY(-2px) scale(1.1);
        }

        @media (max-width: 576px) {
            .topbar { display: none; }
        }

        /* ───────── NAVBAR (padrão do sistema — mesmo estilo/altura/cores
           usados nas telas internas autenticadas, ver dashboard.blade.php
           .navbar) ───────── */
        nav {
            height: 66px;
            position: fixed;
            top: 40px;
            left: 0;
            width: 100%;
            z-index: 999;
            background: linear-gradient(180deg, rgba(18, 63, 34, 0.94), rgba(15, 51, 27, 0.94));
            backdrop-filter: blur(14px) saturate(140%);
            -webkit-backdrop-filter: blur(14px) saturate(140%);
            border-bottom: 1px solid rgba(209, 161, 54, 0.16);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
            transition: background 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease, height 0.4s ease;
        }

        /* Estado "rolado" — aplicado via JS (partials/landing-scripts.blade.php)
           quando scrollY ultrapassa um pequeno limiar. Fundo mais opaco e
           sombra mais presente, efeito comum de navbar premium que "se
           firma" conforme a página desce. Não altera a altura pra não
           deslocar o padding fixo do resto da página. */
        nav.nav-scrolled {
            background: linear-gradient(180deg, rgba(14, 48, 25, 0.98), rgba(11, 38, 20, 0.98));
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.32);
            border-bottom-color: rgba(209, 161, 54, 0.28);
        }

        /* Layout em 3 blocos — logo numa ponta, ações (botão + acessibilidade
           + hambúrguer) na outra, e o menu de navegação centralizado no meio
           da barra (não colado nem à logo nem ao botão). */
        .nav-inner {
            max-width: 1500px;
            height: 100%;
            margin: 0 auto;
            padding: 0 32px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 24px;
        }

        .logo {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .logo img {
            height: 46px;
            width: auto;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, .55)) drop-shadow(0 0 14px rgba(0, 0, 0, .35));
            transition: transform var(--hover-duration) var(--ease-premium), filter var(--hover-duration) ease;
        }

        .logo:hover img,
        .logo:focus-visible img {
            transform: scale(1.05);
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, .55)) drop-shadow(0 0 12px rgba(209, 161, 54, .45));
        }

        .logo:focus-visible {
            outline: 2px solid #e8c463;
            outline-offset: 4px;
            border-radius: 6px;
        }

        /* Botão "Acessar Sistema" + botão de acessibilidade + hambúrguer,
           agrupados na ponta direita (o botão de acessibilidade é movido
           pra dentro de #navAccessibilitySlot via JS). */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
            justify-self: end;
        }

        #navAccessibilitySlot {
            display: flex;
            align-items: center;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            list-style: none;
        }

        .nav-links a {
            position: relative;
            color: rgba(255, 255, 255, 0.78);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 9px 10px;
            border-radius: 8px;
            transition: color var(--hover-duration) var(--ease-premium), background var(--hover-duration) var(--ease-premium), transform var(--hover-duration) var(--ease-premium);
        }

        .nav-links a:not(.btn-nav)::after {
            content: '';
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 4px;
            width: auto;
            transform: scaleX(0);
            transform-origin: left;
            height: 2px;
            border-radius: 2px;
            background: linear-gradient(90deg, #6aaa2e, #e8c463);
            transition: transform var(--hover-duration) var(--ease-premium);
        }

        .nav-links a:not(.btn-nav):hover,
        .nav-links a:not(.btn-nav):focus-visible,
        .nav-links a.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-1px);
        }

        .nav-links a:not(.btn-nav):hover::after,
        .nav-links a:not(.btn-nav):focus-visible::after,
        .nav-links a.active::after {
            transform: scaleX(1);
        }

        /* Anel de foco visível (teclado) nos links do menu — o outline
           padrão do navegador ficava recortado pelo overflow/backdrop da
           navbar em alguns casos; este é sempre visível e consistente com
           a paleta. */
        .nav-links a:focus-visible {
            outline: 2px solid #e8c463;
            outline-offset: 2px;
        }

        /* ───────── BOTÃO ACESSAR SISTEMA ───────── */
        .btn-nav {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #6aaa2e, #4F7724 55%, #3d5e1c);
            color: #ffffff !important;
            border-radius: 999px;
            border: 1px solid rgba(209, 161, 54, 0.4);
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: .2px;
            box-shadow: 0 6px 18px -4px rgba(20, 40, 10, 0.55), inset 0 1px 0 rgba(232, 196, 99, 0.25);
            transition: transform var(--hover-duration) ease, box-shadow var(--hover-duration) ease, border-color var(--hover-duration) ease;
        }

        .btn-nav i {
            font-size: 0.85em;
        }

        /* Brilho que atravessa o botão no hover — dá um toque "premium"
           sem precisar de mais nenhuma cor fora da paleta verde. */
        .btn-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 55px;
            height: 100%;
            background: rgba(255, 255, 255, .45);
            transform: skewX(-25deg);
            transition: left .65s ease;
        }

        .btn-nav:hover::before,
        .btn-nav:focus-visible::before {
            left: 140%;
        }

        .btn-nav:hover,
        .btn-nav:focus-visible {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 26px -4px rgba(20, 40, 10, 0.65), 0 0 0 1px rgba(232, 196, 99, 0.35);
            border-color: rgba(232, 196, 99, 0.55);
        }

        .btn-nav:focus-visible {
            outline: 2px solid #e8c463;
            outline-offset: 3px;
        }

        .btn-nav:active {
            transform: translateY(-1px) scale(1.0);
        }

        /* MENU MOBILE */
        .hamburger {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 9px;
            color: #ffffff;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: background var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) var(--ease-premium), transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) var(--ease-premium);
        }

        .hamburger:hover,
        .hamburger:focus-visible {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(232, 196, 99, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }

        .hamburger svg {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 900px) {
            .nav-links {
                display: none;
            }

            .hamburger {
                display: flex;
            }
        }

        /* Tablet: o botão "Acessar Sistema" e o de acessibilidade continuam
           com texto, mas mais compactos. */
        @media (max-width: 900px) {
            .nav-inner { gap: 12px; padding: 0 20px; }
            .nav-right { gap: 10px; }
            .btn-nav { padding: 10px 16px; }
        }

        @media (max-width: 576px) {
            .nav-inner {
                padding: 0 14px;
                grid-template-columns: auto 1fr auto;
            }

            /* .topbar já fica escondida nesse breakpoint — a navbar sobe
               pro topo pra não sobrar um espaço vazio no lugar dela. */
            nav {
                top: 0;
            }

            /* Celular: "Acessar Sistema" e "Acessibilidade" viram botões
               redondos só com ícone (o texto continua lá pra leitores de
               tela) — antes eles empurravam o ☰ pra fora da tela. */
            .nav-right { gap: 8px; }

            .btn-nav {
                width: 42px;
                height: 42px;
                padding: 0;
                justify-content: center;
                font-size: 0;
                gap: 0;
            }

            .btn-nav i { font-size: 16px; }

            .accessibility-trigger.acc-trigger-inline .acc-trigger-label {
                position: absolute;
                width: 1px;
                height: 1px;
                overflow: hidden;
                clip: rect(0 0 0 0);
                white-space: nowrap;
            }

            .accessibility-trigger.acc-trigger-inline {
                width: 42px;
                height: 42px;
                padding: 0;
                justify-content: center;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.18);
            }

            .hamburger { width: 42px; height: 42px; justify-content: center; }
        }

        .mobile-menu {
            display: none;
            flex-direction: column;
            background: #0e3018;
            padding: 12px 24px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-menu a {
            display: block;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 12px 6px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.92rem;
            transition: color var(--hover-duration) var(--ease-premium), padding-left var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) var(--ease-premium);
        }

        .mobile-menu a:hover,
        .mobile-menu a:focus-visible {
            color: #a3c96e;
            padding-left: 14px;
            border-bottom-color: rgba(106, 170, 46, .3);
        }

        .mobile-menu a:last-child {
            border-bottom: none;
        }

        .mobile-menu.open {
            display: flex;
        }

        /* ── ANIMATIONS ── */
        @keyframes titleReveal {
            0% {
                clip-path: inset(0 100% 0 0);
                opacity: 0;
                transform: translateY(12px);
            }

            100% {
                clip-path: inset(0 0% 0 0);
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes titleSlideUp {
            0% {
                opacity: 0;
                transform: translateY(28px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes tagFade {
            0% {
                opacity: 0;
                letter-spacing: 6px;
            }

            100% {
                opacity: 1;
                letter-spacing: 3px;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .5;
                transform: scale(1.3);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            50% {
                transform: translateX(-50%) translateY(8px);
            }
        }

        @keyframes charRise {
            0% {
                opacity: 0;
                transform: translateY(22px) skewX(-8deg);
            }

            70% {
                opacity: 1;
                transform: translateY(-2px) skewX(1deg);
            }

            100% {
                opacity: 1;
                transform: translateY(0) skewX(0deg);
            }
        }

        @keyframes charGlow {
            0% {
                opacity: 0;
                transform: scale(0.7);
                filter: blur(6px) brightness(2);
            }

            50% {
                filter: blur(1px) brightness(1.3);
            }

            100% {
                opacity: 1;
                transform: scale(1);
                filter: blur(0) brightness(1);
            }
        }

        @keyframes faqOpen {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(1.9rem, 4vw, 3.2rem);
            font-weight: 800;
            text-align: center;
            line-height: 1.22;
            letter-spacing: -0.015em;
            margin: 0 auto 36px;
            /* Impede que a frase vire uma única linha esticada até a borda
               do container (1100px) — assim ela quebra em linhas mais
               curtas e equilibradas, centralizadas de verdade. */
            max-width: 850px;
            perspective: 600px;
        }

        .section-title .accent {
            color: #4F7724;
        }

        .section-title.reveal-title {
            opacity: 1;
        }

        /* Títulos de seção: texto simples, cor sólida (sem efeito letra por
           letra). */
        .section-title { color: #f1f9f4; }
        body.light-theme .section-title { color: #1c2f20; }
        .section-title.reveal-title .accent { color: #5fd06b; }
        body.light-theme .section-title.reveal-title .accent { color: #4a9a2a; }
        body.high-contrast .section-title { color: #ffffff !important; }
        body.high-contrast .section-title .accent { color: #ffff00 !important; }

        .section-tag {
            display: block;
            text-align: center;
            color: #cc8645; /* marrom do AgriFarm um tom mais claro: 4,5:1 no fundo escuro */
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            opacity: 0;
        }

        .section-tag.revealed {
            animation: tagFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
        }

        /* ── HERO ── */
        #hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            perspective: 1200px;
        }

        #hero-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            /* Estava em 25% (bem escuro); um ajuste pra 33% ainda ficou
               escuro demais porque o overlay abaixo é quase opaco por cima
               — subido bem mais, pro vídeo aparecer de verdade. */
            filter: brightness(68%);
            will-change: transform;
        }

        /* Orbs decorativos reais (não pseudo-elemento, pra poderem ser
           movidos por JS — ver .hero-orb em partials/landing-scripts.blade.php)
           que dão profundidade/parallax 3D ao hero: um verde e um dourado,
           bem desfocados, reagindo sutilmente ao mouse e ao scroll. */
        .hero-orb {
            position: absolute;
            z-index: 1;
            border-radius: 50%;
            filter: blur(70px);
            opacity: .35;
            pointer-events: none;
            will-change: transform;
        }

        /* --mx/--my: escritos pelo listener de mousemove em #hero (ver
           landing-scripts.blade.php), de -1 a 1. Cada orb reage numa
           intensidade/direção diferente pra dar sensação de profundidade
           em camadas (parallax de mouse), não de bloco único se movendo. */
        .hero-orb--green {
            width: 480px;
            height: 480px;
            top: -8%;
            left: -6%;
            background: radial-gradient(circle, rgba(106, 170, 46, .55) 0%, transparent 70%);
            transform: translate3d(calc(var(--mx, 0) * -26px), calc(var(--my, 0) * -18px), 0);
        }

        .hero-orb--gold {
            width: 380px;
            height: 380px;
            bottom: -10%;
            right: -4%;
            background: radial-gradient(circle, rgba(232, 196, 99, .4) 0%, transparent 70%);
            transform: translate3d(calc(var(--mx, 0) * 30px), calc(var(--my, 0) * 22px), 0);
        }

        .hero-orb--brown {
            width: 300px;
            height: 300px;
            top: 35%;
            right: 12%;
            background: radial-gradient(circle, rgba(159, 93, 35, .3) 0%, transparent 70%);
            transform: translate3d(calc(var(--mx, 0) * -18px), calc(var(--my, 0) * 14px), 0);
        }

        @media (max-width: 768px) {
            .hero-orb {
                display: none;
            }
        }

        /* Havia uma regra ".hero-overlay" duplicada e aninhada dentro dela
           mesma (CSS inválido) — consolidada numa única regra. Opacidades
           reduzidas junto com o brightness acima, senão o vídeo mais claro
           continuava escondido atrás de um preto quase sólido. */
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(0, 0, 0, .62) 0%,
                rgba(0, 0, 0, .47) 35%,
                rgba(0, 0, 0, .38) 70%,
                rgba(15, 30, 8, .53) 100%
            );
            z-index: 1;
        }

        .hero-glow {
            position: absolute;
            inset: 0;
            z-index: 1;
            opacity: .18;
            background:
                radial-gradient(circle at 20% 50%, rgba(79, 119, 36, .4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(61, 94, 28, .3) 0%, transparent 50%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
            padding: 120px 24px 80px;
            text-align: center;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(79, 119, 36, .18);
            border: 1px solid rgba(106, 170, 46, .35);
            color: #a3c96e;
            font-size: 11px;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 999px;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: tagFade 0.8s ease 0.2s both;
        }

        .tag-dot {
            width: 8px;
            height: 8px;
            background: #6aaa2e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.3rem, 3.4vw, 4.7rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 24px;
            text-shadow: 0 4px 30px rgba(0, 0, 0, 0.35);
            animation: titleReveal 1s cubic-bezier(0.16, 1, 0.3, 1) 0.4s both;
        }

        .hero-title .accent {
            background: linear-gradient(90deg, #6aaa2e, #4F7724);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: rgba(255, 255, 255, .72);
            max-width: 680px;
            margin: 0 auto 40px;
            line-height: 1.7;
            animation: titleSlideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.7s both;
        }

        .hero-btns {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
            animation: titleSlideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.9s both;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #4F7724;
            color: #fff;
            font-weight: 700;
            padding: 16px 32px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 15px;
            box-shadow: 0 8px 32px rgba(79, 119, 36, .35);
            transition: transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) var(--ease-premium), background var(--hover-duration) ease;
        }

        .btn-primary:hover,
        .btn-primary:focus-visible {
            background: #6aaa2e;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 14px 36px rgba(79, 119, 36, .45);
        }

        .btn-primary:focus-visible {
            outline: 2px solid #e8c463;
            outline-offset: 3px;
        }

        .btn-primary:active {
            transform: translateY(0) scale(1.0);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .1);
            color: #fff;
            font-weight: 600;
            padding: 16px 32px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 15px;
            border: 1px solid rgba(255, 255, 255, .28);
            backdrop-filter: blur(8px);
            transition: transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) var(--ease-premium), background var(--hover-duration) ease, border-color var(--hover-duration) ease;
        }

        .btn-secondary:hover,
        .btn-secondary:focus-visible {
            background: rgba(255, 255, 255, .2);
            border-color: rgba(255, 255, 255, .45);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .25);
        }

        .btn-secondary:focus-visible {
            outline: 2px solid #ffffff;
            outline-offset: 3px;
        }

        .btn-secondary:active {
            transform: translateY(0) scale(1.0);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            max-width: 560px;
            margin: 56px auto 0;
            animation: titleSlideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 1.1s both;
            perspective: 900px;
        }

        .stat-card {
            background: rgba(255, 255, 255, .1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 16px;
            padding: 18px 8px;
            transition: transform .3s var(--ease-premium), border-color .3s ease, box-shadow .3s ease;
            transform-style: preserve-3d;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 900;
            color: #6aaa2e;
        }

        .stat-label {
            font-size: 11px;
            color: rgba(255, 255, 255, .92);
            margin-top: 4px;
            /* Reforça a leitura por cima do vídeo (agora mais claro) sem
               precisar escurecer o card de novo. */
            text-shadow: 0 1px 3px rgba(0, 0, 0, .5);
        }

        .scroll-hint {
            position: absolute;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            animation: bounce 2s infinite;
        }

        .scroll-hint span {
            font-size: 11px;
            color: rgba(255, 255, 255, .4);
        }

        .scroll-hint svg {
            width: 20px;
            height: 20px;
            color: rgba(255, 255, 255, .4);
            stroke: currentColor;
        }

        /* Botão flutuante "voltar ao topo" — some por padrão, aparece ao
           rolar a página. Fica ao lado (não embaixo) do botão do assistente
           de IA (partials/assistente-ia.blade.php, right:24/bottom:24,
           58x58, z-index:1010) pra não ficar escondido atrás dele. */
        .back-to-top {
            position: fixed;
            bottom: 24px;
            right: 94px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #4F7724;
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .3);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity .3s ease, transform .3s var(--ease-premium), visibility .3s, background .3s ease, box-shadow .3s var(--ease-premium);
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top:hover {
            background: #6aaa2e;
            transform: translateY(-3px) scale(1.06);
            box-shadow: 0 8px 22px rgba(0, 0, 0, .38);
        }

        .back-to-top svg {
            width: 20px;
            height: 20px;
        }

        body.high-contrast .back-to-top {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* ── SECTIONS ── */
        section {
            padding: var(--section-py) 24px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
        }
/* ── PROBLEMA ── */
#problema {
    background: #151f11;
    padding: var(--section-py) 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Camada de profundidade radial, no mesmo espírito do que #solucao/#faq já
   tinham — estendida a todas as seções internas pra dar sensação
   consistente de "luz vindo de algum lugar" em vez de fundo chapado. Em
   telas grandes, background-attachment:fixed cria um parallax simples (o
   glow "fica parado" enquanto a seção rola por cima); desligado no mobile
   logo abaixo (custo de scroll/repaint em iOS Safari). */
#problema::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 45% at 15% 15%, rgba(106, 170, 46, .1) 0%, transparent 60%),
        radial-gradient(ellipse 45% 35% at 90% 85%, rgba(209, 161, 54, .07) 0%, transparent 55%);
    background-attachment: fixed;
    pointer-events: none;
}

.problema-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 72px;
    align-items: start;
    margin-top: 88px;
    text-align: left;
}

.problema-content {
    max-width: 800px;
    margin: 0 auto;
}

.problema-intro {
    font-size: 1.05rem;
    color: var(--text-muted);
    line-height: 1.85;
    margin-bottom: 40px;
    text-align: left;
}

#problema .section-title,
#problema .section-tag {
    text-align: center;
    margin-top: 32px;
}

#problema .accent {
    color: #6bc048;
}

/* Mesmo respiro entre o topo da seção e a navbar fixa aplicado ao
   Problema, replicado nas demais páginas — sem isso o rótulo/título
   da primeira seção fica colado na navbar. */
#solucao > .container > .section-tag,
#como-funciona > .container > .section-tag,
#sobre > .container > .section-tag,
#contato > .container > .section-tag,
#beneficios > .container > .section-tag {
    margin-top: 32px;
}

        .check-list {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .check-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .check-icon {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .check-icon.bad {
            background: rgba(122, 78, 38, .22);
            border: 1px solid rgba(190, 130, 70, .45);
        }

        .check-icon.good {
            background: rgba(79, 119, 36, .15);
            border: 1px solid rgba(79, 119, 36, .35);
        }

        .check-icon svg {
            width: 12px;
            height: 12px;
        }

        .check-icon.bad svg {
            stroke: #d9a066;
        }

        .check-icon.good svg {
            stroke: #6aaa2e;
        }

        .check-item span {
            color: rgba(255, 255, 255, .82);
            font-size: 15px;
            line-height: 1.65;
        }

        .quote-block {
            margin-top: 44px;
            padding: 4px 0 4px 18px;
            border-left: 3px solid var(--brown);
            color: rgba(255, 255, 255, .55);
            font-size: 13.5px;
            line-height: 1.7;
            font-style: italic;
            text-align: left;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            perspective: 900px;
        }

        .stat-big {
            grid-column: 1/-1;
        }

        .data-card {
            border-radius: 20px;
            padding: 28px 22px;
            text-align: center;
            backdrop-filter: blur(6px);
            transition: transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) ease;
            transform-style: preserve-3d;
        }

        .data-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 32px rgba(0, 0, 0, .3);
        }

        .data-card.red {
            background: linear-gradient(135deg, rgba(122, 78, 38, .4), rgba(70, 45, 22, .15));
            border: 1px solid rgba(190, 130, 70, .35);
        }

        .data-card.yellow {
            background: linear-gradient(135deg, rgba(113, 63, 18, .35), rgba(113, 63, 18, .1));
            border: 1px solid rgba(234, 179, 8, .2);
        }

        .data-card.orange {
            background: linear-gradient(135deg, rgba(124, 45, 18, .35), rgba(124, 45, 18, .1));
            border: 1px solid rgba(249, 115, 22, .2);
        }

        .data-card .num {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .data-card.red .num {
            color: #d9a066;
        }

        .data-card.yellow .num {
            color: #fbbf24;
        }

        .data-card.orange .num {
            color: #fb923c;
        }

        .data-card p {
            font-size: 13px;
            line-height: 1.5;
            color: var(--text-muted);
        }

        /* ── SOLUÇÃO (MELHORADA) ── */
        #solucao {
            background: linear-gradient(180deg, #0d1a05 0%, #12220a 40%, #151f11 100%);
            position: relative;
            overflow: hidden;
        }

        #solucao::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 50% 0%, rgba(79, 119, 36, .12) 0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 80% 80%, rgba(159, 93, 35, .08) 0%, transparent 50%);
            pointer-events: none;
        }

        /* .solucao-card não vive num grid (é peça única), então precisa do
           seu próprio contexto de perspectiva pro rotateX de entrada/tilt
           renderizar com profundidade de verdade, e não como um simples
           achatamento vertical. */
        #solucao .container {
            perspective: 1200px;
        }

        .solucao-card {
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, rgba(40, 70, 20, .5) 0%, rgba(26, 40, 12, .4) 50%, rgba(20, 14, 6, .5) 100%);
            border: 1px solid rgba(79, 119, 36, .35);
            border-radius: 32px;
            padding: 64px;
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 32px 80px rgba(0, 0, 0, .5), inset 0 1px 0 rgba(232, 196, 99, .1);
            transition: transform .4s var(--ease-premium), box-shadow .4s ease;
            transform-style: preserve-3d;
        }

        .solucao-card::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 33px;
            background: linear-gradient(135deg, rgba(106, 170, 46, .3), transparent 40%, rgba(209, 161, 54, .22));
            z-index: -1;
        }

        .sol-icon {
            width: 88px;
            height: 88px;
            background: radial-gradient(circle, rgba(79, 119, 36, .25) 0%, rgba(79, 119, 36, .08) 100%);
            border: 2px solid rgba(106, 170, 46, .4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 0 40px rgba(79, 119, 36, .2), inset 0 1px 0 rgba(255, 255, 255, .1);
        }

        .sol-icon svg {
            width: 40px;
            height: 40px;
            stroke: #6aaa2e;
        }

        .sol-title-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(79, 119, 36, .15);
            border: 1px solid rgba(106, 170, 46, .25);
            color: #a3c96e;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .sol-text {
            font-size: 1.12rem;
            color: rgba(255, 255, 255, .82);
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .sol-divider {
            width: 48px;
            height: 2px;
            margin: 28px auto;
            background: linear-gradient(90deg, transparent, rgba(106, 170, 46, .5), transparent);
        }

        .sol-features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 28px;
            perspective: 900px;
        }

        .sol-feat {
            background: rgba(255, 255, 255, .04);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 18px;
            padding: 26px 18px;
            text-align: center;
            transition: transform var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) ease, box-shadow var(--hover-duration) ease;
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
        }

        .sol-feat::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(79, 119, 36, .08) 0%, transparent 70%);
            opacity: 0;
            transition: opacity var(--hover-duration) ease;
        }

        .sol-feat:hover {
            border-color: rgba(79, 119, 36, .45);
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .3);
        }

        .sol-feat:hover .f-icon {
            transform: scale(1.08);
        }

        .sol-feat:hover::before {
            opacity: 1;
        }

        .sol-feat .f-icon {
            width: 44px;
            height: 44px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(79, 119, 36, .15);
            border-radius: 12px;
            transition: transform var(--hover-duration) var(--ease-premium);
            border: 1px solid rgba(79, 119, 36, .3);
        }

        .sol-feat .f-icon svg {
            width: 22px;
            height: 22px;
            stroke: #6aaa2e;
        }

        .sol-feat .f-title {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .sol-feat .f-desc {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .sol-stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            margin-top: 32px;
            background: rgba(255, 255, 255, .06);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .sol-stat {
            background: rgba(32, 44, 22, .6);
            padding: 18px 12px;
            text-align: center;
        }

        .sol-stat .v {
            font-size: 1.6rem;
            font-weight: 900;
            color: #6aaa2e;
        }

        .sol-stat .l {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
            line-height: 1.4;
        }

        /* ── COMO FUNCIONA ── */
        #como-funciona {
            background: #151f11;
            position: relative;
            overflow: hidden;
        }

        #como-funciona::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 55% 40% at 85% 10%, rgba(209, 161, 54, .08) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 10% 90%, rgba(106, 170, 46, .09) 0%, transparent 55%);
            background-attachment: fixed;
            pointer-events: none;
        }
.steps {
    display: flex;
    flex-direction: column;
    gap: 28px;
    margin-top: 56px;
    position: relative;
    perspective: 1000px;
}

/* Linha central ligando os círculos 01→04: sem ela, cada etapa parecia
   uma linha solta em zigue-zague, dando impressão de desalinhamento em
   vez de um fluxo único. */
.steps::before {
    content: '';
    position: absolute;
    top: 26px;
    bottom: 26px;
    left: 50%;
    width: 2px;
    background: linear-gradient(180deg, rgba(106, 170, 46, .55), rgba(106, 170, 46, .12));
    z-index: 0;
}

.step-row {
    display: grid;
    grid-template-columns: 1fr 70px 1fr; /* coluna central mais enxuta */
    gap: 16px; /* card mais próximo do círculo numerado */
    align-items: center;
}

       .step-card {
    background: rgba(32, 44, 22, .8);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, .1);
    border-radius: 20px;
    padding: 28px 32px; /* espaçamento interno mais compacto */
    transition: transform var(--hover-duration) var(--ease-premium), background var(--hover-duration) ease, border-color var(--hover-duration) ease, box-shadow var(--hover-duration) ease;
    transform-style: preserve-3d;
    will-change: transform;
}

        .step-card:hover {
            border-color: rgba(79, 119, 36, .4);
            background: rgba(32, 44, 22, 1);
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .3);
        }

        .step-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .step-icon-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(150deg, rgba(79, 119, 36, .18), var(--gunmetal) 140%);
            border: 1px solid rgba(79, 119, 36, .28);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) ease;
        }

        .step-card:hover .step-icon-box {
            transform: scale(1.08) translateY(-1px);
            border-color: rgba(232, 196, 99, .45);
        }

        .step-icon-box svg {
            width: 22px;
            height: 22px;
            stroke: #6aaa2e;
        }

        .step-label {
            font-size: 11px;
            color: var(--brown-light);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .step-title {
            font-family: var(--font-display);
            font-size: 1.18rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #fff;
            margin-bottom: 8px;
        }

        .step-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .step-num {
            position: relative;
            z-index: 1;
            width: 52px;
            height: 52px;
            background: #4F7724;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.1rem;
            color: #fff;
            box-shadow: 0 4px 20px rgba(79, 119, 36, .4);
            margin: 0 auto;
            flex-shrink: 0;
        }

        /* ── BENEFÍCIOS ── */
        #beneficios {
            background: linear-gradient(180deg, #151f11 0%, #1a3008 100%);
            position: relative;
            overflow: hidden;
        }

        #beneficios::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 45% at 50% 0%, rgba(209, 161, 54, .07) 0%, transparent 60%);
            background-attachment: fixed;
            pointer-events: none;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 26px;
            margin-top: 72px;
            perspective: 1000px;
        }

        .benefit-card {
            position: relative;
            overflow: hidden;
            background: rgba(32, 44, 22, .65);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 20px;
            padding: 36px;
            transition: transform .45s var(--ease-premium), border-color .45s ease, box-shadow .45s ease;
            cursor: default;
            min-height: 240px;
            transform-style: preserve-3d;
            will-change: transform;
        }

        .benefit-card-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity .5s ease, transform .6s ease;
            border-radius: 20px;
        }

        .benefit-card::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            background: linear-gradient(160deg, rgba(8, 6, 3, 0.88) 0%, rgba(13, 10, 7, 0.72) 100%);
            opacity: 0;
            transition: opacity .5s ease;
            border-radius: 20px;
        }

        .benefit-card:hover .benefit-card-bg {
            opacity: 1;
            transform: scale(1.05);
        }

        .benefit-card:hover::after {
            opacity: 1;
        }

        .benefit-card:hover {
            border-color: rgba(79, 119, 36, .6);
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, .55), 0 0 0 1px rgba(79, 119, 36, .25);
        }

        .benefit-card-content {
            position: relative;
            z-index: 1;
        }

        .benefit-icon {
            width: 56px;
            height: 56px;
            background: rgba(79, 119, 36, .12);
            border: 1px solid rgba(79, 119, 36, .28);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: background var(--hover-duration) ease, border-color var(--hover-duration) ease;
        }

        .benefit-card:hover .benefit-icon {
            background: rgba(79, 119, 36, .28);
            border-color: rgba(106, 170, 46, .55);
        }

        .benefit-icon svg {
            width: 26px;
            height: 26px;
            stroke: #6aaa2e;
        }

        .benefit-title {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 10px;
            transition: color var(--hover-duration) var(--ease-premium);
        }

        .benefit-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.65;
        }

        /* ── IMPACTO ── */
        #impacto {
            background: #151f11;
            position: relative;
            overflow: hidden;
        }

        #impacto .bg-img {
            position: absolute;
            inset: 0;
            background: url('https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=1920&q=30') center/cover;
            opacity: .04;
        }

        .impacto-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 56px;
            align-items: center;
            margin-top: 64px;
            position: relative;
            perspective: 1200px;
        }

        .impacto-quote {
            margin-top: 28px;
            background: rgba(79, 119, 36, .08);
            border: 1px solid rgba(79, 119, 36, .25);
            border-radius: 14px;
            padding: 18px 20px;
            color: #a3c96e;
            font-size: 14px;
            font-style: italic;
            line-height: 1.6;
        }

        .impacto-box {
            background: linear-gradient(135deg, rgba(40, 70, 20, .4), transparent);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(79, 119, 36, .2);
            border-radius: 28px;
            padding: 36px;
            text-align: center;
            transition: transform .4s var(--ease-premium), box-shadow .4s ease;
            transform-style: preserve-3d;
        }

        .impacto-box .big-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .impacto-box .big-icon svg {
            width: 48px;
            height: 48px;
            stroke: #6aaa2e;
        }

        .impacto-box h3 {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 12px;
        }

        .impacto-box p {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .mini-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            perspective: 900px;
        }

        .mini-stat {
            background: rgba(255, 255, 255, .05);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 14px;
            padding: 22px 18px;
            transition: border-color var(--hover-duration) ease, background var(--hover-duration) ease, transform var(--hover-duration) var(--ease-premium);
            transform-style: preserve-3d;
        }

        .mini-stat:hover {
            border-color: rgba(106, 170, 46, .5);
            background: rgba(255, 255, 255, .08);
            transform: translateY(-3px);
        }

        .mini-stat .v {
            font-size: 1.6rem;
            font-weight: 900;
            color: #6aaa2e;
        }

        .mini-stat .l {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ── DIFERENCIAIS ── */
        #diferenciais {
            background: linear-gradient(180deg, #151f11 0%, #1a3008 100%);
            position: relative;
            overflow: hidden;
        }

        #diferenciais::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 50% 40% at 80% 100%, rgba(106, 170, 46, .08) 0%, transparent 55%);
            background-attachment: fixed;
            pointer-events: none;
        }

        .diff-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 22px;
            margin-top: 64px;
            perspective: 1000px;
        }

        .diff-card {
            background: rgba(32, 44, 22, .7);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 18px;
            padding: 28px 18px;
            text-align: center;
            transition: transform var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) ease, box-shadow var(--hover-duration) ease;
            transform-style: preserve-3d;
            will-change: transform;
        }

        .diff-card:hover {
            border-color: rgba(79, 119, 36, .4);
            transform: translateY(-4px);
        }

        .diff-card .d-icon {
            width: 44px;
            height: 44px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(150deg, rgba(79, 119, 36, .16), var(--gunmetal) 140%);
            border-radius: 12px;
            transition: transform var(--hover-duration) var(--ease-premium);
        }

        .diff-card:hover .d-icon {
            transform: scale(1.08) translateY(-1px);
        }

        .diff-card .d-icon svg {
            width: 22px;
            height: 22px;
            stroke: #6aaa2e;
        }

        .diff-card p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ── SOBRE ── */
        #sobre {
            background: #151f11;
            position: relative;
            overflow: hidden;
        }

        #sobre::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 55% 40% at 50% 0%, rgba(106, 170, 46, .08) 0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 100% 100%, rgba(209, 161, 54, .06) 0%, transparent 55%);
            background-attachment: fixed;
            pointer-events: none;
        }

        .sobre-text {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.75;
            text-align: center;
            max-width: 680px;
            margin: 0 auto 16px;
        }

        /* Grade da Equipe: virou uma grade de cartões individuais (não mais
           uma "lista" compacta dentro de uma caixa única) — cada
           integrante ganha seu próprio card, com a foto como elemento
           central e bem maior (era um círculo de 38px, quase ilegível).
           auto-fit + minmax deixa o número de colunas fluir sozinho
           (4 em telas largas, 2-3 em tablets, 1 no celular) sem precisar
           de um breakpoint pra cada largura. */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 1fr));
            gap: 24px;
            max-width: 1020px;
            margin: 56px auto 0;
        }

        .team-grid :is(h2, h3) {
            grid-column: 1/-1;
            text-align: center;
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .member {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: rgba(32, 44, 22, .6);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(106, 170, 46, .22);
            border-radius: 22px;
            padding: 32px 20px 24px;
            transition: border-color var(--hover-duration) ease, background var(--hover-duration) ease, transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) ease;
            transform-style: preserve-3d;
        }

        .member:hover {
            border-color: rgba(106, 170, 46, .55);
            background: rgba(255, 255, 255, .05);
            transform: translateY(-5px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .32);
        }

        /* Foto bem maior e com destaque de verdade — círculo de 128px (era
           38px), moldura mais grossa e uma sombra própria que a separa do
           card por trás. overflow:hidden aqui garante que a <img> (ver
           sobre.blade.php) sempre recorte certinho dentro do círculo,
           mesmo que a foto original não seja perfeitamente quadrada. */
        .member-avatar {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            flex-shrink: 0;
            overflow: hidden;
            margin-bottom: 18px;
            background: rgba(79, 119, 36, .18);
            border: 3px solid rgba(106, 170, 46, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #6aaa2e;
            font-size: 2.4rem;
            box-shadow: 0 10px 26px rgba(0, 0, 0, .35), inset 0 1px 0 rgba(255, 255, 255, .08);
            transition: transform var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) ease, box-shadow var(--hover-duration) ease;
        }

        /* object-position com viés pro topo: fotos de rosto costumam ter a
           cabeça na metade superior — cortar exatamente no centro (padrão
           do object-fit: cover) arrisca "comer" o queixo pra caber o
           quadro, quando na verdade sobra fundo embaixo. 22% do topo
           mantém o rosto inteiro visível na maioria das fotos de perfil. */
        .member-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 22%;
            display: block;
        }

        .member:hover .member-avatar {
            transform: scale(1.06);
            border-color: rgba(232, 196, 99, .7);
            box-shadow: 0 14px 32px rgba(0, 0, 0, .45);
        }

        .member-name {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            line-height: 1.35;
        }

        .member-role {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        @media (max-width: 480px) {
            .member-avatar {
                width: 104px;
                height: 104px;
                font-size: 2rem;
            }
        }

        /* ── FAQ ── */
        #faq {
            background: linear-gradient(180deg, #131a0e 0%, rgba(21, 31, 17, 1) 100%);
            position: relative;
            overflow: hidden;
        }

        #faq::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 40% at 50% 100%, rgba(79, 119, 36, .06) 0%, transparent 70%);
            pointer-events: none;
        }

        .faq-subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 1.05rem;
            max-width: 600px;
            margin: -8px auto 48px;
            line-height: 1.7;
        }

        .faq-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            max-width: 960px;
            margin: 0 auto;
            perspective: 1000px;
        }

        .faq-item {
            background: rgba(32, 44, 22, .7);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 16px;
            overflow: hidden;
            transition: border-color var(--hover-duration) ease, box-shadow var(--hover-duration) ease, transform var(--hover-duration) var(--ease-premium);
            transform-style: preserve-3d;
        }

        .faq-item:hover {
            border-color: rgba(79, 119, 36, .4);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .22);
        }

        .faq-item.open {
            border-color: rgba(79, 119, 36, .45);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .3);
        }

        .faq-question {
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 24px;
            text-align: left;
            color: #fff;
            transition: background var(--hover-duration) ease;
        }

        .faq-question:hover {
            background: rgba(106, 170, 46, .06);
        }

        .faq-question:hover .faq-icon {
            transform: scale(1.1);
            border-color: rgba(232, 196, 99, .45);
        }

        .faq-question-text {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.005em;
            line-height: 1.45;
            color: #fff;
            flex: 1;
        }

        .faq-icon {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            background: rgba(79, 119, 36, .15);
            border: 1px solid rgba(79, 119, 36, .3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background var(--hover-duration) ease, transform var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) ease;
        }

        .faq-icon svg {
            width: 14px;
            height: 14px;
            stroke: #6aaa2e;
            transition: transform .3s ease;
        }

        .faq-item.open .faq-icon {
            background: rgba(79, 119, 36, .3);
        }

        .faq-item.open .faq-icon svg {
            transform: rotate(45deg);
        }

        .faq-answer {
            display: none;
            padding: 0 24px 20px;
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.75;
            border-top: 1px solid rgba(255, 255, 255, .06);
        }

        .faq-answer.open {
            display: block;
            animation: faqOpen .25s ease both;
            padding-top: 16px;
        }

        .faq-answer strong {
            color: rgba(255, 255, 255, .85);
        }

        .faq-cta {
            text-align: center;
            margin-top: 48px;
            background: rgba(32, 44, 22, .6);
            border: 1px solid rgba(79, 119, 36, .2);
            border-radius: 20px;
            padding: 32px;
            max-width: 560px;
            margin: 48px auto 0;
        }

        .faq-cta p {
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .faq-cta a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #4F7724;
            color: #fff;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 6px 24px rgba(79, 119, 36, .3);
            transition: transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) var(--ease-premium), background var(--hover-duration) ease;
        }

        .faq-cta a:hover,
        .faq-cta a:focus-visible {
            background: #6aaa2e;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 12px 30px rgba(79, 119, 36, .4);
        }

        .faq-cta a:focus-visible {
            outline: 2px solid #e8c463;
            outline-offset: 3px;
        }

        .faq-cta a svg {
            width: 16px;
            height: 16px;
            stroke: #fff;
        }

        /* ── CONTATO ── */
        #contato {
            background: linear-gradient(180deg, #1a3008 0%, #151f11 100%);
            position: relative;
            overflow: hidden;
        }

        #contato::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 45% at 50% 100%, rgba(209, 161, 54, .07) 0%, transparent 60%);
            background-attachment: fixed;
            pointer-events: none;
        }

        .form-container {
            max-width: 720px;
            margin: 48px auto 0;
            background: rgba(32, 44, 22, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(106, 170, 46, .35);
            border-radius: 28px;
            padding: 40px 48px;
            position: relative;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .35);
        }

        .form-container::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 29px;
            background: linear-gradient(135deg, rgba(79, 119, 36, .18), transparent, rgba(209, 161, 54, .18));
            z-index: -1;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-field.full {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, .55);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input-wrap .input-icon {
            position: absolute;
            left: 14px;
            width: 16px;
            height: 16px;
            stroke: rgba(106, 170, 46, .7);
            pointer-events: none;
            flex-shrink: 0;
        }

        .form-input,
        .form-textarea,
        .form-select {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(106, 170, 46, .3);
            border-radius: 14px;
            padding: 14px 18px 14px 42px;
            color: #fff;
            font-size: 14px;
            width: 100%;
            font-family: inherit;
            transition: border-color .2s, background .2s;
            outline: none;
            appearance: none;
        }

        /* Placeholder legível (antes 28% de branco: ~2:1 sobre o fundo). */
        .form-input::placeholder,
        .form-textarea::placeholder {
            color: rgba(255, 255, 255, .62);
        }

        /* Foco nítido: borda verde cheia + anel (antes só a borda passava
           de 30% para 60% de opacidade — quase imperceptível). */
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            border-color: #6aaa2e;
            background: rgba(255, 255, 255, .09);
            box-shadow: 0 0 0 3px rgba(106, 170, 46, .38);
        }

        .form-textarea {
            padding-left: 18px;
            resize: none;
            height: 140px;
            line-height: 1.6;
        }

        .form-select {
            cursor: pointer;
        }

        .form-select option {
            background: #1a1208;
            color: #fff;
        }

        .form-footer {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .privacy-note {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, .35);
            line-height: 1.5;
        }

        .privacy-note svg {
            width: 14px;
            height: 14px;
            stroke: rgba(255, 255, 255, .3);
            flex-shrink: 0;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #4F7724;
            color: #fff;
            font-weight: 700;
            padding: 18px;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-family: inherit;
            transition: transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) var(--ease-premium), background var(--hover-duration) ease;
            box-shadow: 0 6px 24px rgba(79, 119, 36, .3);
            width: 100%;
        }

        .btn-submit:hover,
        .btn-submit:focus-visible {
            background: #6aaa2e;
            box-shadow: 0 10px 34px rgba(79, 119, 36, .48);
            transform: translateY(-2px);
        }

        .btn-submit:focus-visible {
            outline: 2px solid #e8c463;
            outline-offset: 3px;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit svg {
            width: 18px;
            height: 18px;
            stroke: #fff;
        }

        .btn-submit.loading {
            opacity: .7;
            cursor: not-allowed;
            pointer-events: none;
        }

        .form-success {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: rgba(79, 119, 36, .15);
            border: 1px solid rgba(79, 119, 36, .4);
            border-radius: 14px;
            padding: 20px;
            color: #6aaa2e;
            font-weight: 700;
            font-size: 15px;
        }

        .form-success.show {
            display: flex;
        }

        .form-success svg {
            width: 22px;
            height: 22px;
            stroke: #6aaa2e;
            flex-shrink: 0;
        }

        .contato-disclaimer {
            text-align: center;
            margin-top: 28px;
            font-size: 13px;
            color: rgba(255, 255, 255, .3);
            line-height: 1.7;
        }

        .contato-disclaimer span {
            font-size: 11px;
            color: rgba(255, 255, 255, .15);
        }

        /* ── FOOTER ── */
        footer {
            position: relative;
            background: linear-gradient(165deg, #2a1a0d 0%, #1a1208 65%, #12180a 100%);
            padding: 56px 24px 24px;
        }

        /* Friso decorativo no topo — reforça a identidade (verde da folha +
           dourado do trigo) e separa o rodapé do conteúdo com mais
           personalidade do que uma borda lisa de 1px. */
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, #6aaa2e 35%, #e8c463 50%, #6aaa2e 65%, transparent 100%);
            opacity: .85;
        }

        /* Glow suave de fundo, ecoando o mesmo tratamento de profundidade
           usado nas seções — dá ao rodapé o mesmo acabamento cinematográfico
           em vez de ficar como um bloco plano isolado no fim da página. */
        footer::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background: radial-gradient(ellipse 50% 60% at 15% 0%, rgba(106, 170, 46, .06) 0%, transparent 60%);
            pointer-events: none;
        }

        .footer-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1fr;
            gap: 32px;
        }

        .footer-col-brand {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .footer-logo img {
            width: auto;
            height: 60px;
            object-fit: contain;
            /* Sem "drop-shadow" preto aqui: o rodapé já é escuro
               (footer { background: ...#1a1208... }), então o duplo
               drop-shadow preto e desfocado (0.6/0.4 de opacidade) criava
               uma mancha/halo escuro atrás de toda a logo, em vez de um
               contorno sutil. */
        }

        .footer-logo span {
            color: rgba(255, 255, 255, .97);
            font-weight: 800;
            font-size: 18px;
            letter-spacing: .2px;
        }

        .footer-tagline {
            font-size: 13px;
            color: rgba(255, 255, 255, .68);
            line-height: 1.65;
            max-width: 260px;
        }

        .footer-social {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.35);
            transition: color var(--hover-duration) var(--ease-premium), text-shadow var(--hover-duration) ease, transform var(--hover-duration) var(--ease-premium);
        }

        .footer-social a:hover,
        .footer-social a:focus-visible {
            color: #e8c463;
            text-shadow: 0 2px 8px rgba(232, 196, 99, 0.45);
            transform: translateY(-3px) scale(1.1);
        }

        .footer-col-title {
            position: relative;
            display: inline-block;
            color: rgba(255, 255, 255, .95);
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 14px;
            padding-bottom: 10px;
        }

        /* Pequeno traço sob o título de cada coluna — dá hierarquia ao
           rodapé sem precisar de linhas divisórias entre as colunas
           inteiras. */
        .footer-col-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 26px;
            height: 2px;
            border-radius: 2px;
            background: #6aaa2e;
        }

        .footer-col {
            display: flex;
            flex-direction: column;
            gap: 11px;
        }

        .footer-col a {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 13.5px;
            color: rgba(255, 255, 255, .68);
            text-decoration: none;
            transition: color var(--hover-duration) var(--ease-premium), transform var(--hover-duration) var(--ease-premium);
            width: fit-content;
        }

        /* Celular: área de toque confortável (~40px) nos links do rodapé e
           nas redes sociais, que tinham 15–20px de altura. Só a área
           clicável cresce; o desenho continua o mesmo. */
        @media (max-width: 767px) {
            .footer-social { gap: 6px; margin-left: -10px; }
            .footer-social a { width: 40px; height: 40px; }
            .footer-col { gap: 2px; }
            .footer-col a { min-height: 40px; }
            /* Barra de contato do topo (visível até ~640px): mesmo cuidado. */
            .topbar-contact-item { min-height: 32px; }
            .topbar .topbar-social a { min-width: 32px; min-height: 32px; }
        }

        .footer-col a i {
            width: 13px;
            flex-shrink: 0;
            text-align: center;
            font-size: 12px;
            color: rgba(106, 170, 46, .85);
            transition: color var(--hover-duration) var(--ease-premium);
        }

        /* Setinha que desliza pra dentro no hover — feedback mais rico do
           que só trocar a cor do texto. */
        .footer-col a::before {
            content: '›';
            display: inline-block;
            color: #e8c463;
            font-weight: 700;
            width: 0;
            opacity: 0;
            transform: translateX(-6px);
            transition: width var(--hover-duration) var(--ease-premium), opacity var(--hover-duration) ease, transform var(--hover-duration) var(--ease-premium);
        }

        .footer-col a:hover,
        .footer-col a:focus-visible {
            color: #ffffff;
            transform: translateX(3px);
        }

        .footer-col a:hover i,
        .footer-col a:focus-visible i {
            color: #a3c96e;
        }

        .footer-col a:hover::before,
        .footer-col a:focus-visible::before {
            width: 10px;
            opacity: 1;
            transform: translateX(0);
        }

        .footer-copy {
            font-size: 13px;
            color: rgba(255, 255, 255, .68);
            line-height: 1.75;
        }

        .footer-bottom {
            max-width: 1100px;
            margin: 40px auto 0;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 12px;
            color: rgba(255, 255, 255, .45);
            text-align: center;
            letter-spacing: .2px;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-col-brand {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 480px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .back-to-top {
                right: 82px;
            }
        }

        /* SPLASH */

        #splash {

            position: fixed;
            width: 100%;
            height: 100vh;

            background: radial-gradient(circle, #0f3d0f, #000);

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            /* Tinha o mesmo z-index (999) da barra de contato e da navbar —
               como as duas vêm depois do splash no HTML, elas desenhavam por
               cima dele e apareciam atrás/ao redor da tela de carregamento.
               Precisa ficar acima das duas pra cobrir a tela inteira
               enquanto a splash estiver ativa. */
            z-index: 1000;

            transition: 1s;

        }

        /* TEXTO */

        .logo-text {

            font-family: var(--font-display);
            font-size: 85px;
            font-weight: 700;
            display: flex;
            filter: drop-shadow(0 0 40px rgba(209, 161, 54, .25));

        }

        .agri {

            color: #3f8d3f;

            transform: translateX(-300px);
            opacity: 0;

            animation: agriEntrada 1.4s ease forwards;
            animation-delay: 0.8s;

        }

        .farm {

            color: #6b3f1a;

            transform: translateX(300px);
            opacity: 0;

            animation: farmEntrada 1.4s ease forwards;
            animation-delay: 0.8s;

        }

        @keyframes agriEntrada {

            to {
                transform: translateX(0);
                opacity: 1;
            }

        }

        @keyframes farmEntrada {

            to {
                transform: translateX(0);
                opacity: 1;
            }

        }

        /* SUBTITULO */

        .subtitle {

            margin-top: 25px;
            font-size: 20px;
            letter-spacing: 4px;
            color: #d4d4d4;

            opacity: 0;
            transform: translateY(40px);

            animation: subtitleEntrada 1.2s ease forwards;
            animation-delay: 2s;

        }

        @keyframes subtitleEntrada {

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }

        /* LOADER */

        .loader {

            margin-top: 40px;
            width: 420px;
            max-width: 80vw;
            height: 8px;

            background: #1c3a1c;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: inset 0 1px 4px rgba(0, 0, 0, .45);

            opacity: 0;

            animation: loaderFade 1s forwards;
            animation-delay: 2.5s;

        }

        @keyframes loaderFade {

            to {
                opacity: 1;
            }

        }

        /* Antes a barra começava a encher em 1.6s (delay: "load 3s
           linear") enquanto o container (".loader") ainda estava
           INVISÍVEL — só ficava opaco de verdade em 3.5s. Resultado: ela
           "aparecia" já ~65% cheia, num salto seco, e ainda terminava
           cortada (o splash saía da tela aos 4s, antes dos 4.6s que a
           animação levava pra completar). Agora ela só começa a encher
           quando o container já está visível (mesmo delay, 2.5s), com uma
           curva suave (--ease-premium, desacelera no final) em vez de
           "linear" — e o setTimeout do splash (script inline logo abaixo
           deste arquivo) foi ajustado pra dar tempo dela terminar antes de
           a tela subir. */
        .loader-bar {

            height: 100%;
            width: 0%;
            border-radius: 20px;

            background: linear-gradient(90deg, #3f8d3f, #d1a136 55%, #6b3f1a);
            box-shadow: 0 0 14px rgba(209, 161, 54, .55);

            animation: load 3.4s var(--ease-premium) forwards;
            animation-delay: 2.5s;

        }

        @keyframes load {

            0% {
                width: 0
            }

            100% {
                width: 100%
            }

        }

        /* ANIMAÇÃO SUBIR */

        .up {

            transform: translateY(-100vh);
            opacity: 0;

        }

        /* HOME PAGE */

        #home {

            height: 100vh;

            background: #f5f5f5;

            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;

            opacity: 0;

            transition: 1.2s;

        }

        #home h1 {

            font-size: 55px;
            color: #2c2c2c;

        }

        #app {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease, transform 1s ease;
        }

        #app.show {
            opacity: 1;
            transform: translateY(0);
        }

        #home p {

            margin-top: 10px;
            font-size: 20px;

        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            /* background-attachment:fixed é pesado/instável em scroll de
               mobile (Safari em particular) — nessas telas os glows radiais
               das seções passam a rolar junto com o conteúdo normalmente. */
            #problema::before,
            #como-funciona::before,
            #beneficios::before,
            #diferenciais::before,
            #sobre::before,
            #contato::before,
            #solucao::before,
            #faq::before {
                background-attachment: scroll;
            }

            .nav-links {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .problema-grid,
            .impacto-grid {
                grid-template-columns: 1fr;
                gap: 48px;
                margin-top: 56px;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .step-row {
                grid-template-columns: 1fr;
            }

            .step-num {
                display: none;
            }

            .steps::before {
                display: none;
            }

            .diff-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .sol-features {
                grid-template-columns: 1fr;
            }

            .sol-stats-row {
                grid-template-columns: 1fr;
            }

            .team-grid {
                grid-template-columns: 1fr;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-field.full {
                grid-column: 1;
            }

            .footer-inner {
                flex-direction: column;
                text-align: center;
            }

            .solucao-card {
                padding: 36px 20px;
            }

            .form-container {
                padding: 28px 20px;
            }

            .faq-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ==========================================================================
        TEMA CLARO (opção própria desta página, adicionada ao painel de
        acessibilidade por um script no fim da página — o painel compartilhado
        só tem Modo Escuro/Alto Contraste; aqui isso vira um terceiro
        interruptor "Tema Claro"). O visual padrão da página (sem nenhum tema
        selecionado) CONTINUA sendo o design original escuro — este bloco só
        entra em vigor com body.light-theme, como alternativa clara e
        profissional, nunca como substituição do padrão. O vídeo do hero, a
        navbar e a barra de contato permanecem como são (a marca não muda).
        ========================================================================== */
        body.light-theme {
            /* Composição sutil em vez de uma cor lisa única: um leve tom
               esverdeado no topo que se dissolve no fundo-base ao longo da
               página — dá uma sensação de profundidade sem virar um
               gradiente chamativo. */
            background:
                radial-gradient(ellipse 1200px 500px at 50% 0%, rgba(106, 170, 46, 0.06) 0%, transparent 60%),
                var(--bg-light);
            color: var(--text-on-light);

            /* Painel de Acessibilidade também vira claro junto com o resto
               da página — só branco e verde claro, sem tom amarelado.
               --panel-btn-bg era quase idêntico a --panel-item-bg (dois
               verdes bem parecidos): os botões (+/-/Padrão) ficavam quase
               invisíveis dentro do próprio cartão. Vira branco — bom
               contraste contra o cartão verde-claro, com o verde reservado
               pro hover (dá pra ver o botão "acender" ao passar o mouse). */
            --panel-bg: #ffffff;
            --panel-text: var(--text-on-light);
            --panel-item-bg: var(--bg-light);
            --panel-btn-bg: #ffffff;
            --panel-btn-text: #24382b;
            --panel-btn-hover: var(--bg-light);

            /* Usada por textos "apagados" da página inteira (incluindo os
               rótulos do próprio Painel de Acessibilidade, ex.: "PREFERÊNCIAS
               DE TEMA"). O valor original era um branco bem transparente,
               pensado pra fundo escuro — no card branco do painel isso
               ficava quase invisível ("letras bugadas"). */
            --text-muted: var(--text-on-light-muted);
        }

        /* Navbar (topbar + nav): fica ESCURA também no Tema Claro, com a
           mesma identidade visual do modo escuro original — maior
           contraste, aparência mais profissional e uma "âncora" visual
           fixa que não muda de aparência conforme o tema do conteúdo
           abaixo dela muda. Por isso nenhuma regra própria é necessária
           aqui: sem override, ela simplesmente herda o estilo escuro
           padrão (fundo, links, logo, botão "Acessar Sistema", botão de
           acessibilidade e hambúrguer todos já pensados pra esse fundo
           escuro e já validados em WCAG AA lá).  */

        /* ── HERO: fica SEMPRE com o visual escuro original (vídeo +
           overlay + textos brancos), mesmo no Tema Claro — só essa seção,
           sem o rodapé, que continua trocando normalmente. Igual à navbar
           (comentário logo acima), é uma "âncora" visual que não muda de
           aparência conforme o tema do resto da página. Chegou a existir
           uma versão clara aqui (overlay mais claro, textos verde-escuro,
           cards brancos); foi removida a pedido — o hero volta a ser
           idêntico em qualquer tema. ── */

        /* ── PROBLEMA: o "vermelho" de alerta (ícone X e o card de
           desperdício) vira verde no tema claro, pra manter só
           branco/verde na paleta — a diferença entre "bom" e "ruim"
           continua clara pelo formato do ícone (check vs. X), não pela
           cor. ── */
        body.light-theme .check-icon.bad {
            background: rgba(79, 119, 36, .12);
            border-color: rgba(79, 119, 36, .3);
        }

        body.light-theme .check-icon.bad svg {
            stroke: var(--green-dark);
        }

        body.light-theme .data-card.red {
            background: linear-gradient(135deg, rgba(79, 119, 36, .12), rgba(79, 119, 36, .04));
            border-color: rgba(79, 119, 36, .3);
        }

        body.light-theme .data-card.red .num {
            color: var(--green-dark);
        }

        /* Mesmo verde de fundo, plano, em todas as seções/páginas do
           tema claro — antes cada uma tinha um gradiente diferente
           (ou nem tinha), o que fazia a cor "pular" de página em
           página. */
        body.light-theme #problema,
        body.light-theme #como-funciona,
        body.light-theme #impacto,
        body.light-theme #sobre,
        body.light-theme #solucao,
        body.light-theme #beneficios,
        body.light-theme #diferenciais,
        body.light-theme #contato {
            background: var(--bg-light);
            color: var(--text-on-light);
        }

        body.light-theme footer {
            background: var(--bg-light-alt);
            color: var(--text-on-light);
            border-top-color: var(--card-light-border);
        }

        /* Ícones sociais e a linha divisória de ".footer-bottom" eram
           branco/quase-branco (pensados pro rodapé escuro original) — sobre
           o fundo claro acima ficavam quase invisíveis (baixíssimo
           contraste), como se só o resto do rodapé tivesse virado claro. */
        body.light-theme .footer-social a {
            color: var(--green-dark);
            text-shadow: none;
        }

        body.light-theme .footer-social a:hover,
        body.light-theme .footer-social a:focus-visible {
            color: #b45309;
            text-shadow: none;
        }

        body.light-theme .footer-bottom {
            border-top-color: var(--card-light-border);
        }

        /* Textos que eram brancos/var(--text-muted) direto (pensados pra
           fundo escuro) precisam virar escuros no tema claro. */
        body.light-theme .section-title,
        body.light-theme .problema-intro,
        body.light-theme .check-item span,
        body.light-theme .quote-block,
        body.light-theme .data-card p,
        body.light-theme .sol-text,
        body.light-theme .sol-feat .f-title,
        body.light-theme .sol-feat .f-desc,
        body.light-theme .step-title,
        body.light-theme .step-desc,
        body.light-theme .benefit-title,
        body.light-theme .benefit-desc,
        body.light-theme .impacto-box h3,
        body.light-theme .impacto-box p,
        body.light-theme .mini-stat .l,
        body.light-theme .diff-card p,
        body.light-theme .faq-subtitle,
        body.light-theme .faq-question,
        body.light-theme .faq-question-text,
        body.light-theme .faq-answer,
        body.light-theme .faq-answer strong,
        body.light-theme .faq-cta p,
        body.light-theme .sobre-text,
        body.light-theme .team-grid :is(h2, h3),
        body.light-theme .member-name,
        body.light-theme .member-role,
        body.light-theme .form-label,
        body.light-theme .footer-logo span,
        body.light-theme .footer-col-title,
        body.light-theme .footer-copy,
        body.light-theme .footer-bottom,
        body.light-theme .footer-col a {
            color: var(--text-on-light);
        }

        /* Textos secundários (mais discretos que o texto principal) */
        body.light-theme .problema-intro,
        body.light-theme .quote-block,
        body.light-theme .data-card p,
        body.light-theme .step-desc,
        body.light-theme .benefit-desc,
        body.light-theme .impacto-box p,
        body.light-theme .mini-stat .l,
        body.light-theme .diff-card p,
        body.light-theme .faq-subtitle,
        body.light-theme .faq-answer,
        body.light-theme .faq-cta p,
        body.light-theme .sobre-text,
        body.light-theme .member-role,
        body.light-theme .form-label,
        body.light-theme .footer-tagline,
        body.light-theme .footer-copy {
            color: var(--text-on-light-muted);
        }

        /* Tons de verde/marrom claros demais pra funcionar em fundo escuro
           ficam ilegíveis em fundo claro — escurecidos só neste tema. */
        body.light-theme .section-tag,
        body.light-theme .step-label {
            color: var(--green-dark);
        }

        body.light-theme #problema .accent {
            color: var(--green-dark);
        }

        body.light-theme .sol-title-badge,
        body.light-theme .impacto-quote {
            color: var(--green-dark);
        }

        body.light-theme .member-avatar {
            color: var(--green-dark);
        }

        /* Os cards amarelo/laranja mantinham o fundo escuro translúcido
           original (pensado pra ficar sobre o hero escuro) — sobre o fundo
           claro isso resultava numa cor terrosa neutra por trás do número,
           derrubando o contraste do texto pra ~2.5:1 (abaixo do mínimo
           3:1 do WCAG AA pra texto grande). Ganham o mesmo tratamento do
           card vermelho: fundo bem claro na própria cor, texto mais escuro. */
        body.light-theme .data-card.yellow {
            background: linear-gradient(135deg, rgba(217, 119, 6, .08), rgba(217, 119, 6, .03));
            border-color: rgba(217, 119, 6, .3);
        }

        body.light-theme .data-card.orange {
            background: linear-gradient(135deg, rgba(194, 65, 12, .08), rgba(194, 65, 12, .03));
            border-color: rgba(194, 65, 12, .3);
        }

        body.light-theme .data-card.yellow .num {
            color: #b45309;
        }

        body.light-theme .data-card.orange .num {
            color: #c2410c;
        }

        /* Cartões translúcidos escuros → cartões brancos com sombra suave */
        body.light-theme .sol-feat,
        body.light-theme .sol-stat,
        body.light-theme .step-card,
        body.light-theme .benefit-card,
        body.light-theme .mini-stat,
        body.light-theme .faq-item,
        body.light-theme .member,
        body.light-theme .form-container,
        body.light-theme .faq-cta {
            background: var(--card-light-bg);
            border-color: var(--card-light-border);
            box-shadow: var(--card-light-shadow);
        }

        /* Os três cards abaixo (solução, impacto, diferenciais) ficavam
           brancos idênticos aos demais, sem nada que os distinguisse uns
           dos outros — cada um ganha um tratamento próprio, sem sair da
           paleta branco/verde-claro já definida pro tema claro. */
        body.light-theme .solucao-card {
            background: linear-gradient(150deg, #eef8ee 0%, #ffffff 65%);
            border-color: var(--card-light-border);
            box-shadow: var(--card-light-shadow);
        }

        body.light-theme .impacto-box {
            background: linear-gradient(160deg, #e3f3e6 0%, #f6fbf3 100%);
            border-color: rgba(79, 119, 36, .25);
        }

        body.light-theme .diff-card {
            background: #f5fbf6;
            border-color: var(--card-light-border);
        }

        body.light-theme .step-card:hover,
        body.light-theme .diff-card:hover,
        body.light-theme .faq-item:hover,
        body.light-theme .faq-item.open {
            border-color: rgba(79, 119, 36, .4);
        }

        /* Card "Entre em Contato": linhas (bordas do card e dos campos,
           ícones dos campos) e escritas (rótulos) em verde, em vez do
           contorno/texto meio acinzentado que sobrava do tema escuro. */
        body.light-theme .form-container {
            border-color: rgba(79, 119, 36, .35);
        }

        body.light-theme .form-label {
            color: var(--green-dark);
        }

        body.light-theme .form-input-wrap .input-icon {
            stroke: var(--green-dark);
        }

        /* Campos de formulário */
        body.light-theme .form-input,
        body.light-theme .form-textarea,
        body.light-theme .form-select {
            background: #ffffff;
            border-color: rgba(79, 119, 36, .3);
            color: var(--text-on-light);
        }

        body.light-theme .form-select option {
            background: #ffffff;
            color: var(--text-on-light);
        }

        body.light-theme .form-input::placeholder,
        body.light-theme .form-textarea::placeholder {
            color: rgba(28, 39, 22, .62);
        }

        /* Sem isto a borda de foco do tema claro ficava igual à de repouso
           (a regra acima é mais específica que ".form-input:focus"). */
        body.light-theme .form-input:focus,
        body.light-theme .form-textarea:focus,
        body.light-theme .form-select:focus {
            border-color: #3d7a1f;
            box-shadow: 0 0 0 3px rgba(61, 122, 31, .25);
        }

        /* Círculos/ícones translúcidos verdes continuam verdes, só ajusta o fundo */
        body.light-theme .sol-icon,
        body.light-theme .step-icon-box,
        body.light-theme .benefit-icon,
        body.light-theme .d-icon,
        body.light-theme .faq-icon,
        body.light-theme .sol-feat .f-icon {
            background: rgba(79, 119, 36, .1);
            border-color: rgba(79, 119, 36, .3);
        }

        /* O ".sol-icon" (círculo grande no topo do card "A solução") é o
           único ícone com um glow forte + reflexo branco por dentro,
           pensados pro card escuro original — sobre o card branco do tema
           claro isso virava uma névoa esbranquiçada por cima do ícone,
           deixando-o com aparência "quebrada". */
        body.light-theme .sol-icon {
            box-shadow: 0 10px 24px -8px rgba(79, 119, 36, .35);
        }

        /* O hover do card de benefício ainda escurece revelando a foto —
           o texto continua branco só nesse momento, sobre a foto escurecida. */
        body.light-theme .benefit-card:hover .benefit-title,
        body.light-theme .benefit-card:hover .benefit-desc {
            color: #fff;
        }

        /* "Modo Escuro" nesta página = o design original (nenhuma regra
           própria necessária: é o que já aparece sem nenhuma classe extra
           no <body>). O interruptor "Modo Escuro" do painel de
           acessibilidade agora só alterna entre esse original e o Tema
           Claro (bloco "TEMA CLARO" acima) — ver script no fim da página. */

        /* ==========================================================================
        ALTO CONTRASTE (via partials.acessibilidade)
        Página fixa em tom escuro por design (landing/marketing), então o
        alto contraste não tenta imitar as cores originais — ele zera fundos,
        gradientes e imagens decorativas para preto puro e força todo texto,
        borda e ícone para amarelo, seguindo o mesmo padrão preto/amarelo
        usado no resto do sistema (ver dashboard.blade.php).
        ========================================================================== */
        body.high-contrast {
            background: #000000 !important;
            color: #ffff00 !important;

            /* Painel de Acessibilidade em preto/amarelo também. */
            --panel-bg: #000000;
            --panel-text: #ffffff;
            --panel-item-bg: #111111;
            --panel-btn-bg: #ffffff;
            --panel-btn-text: #000000;
            --panel-btn-hover: #ffff00;
        }

        body.high-contrast #hero-video,
        body.high-contrast .hero-overlay,
        body.high-contrast .hero-glow,
        body.high-contrast .hero-orb,
        body.high-contrast #impacto .bg-img,
        body.high-contrast #solucao::before,
        body.high-contrast #faq::before,
        body.high-contrast #problema::before,
        body.high-contrast #como-funciona::before,
        body.high-contrast #beneficios::before,
        body.high-contrast #diferenciais::before,
        body.high-contrast #sobre::before,
        body.high-contrast #contato::before,
        body.high-contrast .benefit-card-bg,
        body.high-contrast .benefit-card::after,
        body.high-contrast .solucao-card::before,
        body.high-contrast .form-container::before {
            display: none !important;
        }

        /* Tilt/parallax 3D desligados no Alto Contraste — a movimentação
           só atrapalharia a leitura, e o contraste preto/amarelo já não
           depende de profundidade visual. */
        body.high-contrast .benefit-card,
        body.high-contrast .diff-card,
        body.high-contrast .step-card,
        body.high-contrast .faq-item,
        body.high-contrast .mini-stat,
        body.high-contrast .data-card,
        body.high-contrast .sol-feat,
        body.high-contrast .stat-card,
        body.high-contrast .member,
        body.high-contrast .solucao-card,
        body.high-contrast .impacto-box {
            transform: none !important;
        }

        body.high-contrast nav,
        body.high-contrast .mobile-menu,
        body.high-contrast section,
        body.high-contrast footer,
        body.high-contrast .data-card,
        body.high-contrast .solucao-card,
        body.high-contrast .sol-feat,
        body.high-contrast .sol-stat,
        body.high-contrast .step-card,
        body.high-contrast .benefit-card,
        body.high-contrast .impacto-box,
        body.high-contrast .mini-stat,
        body.high-contrast .diff-card,
        body.high-contrast .faq-item,
        body.high-contrast .form-container,
        body.high-contrast .team-grid,
        body.high-contrast .member,
        body.high-contrast .stat-card,
        body.high-contrast .hero-tag,
        body.high-contrast .form-success {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
        }

        body.high-contrast * {
            color: #ffff00 !important;
            text-shadow: none !important;
        }

        body.high-contrast .hero-title .accent,
        body.high-contrast .accent {
            background: none !important;
            -webkit-background-clip: unset !important;
            -webkit-text-fill-color: #ffffff !important;
            color: #ffffff !important;
        }

        body.high-contrast svg,
        body.high-contrast .tag-dot,
        body.high-contrast .check-icon.good svg,
        body.high-contrast .check-icon.bad svg {
            stroke: #ffff00 !important;
            fill: none !important;
        }

        body.high-contrast .btn-primary,
        body.high-contrast .btn-nav,
        body.high-contrast .btn-secondary,
        body.high-contrast .btn-submit,
        body.high-contrast .faq-cta a,
        body.high-contrast .form-input,
        body.high-contrast .form-textarea,
        body.high-contrast .form-select,
        body.high-contrast .check-icon,
        body.high-contrast .step-icon-box,
        body.high-contrast .step-num,
        body.high-contrast .sol-icon,
        body.high-contrast .benefit-icon,
        body.high-contrast .f-icon,
        body.high-contrast .d-icon,
        body.high-contrast .member-avatar,
        body.high-contrast .faq-icon {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .btn-primary:hover,
        body.high-contrast .btn-nav:hover,
        body.high-contrast .btn-secondary:hover,
        body.high-contrast .btn-submit:hover,
        body.high-contrast .faq-cta a:hover {
            background: #ffff00 !important;
            color: #000000 !important;
        }

        body.high-contrast .btn-primary:hover svg,
        body.high-contrast .btn-nav:hover svg,
        body.high-contrast .btn-submit:hover svg,
        body.high-contrast .faq-cta a:hover svg {
            stroke: #000000 !important;
        }

        /* Esta página não tem .navbar-right (nav própria de landing page),
           então o auto-move padrão do partials.acessibilidade (que procura
           .navbar-right) nunca acha destino e o botão ficava flutuando
           solto. O script no fim da página move o botão pra dentro de
           #navAccessibilitySlot, no meio da navbar — aqui só o estilo pra
           ele virar um item de navbar normal (mesma classe usada quando o
           auto-move padrão funciona). */
        .accessibility-trigger.acc-trigger-inline {
            position: static;
            width: 38px;
            height: 38px;
            font-size: 17px;
            box-shadow: none;
        }

        /* O seletor universal "body.high-contrast *" não alcança
           pseudo-elementos (::before/::after) — sem isso, os detalhes
           decorativos verdes/dourados (sublinhado do menu, friso do
           rodapé, setinha dos links) continuavam coloridos mesmo no Alto
           Contraste, quebrando o preto/amarelo puro exigido pelo modo. */
        body.high-contrast .nav-links a::after,
        body.high-contrast .footer-col-title::after {
            background: #ffff00 !important;
        }

        body.high-contrast .footer-col a::before {
            color: #ffff00 !important;
        }

        body.high-contrast footer::before {
            background: #ffff00 !important;
            opacity: 1 !important;
        }

        body.high-contrast .footer-social a,
        body.high-contrast .topbar-social a,
        body.high-contrast .nav-links a.active,
        body.high-contrast .nav-links a:not(.btn-nav):hover {
            background: #000000 !important;
        }

        /* Cartões/itens que ganham sombra ou elevação no hover (fora dos já
           cobertos acima) — no Alto Contraste a sombra unicamente cria
           ruído visual sem ajudar leitura, então fica só a borda amarela
           mais grossa marcando o foco/hover. */
        body.high-contrast .data-card:hover,
        body.high-contrast .mini-stat:hover,
        body.high-contrast .member:hover,
        body.high-contrast .step-card:hover,
        body.high-contrast .diff-card:hover,
        body.high-contrast .sol-feat:hover,
        body.high-contrast .faq-item:hover,
        body.high-contrast .benefit-card:hover {
            box-shadow: none !important;
            border-width: 3px !important;
        }

        body.high-contrast .logo img,
        body.high-contrast .footer-logo img {
            filter: brightness(0) invert(1) !important;
        }
    </style>
