<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFarm | Avaliações</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ==========================================================================
        1. RESET
        ========================================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        html { scroll-behavior: smooth; }

        :root {
            /* Painel de conteúdo — mesma paleta escura usada por padrão em
               todas as outras páginas públicas (problema, solução etc, ver
               partials/landing-styles.blade.php), pra essa tela não parecer
               de outro sistema. O Tema Claro (opt-in, painel de
               acessibilidade) redefine tudo em body.light-theme abaixo. */
            --bg-principal: #0a1810;
            --text-principal: #f1f9f4;
            --text-secundario: #9db8a8;
            --card-bg: #12251a;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
            --card-border: 1px solid rgba(106, 170, 46, 0.16);

            /* Aliases genéricos — mesmos nomes usados em
               partials/landing-styles.blade.php, pra facilitar auditar a
               identidade visual em qualquer página do site. */
            --bg-primary: var(--bg-principal);
            --bg-secondary: var(--card-bg);
            --surface: var(--card-bg);
            --text-primary: var(--text-principal);
            --border-color: rgba(106, 170, 46, 0.16);

            --card-verde-bg: rgba(106, 170, 46, 0.1);
            --card-verde-border: rgba(106, 170, 46, 0.3);
            --card-verde-texto: #6aaa2e;

            --card-vermelho-bg: rgba(239, 68, 68, 0.12);
            --card-vermelho-border: rgba(239, 68, 68, 0.3);
            --card-vermelho-texto: #fca5a5;

            --chart-grid: rgba(106, 170, 46, 0.1);

            /* Cabeçalho/rodapé (idênticos ao restante do site público —
               sempre verde escuro, não reagem ao tema claro/escuro do
               conteúdo). */
            --panel-bg: #12251a;
            --panel-text: #f1f9f4;
            --panel-item-bg: #0e1f16;
            --panel-btn-bg: #1e3a2a;
            --panel-btn-text: #d9ede1;
            --panel-btn-hover: #2a4d38;
        }

        body {
            background:
                radial-gradient(ellipse 1200px 500px at 50% 0%, rgba(106, 170, 46, 0.06) 0%, transparent 60%),
                var(--bg-principal);
            color: var(--text-principal);
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        body.acc-modal-open .site-header,
        body.acc-modal-open .main {
            filter: blur(6px);
            pointer-events: none;
        }

        /* ==========================================================================
        2. BARRA DE CONTATO + NAVBAR (padrão do sistema — mesmo estilo da
        landing page, ver partials/landing-styles.blade.php)
        ========================================================================== */
        .topbar {
            height: 40px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            background: #182f0e;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .topbar-inner {
            max-width: 1500px; height: 100%; margin: 0 auto; padding: 0 32px;
            display: flex; align-items: center; justify-content: space-between;
        }

        .topbar-contact { display: flex; align-items: center; gap: 22px; }
        .topbar-contact-item {
            display: flex; align-items: center; gap: 6px;
            color: rgba(214, 240, 210, 0.85); text-decoration: none;
            font-size: 11.5px; letter-spacing: 0.2px; transition: color 0.2s ease;
        }
        .topbar-contact-item svg { width: 12px; height: 12px; flex-shrink: 0; }
        .topbar-contact-item:hover, .topbar-contact-item:focus-visible { color: #ffffff; }

        .topbar-social { display: flex; align-items: center; gap: 14px; }
        .topbar-social a { display: flex; align-items: center; justify-content: center; color: rgba(255, 255, 255, 0.7); font-size: 13px; transition: color 0.2s ease, transform 0.2s ease; }
        .topbar-social a:hover, .topbar-social a:focus-visible { color: #6aaa2e; transform: translateY(-1px); }

        @media (max-width: 576px) { .topbar { display: none; } }

        nav {
            height: 66px;
            position: fixed;
            top: 40px;
            left: 0;
            width: 100%;
            z-index: 999;
            background: #123f22;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 576px) {
            nav { top: 0; }
        }

        .nav-inner {
            max-width: 1500px;
            height: 100%;
            margin: 0 auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 24px;
        }

        .logo { display: flex; align-items: center; height: 100%; }
        .logo img { height: 38px; width: auto; object-fit: contain; }
        .logo:hover img { transform: scale(1.03); }

        .nav-right { display: flex; align-items: center; gap: 16px; justify-self: end; }
        #navAccessibilitySlot { display: flex; align-items: center; }

        .nav-links { list-style: none; display: flex; align-items: center; justify-content: center; gap: 4px; }
        .nav-links a {
            position: relative;
            color: rgba(255, 255, 255, 0.78); text-decoration: none;
            font-size: 0.9rem; font-weight: 500; padding: 9px 10px; border-radius: 8px;
            transition: color 0.2s ease, background 0.2s ease;
        }
        .nav-links a:not(.btn-nav)::after {
            content: ''; position: absolute; left: 10px; right: 10px; bottom: 4px;
            transform: scaleX(0); transform-origin: left; height: 2px; border-radius: 2px;
            background: #6aaa2e; transition: transform 0.25s ease;
        }
        .nav-links a:not(.btn-nav):hover, .nav-links a.active { color: #ffffff; background: rgba(255, 255, 255, 0.06); }
        .nav-links a:not(.btn-nav):hover::after, .nav-links a.active::after { transform: scaleX(1); }

        .btn-nav {
            position: relative; overflow: hidden;
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, #6aaa2e, #4F7724 55%, #3d5e1c);
            color: #ffffff !important; padding: 12px 24px; border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-weight: 700 !important;
            box-shadow: 0 6px 18px -4px rgba(20, 40, 10, 0.55);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .btn-nav i { font-size: 0.85em; }
        .btn-nav::before {
            content: ''; position: absolute; top: 0; left: -120%; width: 55px; height: 100%;
            background: rgba(255, 255, 255, .45); transform: skewX(-25deg); transition: left .65s ease;
        }
        .btn-nav:hover::before { left: 140%; }
        .btn-nav:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 26px -4px rgba(20, 40, 10, 0.65);
            border-color: rgba(255, 255, 255, 0.32);
        }
        .btn-nav:active { transform: translateY(-1px) scale(1.0); }

        .hamburger {
            display: none; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px; padding: 9px; color: #ffffff; cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }
        .hamburger:hover { background: rgba(255, 255, 255, 0.18); border-color: rgba(255, 255, 255, 0.3); transform: translateY(-1px); }
        .hamburger svg { width: 20px; height: 20px; }

        .mobile-menu {
            display: none; flex-direction: column; background: #0e3018;
            padding: 12px 24px 20px;
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a {
            display: block; color: rgba(255, 255, 255, 0.85); text-decoration: none;
            padding: 12px 6px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-size: 0.92rem;
            transition: color 0.2s ease, padding-left 0.2s ease;
        }
        .mobile-menu a:hover, .mobile-menu a:focus-visible { color: #6aaa2e; padding-left: 12px; }
        .mobile-menu a:last-child { border-bottom: none; }

        @media (max-width: 900px) {
            .nav-links { display: none; }
            .hamburger { display: flex; }
        }

        /* ==========================================================================
        3. ÁREA PRINCIPAL
        ========================================================================== */
        .main {
            max-width: 1200px;
            margin: 0 auto;
            /* 40px (barra de contato) + 66px (navbar) fixas no topo, como
               nas demais páginas públicas — antes a navbar era "sticky" em
               vez de "fixed", um comportamento visualmente diferente do
               resto do site. */
            padding: 146px 24px 60px;
        }

        .header { margin-bottom: 28px; text-align: center; }
        .header .eyebrow {
            display: inline-flex; align-items: center; gap: 8px; font-size: 0.75rem;
            font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase;
            color: var(--card-verde-texto); background: var(--card-verde-bg);
            border: 1px solid var(--card-verde-border); padding: 6px 14px; border-radius: 20px;
            margin-bottom: 14px;
        }
        .header h1 { font-size: 2rem; font-weight: 800; color: var(--text-principal); margin-bottom: 8px; letter-spacing: -0.5px; }
        .header p { color: var(--text-secundario); font-size: 1rem; max-width: 560px; margin: 0 auto; }

        .box {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 26px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
        }

        .box h2 {
            font-size: 1.05rem; font-weight: 700; color: var(--text-principal);
            margin-bottom: 4px; display: flex; align-items: center; gap: 8px;
        }
        .box h2 i { color: #4F7724; }
        .box .box-subtitulo { color: var(--text-secundario); font-size: 0.82rem; margin-bottom: 20px; }

        /* ==========================================================================
        4. ALERTA FLASH
        ========================================================================== */
        .alerta-flash {
            display: flex; align-items: flex-start; gap: 12px; padding: 16px 20px;
            border-radius: 14px; margin-bottom: 20px; font-size: 0.9rem;
        }
        .alerta-flash i { margin-top: 2px; flex-shrink: 0; font-size: 1.1rem; }
        .alerta-flash ul { margin: 6px 0 0 18px; }
        .alerta-flash strong { display: block; }

        .alerta-erro { background: var(--card-vermelho-bg); border: 1px solid var(--card-vermelho-border); color: var(--card-vermelho-texto); }
        .alerta-sucesso { background: var(--card-verde-bg); border: 1px solid var(--card-verde-border); color: var(--card-verde-texto); }
        body.high-contrast .alerta-erro, body.high-contrast .alerta-sucesso { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }

        /* ==========================================================================
        5. CARDS DE ESTATÍSTICA
        ========================================================================== */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }

        .stat-card { background: var(--card-bg); border-radius: 18px; padding: 22px 24px; box-shadow: var(--card-shadow); border: var(--card-border); }

        .stat-card-topo { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
        .stat-icone {
            width: 42px; height: 42px; border-radius: 11px;
            background: var(--card-verde-bg); color: var(--card-verde-texto);
            display: flex; align-items: center; justify-content: center; font-size: 1.05rem;
        }

        .stat-tendencia { display: flex; align-items: center; gap: 4px; font-size: 0.76rem; font-weight: 700; padding: 4px 9px; border-radius: 20px; }
        .stat-tendencia.up { color: #3d5e1c; background: var(--card-verde-bg); }
        .stat-tendencia.down { color: #b91c1c; background: var(--card-vermelho-bg); }
        .stat-tendencia.igual { color: var(--text-secundario); background: var(--panel-item-bg); }
        .stat-tendencia.sem-dado { color: var(--text-secundario); background: var(--panel-item-bg); }

        .stat-valor { font-size: 2rem; font-weight: 800; color: var(--text-principal); display: flex; align-items: baseline; gap: 4px; line-height: 1; }
        .stat-valor small { font-size: 1rem; font-weight: 600; color: var(--text-secundario); }
        .stat-label { font-size: 0.85rem; color: var(--text-secundario); font-weight: 600; margin-top: 6px; }
        .stat-extra { font-size: 0.76rem; color: var(--text-secundario); margin-top: 8px; }

        .stat-estrelas { display: flex; gap: 2px; margin-top: 8px; font-size: 0.8rem; }
        .stat-estrelas i { color: #374151; }
        .stat-estrelas i.preenchida { color: #fbbf24; }
        body.light-theme .stat-estrelas i { color: #d1d5db; }
        body.light-theme .stat-estrelas i.preenchida { color: #f59e0b; }

        body.high-contrast .stat-card { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .stat-icone { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .stat-tendencia { background: #000000 !important; border: 1px solid #ffff00 !important; }

        /* ==========================================================================
        6. VISÃO GERAL DAS AVALIAÇÕES (distribuição + sentimento + evolução)
        ========================================================================== */
        .visao-geral { margin-bottom: 24px; }
        .visao-geral-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 24px; margin-top: 6px; }

        .visao-geral-resumo { display: flex; gap: 22px; margin-bottom: 18px; flex-wrap: wrap; }
        .resumo-item { display: flex; flex-direction: column; }
        .resumo-item .resumo-valor { font-size: 1.3rem; font-weight: 800; color: var(--text-principal); }
        .resumo-item .resumo-label { font-size: 0.74rem; color: var(--text-secundario); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

        .distribuicao-linha { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .distribuicao-linha .dist-label { display: flex; align-items: center; gap: 4px; font-size: 0.8rem; font-weight: 600; color: var(--text-principal); width: 46px; flex-shrink: 0; }
        .distribuicao-linha .dist-label i { color: #f59e0b; font-size: 0.72rem; }
        .distribuicao-barra { flex: 1; height: 9px; background: var(--panel-item-bg); border-radius: 20px; overflow: hidden; }
        .distribuicao-barra-fill { height: 100%; border-radius: 20px; background: linear-gradient(90deg, #6aaa2e, #4F7724); transition: width 0.4s ease; }
        .distribuicao-linha[data-estrelas="3"] .distribuicao-barra-fill { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .distribuicao-linha[data-estrelas="2"] .distribuicao-barra-fill,
        .distribuicao-linha[data-estrelas="1"] .distribuicao-barra-fill { background: linear-gradient(90deg, #f87171, #dc2626); }
        .distribuicao-linha .dist-num { width: 62px; flex-shrink: 0; text-align: right; font-size: 0.78rem; color: var(--text-secundario); font-weight: 600; }

        .sentimento-barra { display: flex; height: 12px; border-radius: 20px; overflow: hidden; margin: 16px 0 12px; background: var(--panel-item-bg); }
        .sentimento-seg-positivo { background: #6aaa2e; }
        .sentimento-seg-neutro { background: #f59e0b; }
        .sentimento-seg-negativo { background: #ef4444; }

        .sentimento-legenda { display: flex; gap: 18px; flex-wrap: wrap; }
        .sentimento-legenda span { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: var(--text-secundario); font-weight: 600; }
        .sentimento-legenda .dot { width: 9px; height: 9px; border-radius: 50%; }
        .dot-positivo { background: #6aaa2e; }
        .dot-neutro { background: #f59e0b; }
        .dot-negativo { background: #ef4444; }

        .evolucao-chart-container { position: relative; height: 180px; margin-top: 6px; }

        .categorias-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px 24px; margin-top: 8px; }
        .categoria-linha { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .categoria-linha .categoria-nome { font-size: 0.84rem; color: var(--text-principal); font-weight: 600; }
        .categoria-linha .categoria-estrelas { display: flex; gap: 2px; font-size: 0.85rem; }
        .categoria-linha .categoria-estrelas i { color: #374151; }
        .categoria-linha .categoria-estrelas i.preenchida { color: #fbbf24; }
        .categoria-linha .categoria-sem-dado { font-size: 0.76rem; color: var(--text-secundario); font-style: italic; }
        body.light-theme .categoria-linha .categoria-estrelas i { color: #d1d5db; }
        body.light-theme .categoria-linha .categoria-estrelas i.preenchida { color: #f59e0b; }

        /* ==========================================================================
        7. AVALIAÇÃO (PAINEL EM DUAS COLUNAS) + FORMULÁRIO DE COMENTÁRIO
        ========================================================================== */
        .avaliacao-wrap {
            display: grid; grid-template-columns: 300px 1fr;
            border-radius: 20px; overflow: hidden;
            box-shadow: var(--card-shadow); border: var(--card-border);
            margin-bottom: 24px;
        }

        .avaliacao-intro {
            background: linear-gradient(160deg, #1a4d32, #123f22 55%, #0a2416);
            color: #e7f5ec; padding: 32px 28px;
            display: flex; flex-direction: column; gap: 20px;
        }
        .avaliacao-intro h2 { font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; }
        .avaliacao-intro p { font-size: 0.86rem; line-height: 1.6; color: rgba(231, 245, 236, 0.82); }

        .nota-preview { margin-top: auto; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 14px; padding: 18px; text-align: center; }
        .nota-preview-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.6px; color: rgba(231, 245, 236, 0.65); font-weight: 700; }
        .nota-preview-valor { font-size: 2.6rem; font-weight: 800; color: #ffffff; line-height: 1.15; margin: 4px 0; }
        .nota-preview-texto { font-size: 0.88rem; color: #a7f3d0; font-weight: 700; }

        .avaliacao-form { background: var(--card-bg); padding: 32px 28px; }
        .avaliacao-form h2 { font-size: 1.1rem; font-weight: 700; color: var(--text-principal); margin-bottom: 4px; }
        .avaliacao-form > p.form-intro { color: var(--text-secundario); font-size: 0.85rem; margin-bottom: 22px; }

        body.high-contrast .avaliacao-wrap { border: 2px solid #ffff00 !important; }
        body.high-contrast .avaliacao-intro { background: #000000 !important; color: #ffff00 !important; }
        body.high-contrast .avaliacao-intro p, body.high-contrast .nota-preview-texto, body.high-contrast .nota-preview-label { color: #ffff00 !important; }
        body.high-contrast .nota-preview { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .avaliacao-form { background: #000000 !important; }

        /* Avaliação por estrelas (input interativo) */
        .estrelas-input { display: flex; gap: 8px; margin-bottom: 2px; }
        .estrelas-input button {
            background: none; border: none; cursor: pointer; padding: 4px;
            font-size: 1.9rem; line-height: 1; color: #374151;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        .estrelas-input button:hover { transform: scale(1.12); }
        .estrelas-input button:focus-visible { outline: 2px solid #4F7724; outline-offset: 2px; border-radius: 6px; }
        .estrelas-input button.selecionada { color: #fbbf24; }
        .estrelas-input.pequena button { font-size: 1.15rem; padding: 2px; }

        body.light-theme .estrelas-input button { color: #d1d5db; }
        body.light-theme .estrelas-input button.selecionada { color: #f59e0b; }
        body.high-contrast .estrelas-input button { color: #555555 !important; }
        body.high-contrast .estrelas-input button.selecionada { color: #ffff00 !important; }

        .campo { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
        .campo label { font-size: 0.85rem; font-weight: 600; color: var(--text-principal); display: flex; align-items: center; gap: 6px; }
        .campo label .obrigatorio { color: #dc2626; }
        .campo label .opcional { font-weight: 500; color: var(--text-secundario); font-size: 0.76rem; }

        .campo input[type="text"] {
            width: 100%; padding: 12px 14px; border-radius: 10px;
            border: 1px solid #374151; font-size: 0.92rem;
            background: #0f1712; color: #ededec; font-family: inherit;
        }
        .campo textarea {
            width: 100%; padding: 14px; border-radius: 12px;
            border: 1px solid #374151; font-size: 0.94rem;
            background: #0f1712; color: #ededec;
            font-family: inherit; resize: vertical; min-height: 140px; line-height: 1.5;
        }
        .campo input[type="text"]:focus, .campo textarea:focus { outline: none; border-color: #4F7724; box-shadow: 0 0 0 3px rgba(79, 119, 36, 0.12); }
        .campo.campo-invalido input[type="text"], .campo.campo-invalido textarea, .campo.campo-invalido .estrelas-input { border-color: #dc2626; }
        .campo-erro { display: none; align-items: center; gap: 6px; font-size: 0.78rem; color: #dc2626; font-weight: 600; }
        .campo.campo-invalido .campo-erro { display: flex; }

        body.light-theme .campo input[type="text"], body.light-theme .campo textarea { background: #fff; color: #1f2937; border-color: #d1d5db; }
        body.high-contrast .campo input[type="text"], body.high-contrast .campo textarea { background: #000000 !important; color: #ffffff !important; border: 2px solid #ffffff !important; }

        .contador-caracteres { font-size: 0.75rem; color: var(--text-secundario); text-align: right; }
        .contador-caracteres.perto-limite { color: #d97706; font-weight: 700; }

        .categorias-opcionais { border-top: 1px dashed var(--card-border); margin-top: 4px; padding-top: 20px; }
        .categorias-opcionais summary { cursor: pointer; font-size: 0.9rem; font-weight: 700; color: var(--text-principal); display: flex; align-items: center; gap: 8px; list-style: none; }
        .categorias-opcionais summary::-webkit-details-marker { display: none; }
        .categorias-opcionais summary i.fa-chevron-right { transition: transform 0.2s ease; margin-left: auto; color: var(--text-secundario); font-size: 0.8rem; }
        .categorias-opcionais[open] summary i.fa-chevron-right { transform: rotate(90deg); }
        .categorias-opcionais summary small { font-weight: 500; color: var(--text-secundario); }
        .categorias-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 24px; margin-top: 18px; }
        .categoria-campo { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .categoria-campo label { font-size: 0.82rem; font-weight: 600; color: var(--text-principal); }

        .btn-salvar {
            padding: 14px 28px; border-radius: 12px; border: none;
            background: linear-gradient(90deg, #14532d, #166534); color: #fff;
            font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
            display: inline-flex; align-items: center; gap: 8px; margin-top: 6px;
        }
        .btn-salvar:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(22, 101, 52, 0.3); }
        body.high-contrast .btn-salvar { background: #ffff00 !important; color: #000000 !important; }

        /* ==========================================================================
        8. COMENTÁRIOS RECENTES (filtros + busca + cards)
        ========================================================================== */
        .comentarios-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }

        .filtros-estrela { display: flex; gap: 6px; flex-wrap: wrap; }
        .filtro-chip {
            border: 1px solid var(--card-border); background: var(--panel-item-bg); color: var(--text-secundario);
            font-size: 0.78rem; font-weight: 600; padding: 7px 13px; border-radius: 20px; cursor: pointer;
            display: flex; align-items: center; gap: 5px; transition: all 0.15s ease;
        }
        .filtro-chip i { color: #f59e0b; font-size: 0.72rem; }
        .filtro-chip.ativo { background: var(--card-verde-bg); border-color: var(--card-verde-border); color: var(--card-verde-texto); }
        body.high-contrast .filtro-chip { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
        body.high-contrast .filtro-chip.ativo { background: #1a1a00 !important; }

        .busca-comentarios { position: relative; min-width: 220px; }
        .busca-comentarios i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--text-secundario); font-size: 0.85rem; }
        .busca-comentarios input {
            width: 100%; padding: 9px 14px 9px 34px; border-radius: 20px;
            border: 1px solid #374151; font-size: 0.84rem; background: #0f1712; color: #ededec;
        }
        .busca-comentarios input:focus { outline: none; border-color: #4F7724; }
        body.light-theme .busca-comentarios input { background: #fff; color: #1f2937; border-color: #d1d5db; }
        body.high-contrast .busca-comentarios input { background: #000000 !important; color: #ffffff !important; border: 2px solid #ffffff !important; }

        .lista-comentarios { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; align-items: start; }

        .comentario-item { padding: 13px 16px; border-radius: 14px; background: var(--panel-item-bg); border: 1px solid var(--card-border); }
        body.high-contrast .comentario-item { background: #000000 !important; border: 2px solid #ffff00 !important; }

        .comentario-topo { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 6px; flex-wrap: wrap; }
        .comentario-autor { display: flex; align-items: center; gap: 8px; }
        .comentario-avatar {
            width: 28px; height: 28px; border-radius: 50%; background: var(--card-verde-bg); color: var(--card-verde-texto);
            display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.72rem; flex-shrink: 0;
        }
        .comentario-autor-info { display: flex; flex-direction: column; }
        .comentario-nome { font-size: 0.8rem; font-weight: 700; color: var(--text-principal); }
        .comentario-data { font-size: 0.68rem; color: var(--text-secundario); }

        .comentario-estrelas { display: flex; gap: 2px; font-size: 0.72rem; }
        .comentario-estrelas i { color: #374151; }
        .comentario-estrelas i.preenchida { color: #fbbf24; }
        body.light-theme .comentario-estrelas i { color: #d1d5db; }
        body.light-theme .comentario-estrelas i.preenchida { color: #f59e0b; }

        .sentimento-badge { font-size: 0.64rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
        .sentimento-badge.positivo { background: var(--card-verde-bg); color: var(--card-verde-texto); }
        .sentimento-badge.neutro { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .sentimento-badge.negativo { background: var(--card-vermelho-bg); color: var(--card-vermelho-texto); }
        body.light-theme .sentimento-badge.neutro { background: #fef3c7; color: #b45309; }
        body.high-contrast .sentimento-badge { background: #000000 !important; border: 1px solid #ffff00 !important; color: #ffff00 !important; }

        .comentario-item p.comentario-texto { color: var(--text-principal); font-size: 0.84rem; line-height: 1.5; white-space: pre-wrap; margin-bottom: 8px; }

        .comentario-categorias { display: flex; flex-wrap: wrap; gap: 8px; }
        .categoria-tag { font-size: 0.64rem; font-weight: 600; color: var(--text-secundario); background: var(--card-bg); border: 1px solid var(--card-border); padding: 3px 8px; border-radius: 20px; }
        body.high-contrast .categoria-tag { background: #000000 !important; border: 1px solid #ffff00 !important; color: #ffff00 !important; }

        .estado-vazio { text-align: center; padding: 40px 20px; color: var(--text-secundario); }
        .estado-vazio i { font-size: 2rem; color: #2a4d38; margin-bottom: 12px; display: block; }
        body.light-theme .estado-vazio i { color: #cbd5e1; }

        /* ==========================================================================
        9. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard/Perfil
        ========================================================================== */
        body.light-theme {
            --bg-principal: #D9EFD1;
            --bg-light-alt: #C3E2B4;
            --text-principal: #14231a;
            --text-secundario: #5b7062;
            --card-bg: #ffffff;
            --card-border: 1px solid #d7ecdc;
            --card-shadow: 0 2px 8px rgba(20, 42, 26, 0.05), 0 16px 36px -12px rgba(20, 42, 26, 0.16);
            --card-verde-bg: #f0fdf4;
            --card-verde-border: #dcfce7;
            --card-verde-texto: #3d5e1c;
            --card-vermelho-bg: #fef2f2;
            --card-vermelho-border: #fecaca;
            --card-vermelho-texto: #b91c1c;
            --chart-grid: #eaf3ec;
            --panel-bg: #ffffff;
            --panel-text: #1f2937;
            --panel-item-bg: #f9fafb;
            --panel-btn-bg: #e5e7eb;
            --panel-btn-text: #374151;
            --panel-btn-hover: #d1d5db;
        }

        /* Topbar/nav/footer não são orientadas por variável (mesmo padrão
           fixo usado nas 7 páginas do site) — no tema claro precisam do
           mesmo ajuste de tom aplicado lá, senão ficam escuras demais. */
        body.light-theme .topbar {
            background: var(--green-dark, #3d5e1c);
            border-bottom-color: rgba(255, 255, 255, .12);
        }

        body.light-theme nav {
            background: var(--green-light, #6aaa2e);
            border-bottom-color: rgba(255, 255, 255, .12);
        }

        body.light-theme .nav-links a {
            color: var(--text-principal) !important;
        }

        body.light-theme .nav-links a.active {
            color: #3d5e1c !important;
        }

        /* Rodapé era só o gradiente marrom-escuro fixo (pensado pro tema
           escuro) — sem isso, no tema claro ele continuava escuro
           enquanto o resto da página ficava verde-claro. Tom um pouco
           mais escuro que o fundo da página (--bg-light-alt), igual ao
           padrão das outras páginas do site. */
        body.light-theme footer {
            background: var(--bg-light-alt, #9BC193);
            color: var(--text-principal);
            border-top-color: rgba(20, 42, 26, .12);
        }

        body.light-theme .footer-logo span,
        body.light-theme .footer-col-title,
        body.light-theme .footer-col a {
            color: var(--text-principal);
        }

        body.light-theme .footer-tagline,
        body.light-theme .footer-copy {
            color: var(--text-secundario);
        }

        body.high-contrast {
            background: #000000 !important;
            color: #ffff00 !important;
            --text-principal: #ffff00;
            --text-secundario: #ffff00;
            --panel-bg: #000000;
            --panel-text: #ffffff;
            --panel-item-bg: #111111;
            --panel-btn-bg: #ffffff;
            --panel-btn-text: #000000;
            --panel-btn-hover: #ffff00;
        }
        body.high-contrast .box { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3,
        body.high-contrast p, body.high-contrast span, body.high-contrast label,
        body.high-contrast small, body.high-contrast i { color: #ffff00 !important; }
        body.high-contrast a:focus-visible, body.high-contrast button:focus-visible { outline: 3px solid #ffff00 !important; outline-offset: 2px !important; }

        /* ==========================================================================
        9.5. RODAPÉ (mesma identidade do projetovideo.blade.php — sempre
        verde escuro, não reage ao tema claro/escuro/alto contraste do
        conteúdo, igual o cabeçalho)
        ========================================================================== */
        footer {
            position: relative;
            background: linear-gradient(165deg, #2a1a0d 0%, #1a1208 65%, #12180a 100%);
            padding: 56px 24px 24px;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 3px;
            background: linear-gradient(90deg, transparent 0%, #6aaa2e 50%, transparent 100%);
            opacity: .85;
        }

        .footer-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1fr;
            gap: 32px;
        }

        .footer-col-brand { display: flex; flex-direction: column; gap: 14px; }
        .footer-logo { display: flex; align-items: center; gap: 12px; }
        .footer-logo img { width: 46px; height: 46px; object-fit: contain; filter: drop-shadow(0 1px 6px rgba(0, 0, 0, 0.5)); }
        .footer-logo span { color: rgba(255, 255, 255, .97); font-weight: 800; font-size: 18px; letter-spacing: .2px; }
        .footer-tagline { font-size: 13px; color: rgba(255, 255, 255, .68); line-height: 1.65; max-width: 260px; }

        .footer-social { display: flex; align-items: center; gap: 14px; }
        .footer-social a {
            display: flex; align-items: center; justify-content: center;
            width: 20px; height: 20px;
            color: rgba(255, 255, 255, 0.8); font-size: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.35);
            transition: color 0.2s ease, text-shadow 0.2s ease, transform 0.2s ease;
        }
        .footer-social a:hover, .footer-social a:focus-visible {
            color: #6aaa2e; text-shadow: 0 2px 6px rgba(106, 170, 46, 0.45); transform: translateY(-2px);
        }

        .footer-col-title {
            position: relative; display: inline-block;
            color: rgba(255, 255, 255, .95); font-size: 12.5px; font-weight: 700;
            letter-spacing: 0.6px; text-transform: uppercase;
            margin-bottom: 14px; padding-bottom: 10px;
        }
        .footer-col-title::after {
            content: ''; position: absolute; left: 0; bottom: 0;
            width: 26px; height: 2px; border-radius: 2px; background: #6aaa2e;
        }

        .footer-col { display: flex; flex-direction: column; gap: 11px; }
        .footer-col a {
            display: inline-flex; align-items: center; gap: 9px;
            font-size: 13.5px; color: rgba(255, 255, 255, .68); text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease; width: fit-content;
        }
        .footer-col a i { width: 13px; flex-shrink: 0; text-align: center; font-size: 12px; color: rgba(106, 170, 46, .85); }
        .footer-col a::before {
            content: '›'; display: inline-block; color: #6aaa2e; font-weight: 700;
            width: 0; opacity: 0; transform: translateX(-6px);
            transition: width 0.2s ease, opacity 0.2s ease, transform 0.2s ease;
        }
        .footer-col a:hover, .footer-col a:focus-visible { color: #ffffff; transform: translateX(2px); }
        .footer-col a:hover::before, .footer-col a:focus-visible::before { width: 10px; opacity: 1; transform: translateX(0); }

        .footer-copy { font-size: 13px; color: rgba(255, 255, 255, .68); line-height: 1.75; }

        .footer-bottom {
            max-width: 1100px; margin: 40px auto 0; padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 12px; color: rgba(255, 255, 255, .45); text-align: center; letter-spacing: .2px;
        }

        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .footer-col-brand { grid-column: 1 / -1; }
        }

        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; }
        }

        /* ==========================================================================
        10. RESPONSIVIDADE
        ========================================================================== */
        @media (max-width: 992px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .visao-geral-grid { grid-template-columns: 1fr; }
            .categorias-grid { grid-template-columns: 1fr; }
            .lista-comentarios { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .main { padding: 126px 18px 48px; }
            .avaliacao-wrap { grid-template-columns: 1fr; }
            .nota-preview { margin-top: 20px; }
            .categorias-form-grid { grid-template-columns: 1fr; }
            .comentarios-toolbar { flex-direction: column; align-items: stretch; }
            .busca-comentarios { min-width: 0; }
        }

        @media (max-width: 576px) {
            /* A barra de contato some nesse breakpoint (ver .topbar acima),
               então o main só precisa reservar espaço pra navbar. */
            .main { padding-top: 86px; }
        }

        @media (max-width: 560px) {
            .stats-grid { grid-template-columns: 1fr; }
            .lista-comentarios { grid-template-columns: 1fr; }
            .header h1 { font-size: 1.5rem; }
        }

        /* ALTO CONTRASTE no cabeçalho/rodapé — antes ficavam sempre verde
           mesmo com o modo ativado (nunca tinham regra própria), o que é
           inconsistente com o resto das páginas públicas e prejudica quem
           realmente precisa do alto contraste ao navegar até aqui. */
        body.high-contrast .topbar,
        body.high-contrast nav,
        body.high-contrast .mobile-menu,
        body.high-contrast footer {
            background: #000000 !important;
        }
        body.high-contrast .topbar,
        body.high-contrast nav {
            border-bottom: 2px solid #ffff00 !important;
        }
        body.high-contrast .topbar-contact-item,
        body.high-contrast .topbar-social a,
        body.high-contrast .nav-links a,
        body.high-contrast .mobile-menu a,
        body.high-contrast .footer-col a,
        body.high-contrast .footer-tagline,
        body.high-contrast .footer-copy,
        body.high-contrast .footer-bottom,
        body.high-contrast .footer-logo span,
        body.high-contrast .footer-col-title {
            color: #ffff00 !important;
        }
        body.high-contrast .btn-nav {
            background: #ffff00 !important;
            color: #000000 !important;
            border: 2px solid #ffff00 !important;
        }
        body.high-contrast .hamburger {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }
        body.high-contrast .footer-social a {
            background: #000000 !important;
            border: 1px solid #ffff00 !important;
        }
        body.high-contrast .nav-links a::after,
        body.high-contrast .footer-col-title::after {
            background: #ffff00 !important;
        }
        body.high-contrast .footer-col a::before {
            color: #ffff00 !important;
        }
        body.high-contrast footer::before {
            background: #ffff00 !important;
        }
        body.high-contrast .logo img,
        body.high-contrast .footer-logo img {
            filter: brightness(0) invert(1) !important;
        }
    </style>
</head>

<body>

    @include('partials.landing-nav', ['paginaAtiva' => 'avaliacoes'])

    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <main class="main">
        <div class="header">
            <span class="eyebrow"><i class="fa-solid fa-comment-dots"></i> Avaliação do AgriFarm</span>
            <h1>O que dizem sobre o sistema</h1>
            <p>Sua opinião ajuda o AgriFarm a evoluir continuamente. Avalie sua experiência e veja o que outros usuários acharam.</p>
        </div>

        @if (session('sucesso'))
            <div class="alerta-flash alerta-sucesso" role="status" aria-live="polite">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <span>{{ session('sucesso') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="alerta-flash alerta-erro" role="alert" aria-live="assertive">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                <div>
                    <strong>Não foi possível enviar o comentário:</strong>
                    <ul>
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- 1. RESUMO DAS AVALIAÇÕES --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-topo">
                    <div class="stat-icone"><i class="fa-solid fa-face-smile"></i></div>
                    @if ($satisfacaoTendencia)
                        <span class="stat-tendencia {{ $satisfacaoTendencia['direcao'] }}">
                            <i class="fa-solid fa-arrow-{{ $satisfacaoTendencia['direcao'] === 'down' ? 'down' : 'up' }}"></i>
                            {{ $satisfacaoTendencia['valor'] }}%
                        </span>
                    @else
                        <span class="stat-tendencia sem-dado">—</span>
                    @endif
                </div>
                <div class="stat-valor">{{ $satisfacao }}<small>%</small></div>
                <div class="stat-label">Satisfação média</div>
                <div class="stat-extra">em relação ao mês anterior</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-topo">
                    <div class="stat-icone"><i class="fa-solid fa-comments"></i></div>
                    @if ($comentariosTendencia)
                        <span class="stat-tendencia {{ $comentariosTendencia['direcao'] }}">
                            <i class="fa-solid fa-arrow-{{ $comentariosTendencia['direcao'] === 'down' ? 'down' : 'up' }}"></i>
                            {{ $comentariosTendencia['valor'] }}%
                        </span>
                    @else
                        <span class="stat-tendencia sem-dado">—</span>
                    @endif
                </div>
                <div class="stat-valor">{{ $totalComentarios }}</div>
                <div class="stat-label">Comentários enviados</div>
                <div class="stat-extra">{{ $totalComentariosMes }} neste mês</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-topo">
                    <div class="stat-icone"><i class="fa-solid fa-star"></i></div>
                </div>
                <div class="stat-valor">{{ $notaMedia }}<small>/5</small></div>
                <div class="stat-label">Nota média</div>
                <div class="stat-estrelas" aria-hidden="true">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= round($notaMedia) ? 'preenchida' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>

        {{-- 2. VISÃO GERAL DAS AVALIAÇÕES --}}
        <div class="box visao-geral">
            <h2><i class="fa-solid fa-chart-simple"></i> Visão geral das avaliações</h2>
            <p class="box-subtitulo">Distribuição de notas, sentimento geral e evolução ao longo do tempo.</p>

            <div class="visao-geral-resumo">
                <div class="resumo-item">
                    <span class="resumo-valor">{{ $totalComNota }}</span>
                    <span class="resumo-label">Total de avaliações</span>
                </div>
                <div class="resumo-item">
                    <span class="resumo-valor">{{ $notaMedia }}/5</span>
                    <span class="resumo-label">Média geral</span>
                </div>
            </div>

            <div class="visao-geral-grid">
                <div>
                    @for ($i = 5; $i >= 1; $i--)
                        <div class="distribuicao-linha" data-estrelas="{{ $i }}">
                            <span class="dist-label">{{ $i }} <i class="fa-solid fa-star"></i></span>
                            <div class="distribuicao-barra">
                                <div class="distribuicao-barra-fill" style="width: {{ $distribuicaoNotas[$i]['percentual'] }}%"></div>
                            </div>
                            <span class="dist-num">{{ $distribuicaoNotas[$i]['quantidade'] }} ({{ $distribuicaoNotas[$i]['percentual'] }}%)</span>
                        </div>
                    @endfor

                    <div class="sentimento-barra" role="img" aria-label="{{ $percentPositivo }}% positivas, {{ $percentNeutro }}% neutras, {{ $percentNegativo }}% negativas">
                        <div class="sentimento-seg-positivo" style="width: {{ $percentPositivo }}%"></div>
                        <div class="sentimento-seg-neutro" style="width: {{ $percentNeutro }}%"></div>
                        <div class="sentimento-seg-negativo" style="width: {{ $percentNegativo }}%"></div>
                    </div>
                    <div class="sentimento-legenda">
                        <span><span class="dot dot-positivo"></span> Positivas ({{ $percentPositivo }}%)</span>
                        <span><span class="dot dot-neutro"></span> Neutras ({{ $percentNeutro }}%)</span>
                        <span><span class="dot dot-negativo"></span> Negativas ({{ $percentNegativo }}%)</span>
                    </div>
                </div>

                <div>
                    <div class="evolucao-chart-container">
                        <canvas id="chartEvolucao"></canvas>
                    </div>
                </div>
            </div>

            @if (collect($categoriasMedias)->contains(fn ($c) => $c['quantidade'] > 0))
                <div class="categorias-grid" style="margin-top: 22px; border-top: 1px dashed var(--card-border); padding-top: 20px;">
                    @foreach ($categoriasMedias as $dados)
                        <div class="categoria-linha">
                            <span class="categoria-nome">{{ $dados['rotulo'] }}</span>
                            @if ($dados['quantidade'] > 0)
                                <span class="categoria-estrelas" aria-label="{{ $dados['media'] }} de 5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= round($dados['media']) ? 'preenchida' : '' }}"></i>
                                    @endfor
                                </span>
                            @else
                                <span class="categoria-sem-dado">Sem avaliações</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 3/4/5. FORMULÁRIO DE AVALIAÇÃO --}}
        <div class="avaliacao-wrap">
            <div class="avaliacao-intro">
                <div>
                    <h2>Sua Avaliação</h2>
                    <p>Compartilhe sua experiência usando o AgriFarm. Seu comentário é essencial para aprimorar o sistema de gestão de rações.</p>
                </div>
                <div class="nota-preview">
                    <div class="nota-preview-label">Nota selecionada</div>
                    <div class="nota-preview-valor" id="notaPreviewValor">0</div>
                    <div class="nota-preview-texto" id="notaPreviewTexto">Nenhuma avaliação</div>
                </div>
            </div>

            <div class="avaliacao-form">
                <h2>Compartilhe sua experiência</h2>
                <p class="form-intro">Conte pra gente o que achou do sistema, o que poderia melhorar ou algum problema que encontrou.</p>

                <form action="{{ route('comentario.salvar') }}" method="POST" id="formComentario" novalidate>
                    @csrf

                    <div class="campo">
                        <label for="nome"><i class="fa-solid fa-user"></i> Nome <span class="opcional">(opcional)</span></label>
                        <input type="text" id="nome" name="nome" maxlength="150" placeholder="Seu nome completo" value="{{ old('nome', $nomeVisitante) }}">
                    </div>

                    <div class="campo" id="campoNota">
                        <label><i class="fa-solid fa-star" style="color:#f59e0b;"></i> Avaliação <span class="obrigatorio">*</span></label>
                        <input type="hidden" name="nota" id="notaInput" value="{{ old('nota') }}">
                        <div class="estrelas-input" id="estrelasInput" role="radiogroup" aria-label="Nota de 1 a 5 estrelas">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" data-valor="{{ $i }}" role="radio" aria-checked="false" aria-label="{{ $i }} estrela{{ $i > 1 ? 's' : '' }}">
                                    <i class="fa-solid fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <span class="campo-erro"><i class="fa-solid fa-circle-exclamation"></i> Selecione uma nota antes de enviar.</span>
                    </div>

                    <div class="campo" id="campoMensagem">
                        <label for="mensagem"><i class="fa-solid fa-comment"></i> Comentário <span class="obrigatorio">*</span></label>
                        <textarea id="mensagem" name="mensagem" maxlength="2000" placeholder="O que você achou do AgriFarm?">{{ old('mensagem') }}</textarea>
                        <span class="campo-erro"><i class="fa-solid fa-circle-exclamation"></i> Escreva um comentário antes de enviar.</span>
                        <span class="contador-caracteres"><span id="contadorAtual">0</span>/2000</span>
                    </div>

                    <details class="categorias-opcionais">
                        <summary>
                            <i class="fa-solid fa-list-check"></i> O que você achou?
                            <small>(opcional)</small>
                            <i class="fa-solid fa-chevron-right"></i>
                        </summary>
                        <div class="categorias-form-grid">
                            @foreach (\App\Models\Comentario::CATEGORIAS as $coluna => $rotulo)
                                <div class="categoria-campo">
                                    <label id="label-{{ $coluna }}">{{ $rotulo }}</label>
                                    <input type="hidden" name="{{ $coluna }}" id="input-{{ $coluna }}" value="{{ old($coluna) }}">
                                    <div class="estrelas-input pequena categoria-estrelas-input" data-target="{{ $coluna }}" role="radiogroup" aria-labelledby="label-{{ $coluna }}">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" data-valor="{{ $i }}" role="radio" aria-checked="false" aria-label="{{ $i }} estrela{{ $i > 1 ? 's' : '' }}">
                                                <i class="fa-solid fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>

                    <button type="submit" class="btn-salvar"><i class="fa-solid fa-paper-plane"></i> Enviar comentário</button>
                </form>
            </div>
        </div>

        {{-- 6/7. COMENTÁRIOS RECENTES --}}
        <div class="box">
            <h2><i class="fa-solid fa-clock-rotate-left"></i> Comentários recentes</h2>
            <p class="box-subtitulo">{{ $totalComentarios }} avaliação{{ $totalComentarios == 1 ? '' : 'ões' }} de usuários do AgriFarm.</p>

            @if ($comentarios->isEmpty())
                <div class="estado-vazio">
                    <i class="fa-solid fa-comment-slash"></i>
                    Ainda não há comentários. Seja o primeiro a avaliar o AgriFarm!
                </div>
            @else
                <div class="comentarios-toolbar">
                    <div class="filtros-estrela" id="filtrosEstrela" role="group" aria-label="Filtrar por nota">
                        <button type="button" class="filtro-chip ativo" data-filtro="todas">Todas</button>
                        @for ($i = 5; $i >= 1; $i--)
                            <button type="button" class="filtro-chip" data-filtro="{{ $i }}">{{ $i }} <i class="fa-solid fa-star"></i></button>
                        @endfor
                    </div>
                    <div class="busca-comentarios">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscaComentarios" placeholder="Buscar nos comentários..." aria-label="Buscar nos comentários">
                    </div>
                </div>

                <div class="lista-comentarios" id="listaComentarios">
                    @foreach ($comentarios as $comentario)
                        <div class="comentario-item"
                             data-nota="{{ $comentario->nota ?? 0 }}"
                             data-texto="{{ mb_strtolower($comentario->nomeExibicao() . ' ' . $comentario->mensagem) }}">
                            <div class="comentario-topo">
                                <div class="comentario-autor">
                                    <div class="comentario-avatar">{{ mb_strtoupper(mb_substr($comentario->nomeExibicao(), 0, 1)) }}</div>
                                    <div class="comentario-autor-info">
                                        <span class="comentario-nome">{{ $comentario->nomeExibicao() }}</span>
                                        <span class="comentario-data">{{ $comentario->created_at->format('d/m/Y \à\s H:i') }}</span>
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    @if ($comentario->nota)
                                        <div class="comentario-estrelas" aria-label="{{ $comentario->nota }} de 5 estrelas">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $comentario->nota ? 'preenchida' : '' }}"></i>
                                            @endfor
                                        </div>
                                        @php $sentimento = $comentario->sentimento(); @endphp
                                        <span class="sentimento-badge {{ $sentimento }}">
                                            <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                                            {{ ['positivo' => 'Positivo', 'neutro' => 'Neutro', 'negativo' => 'Negativo'][$sentimento] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <p class="comentario-texto">{{ $comentario->mensagem }}</p>
                            @php
                                $tagsCategorias = collect(\App\Models\Comentario::CATEGORIAS)
                                    ->filter(fn ($rotulo, $coluna) => $comentario->{$coluna});
                            @endphp
                            @if ($tagsCategorias->isNotEmpty())
                                <div class="comentario-categorias">
                                    @foreach ($tagsCategorias as $coluna => $rotulo)
                                        <span class="categoria-tag">{{ $rotulo }}: {{ $comentario->{$coluna} }}/5</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="estado-vazio" id="semResultadosFiltro" style="display:none;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Nenhum comentário encontrado para esse filtro.
                </div>
            @endif
        </div>
    </main>

    @include('partials.landing-footer')

    @include('partials.acessibilidade')
    @include('partials.assistente-ia')

    <!-- WIDGET VLIBRAS -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>

    <script src="{{ asset('js/vendor/chart.min.js') }}"></script>
    <script>
        // ─── MENU MOBILE ───
        function toggleMenu() {
            document.getElementById('mobile-menu').classList.toggle('open');
        }

        function closeMenu() {
            document.getElementById('mobile-menu').classList.remove('open');
        }

        document.addEventListener('DOMContentLoaded', function () {
            /* --- BOTÃO DE ACESSIBILIDADE DENTRO DA NAVBAR ---
               Esta página tem uma navbar própria de landing page (sem
               .navbar-right), então o auto-move padrão do painel de
               acessibilidade nunca encontra destino. Move o botão pra
               dentro do slot no meio da navbar (junto da logo). */
            const accTrigger = document.getElementById('accTrigger');
            const accSlot = document.getElementById('navAccessibilitySlot');
            if (accTrigger && accSlot) {
                accSlot.appendChild(accTrigger);
                accTrigger.classList.add('acc-trigger-inline');
            }

            /* --- MODO ESCURO / TEMA CLARO ---
               Esta página segue o mesmo padrão das demais páginas públicas
               (problema, solução etc.): o padrão JÁ é o design escuro
               (nenhuma regra própria pra ele — é o que está em :root). O
               interruptor "Modo Escuro" do painel de acessibilidade vira um
               Escuro/Claro simples: marcado (padrão) = escuro original;
               desmarcado = Tema Claro (body.light-theme). Reaproveita a
               mesma chave "theme" que o painel compartilhado já salva no
               localStorage ('dark'/'light'/'contrast'). */
            const chkDarkMode = document.getElementById('chkDarkMode');
            const chkHighContrast = document.getElementById('chkHighContrast');
            if (chkDarkMode && chkHighContrast) {
                function aplicarTemaClaro(ativo) {
                    document.body.classList.toggle('light-theme', ativo);
                }

                const salvo = localStorage.getItem('theme');
                if (salvo === null) {
                    chkDarkMode.checked = true;
                }
                aplicarTemaClaro(salvo === 'light');

                chkDarkMode.addEventListener('change', function () {
                    aplicarTemaClaro(!this.checked);
                });

                chkHighContrast.addEventListener('change', function () {
                    aplicarTemaClaro(!this.checked && !chkDarkMode.checked);
                });
            }

            /* --- CONTADOR DE CARACTERES --- */
            const textarea = document.getElementById('mensagem');
            const contador = document.getElementById('contadorAtual');
            const contadorWrap = contador ? contador.closest('.contador-caracteres') : null;
            function atualizarContador() {
                const len = textarea.value.length;
                contador.textContent = len;
                if (contadorWrap) contadorWrap.classList.toggle('perto-limite', len > 1800);
            }
            if (textarea && contador) {
                textarea.addEventListener('input', atualizarContador);
                atualizarContador();
            }

            /* --- COMPONENTE GENÉRICO DE ESTRELAS --- */
            function montarEstrelas(container, inputEl, aoMudar) {
                if (!container || !inputEl) return;
                const botoes = Array.from(container.querySelectorAll('button'));

                function aplicar(valor) {
                    botoes.forEach(function (btn) {
                        const marcado = Number(btn.dataset.valor) <= valor;
                        btn.classList.toggle('selecionada', marcado);
                        btn.setAttribute('aria-checked', marcado ? 'true' : 'false');
                    });
                    inputEl.value = valor || '';
                    if (aoMudar) aoMudar(valor);
                }

                botoes.forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        aplicar(Number(btn.dataset.valor));
                    });
                });

                aplicar(Number(inputEl.value) || 0);
            }

            /* --- AVALIAÇÃO PRINCIPAL --- */
            const notaPreviewValor = document.getElementById('notaPreviewValor');
            const notaPreviewTexto = document.getElementById('notaPreviewTexto');
            const textosNota = { 1: 'Muito ruim', 2: 'Ruim', 3: 'Neutro', 4: 'Muito bom', 5: 'Excelente' };

            montarEstrelas(document.getElementById('estrelasInput'), document.getElementById('notaInput'), function (valor) {
                if (notaPreviewValor) notaPreviewValor.textContent = valor || '0';
                if (notaPreviewTexto) notaPreviewTexto.textContent = valor ? (valor + ' — ' + textosNota[valor]) : 'Nenhuma avaliação';
            });

            /* --- CATEGORIAS OPCIONAIS --- */
            document.querySelectorAll('.categoria-estrelas-input').forEach(function (container) {
                const input = document.getElementById('input-' + container.dataset.target);
                montarEstrelas(container, input);
            });

            /* --- VALIDAÇÃO INTEGRADA AO DESIGN (sem popups nativos do navegador) --- */
            const formComentario = document.getElementById('formComentario');
            if (formComentario) {
                formComentario.addEventListener('submit', function (e) {
                    let valido = true;

                    const campoNota = document.getElementById('campoNota');
                    const notaInput = document.getElementById('notaInput');
                    if (!notaInput.value) {
                        campoNota.classList.add('campo-invalido');
                        valido = false;
                    } else {
                        campoNota.classList.remove('campo-invalido');
                    }

                    const campoMensagem = document.getElementById('campoMensagem');
                    if (!textarea.value.trim()) {
                        campoMensagem.classList.add('campo-invalido');
                        valido = false;
                    } else {
                        campoMensagem.classList.remove('campo-invalido');
                    }

                    if (!valido) {
                        e.preventDefault();
                        const primeiroInvalido = formComentario.querySelector('.campo-invalido');
                        if (primeiroInvalido) primeiroInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }

            /* --- FILTROS POR ESTRELA + BUSCA --- */
            const filtrosEstrela = document.getElementById('filtrosEstrela');
            const buscaInput = document.getElementById('buscaComentarios');
            const cardsComentarios = Array.from(document.querySelectorAll('#listaComentarios .comentario-item'));
            const semResultados = document.getElementById('semResultadosFiltro');
            let filtroAtual = 'todas';

            function aplicarFiltros() {
                const termo = (buscaInput ? buscaInput.value : '').trim().toLowerCase();
                let visiveis = 0;

                cardsComentarios.forEach(function (card) {
                    const passaEstrela = filtroAtual === 'todas' || card.dataset.nota === filtroAtual;
                    const passaBusca = !termo || card.dataset.texto.includes(termo);
                    const mostrar = passaEstrela && passaBusca;
                    card.style.display = mostrar ? '' : 'none';
                    if (mostrar) visiveis++;
                });

                if (semResultados) semResultados.style.display = visiveis === 0 ? 'block' : 'none';
            }

            if (filtrosEstrela) {
                filtrosEstrela.querySelectorAll('.filtro-chip').forEach(function (chip) {
                    chip.addEventListener('click', function () {
                        filtrosEstrela.querySelectorAll('.filtro-chip').forEach(function (c) { c.classList.remove('ativo'); });
                        chip.classList.add('ativo');
                        filtroAtual = chip.dataset.filtro;
                        aplicarFiltros();
                    });
                });
            }

            if (buscaInput) {
                buscaInput.addEventListener('input', aplicarFiltros);
            }

            /* --- GRÁFICO DE EVOLUÇÃO MENSAL --- */
            if (window.Chart) {
                Chart.defaults.devicePixelRatio = 1;

                function cssVar(name, fallback) {
                    const valor = getComputedStyle(document.body).getPropertyValue(name).trim();
                    return valor || fallback;
                }

                const canvasEvolucao = document.getElementById('chartEvolucao');
                if (canvasEvolucao) {
                    const labels = @json(collect($evolucaoMensal)->pluck('mes'));
                    const dados = @json(collect($evolucaoMensal)->pluck('media'));

                    new Chart(canvasEvolucao, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Nota média',
                                data: dados,
                                borderColor: cssVar('--card-verde-texto', '#4F7724'),
                                backgroundColor: 'rgba(79, 119, 36, 0.12)',
                                borderWidth: 2.5,
                                tension: 0.35,
                                fill: true,
                                spanGaps: true,
                                pointRadius: 3,
                                pointBackgroundColor: cssVar('--card-verde-texto', '#4F7724'),
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    min: 0, max: 5,
                                    ticks: { stepSize: 1, color: cssVar('--text-secundario', '#64748b'), font: { size: 11 } },
                                    grid: { color: cssVar('--chart-grid', '#eef2f6') }
                                },
                                x: {
                                    ticks: { color: cssVar('--text-secundario', '#64748b'), font: { size: 11 } },
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
