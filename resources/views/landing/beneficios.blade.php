<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | Benefícios</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

@include('partials.landing-nav', ['paginaAtiva' => 'beneficios'])

    <section id="beneficios">
        <div class="container">
            <span class="section-tag reveal-tag">Por que AgriFarm?</span>
            <h2 class="section-title reveal-title">Principais benefícios do <span class="accent">AgriFarm</span></h2>
            <div class="cards-grid">
                <!-- Foto: fazenda com cochos de gado, fotografia real -->
                <div class="benefit-card">
                    <div class="benefit-card-bg"
                        style="background-image:url('https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=900&q=85')">
                    </div>
                    <div class="benefit-card-content">
                        <div class="benefit-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        <div class="benefit-title">Controle Preciso do Consumo</div>
                        <p class="benefit-desc">Monitoramento individualizado por lote com leitura contínua dos
                            sensores, eliminando estimativas manuais imprecisas e garantindo rastreabilidade completa da
                            nutrição.</p>
                    </div>
                </div>
                <!-- Foto: paisagem rural brasileira, campo verde e céu azul -->
                <div class="benefit-card">
                    <div class="benefit-card-bg"
                        style="background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR55xFgdQjb8WVaZcoc3UUAjUHHQTt2s7paacwK0GI9pA&s=10')">
                    </div>
                    <div class="benefit-card-content">
                        <div class="benefit-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="benefit-title">Redução de Custos Operacionais</div>
                        <p class="benefit-desc">Aproveitamento superior dos insumos adquiridos com redução comprovada
                            do desperdício, refletindo diretamente na margem de lucro e na competitividade da
                            propriedade rural.</p>
                    </div>
                </div>
                <!-- Foto: pessoa usando tablet em fazenda / gestão rural -->
                <div class="benefit-card">
                    <div class="benefit-card-bg"
                        style="background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR2QZfRpoMi0EQce9-Z4oJ2z80VI81DHpFJfEWKs_ITvw&s=10')">
                    </div>
                    <div class="benefit-card-content">
                        <div class="benefit-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                        <div class="benefit-title">Gestão Baseada em Dados</div>
                        <p class="benefit-desc">Relatórios analíticos e painéis de desempenho que transformam dados
                            brutos em informações estratégicas, fundamentando decisões com precisão técnica e agilidade.
                        </p>
                    </div>
                </div>
                <!-- Foto: gado bovino em pasto, fotografia documental -->
                <div class="benefit-card">
                    <div class="benefit-card-bg"
                        style="background-image:url('https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=900&q=85')">
                    </div>
                    <div class="benefit-card-content">
                        <div class="benefit-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="benefit-title">Automação e Eficiência Operacional</div>
                        <p class="benefit-desc">Substituição dos processos manuais por automação inteligente, liberando
                            a equipe para atividades estratégicas e elevando os padrões de qualidade da gestão animal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="impacto">
        <div class="bg-img"></div>
        <div class="container">
            <span class="section-tag reveal-tag">Agronegócio 4.0</span>
            <h2 class="section-title reveal-title">Impacto para o agronegócio</h2>
            <div class="impacto-grid">
                <div>
                    <p style="color:var(--text-muted);font-size:1rem;line-height:1.7;margin-bottom:20px">
                        A incorporação de tecnologias inteligentes na cadeia pecuária contribui diretamente para:
                    </p>
                    <div class="check-list">
                        <div class="check-item">
                            <div class="check-icon good"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg></div><span>Produção agropecuária mais sustentável e responsável</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon good"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg></div><span>Redução significativa dos custos operacionais</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon good"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg></div><span>Incremento da produtividade e sanidade do rebanho</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon good"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg></div><span>Uso racional e eficiente dos recursos disponíveis</span>
                        </div>
                        <div class="check-item">
                            <div class="check-icon good"><svg fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg></div><span>Modernização integral das propriedades rurais</span>
                        </div>
                    </div>
                    <p class="impacto-quote">
                        O AgriFarm está plenamente alinhado ao conceito de <strong>Agricultura 4.0</strong> — integrando
                        tecnologia, dados e sustentabilidade na cadeia produtiva do campo.
                    </p>
                </div>
                <div class="impacto-box">
                    <div class="big-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>
                    <h3>Agricultura 4.0</h3>
                    <p>A tecnologia inteligente na pecuária integra sensores, plataformas digitais e análise de dados
                        para decisões mais ágeis, precisas e eficientes.</p>
                    <div class="mini-stats">
                        <div class="mini-stat">
                            <div class="v">65k+</div>
                            <div class="l">alimentadores inteligentes em operação no mundo</div>
                        </div>
                        <div class="mini-stat">
                            <div class="v">15%</div>
                            <div class="l">redução de desperdício com manejo de precisão</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="diferenciais">
        <div class="container">
            <span class="section-tag reveal-tag">O que nos diferencia</span>
            <h2 class="section-title reveal-title">Diferenciais da solução</h2>
            <div class="diff-grid">
                <div class="diff-card">
                    <div class="d-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg></div>
                    <p>Integração nativa com sensores IoT de campo</p>
                </div>
                <div class="diff-card">
                    <div class="d-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <p>Monitoramento contínuo em tempo real</p>
                </div>
                <div class="diff-card">
                    <div class="d-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3" />
                        </svg></div>
                    <p>Interface operacional intuitiva e acessível</p>
                </div>
                <div class="diff-card">
                    <div class="d-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                        </svg></div>
                    <p>Aplicação prática e validada no agronegócio</p>
                </div>
                <div class="diff-card">
                    <div class="d-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18-3a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3m18-3V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                        </svg></div>
                    <p>Foco em sustentabilidade e eficiência produtiva</p>
                </div>
            </div>
        </div>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')
@include('partials.landing-scripts')
