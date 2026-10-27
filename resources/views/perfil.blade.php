@extends('layouts.app')

@section('title', 'AgriFarm | Meu Perfil')

@push('styles')
    <style>
        /* ==========================================================================
        1. RESET, VARIÁVEIS DE SISTEMA (mesmo design system do Dashboard)
        ========================================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }


        :root {
            --bg-principal: #f8fafc;
            --text-principal: #0f172a;
            --text-secundario: #64748b;
            --card-bg: #ffffff;
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 8px -1px rgba(15, 23, 42, 0.02);
            --card-border: 1px solid rgba(226, 232, 240, 0.8);

            --card-verde-bg: var(--verde-claro, #e8f5ee);
            --card-verde-border: var(--verde-claro, #e8f5ee);
            --card-verde-texto: var(--verde-texto, #0e4a27);

            --card-azul-bg: #f0f7ff;
            --card-azul-border: #e0f2fe;
            --card-azul-texto: #0369a1;

            --card-ambar-bg: #fffbeb;
            --card-ambar-border: #fde68a;
            --card-ambar-texto: #b45309;



        }

        body {
            background: var(--bg-principal);
            color: var(--text-principal);
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar, .navbar, .main, .box {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, filter 0.25s ease, left 0.3s ease, width 0.3s ease, margin 0.3s ease;
        }

        /* ==========================================================================
        2. NAVBAR SUPERIOR
        (blur do modal de acessibilidade e estrutura de navbar/sidebar/main
        já vêm de layouts/app.blade.php — fonte única, não duplicar aqui)
        ========================================================================== */
















        .navbar-right .accessibility-trigger {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid var(--surface-border);
        }
        .navbar-right .accessibility-trigger:hover { background: #e2e8f0; color: #0f172a; }

        /* ==========================================================================
        3. SIDEBAR LATERAL
        ========================================================================== */


        /* Fundo escurecido do menu mobile (fecha a sidebar ao clicar fora),
           igual ao Dashboard. */

        @media (max-width: 992px) {
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
        }



        @keyframes menuIconBounce {
            0% { transform: translateY(0) scale(1); }
            30% { transform: translateY(-9px) scale(1.16); }
            50% { transform: translateY(1px) scale(0.95); }
            70% { transform: translateY(-4px) scale(1.06); }
            100% { transform: translateY(0) scale(1); }
        }




        @media (prefers-reduced-motion: reduce) {
            .menu a, .menu i { transition: background-color 0.18s ease, color 0.18s ease; }
            .menu a:hover { transform: none; }
            .menu a:hover i { animation: none; }
        }


        /* ==========================================================================
        4. ÁREA PRINCIPAL
        ========================================================================== */
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
            padding-top: 20px;
        }


        .header { margin-bottom: 28px; }
        .header h2 { font-size: 28px; font-weight: 700; color: var(--text-principal); margin-bottom: 6px; letter-spacing: -0.5px; }
        .header p { color: var(--text-secundario); font-size: 15px; }

        .box {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 26px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
        }

        /* ==========================================================================
        5. RESUMO DO PERFIL
        ========================================================================== */
        .profile-summary {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .avatar-wrap { position: relative; width: 96px; height: 96px; flex-shrink: 0; }

        .avatar-box {
            width: 96px; height: 96px; border-radius: 50%; overflow: hidden;
            border: 3px solid var(--card-verde-border); background: var(--card-verde-bg);
            display: flex; align-items: center; justify-content: center;
            color: var(--card-verde-texto); font-size: 2.1rem;
        }

        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }

        .avatar-edit-btn {
            position: absolute; bottom: 2px; right: 2px; width: 32px; height: 32px;
            border-radius: 50%; background: var(--verde-principal, #0e4a27); color: #fff; border: 3px solid var(--card-bg);
            display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem;
        }
        .avatar-edit-btn:hover { background: var(--verde-hover, #0b3d21); }

        .profile-summary-info h1 { font-size: 1.4rem; color: var(--text-principal); margin-bottom: 2px; }
        .profile-summary-info p { color: var(--text-secundario); font-size: 0.9rem; margin-bottom: 10px; }

        .security-badges { display: flex; flex-wrap: wrap; gap: 6px; }

        .badge-info {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.7rem; font-weight: 600; padding: 4px 10px; border-radius: 20px;
            background: var(--card-azul-bg); color: var(--card-azul-texto); border: 1px solid var(--card-azul-border);
        }

        .badge-info i { font-size: 0.72rem; }

        .badge-info.ok { background: var(--card-verde-bg); color: var(--card-verde-texto); border-color: var(--card-verde-border); }
        .badge-info.alerta { background: var(--card-ambar-bg); color: var(--card-ambar-texto); border-color: var(--card-ambar-border); }

        /* ==========================================================================
        6. AVISO DE SEGURANÇA + ALERTAS
        ========================================================================== */
        .alerta {
            padding: 14px 18px; border-radius: 12px; margin-bottom: 22px; font-size: 0.9rem;
            display: flex; align-items: flex-start; gap: 10px; line-height: 1.5;
        }
        .alerta-info { background: var(--card-azul-bg); color: var(--card-azul-texto); border: 1px solid var(--card-azul-border); }

        /* Mensagem de cadastrar/editar/excluir — mesmo estilo usado em
           Lote/Plano/Estoque/Ração/Fazenda/Cocho (antes esta página tinha
           cores levemente diferentes das outras e nenhuma reação ao tema
           escuro/alto contraste). */
        .alerta-flash {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 0.875rem;
        }

        .alerta-flash i { margin-top: 2px; flex-shrink: 0; }
        .alerta-flash ul { margin: 6px 0 0 18px; }
        .alerta-flash strong { display: block; }

        .alerta-erro { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alerta-sucesso { background: var(--verde-claro, #e8f5ee); border: 1px solid var(--verde-borda, rgba(14, 74, 39, 0.28)); color: var(--verde-texto, #0e4a27); }

        body.dark-theme .alerta-erro { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        body.dark-theme .alerta-sucesso { background: color-mix(in srgb, var(--verde-secundario, #16a765) 12%, transparent); border-color: color-mix(in srgb, var(--verde-secundario, #16a765) 30%, transparent); color: var(--verde-texto, #0e4a27); }

        body.high-contrast .alerta-erro,
        body.high-contrast .alerta-sucesso {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        /* ==========================================================================
        7. FORMULÁRIO
        ========================================================================== */
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; align-items: start; margin-bottom: 22px; }

        .box :is(h2, h3) {
            font-size: 15px; font-weight: 700; color: var(--text-principal);
            margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
        }
        .box :is(h2, h3) i { color: var(--verde-texto, #0e4a27); }

        .campos-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .campo { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .campo.full { grid-column: 1 / -1; }
        .campo label { font-size: 0.85rem; font-weight: 600; color: var(--text-principal); }
        .campo small { font-size: 0.74rem; color: var(--text-secundario); }

        .campo .input-wrap { position: relative; }

        .campo input {
            width: 100%; padding: 11px 14px; border-radius: 10px;
            border: 1px solid #d1d5db; font-size: 0.92rem;
            background: #fff; color: #1f2937;
        }
        .campo input:focus { outline: none; border-color: var(--verde-ativo, #087a47); box-shadow: 0 0 0 3px color-mix(in srgb, var(--verde-principal, #0e4a27) 12%, transparent); }

        .campo input.has-toggle { padding-right: 42px; }

        /* O Edge (e navegadores baseados em Chromium com essa opção) injeta
           seu próprio ícone nativo de "revelar senha" dentro de qualquer
           input[type=password], colado na borda direita — bem em cima do
           nosso botão .toggle-password (também absolute, right:12px). Sem
           bloquear o nativo, os dois ícones ficam sobrepostos e um clique
           pode acionar a revelação do navegador sem passar pelo nosso botão
           (e sem atualizar o ícone de olho pra refletir o estado), dando a
           impressão de que a senha "aparece sozinha" ao digitar. */
        .campo input[type="password"]::-ms-reveal,
        .campo input[type="password"]::-ms-clear {
            display: none;
        }

        .toggle-password {
            position: absolute; right: 6px; min-width: 32px; min-height: 32px; display: inline-flex; align-items: center; justify-content: center; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--text-secundario); cursor: pointer; font-size: 0.9rem;
        }
        .toggle-password:hover { color: var(--text-principal); }

        .metadados-seguranca {
            margin-top: 4px; padding-top: 16px; border-top: 1px dashed var(--card-border);
            display: flex; flex-direction: column; gap: 6px; font-size: 0.78rem; color: var(--text-secundario);
        }
        .metadados-seguranca i { width: 16px; color: #94a3b8; }

        .btn-salvar {
            width: 100%; padding: 14px; border-radius: 12px; border: none;
            background: linear-gradient(90deg, var(--verde-principal, #0e4a27), var(--verde-principal, #0e4a27)); color: #fff;
            font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-salvar:hover { transform: translateY(-2px); box-shadow: 0 10px 20px color-mix(in srgb, var(--verde-principal, #0e4a27) 30%, transparent); }

        /* ==========================================================================
        8. TEMAS (Dark / Alto Contraste) — mesmos tokens do Dashboard
        ========================================================================== */
        body.dark-theme {
            --bg-principal: #0a1810;
            --text-principal: #f1f9f4;
            --text-secundario: #9db8a8;
            --card-bg: #12251a;
            --card-border: 1px solid color-mix(in srgb, var(--verde-secundario, #16a765) 16%, transparent);
            --card-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);
            --card-verde-bg: color-mix(in srgb, var(--verde-secundario, #16a765) 10%, transparent);
            --card-verde-border: color-mix(in srgb, var(--verde-secundario, #16a765) 30%, transparent);
            --card-verde-texto: var(--verde-texto, #0e4a27);
            --card-azul-bg: rgba(56, 189, 248, 0.1);
            --card-azul-border: rgba(56, 189, 248, 0.3);
            --card-azul-texto: #38bdf8;
            --card-ambar-bg: rgba(251, 191, 36, 0.1);
            --card-ambar-border: rgba(251, 191, 36, 0.3);
            --card-ambar-texto: #fbbf24;
        }
        body.dark-theme .navbar-right .accessibility-trigger:hover { background: rgba(255, 255, 255, 0.16); color: #ffffff; }
        body.dark-theme .navbar-right .accessibility-trigger { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.16); color: #f1f5f9; }
        body.dark-theme .campo input { background: #0f1712; color: #ededec; border-color: #374151; }

        body.high-contrast {
            background: #000000 !important;
            color: #ffff00 !important;
            /* Sem isso, textos que usam --text-principal/--text-secundario
               ficavam com a cor cinza-escura do tema claro sobre o fundo
               preto forçado — quase ilegível. */
            --text-principal: #ffff00;
            --text-secundario: #ffff00;
            --panel-bg: #000000;
            --panel-text: #ffffff;
            --panel-item-bg: #111111;
            --panel-btn-bg: #ffffff;
            --panel-btn-text: #000000;
            --panel-btn-hover: #ffff00;
        }
        body.high-contrast .box,
        body.high-contrast .navbar,
        body.high-contrast .sidebar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }
        body.high-contrast .navbar { border-width: 0 0 2px 0 !important; }
        body.high-contrast .sidebar { border-width: 0 2px 0 0 !important; }
        body.high-contrast .navbar-logo img { filter: brightness(0) invert(1); }
        body.high-contrast h1, body.high-contrast h2, body.high-contrast h3,
        body.high-contrast p, body.high-contrast span, body.high-contrast label,
        body.high-contrast small, body.high-contrast i {
            color: #ffff00 !important;
        }
        body.high-contrast .menu a.active,
        body.high-contrast .menu a:hover { background: #1a1a00 !important; }
        body.high-contrast .toggle-menu-btn,
        body.high-contrast .user-avatar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
        }
        body.high-contrast .campo input {
            background: #000000 !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
        }
        body.high-contrast .badge-info {
            background: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }
        /* Faltava aqui (o Dashboard já tem): sem isso o botão "Sair" ficava
           com o fundo vermelho translúcido de sempre no alto contraste, em
           vez do preto/branco usado no resto da navbar. */
        body.high-contrast .navbar-logout {
            background: #000000 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
        }
        body.high-contrast .btn-salvar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
            box-shadow: none !important;
        }
        body.high-contrast .btn-salvar:hover { background: #1a1a00 !important; }
        body.high-contrast .btn-salvar:focus-visible,
        body.high-contrast a:focus-visible,
        body.high-contrast button:focus-visible {
            outline: 3px solid #ffff00 !important;
            outline-offset: 2px !important;
        }

        /* ==========================================================================
        9. RESPONSIVIDADE
        ========================================================================== */
        @media (max-width: 992px) {
            .main { margin-left: 0; width: 100%; padding: 24px; padding-top: 20px; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .navbar-logo img { height: 36px; }
            .form-grid-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .campos-grid { grid-template-columns: 1fr; }
            .navbar-logout span { display: none; }
        }
    </style>
@endpush

@section('content')
    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <main class="main" id="mainArea">
        <div class="header">
            <h2><i class="fa-solid fa-user-gear titulo-icone" aria-hidden="true"></i> Meu Perfil</h2>
            <p>Gerencie seus dados pessoais, sua foto e a segurança da sua conta.</p>
        </div>

        @if (session('perfil_success'))
            <div class="alerta-flash alerta-sucesso" role="status" aria-live="polite">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <span>{{ session('perfil_success') }}</span>
            </div>
        @endif
        @if ($errors->perfil->any())
            <div class="alerta-flash alerta-erro" role="alert" aria-live="assertive">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                <div>
                    <strong>Não foi possível salvar o perfil:</strong>
                    <ul>
                        @foreach ($errors->perfil->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="box">
            <div class="profile-summary">
                <div class="avatar-wrap">
                    <div class="avatar-box" id="avatarPreviewBox">
                        @if($cadastro?->foto_url)
                            <img id="avatarPreviewImg" src="{{ $cadastro->foto_url }}" alt="Foto de perfil" referrerpolicy="no-referrer">
                        @else
                            <i class="fa-solid fa-user" id="avatarPreviewIcon"></i>
                            <img id="avatarPreviewImg" style="display:none;width:100%;height:100%;object-fit:cover;">
                        @endif
                    </div>
                    <label class="avatar-edit-btn" for="fotoInput" role="button" tabindex="0" aria-label="Alterar foto de perfil" onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); document.getElementById('fotoInput').click(); }">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                </div>
                <div class="profile-summary-info">
                    <h1>{{ $cadastro->responsavel }}</h1>
                    <p>{{ $cadastro->email }} &middot; Membro desde {{ optional($cadastro->created_at)->format('d/m/Y') ?? '—' }}</p>
                    <div class="security-badges">
                        <span class="badge-info {{ $cadastro->cpf ? 'ok' : '' }}">
                            <i class="fa-solid {{ $cadastro->cpf ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                            {{ $cadastro->cpf ? 'CPF cadastrado' : 'CPF não cadastrado' }}
                        </span>
                        <span class="badge-info ok">
                            <i class="fa-solid fa-phone"></i>
                            {{ $cadastro->telefone_formatado }}
                        </span>
                        <span class="badge-info">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Senha alterada {{ $cadastro->senha_alterada_em ? $cadastro->senha_alterada_em->diffForHumans() : 'nunca' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="alerta alerta-info">
            <i class="fa-solid fa-shield-halved"></i>
            {{-- Texto num <span> só: solto dentro do .alerta (flex), o
                 <strong> do e-mail virava um item à parte que não quebrava
                 linha e saía da tela com texto ampliado. --}}
            <span style="min-width: 0; overflow-wrap: anywhere;">Por segurança, toda alteração de perfil exige sua senha atual e gera um e-mail de confirmação
            para <strong>{{ $cadastro->email }}</strong> — inclusive para o endereço antigo, se você trocar o e-mail.</span>
        </div>

        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" id="formPerfil">
            @csrf
            @method('PUT')
            <input type="file" id="fotoInput" name="foto" accept="image/png,image/jpeg,image/webp" style="display:none;">

            <div class="form-grid-2">
                <div class="box">
                    <h2 class="box-titulo"><i class="fa-solid fa-id-card"></i> Dados pessoais</h2>
                    <div class="campos-grid">
                        <div class="campo full">
                            <label for="responsavel">Nome completo</label>
                            <input type="text" id="responsavel" name="responsavel" value="{{ old('responsavel', $cadastro->responsavel) }}" required>
                        </div>
                        <div class="campo">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $cadastro->cpf_formatado) }}" placeholder="000.000.000-00" maxlength="14" inputmode="numeric">
                            <small>Exige maioridade e data de nascimento.</small>
                        </div>
                        <div class="campo">
                            <label for="data_nascimento">Data de nascimento</label>
                            <input type="date" id="data_nascimento" name="data_nascimento"
                                   value="{{ old('data_nascimento', optional($cadastro->data_nascimento)->format('Y-m-d')) }}"
                                   max="{{ now()->subYears(18)->format('Y-m-d') }}">
                            <small>Obrigatória apenas se um CPF for informado.</small>
                        </div>
                        <div class="campo">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $cadastro->telefone_formatado) }}" placeholder="(00) 00000-0000" maxlength="15" inputmode="numeric" required>
                        </div>
                        <div class="campo">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $cadastro->email) }}" required>
                            <small>Trocar dispara um alerta para o e-mail antigo.</small>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <h2 class="box-titulo"><i class="fa-solid fa-lock"></i> Segurança</h2>
                    <div class="campo">
                        <label for="password">Nova senha</label>
                        <div class="input-wrap">
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="Deixe em branco para manter a atual" autocomplete="new-password">
                            <button type="button" class="toggle-password" data-target="password" aria-label="Mostrar senha"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        <small>Mínimo 8 caracteres, com maiúscula, minúscula, número e @.</small>
                    </div>
                    <div class="campo">
                        <label for="password_confirmation">Confirmar nova senha</label>
                        <div class="input-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="has-toggle" placeholder="Repita a nova senha" autocomplete="new-password">
                            <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Mostrar senha"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="senha_atual">Senha atual <span style="color:#b91c1c;">*</span></label>
                        <div class="input-wrap">
                            <input type="password" id="senha_atual" name="senha_atual" placeholder="Obrigatória para confirmar as alterações" autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="metadados-seguranca">
                        <span><i class="fa-solid fa-key"></i> Senha alterada em: {{ $cadastro->senha_alterada_em ? $cadastro->senha_alterada_em->format('d/m/Y \à\s H:i') : 'nunca alterada' }}</span>
                        <span><i class="fa-solid fa-envelope"></i> E-mail alterado em: {{ $cadastro->email_alterado_em ? $cadastro->email_alterado_em->format('d/m/Y \à\s H:i') : 'nunca alterado' }}</span>
                        <span><i class="fa-solid fa-shield-halved"></i> E-mail verificado em: {{ $cadastro->email_verificado_em ? $cadastro->email_verificado_em->format('d/m/Y \à\s H:i') : 'ainda não verificado' }}</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-salvar"><i class="fa-solid fa-floppy-disk"></i> Salvar alterações</button>
        </form>
    </main>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* --- MÁSCARAS --- */
            function aplicarMascara(input, mascara) {
                if (!input) return;
                input.addEventListener('input', function () {
                    let digitos = input.value.replace(/\D/g, '');
                    let resultado = '';
                    let posicaoDigito = 0;
                    for (let i = 0; i < mascara.length && posicaoDigito < digitos.length; i++) {
                        if (mascara[i] === '0') {
                            resultado += digitos[posicaoDigito];
                            posicaoDigito++;
                        } else {
                            resultado += mascara[i];
                        }
                    }
                    input.value = resultado;
                });
            }
            aplicarMascara(document.getElementById('cpf'), '000.000.000-00');
            aplicarMascara(document.getElementById('telefone'), '(00) 00000-0000');

            /* --- PRÉ-VISUALIZAÇÃO DA FOTO --- */
            document.getElementById('fotoInput').addEventListener('change', function (e) {
                var file = e.target.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (ev) {
                    var img = document.getElementById('avatarPreviewImg');
                    var icon = document.getElementById('avatarPreviewIcon');
                    img.src = ev.target.result;
                    img.style.display = 'block';
                    if (icon) icon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            });

            /* --- MOSTRAR/OCULTAR SENHA --- */
            document.querySelectorAll('.toggle-password').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const target = document.getElementById(btn.dataset.target);
                    if (!target) return;
                    const icon = btn.querySelector('i');
                    const isHidden = target.type === 'password';
                    target.type = isHidden ? 'text' : 'password';
                    icon.classList.toggle('fa-eye', !isHidden);
                    icon.classList.toggle('fa-eye-slash', isHidden);
                });
            });

            /* --- CPF só é aceito para maiores de 18 anos (checagem real é no servidor) --- */
            var campoCpf = document.getElementById('cpf');
            var campoNascimento = document.getElementById('data_nascimento');

            function alternarObrigatoriedadeNascimento() {
                var temCpf = campoCpf.value.replace(/\D/g, '').length > 0;
                campoNascimento.required = temCpf;
            }
            campoCpf.addEventListener('input', alternarObrigatoriedadeNascimento);
            alternarObrigatoriedadeNascimento();

            document.getElementById('formPerfil').addEventListener('submit', function (e) {
                var temCpf = campoCpf.value.replace(/\D/g, '').length > 0;
                if (temCpf && campoNascimento.value) {
                    var hoje = new Date();
                    var nascimento = new Date(campoNascimento.value);
                    var idade = hoje.getFullYear() - nascimento.getFullYear();
                    var aniversarioJaPassou = (hoje.getMonth() > nascimento.getMonth()) ||
                        (hoje.getMonth() === nascimento.getMonth() && hoje.getDate() >= nascimento.getDate());
                    if (!aniversarioJaPassou) idade--;

                    if (idade < 18) {
                        e.preventDefault();
                        alert('Só é permitido cadastrar CPF para maiores de 18 anos.');
                    }
                }
            });
        });
    </script>
@endpush
