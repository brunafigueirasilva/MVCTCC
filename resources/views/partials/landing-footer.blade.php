{{--
    Rodapé compartilhado por todas as páginas da landing page — mesmo
    motivo do partials/landing-nav.blade.php: cada seção virou página
    própria, então o rodapé (e o link ativo do menu, ali) precisa ficar
    idêntico em todas em vez de duplicado e podendo divergir.
--}}
</main>{{-- aberto no fim de partials/landing-nav --}}
<footer>
    <div class="footer-grid">
        <div class="footer-col footer-col-brand">
            <div class="footer-logo">
                <img src="{{ asset('agrifarm_oficial.png') }}" alt="AgriFarm" />
            </div>
            <p class="footer-tagline">Gestão inteligente de ração e nutrição animal para fazendas modernas.</p>
            <div class="footer-social" role="navigation" aria-label="Redes sociais (rodapé)">
                <a href="https://facebook.com/agrifarm" target="_blank" rel="noopener noreferrer" aria-label="Facebook do AgriFarm (abre em nova aba)">
                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                </a>
                <a href="https://instagram.com/agrifarm" target="_blank" rel="noopener noreferrer" aria-label="Instagram do AgriFarm (abre em nova aba)">
                    <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                </a>
                <a href="https://linkedin.com/company/agrifarm" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn do AgriFarm (abre em nova aba)">
                    <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                </a>
                <a href="https://youtube.com/@agrifarm" target="_blank" rel="noopener noreferrer" aria-label="YouTube do AgriFarm (abre em nova aba)">
                    <i class="fa-brands fa-youtube" aria-hidden="true"></i>
                </a>
                <a href="https://wa.me/5511999999999" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp do AgriFarm (abre em nova aba)">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div class="footer-col">
            <h2 class="footer-col-title">Navegação</h2>
            <a href="{{ route('projeto.index') }}">Início</a>
            <a href="{{ route('landing.problema') }}">Problema</a>
            <a href="{{ route('landing.solucao') }}">Solução</a>
            <a href="{{ route('landing.comoFunciona') }}">Como Funciona</a>
            <a href="{{ route('landing.beneficios') }}">Benefícios</a>
            <a href="{{ route('landing.sobre') }}">Sobre</a>
        </div>

        <div class="footer-col">
            <h2 class="footer-col-title">Contato</h2>
            <a href="mailto:contato@agrifarm.com.br"><i class="fa-solid fa-envelope" aria-hidden="true"></i> contato@agrifarm.com.br</a>
            <a href="https://wa.me/5511999999999" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp (11) 99999-9999 (abre em nova aba)"><i class="fa-solid fa-phone" aria-hidden="true"></i> (11) 99999-9999</a>
            <a href="{{ route('landing.contato') }}"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Fale Conosco</a>
            <a href="{{ route('login.index') }}"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Acessar Sistema</a>
        </div>

        <div class="footer-col">
            <h2 class="footer-col-title">Sobre o Projeto</h2>
            <p class="footer-copy">
                Projeto Acadêmico<br>
                Curso de Desenvolvimento de Sistemas<br>
                SENAI
            </p>
        </div>
    </div>

    <div class="footer-bottom">© 2026 AgriFarm — Todos os direitos reservados</div>
</footer>
