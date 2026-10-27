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

    <style>
        /* ── Card da equipe clicável: o .member continua com o visual do
           landing-styles, só vira um <button> (acessível por teclado). ── */
        button.member {
            font: inherit;
            color: inherit;
            width: 100%;
            cursor: pointer;
        }

        button.member:focus-visible {
            outline: 2px solid var(--gold-light);
            outline-offset: 3px;
        }

        .member-ver-mais {
            margin-top: 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--green-light);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap var(--hover-duration) var(--ease-premium);
        }

        .member:hover .member-ver-mais { gap: 10px; }

        /* ── Modal do integrante ── */
        .membro-modal {
            margin: auto;
            width: min(560px, calc(100% - 32px));
            max-height: calc(100% - 32px);
            padding: 0;
            border: 1px solid rgba(106, 170, 46, .35);
            border-radius: 28px;
            background: #1e2b19;
            color: var(--text);
            box-shadow: 0 30px 80px rgba(0, 0, 0, .55);
            overflow: auto;
        }

        .membro-modal::backdrop {
            background: rgba(8, 12, 7, .72);
            backdrop-filter: blur(6px);
        }

        .membro-modal[open] { animation: membroModalIn .45s var(--ease-premium); }

        @keyframes membroModalIn {
            from { opacity: 0; transform: translateY(18px) scale(.97); }
            to { opacity: 1; transform: none; }
        }

        .membro-modal-topo {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 40px 32px 26px;
            background: linear-gradient(160deg, #3d5e1c, #2c4418 55%, #1e2b19);
        }

        .membro-modal-fechar {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .2);
            background: rgba(0, 0, 0, .2);
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background var(--hover-duration) var(--ease-premium), transform var(--hover-duration) var(--ease-premium);
        }

        .membro-modal-fechar:hover,
        .membro-modal-fechar:focus-visible {
            background: rgba(0, 0, 0, .4);
            transform: rotate(90deg);
        }

        .membro-modal-foto {
            width: 132px;
            height: 132px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid rgba(232, 196, 99, .7);
            box-shadow: 0 14px 32px rgba(0, 0, 0, .45);
            margin-bottom: 16px;
        }

        .membro-modal-foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 22%;
            display: block;
        }

        .membro-modal-nome {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.3;
        }

        .membro-modal-cargo {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--gold-light);
        }

        .membro-modal-corpo { padding: 26px 32px 32px; }

        .membro-modal-rotulo {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--brown-light);
            margin-bottom: 10px;
        }

        .membro-modal-sobre {
            font-size: 14.5px;
            line-height: 1.7;
            color: rgba(241, 245, 249, .85);
            margin-bottom: 22px;
        }

        .membro-modal-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
        }

        .membro-modal-tags span {
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(106, 170, 46, .12);
            border: 1px solid rgba(106, 170, 46, .35);
            color: var(--green-light);
        }

        .membro-modal-redes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .membro-rede {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            text-decoration: none;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            color: var(--text);
            transition: border-color var(--hover-duration) ease, transform var(--hover-duration) var(--ease-premium), background var(--hover-duration) ease;
        }

        .membro-rede:hover,
        .membro-rede:focus-visible {
            border-color: rgba(106, 170, 46, .55);
            background: rgba(255, 255, 255, .08);
            transform: translateY(-2px);
        }

        .membro-rede i {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: #fff;
            flex-shrink: 0;
        }

        .membro-rede.instagram i { background: linear-gradient(135deg, #f58529, #dd2a7b 50%, #8134af); }
        .membro-rede.whatsapp i { background: #25d366; }
        .membro-rede.github i { background: #24292f; }

        /* GitHub ocupa a linha inteira embaixo de Instagram/WhatsApp. */
        .membro-rede.github { grid-column: 1 / -1; }
        .membro-rede[hidden] { display: none; }

        .membro-rede-texto { display: flex; flex-direction: column; min-width: 0; }
        .membro-rede-texto small { font-size: 11px; color: var(--text-muted); }
        .membro-rede-texto strong {
            font-size: 13.5px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 480px) {
            .membro-modal-topo { padding: 36px 20px 22px; }
            .membro-modal-corpo { padding: 22px 20px 26px; }
            .membro-modal-redes { grid-template-columns: 1fr; }
        }

        /* Tema Claro */
        body.light-theme .membro-modal {
            background: #ffffff;
            color: var(--text-on-light);
            border-color: rgba(79, 119, 36, .35);
        }

        body.light-theme .membro-modal-corpo { background: #ffffff; }
        body.light-theme .membro-modal-sobre { color: var(--text-on-light); }
        body.light-theme .membro-modal-tags span { background: rgba(79, 119, 36, .1); border-color: rgba(79, 119, 36, .3); color: var(--green-dark); }
        body.light-theme .membro-rede { background: #f5fbf6; border-color: var(--card-light-border); color: var(--text-on-light); }
        body.light-theme .membro-rede-texto small { color: var(--text-on-light-muted); }
        body.light-theme .member-ver-mais { color: var(--green-dark); }

        /* Alto Contraste */
        body.high-contrast .membro-modal,
        body.high-contrast .membro-modal-topo,
        body.high-contrast .membro-modal-corpo {
            background: #000000 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .membro-modal { border: 2px solid #ffff00 !important; }
        body.high-contrast .membro-modal-nome,
        body.high-contrast .membro-modal-cargo,
        body.high-contrast .membro-modal-rotulo,
        body.high-contrast .membro-modal-sobre,
        body.high-contrast .membro-rede-texto small,
        body.high-contrast .membro-rede-texto strong,
        body.high-contrast .member-ver-mais { color: #ffff00 !important; }

        body.high-contrast .membro-modal-tags span,
        body.high-contrast .membro-rede,
        body.high-contrast .membro-modal-fechar {
            background: #000000 !important;
            border: 2px solid #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast .membro-rede i {
            background: #000000 !important;
            border: 1px solid #ffff00;
            color: #ffff00 !important;
        }

        body.high-contrast .membro-modal-foto { border-color: #ffff00 !important; }
    </style>
</head>

<body>

@include('partials.landing-nav', ['paginaAtiva' => 'sobre'])

@php
    // Integrantes da equipe — cada card abre o modal com estes dados.
    // "whatsapp" guarda só os dígitos (com DDI 55) pro link wa.me;
    // "whatsapp_exibicao" é o número formatado mostrado na tela.
    $equipe = [
        [
            'nome' => 'Bruna Figueira Silva',
            'foto' => 'Bruna.png',
            'cargo' => 'Scrum Master / Back-End',
            'sobre' => 'Profissional que atua como Scrum Master e Desenvolvedora Back-End, unindo liderança, organização e desenvolvimento de soluções eficientes e escaláveis para garantir o bom andamento e a qualidade dos projetos.',
            'areas' => ['Scrum Master', 'Back-End', 'Gestão de Projetos'],
            'instagram' => '_w3brunaa',
            'whatsapp' => '5519990019586',
            'whatsapp_exibicao' => '(19) 99001-9586',
            'github' => 'https://github.com/brunafigueirasilva',
        ],
        [
            'nome' => 'João Emanoel Porfirio de Souza',
            'foto' => 'Joao.png',
            'cargo' => 'Front-End / Banco de Dados',
            'sobre' => 'Profissional responsável pelo desenvolvimento Front-End e gerenciamento de Banco de Dados, criando interfaces modernas e funcionais e garantindo que as informações do sistema sejam organizadas, seguras e eficientes.',
            'areas' => ['Front-End', 'Banco de Dados', 'Interfaces'],
            'instagram' => 'e__porfirio',
            'whatsapp' => '5519994058068',
            'whatsapp_exibicao' => '(19) 99405-8068',
            'github' => 'https://github.com/Joao-Emanoel',
        ],
        [
            'nome' => 'Leonardo Henrique Boscolo da Cruz',
            'foto' => 'Leo.png',
            'cargo' => 'Front-End / Banco de Dados',
            'sobre' => 'Profissional responsável pelo desenvolvimento Front-End e gerenciamento de Banco de Dados, criando interfaces modernas e funcionais e garantindo que as informações do sistema sejam organizadas, seguras e eficientes.',
            'areas' => ['Front-End', 'Banco de Dados', 'Interfaces'],
            'instagram' => '_leonardohenriquec_',
            'whatsapp' => '5519994069239',
            'whatsapp_exibicao' => '(19) 99406-9239',
            'github' => null,
        ],
        [
            'nome' => 'Maria Eduarda Evangelista',
            'foto' => 'MariaEduarda.png',
            'cargo' => 'Desenvolvedora Back-End',
            'sobre' => 'Profissional responsável pelo desenvolvimento Back-End, trabalhando na criação de funcionalidades, APIs e integrações, garantindo que o sistema funcione de forma segura, eficiente e confiável.',
            'areas' => ['Back-End', 'APIs', 'Integrações'],
            'instagram' => 'maduxwp',
            'whatsapp' => '5519991469672',
            'whatsapp_exibicao' => '(19) 99146-9672',
            'github' => null,
        ],
        [
            'nome' => 'Matheus Emanuel Carvalho de Oliveira',
            'foto' => 'Matheus.png',
            'cargo' => 'Desenvolvedor Back-End',
            'sobre' => 'Profissional responsável pelo desenvolvimento Back-End, trabalhando na criação de funcionalidades, APIs e integrações, garantindo que o sistema funcione de forma segura, eficiente e confiável.',
            'areas' => ['Back-End', 'APIs', 'Integrações'],
            'instagram' => 'matheusoliveira1620',
            'whatsapp' => '5519993020664',
            'whatsapp_exibicao' => '(19) 99302-0664',
            'github' => 'https://github.com/Matheus-41',
        ],
        [
            'nome' => 'Yasmin Gabrielli Ruivo Zanotti',
            'foto' => 'YasminZanotti.png',
            'cargo' => 'Desenvolvedora Back-End',
            'sobre' => 'Profissional responsável pelo desenvolvimento Back-End, trabalhando na criação de funcionalidades, APIs e integrações, garantindo que o sistema funcione de forma segura, eficiente e confiável.',
            'areas' => ['Back-End', 'APIs', 'Integrações'],
            'instagram' => 'yasminzanotti_',
            'whatsapp' => '5519989708822',
            'whatsapp_exibicao' => '(19) 98970-8822',
            'github' => null,
        ],
        [
            'nome' => 'Yasmin Gentina Karolaine',
            'foto' => 'YasminKarolaine.png',
            'cargo' => 'Full Stack / Product Owner',
            'sobre' => 'Profissional que atua como Desenvolvedora Full Stack e Product Owner (PO), unindo desenvolvimento de sistemas, gestão de funcionalidades e organização das necessidades do projeto para garantir soluções completas e alinhadas aos objetivos da equipe e dos usuários.',
            'areas' => ['Full Stack', 'Product Owner', 'Gestão de Produto'],
            'instagram' => 'karoline_nimsay',
            'whatsapp' => '5519993127375',
            'whatsapp_exibicao' => '(19) 99312-7375',
            'github' => 'https://github.com/Yasmin201015',
        ],
    ];
@endphp

    <section id="sobre">
        <div class="container">
            <span class="section-tag reveal-tag">TCC SENAI</span>
            <h1 class="section-title reveal-title"><i class="fa-solid fa-circle-info accent titulo-icone" aria-hidden="true"></i> Sobre o Projeto</h1>
            <p class="sobre-text">
                O AgriFarm foi desenvolvido como <strong style="color:#6aaa2e">projeto de conclusão de curso</strong> com
                o objetivo de aplicar tecnologia e inovação na gestão da alimentação animal em escala operacional.
            </p>
            <p class="sobre-text">
                O projeto demonstra como a automação e a análise de dados têm o potencial de transformar a pecuária,
                tornando-a mais eficiente, econômica e ambientalmente sustentável.
            </p>
            <div class="team-grid">
                <h2>Equipe de Desenvolvimento</h2>
                @foreach ($equipe as $indice => $membro)
                    <button type="button" class="member" data-membro="{{ $indice }}" aria-haspopup="dialog">
                        <div class="member-avatar"><img src="{{ asset($membro['foto']) }}" alt="Foto de {{ $membro['nome'] }}" loading="lazy"></div>
                        <div>
                            <div class="member-name">{{ $membro['nome'] }}<span class="a11y-sr-only"> (ver mais)</span></div>
                            <div class="member-role">{{ $membro['cargo'] }}</div>
                        </div>
                        <span class="member-ver-mais">Ver perfil <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- MODAL DO INTEGRANTE (preenchido pelo JS a partir de $equipe) -->
    <dialog class="membro-modal" id="membroModal" aria-labelledby="membroModalNome">
        <div class="membro-modal-topo">
            <button type="button" class="membro-modal-fechar" id="membroModalFechar" aria-label="Fechar">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
            <div class="membro-modal-foto"><img id="membroModalFoto" src="" alt=""></div>
            <div class="membro-modal-nome" id="membroModalNome"></div>
            <div class="membro-modal-cargo" id="membroModalCargo"></div>
        </div>
        <div class="membro-modal-corpo">
            <div class="membro-modal-rotulo">Sobre</div>
            <p class="membro-modal-sobre" id="membroModalSobre"></p>

            <div class="membro-modal-rotulo">Áreas de atuação</div>
            <div class="membro-modal-tags" id="membroModalAreas"></div>

            <div class="membro-modal-rotulo">Contato e redes</div>
            <div class="membro-modal-redes">
                <a class="membro-rede instagram" id="membroModalInstagram" href="#" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                    <span class="membro-rede-texto">
                        <small>Instagram</small>
                        <strong id="membroModalInstagramTexto"></strong>
                        <span class="a11y-sr-only">(abre em nova aba)</span>
                    </span>
                </a>
                <a class="membro-rede whatsapp" id="membroModalWhatsapp" href="#" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                    <span class="membro-rede-texto">
                        <small>WhatsApp</small>
                        <strong id="membroModalWhatsappTexto"></strong>
                        <span class="a11y-sr-only">(abre em nova aba)</span>
                    </span>
                </a>
                <a class="membro-rede github" id="membroModalGithub" href="#" target="_blank" rel="noopener noreferrer" hidden>
                    <i class="fa-brands fa-github" aria-hidden="true"></i>
                    <span class="membro-rede-texto">
                        <small>GitHub</small>
                        <strong id="membroModalGithubTexto"></strong>
                        <span class="a11y-sr-only">(abre em nova aba)</span>
                    </span>
                </a>
            </div>
        </div>
    </dialog>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')
@include('partials.landing-scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const equipe = @json(collect($equipe)->map(fn ($m) => $m + ['foto_url' => asset($m['foto'])]));
        const modal = document.getElementById('membroModal');
        if (!modal || typeof modal.showModal !== 'function') return;

        const el = (id) => document.getElementById(id);

        function abrirMembro(indice) {
            const m = equipe[indice];
            if (!m) return;

            el('membroModalFoto').src = m.foto_url;
            el('membroModalFoto').alt = 'Foto de ' + m.nome;
            el('membroModalNome').textContent = m.nome;
            el('membroModalCargo').textContent = m.cargo;
            el('membroModalSobre').textContent = m.sobre;

            const areas = el('membroModalAreas');
            areas.replaceChildren(...m.areas.map(function (area) {
                const tag = document.createElement('span');
                tag.textContent = area;
                return tag;
            }));

            el('membroModalInstagram').href = 'https://instagram.com/' + m.instagram;
            el('membroModalInstagramTexto').textContent = '@' + m.instagram;
            el('membroModalWhatsapp').href = 'https://wa.me/' + m.whatsapp;
            el('membroModalWhatsappTexto').textContent = m.whatsapp_exibicao;

            // Card do GitHub só aparece pra quem tem link cadastrado.
            const github = el('membroModalGithub');
            github.hidden = !m.github;
            if (m.github) {
                github.href = m.github;
                el('membroModalGithubTexto').textContent = m.github.replace(/^https?:\/\/(www\.)?/, '');
            }

            modal.showModal();
        }

        document.querySelectorAll('.member[data-membro]').forEach(function (card) {
            card.addEventListener('click', function () {
                abrirMembro(Number(card.dataset.membro));
            });
        });

        el('membroModalFechar').addEventListener('click', function () {
            modal.close();
        });

        // Clique fora do conteúdo (no fundo escurecido) também fecha.
        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.close();
        });
    });
</script>
