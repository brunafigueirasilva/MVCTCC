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

        // ─── REVELAÇÃO DE TÍTULOS (efeito letra por letra) E CARDS AO ENTRAR NA TELA ───
        function splitChars(el) {
            // Cada letra vira um <span class="char"> pra animar (charRise/
            // charGlow), mas as letras de uma mesma palavra ficam agrupadas
            // num wrapper "word-nowrap" — sem isso, o navegador trata cada
            // span de letra como uma caixa própria e pode quebrar a linha
            // no meio de uma palavra (ex.: "AgriFarm" virando "A" / "griFarm"
            // em telas estreitas). Espaços entre palavras continuam como
            // texto normal, então a quebra de linha entre palavras keeps
            // funcionando normalmente.
            const wrap = (node) => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const frag = document.createDocumentFragment();
                    const tokens = node.textContent.match(/\S+|\s+/g) || [];
                    tokens.forEach((token) => {
                        if (/^\s+$/.test(token)) {
                            frag.appendChild(document.createTextNode(token));
                            return;
                        }
                        const wordWrap = document.createElement('span');
                        wordWrap.className = 'word-nowrap';
                        for (const char of token) {
                            const s = document.createElement('span');
                            s.className = 'char';
                            s.textContent = char;
                            wordWrap.appendChild(s);
                        }
                        frag.appendChild(wordWrap);
                    });
                    node.parentNode.replaceChild(frag, node);
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    Array.from(node.childNodes).forEach(wrap);
                }
            };
            Array.from(el.childNodes).forEach(wrap);
        }

        function animateTitle(el) {
            el.querySelectorAll('.char').forEach((c, i) => {
                c.style.animationDelay = (i * 0.028) + 's';
            });
            el.classList.add('revealed');
        }
        document.querySelectorAll('.section-title.reveal-title').forEach(el => splitChars(el));

        const revealObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.contains('reveal-title') ? animateTitle(e.target) : e.target.classList.add('revealed');
                    revealObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.2 });
        document.querySelectorAll('.reveal-title, .reveal-tag').forEach(el => revealObs.observe(el));

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const hasFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        const tiltSelector = '.benefit-card, .diff-card, .step-card, .data-card, .member, .faq-item, .sol-feat, .mini-stat, .stat-card, .solucao-card, .impacto-box';

        const cardObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    setTimeout(() => {
                        e.target.style.opacity = '1';
                        e.target.style.transform = 'translateY(0) rotateX(0deg)';
                    }, parseInt(e.target.dataset.delay || 0));
                    cardObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.benefit-card, .diff-card, .step-card, .data-card, .member, .faq-item, .sol-feat, .mini-stat, .solucao-card, .impacto-box').forEach((el, i) => {
            el.style.opacity = '0';
            // Entrada com leve perspectiva 3D (rotateX) além do fade-up já
            // existente — some assim que o tilt (abaixo) assume o controle
            // do transform no primeiro mousemove sobre o card.
            el.style.transform = prefersReducedMotion ? 'none' : 'translateY(24px) rotateX(8deg)';
            el.style.transition = 'opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1)';
            el.dataset.delay = (i % 4) * 100;
            cardObs.observe(el);
        });

        // ─── TILT 3D NOS CARDS (mouse) ───
        // Só liga com mouse de verdade (não touch) e sem "prefers-reduced-motion".
        // Reavalia body.high-contrast a cada movimento (o usuário pode ligar o
        // Alto Contraste em tempo real pelo painel, sem recarregar a página).
        if (hasFinePointer && !prefersReducedMotion) {
            document.querySelectorAll(tiltSelector).forEach((card) => {
                card.addEventListener('mousemove', (e) => {
                    if (document.body.classList.contains('high-contrast')) return;
                    const rect = card.getBoundingClientRect();
                    const px = (e.clientX - rect.left) / rect.width - 0.5;
                    const py = (e.clientY - rect.top) / rect.height - 0.5;
                    const rotY = px * 10;
                    const rotX = py * -10;
                    card.style.transform = `perspective(900px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-4px) scale(1.015)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                });
            });
        }

        // ─── NAVBAR: ESTADO "ROLADO" ───
        const navEl = document.querySelector('nav');
        if (navEl) {
            const updateNavScrolled = () => navEl.classList.toggle('nav-scrolled', window.scrollY > 24);
            updateNavScrolled();
            window.addEventListener('scroll', updateNavScrolled, { passive: true });
        }

        // ─── HERO: PARALLAX DE SCROLL + MOUSE-PARALLAX NOS ORBS ───
        // Só existe na página inicial (projetovideo.blade.php) — as demais
        // páginas não têm #hero, então este bloco simplesmente não faz nada nelas.
        const heroEl = document.getElementById('hero');
        if (heroEl && !prefersReducedMotion) {
            const heroVideo = document.getElementById('hero-video');
            const heroGlow = heroEl.querySelector('.hero-glow');

            let ticking = false;
            const applyScrollParallax = () => {
                const offset = Math.min(window.scrollY, heroEl.offsetHeight) * 0.35;
                if (heroVideo) heroVideo.style.transform = `translateY(${offset}px) scale(1.08)`;
                if (heroGlow) heroGlow.style.transform = `translateY(${offset * 0.6}px)`;
                ticking = false;
            };
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(applyScrollParallax);
                    ticking = true;
                }
            }, { passive: true });

            if (hasFinePointer) {
                heroEl.addEventListener('mousemove', (e) => {
                    const rect = heroEl.getBoundingClientRect();
                    const mx = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
                    const my = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
                    heroEl.style.setProperty('--mx', mx.toFixed(3));
                    heroEl.style.setProperty('--my', my.toFixed(3));
                });
                heroEl.addEventListener('mouseleave', () => {
                    heroEl.style.setProperty('--mx', 0);
                    heroEl.style.setProperty('--my', 0);
                });
            }
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
        // (body.light-theme). Reaproveita a mesma chave "theme" que o
        // painel compartilhado já salva no localStorage ('dark'/'light'/'contrast').
        const chkDarkMode = document.getElementById('chkDarkMode');
        const chkHighContrast = document.getElementById('chkHighContrast');
        if (chkDarkMode && chkHighContrast) {
            function aplicarTemaClaro(ativo) {
                document.body.classList.toggle('light-theme', ativo);
            }

            const salvo = localStorage.getItem('theme');
            if (salvo === null) {
                chkDarkMode.checked = true;
            }
            aplicarTemaClaro(salvo === 'light');

            chkDarkMode.addEventListener('change', function () {
                aplicarTemaClaro(!this.checked);
            });

            chkHighContrast.addEventListener('change', function () {
                aplicarTemaClaro(!this.checked && !chkDarkMode.checked);
            });
        }
    });

    // ─── MENU MOBILE ───
    function toggleMenu() {
        document.getElementById('mobile-menu').classList.toggle('open');
    }

    function closeMenu() {
        document.getElementById('mobile-menu').classList.remove('open');
    }
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
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
