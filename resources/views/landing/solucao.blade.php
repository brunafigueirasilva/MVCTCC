<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | A Solução</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

@include('partials.landing-nav', ['paginaAtiva' => 'solucao'])

    <section id="solucao">
        <div class="container">
            <span class="section-tag reveal-tag">A Solução</span>
            <h1 class="section-title reveal-title"><i class="fa-solid fa-lightbulb accent titulo-icone" aria-hidden="true"></i> A solução: <span class="accent">AgriFarm</span></h1>

            <div class="solucao-card">
                <div class="sol-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v1m0 16v1M4.22 4.22l.707.707m12.02 12.02l.707.707M1 12h1m20 0h1M4.22 19.78l.707-.707M18.364 5.636l.707-.707M12 7a5 5 0 100 10A5 5 0 0012 7z" />
                    </svg>
                </div>

                <div class="sol-title-badge">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2"
                        style="width:12px;height:12px;stroke:#a3c96e">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Sistema Homologado · Projeto de TCC SENAI
                </div>

                <p class="sol-text">
                    O <strong style="color:#6aaa2e">AgriFarm</strong> é um sistema integrado de monitoramento e
                    automação
                    da alimentação animal, fundamentado em <strong>sensores IoT</strong> e análise de dados em tempo
                    real.
                </p>
                <p class="sol-text" style="font-size:.96rem">
                    A plataforma confere ao produtor controle integral sobre a nutrição do rebanho,
                    operando de forma <strong>precisa, automatizada e
                        eficiente</strong> — eliminando
                    a dependência de processos manuais sujeitos a erros e inconsistências.
                </p>

                <div class="sol-divider"></div>

                <div class="sol-features">
                    <div class="sol-feat">
                        <div class="f-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                            </svg>
                        </div>
                        <div class="f-title">Sensores IoT</div>
                        <div class="f-desc">Leitura contínua e em tempo real diretamente nos cochos de alimentação
                        </div>
                    </div>
                    <div class="sol-feat">
                        <div class="f-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                            </svg>
                        </div>
                        <div class="f-title">Nuvem em Tempo Real</div>
                        <div class="f-desc">Transmissão e armazenamento seguro dos dados com acesso remoto imediato
                        </div>
                    </div>
                    <div class="sol-feat">
                        <div class="f-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        <div class="f-title">Relatórios Analíticos</div>
                        <div class="f-desc">Painéis e relatórios detalhados para fundamentar decisões operacionais
                        </div>
                    </div>
                </div>

                <div class="sol-stats-row">
                    <div class="sol-stat">
                        <div class="v">IoT</div>
                        <div class="l">Sensores integrados ao campo</div>
                    </div>
                    <div class="sol-stat">
                        <div class="v">Cloud</div>
                        <div class="l">Dados seguros e acessíveis</div>
                    </div>
                    <div class="sol-stat">
                        <div class="v">BI</div>
                        <div class="l">Inteligência analítica avançada</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')
@include('partials.landing-scripts')
