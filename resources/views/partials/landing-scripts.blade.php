{{--
    Comportamento compartilhado por todas as páginas da landing page: menu
    mobile, botão "voltar ao topo", animação de revelação de títulos/cards
    ao entrar na tela, mover o botão de acessibilidade pra dentro da
    navbar, e o interruptor Modo Escuro/Tema Claro. A navbar em si é fixa e
    sempre visível (mesmo comportamento da navbar padrão do sistema — não
    esconde ao rolar). Inclua depois de partials.landing-footer e antes do
    fechamento de </body> (depois de partials.acessibilidade e
    partials.assistente-ia, que cada página já inclui por conta própria).
--}}
<!-- BOTÃO VOLTAR AO TOPO -->
<button type="button" id="backToTop" class="back-to-top" aria-label="Voltar ao topo da página">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
    </svg>
</button>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ─── BOTÃO VOLTAR AO TOPO ───
        const backToTop = document.getElementById('backToTop');

        window.addEventListener('scroll', () => {
            if (backToTop) {
                backToTop.classList.toggle('visible', window.scrollY > 500);
            }
        });

        if (backToTop) {
            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // ─── REVELAÇÃO DAS TAGS DE SEÇÃO E CARDS AO ENTRAR NA TELA ───
        // Títulos de seção ficam como texto simples, sem efeito letra por
        // letra — só a tag de seção aparece ao entrar na tela.
        const revealObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('revealed');
                    revealObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.2 });
        document.querySelectorAll('.reveal-tag').forEach(el => revealObs.observe(el));

        const prefersReducedMotion = document.documentElement.classList.contains('ui-movimento-reduzido');

        const cardObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    setTimeout(() => {
                        e.target.style.opacity = '1';
                        e.target.style.transform = 'translateY(0) rotateX(0deg)';
                        // Fim da entrada: devolve o controle ao CSS (elevação
                        // no hover), sem deixar transform/transição presos.
                        setTimeout(() => { e.target.style.transform = ''; e.target.style.opacity = ''; e.target.style.transition = ''; }, 650);
                    }, parseInt(e.target.dataset.delay || 0));
                    cardObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        // Movimento reduzido: os cards já aparecem prontos (sem fade).
        if (!prefersReducedMotion) document.querySelectorAll('.benefit-card, .diff-card, .step-card, .data-card, .member, .faq-item, .sol-feat, .mini-stat, .solucao-card, .impacto-box').forEach((el, i) => {
            // Navegação por Tab: o card (ou um botão dentro dele) que recebe
            // foco aparece na hora — antes o foco caía num card ainda
            // invisível (opacity 0) até a animação de entrada terminar.
            el.addEventListener('focusin', () => {
                el.style.transition = 'none';
                el.style.opacity = '1';
                el.style.transform = 'none';
                cardObs.unobserve(el);
            });
            el.style.opacity = '0';
            // Entrada com leve perspectiva 3D (rotateX) além do fade-up.
            el.style.transform = prefersReducedMotion ? 'none' : 'translateY(24px) rotateX(8deg)';
            el.style.transition = 'opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1)';
            el.dataset.delay = (i % 4) * 100;
            cardObs.observe(el);
        });

        // (Inclinação 3D dos cards que seguia o mouse removida: nenhuma
        // animação acompanha a posição do cursor. O hover continua só com
        // a elevação definida no CSS.)

        // ─── NAVBAR: ESTADO "ROLADO" ───
        const navEl = document.querySelector('nav');
        if (navEl) {
            const updateNavScrolled = () => navEl.classList.toggle('nav-scrolled', window.scrollY > 24);
            updateNavScrolled();
            window.addEventListener('scroll', updateNavScrolled, { passive: true });
        }

        // ─── HERO: PARALLAX DE SCROLL ───
        // Só existe na página inicial (projetovideo.blade.php) — as demais
        // páginas não têm #hero, então este bloco simplesmente não faz nada nelas.
        const heroEl = document.getElementById('hero');
        if (heroEl && !prefersReducedMotion) {
            const heroVideo = document.getElementById('hero-video');
            const heroGlow = heroEl.querySelector('.hero-glow');

            let ticking = false;
            const applyScrollParallax = () => {
                const offset = Math.min(window.scrollY, heroEl.offsetHeight) * 0.35;
                // Sem scale(): ampliar o vídeo deixava a imagem embaçada.
                if (heroVideo) heroVideo.style.transform = `translate3d(0, ${offset}px, 0)`;
                if (heroGlow) heroGlow.style.transform = `translateY(${offset * 0.6}px)`;
                ticking = false;
            };
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(applyScrollParallax);
                    ticking = true;
                }
            }, { passive: true });

            // (Parallax dos orbs que seguia o mouse removido — só a rolagem
            // move o fundo.)
        }

        // ─── CONTADOR ANIMADO NOS NÚMEROS DE DESTAQUE ───
        // Reaproveita o texto já existente (ex.: "20%", "65k+", "IoT") — extrai
        // o número líder via regex e anima só esses; textos sem número
        // (IoT/Cloud/BI) ficam intactos.
        const countSelector = '.stat-value, .data-card .num, .mini-stat .v, .sol-stat .v';
        const countObs = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                countObs.unobserve(entry.target);
                const el = entry.target;
                const raw = el.textContent.trim();
                const match = raw.match(/^(\d+(?:[.,]\d+)?)/);
                if (!match) return;
                if (prefersReducedMotion) return;
                const numberStr = match[1];
                const suffix = raw.slice(numberStr.length);
                const target = parseFloat(numberStr.replace(',', '.'));
                const decimals = numberStr.includes(',') || numberStr.includes('.') ? 1 : 0;
                const duration = 1400;
                const start = performance.now();
                const step = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const value = target * eased;
                    el.textContent = value.toFixed(decimals) + suffix;
                    if (progress < 1) {
                        requestAnimationFrame(step);
                    } else {
                        el.textContent = raw;
                    }
                };
                requestAnimationFrame(step);
            });
        }, { threshold: 0.4 });
        document.querySelectorAll(countSelector).forEach((el) => countObs.observe(el));

        // ─── BOTÃO DE ACESSIBILIDADE DENTRO DA NAVBAR ───
        // Esta página não tem .navbar-right (é uma nav própria de landing
        // page), então o auto-move padrão do painel de acessibilidade nunca
        // encontra destino. Move o botão pra dentro do slot no meio da
        // navbar (junto da logo) manualmente.
        const accTrigger = document.getElementById('accTrigger');
        const accSlot = document.getElementById('navAccessibilitySlot');
        if (accTrigger && accSlot) {
            accSlot.appendChild(accTrigger);
            accTrigger.classList.add('acc-trigger-inline');
        }

        // ─── MODO ESCURO / TEMA CLARO ───
        // "Modo Escuro" nesta página não abre um tema extra-escuro — o
        // padrão JÁ é o design original escuro (nenhuma regra CSS própria
        // pra ele). O interruptor vira um Escuro/Claro simples: marcado
        // (padrão, já vem assim) = design original; desmarcado = Tema Claro
        // (body.light-theme).
        //
        // Chave PRÓPRIA da landing page ("landingTheme"), separada da
        // "theme" que o painel de acessibilidade usa no sistema interno
        // (Dashboard etc.) — lá "light" é o PADRÃO (inclusive gravado
        // automaticamente pra quem nunca escolheu nada, ao sincronizar com
        // o servidor). Reaproveitar a mesma chave aqui fazia a landing page
        // inteira (incluindo o rodapé) cair no Tema Claro sempre que o
        // usuário só tivesse o padrão do sistema interno salvo — nunca
        // tinha escolhido "Tema Claro" nesta página de verdade. Resultado:
        // logo na primeira visita a página já abria fora do modo escuro
        // padrão, e olhava para o rodapé nesse estado do jeito errado (o
        // "Tema Claro" daqui ainda não cobre todo elemento do rodapé).
        const chkDarkMode = document.getElementById('chkDarkMode');
        const chkHighContrast = document.getElementById('chkHighContrast');
        if (chkDarkMode && chkHighContrast) {
            function aplicarTemaClaro(ativo) {
                document.body.classList.toggle('light-theme', ativo);
            }

            const salvo = localStorage.getItem('landingTheme');
            if (salvo === null) {
                chkDarkMode.checked = true;
            }
            aplicarTemaClaro(salvo === 'light');

            chkDarkMode.addEventListener('change', function () {
                const claro = !this.checked;
                aplicarTemaClaro(claro);
                localStorage.setItem('landingTheme', claro ? 'light' : 'dark');
            });

            chkHighContrast.addEventListener('change', function () {
                aplicarTemaClaro(!this.checked && !chkDarkMode.checked);
            });
        }
    });

    // ─── MENU MOBILE ───
    // aria-expanded/aria-label do ☰ acompanham o menu (leitor de tela
    // anuncia "expandido/recolhido"); Esc fecha e devolve o foco ao ☰.
    function sincronizarBotaoMenu(aberto) {
        const botao = document.querySelector('.hamburger');
        if (!botao) return;
        botao.setAttribute('aria-expanded', aberto ? 'true' : 'false');
        botao.setAttribute('aria-label', aberto ? 'Fechar menu' : 'Abrir menu');
    }

    function toggleMenu() {
        const aberto = document.getElementById('mobile-menu').classList.toggle('open');
        sincronizarBotaoMenu(aberto);
    }

    function closeMenu() {
        document.getElementById('mobile-menu').classList.remove('open');
        sincronizarBotaoMenu(false);
    }

    document.addEventListener('keydown', function (e) {
        const menu = document.getElementById('mobile-menu');
        if (e.key !== 'Escape' || !menu || !menu.classList.contains('open')) return;
        const focoNoMenu = menu.contains(document.activeElement);
        closeMenu();
        if (focoNoMenu) document.querySelector('.hamburger')?.focus();
    });
</script>

<!-- VLibras: widget oficial do governo federal que traduz o conteúdo da
     página para Libras (Língua Brasileira de Sinais). O botão próprio do
     widget fica preso na borda direita da tela — reforçamos a
     posição/z-index aqui pra ele nunca ficar por cima do botão "voltar ao
     topo" nem do painel de acessibilidade, que também disputam a mesma
     área da tela. -->
<style>
    div[vw] .vw-access-button {
        z-index: 997;
        right: 10px !important;
        bottom: 90px !important;
        top: auto !important;
    }

    div[vw] .vw-plugin-top-wrapper {
        z-index: 1006;
    }
</style>
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
{{-- Assíncrono: não segura o DOMContentLoaded da página esperando o CDN do governo (scripts async ainda terminam antes do "load", que o widget usa). Se o CDN falhar, segue sem o widget. --}}
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js" async onload="if (window.VLibras) new window.VLibras.Widget('https://vlibras.gov.br/app');"></script>
