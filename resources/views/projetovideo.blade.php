<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriFarm | Gestão Inteligente de Ração</title>
    <!-- Sem isso, o ícone do botão de acessibilidade (fa-solid fa-universal-access,
         vindo de partials/acessibilidade.blade.php) nunca aparecia nesta página —
         as classes do Font Awesome existem, mas a fonte/CSS não estava carregado aqui. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@include('partials.landing-styles')
</head>

<body>

    <div id="splash">
        <div class="logo-text">

            <span class="agri">Agri</span>
            <span class="farm">Farm</span>

        </div>

        <div class="subtitle">

            Gerenciamento Inteligente de Ração nas Fazendas

        </div>

        <div class="loader">
            <div class="loader-bar"></div>
        </div>
    </div>


@include('partials.landing-nav', ['paginaAtiva' => 'inicio'])

    <section id="hero">
        <video id="hero-video" autoplay muted loop playsinline
            poster="https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=1920&q=80">
            <source src="vacas.mp4" type="video/mp4" />
        </video>
        <div class="hero-overlay"></div>
        <div class="hero-glow"></div>
        <div class="hero-orb hero-orb--green" aria-hidden="true"></div>
        <div class="hero-orb hero-orb--gold" aria-hidden="true"></div>
        <div class="hero-orb hero-orb--brown" aria-hidden="true"></div>
        <div class="hero-content">
            <div class="hero-tag">
                <div class="tag-dot"></div>
                Tecnologia e IoT para Pecuária de Precisão
            </div>
            <h1 class="hero-title">
                Gestão Inteligente de <span class="accent">Ração</span><br>
                para Fazendas Modernas
            </h1>
            <p class="hero-sub">
                Sensores e automação monitoram o consumo alimentar do rebanho em tempo real,
                reduzindo desperdícios, otimizando custos e incrementando a eficiência operacional
                da produção agropecuária.
            </p>
            <div class="hero-btns">
                <a href="{{ route('landing.contato') }}" class="btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width:16px;height:16px;stroke-width:2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Solicitar Demonstração
                </a>
                <a href="{{ route('landing.comoFunciona') }}" class="btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width:16px;height:16px;stroke-width:2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Conhecer a Solução
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-card">
                    <div class="stat-value">15%</div>
                    <div class="stat-label">Redução de desperdício</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Monitoramento contínuo</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Dados em tempo real</div>
                </div>
            </div>
        </div>
        <a href="{{ route('landing.problema') }}" class="scroll-hint" aria-label="Conhecer o problema que o AgriFarm resolve">
            <span>Continuar</span>
            <svg fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </a>
    </section>

@include('partials.landing-footer')

@include('partials.acessibilidade')
@include('partials.assistente-ia')

<!-- O botãozinho do assistente de IA só aparece depois que a vinheta
     (#splash) termina de subir e sair da tela — antes disso fica
     invisível pra não competir com a animação de abertura. -->
<style>
    .ai-chat-trigger {
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
    }

    .ai-chat-trigger.assistente-visivel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        transition: opacity 0.5s ease, transform 0.5s ease, visibility 0.5s;
    }
</style>

@include('partials.landing-scripts')

<script>
    setTimeout(() => {
        const splash = document.getElementById("splash");
        if (!splash) return;

        splash.style.transition = "transform 1s cubic-bezier(0.76, 0, 0.24, 1)";
        splash.style.transform = "translateY(-100%)";

        setTimeout(() => {
            splash.style.display = "none";
            document.body.style.overflow = "auto";

            const trigger = document.querySelector('.ai-chat-trigger');
            if (trigger) trigger.classList.add('assistente-visivel');
        }, 1050);
    }, 4000);
</script>
