<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AgriFarm | Contato</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @include('partials.landing-styles')

    <style>
        /* =========================
           VALIDAÇÃO DO FORMULÁRIO
        ========================== */

        .form-field {
            position: relative;
        }

        .form-input,
        .form-select,
        .form-textarea {
            transition: border-color .2s ease,
                box-shadow .2s ease,
                background-color .2s ease;
        }

        .form-input.input-error,
        .form-select.input-error,
        .form-textarea.input-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, .10);
        }

        .form-input.input-valid,
        .form-select.input-valid,
        .form-textarea.input-valid {
            border-color: #4f7724 !important;
        }

        .field-error {
            display: none;
            margin-top: 6px;
            color: #dc3545;
            font-size: .82rem;
            font-weight: 500;
            line-height: 1.3;
        }

        .field-error.show {
            display: block;
        }

        .field-error i {
            margin-right: 4px;
        }

        .character-counter {
            display: block;
            text-align: right;
            margin-top: 5px;
            color: var(--text-muted);
            font-size: .78rem;
        }

        .form-input-wrap {
            position: relative;
        }

        .form-input-wrap .form-input,
        .form-input-wrap .form-select {
            width: 100%;
        }

        .form-textarea {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .form-success {
            display: none;
        }

        .form-success.show {
            display: flex;
        }

        .form-error-global {
            display: none;
            margin-bottom: 20px;
            padding: 13px 16px;
            border-radius: 10px;
            background: rgba(220, 53, 69, .08);
            border: 1px solid rgba(220, 53, 69, .25);
            color: #b42318;
            font-size: .9rem;
        }

        .form-error-global.show {
            display: block;
        }

        /* Alto Contraste: os estados de validação usavam vermelho/verde
           fixos, por fora do padrão preto/amarelo do resto do modo — a
           borda/realce ficava com um contraste imprevisível contra o fundo
           preto forçado. Vira amarelo uniforme (como os demais avisos do
           site em Alto Contraste); quem depende do modo continua sabendo
           qual campo tem erro pela própria mensagem de texto, que nunca
           depende de cor. */
        body.high-contrast .form-input.input-error,
        body.high-contrast .form-select.input-error,
        body.high-contrast .form-textarea.input-error,
        body.high-contrast .form-input.input-valid,
        body.high-contrast .form-select.input-valid,
        body.high-contrast .form-textarea.input-valid {
            border-color: #ffff00 !important;
            box-shadow: none !important;
        }

        body.high-contrast .field-error {
            color: #ffff00 !important;
        }

        body.high-contrast .character-counter {
            color: #ffff00 !important;
        }

        body.high-contrast .form-error-global {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

    @include('partials.landing-nav', ['paginaAtiva' => 'contato'])

    <section id="contato">

        <div class="container">

            <span class="section-tag reveal-tag">
                Fale Conosco
            </span>

            <h1 class="section-title reveal-title">
                <i class="fa-solid fa-envelope accent titulo-icone" aria-hidden="true"></i> Entre em Contato
            </h1>

            <p
                style="text-align:center;color:var(--text-muted);font-size:1.05rem;margin-top:8px">
                Interessado em conhecer o AgriFarm?
                Preencha os dados corretamente e nossa equipe retornará em breve.
            </p>

            <div class="form-container">

                <form id="contact-form" novalidate>

                    <!-- ERRO GERAL -->
                    <div id="form-global-error" class="form-error-global">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Verifique os campos destacados antes de enviar o formulário.
                    </div>

                    <div class="form-grid">

                        <!-- NOME -->
                        <div class="form-field">

                            <label class="form-label" for="contact-name">
                                Nome Completo *
                            </label>

                            <div class="form-input-wrap">

                                <svg class="input-icon"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0
                                        3.75 3.75 0 017.5 0z
                                        M4.501 20.118a7.5 7.5 0 0114.998 0
                                        A17.933 17.933 0 0112 21.75
                                        c-2.676 0-5.216-.584-7.499-1.632z" />

                                </svg>

                                <input
                                    type="text"
                                    id="contact-name"
                                    name="name"
                                    class="form-input"
                                    placeholder="Seu nome completo"
                                    autocomplete="name"
                                    maxlength="100"
                                    required>

                            </div>

                            <span
                                id="name-error"
                                class="field-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Informe seu nome completo usando apenas letras e espaços.
                            </span>

                        </div>


                        <!-- EMAIL -->
                        <div class="form-field">

                            <label class="form-label" for="contact-email">
                                Endereço de E-mail *
                            </label>

                            <div class="form-input-wrap">

                                <svg class="input-icon"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M21.75 6.75v10.5
                                        a2.25 2.25 0 01-2.25 2.25h-15
                                        a2.25 2.25 0 01-2.25-2.25V6.75
                                        m19.5 0A2.25 2.25 0 0019.5 4.5h-15
                                        a2.25 2.25 0 00-2.25 2.25
                                        m19.5 0v.243
                                        a2.25 2.25 0 01-1.07 1.916l-7.5 4.615
                                        a2.25 2.25 0 01-2.36 0L3.32 8.91
                                        a2.25 2.25 0 01-1.07-1.916V6.75" />

                                </svg>

                                <input
                                    type="email"
                                    id="contact-email"
                                    name="email"
                                    class="form-input"
                                    placeholder="seu@email.com"
                                    autocomplete="email"
                                    maxlength="150"
                                    required>

                            </div>

                            <span
                                id="email-error"
                                class="field-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Informe um endereço de e-mail válido.
                            </span>

                        </div>


                        <!-- TELEFONE -->
                        <div class="form-field">

                            <label class="form-label" for="contact-phone">
                                Telefone / WhatsApp
                            </label>

                            <div class="form-input-wrap">

                                <svg class="input-icon"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.25 6.75
                                        c0 8.284 6.716 15 15 15h2.25
                                        a2.25 2.25 0 002.25-2.25v-1.372
                                        c0-.516-.351-.966-.852-1.091l-4.423-1.106
                                        c-.44-.11-.902.055-1.173.417l-.97 1.293
                                        c-.282.376-.769.542-1.21.38
                                        a12.035 12.035 0 01-7.143-7.143
                                        c-.162-.441.004-.928.38-1.21l1.293-.97
                                        c.363-.271.527-.734.417-1.173L6.963 3.102
                                        a1.125 1.125 0 00-1.091-.852H4.5
                                        A2.25 2.25 0 002.25 4.5v2.25z" />

                                </svg>

                                <input
                                    type="tel"
                                    id="contact-phone"
                                    name="phone"
                                    class="form-input"
                                    placeholder="(00) 00000-0000"
                                    inputmode="numeric"
                                    autocomplete="tel"
                                    maxlength="15">

                            </div>

                            <span
                                id="phone-error"
                                class="field-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Informe um telefone válido com DDD.
                            </span>

                        </div>


                        <!-- EMPRESA -->
                        <div class="form-field">

                            <label class="form-label" for="contact-company">
                                Empresa / Propriedade Rural
                            </label>

                            <div class="form-input-wrap">

                                <svg class="input-icon"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15
                                        M5.25 3v18m13.5-18v18
                                        M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5
                                        m3-6H15m-1.5 3H15m-1.5 3H15
                                        M9 21v-3.375
                                        c0-.621.504-1.125 1.125-1.125h3.75
                                        c.621 0 1.125.504 1.125 1.125V21" />

                                </svg>

                                <input
                                    type="text"
                                    id="contact-company"
                                    name="company"
                                    class="form-input"
                                    placeholder="Nome da empresa ou fazenda"
                                    autocomplete="organization"
                                    maxlength="150">

                            </div>

                            <span
                                id="company-error"
                                class="field-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Informe um nome válido.
                            </span>

                        </div>


                        <!-- ASSUNTO -->
                        <div class="form-field full">

                            <label class="form-label" for="contact-subject">
                                Assunto *
                            </label>

                            <div class="form-input-wrap">

                                <svg class="input-icon"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    style="top:16px;transform:none;position:absolute;">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M7.5 8.25h9m-9 3H12
                                        m-9.75 1.51
                                        c0 1.6 1.123 2.994 2.707 3.227
                                        1.129.166 2.27.293 3.423.379
                                        .35.026.67.21.865.501L12 21
                                        l2.755-4.133a1.14 1.14 0 01.865-.501
                                        48.172 48.172 0 003.423-.379
                                        c1.584-.233 2.707-1.626 2.707-3.228V6.741
                                        c0-1.602-1.123-2.995-2.707-3.228
                                        A48.394 48.394 0 0012 3
                                        c-2.392 0-4.744.175-7.043.513
                                        C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />

                                </svg>

                                <select
                                    id="contact-subject"
                                    name="subject"
                                    class="form-select"
                                    style="padding-left:42px"
                                    required>

                                    <option value="" disabled selected>
                                        Selecione um assunto
                                    </option>

                                    <option value="demo">
                                        Solicitar Demonstração do Sistema
                                    </option>

                                    <option value="pricing">
                                        Informações sobre Planos e Preços
                                    </option>

                                    <option value="partnership">
                                        Proposta de Parceria Comercial
                                    </option>

                                    <option value="support">
                                        Suporte Técnico
                                    </option>

                                    <option value="investor">
                                        Interesse em Investimento
                                    </option>

                                    <option value="other">
                                        Outro Assunto
                                    </option>

                                </select>

                            </div>

                            <span
                                id="subject-error"
                                class="field-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Selecione um assunto.
                            </span>

                        </div>


                        <!-- MENSAGEM -->
                        <div class="form-field full">

                            <label class="form-label" for="contact-message">
                                Mensagem *
                            </label>

                            <div class="form-input-wrap">

                                <textarea
                                    id="contact-message"
                                    name="message"
                                    class="form-textarea"
                                    placeholder="Descreva detalhadamente sua necessidade ou dúvida..."
                                    minlength="10"
                                    maxlength="2000"
                                    required></textarea>

                            </div>

                            <span
                                id="message-error"
                                class="field-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Escreva uma mensagem com pelo menos 10 caracteres.
                            </span>

                            <span
                                id="message-counter"
                                class="character-counter">
                                0 / 2000
                            </span>

                        </div>

                    </div>


                    <!-- RODAPÉ -->
                    <div class="form-footer">

                        <div class="privacy-note">

                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75
                                    m-3-7.036A11.959 11.959 0 013.598 6
                                    11.99 11.99 0 003 9.749
                                    c0 5.592 3.824 10.29 9 11.623
                                    5.176-1.332 9-6.03 9-11.622
                                    0-1.31-.21-2.571-.598-3.751
                                    h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />

                            </svg>

                            Suas informações são tratadas com total
                            confidencialidade e não serão compartilhadas
                            com terceiros.

                        </div>


                        <!-- SUCESSO -->
                        <div
                            id="form-success"
                            class="form-success">

                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75
                                    M21 12a9 9 0 11-18 0
                                    9 9 0 0118 0z" />

                            </svg>

                            Mensagem enviada com sucesso!
                            Retornaremos em breve.

                        </div>


                        <button
                            type="submit"
                            id="btn-submit"
                            class="btn-submit">

                            <svg
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 12L3.269 3.126
                                    A59.768 59.768 0 0121.485 12
                                    59.77 59.77 0 013.27 20.876
                                    L5.999 12zm0 0h7.5" />

                            </svg>

                            Enviar Mensagem

                        </button>

                    </div>

                </form>

            </div>


            <p class="contato-disclaimer">

                Projeto Acadêmico — Curso de Desenvolvimento de Sistemas · SENAI
                <br>

                <span>
                    Desenvolvido para fins educacionais e de inovação tecnológica
                    no agronegócio.
                </span>

            </p>

        </div>

    </section>


    @include('partials.landing-footer')

    @include('partials.acessibilidade')

    @include('partials.assistente-ia')

    @include('partials.landing-scripts')


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const form = document.getElementById('contact-form');

            const nameInput = document.getElementById('contact-name');
            const emailInput = document.getElementById('contact-email');
            const phoneInput = document.getElementById('contact-phone');
            const companyInput = document.getElementById('contact-company');
            const subjectInput = document.getElementById('contact-subject');
            const messageInput = document.getElementById('contact-message');

            const btnSubmit = document.getElementById('btn-submit');
            const successBox = document.getElementById('form-success');
            const globalError = document.getElementById('form-global-error');
            const messageCounter = document.getElementById('message-counter');


            /* =========================
               FUNÇÕES AUXILIARES
            ========================== */

            function limparTexto(valor) {
                return valor
                    .replace(/\s+/g, ' ')
                    .trim();
            }


            function mostrarErro(input, errorId, mensagem = null) {

                input.classList.add('input-error');
                input.classList.remove('input-valid');

                const error = document.getElementById(errorId);

                if (mensagem) {
                    error.innerHTML =
                        '<i class="fa-solid fa-circle-exclamation"></i> ' +
                        mensagem;
                }

                error.classList.add('show');
            }


            function removerErro(input, errorId) {

                input.classList.remove('input-error');

                if (input.value.trim() !== '') {
                    input.classList.add('input-valid');
                }

                const error = document.getElementById(errorId);

                error.classList.remove('show');
            }


            // normalizar=true só no blur/envio: reescrever o campo a cada
            // tecla apagava o espaço digitado antes da próxima palavra.
            function validarNome(normalizar = false) {

                const valor = limparTexto(nameInput.value);

                /*
                 * Permite:
                 * - letras com acentos
                 * - espaços
                 * - nomes compostos
                 *
                 * Não permite:
                 * - números
                 * - símbolos
                 */

                const somenteLetras =
                    /^[A-Za-zÀ-ÖØ-öø-ÿ]+(?:\s+[A-Za-zÀ-ÖØ-öø-ÿ]+)+$/;

                if (!valor) {

                    mostrarErro(
                        nameInput,
                        'name-error',
                        'Informe seu nome completo.'
                    );

                    return false;
                }

                if (!somenteLetras.test(valor)) {

                    mostrarErro(
                        nameInput,
                        'name-error',
                        'Digite seu nome completo usando apenas letras e espaços.'
                    );

                    return false;
                }

                if (normalizar === true) nameInput.value = valor;

                removerErro(nameInput, 'name-error');

                return true;
            }


            function validarEmail() {

                const valor = emailInput.value.trim();

                const emailRegex =
                    /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

                if (!valor) {

                    mostrarErro(
                        emailInput,
                        'email-error',
                        'Informe seu endereço de e-mail.'
                    );

                    return false;
                }

                if (!emailRegex.test(valor)) {

                    mostrarErro(
                        emailInput,
                        'email-error',
                        'Informe um endereço de e-mail válido.'
                    );

                    return false;
                }

                removerErro(emailInput, 'email-error');

                return true;
            }


            function validarTelefone() {

                const valor = phoneInput.value.trim();

                /*
                 * Campo opcional.
                 * Se estiver vazio, não apresenta erro.
                 */

                if (!valor) {

                    phoneInput.classList.remove('input-error');
                    phoneInput.classList.remove('input-valid');

                    document
                        .getElementById('phone-error')
                        .classList.remove('show');

                    return true;
                }

                const numeros = valor.replace(/\D/g, '');

                /*
                 * Brasil:
                 * 10 números = telefone fixo
                 * 11 números = celular
                 */

                if (numeros.length !== 10 && numeros.length !== 11) {

                    mostrarErro(
                        phoneInput,
                        'phone-error',
                        'Informe um telefone válido com DDD.'
                    );

                    return false;
                }

                removerErro(phoneInput, 'phone-error');

                return true;
            }


            function validarEmpresa(normalizar = false) {

                const valor = limparTexto(companyInput.value);

                /*
                 * Campo opcional.
                 * Permite letras, números e caracteres comuns.
                 */

                if (!valor) {

                    companyInput.classList.remove('input-error');
                    companyInput.classList.remove('input-valid');

                    document
                        .getElementById('company-error')
                        .classList.remove('show');

                    return true;
                }

                if (valor.length < 2) {

                    mostrarErro(
                        companyInput,
                        'company-error',
                        'Informe um nome válido.'
                    );

                    return false;
                }

                if (normalizar === true) companyInput.value = valor;

                removerErro(companyInput, 'company-error');

                return true;
            }


            function validarAssunto() {

                if (!subjectInput.value) {

                    mostrarErro(
                        subjectInput,
                        'subject-error',
                        'Selecione um assunto.'
                    );

                    return false;
                }

                removerErro(subjectInput, 'subject-error');

                return true;
            }


            function validarMensagem(normalizar = false) {

                // Só trim (sem limparTexto): a mensagem pode ter quebras de linha.
                const valor = messageInput.value.trim();

                if (!valor) {

                    mostrarErro(
                        messageInput,
                        'message-error',
                        'Informe uma mensagem.'
                    );

                    return false;
                }

                if (valor.length < 10) {

                    mostrarErro(
                        messageInput,
                        'message-error',
                        'A mensagem deve possuir pelo menos 10 caracteres.'
                    );

                    return false;
                }

                if (normalizar === true) messageInput.value = valor;

                removerErro(messageInput, 'message-error');

                return true;
            }


            /* =========================
               NOME
               BLOQUEIA NÚMEROS E
               CARACTERES INVÁLIDOS
            ========================== */

            nameInput.addEventListener('input', function () {

                // Só reatribui se algo mudou — senão o cursor pula pro fim.
                const filtrado = this.value
                    .replace(/[^A-Za-zÀ-ÖØ-öø-ÿ ]/g, '')
                    .replace(/ {2,}/g, ' ');
                if (filtrado !== this.value) this.value = filtrado;

                if (this.value.trim()) {
                    validarNome();
                }

            });


            nameInput.addEventListener('blur', () => validarNome(true));


            /* =========================
               TELEFONE
               SOMENTE NÚMEROS
            ========================== */

            phoneInput.addEventListener('input', function () {

                /*
                 * Remove qualquer letra ou símbolo.
                 */
                let numeros = this.value.replace(/\D/g, '');

                /*
                 * Limita a 11 números.
                 */
                numeros = numeros.substring(0, 11);

                /*
                 * Aplica máscara:
                 *
                 * 11 99999-9999
                 */

                if (numeros.length <= 2) {

                    this.value = numeros
                        ? '(' + numeros
                        : '';

                } else if (numeros.length <= 6) {

                    this.value =
                        '(' +
                        numeros.substring(0, 2) +
                        ') ' +
                        numeros.substring(2);

                } else if (numeros.length <= 10) {

                    this.value =
                        '(' +
                        numeros.substring(0, 2) +
                        ') ' +
                        numeros.substring(2, 6) +
                        '-' +
                        numeros.substring(6);

                } else {

                    this.value =
                        '(' +
                        numeros.substring(0, 2) +
                        ') ' +
                        numeros.substring(2, 7) +
                        '-' +
                        numeros.substring(7, 11);

                }

                if (this.value.trim()) {
                    validarTelefone();
                }

            });


            phoneInput.addEventListener('blur', validarTelefone);


            /* =========================
               EMAIL
            ========================== */

            emailInput.addEventListener('input', function () {

                /*
                 * Remove espaços.
                 */
                this.value = this.value.replace(/\s/g, '');

                if (this.value.trim()) {
                    validarEmail();
                }

            });

            emailInput.addEventListener('blur', validarEmail);


            /* =========================
               EMPRESA
            ========================== */

            companyInput.addEventListener('input', function () {

                const filtrado = this.value.replace(/ {2,}/g, ' ');
                if (filtrado !== this.value) this.value = filtrado;

                if (this.value.trim()) {
                    validarEmpresa();
                }

            });

            companyInput.addEventListener('blur', () => validarEmpresa(true));


            /* =========================
               ASSUNTO
            ========================== */

            subjectInput.addEventListener('change', validarAssunto);


            /* =========================
               MENSAGEM
            ========================== */

            messageInput.addEventListener('input', function () {

                /*
                 * Limita a 2000 caracteres.
                 */
                if (this.value.length > 2000) {
                    this.value = this.value.substring(0, 2000);
                }

                const quantidade =
                    this.value.length;

                messageCounter.textContent =
                    quantidade + ' / 2000';

                if (this.value.trim()) {
                    validarMensagem();
                }

            });

            messageInput.addEventListener('blur', () => validarMensagem(true));


            /* =========================
               ENVIO DO FORMULÁRIO
            ========================== */

            form.addEventListener('submit', async function (event) {

                event.preventDefault();

                globalError.classList.remove('show');

                /*
                 * Valida TODOS os campos.
                 */

                const nomeValido = validarNome(true);
                const emailValido = validarEmail();
                const telefoneValido = validarTelefone();
                const empresaValida = validarEmpresa(true);
                const assuntoValido = validarAssunto();
                const mensagemValida = validarMensagem(true);

                const formularioValido =
                    nomeValido &&
                    emailValido &&
                    telefoneValido &&
                    empresaValida &&
                    assuntoValido &&
                    mensagemValida;


                /*
                 * Se houver erro, não envia.
                 */

                if (!formularioValido) {

                    globalError.classList.add('show');

                    const primeiroErro =
                        form.querySelector('.input-error');

                    if (primeiroErro) {
                        primeiroErro.focus();
                    }

                    return;
                }


                /*
                 * Evita múltiplos envios.
                 */

                btnSubmit.disabled = true;

                const textoOriginal = btnSubmit.innerHTML;

                btnSubmit.innerHTML = `
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        style="width:18px;height:18px;animation:spin 1s linear infinite">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001
                            M2.985 19.644v-4.992m0 0h4.992
                            m-4.993 0l3.181 3.183
                            a8.25 8.25 0 0013.803-3.7
                            M4.031 9.865a8.25 8.25 0 0013.803-3.7
                            l3.181 3.182
                            m0-4.991v4.99" />

                    </svg>

                    Enviando...
                `;


                /*
                 * ==================================================
                 * INTEGRAÇÃO COM BACKEND
                 * ==================================================
                 *
                 * O formulário está pronto para receber o endpoint
                 * real.
                 *
                 * Exemplo futuro:
                 *
                 * const response = await fetch('/contato', {
                 *     method: 'POST',
                 *     headers: {
                 *         'Content-Type': 'application/json',
                 *         'X-CSRF-TOKEN': csrfToken
                 *     },
                 *     body: JSON.stringify({
                 *         name: nameInput.value,
                 *         email: emailInput.value,
                 *         phone: phoneInput.value,
                 *         company: companyInput.value,
                 *         subject: subjectInput.value,
                 *         message: messageInput.value
                 *     })
                 * });
                 *
                 * ==================================================
                 */


                try {

                    /*
                     * IMPORTANTE:
                     * Não simular sucesso.
                     *
                     * Aqui o código aguarda a integração real
                     * com o backend.
                     */

                    const formData = new FormData(form);

                    const response = await fetch(@json(route('landing.contato.enviar')), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const resultado = await response.json().catch(() => ({}));

                    if (!response.ok || !resultado.ok) {
                        let mensagemErro = resultado.mensagem
                            || 'Não foi possível enviar sua mensagem. Tente novamente.';

                        if (response.status === 422 && resultado.errors) {
                            mensagemErro = Object.values(resultado.errors)[0][0];
                        } else if (response.status === 429) {
                            mensagemErro = 'Muitas tentativas seguidas. Aguarde um minuto e tente novamente.';
                        }

                        throw new Error(mensagemErro);
                    }

                    successBox.innerHTML = `
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75
                                M21 12a9 9 0 11-18 0
                                9 9 0 0118 0z" />

                        </svg>
                    `;
                    successBox.append(resultado.mensagem);

                    successBox.classList.add('show');

                    form.reset();
                    messageCounter.textContent = '0 / 2000';

                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = textoOriginal;

                } catch (error) {

                    console.error(
                        'Erro ao enviar formulário:',
                        error
                    );

                    globalError.textContent = error.message
                        || 'Não foi possível enviar sua mensagem. Tente novamente.';

                    globalError.classList.add('show');

                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = textoOriginal;

                }

            });

        });

    </script>

</body>

</html>