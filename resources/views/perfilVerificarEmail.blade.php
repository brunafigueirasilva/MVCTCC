<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrifarm | Verificar E-mail</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
@include('partials.paleta-verde')
    <style>
    :root {
        --bg-body-overlay: rgba(2, 31, 15, 0.75);
        --bg-container: white;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-label: #374151;
        --border-color: #d1d5db;
        --input-bg: white;

        --panel-bg: white;
        --panel-text: #1f2937;
        --panel-item-bg: #f9fafb;
        --panel-btn-bg: #e5e7eb;
        --panel-btn-text: #374151;
        --panel-btn-hover: #d1d5db;
    }

    /* Tema global sincronizado (localStorage/servidor via
       partials/acessibilidade) — mesmo padrão de perfilConfirmarSenha.blade.php,
       pra esta tela (no meio do fluxo de verificar e-mail) não ignorar o
       tema escolhido em qualquer outra página do sistema. */
    body.dark-theme {
        --bg-body-overlay: rgba(2, 8, 5, 0.85);
        --bg-container: #17251d;
        --text-primary: #f3f4f6;
        --text-secondary: #9ca3af;
        --text-label: #e5e7eb;
        --border-color: #374151;
        --input-bg: #111c16;

        --panel-bg: #111c16;
        --panel-text: #f1f9f4;
        --panel-item-bg: #0a1810;
        --panel-btn-bg: #1e3a2a;
        --panel-btn-text: #d9ede1;
        --panel-btn-hover: #2a4d38;
    }

    body.high-contrast {
        --bg-body-overlay: rgba(0, 0, 0, 0.95);
        --bg-container: #000000;
        --text-primary: #ffff00;
        --text-secondary: #ffffff;
        --text-label: #ffffff;
        --border-color: #ffffff;
        --input-bg: #000000;

        --panel-bg: #000000;
        --panel-text: #ffffff;
        --panel-item-bg: #111111;
        --panel-btn-bg: #ffffff;
        --panel-btn-text: #000000;
        --panel-btn-hover: #ffff00;
    }

    body.high-contrast .login-container { border: 2px solid #ffff00; }
    body.high-contrast .btn-login { background: #000000 !important; color: #ffff00 !important; border: 2px solid #ffff00 !important; }
    body.high-contrast .btn-reenviar { background: #000000 !important; color: #ffff00 !important; border: 2px solid #ffff00 !important; }
    body.high-contrast .footer a { color: #ffff00 !important; }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
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
    }

    .login-container {
        width: 100%;
        max-width: 1100px;
        background: var(--bg-container);
        border-radius: 28px;
        overflow: hidden;
        display: flex;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }

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
        justify-content: center;
    }

    .logo-icon img {
        width: 150px;
        height: auto;
        margin-bottom: 30px;
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

    .top-text h1 {
        font-size: 2rem;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .top-text p {
        color: var(--text-secondary);
    }

    .top-text p strong {
        color: var(--text-primary);
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

    .input-box i {
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
        letter-spacing: 4px;
        font-weight: 600;
    }

    .input-box input:focus {
        outline: none;
        border-color: var(--verde-ativo, #087a47);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--verde-principal, #0e4a27) 15%, transparent);
    }

    .erro {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #f87171;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-size: 0.88rem;
    }

    .success {
        background: var(--verde-claro, #e8f5ee);
        color: var(--verde-texto, #0e4a27);
        border: 1px solid var(--verde-secundario, #16a765);
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-size: 0.88rem;
    }

    .btn-login {
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

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px color-mix(in srgb, var(--verde-principal, #0e4a27) 30%, transparent);
    }

    .btn-reenviar {
        width: 100%;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-primary);
        padding: 13px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        margin-top: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-reenviar:hover:not(:disabled) {
        background: var(--panel-item-bg);
    }

    .btn-reenviar:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .footer {
        margin-top: 25px;
        text-align: center;
        color: var(--text-secondary);
        font-size: 0.88rem;
    }

    .footer a {
        color: var(--verde-texto, #0e4a27);
        font-weight: 600;
        text-decoration: none;
    }

    @media(max-width: 900px) {
        .login-container {
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
    }
</style>

<body>

    <div class="login-container">
        <div class="left-side">
            <div class="logo-icon">
                <img src="{{ asset('logo.png') }}" alt="Logo Agrifarm">
            </div>

            <div class="left-content">
                <h2>Verifique seu E-mail</h2>
                <p>
                    Confirmar seu e-mail garante que só você tenha acesso à conta e que consiga
                    recuperá-la caso esqueça a senha no futuro.
                </p>
            </div>
        </div>

        <div class="right-side">
            @if(session('success'))
                <div class="success" role="status">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="erro" role="alert">
                    @foreach($errors->all() as $erro)
                        <p>{{ $erro }}</p>
                    @endforeach
                </div>
            @endif

            <div class="top-text">
                <h1><i class="fa-solid fa-envelope-circle-check titulo-icone titulo-icone-verde" aria-hidden="true"></i> Verificar E-mail</h1>
                <p id="ajudaCodigo">Digite o código de 6 dígitos enviado para <strong>{{ $cadastro->email }}</strong>. O código vale por 15 minutos.</p>
            </div>

            <form method="POST" action="{{ route('perfil.verificarEmail.post') }}">
                @csrf

                <div class="form-group">
                    <label for="campoCodigo">Código de Verificação</label>
                    <div class="input-box">
                        <i class="fa-solid fa-key" aria-hidden="true"></i>
                        <input type="text" id="campoCodigo" name="codigo" autocomplete="one-time-code" aria-describedby="ajudaCodigo" @error('codigo') aria-invalid="true" @enderror value="{{ old('codigo') }}" placeholder="000000" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-login">Confirmar E-mail</button>
            </form>

            <form method="POST" action="{{ route('perfil.verificarEmail.enviar') }}" id="formReenviar">
                @csrf
                <button type="submit" class="btn-reenviar" id="btnReenviar">
                    <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                    <span id="textoReenviar">Reenviar código</span>
                </button>
            </form>

            <div class="footer">
                Errou o e-mail? <a href="{{ route('perfil') }}">Volte ao perfil</a> para corrigi-lo antes de verificar.
            </div>
        </div>
    </div>

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
        if (window.VLibras) new window.VLibras.Widget('https://vlibras.gov.br/app'); // CDN do governo fora do ar/bloqueado: segue sem o widget
    </script>

    <script>
        // Controle de tempo do "Reenviar código": 60s de espera entre
        // envios, guardado no localStorage (sobrevive ao redirect que o
        // próprio reenvio causa) pra não depender só do rate-limit do
        // servidor — o usuário já vê o botão desabilitado com a contagem
        // regressiva, em vez de só receber um erro se clicar de novo rápido
        // demais.
        (function () {
            const CHAVE = 'agrifarm_reenvio_verificacao_email_ate';
            const ESPERA_SEGUNDOS = 60;
            const botao = document.getElementById('btnReenviar');
            const texto = document.getElementById('textoReenviar');
            const form = document.getElementById('formReenviar');
            if (!botao || !texto || !form) return;

            let intervaloId = null;

            function iniciarContagem(ateTimestamp) {
                function atualizar() {
                    const restanteMs = ateTimestamp - Date.now();
                    if (restanteMs <= 0) {
                        clearInterval(intervaloId);
                        botao.disabled = false;
                        texto.textContent = 'Reenviar código';
                        return;
                    }
                    const segundos = Math.ceil(restanteMs / 1000);
                    botao.disabled = true;
                    texto.textContent = `Aguarde ${segundos}s para reenviar`;
                }

                atualizar();
                intervaloId = setInterval(atualizar, 1000);
            }

            const ateSalvo = parseInt(localStorage.getItem(CHAVE) || '0', 10);
            if (ateSalvo > Date.now()) {
                iniciarContagem(ateSalvo);
            }

            form.addEventListener('submit', () => {
                localStorage.setItem(CHAVE, String(Date.now() + ESPERA_SEGUNDOS * 1000));
            });
        })();
    </script>
</body>

</html>
