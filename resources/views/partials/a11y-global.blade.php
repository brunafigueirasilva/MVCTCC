{{-- ==========================================================================
     ACESSIBILIDADE GLOBAL (WCAG 2.2) — incluído por partials/acessibilidade
     (layout interno, landing e telas avulsas) e pelas telas de login,
     cadastro e "esqueci a senha" (que têm painel próprio).

     Corrige de uma vez, em todas as páginas, o que se repetia em cada uma:
      - link "Pular para o conteúdo" e foco sempre visível (teclado);
      - modais: role/aria, foco vai pra dentro, Tab fica preso no modal,
        Esc fecha e o foco volta pro botão que abriu; modal fechado não
        recebe foco (inert);
      - ícones decorativos escondidos do leitor de tela e botões só-ícone
        com nome acessível;
      - campos sem rótulo associado, gráficos (canvas) com descrição,
        tabelas largas com rolagem horizontal própria no celular;
      - mensagens de sucesso/erro anunciadas pelo leitor de tela;
      - prefers-reduced-motion e campos que não dão zoom no iPhone.
     Nada aqui muda cores/layout do AgriFarm além do anel de foco.
     ========================================================================== --}}
@once
<style>
    /* Link de pular navegação: invisível até receber foco pelo Tab. */
    .a11y-pular {
        position: fixed;
        top: 8px;
        left: 8px;
        z-index: 100000;
        padding: 12px 18px;
        border-radius: 10px;
        background: #0e4a27;
        color: #ffffff;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        transform: translateY(-160%);
        transition: transform 0.15s ease;
    }
    .a11y-pular:focus { transform: translateY(0); }

    .a11y-sr-only {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    /* Foco visível em TUDO que é navegável por teclado. Âmbar (marrom do
       AgriFarm): contraste ≥ 3:1 tanto no branco quanto no verde escuro da
       drawer e no fundo do modo escuro. !important porque várias telas
       tinham "outline: none" nos campos/botões. */
    :focus-visible {
        outline: 3px solid #d97706 !important;
        outline-offset: 2px !important;
    }
    /* Campos de escrita: sem o anel âmbar — o foco é indicado só pela borda
       verde que cada tela já aplica no :focus (pedido do projeto). */
    input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):focus-visible,
    select:focus-visible,
    textarea:focus-visible {
        outline: none !important;
    }
    /* Reserva para campo cuja tela não define cor de foco: borda verde. */
    input:focus,
    select:focus,
    textarea:focus {
        border-color: #16a765;
    }
    body.high-contrast :focus-visible {
        outline: 3px solid #ffff00 !important;
        box-shadow: 0 0 0 6px #000000 !important;
    }
    /* Elementos que recebem foco só por script (títulos, contêineres). */
    [tabindex="-1"]:focus:not(:focus-visible) { outline: none; }

    /* Símbolo antes do título principal de cada página (mesmo ícone do item
       no menu). Decorativo: o texto do título continua sendo o nome lido. */
    .titulo-icone {
        display: inline-block;
        margin-inline-end: 0.35em;
        font-size: 0.85em;
    }
    .titulo-icone-verde { color: var(--verde-texto, #0e4a27); }
    body.high-contrast .titulo-icone { color: #ffff00 !important; }

    /* Palavra longa (e-mail, nome de ração) quebra em vez de estourar o card. */
    p, li, dd, dt, label, h1, h2, h3, h4, h5, h6 { overflow-wrap: break-word; }

    /* Mídia nunca maior que o contêiner (evita rolagem horizontal). */
    img, video, canvas, iframe { max-width: 100%; }
    img, video { height: auto; }

    /* Tabela larga: rola dentro do próprio quadro, sem quebrar a página. */
    .a11y-tabela-rolagem {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .a11y-tabela-rolagem:focus-visible { outline-offset: -3px !important; }

    @media (max-width: 767px) {
        /* iOS dá zoom automático em campo com fonte < 16px. */
        input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]),
        select,
        textarea {
            font-size: max(16px, 1em) !important;
        }
    }

    /* Movimento reduzido: tira animações/transições não essenciais. */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>

<script>
(function () {
    'use strict';

    // ------------------------------------------------------------------
    // Tema salvo aplicado ANTES do primeiro desenho (sem "piscar" claro
    // ao abrir uma página em modo escuro/alto contraste).
    // ------------------------------------------------------------------
    function aplicarTemaCedo() {
        try {
            var tema = localStorage.getItem('theme');
            if (tema === 'dark') document.body.classList.add('dark-theme');
            if (tema === 'contrast') document.body.classList.add('high-contrast');
        } catch (e) { /* armazenamento bloqueado: segue o padrão */ }
    }
    if (document.body) aplicarTemaCedo();

    var FOCAVEIS = 'a[href], area[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), iframe, [tabindex]:not([tabindex="-1"]), [contenteditable="true"]';

    function visivel(el) {
        if (!el || !el.getClientRects().length) return false;
        var s = getComputedStyle(el);
        return s.visibility !== 'hidden' && s.display !== 'none';
    }

    function textoLimpo(t) { return (t || '').replace(/\s+/g, ' ').trim(); }

    // ------------------------------------------------------------------
    // 1. Pular para o conteúdo + landmark principal
    // ------------------------------------------------------------------
    function conteudoPrincipal() {
        return document.querySelector('main, [role="main"]')
            || document.querySelector('#mainArea, #conteudo-principal, .main, .content, .container, .auth-container, .login-container, .register-container');
    }

    function prepararConteudo() {
        var alvo = conteudoPrincipal();
        if (!alvo) return;
        if (alvo.tagName !== 'MAIN' && !alvo.hasAttribute('role')) alvo.setAttribute('role', 'main');
        if (!alvo.id) alvo.id = 'conteudo-principal';
        if (!alvo.hasAttribute('tabindex')) alvo.setAttribute('tabindex', '-1');

        if (!document.querySelector('.a11y-pular')) {
            var link = document.createElement('a');
            link.href = '#' + alvo.id;
            link.className = 'a11y-pular';
            link.textContent = 'Pular para o conteúdo principal';
            link.addEventListener('click', function (e) {
                e.preventDefault();
                alvo.focus({ preventScroll: false });
                alvo.scrollIntoView();
            });
            document.body.insertBefore(link, document.body.firstChild);
        }
    }

    // ------------------------------------------------------------------
    // 2. Ícones e nomes acessíveis
    // ------------------------------------------------------------------
    var NOMES_POR_ICONE = [
        [/fa-(trash|trash-can|trash-alt)\b/, 'Excluir'],
        [/fa-(pen|pencil|pen-to-square|edit)\b/, 'Editar'],
        [/fa-(xmark|times|close)\b/, 'Fechar'],
        [/fa-(eye)\b/, 'Ver detalhes'],
        [/fa-(eye-slash)\b/, 'Ocultar'],
        [/fa-(plus)\b/, 'Adicionar'],
        [/fa-(minus)\b/, 'Diminuir'],
        [/fa-(magnifying-glass|search)\b/, 'Pesquisar'],
        [/fa-(filter|sliders)\b/, 'Filtros'],
        [/fa-(bars)\b/, 'Abrir menu'],
        [/fa-(bell)\b/, 'Notificações'],
        [/fa-(download|file-pdf|file-arrow-down)\b/, 'Baixar'],
        [/fa-(print)\b/, 'Imprimir'],
        [/fa-(gear|cog)\b/, 'Configurações'],
        [/fa-(chevron-left|arrow-left|angle-left)\b/, 'Anterior'],
        [/fa-(chevron-right|arrow-right|angle-right)\b/, 'Próximo'],
        [/fa-(chevron-down|angle-down|caret-down)\b/, 'Expandir'],
        [/fa-(rotate|arrows-rotate|refresh)\b/, 'Atualizar'],
        [/fa-(paper-plane)\b/, 'Enviar'],
        [/fa-(right-from-bracket|sign-out)\b/, 'Sair'],
        [/fa-(universal-access)\b/, 'Acessibilidade'],
        [/fa-(play)\b/, 'Reproduzir'],
        [/fa-(stop)\b/, 'Parar'],
        [/fa-(gas-pump|fill-drip)\b/, 'Abastecer'],
        [/fa-(circle-info|info)\b/, 'Informações'],
    ];

    function contextoDoItem(el) {
        // Nome do item (linha/card) ao qual o botão pertence — "Excluir lote
        // Lote A1" em vez de só "Excluir".
        var item = el.closest('tr, li, article, [class*="card"], [class*="-row"], [class*="item"]');
        if (!item) return '';
        var titulo = item.querySelector('h1, h2, h3, h4, h5, strong, [class*="nome"], [class*="titulo"], [class*="title"], td');
        var t = titulo && !titulo.contains(el) ? textoLimpo(titulo.textContent) : '';
        return t.length > 0 && t.length <= 60 ? t : '';
    }

    function nomearControle(el) {
        if (el.hasAttribute('aria-label') || el.hasAttribute('aria-labelledby')) return;
        if (textoLimpo(el.textContent)) return;
        if (el.querySelector('img[alt]:not([alt=""])')) return;

        var nome = el.getAttribute('title');
        if (!nome) {
            var icone = el.querySelector('i, svg, span[class*="fa-"]');
            var classes = icone ? (icone.getAttribute('class') || '') : '';
            for (var i = 0; i < NOMES_POR_ICONE.length; i++) {
                if (NOMES_POR_ICONE[i][0].test(classes)) { nome = NOMES_POR_ICONE[i][1]; break; }
            }
            if (nome && /^(Excluir|Editar|Ver detalhes|Abastecer)$/.test(nome)) {
                var ctx = contextoDoItem(el);
                if (ctx) nome += ': ' + ctx;
            }
        }
        if (nome) el.setAttribute('aria-label', nome);
    }

    function prepararIcones(raiz) {
        raiz.querySelectorAll('i[class*="fa-"], span[class*="fa-"]:empty, svg:not([role]):not([aria-label])').forEach(function (ic) {
            if (!ic.hasAttribute('aria-hidden') && !ic.hasAttribute('aria-label') && !ic.hasAttribute('role')) {
                ic.setAttribute('aria-hidden', 'true');
            }
        });
        raiz.querySelectorAll('button, a[href], [role="button"]').forEach(nomearControle);
        // Imagens sem alt: decorativas por padrão (alt vazio é melhor que
        // o leitor soletrar o nome do arquivo).
        raiz.querySelectorAll('img:not([alt])').forEach(function (img) { img.setAttribute('alt', ''); });
    }

    // ------------------------------------------------------------------
    // 3. Campos sem rótulo associado
    // ------------------------------------------------------------------
    var idSeq = 0;
    function novoId(prefixo) { idSeq += 1; return prefixo + '-' + idSeq; }

    function temNome(campo) {
        if (campo.hasAttribute('aria-label') || campo.hasAttribute('aria-labelledby')) return true;
        if (campo.labels && campo.labels.length) return true;
        return false;
    }

    // Padrão comum nas telas: <label>Nome</label> <div class="input-box">
    // <i></i><input></div> — o rótulo é IRMÃO do bloco do campo, não está
    // dentro dele. Sobe até 3 níveis procurando um <label> sem "for" logo
    // antes (sem outro campo no meio). Antes o campo caía no placeholder
    // ("(19) 99999 9999" como nome do telefone).
    function rotuloAnterior(campo) {
        var no = campo;
        for (var nivel = 0; nivel < 3 && no && no !== document.body; nivel++) {
            var irmao = no.previousElementSibling;
            while (irmao) {
                if (irmao.matches('label:not([for])')) return irmao.querySelector('input, select, textarea') ? null : irmao;
                if (irmao.matches('input, select, textarea') || irmao.querySelector('input:not([type="hidden"]), select, textarea')) break;
                irmao = irmao.previousElementSibling;
            }
            no = no.parentElement;
            if (no && no.querySelectorAll('input:not([type="hidden"]), select, textarea').length > 1) break;
        }
        return null;
    }

    function prepararCampos(raiz) {
        raiz.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), select, textarea').forEach(function (campo) {
            if (temNome(campo)) return;

            var anterior = rotuloAnterior(campo);
            if (anterior) {
                if (!anterior.id) anterior.id = novoId('rotulo');
                campo.setAttribute('aria-labelledby', anterior.id);
                return;
            }

            // Rótulo visível no mesmo grupo, sem "for": associa.
            var grupo = campo.closest('.form-group, .input-box, .campo, .field, .control-item, .control-item-vertical, .form-row, div');
            var rotulo = grupo ? grupo.querySelector('label:not([for]), .control-label, .campo-label') : null;
            if (rotulo && !rotulo.contains(campo) && !rotulo.querySelector('input, select, textarea')) {
                if (!rotulo.id) rotulo.id = novoId('rotulo');
                campo.setAttribute('aria-labelledby', rotulo.id);
                return;
            }
            var texto = campo.getAttribute('placeholder') || campo.getAttribute('title') || campo.getAttribute('name');
            if (texto) campo.setAttribute('aria-label', texto.replace(/[_-]+/g, ' '));
        });

        // Campo obrigatório anunciado; erro do Laravel ligado ao campo.
        raiz.querySelectorAll('[required]').forEach(function (c) { c.setAttribute('aria-required', 'true'); });
    }

    // ------------------------------------------------------------------
    // 4. Gráficos (canvas) e tabelas
    // ------------------------------------------------------------------
    function tituloDoBloco(el) {
        var bloco = el.closest('.box, .card-grafico, .dashboard-card, [class*="card"], [class*="grafico"], section, article');
        var h = bloco ? bloco.querySelector('h1, h2, h3, h4, [class*="titulo"], [class*="title"]') : null;
        return h ? textoLimpo(h.textContent) : '';
    }

    function prepararGraficosETabelas(raiz) {
        raiz.querySelectorAll('canvas').forEach(function (cv) {
            if (cv.hasAttribute('aria-label') || cv.hasAttribute('aria-labelledby')) return;
            var t = tituloDoBloco(cv);
            cv.setAttribute('role', 'img');
            cv.setAttribute('aria-label', t ? 'Gráfico: ' + t : 'Gráfico');
        });

        raiz.querySelectorAll('table').forEach(function (tb) {
            // Tabela que já rola sozinha (overflow na própria tabela): precisa
            // receber foco para rolar pelo teclado.
            var oxTabela = getComputedStyle(tb).overflowX;
            if ((oxTabela === 'auto' || oxTabela === 'scroll') && !tb.hasAttribute('tabindex')) {
                tb.setAttribute('tabindex', '0');
            }
            if (tb.closest('.a11y-tabela-rolagem')) return;
            var pai = tb.parentElement;
            if (!pai) return;
            var ox = getComputedStyle(pai).overflowX;
            if (ox === 'auto' || ox === 'scroll') return;
            var caixa = document.createElement('div');
            caixa.className = 'a11y-tabela-rolagem';
            caixa.setAttribute('role', 'region');
            caixa.setAttribute('tabindex', '0');
            var cap = tb.querySelector('caption');
            caixa.setAttribute('aria-label', cap ? textoLimpo(cap.textContent) : (tituloDoBloco(tb) || 'Tabela'));
            pai.insertBefore(caixa, tb);
            caixa.appendChild(tb);
        });
    }

    // ------------------------------------------------------------------
    // 4b. Gráficos Chart.js: os DADOS também em texto (não só o título).
    //     Um <p> invisível ao lado do canvas, ligado por aria-describedby,
    //     com "rótulo: valor" de cada série — refeito a cada atualização do
    //     gráfico (troca de filtro/período, dados via fetch).
    // ------------------------------------------------------------------
    function formatarValor(v) {
        if (v && typeof v === 'object') v = v.y !== undefined ? v.y : (v.r !== undefined ? v.r : v.x);
        if (v === null || v === undefined || v === '') return null;
        return typeof v === 'number' ? v.toLocaleString('pt-BR', { maximumFractionDigits: 2 }) : String(v);
    }

    function descreverGrafico(chart) {
        try {
            var cv = chart.canvas;
            if (!cv || !document.contains(cv)) return;
            var rotulos = (chart.data && chart.data.labels) || [];
            var partes = [];
            (chart.data.datasets || []).forEach(function (ds, di) {
                if (chart.isDatasetVisible && !chart.isDatasetVisible(di)) return;
                var dados = ds.data || [];
                var itens = dados.slice(0, 31).map(function (v, i) {
                    var valor = formatarValor(v);
                    if (valor === null) return null;
                    var r = rotulos[i];
                    if (Array.isArray(r)) r = r.join(' ');
                    if ((r === undefined || r === null) && v && typeof v === 'object' && v.x !== undefined) r = v.x;
                    return (r !== undefined && r !== null && r !== '' ? r + ': ' : '') + valor;
                }).filter(Boolean);
                if (itens.length) partes.push((ds.label ? ds.label + ' — ' : '') + itens.join('; ') + (dados.length > 31 ? '; …' : ''));
            });
            var texto = 'Dados do gráfico: ' + (partes.length ? partes.join('. ') + '.' : 'sem dados para exibir.');

            var el = document.getElementById(cv.getAttribute('data-a11y-desc') || '');
            if (!el) {
                el = document.createElement('p');
                el.className = 'a11y-sr-only';
                el.id = novoId('dados-grafico');
                cv.setAttribute('data-a11y-desc', el.id);
                cv.insertAdjacentElement('afterend', el);
            }
            if (el.textContent !== texto) el.textContent = texto;
            var ids = (cv.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
            if (ids.indexOf(el.id) === -1) { ids.push(el.id); cv.setAttribute('aria-describedby', ids.join(' ')); }
        } catch (e) { /* gráfico sem dados no formato esperado: segue sem descrição */ }
    }

    function ligarGraficos() {
        var C = window.Chart;
        if (!C || C.__a11yDescricao) return;
        C.__a11yDescricao = true;
        try { C.register({ id: 'a11yDescricao', afterUpdate: descreverGrafico }); } catch (e) { /* versão sem register */ }
        Object.keys(C.instances || {}).forEach(function (k) { descreverGrafico(C.instances[k]); });
    }

    // ------------------------------------------------------------------
    // 5. Mensagens dinâmicas (sucesso/erro) anunciadas
    // ------------------------------------------------------------------
    function prepararMensagens(raiz) {
        raiz.querySelectorAll('.alerta-flash, .alert, .alert-success, .alert-danger, .alert-error, .alerta-sucesso, .alerta-erro, .mensagem-sucesso, .mensagem-erro, .msg-sucesso, .msg-erro, .erro, .erros, .sucesso, .error-message, .success-message').forEach(function (m) {
            if (m.hasAttribute('role') || m.closest('[role="alert"], [role="status"]')) return;
            var erro = /(erro|error|danger)/i.test(m.className);
            m.setAttribute('role', erro ? 'alert' : 'status');
        });
    }

    // ------------------------------------------------------------------
    // 6. Modais: foco, Tab preso, Esc, devolução do foco
    // ------------------------------------------------------------------
    var SEL_MODAL = '.modal, .modal-m, .modal-overlay:not(#accOverlay), .accessibility-modal, .alertas-modal, [role="dialog"], [aria-modal="true"]';
    var CLASSES_ABERTO = ['open', 'active', 'show', 'aberto', 'ativo', 'visible', 'is-open', 'mostrar'];
    var estado = new WeakMap();   // modal -> { aberto, origem }
    var pilha = [];

    function caixaDoModal(m) {
        return m.querySelector('.modal-card, .modal-m-card, .modal-content, .modal-box, .modal-dialog') || m;
    }

    function estaAberto(m) {
        // aria-hidden="true": já saindo (ex.: vinheta da landing durante a
        // animação de saída) — conta como fechado na hora.
        if (m.hidden || m.getAttribute('aria-hidden') === 'true') return false;
        var s = getComputedStyle(m);
        if (s.display === 'none' || s.visibility === 'hidden') return false;
        var porClasse = CLASSES_ABERTO.some(function (c) { return m.classList.contains(c); });
        // Modal que abre por classe ("open"/"active"...): vale só a classe.
        // A opacidade ainda está no meio da transição logo depois de fechar
        // e faria o modal parecer aberto (e o Esc o reabria).
        var st = estado.get(m);
        if (porClasse && st) st.usaClasse = true;
        if (st && st.usaClasse) return porClasse;
        return porClasse || parseFloat(s.opacity) > 0.5;
    }

    function ehModalVerdadeiro(m) {
        // Painéis não modais (ex.: chat da IA) só ganham Esc/foco, sem trava.
        return m.getAttribute('aria-modal') !== 'false';
    }

    function focaveisDe(el) {
        return Array.prototype.filter.call(el.querySelectorAll(FOCAVEIS), visivel);
    }

    function prepararModal(m) {
        if (estado.has(m)) return;
        // O próprio card de dentro de um .modal já é tratado pelo .modal pai.
        if (m.parentElement && m.parentElement.closest(SEL_MODAL) && caixaDoModal(m.parentElement.closest(SEL_MODAL)) === m) return;
        estado.set(m, { aberto: false, origem: null });

        var caixa = caixaDoModal(m);
        if (!caixa.hasAttribute('role')) caixa.setAttribute('role', 'dialog');
        if (!caixa.hasAttribute('aria-modal') && ehModalVerdadeiro(m)) caixa.setAttribute('aria-modal', 'true');
        if (!caixa.hasAttribute('aria-labelledby') && !caixa.hasAttribute('aria-label')) {
            var titulo = caixa.querySelector('h1, h2, h3, h4, .modal-top p, .modal-m-top p');
            if (titulo) {
                if (!titulo.id) titulo.id = novoId('titulo-modal');
                caixa.setAttribute('aria-labelledby', titulo.id);
            }
        }
        if (!caixa.hasAttribute('tabindex')) caixa.setAttribute('tabindex', '-1');
        sincronizarModal(m);
    }

    function sincronizarModal(m) {
        var st = estado.get(m);
        if (!st) return;
        var aberto = estaAberto(m);
        // Fechado: fora da ordem de Tab e da árvore de acessibilidade.
        if ('inert' in m) m.inert = !aberto;

        if (aberto && !st.aberto) {
            st.aberto = true;
            var ativo = document.activeElement;
            st.origem = ativo && ativo !== document.body && !m.contains(ativo) ? ativo : st.origem;
            pilha.push(m);
            // Dá um tempo pro script da página focar o campo dela, se quiser.
            setTimeout(function () {
                if (!estaAberto(m) || m.contains(document.activeElement)) return;
                var caixa = caixaDoModal(m);
                var alvo = caixa.querySelector('[autofocus]') || focaveisDe(caixa).filter(function (el) {
                    return !el.matches('.close, .modal-close, [class*="fechar"], [aria-label^="Fechar"]');
                })[0] || focaveisDe(caixa)[0] || caixa;
                try { alvo.focus({ preventScroll: true }); } catch (e) { alvo.focus(); }
            }, 60);
        } else if (!aberto && st.aberto) {
            st.aberto = false;
            pilha = pilha.filter(function (x) { return x !== m; });
            var dentro = m.contains(document.activeElement) || document.activeElement === document.body || !document.activeElement;
            if (dentro && st.origem && document.contains(st.origem) && visivel(st.origem)) {
                try { st.origem.focus({ preventScroll: true }); } catch (e) { st.origem.focus(); }
            }
        }
    }

    function sincronizarTodos() {
        document.querySelectorAll(SEL_MODAL).forEach(function (m) {
            prepararModal(m);
            sincronizarModal(m);
        });
    }

    function modalNoTopo() {
        for (var i = pilha.length - 1; i >= 0; i--) {
            if (estaAberto(pilha[i])) return pilha[i];
        }
        return null;
    }

    document.addEventListener('keydown', function (e) {
        var m = modalNoTopo();
        if (!m) return;

        if (e.key === 'Tab' && ehModalVerdadeiro(m)) {
            var lista = focaveisDe(caixaDoModal(m));
            if (!lista.length) { e.preventDefault(); return; }
            var primeiro = lista[0];
            var ultimo = lista[lista.length - 1];
            var ativo = document.activeElement;
            if (!caixaDoModal(m).contains(ativo)) { e.preventDefault(); primeiro.focus(); return; }
            if (e.shiftKey && ativo === primeiro) { e.preventDefault(); ultimo.focus(); }
            else if (!e.shiftKey && ativo === ultimo) { e.preventDefault(); primeiro.focus(); }
        }

        if (e.key === 'Escape') {
            // Depois dos scripts da própria página: se ela não fechou, fecha
            // pelo botão de fechar do modal.
            setTimeout(function () {
                if (!estaAberto(m)) return;
                var fechar = m.querySelector('.close, .modal-close, .ai-chat-close, [data-close], .btn-fechar, .fechar, [aria-label^="Fechar"], .btn-cancelar, .btn-cancel');
                if (fechar) fechar.click();
                else m.classList.remove.apply(m.classList, CLASSES_ABERTO);
                sincronizarModal(m);
            }, 0);
        }
    });

    // ------------------------------------------------------------------
    // 7. Botões que abrem/fecham painéis: aria-expanded coerente
    // ------------------------------------------------------------------
    function prepararGatilhos() {
        var acc = document.getElementById('accTrigger');
        if (acc) {
            acc.setAttribute('aria-controls', 'accModal');
            acc.setAttribute('aria-haspopup', 'dialog');
        }
        var vlibras = document.querySelector('[vw-access-button]');
        if (vlibras && !vlibras.hasAttribute('aria-label')) {
            vlibras.setAttribute('role', 'button');
            vlibras.setAttribute('aria-label', 'VLibras: traduzir o conteúdo para Libras');
        }
        var wrapper = document.getElementById('vlibras-access-wrapper');
        if (wrapper && !wrapper.hasAttribute('aria-label')) wrapper.setAttribute('aria-label', 'VLibras: traduzir o conteúdo para Libras');
    }

    function atualizarExpandido() {
        var acc = document.getElementById('accTrigger');
        var accModal = document.getElementById('accModal');
        if (acc && accModal) acc.setAttribute('aria-expanded', estaAberto(accModal) ? 'true' : 'false');
        var sino = document.getElementById('notificationsBtn');
        var alertas = document.getElementById('alertasModal');
        if (sino && alertas) sino.setAttribute('aria-expanded', estaAberto(alertas) ? 'true' : 'false');
    }

    // ------------------------------------------------------------------
    // Inicialização + conteúdo que chega depois (fetch, modais montados)
    // ------------------------------------------------------------------
    function preparar(raiz) {
        prepararIcones(raiz);
        prepararCampos(raiz);
        prepararGraficosETabelas(raiz);
        prepararMensagens(raiz);
    }

    var agendado = false;
    var pendentes = [];
    function agendar(raiz) {
        pendentes.push(raiz);
        if (agendado) return;
        agendado = true;
        requestAnimationFrame(function () {
            agendado = false;
            var lista = pendentes.splice(0);
            lista.forEach(function (r) { if (document.contains(r)) preparar(r); });
            prepararGatilhos();
            sincronizarTodos();
            atualizarExpandido();
        });
    }

    function iniciar() {
        aplicarTemaCedo();
        prepararConteudo();
        preparar(document.body);
        prepararGatilhos();
        sincronizarTodos();
        atualizarExpandido();

        new MutationObserver(function (mutacoes) {
            var mudouModal = false;
            mutacoes.forEach(function (mu) {
                if (mu.type === 'childList') {
                    mu.addedNodes.forEach(function (n) { if (n.nodeType === 1) agendar(n); });
                } else if (mu.target.nodeType === 1 && (mu.target.matches(SEL_MODAL) || mu.target === document.body)) {
                    mudouModal = true;
                }
            });
            if (mudouModal) {
                sincronizarTodos();
                atualizarExpandido();
                // Fechamento com transição: confere de novo no fim dela.
                setTimeout(function () { sincronizarTodos(); atualizarExpandido(); }, 400);
            }
        }).observe(document.body, { subtree: true, childList: true, attributes: true, attributeFilter: ['class', 'style', 'hidden', 'open'] });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
    else iniciar();

    // Gráficos: registra o plugin antes dos scripts das páginas criarem os
    // gráficos (este listener é o primeiro do DOMContentLoaded) e confere de
    // novo no load, para gráficos criados antes do registro.
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ligarGraficos);
    else ligarGraficos();
    window.addEventListener('load', function () {
        ligarGraficos();
        var C = window.Chart;
        if (C) Object.keys(C.instances || {}).forEach(function (k) { descreverGrafico(C.instances[k]); });
    });
})();
</script>
@endonce
