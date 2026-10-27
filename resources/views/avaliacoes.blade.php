<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | Avaliações</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')

    <style>
        /* ==========================================================================
        AVALIAÇÕES — só o que é próprio desta página. Navbar, rodapé, fontes,
        paleta, Tema Claro e Alto Contraste vêm de partials/landing-styles
        (os mesmos das demais páginas públicas); aqui os tokens do conteúdo
        apenas apontam pra essa paleta compartilhada.
        ========================================================================== */
        :root {
            --bg-principal: var(--bg-dark);
            --text-principal: var(--text);
            --text-secundario: var(--text-muted);
            --card-bg: rgba(32, 44, 22, .7);
            --card-shadow: 0 24px 60px rgba(0, 0, 0, .25);
            --card-border: 1px solid rgba(255, 255, 255, .1);
            --item-bg: rgba(255, 255, 255, .04);

            --card-verde-bg: rgba(106, 170, 46, 0.12);
            --card-verde-border: rgba(106, 170, 46, 0.35);
            --card-verde-texto: #8fd45a; /* verde claro legível (4,5:1) sobre os fundos verdes escuros */

            --card-vermelho-bg: rgba(239, 68, 68, 0.12);
            --card-vermelho-border: rgba(239, 68, 68, 0.3);
            --card-vermelho-texto: #fca5a5;

            --campo-bg: rgba(255, 255, 255, .06);
            --campo-border: rgba(106, 170, 46, .3);
            --campo-texto: #ffffff;

            --chart-grid: rgba(106, 170, 46, 0.1);
        }

        body.light-theme {
            --bg-principal: var(--bg-light);
            --text-principal: var(--text-on-light);
            --text-secundario: var(--text-on-light-muted);
            --card-bg: var(--card-light-bg);
            --card-shadow: var(--card-light-shadow);
            --card-border: 1px solid var(--card-light-border);
            --item-bg: #f5fbf6;
            --card-verde-bg: rgba(79, 119, 36, .1);
            --card-verde-border: rgba(79, 119, 36, .3);
            --card-verde-texto: var(--green-dark);
            --card-vermelho-bg: #fef2f2;
            --card-vermelho-border: #fecaca;
            --card-vermelho-texto: #b91c1c;
            --campo-bg: #ffffff;
            --campo-border: rgba(79, 119, 36, .3);
            --campo-texto: var(--text-on-light);
            --chart-grid: rgba(79, 119, 36, .12);
        }

        body.acc-modal-open .site-header,
        body.acc-modal-open .main {
            filter: blur(6px);
            pointer-events: none;
        }

        /* ==========================================================================
        1. ÁREA PRINCIPAL
        ========================================================================== */
        .main {
            max-width: 1100px;
            margin: 0 auto;
            /* 40px (barra de contato) + 66px (navbar) fixas no topo. */
            padding: 146px 24px var(--section-py);
        }

        .header { margin-bottom: 36px; text-align: center; }
        .header .section-title { margin-bottom: 14px; }
        .header p { color: var(--text-secundario); font-size: 1rem; line-height: 1.7; max-width: 600px; margin: 0 auto; }

        .box {
            background: var(--card-bg);
            backdrop-filter: blur(6px);
            border-radius: 20px;
            padding: 28px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
            transition: border-color var(--hover-duration) ease;
        }
        .box:hover { border-color: rgba(79, 119, 36, .4); }

        .box h2 {
            font-family: var(--font-display);
            font-size: 1.1rem; font-weight: 700; color: var(--text-principal);
            margin-bottom: 4px; display: flex; align-items: center; gap: 8px;
        }
        .box h2 i { color: var(--green-light); }
        .box .box-subtitulo { color: var(--text-secundario); font-size: 0.85rem; margin-bottom: 20px; }

        /* ==========================================================================
        2. ALERTA FLASH
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
        3. CARDS DE ESTATÍSTICA
        ========================================================================== */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }

        .stat-card {
            background: var(--card-bg); backdrop-filter: blur(6px);
            border-radius: 18px; padding: 24px; box-shadow: var(--card-shadow); border: var(--card-border);
            transition: transform var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) ease;
        }
        .stat-card:hover { transform: translateY(-4px); border-color: rgba(79, 119, 36, .4); }

        .stat-card-topo { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
        .stat-icone {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--card-verde-bg); border: 1px solid var(--card-verde-border); color: var(--card-verde-texto);
            display: flex; align-items: center; justify-content: center; font-size: 1.05rem;
        }

        .stat-tendencia { display: flex; align-items: center; gap: 4px; font-size: 0.76rem; font-weight: 700; padding: 4px 9px; border-radius: 20px; }
        .stat-tendencia.up { color: var(--card-verde-texto); background: var(--card-verde-bg); }
        .stat-tendencia.down { color: var(--card-vermelho-texto); background: var(--card-vermelho-bg); }
        .stat-tendencia.igual,
        .stat-tendencia.sem-dado { color: var(--text-secundario); background: var(--item-bg); }

        .stat-valor { font-family: var(--font-display); font-size: 2.1rem; font-weight: 800; color: var(--text-principal); display: flex; align-items: baseline; gap: 4px; line-height: 1; }
        .stat-valor small { font-size: 1rem; font-weight: 600; color: var(--text-secundario); }
        .stat-label { font-size: 0.85rem; color: var(--text-secundario); font-weight: 600; margin-top: 6px; }
        .stat-extra { font-size: 0.76rem; color: var(--text-secundario); margin-top: 8px; }

        .stat-estrelas { display: flex; gap: 2px; margin-top: 8px; font-size: 0.8rem; }
        .stat-estrelas i { color: rgba(255, 255, 255, .18); }
        .stat-estrelas i.preenchida { color: var(--gold-light); }
        body.light-theme .stat-estrelas i { color: #d1d5db; }
        body.light-theme .stat-estrelas i.preenchida { color: var(--gold); }

        body.high-contrast .stat-card { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .stat-icone { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .stat-tendencia { background: #000000 !important; border: 1px solid #ffff00 !important; }

        /* ==========================================================================
        4. VISÃO GERAL DAS AVALIAÇÕES (distribuição + sentimento + evolução)
        ========================================================================== */
        .visao-geral { margin-bottom: 24px; }
        .visao-geral-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 24px; margin-top: 6px; }

        .visao-geral-resumo { display: flex; gap: 22px; margin-bottom: 18px; flex-wrap: wrap; }
        .resumo-item { display: flex; flex-direction: column; }
        .resumo-item .resumo-valor { font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: var(--text-principal); }
        .resumo-item .resumo-label { font-size: 0.74rem; color: var(--text-secundario); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

        .distribuicao-linha { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .distribuicao-linha .dist-label { display: flex; align-items: center; gap: 4px; font-size: 0.8rem; font-weight: 600; color: var(--text-principal); width: 46px; flex-shrink: 0; }
        .distribuicao-linha .dist-label i { color: var(--gold); font-size: 0.72rem; }
        .distribuicao-barra { flex: 1; height: 9px; background: var(--item-bg); border-radius: 20px; overflow: hidden; }
        .distribuicao-barra-fill { height: 100%; border-radius: 20px; background: linear-gradient(90deg, #6aaa2e, #4F7724); transition: width 0.4s ease; }
        .distribuicao-linha[data-estrelas="3"] .distribuicao-barra-fill { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .distribuicao-linha[data-estrelas="2"] .distribuicao-barra-fill,
        .distribuicao-linha[data-estrelas="1"] .distribuicao-barra-fill { background: linear-gradient(90deg, #f87171, #dc2626); }
        .distribuicao-linha .dist-num { width: 62px; flex-shrink: 0; text-align: right; font-size: 0.78rem; color: var(--text-secundario); font-weight: 600; }

        .sentimento-barra { display: flex; height: 12px; border-radius: 20px; overflow: hidden; margin: 16px 0 12px; background: var(--item-bg); }
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
        .categoria-linha .categoria-estrelas i { color: rgba(255, 255, 255, .18); }
        .categoria-linha .categoria-estrelas i.preenchida { color: var(--gold-light); }
        .categoria-linha .categoria-sem-dado { font-size: 0.76rem; color: var(--text-secundario); font-style: italic; }
        body.light-theme .categoria-linha .categoria-estrelas i { color: #d1d5db; }
        body.light-theme .categoria-linha .categoria-estrelas i.preenchida { color: var(--gold); }

        /* ==========================================================================
        5. AVALIAÇÃO (PAINEL EM DUAS COLUNAS) + FORMULÁRIO — mesmo visual do
        card "Entre em Contato" (.form-container / .form-input / .btn-submit)
        ========================================================================== */
        .avaliacao-wrap {
            display: grid; grid-template-columns: 300px 1fr;
            border-radius: 28px; overflow: hidden;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(106, 170, 46, .35);
            margin-bottom: 24px;
        }

        .avaliacao-intro {
            background: linear-gradient(160deg, #3d5e1c, #2c4418 55%, #1b241a);
            color: #f1f5f9; padding: 36px 30px;
            display: flex; flex-direction: column; gap: 20px;
        }
        .avaliacao-intro h2 { font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; margin-bottom: 8px; }
        .avaliacao-intro p { font-size: 0.88rem; line-height: 1.65; color: rgba(241, 245, 249, 0.8); }

        .nota-preview { margin-top: auto; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14); border-radius: 16px; padding: 18px; text-align: center; }
        .nota-preview-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(241, 245, 249, 0.65); font-weight: 700; }
        .nota-preview-valor { font-family: var(--font-display); font-size: 2.6rem; font-weight: 800; color: #ffffff; line-height: 1.15; margin: 4px 0; }
        .nota-preview-texto { font-size: 0.88rem; color: var(--gold-light); font-weight: 700; }

        .avaliacao-form { background: var(--card-bg); backdrop-filter: blur(10px); padding: 36px 40px; }
        .avaliacao-form h2 { font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; color: var(--text-principal); margin-bottom: 4px; }
        .avaliacao-form > p.form-intro { color: var(--text-secundario); font-size: 0.86rem; margin-bottom: 22px; }

        body.light-theme .avaliacao-wrap { border-color: rgba(79, 119, 36, .35); }

        body.high-contrast .avaliacao-wrap { border: 2px solid #ffff00 !important; }
        body.high-contrast .avaliacao-intro { background: #000000 !important; color: #ffff00 !important; }
        body.high-contrast .avaliacao-intro p, body.high-contrast .nota-preview-texto, body.high-contrast .nota-preview-label { color: #ffff00 !important; }
        body.high-contrast .nota-preview { background: #000000 !important; border: 2px solid #ffff00 !important; }
        body.high-contrast .avaliacao-form { background: #000000 !important; }

        /* Avaliação por estrelas (input interativo) */
        .estrelas-input { display: flex; gap: 8px; margin-bottom: 2px; }
        .estrelas-input button {
            background: none; border: none; cursor: pointer; padding: 4px;
            font-size: 1.9rem; line-height: 1; color: rgba(255, 255, 255, .18);
            transition: color var(--hover-duration) var(--ease-premium), transform var(--hover-duration) var(--ease-premium);
        }
        .estrelas-input button:hover { transform: scale(1.12); }
        .estrelas-input button:focus-visible { outline: 2px solid var(--gold-light); outline-offset: 2px; border-radius: 6px; }
        .estrelas-input button.selecionada { color: var(--gold-light); }
        .estrelas-input.pequena button { font-size: 1.15rem; padding: 2px; min-width: 28px; min-height: 28px; }

        body.light-theme .estrelas-input button { color: #d1d5db; }
        body.light-theme .estrelas-input button.selecionada { color: var(--gold); }
        body.high-contrast .estrelas-input button { color: #555555 !important; }
        body.high-contrast .estrelas-input button.selecionada { color: #ffff00 !important; }

        .campo { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }
        .campo label { font-size: 0.85rem; font-weight: 600; color: var(--text-principal); display: flex; align-items: center; gap: 6px; }
        .campo label i { color: var(--green-light); }
        .campo label .obrigatorio { color: #ef4444; }
        .campo label .opcional { font-weight: 500; color: var(--text-secundario); font-size: 0.76rem; }
        body.light-theme .campo label { color: var(--green-dark); }
        body.light-theme .campo label i { color: var(--green-dark); }

        .campo input[type="text"],
        .campo textarea {
            width: 100%; padding: 14px 18px; border-radius: 14px;
            border: 1px solid var(--campo-border); font-size: 14px;
            background: var(--campo-bg); color: var(--campo-texto); font-family: inherit;
            transition: border-color .2s, background .2s; outline: none;
        }
        .campo textarea { resize: vertical; min-height: 140px; line-height: 1.6; }
        .campo input[type="text"]::placeholder,
        .campo textarea::placeholder { color: rgba(255, 255, 255, .62); }
        body.light-theme .campo input[type="text"]::placeholder,
        body.light-theme .campo textarea::placeholder { color: rgba(28, 39, 22, .62); }
        /* Foco nítido (antes só a borda passava a 60% de opacidade). */
        .campo input[type="text"]:focus, .campo textarea:focus, .busca-comentarios input:focus { border-color: #6aaa2e; background: rgba(255, 255, 255, .09); box-shadow: 0 0 0 3px rgba(106, 170, 46, .38); }
        body.light-theme .campo input[type="text"]:focus, body.light-theme .campo textarea:focus, body.light-theme .busca-comentarios input:focus { border-color: #3d7a1f; box-shadow: 0 0 0 3px rgba(61, 122, 31, .25); }
        body.light-theme .campo input[type="text"]:focus, body.light-theme .campo textarea:focus { background: #ffffff; }
        .campo.campo-invalido input[type="text"], .campo.campo-invalido textarea, .campo.campo-invalido .estrelas-input { border-color: #dc2626; }
        .campo-erro { display: none; align-items: center; gap: 6px; font-size: 0.78rem; color: #ef4444; font-weight: 600; }
        .campo.campo-invalido .campo-erro { display: flex; }

        body.high-contrast .campo input[type="text"], body.high-contrast .campo textarea { background: #000000 !important; color: #ffffff !important; border: 2px solid #ffffff !important; }

        .contador-caracteres { font-size: 0.75rem; color: var(--text-secundario); text-align: right; }
        .contador-caracteres.perto-limite { color: #d97706; font-weight: 700; }

        .categorias-opcionais { border-top: 1px dashed var(--campo-border); margin-top: 4px; padding-top: 20px; }
        .categorias-opcionais summary { cursor: pointer; font-size: 0.9rem; font-weight: 700; color: var(--text-principal); display: flex; align-items: center; gap: 8px; list-style: none; }
        .categorias-opcionais summary::-webkit-details-marker { display: none; }
        .categorias-opcionais summary i { color: var(--green-light); }
        .categorias-opcionais summary i.fa-chevron-right { transition: transform 0.2s ease; margin-left: auto; color: var(--text-secundario); font-size: 0.8rem; }
        .categorias-opcionais[open] summary i.fa-chevron-right { transform: rotate(90deg); }
        .categorias-opcionais summary small { font-weight: 500; color: var(--text-secundario); }
        .categorias-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 24px; margin-top: 18px; }
        .categoria-campo { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .categoria-campo label { font-size: 0.82rem; font-weight: 600; color: var(--text-principal); }

        /* Mesmo botão do formulário "Entre em Contato" (.btn-submit). */
        .btn-salvar {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; margin-top: 22px; padding: 18px;
            background: #4F7724; color: #fff;
            font-weight: 700; font-size: 15px; font-family: inherit;
            border: none; border-radius: 14px; cursor: pointer;
            box-shadow: 0 6px 24px rgba(79, 119, 36, .3);
            transition: transform var(--hover-duration) var(--ease-premium), box-shadow var(--hover-duration) var(--ease-premium), background var(--hover-duration) ease;
        }
        .btn-salvar:hover, .btn-salvar:focus-visible { background: #6aaa2e; box-shadow: 0 10px 34px rgba(79, 119, 36, .48); transform: translateY(-2px); }
        .btn-salvar:focus-visible { outline: 2px solid #e8c463; outline-offset: 3px; }
        .btn-salvar:active { transform: translateY(0); }
        body.high-contrast .btn-salvar { background: #000000 !important; color: #ffff00 !important; border: 2px solid #ffff00 !important; box-shadow: none !important; }
        body.high-contrast .btn-salvar:hover { background: #1a1a00 !important; }

        /* ==========================================================================
        6. COMENTÁRIOS RECENTES (filtros + busca + cards)
        ========================================================================== */
        .comentarios-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }

        .filtros-estrela { display: flex; gap: 6px; flex-wrap: wrap; }
        .filtro-chip {
            border: 1px solid var(--campo-border); background: var(--item-bg); color: var(--text-secundario);
            font-size: 0.78rem; font-weight: 600; font-family: inherit; padding: 7px 14px; border-radius: 999px; cursor: pointer;
            display: flex; align-items: center; gap: 5px;
            transition: background var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) var(--ease-premium), color var(--hover-duration) var(--ease-premium);
        }
        .filtro-chip:hover { border-color: rgba(79, 119, 36, .6); color: var(--text-principal); }
        .filtro-chip i { color: var(--gold); font-size: 0.72rem; }
        .filtro-chip.ativo { background: var(--card-verde-bg); border-color: var(--card-verde-border); color: var(--card-verde-texto); }
        body.high-contrast .filtro-chip { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
        /* Ativo no alto contraste: amarelo cheio (antes #1a1a00 sobre preto, invisível). */
        body.high-contrast .filtro-chip.ativo { background: #ffff00 !important; color: #000000 !important; }
        body.high-contrast .filtro-chip.ativo i { color: #000000 !important; }
        /* Ativo também por forma, não só por cor. */
        .filtro-chip.ativo { font-weight: 800; box-shadow: inset 0 0 0 1px currentColor; }

        .busca-comentarios { position: relative; min-width: 240px; }
        .busca-comentarios i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--green-light); font-size: 0.85rem; }
        .busca-comentarios input {
            width: 100%; padding: 10px 16px 10px 38px; border-radius: 999px;
            border: 1px solid var(--campo-border); font-size: 0.85rem; font-family: inherit;
            background: var(--campo-bg); color: var(--campo-texto); outline: none;
            transition: border-color .2s, background .2s;
        }
        .busca-comentarios input::placeholder { color: rgba(255, 255, 255, .62); }
        body.light-theme .busca-comentarios input::placeholder { color: rgba(28, 39, 22, .62); }
        body.high-contrast .busca-comentarios input { background: #000000 !important; color: #ffffff !important; border: 2px solid #ffffff !important; }

        .lista-comentarios { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; align-items: start; }

        .comentario-item {
            padding: 16px 18px; border-radius: 16px; background: var(--item-bg); border: var(--card-border);
            transition: transform var(--hover-duration) var(--ease-premium), border-color var(--hover-duration) ease;
        }
        .comentario-item:hover { transform: translateY(-3px); border-color: rgba(79, 119, 36, .4); }
        body.high-contrast .comentario-item { background: #000000 !important; border: 2px solid #ffff00 !important; }

        .comentario-topo { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
        .comentario-autor { display: flex; align-items: center; gap: 8px; }
        .comentario-avatar {
            width: 30px; height: 30px; border-radius: 50%; background: var(--card-verde-bg); border: 1px solid var(--card-verde-border); color: var(--card-verde-texto);
            display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.74rem; flex-shrink: 0;
        }
        .comentario-autor-info { display: flex; flex-direction: column; }
        .comentario-nome { font-size: 0.82rem; font-weight: 700; color: var(--text-principal); }
        .comentario-data { font-size: 0.68rem; color: var(--text-secundario); }

        .comentario-estrelas { display: flex; gap: 2px; font-size: 0.72rem; }
        .comentario-estrelas i { color: rgba(255, 255, 255, .18); }
        .comentario-estrelas i.preenchida { color: var(--gold-light); }
        body.light-theme .comentario-estrelas i { color: #d1d5db; }
        body.light-theme .comentario-estrelas i.preenchida { color: var(--gold); }

        .sentimento-badge { font-size: 0.64rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
        .sentimento-badge.positivo { background: var(--card-verde-bg); color: var(--card-verde-texto); }
        .sentimento-badge.neutro { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .sentimento-badge.negativo { background: var(--card-vermelho-bg); color: var(--card-vermelho-texto); }
        body.light-theme .sentimento-badge.neutro { background: #fef3c7; color: #b45309; }
        body.high-contrast .sentimento-badge { background: #000000 !important; border: 1px solid #ffff00 !important; color: #ffff00 !important; }

        .comentario-item p.comentario-texto { color: var(--text-principal); font-size: 0.86rem; line-height: 1.6; white-space: pre-wrap; margin-bottom: 8px; }

        .comentario-categorias { display: flex; flex-wrap: wrap; gap: 8px; }
        .categoria-tag { font-size: 0.64rem; font-weight: 600; color: var(--text-secundario); background: var(--card-bg); border: var(--card-border); padding: 3px 8px; border-radius: 20px; }
        body.high-contrast .categoria-tag { background: #000000 !important; border: 1px solid #ffff00 !important; color: #ffff00 !important; }

        .estado-vazio { text-align: center; padding: 40px 20px; color: var(--text-secundario); }
        .estado-vazio i { font-size: 2rem; color: var(--green); margin-bottom: 12px; display: block; }

        /* ==========================================================================
        7. ALTO CONTRASTE (conteúdo — cabeçalho/rodapé já vêm do compartilhado)
        ========================================================================== */
        body.high-contrast {
            --text-principal: #ffff00;
            --text-secundario: #ffff00;
            --item-bg: #111111;
        }
        body.high-contrast .box { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }
        body.high-contrast .main h1, body.high-contrast .main h2, body.high-contrast .main h3,
        body.high-contrast .main p, body.high-contrast .main span, body.high-contrast .main label,
        body.high-contrast .main small, body.high-contrast .main i { color: #ffff00 !important; }
        body.high-contrast a:focus-visible, body.high-contrast button:focus-visible { outline: 3px solid #ffff00 !important; outline-offset: 2px !important; }

        /* ==========================================================================
        8. RESPONSIVIDADE
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
            .avaliacao-form { padding: 28px 22px; }
            .nota-preview { margin-top: 20px; }
            .categorias-form-grid { grid-template-columns: 1fr; }
            .comentarios-toolbar { flex-direction: column; align-items: stretch; }
            .busca-comentarios { min-width: 0; }
        }

        @media (max-width: 576px) {
            /* A barra de contato some nesse breakpoint, então o main só
               precisa reservar espaço pra navbar. */
            .main { padding-top: 86px; }
        }

        @media (max-width: 560px) {
            .stats-grid { grid-template-columns: 1fr; }
            .lista-comentarios { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

    @include('partials.landing-nav', ['paginaAtiva' => 'avaliacoes'])

    <!-- ÁREA DE CONTEÚDO PRINCIPAL (o <main> vem de partials/landing-nav) -->
    <div class="main">
        <div class="header">
            <span class="section-tag reveal-tag">Avaliação do AgriFarm</span>
            <h1 class="section-title reveal-title"><i class="fa-solid fa-comments accent titulo-icone" aria-hidden="true"></i> O que dizem sobre o <span class="accent">sistema</span></h1>
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
                        <button type="button" class="filtro-chip ativo" data-filtro="todas" aria-pressed="true">Todas</button>
                        @for ($i = 5; $i >= 1; $i--)
                            <button type="button" class="filtro-chip" data-filtro="{{ $i }}" aria-pressed="false">{{ $i }} <i class="fa-solid fa-star" aria-hidden="true"></i><span class="a11y-sr-only">{{ $i == 1 ? ' estrela' : ' estrelas' }}</span></button>
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
                                        <div class="comentario-estrelas" role="img" aria-label="{{ $comentario->nota }} de 5 estrelas">
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
    </div>

    @include('partials.landing-footer')

    @include('partials.acessibilidade')
    @include('partials.assistente-ia')

    @include('partials.landing-scripts')

    <script src="{{ asset('js/vendor/chart.min.js') }}"></script>
    <script>
        // Menu mobile, botão de acessibilidade na navbar, Tema Claro,
        // "voltar ao topo", VLibras e animação dos títulos vêm de
        // partials.landing-scripts (os mesmos das demais páginas públicas).
        document.addEventListener('DOMContentLoaded', function () {
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

                // Visual: acende todas as estrelas até a nota. Leitor de tela
                // (role="radio"): só a da nota escolhida fica marcada — antes
                // 1..N ficavam "marcadas" juntas. Tab entra no grupo uma vez
                // (na nota atual) e as setas trocam a nota, como num radio.
                function aplicar(valor) {
                    botoes.forEach(function (btn, i) {
                        const v = Number(btn.dataset.valor);
                        btn.classList.toggle('selecionada', v <= valor);
                        btn.setAttribute('aria-checked', v === valor ? 'true' : 'false');
                        btn.tabIndex = (valor ? v === valor : i === 0) ? 0 : -1;
                    });
                    inputEl.value = valor || '';
                    if (aoMudar) aoMudar(valor);
                }

                botoes.forEach(function (btn, i) {
                    btn.addEventListener('click', function () {
                        aplicar(Number(btn.dataset.valor));
                    });
                    btn.addEventListener('keydown', function (e) {
                        const passo = { ArrowRight: 1, ArrowUp: 1, ArrowLeft: -1, ArrowDown: -1 }[e.key];
                        if (!passo) return;
                        e.preventDefault();
                        const alvo = botoes[Math.min(Math.max(i + passo, 0), botoes.length - 1)];
                        aplicar(Number(alvo.dataset.valor));
                        alvo.focus();
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
                        filtrosEstrela.querySelectorAll('.filtro-chip').forEach(function (c) { c.classList.remove('ativo'); c.setAttribute('aria-pressed', 'false'); });
                        chip.classList.add('ativo');
                        chip.setAttribute('aria-pressed', 'true');
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
