<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrifarm | Criar Conta</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
@include('partials.paleta-verde')
    <style>
    /* ================= CONFIGURAÇÕES DE VARIÁVEIS (TEMA CLARO DEFAULT) ================= */
    :root {
        --font-scale: 100%;
        /* Tamanho natural do sistema: 100% (sem redução) — o controle
           "Zoom do Sistema" no painel de acessibilidade continua ajustando
           a partir daqui. */
        --zoom-scale: 1;

        /* Cores do Tema Claro (Base do seu design original) */
        --bg-body-overlay: rgba(2, 31, 15, 0.75);
        --bg-container: white;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-label: #374151;
        --border-color: #d1d5db;
        --input-bg: white;
        --divider-color: #e5e7eb;
        --google-btn-bg: white;
        --google-btn-hover: #f9fafb;

        /* Cores do Painel */
        --panel-bg: white;
        --panel-text: #1f2937;
        --panel-item-bg: #f9fafb;
        --panel-btn-bg: #e5e7eb;
        --panel-btn-text: #374151;
        --panel-btn-hover: #d1d5db;
    }

    /* ================= VARIÁVEIS DO TEMA ESCURO ================= */
    body.dark-theme {
    --bg-body-overlay: rgba(2, 31, 15, 0.75);
        --bg-container: #17251d;
        --text-primary: #f3f4f6;
        --text-secondary: #9ca3af;
        --text-label: #e5e7eb;
        --border-color: #374151;
        --input-bg: #111c16;
        --divider-color: #2b3e34;
        --google-btn-bg: #111c16;
        --google-btn-hover: #1c2e24;

        --panel-bg: #111c16;
        --panel-text: #f3f4f6;
        --panel-item-bg: #17251d;
        --panel-btn-bg: #2b3e34;
        --panel-btn-text: #e5e7eb;
        --panel-btn-hover: #385345;
    }

    /* ================= VARIÁVEIS DE ALTO CONTRASTE ================= */
    body.high-contrast {
        --bg-body-overlay: rgba(0, 0, 0, 0.95);
        --bg-container: #000000;
        --text-primary: #ffff00;
        /* Texto Amarelo para alto contraste */
        --text-secondary: #ffffff;
        --text-label: #ffffff;
        --border-color: #ffffff;
        --input-bg: #000000;
        --divider-color: #ffffff;
        --google-btn-bg: #000000;
        --google-btn-hover: #1a1a1a;

        --panel-bg: #000000;
        --panel-text: #ffffff;
        --panel-item-bg: #111111;
        --panel-btn-bg: #ffffff;
        --panel-btn-text: #000000;
        --panel-btn-hover: #ffff00;
    }

    /* RESET E APLICAÇÃO DE ESCALAS DINÂMICAS */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    /* ================= CSS EXCLUSIVO DO QUADRADO CENTRAL DE CONFIGURAÇÕES ================= */
    /* Gatilho (Engrenagem Flutuante) */
    .accessibility-trigger {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--verde-principal, #0e4a27);
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: transform 0.3s ease, background-color 0.2s;
    }

    .accessibility-trigger:hover {
        transform: scale(1.1) rotate(45deg);
        background: var(--verde-hover, #0b3d21);
    }

    body.high-contrast .accessibility-trigger {
        background: #ffff00;
        color: #000000;
        border: 2px solid #ffffff;
    }

    /* Caixa Quadrada Centralizada (Modal) */
    .accessibility-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        width: 90%;
        max-width: 550px;
        /* Tamanho proporcional para o quadrado */
        background: var(--panel-bg);
        color: var(--panel-text);
        border-radius: 24px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
        z-index: 1002;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
    }

    .accessibility-modal.open {
        opacity: 1;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1);
    }

    /* ================= APLICAÇÃO DO ALTO CONTRASTE NA PÁGINA DE CADASTRO INTEIRA ================= */

    body.high-contrast {
        /* Fundo da tela inteira fica preto absoluto */
        background: #000000 !important;
    }

    /* Container Principal do Formulário e Painel Esquerdo */
    body.high-contrast .register-container {
        background: #000000 !important;
        border: 3px solid #ffffff !important;
        box-shadow: none !important;
    }

    /* Lado Esquerdo (Painel Informativo Verde) */
    body.high-contrast .left-side {
        background: #000000 !important;
        /* Remove o gradiente verde e a foto */
        border-right: 2px solid #ffffff !important;
    }

    body.high-contrast .left-content h2 {
        color: #ffff00 !important;
        /* Texto em Destaque Amarelo */
    }

    body.high-contrast .left-content p {
        color: #ffffff !important;
        /* Texto comum Branco */
    }

    body.high-contrast .feature i {
        background: #000000 !important;
        border: 1px solid #ffff00 !important;
        color: #ffffff !important;
        /* Ícones em Amarelo */
    }

    body.high-contrast .feature span {
        color: #ffffff !important;
    }

    /* Lado Direito (Formulário de Cadastro) */
    body.high-contrast .right-side {
        background: #000000 !important;
    }

    body.high-contrast .top-text :is(h1, h2) {
        color: #ffff00 !important;
    }

    body.high-contrast .top-text p {
        color: #ffffff !important;
    }

    /* Labels e Caixas de Entrada (Inputs) */
    body.high-contrast label {
        color: #ffffff !important;
    }

    body.high-contrast .input-box input {
        background-color: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
    }

    body.high-contrast .input-box i {
        color: #ffff00 !important;
        /* Ícones dentro dos inputs em amarelo */
    }

    body.high-contrast .input-box input:focus {
        border-color: #ffff00 !important;
        box-shadow: 0 0 0 3px #ffff00 !important;
    }

    /* Botão Principal de Criar Conta */
    body.high-contrast .btn-register {
        background: #000000 !important;
        color: #ffff00 !important;
        border: 2px solid #ffff00 !important;
        box-shadow: none !important;
    }

    body.high-contrast .btn-register:hover {
        background: #ffff00 !important;
        color: #000000 !important;
        transform: none !important;
    }

    /* Linha Divisória */
    body.high-contrast .divider {
        color: #ffffff !important;
    }

    body.high-contrast .divider::before,
    body.high-contrast .divider::after {
        background: #ffffff !important;
    }

    /* Botão do Google */
    body.high-contrast .google-btn {
        background: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
    }

    body.high-contrast .google-btn:hover {
        background: #ffffff !important;
        color: #000000 !important;
    }

    /* Torna a logo do Google em escala de cinza/branca no alto contraste para não quebrar a paleta */
    body.high-contrast .google-btn img {
        filter: brightness(0) invert(1);
    }

    /* Rodapé do Formulário */
    body.high-contrast .footer {
        color: #ffffff !important;
    }

    body.high-contrast .footer a {
        color: #ffff00 !important;
        text-decoration: underline !important;
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header h3 {
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: transparent;
        border: none;
        font-size: 30px;
        color: var(--text-secondary);
        cursor: pointer;
        line-height: 1;
    }

    .modal-close:hover {
        color: var(--text-primary);
    }

    .modal-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-height: 80vh;
        overflow-y: auto;
    }

    /* Organização interna das seções do quadrado */
    .modal-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* ================= ALTO CONTRASTE TOTAL UNIFICADO (PRETO, BRANCO E AMARELO) ================= */

    body.high-contrast {
        /* Fundo da tela inteira fica preto absoluto */
        background: #000000 !important;
    }

    /* ---------------- 1. A PÁGINA DE CADASTRO ---------------- */
    /* Container Principal do Formulário e Painel Esquerdo */
    body.high-contrast .register-container {
        body.high-contrast .register-container {

    background: rgba(0,0,0,0.92) !important;

    border: 3px solid #ffff00 !important;

    box-shadow:
        0 0 25px rgba(255,255,0,0.4) !important;

}
    }

    /* Lado Esquerdo (Painel Informativo) */
    body.high-contrast .left-side {
        background: #000000 !important;
        /* Remove o gradiente verde e a foto de fundo */
        border-right: 2px solid #ffffff !important;
    }

    body.high-contrast .left-content h2,
    body.high-contrast .top-text :is(h1, h2) {
        color: #ffff00 !important;
        /* Títulos em Amarelo */
    }

    body.high-contrast .left-content p,
    body.high-contrast .top-text p,
    body.high-contrast label,
    body.high-contrast .footer {
        color: #ffffff !important;
        /* Textos comuns e labels em Branco */
    }

    body.high-contrast .feature i {
        background: #000000 !important;
        border: 1px solid #ffff00 !important;
        color: #ffffff !important;
    }

    body.high-contrast .feature span {
        color: #ffffff !important;
    }

    body.high-contrast .input-box i {
        color: #ffff00 !important;
        /* Ícones internos em amarelo */
    }

    body.high-contrast .input-box input:focus {
        border-color: #ffff00 !important;
        box-shadow: 0 0 0 3px #ffff00 !important;
    }

    /* Botões da Página de Cadastro */
    body.high-contrast .btn-register {
        background: #000000 !important;
        color: #ffff00 !important;
        border: 2px solid #ffff00 !important;
        box-shadow: none !important;
    }

    body.high-contrast .btn-register:hover {
        background: #ffff00 !important;
        color: #000000 !important;
        transform: none !important;
    }

    body.high-contrast .google-btn {
        background: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
    }

    body.high-contrast .google-btn:hover {
        background: #ffffff !important;
        color: #000000 !important;
    }

    body.high-contrast .google-btn img {
        filter: brightness(0) invert(1);
        /* Torna a logo do Google branca */
    }

    body.high-contrast .divider {
        color: #ffffff !important;
    }

    body.high-contrast .divider::before,
    body.high-contrast .divider::after {
        background: #ffffff !important;
    }

    body.high-contrast .footer a {
        color: #ffff00 !important;
        text-decoration: underline !important;
    }


    /* ---------------- 2. O PAINEL DE ACESSIBILIDADE (MODAL E GATILHO) ---------------- */
    /* Botão da Engrenagem */
    body.high-contrast .accessibility-trigger {
        background-color: #000000 !important;
        color: #ffff00 !important;
        border: 3px solid #ffff00 !important;
    }

    body.high-contrast .accessibility-trigger:hover {
        background-color: #ffff00 !important;
        color: #000000 !important;
    }

    /* O Quadrado Centralizado (Modal) */
    body.high-contrast .accessibility-modal {
        background: #000000 !important;
        color: #ffffff !important;
        border: 3px solid #ffffff !important;
        box-shadow: 0 0 0 5px #000000, 0 25px 60px rgba(0, 0, 0, 0.9) !important;
    }

    /* Cabeçalho do Modal e Títulos de Seção */
    body.high-contrast .modal-header {
        border-bottom: 2px solid #ffffff !important;
    }

    body.high-contrast .modal-header h3,
    body.high-contrast .modal-close,
    body.high-contrast .modal-section h4 {
        color: #ffff00 !important;
    }

    /* Cards Internos do Modal (Temas, Fontes, Leitura) */
    body.high-contrast .control-item,
    body.high-contrast .control-item-vertical,
    body.high-contrast .voice-box {
        background: #000000 !important;
        border: 2px solid #ffffff !important;
        color: #ffffff !important;
    }

    body.high-contrast .control-label,
    body.high-contrast .voice-tip {
        color: #ffffff !important;
    }

    /* Botões do Modal (+, -, Padrão) */
    body.high-contrast .acc-btn {
        background: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
    }

    body.high-contrast .acc-btn:hover {
        background: #ffffff !important;
        color: #000000 !important;
    }

    body.high-contrast .acc-btn.btn-secondary {
        color: #ffff00 !important;
        border-color: #ffff00 !important;
    }

    body.high-contrast .acc-btn.btn-secondary:hover {
        background: #ffff00 !important;
        color: #000000 !important;
    }

    /* Botões de Ouvir e Parar Página (Removido Verde/Vermelho) */
    body.high-contrast .voice-start {
        background: #000000 !important;
        color: #ffff00 !important;
        border: 2px solid #ffff00 !important;
    }

    body.high-contrast .voice-start:hover {
        background: #ffff00 !important;
        color: #000000 !important;
    }

    body.high-contrast .voice-stop {
        background: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
    }

    body.high-contrast .voice-stop:hover {
        background: #ffffff !important;
        color: #000000 !important;
    }

    /* Chaves Liga/Desliga (Switches) dentro do Modal */
    body.high-contrast .slider-switch {
        background-color: #000000 !important;
        border: 2px solid #ffffff !important;
    }

    body.high-contrast .slider-switch:before {
        background-color: #ffffff !important;
    }

    body.high-contrast input:checked+.slider-switch {
        background-color: #ffff00 !important;
        border-color: #ffff00 !important;
    }

    body.high-contrast input:checked+.slider-switch:before {
        background-color: #000000 !important;
    }

    /* Fundo Escurecido de Trás do Modal (Overlay) */
    body.high-contrast .modal-overlay {
        body.high-contrast .modal-overlay {
    background: rgba(0,0,0,0.45) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
    }

    .modal-section h4 {
        font-size: 0.88rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    /* Grade de exibição lado a lado dentro do quadrado */
    .control-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .control-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--panel-item-bg);
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .control-item-vertical {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: var(--panel-item-bg);
        padding: 14px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .control-label {
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Chaves liga/desliga (Switches) */
    .switch-btn {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }

    .switch-btn input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider-switch {
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

    .slider-switch:before {
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

    input:checked+.slider-switch {
        background-color: var(--verde-secundario, #16a765);
    }

    body.high-contrast input:checked+.slider-switch {
        background-color: #ffff00;
    }

    body.high-contrast input:checked+.slider-switch:before {
        background-color: #000000;
    }

    input:checked+.slider-switch:before {
        transform: translateX(22px);
    }

    /* Ajuste dos botões de controle de escala */
    .btn-group-row {
        display: flex;
        gap: 6px;
    }

    .acc-btn {
        flex: 1;
        padding: 8px;
        border: 1px solid var(--border-color);
        background: var(--panel-btn-bg);
        color: var(--panel-btn-text);
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: 0.2s;
    }

    .acc-btn:hover {
        background: var(--panel-btn-hover);
    }

    .acc-btn.btn-secondary {
        background: transparent;
    }

    /* Box da Leitura de Tela */
    .voice-box {
        background: var(--panel-item-bg);
        border: 1px solid var(--border-color);
        padding: 16px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .voice-controls {
        display: flex;
        gap: 10px;
    }

    .acc-btn-voice {
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
    }

    .voice-start {
        background: var(--verde-principal, #0e4a27);
    }

    .voice-start:hover {
        background: var(--verde-hover, #0b3d21);
    }

    .voice-stop {
        background: #b91c1c;
    }

    .voice-stop:hover {
        background: #dc2626;
    }

    .voice-tip {
        font-size: 0.78rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    /* Backdrop/Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        /* Suave desfoque ao fundo */
        z-index: 1001;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    /* Responsividade Adaptada para Telas Menores */
    @media(max-width: 600px) {
        .control-grid {
            grid-template-columns: 1fr;
        }

        .accessibility-modal {
            width: 95%;
            border-radius: 16px;
        }

    }

    html {
        /* "zoom" em vez de "transform: scale()": um transform no <html>
           cria containing block novo para os elementos position:fixed. */
        font-size: var(--font-scale);
        zoom: var(--zoom-scale);
        width: 100%;
        height: 100%;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(var(--bg-body-overlay), var(--bg-body-overlay)),
            url('https://images.unsplash.com/photo-1500937386664-56d1dfef3854?q=80&w=1600&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        transition: background 0.3s ease;
    }

    /* CONTAINER INTEGRADO */
    .register-container {
        width: 100%;
        max-width: 1100px;
        background: var(--bg-container);
        border-radius: 28px;
        overflow: hidden;
        display: flex;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        transition: background 0.3s ease;
    }

    /* LADO ESQUERDO */
    .left-side {
        width: 50%;
        background: linear-gradient(color-mix(in srgb, var(--verde-principal, #0e4a27) 75%, transparent), rgba(8, 26, 16, 0.95)),
            url('https://images.unsplash.com/photo-1516467508483-a7212febe31a?q=80&w=1400&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 60px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .left-content h2 {
        font-size: 2.3rem;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .left-content p {
        color: #d1d5db;
        font-size: 1rem;
        line-height: 1.7;
        max-width: 450px;
    }

    .features {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-top: 40px;
    }

    .feature {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .feature i {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
    }

    /* LADO DIREITO (FORMULÁRIO MODIFICADO) */
    .right-side {
        width: 50%;
        padding: 50px 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .top-text {
        margin-bottom: 25px;
    }

    .top-text :is(h1, h2) {
        font-size: 2rem;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .top-text p {
        color: var(--text-secondary);
    }

    .form-row {
        display: flex;
        gap: 16px;
    }

    .form-row .form-group {
        flex: 1;
        /* Sem isso, um item flex não encolhe além do tamanho intrínseco do
           <input> (padrão do navegador) — em telas mais estreitas isso
           empurrava a coluna "Confirmar Senha" (e o botão do olhinho
           dentro dela) pra fora da borda do card. */
        min-width: 0;
    }

    .form-group {
        margin-bottom: 18px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-label);
    }

    .input-box {
        position: relative;
    }

    /* ">" (filho direto), não descendente qualquer: sem isso, esta regra
       também pegava o <i> do olho (que fica dentro do <button
       class="toggle-password">, um neto de .input-box) e o posicionava
       em "left:15px" relativo ao próprio botão — empurrando o ícone do
       olho pra fora do campo, por cima do card. */
    .input-box > i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }

    .input-box input {
        width: 100%;
        padding: 13px 13px 13px 45px;
        border: 1px solid var(--border-color);
        background-color: var(--input-bg);
        color: var(--text-primary);
        border-radius: 12px;
        font-size: 0.95rem;
        transition: 0.2s;
    }

    .input-box input:focus {
        outline: none;
        border-color: var(--verde-ativo, #087a47);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--verde-principal, #0e4a27) 15%, transparent);
    }

    /* Estado de validação (usado no campo de Email) */
    .input-box.is-valid input {
        border-color: var(--verde-secundario, #16a765);
    }

    .input-box.is-invalid input {
        border-color: #ef4444;
    }

    .input-box.is-invalid input:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
    }

    /* Cores do retorno dos campos (ok/erro) com contraste ≥ 4,5:1 em cada tema. */
    :root { --feedback-ok: #15803d; --feedback-erro: #dc2626; }
    body.dark-theme { --feedback-ok: #4ade80; --feedback-erro: #fca5a5; }
    body.high-contrast { --feedback-ok: #ffff00; --feedback-erro: #ffffff; }

    .field-feedback {
        display: block;
        margin-top: 6px;
        font-size: 0.8rem;
        color: var(--feedback-erro);
        min-height: 1em;
    }

    /* Botão de mostrar/ocultar senha */
    .input-box input.has-toggle {
        padding-right: 45px;
    }

    .toggle-password {
        position: absolute;
        right: 8px;
        min-width: 32px; /* alvo de toque confortável */
        min-height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 0.95rem;
    }

    .toggle-password:hover {
        color: var(--text-primary);
    }

    body.high-contrast .toggle-password {
        color: #ffff00 !important;
    }

    .btn-register {
        width: 100%;
        border: none;
        background: linear-gradient(90deg, var(--verde-principal, #0e4a27), var(--verde-principal, #0e4a27));
        color: white;
        padding: 15px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 5px;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px color-mix(in srgb, var(--verde-principal, #0e4a27) 30%, transparent);
    }

    .divider {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 24px 0;
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--divider-color);
    }

    .google-btn {
        width: 100%;
        border: 1px solid var(--border-color);
        background: var(--google-btn-bg);
        color: var(--text-primary);
        padding: 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        cursor: pointer;
        transition: 0.2s;
        font-weight: 500;
    }

    .google-btn:hover {
        background: var(--google-btn-hover);
    }

    .footer {
        margin-top: 25px;
        text-align: center;
        color: var(--text-secondary);
        font-size: 0.88rem;
    }

    .footer a,
    .footer a:visited {
        color: var(--verde-texto, #0e4a27);
        font-weight: 600;
        text-decoration: none;
    }

    .footer a:hover,
    .footer a:active {
        color: var(--verde-texto, #0e4a27);
    }

    .footer-note {
        margin-top: 14px;
        font-size: 0.75rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    body.high-contrast .footer a {
        color: var(--text-primary);
        text-decoration: underline;
    }

    body.high-contrast .footer-note {
        color: #ffffff !important;
    }

    /* ================= CSS EXCLUSIVO DO COMPONENTE DE CONFIGURAÇÃO ================= */
    /* Gatilho (Engrenagem Flutuante) */
    .accessibility-trigger {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--verde-principal, #0e4a27);
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        transition: transform 0.3s ease, background-color 0.2s;
    }

    .accessibility-trigger:hover {
        transform: scale(1.1) rotate(45deg);
        background: var(--verde-hover, #0b3d21);
    }

    body.high-contrast .accessibility-trigger {
        background: #ffff00;
        color: #000000;
        border: 2px solid #ffffff;
    }

    /* Drawer (Painel Deslizante Lateral) */
    .accessibility-drawer {
        position: fixed;
        top: 0;
        right: -360px;
        /* Escondido inicialmente */
        width: 350px;
        height: 100vh;
        background: var(--panel-bg);
        color: var(--panel-text);
        box-shadow: -5px 0 25px rgba(0, 0, 0, 0.25);
        z-index: 1002;
        transition: right 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
        overflow: hidden;
    }

    .accessibility-drawer.open {
        right: 0;
    }

    .drawer-header {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .drawer-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .drawer-close {
        background: transparent;
        border: none;
        font-size: 28px;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .drawer-close:hover {
        color: var(--text-primary);
    }

    .drawer-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 24px;
        overflow-y: auto;
    }

    /* Linhas estruturadas dos controles */
    .control-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--panel-item-bg);
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
    }

    .control-item-vertical {
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: var(--panel-item-bg);
        padding: 16px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
    }

    .control-label {
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Switches Customizados */
    .switch-btn {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .switch-btn input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider-switch {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 34px;
    }

    .slider-switch:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked+.slider-switch {
        background-color: var(--verde-secundario, #16a765);
    }

    body.high-contrast input:checked+.slider-switch {
        background-color: #ffff00;
    }

    body.high-contrast input:checked+.slider-switch:before {
        background-color: #000000;
    }

    input:checked+.slider-switch:before {
        transform: translateX(24px);
    }

    /* Grupo de botões (Aumentar/Diminuir Fonte e Zoom) */
    .btn-group-row {
        display: flex;
        gap: 8px;
    }

    .acc-btn {
        flex: 1;
        padding: 10px;
        border: 1px solid var(--border-color);
        background: var(--panel-btn-bg);
        color: var(--panel-btn-text);
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: 0.2s;
    }

    .acc-btn:hover {
        background: var(--panel-btn-hover);
    }

    .acc-btn.btn-secondary {
        background: transparent;
    }

    /* Controles de Voz */
    .voice-controls {
        display: flex;
        gap: 10px;
    }

    .acc-btn-voice {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: white;
    }

    .voice-start {
        background: var(--verde-principal, #0e4a27);
    }

    .voice-start:hover {
        background: var(--verde-hover, #0b3d21);
    }

    .voice-stop {
        background: #b91c1c;
    }

    .voice-stop:hover {
        background: #dc2626;
    }

    .voice-tip {
        font-size: 0.75rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    /* Overlay de escurecimento de fundo */
    .drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1001;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .drawer-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    /* RESPONSIVIDADE ADAPTADA */
    @media(max-width: 900px) {
        .register-container {
            flex-direction: column;
        }

        .left-side,
        .right-side {
            width: 100%;
        }

        .left-side {
            padding: 40px;
        }

        .right-side {
            padding: 40px 30px;
        }

        .left-content h2 {
            font-size: 1.8rem;
        }

        .form-row {
            flex-direction: column;
            gap: 0;
        }

        .accessibility-drawer {
            width: 100%;
            right: -100%;
            border-radius: 0;
        }
    }

    @media(max-width: 600px) {
        .top-text :is(h1, h2) {
            font-size: 1.5rem;
        }

        .left-content h2 {
            font-size: 1.4rem;
        }

        /* Acima do robô do assistente (mesmo canto): antes os dois ficavam
           no mesmo lugar e o robô cobria este botão no celular. */
        .accessibility-trigger {
            top: auto;
            bottom: calc(16px + 58px + 14px);
            right: 16px;
        }
    }
</style>

<body>
    @include('partials.a11y-global')

    <div class="register-container">
        <!-- LADO ESQUERDO -->
        <div class="left-side">
            <div class="logo">
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo Agrifarm" width="150" height="147">
            </div>

            <div class="left-content">
                <h2>Crie sua conta e modernize sua fazenda</h2>
                <p>
                    Gerencie fazendas, estoque, lotes, rações e relatórios
                    em um único sistema inteligente.
                </p>

                <div class="features">
                    <div class="feature">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard completo</span>
                    </div>
                    <div class="feature">
                        <i class="fa-solid fa-cow"></i>
                        <span>Controle animal em tempo real</span>
                    </div>
                    <div class="feature">
                        <i class="fa-solid fa-box"></i>
                        <span>Gestão de estoque inteligente</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- LADO DIREITO -->
        <div class="right-side">
            <div class="top-text">
                <h1><i class="fa-solid fa-user-plus titulo-icone titulo-icone-verde" aria-hidden="true"></i> Criar Conta</h1>
                <p>Cadastre-se para acessar o sistema</p>
            </div>

            <form action="{{ route('cadastro.add') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome</label>
                        <div class="input-box">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" name="nome" placeholder="Seu nome" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <div class="input-box">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" id="telefone" name="telefone" placeholder="(19) 99999-9999"
                                inputmode="tel" maxlength="15" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-box" id="emailBox">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Digite seu email" autocomplete="email" aria-describedby="emailFeedback" required>
                    </div>
                    <span class="field-feedback" id="emailFeedback" aria-live="polite"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Senha</label>
                        <div class="input-box" id="passwordBox">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" name="password" class="has-toggle"
                                placeholder="Digite sua senha" minlength="8" autocomplete="new-password" aria-describedby="passwordFeedback" required>
                            <button type="button" class="toggle-password" data-target="password"
                                aria-label="Mostrar senha">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <span class="field-feedback" id="passwordFeedback">Mínimo 8 caracteres, com maiúscula, minúscula, número e @.</span>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Senha</label>
                        <div class="input-box">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="has-toggle" placeholder="Confirme sua senha" minlength="8" required>
                            <button type="button" class="toggle-password" data-target="password_confirmation"
                                aria-label="Mostrar senha">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>


                <button type="submit" class="btn-register">Criar Conta</button>
            </form>

            <div class="divider">ou</div>

            <button type="button" class="google-btn">
                <img src="https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg" width="20"
                    alt="Google Logo">
                Continuar com Google
            </button>

            <div class="footer">
                Já possui conta?
                <a href="{{ route('login.index') }}">Entrar</a>            </div>
            </form>
        </div>
    </div>

    <!-- Botão Fixo de Engrenagem (Disparador) -->
    <button type="button" class="accessibility-trigger" id="accTrigger" aria-label="Abrir configurações de acessibilidade" aria-controls="accModal" aria-haspopup="dialog">
        <i class="fa-solid fa-universal-access"></i>
    </button>

    <!-- Quadrado Central de Configurações (Modal Box) -->
    <div class="accessibility-modal" id="accModal" role="dialog" aria-modal="true" aria-label="Painel de Acessibilidade">
        <div class="modal-header">
            <h3><i class="fa-solid fa-universal-access"></i> Painel de Acessibilidade</h3>
            <button class="modal-close" id="accClose" aria-label="Fechar painel">&times;</button>
        </div>

        <div class="modal-body">
            <!-- SEÇÃO: TEMAS -->
            <div class="modal-section">
                <h4><i class="fa-solid fa-palette"></i> Preferências de Tema</h4>
                <div class="control-grid">
                    <div class="control-item">
                        <span class="control-label" id="lblDarkMode"><i class="fa-solid fa-moon" aria-hidden="true"></i> Modo Escuro</span>
                        <label class="switch-btn">
                            <input type="checkbox" id="chkDarkMode" role="switch" aria-labelledby="lblDarkMode">
                            <span class="slider-switch"></span>
                        </label>
                    </div>
                    <div class="control-item">
                        <span class="control-label" id="lblHighContrast"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i> Alto Contraste</span>
                        <label class="switch-btn">
                            <input type="checkbox" id="chkHighContrast" role="switch" aria-labelledby="lblHighContrast">
                            <span class="slider-switch"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: FONTES E ZOOM -->
            <div class="modal-section">
                <h4><i class="fa-solid fa-sliders"></i> Dimensionamento</h4>
                <div class="control-grid">
                    <div class="control-item-vertical">
                        <span class="control-label"><i class="fa-solid fa-font"></i> Tamanho do Texto</span>
                        <div class="btn-group-row">
                            <button class="acc-btn" id="btnDecreaseFont" title="Diminuir texto"><i
                                    class="fa-solid fa-minus"></i></button>
                            <button class="acc-btn btn-secondary" id="btnResetFont"
                                title="Restaurar texto">Padrão</button>
                            <button class="acc-btn" id="btnIncreaseFont" title="Aumentar texto"><i
                                    class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="control-item-vertical">
                        <span class="control-label"><i class="fa-solid fa-magnifying-glass-plus"></i> Zoom do
                            Sistema</span>
                        <div class="btn-group-row">
                            <button class="acc-btn" id="btnDecreaseZoom" title="Diminuir zoom"><i
                                    class="fa-solid fa-minus"></i></button>
                            <button class="acc-btn btn-secondary" id="btnResetZoom"
                                title="Restaurar zoom">Padrão</button>
                            <button class="acc-btn" id="btnIncreaseZoom" title="Aumentar zoom"><i
                                    class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO: LEITURA DE TELA -->
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
                    <p class="voice-tip">Clique em "Ouvir Página" para escutar de forma automatizada o conteúdo
                        informativo desta tela.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay escuro ao fundo -->
    <div class="modal-overlay" id="accOverlay"></div>
    <!-- VLIBRAS EXISTENTE -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    {{-- Assíncrono: não segura o DOMContentLoaded da página esperando o CDN do governo (scripts async ainda terminam antes do "load", que o widget usa). Se o CDN falhar, segue sem o widget. --}}
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js" async onload="if (window.VLibras) new window.VLibras.Widget('https://vlibras.gov.br/app');"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // CAPTURA DOS ELEMENTOS DO MODAL
            const accTrigger = document.getElementById('accTrigger');
            const accModal = document.getElementById('accModal');
            const accClose = document.getElementById('accClose');
            const accOverlay = document.getElementById('accOverlay');

            // BOTÕES INTERNOS
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

            let currentFontScale = 100;
            let currentZoomScale = 1.0;
            let speechUtterance = null;

            // MÁSCARA DE TELEFONE - formata como (99) 99999-9999 enquanto o usuário digita
            const telefoneInput = document.getElementById('telefone');
            if (telefoneInput) {
                telefoneInput.addEventListener('input', (e) => {
                    let digits = e.target.value.replace(/\D/g, '').slice(0, 11);

                    if (digits.length > 10) {
                        digits = digits.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
                    } else if (digits.length > 6) {
                        digits = digits.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                    } else if (digits.length > 2) {
                        digits = digits.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
                    } else if (digits.length > 0) {
                        digits = digits.replace(/^(\d{0,2}).*/, '($1');
                    }

                    e.target.value = digits;
                });
            }

            // VALIDAÇÃO DE EMAIL - feedback visual em tempo real
            const emailInput = document.getElementById('email');
            const emailBox = document.getElementById('emailBox');
            const emailFeedback = document.getElementById('emailFeedback');
            if (emailInput) {
                const validateEmail = () => {
                    const value = emailInput.value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    emailBox.classList.remove('is-valid', 'is-invalid');
                    emailInput.removeAttribute('aria-invalid');
                    emailFeedback.textContent = '';

                    if (value.length === 0) return;

                    if (emailRegex.test(value)) {
                        emailBox.classList.add('is-valid');
                    } else {
                        emailBox.classList.add('is-invalid');
                        emailInput.setAttribute('aria-invalid', 'true');
                        emailFeedback.textContent = 'Digite um email válido.';
                    }
                };

                emailInput.addEventListener('input', validateEmail);
                emailInput.addEventListener('blur', validateEmail);
            }

            // VALIDAÇÃO DE SENHA - feedback visual em tempo real
            // Regras: mínimo 8 caracteres, 1 maiúscula, 1 minúscula, 1 número e o caractere @.
            const passwordInput = document.getElementById('password');
            const passwordBox = document.getElementById('passwordBox');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*@).{8,}$/;
            const passwordDica = 'Mínimo 8 caracteres, com maiúscula, minúscula, número e @.';

            if (passwordInput) {
                const validatePassword = () => {
                    const value = passwordInput.value;

                    passwordBox.classList.remove('is-valid', 'is-invalid');
                    passwordInput.removeAttribute('aria-invalid');

                    if (value.length === 0) {
                        passwordFeedback.textContent = passwordDica;
                        passwordFeedback.style.color = 'var(--text-secondary)';
                        return;
                    }

                    if (passwordRegex.test(value)) {
                        passwordBox.classList.add('is-valid');
                        passwordFeedback.textContent = passwordDica;
                        passwordFeedback.style.color = 'var(--feedback-ok)';
                    } else {
                        passwordBox.classList.add('is-invalid');
                        passwordInput.setAttribute('aria-invalid', 'true');
                        passwordFeedback.textContent = passwordDica;
                        passwordFeedback.style.color = 'var(--feedback-erro)';
                    }
                };

                passwordInput.addEventListener('input', validatePassword);
                passwordInput.addEventListener('blur', validatePassword);
            }

            // MOSTRAR/OCULTAR SENHA
            document.querySelectorAll('.toggle-password').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const target = document.getElementById(btn.dataset.target);
                    if (!target) return;

                    const icon = btn.querySelector('i');
                    const isHidden = target.type === 'password';

                    target.type = isHidden ? 'text' : 'password';
                    icon.classList.toggle('fa-eye', !isHidden);
                    icon.classList.toggle('fa-eye-slash', isHidden);
                    btn.setAttribute('aria-label', isHidden ? 'Ocultar senha' : 'Mostrar senha');
                });
            });

            // Alternar visibilidade do Quadrado Central
            const toggleModal = () => {
                accModal.classList.toggle('open');
                accOverlay.classList.toggle('active');
            };

            accTrigger.addEventListener('click', toggleModal);
            accClose.addEventListener('click', toggleModal);
            accOverlay.addEventListener('click', toggleModal);

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && accModal.classList.contains('open')) {
                    toggleModal();
                }
            });

            // Temas (Modo Escuro / Alto Contraste)
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
            });

            // Fontes
            const updateFontScale = (newScale) => {
                currentFontScale = Math.max(80, Math.min(newScale, 150));
                document.documentElement.style.setProperty('--font-scale', `${currentFontScale}%`);
                localStorage.setItem('fontScale', currentFontScale);
            };
            btnIncreaseFont.addEventListener('click', () => updateFontScale(currentFontScale + 10));
            btnDecreaseFont.addEventListener('click', () => updateFontScale(currentFontScale - 10));
            btnResetFont.addEventListener('click', () => updateFontScale(100));

            // Zoom
            const updateZoomScale = (newZoom) => {
                currentZoomScale = Math.max(0.5, Math.min(newZoom, 1.3));
                document.documentElement.style.setProperty('--zoom-scale', currentZoomScale);
                localStorage.setItem('zoomScale', currentZoomScale);
            };
            btnIncreaseZoom.addEventListener('click', () => updateZoomScale(currentZoomScale + 0.05));
            btnDecreaseZoom.addEventListener('click', () => updateZoomScale(currentZoomScale - 0.05));
            btnResetZoom.addEventListener('click', () => updateZoomScale(1.0));

            // Sintetizador de Voz (Leitura da Tela)
            btnStartVoice.addEventListener('click', () => {
                window.speechSynthesis.cancel();
                let textToRead = "";

                const mainHeading = document.querySelector('.left-content h2');
                const mainDesc = document.querySelector('.left-content p');
                const rightHeading = document.querySelector('.top-text :is(h1, h2)');
                const rightDesc = document.querySelector('.top-text p');

                if (mainHeading) textToRead += mainHeading.innerText + ". ";
                if (mainDesc) textToRead += mainDesc.innerText + ". ";
                if (rightHeading) textToRead += rightHeading.innerText + ". ";
                if (rightDesc) textToRead += rightDesc.innerText + ". ";

                document.querySelectorAll('.feature span').forEach(f => textToRead += f.innerText + ", ");

                if (!textToRead.trim()) return;

                speechUtterance = new SpeechSynthesisUtterance(textToRead);
                speechUtterance.lang = 'pt-BR';

                btnStartVoice.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Lendo...';

                speechUtterance.onend = () => btnStartVoice.innerHTML =
                    '<i class="fa-solid fa-play"></i> Ouvir Página';
                speechUtterance.onerror = () => btnStartVoice.innerHTML =
                    '<i class="fa-solid fa-play"></i> Ouvir Página';

                window.speechSynthesis.speak(speechUtterance);
            });

            btnStopVoice.addEventListener('click', () => {
                window.speechSynthesis.cancel();
                btnStartVoice.innerHTML = '<i class="fa-solid fa-play"></i> Ouvir Página';
            });

            // Carregar configurações salvas
            const loadPreferences = () => {
                // Tela de cadastro sempre abre em fundo claro, mesmo que o
                // usuário tenha escolhido modo escuro em outra tela do
                // sistema — o modo escuro salvo não é aplicado aqui (alto
                // contraste continua valendo, por ser uma necessidade real
                // de acessibilidade, não só estética).
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'contrast') {
                    document.body.classList.add('high-contrast');
                    chkHighContrast.checked = true;
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
        });
    </script>

    @include('partials.assistente-ia')
</body>

</html>
