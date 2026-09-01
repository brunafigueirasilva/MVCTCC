<!-- ==========================================================================
     ASSISTENTE IA (COMPONENTE PADRONIZADO E REUTILIZÁVEL)
     Inclua com @@include('partials.assistente-ia') em qualquer página
     autenticada. Autossuficiente (CSS + HTML + JS) — não depende de
     variáveis de tema da página que o inclui, então funciona igual em
     qualquer tela, com qualquer paleta. Chama POST /assistente-ia/perguntar
     (Google Gemini 3.6 Flash no back-end).
     ========================================================================== -->
<style>
    .ai-chat-trigger {
        position: fixed;
        right: 24px;
        bottom: 24px;
        width: 58px;
        height: 58px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.375rem;
        color: #ffffff;
        background: linear-gradient(145deg, #1e5c3a, #0f3020);
        box-shadow: 0 10px 24px -6px rgba(10, 36, 22, 0.6), inset 0 1px 2px rgba(255, 255, 255, 0.15), inset 0 -3px 5px rgba(0, 0, 0, 0.25);
        cursor: pointer;
        z-index: 1010;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ai-chat-trigger:hover { transform: translateY(-2px) scale(1.04); box-shadow: 0 14px 30px -6px rgba(10, 36, 22, 0.65), inset 0 1px 2px rgba(255, 255, 255, 0.15), inset 0 -3px 5px rgba(0, 0, 0, 0.25); }
    .ai-chat-trigger.aberto i { transform: rotate(90deg); }
    .ai-chat-trigger i { transition: transform 0.2s ease; }

    body.high-contrast .ai-chat-trigger { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; box-shadow: none !important; }

    .ai-chat-panel {
        position: fixed;
        right: 24px;
        bottom: 94px;
        width: min(360px, calc(100vw - 48px));
        height: min(520px, calc(100vh - 140px));
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
        z-index: 1009;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        transform: translateY(16px) scale(0.97);
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .ai-chat-panel.aberto { opacity: 1; transform: translateY(0) scale(1); pointer-events: auto; }

    body.dark-theme .ai-chat-panel { background: #12251a; }
    body.high-contrast .ai-chat-panel { background: #000000 !important; border: 2px solid #ffff00 !important; box-shadow: none !important; }

    .ai-chat-header {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 16px;
        background: linear-gradient(135deg, #1a4d32 0%, #123f22 55%, #0a2416 100%);
        color: #ffffff;
        flex-shrink: 0;
        overflow: hidden;
    }

    /* Acabamento decorativo sutil (traços dourados, tema "campo") — só
       textura de fundo, sempre atrás do conteúdo real do cabeçalho. */
    .ai-chat-header::after {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0.16;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 160 90' fill='none' stroke='%23d4af7a' stroke-width='1.6'%3E%3Cpath d='M96 78V45l16-10 16 10v33'/%3E%3Cpath d='M96 78h32'/%3E%3Cpath d='M112 35v-9M107 30l5-5 5 5'/%3E%3Ccircle cx='140' cy='30' r='9'/%3E%3Cpath d='M140 39v39M133 78h14'/%3E%3Cpath d='M8 82c14-22 34-22 48 0'/%3E%3Cpath d='M32 82V52M32 52l-9 10M32 58l11 12'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right -6px bottom -10px;
        background-size: 200px 112px;
    }

    .ai-chat-header-info { position: relative; display: flex; align-items: center; gap: 10px; min-width: 0; z-index: 1; }

    .ai-chat-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.16);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.0625rem;
    }

    .ai-chat-header-info strong { display: block; font-size: 0.9375rem; font-weight: 800; letter-spacing: 0.2px; }
    .ai-chat-subtitle { display: block; font-size: 0.6875rem; color: #d9ede1; margin-top: 1px; }

    .ai-chat-close {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.14);
        border: none;
        color: #ffffff;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        cursor: pointer;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ai-chat-close:hover { background: rgba(255, 255, 255, 0.26); }

    body.high-contrast .ai-chat-header { background: #000000 !important; border-bottom: 2px solid #ffff00 !important; }
    body.high-contrast .ai-chat-header::after { display: none; }
    body.high-contrast .ai-chat-header-info strong,
    body.high-contrast .ai-chat-subtitle { color: #ffff00 !important; }
    body.high-contrast .ai-chat-close { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }

    .ai-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f8fafc;
    }

    body.dark-theme .ai-chat-messages { background: #0a1810; }
    body.high-contrast .ai-chat-messages { background: #000000 !important; }

    .ai-chat-msg {
        max-width: 84%;
        padding: 9px 12px;
        border-radius: 12px;
        font-size: 0.8125rem;
        line-height: 1.5;
        white-space: pre-wrap;
        overflow-wrap: break-word;
    }

    .ai-chat-msg-bot {
        align-self: flex-start;
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 3px;
    }

    body.dark-theme .ai-chat-msg-bot { background: #16281c; color: #f1f9f4; border-color: #2a4d38; }
    body.high-contrast .ai-chat-msg-bot { background: #000000 !important; color: #ffffff !important; border: 2px solid #ffff00 !important; }

    .ai-chat-msg-user {
        align-self: flex-end;
        background: linear-gradient(135deg, #4ade80, #059669);
        color: #ffffff;
        border-bottom-right-radius: 3px;
    }

    body.high-contrast .ai-chat-msg-user { background: #000000 !important; color: #ffff00 !important; border: 2px solid #ffff00 !important; }

    .ai-chat-msg-erro { align-self: flex-start; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; border-bottom-left-radius: 3px; }

    .ai-chat-msg-typing { align-self: flex-start; display: flex; gap: 4px; padding: 12px 14px; }
    .ai-chat-msg-typing span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #94a3b8;
        animation: aiChatTypingBounce 1.1s ease-in-out infinite;
    }
    .ai-chat-msg-typing span:nth-child(2) { animation-delay: 0.15s; }
    .ai-chat-msg-typing span:nth-child(3) { animation-delay: 0.3s; }

    @keyframes aiChatTypingBounce {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
        30% { transform: translateY(-4px); opacity: 1; }
    }

    .ai-chat-form {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid #e2e8f0;
        background: #ffffff;
        flex-shrink: 0;
    }

    body.dark-theme .ai-chat-form { background: #12251a; border-top-color: #2a4d38; }
    body.high-contrast .ai-chat-form { background: #000000 !important; border-top: 2px solid #ffff00 !important; }

    .ai-chat-form input {
        flex: 1;
        min-width: 0;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 0.8125rem;
        font-family: inherit;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
    }

    .ai-chat-form input:focus { border-color: #4ade80; }

    body.dark-theme .ai-chat-form input { background: #0a1810; border-color: #2a4d38; color: #f1f9f4; }
    body.high-contrast .ai-chat-form input { background: #000000 !important; color: #ffffff !important; border: 2px solid #ffff00 !important; }

    .ai-chat-send {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        flex-shrink: 0;
        background: linear-gradient(145deg, #4ade80, #059669);
        color: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
    }

    .ai-chat-send:disabled { opacity: 0.55; cursor: default; }
    body.high-contrast .ai-chat-send { background: #000000 !important; border: 2px solid #ffff00 !important; color: #ffff00 !important; }

    @media (max-width: 480px) {
        .ai-chat-trigger { right: 16px; bottom: 16px; }
        .ai-chat-panel { right: 16px; bottom: 84px; }
    }
</style>

<button type="button" class="ai-chat-trigger" id="aiChatTrigger" aria-label="Abrir assistente de IA" aria-expanded="false">
    <i class="fa-solid fa-robot" aria-hidden="true"></i>
</button>

<div class="ai-chat-panel" id="aiChatPanel" role="dialog" aria-modal="false" aria-labelledby="aiChatTitle" hidden>
    <div class="ai-chat-header">
        <div class="ai-chat-header-info">
            <span class="ai-chat-avatar"><i class="fa-solid fa-robot" aria-hidden="true"></i></span>
            <div>
                <strong id="aiChatTitle">Assistente AgriFarm</strong>
                <span class="ai-chat-subtitle">Tire dúvidas sobre o sistema</span>
            </div>
        </div>
        <button type="button" class="ai-chat-close" id="aiChatClose" aria-label="Fechar assistente">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    <div class="ai-chat-messages" id="aiChatMessages">
        <div class="ai-chat-msg ai-chat-msg-bot">
            Olá! Sou o assistente do AgriFarm. Pergunte algo sobre como usar o sistema — rações, lotes, plano alimentar, relatórios e mais.
        </div>
    </div>

    <form class="ai-chat-form" id="aiChatForm" autocomplete="off">
        <input type="text" id="aiChatInput" placeholder="Digite sua pergunta..." maxlength="2000" aria-label="Sua pergunta">
        <button type="submit" class="ai-chat-send" id="aiChatSend" aria-label="Enviar pergunta">
            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
        </button>
    </form>
</div>

<script>
    (function () {
        const trigger = document.getElementById('aiChatTrigger');
        const panel = document.getElementById('aiChatPanel');
        const closeBtn = document.getElementById('aiChatClose');
        const form = document.getElementById('aiChatForm');
        const input = document.getElementById('aiChatInput');
        const sendBtn = document.getElementById('aiChatSend');
        const mensagens = document.getElementById('aiChatMessages');
        const csrfToken = '{{ csrf_token() }}';
        const urlPerguntar = '{{ route('assistenteIa.perguntar') }}';

        // Histórico só em memória (some ao recarregar a página) — nada é
        // persistido no banco nem no localStorage.
        let historico = [];
        let carregando = false;

        function alternarPainel(abrir) {
            const vaiAbrir = abrir ?? panel.hidden;
            panel.hidden = !vaiAbrir;
            requestAnimationFrame(() => panel.classList.toggle('aberto', vaiAbrir));
            trigger.classList.toggle('aberto', vaiAbrir);
            trigger.setAttribute('aria-expanded', vaiAbrir ? 'true' : 'false');
            if (vaiAbrir) input.focus();
        }

        trigger.addEventListener('click', () => alternarPainel());
        closeBtn.addEventListener('click', () => alternarPainel(false));

        document.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape' && !panel.hidden) alternarPainel(false);
        });

        // Escapa HTML antes de qualquer coisa — a resposta vem de um LLM,
        // então nunca é HTML confiável (evita XSS caso o texto contenha
        // algo como "<script>"). Só depois de escapado é que os marcadores
        // de negrito viram <strong> de verdade.
        function escaparHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto;
            return div.innerHTML;
        }

        function formatarRespostaAssistente(texto) {
            return escaparHtml(texto)
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<strong>$1</strong>');
        }

        function adicionarMensagem(texto, classe) {
            const bolha = document.createElement('div');
            bolha.className = 'ai-chat-msg ' + classe;

            // Só a resposta do assistente pode vir com marcação (**negrito**
            // do Gemini) — a pergunta do próprio usuário e mensagens de erro
            // ficam em texto puro, sem interpretar nada.
            if (classe === 'ai-chat-msg-bot') {
                bolha.innerHTML = formatarRespostaAssistente(texto);
            } else {
                bolha.textContent = texto;
            }

            mensagens.appendChild(bolha);
            mensagens.scrollTop = mensagens.scrollHeight;
            return bolha;
        }

        function mostrarDigitando() {
            const bolha = document.createElement('div');
            bolha.className = 'ai-chat-msg-typing';
            bolha.innerHTML = '<span></span><span></span><span></span>';
            mensagens.appendChild(bolha);
            mensagens.scrollTop = mensagens.scrollHeight;
            return bolha;
        }

        form.addEventListener('submit', async function (evento) {
            evento.preventDefault();
            const pergunta = input.value.trim();
            if (!pergunta || carregando) return;

            adicionarMensagem(pergunta, 'ai-chat-msg-user');
            input.value = '';
            carregando = true;
            sendBtn.disabled = true;
            const indicador = mostrarDigitando();

            try {
                const resposta = await fetch(urlPerguntar, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ mensagem: pergunta, historico: historico })
                });

                const dados = await resposta.json().catch(() => null);
                indicador.remove();

                if (resposta.ok && dados?.resposta) {
                    adicionarMensagem(dados.resposta, 'ai-chat-msg-bot');
                    historico.push({ papel: 'usuario', texto: pergunta });
                    historico.push({ papel: 'assistente', texto: dados.resposta });
                    if (historico.length > 12) historico = historico.slice(-12);
                } else {
                    adicionarMensagem(dados?.erro || 'Não foi possível falar com o assistente agora.', 'ai-chat-msg-erro');
                }
            } catch (erro) {
                indicador.remove();
                adicionarMensagem('Falha de conexão. Verifique sua internet e tente de novo.', 'ai-chat-msg-erro');
            } finally {
                carregando = false;
                sendBtn.disabled = false;
                input.focus();
            }
        });
    })();
</script>
