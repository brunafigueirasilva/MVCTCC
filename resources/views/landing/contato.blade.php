<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | Contato</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

@include('partials.landing-nav', ['paginaAtiva' => 'contato'])

    <section id="contato">
        <div class="container">
            <span class="section-tag reveal-tag">Fale Conosco</span>
            <h2 class="section-title reveal-title">Entre em Contato</h2>
            <p style="text-align:center;color:var(--text-muted);font-size:1.05rem;margin-top:8px">
                Interessado em conhecer o AgriFarm? Preencha o formulário abaixo e retornaremos em breve.
            </p>
            <div class="form-container">
                <form id="contact-form" onsubmit="handleSubmit(event)">
                    <div class="form-grid">
                        <div class="form-field">
                            <label class="form-label">Nome Completo *</label>
                            <div class="form-input-wrap">
                                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <input type="text" name="name" class="form-input"
                                    placeholder="Seu nome completo" required />
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Endereço de E-mail *</label>
                            <div class="form-input-wrap">
                                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                <input type="email" name="email" class="form-input" placeholder="seu@email.com"
                                    required />
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Telefone / WhatsApp</label>
                            <div class="form-input-wrap">
                                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                                <input type="tel" name="phone" class="form-input"
                                    placeholder="(00) 00000-0000" />
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Empresa / Propriedade Rural</label>
                            <div class="form-input-wrap">
                                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                <input type="text" name="company" class="form-input"
                                    placeholder="Nome da empresa ou fazenda" />
                            </div>
                        </div>
                        <div class="form-field full">
                            <label class="form-label">Assunto *</label>
                            <div class="form-input-wrap">
                                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    style="top:16px;transform:none;position:absolute;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                                <select name="subject" class="form-select" style="padding-left:42px">
                                    <option value="" disabled selected>Selecione um assunto</option>
                                    <option value="demo">Solicitar Demonstração do Sistema</option>
                                    <option value="pricing">Informações sobre Planos e Preços</option>
                                    <option value="partnership">Proposta de Parceria Comercial</option>
                                    <option value="support">Suporte Técnico</option>
                                    <option value="investor">Interesse em Investimento</option>
                                    <option value="other">Outro Assunto</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-field full">
                            <label class="form-label">Mensagem *</label>
                            <textarea name="message" class="form-textarea" placeholder="Descreva detalhadamente sua necessidade ou dúvida..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <div class="privacy-note">
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Suas informações são tratadas com total confidencialidade e não serão compartilhadas com
                            terceiros.
                        </div>
                        <div id="form-success" class="form-success">
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Mensagem enviada com sucesso! Retornaremos em breve.
                        </div>
                        <button type="submit" id="btn-submit" class="btn-submit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Enviar Mensagem
                        </button>
                    </div>
                </form>
            </div>
            <p class="contato-disclaimer">
                Projeto Acadêmico — Curso de Desenvolvimento de Sistemas · SENAI<br>
                <span>Desenvolvido para fins educacionais e de inovação tecnológica no agronegócio.</span>
            </p>
        </div>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')
@include('partials.landing-scripts')

<script>
    // ─── ENVIO DO FORMULÁRIO DE CONTATO ───
    function handleSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-submit');
        const success = document.getElementById('form-success');
        btn.classList.add('loading');
        btn.innerHTML =
            '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:18px;height:18px;animation:spin 1s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg> Enviando...';
        setTimeout(() => {
            btn.style.display = 'none';
            success.classList.add('show');
            document.getElementById('contact-form').reset();
            setTimeout(() => {
                success.classList.remove('show');
                btn.style.display = 'flex';
                btn.classList.remove('loading');
                btn.innerHTML =
                    '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg> Enviar Mensagem';
            }, 5000);
        }, 1500);
    }

    document.head.insertAdjacentHTML('beforeend', '<style>@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>');
</script>
