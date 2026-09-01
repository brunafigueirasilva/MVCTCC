{{--
    Cabeçalho público (barra de contato + navbar) compartilhado por todas
    as páginas da landing page (Início, Problema, Solução, Como Funciona,
    Benefícios, FAQ, Sobre, Contato, Avaliações) — cada seção virou uma
    página própria, então esse cabeçalho fica idêntico em todas, com o
    link da página atual marcado como ativo via a variável $paginaAtiva
    (ex.: 'problema'). A navbar usa o mesmo estilo/altura/cores da navbar
    padrão das telas internas autenticadas (dashboard.blade.php etc.).

    Precisa ser incluído dentro de um <body> que já tenha o <style> completo
    da landing page (reset, tokens, CSS de .topbar/nav) — ver
    resources/views/landing/*.blade.php.
--}}
<div class="topbar">
    <div class="topbar-inner">
        <div class="topbar-contact">
            <a href="tel:+5519994010744" class="topbar-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
                +55 (19) 99401-0744
            </a>
            <a href="mailto:contato@agrifarm.com.br" class="topbar-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                contato@agrifarm.com.br
            </a>
        </div>
        <div class="topbar-social" role="navigation" aria-label="Redes sociais">
            <a href="https://facebook.com/agrifarm" target="_blank" rel="noopener noreferrer" aria-label="Facebook do AgriFarm (abre em nova aba)">
                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
            </a>
            <a href="https://instagram.com/agrifarm" target="_blank" rel="noopener noreferrer" aria-label="Instagram do AgriFarm (abre em nova aba)">
                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
            </a>
            <a href="https://linkedin.com/company/agrifarm" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn do AgriFarm (abre em nova aba)">
                <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
            </a>
            <a href="https://wa.me/5511999999999" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp do AgriFarm (abre em nova aba)">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav>
    <div class="nav-inner">
        <a href="{{ route('projeto.index') }}" class="logo">
            <img src="{{ asset('agrifarm-removebg-preview.png') }}" alt="AgriFarm" />
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('landing.problema') }}" class="{{ $paginaAtiva === 'problema' ? 'active' : '' }}">Problema</a></li>
            <li><a href="{{ route('landing.solucao') }}" class="{{ $paginaAtiva === 'solucao' ? 'active' : '' }}">Solução</a></li>
            <li><a href="{{ route('landing.comoFunciona') }}" class="{{ $paginaAtiva === 'como-funciona' ? 'active' : '' }}">Como Funciona</a></li>
            <li><a href="{{ route('landing.beneficios') }}" class="{{ $paginaAtiva === 'beneficios' ? 'active' : '' }}">Benefícios</a></li>
            <li><a href="{{ route('landing.sobre') }}" class="{{ $paginaAtiva === 'sobre' ? 'active' : '' }}">Sobre</a></li>
            <li><a href="{{ route('landing.contato') }}" class="{{ $paginaAtiva === 'contato' ? 'active' : '' }}">Contato</a></li>
            <li><a href="{{ route('comentario.listar') }}" class="{{ $paginaAtiva === 'avaliacoes' ? 'active' : '' }}">Avaliações</a></li>
        </ul>
        <div class="nav-right">
            <a href="{{ route('login.index') }}" class="btn-nav"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Acessar Sistema</a>
            <!-- O botão de acessibilidade (partials/acessibilidade.blade.php)
                 é movido pra dentro deste slot via JS, ficando na ponta
                 direita da navbar em vez de flutuar no canto da tela. -->
            <div id="navAccessibilitySlot"></div>
            <button class="hamburger" onclick="toggleMenu()" aria-label="Menu">
                <svg id="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
    <div class="mobile-menu" id="mobile-menu">
        <a href="{{ route('landing.problema') }}" onclick="closeMenu()">Problema</a>
        <a href="{{ route('landing.solucao') }}" onclick="closeMenu()">Solução</a>
        <a href="{{ route('landing.comoFunciona') }}" onclick="closeMenu()">Como Funciona</a>
        <a href="{{ route('landing.beneficios') }}" onclick="closeMenu()">Benefícios</a>
        <a href="{{ route('landing.sobre') }}" onclick="closeMenu()">Sobre</a>
        <a href="{{ route('landing.contato') }}" onclick="closeMenu()">Contato</a>
        <a href="{{ route('comentario.listar') }}" onclick="closeMenu()">Avaliações</a>
    </div>
</nav>
