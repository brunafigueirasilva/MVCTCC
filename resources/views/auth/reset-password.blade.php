<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrifarm | Redefinir Senha</title>

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
    }

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
                <img src="{{ asset('otimizado/marca/logo.webp') }}" alt="Logo Agrifarm">
            </div>

            <div class="left-content">
                <h2>Defina sua Nova Senha</h2>
                <p>
                    Escolha uma senha forte para manter o acesso à sua conta protegido.
                </p>
            </div>
        </div>

        <div class="right-side">
            @if($errors->any())
                <div class="erro" role="alert">
                    @foreach($errors->all() as $erro)
                        <p>{{ $erro }}</p>
                    @endforeach
                </div>
            @endif

            <div class="top-text">
                <h1><i class="fa-solid fa-key titulo-icone titulo-icone-verde" aria-hidden="true"></i> Redefinir Senha</h1>
                <p id="ajudaCodigo">Digite o código enviado para o seu e-mail e escolha sua nova senha.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <div class="form-group">
                    <label for="campoEmail">Email</label>
                    <div class="input-box">
                        <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                        <input type="email" id="campoEmail" name="email" autocomplete="email" @error('email') aria-invalid="true" @enderror value="{{ old('email', $email ?? '') }}" placeholder="Digite seu email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="campoCodigo">Código de Verificação</label>
                    <div class="input-box">
                        <i class="fa-solid fa-key" aria-hidden="true"></i>
                        <input type="text" id="campoCodigo" name="codigo" autocomplete="one-time-code" aria-describedby="ajudaCodigo" @error('codigo') aria-invalid="true" @enderror value="{{ old('codigo') }}" placeholder="000000" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="campoSenha">Nova Senha</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        <input type="password" id="campoSenha" name="password" autocomplete="new-password" @error('password') aria-invalid="true" @enderror placeholder="Mín. 8 caracteres, maiúscula, minúscula, número e @" required minlength="8">
                    </div>
                </div>

                <div class="form-group">
                    <label for="campoSenhaConfirmacao">Confirmar Nova Senha</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        <input type="password" id="campoSenhaConfirmacao" name="password_confirmation" autocomplete="new-password" @error('password_confirmation') aria-invalid="true" @enderror placeholder="Repita a nova senha" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn-login">Redefinir Senha</button>

                <div class="footer">
                    Não recebeu ou o código expirou?
                    <a href="{{ route('password.request') }}">Pedir um novo código</a>
                </div>
                <div class="footer">
                    Lembrou a senha?
                    <a href="{{ route('login.index') }}">Voltar para o Login</a>
                </div>
            </form>
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
    {{-- Assíncrono: não segura o DOMContentLoaded da página esperando o CDN do governo (scripts async ainda terminam antes do "load", que o widget usa). Se o CDN falhar, segue sem o widget. --}}
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js" async onload="if (window.VLibras) new window.VLibras.Widget('https://vlibras.gov.br/app');"></script>
</body>

</html>
