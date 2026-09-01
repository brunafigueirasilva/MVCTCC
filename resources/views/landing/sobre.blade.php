<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | Sobre o Projeto</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

@include('partials.landing-nav', ['paginaAtiva' => 'sobre'])

    <section id="sobre">
        <div class="container">
            <span class="section-tag reveal-tag">TCC SENAI</span>
            <h2 class="section-title reveal-title">Sobre o Projeto</h2>
            <p class="sobre-text">
                O AgriFarm foi desenvolvido como <strong style="color:#6aaa2e">projeto de conclusão de curso</strong> com
                o objetivo de aplicar tecnologia e inovação na gestão da alimentação animal em escala operacional.
            </p>
            <p class="sobre-text">
                O projeto demonstra como a automação e a análise de dados têm o potencial de transformar a pecuária,
                tornando-a mais eficiente, econômica e ambientalmente sustentável.
            </p>
            <div class="team-grid">
                <h3>Equipe de Desenvolvimento</h3>
                <div class="member">
                    <div class="member-avatar"><img src="{{ asset('Bruna.png') }}" alt="Foto de Bruna Figueira Silva" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>
                    <div>
                        <div class="member-name">Bruna Figueira Silva</div>
                        <div class="member-role">Programadora Back-End</div>
                    </div>
                </div>
                <div class="member">
                    <div class="member-avatar"><img src="{{ asset('JoaoEmanoel.png') }}" alt="Foto de João Emanoel Porfirio de Souza" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>
                    <div>
                        <div class="member-name">João Emanoel Porfirio de Souza</div>
                        <div class="member-role">Banco de Dados / Front-End</div>
                    </div>
                </div>
                <div class="member">
                    <div class="member-avatar"><img src="{{ asset('Leonardo.png') }}" alt="Foto de Leonardo Henrique Boscolo da Cruz" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>
                    <div>
                        <div class="member-name">Leonardo Henrique Boscolo da Cruz</div>
                        <div class="member-role">Banco de Dados / Front-End</div>
                    </div>
                </div>
                <div class="member">
                    <div class="member-avatar"><img src="{{ asset('MariaEduarda.png') }}" alt="Foto de Maria Eduarda Evangelista" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>
                    <div>
                        <div class="member-name">Maria Eduarda Evangelista</div>
                        <div class="member-role">Programadora Back-End</div>
                    </div>
                </div>

                 <div class="member">
                    <div class="member-avatar"><img src="{{ asset('Matheus.png') }}" alt="Foto de Matheus Emanuel Carvalho de Oliveira" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>
                    <div>
                        <div class="member-name">Matheus Emanuel Carvalho de Oliveira</div>
                        <div class="member-role">Banco de Dados / Front-End</div>
                    </div>
                </div>
                <div class="member">
                    <div class="member-avatar">Y</div>
                    <div>
                        <div class="member-name">Yasmin Zanotti</div>
                        <div class="member-role">Banco de Dados / Front-End</div>
                    </div>
                </div>
                <div class="member">
                    <div class="member-avatar"><img src="{{ asset('YasminKarolaine.png') }}" alt="Foto de Yasmin Gentina Karolaine" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"></div>
                    <div>
                        <div class="member-name">Yasmin Gentina Karolaine</div>
                        <div class="member-role">Desenvolvedora Full Stack</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')
@include('partials.landing-scripts')
