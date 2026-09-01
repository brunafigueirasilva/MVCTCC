<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | Como Funciona</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

@include('partials.landing-nav', ['paginaAtiva' => 'como-funciona'])

    <section id="como-funciona">
        <div class="container">
            <span class="section-tag reveal-tag">Processo Técnico</span>
            <h2 class="section-title reveal-title">Como o sistema opera</h2>
            <div class="steps">
                <div class="step-row">
                    <div class="step-card">
                        <div class="step-header">
                            <div class="step-icon-box">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                            </div>
                            <div>
                                <div class="step-label">Etapa 01</div>
                                <div class="step-title">Instrumentação de Campo</div>
                                <p class="step-desc">Sensores de precisão instalados nos cochos realizam leitura
                                    contínua da quantidade de ração disponível e do consumo efetivo do rebanho.</p>
                            </div>
                        </div>
                    </div>
                    <div class="step-num">01</div>
                    <div></div>
                </div>
                <div class="step-row">
                    <div></div>
                    <div class="step-num">02</div>
                    <div class="step-card">
                        <div class="step-header">
                            <div class="step-icon-box">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </div>
                            <div>
                                <div class="step-label">Etapa 02</div>
                                <div class="step-title">Transmissão de Dados em Tempo Real</div>
                                <p class="step-desc">As informações coletadas são transmitidas automaticamente para a
                                    plataforma digital por meio de conectividade IoT dedicada.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="step-row">
                    <div class="step-card">
                        <div class="step-header">
                            <div class="step-icon-box">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                            </div>
                            <div>
                                <div class="step-label">Etapa 03</div>
                                <div class="step-title">Análise e Relatórios Estratégicos</div>
                                <p class="step-desc">O sistema processa os dados e gera relatórios detalhados de
                                    consumo, desperdício e eficiência alimentar com inteligência analítica avançada.</p>
                            </div>
                        </div>
                    </div>
                    <div class="step-num">03</div>
                    <div></div>
                </div>
                <div class="step-row">
                    <div></div>
                    <div class="step-num">04</div>
                    <div class="step-card">
                        <div class="step-header">
                            <div class="step-icon-box">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="step-label">Etapa 04</div>
                                <div class="step-title">Decisão Fundamentada em Evidências</div>
                                <p class="step-desc">O produtor recebe indicadores claros e precisos para ajustar a
                                    alimentação do rebanho e reduzir custos operacionais com máxima assertividade.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')
@include('partials.landing-scripts')
