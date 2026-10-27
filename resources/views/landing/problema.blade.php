<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | O Problema</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

@include('partials.landing-nav', ['paginaAtiva' => 'problema'])

<section id="problema">
    <div class="container">

        <span class="section-tag reveal-tag">
            O Problema
        </span>

        <h1 class="section-title reveal-title"><i class="fa-solid fa-triangle-exclamation accent titulo-icone" aria-hidden="true"></i> O desperdício de ração ainda é um dos <span class="accent">maiores custos</span> da pecuária</h1>

            <div class="problema-grid">
                <div>
                    <p class="problema-intro">
                        Parcela significativa das propriedades rurais ainda conduz o controle alimentar
                        de forma manual, gerando ineficiências que comprometem diretamente
                        a rentabilidade e a sustentabilidade da operação.
                    </p>
                    <div class="check-list">
                        <div class="check-item">
                            <div class="check-icon bad"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg></div>
                            <span>Ausência de precisão no controle do fornecimento de ração</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon bad"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg></div>
                            <span>Desperdício expressivo de insumos alimentares</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon bad"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg></div>
                            <span>Dificuldade no controle individualizado por lote</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon bad"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg></div>
                            <span>Insuficiência de dados para a tomada de decisões estratégicas</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon bad"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg></div>
                            <span>Elevação progressiva dos custos operacionais</span>
                        </div>
                    </div>
                    <p class="quote-block">
                        A ausência de instrumentação tecnológica torna a gestão da 
                        alimentação animal
                        substancialmente menos eficiente e mais onerosa para o produtor rural.
                    </p>
                </div>
                <div class="stats-grid">
                    <div class="data-card red stat-big">
                        <div class="num">20%</div>
                        <p>da ração é desperdiçada em propriedades sem manejo tecnológico adequado</p>
                    </div>
                    <div class="data-card yellow">
                        <div class="num">72%</div>
                        <p>do custo de produção é atribuído diretamente à ração (frango de corte)</p>
                    </div>
                    <div class="data-card orange">
                        <div class="num">3%</div>
                        <p>do orçamento comprometido com índice de apenas 5% de desperdício</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')
@include('partials.landing-scripts')
