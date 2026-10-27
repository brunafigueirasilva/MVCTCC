<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AgriFarm | Gerenciamento Inteligente de Ração</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --verde-escuro: #0E4A27;
            --verde: #16A765;
            --verde-logo: #4F7724;
            --verde-claro: #6AAA2E;
            --marrom: #6B4226;
            --marrom-escuro: #2A180D;
            --preto: #07130C;
            --branco: #FFFFFF;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(
                    circle at 50% 42%,
                    rgba(22, 167, 101, 0.14),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 20% 80%,
                    rgba(79, 119, 36, 0.10),
                    transparent 30%
                ),
                linear-gradient(
                    135deg,
                    #07130C 0%,
                    #0B2415 48%,
                    #102D1A 100%
                );

            color: var(--branco);
        }

        .welcome {
            position: relative;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* =========================================
           FUNDO
        ========================================= */

        .background-glow {
            position: absolute;
            width: 650px;
            height: 650px;
            border-radius: 50%;

            background: radial-gradient(
                circle,
                rgba(22, 167, 101, 0.13),
                rgba(22, 167, 101, 0.03) 45%,
                transparent 72%
            );

            filter: blur(25px);
            animation: glow 9s ease-in-out infinite;
        }

        @keyframes glow {
            0%,
            100% {
                transform: scale(0.95);
                opacity: 0.65;
            }

            50% {
                transform: scale(1.08);
                opacity: 1;
            }
        }

        .grid {
            position: absolute;
            inset: 0;

            background-image:
                linear-gradient(
                    rgba(106, 170, 46, 0.035) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(106, 170, 46, 0.035) 1px,
                    transparent 1px
                );

            background-size: 70px 70px;

            mask-image: radial-gradient(
                circle at center,
                black,
                transparent 75%
            );

            opacity: 0.5;
        }

        /* =========================================
           PARTÍCULAS
        ========================================= */

        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(106, 170, 46, 0.65);

            box-shadow:
                0 0 12px rgba(106, 170, 46, 0.5);

            animation: particleFloat linear infinite;
        }

        .particle:nth-child(1) {
            left: 12%;
            top: 24%;
            animation-duration: 10s;
        }

        .particle:nth-child(2) {
            left: 23%;
            top: 70%;
            animation-duration: 13s;
        }

        .particle:nth-child(3) {
            left: 78%;
            top: 20%;
            animation-duration: 11s;
        }

        .particle:nth-child(4) {
            left: 88%;
            top: 63%;
            animation-duration: 15s;
        }

        .particle:nth-child(5) {
            left: 67%;
            top: 78%;
            animation-duration: 12s;
        }

        .particle:nth-child(6) {
            left: 34%;
            top: 17%;
            animation-duration: 14s;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(20px);
                opacity: 0;
            }

            20% {
                opacity: 0.8;
            }

            80% {
                opacity: 0.5;
            }

            100% {
                transform: translateY(-90px);
                opacity: 0;
            }
        }

        /* =========================================
           CONTEÚDO
        ========================================= */

        .content {
            position: relative;
            z-index: 5;

            width: 100%;
            max-width: 900px;

            padding: 40px 24px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-align: center;
        }

        /* =========================================
           LOGO
        ========================================= */

        .logo-wrapper {
            position: relative;

            width: 220px;
            height: 220px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 28px;

            animation:
                logoEntrance 3s cubic-bezier(.22,.61,.36,1) forwards,
                logoFloat 8s ease-in-out 3s infinite;
        }

        .logo-wrapper::before {
            content: "";

            position: absolute;
            inset: 15px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(22, 167, 101, 0.20),
                    rgba(79, 119, 36, 0.07) 45%,
                    transparent 70%
                );

            filter: blur(12px);

            animation: logoAura 7s ease-in-out infinite;
        }

        .logo-wrapper::after {
            content: "";

            position: absolute;
            width: 180px;
            height: 180px;

            border-radius: 50%;

            border: 1px solid rgba(106, 170, 46, 0.16);

            animation: ringPulse 6s ease-in-out infinite;
        }

        .logo {
            position: relative;
            z-index: 2;

            width: 155px;
            max-width: 70vw;

            object-fit: contain;

            filter:
                drop-shadow(0 0 10px rgba(22, 167, 101, 0.25))
                drop-shadow(0 10px 30px rgba(0, 0, 0, 0.35));

            opacity: 0;

            animation:
                logoReveal 3.2s cubic-bezier(.22,.61,.36,1) 0.3s forwards;
        }

        @keyframes logoEntrance {
            0% {
                transform: scale(0.78);
                opacity: 0;
            }

            65% {
                transform: scale(1.025);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes logoReveal {
            0% {
                opacity: 0;
                filter:
                    blur(8px)
                    drop-shadow(0 0 0 rgba(22, 167, 101, 0));
            }

            55% {
                opacity: 0.75;
                filter:
                    blur(2px)
                    drop-shadow(0 0 25px rgba(22, 167, 101, 0.35));
            }

            100% {
                opacity: 1;
                filter:
                    blur(0)
                    drop-shadow(0 0 10px rgba(22, 167, 101, 0.25))
                    drop-shadow(0 10px 30px rgba(0, 0, 0, 0.35));
            }
        }

        @keyframes logoFloat {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-7px);
            }
        }

        @keyframes logoAura {
            0%,
            100% {
                transform: scale(0.88);
                opacity: 0.45;
            }

            50% {
                transform: scale(1.12);
                opacity: 0.85;
            }
        }

        @keyframes ringPulse {
            0%,
            100% {
                transform: scale(0.88);
                opacity: 0.15;
            }

            50% {
                transform: scale(1.12);
                opacity: 0.45;
            }
        }

        /* =========================================
           TEXTO
        ========================================= */

        .brand {
            font-size: clamp(38px, 7vw, 76px);

            font-weight: 800;

            letter-spacing: 0.12em;

            line-height: 1;

            margin-left: 0.12em;

            background:
                linear-gradient(
                    110deg,
                    #FFFFFF 15%,
                    #B7D99A 45%,
                    #6AAA2E 70%,
                    #16A765 100%
                );

            -webkit-background-clip: text;
            background-clip: text;

            -webkit-text-fill-color: transparent;

            opacity: 0;

            animation:
                textReveal 2.5s ease 1.2s forwards;
        }

        .subtitle {
            margin-top: 17px;

            font-size: clamp(14px, 2vw, 19px);

            font-weight: 400;

            letter-spacing: 0.18em;

            color: rgba(255, 255, 255, 0.72);

            opacity: 0;

            animation:
                textReveal 2.5s ease 1.7s forwards;
        }

        .line {
            width: 0;
            height: 1px;

            margin: 28px 0 24px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    #16A765,
                    #6AAA2E,
                    transparent
                );

            animation:
                lineReveal 2.5s ease 2s forwards;
        }

        .description {
            max-width: 570px;

            font-size: 14px;

            line-height: 1.8;

            color: rgba(255, 255, 255, 0.52);

            opacity: 0;

            animation:
                textReveal 2.5s ease 2.2s forwards;
        }

        @keyframes textReveal {
            from {
                opacity: 0;
                transform: translateY(15px);
                filter: blur(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }

        @keyframes lineReveal {
            from {
                width: 0;
                opacity: 0;
            }

            to {
                width: 190px;
                opacity: 1;
            }
        }

        /* =========================================
           BOTÃO
        ========================================= */

        .actions {
            margin-top: 34px;

            opacity: 0;

            animation:
                textReveal 2.5s ease 2.5s forwards;
        }

        .enter-button {
            position: relative;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;

            min-width: 190px;

            padding: 14px 28px;

            border-radius: 999px;

            border: 1px solid rgba(106, 170, 46, 0.5);

            background:
                linear-gradient(
                    135deg,
                    rgba(22, 167, 101, 0.20),
                    rgba(79, 119, 36, 0.16)
                );

            color: white;

            text-decoration: none;

            font-size: 14px;
            font-weight: 600;

            letter-spacing: 0.05em;

            overflow: hidden;

            transition:
                transform .45s ease,
                border-color .45s ease,
                box-shadow .45s ease,
                background .45s ease;
        }

        .enter-button::before {
            content: "";

            position: absolute;

            top: 0;
            left: -120%;

            width: 80%;
            height: 100%;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    rgba(255,255,255,.16),
                    transparent
                );

            transform: skewX(-20deg);

            transition: left .8s ease;
        }

        .enter-button:hover {
            transform: translateY(-3px);

            border-color: rgba(106, 170, 46, 0.9);

            background:
                linear-gradient(
                    135deg,
                    rgba(22, 167, 101, 0.34),
                    rgba(79, 119, 36, 0.25)
                );

            box-shadow:
                0 12px 35px rgba(22, 167, 101, 0.18),
                inset 0 0 20px rgba(106, 170, 46, 0.08);
        }

        .enter-button:hover::before {
            left: 140%;
        }

        .arrow {
            font-size: 18px;

            transition:
                transform .45s ease;
        }

        .enter-button:hover .arrow {
            transform: translateX(5px);
        }

        /* =========================================
           RODAPÉ
        ========================================= */

        .footer {
            position: absolute;

            bottom: 24px;

            left: 0;
            right: 0;

            z-index: 5;

            text-align: center;

            font-size: 10px;

            letter-spacing: 0.16em;

            text-transform: uppercase;

            color: rgba(255, 255, 255, 0.25);
        }

        /* =========================================
           RESPONSIVO
        ========================================= */

        @media (max-width: 600px) {

            .content {
                padding: 25px 20px;
            }

            .logo-wrapper {
                width: 175px;
                height: 175px;
                margin-bottom: 20px;
            }

            .logo-wrapper::after {
                width: 145px;
                height: 145px;
            }

            .logo {
                width: 125px;
            }

            .brand {
                font-size: 35px;
                letter-spacing: 0.08em;
            }

            .subtitle {
                font-size: 11px;
                letter-spacing: 0.12em;
                line-height: 1.7;
            }

            .description {
                font-size: 12px;
                max-width: 330px;
                line-height: 1.7;
            }

            .line {
                margin: 21px 0;
            }

            .actions {
                margin-top: 27px;
            }

            .enter-button {
                min-width: 175px;
                padding: 13px 23px;
            }

            .footer {
                bottom: 15px;
                font-size: 8px;
            }
        }

        /* =========================================
           REDUÇÃO DE MOVIMENTO
        ========================================= */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body>

<div class="welcome">

    <div class="background-glow"></div>
    <div class="grid"></div>

    <div class="particles">
        <span class="particle"></span>
        <span class="particle"></span>
        <span class="particle"></span>
        <span class="particle"></span>
        <span class="particle"></span>
        <span class="particle"></span>
    </div>

    <main class="content">

        <div class="logo-wrapper">

            <img
                src="{{ asset('images/logo.png') }}"
                alt="AgriFarm"
                class="logo"
            >

        </div>

        <h1 class="brand">
            AGRI FARM
        </h1>

        <p class="subtitle">
            GERENCIAMENTO INTELIGENTE DE RAÇÃO
        </p>

        <div class="line"></div>

        <p class="description">
            Tecnologia e IoT para transformar o gerenciamento
            de ração em uma operação mais inteligente,
            eficiente e conectada.
        </p>

        <div class="actions">

            <a
                href="{{ route('login') }}"
                class="enter-button"
            >
                Acessar sistema

                <span class="arrow">
                    →
                </span>
            </a>

        </div>

    </main>

    <div class="footer">
        AGRIFARM · TECNOLOGIA PARA O AGRONEGÓCIO
    </div>

</div>

</body>
</html>