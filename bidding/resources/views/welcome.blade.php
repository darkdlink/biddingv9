<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema Bidding - Sua plataforma para gerenciamento de licitações públicas">
    <title>{{ config('app.name', 'Sistema Bidding') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .navbar {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 6rem 0;
        }

        .feature-item {
            padding: 2rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-item:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }

        .pricing-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 2rem 1.5rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .pricing-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .pricing-card.popular {
            border-color: var(--primary-color);
            position: relative;
        }

        .pricing-card.popular::before {
            content: "Mais Popular";
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .testimonial-card {
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        .testimonial-avatar {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 4rem 0;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 3rem 0 1.5rem;
        }

        .footer-title {
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 0.75rem;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: white;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            margin-right: 0.75rem;
            transition: background-color 0.3s ease;
        }

        .social-icon:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #224abe;
            border-color: #224abe;
        }

        .btn-white {
            background-color: white;
            color: var(--primary-color);
        }

        .btn-white:hover {
            background-color: #f8f9fc;
            color: #224abe;
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Sistema Bidding" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#features">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Planos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Depoimentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contato</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary px-4">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary px-4">Entrar</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-4">Transforme a Gestão de Licitações da sua Empresa</h1>
                    <p class="lead mb-4">Uma plataforma completa para encontrar, analisar e participar de licitações públicas de forma eficiente e organizada.</p>
                    <div class="d-flex gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-white btn-lg px-4">Acessar Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-white btn-lg px-4">Começar Agora</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">Entrar</a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/hero-image.png') }}" alt="Sistema Bidding Dashboard" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary text-white rounded-circle p-3 me-3">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Economize Tempo</h5>
                            <p class="mb-0 text-muted">Reduza em até 70% o tempo gasto na busca por licitações</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning text-white rounded-circle p-3 me-3">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Aumente Oportunidades</h5>
                            <p class="mb-0 text-muted">Encontre até 3x mais oportunidades relevantes para seu negócio</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success text-white rounded-circle p-3 me-3">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Maximize Resultados</h5>
                            <p class="mb-0 text-muted">Aumente sua taxa de sucesso em participações em licitações</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 py-md-7">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-3">Recursos Completos para Participar de Licitações</h2>
                    <p class="lead text-muted">Descubra como nossa plataforma pode transformar o processo de participação em licitações públicas.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="feature-item shadow-sm border">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-search"></i>
                        </div>
                        <h4 class="mb-3 text-center">Busca Inteligente</h4>
                        <p class="text-muted mb-0">Encontre licitações relevantes para seu negócio através de filtros avançados e segmentação por área de atuação.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-item shadow-sm border">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h4 class="mb-3 text-center">Alertas Personalizados</h4>
                        <p class="text-muted mb-0">Receba notificações sobre novas licitações que correspondam aos seus critérios de interesse.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-item shadow-sm border">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h4 class="mb-3 text-center">Gestão de Propostas</h4>
                        <p class="text-muted mb-0">Crie, edite e acompanhe suas propostas em um único lugar, com controle de versões e histórico completo.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-item shadow-sm border">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h4 class="mb-3 text-center">Acompanhamento de Prazos</h4>
                        <p class="text-muted mb-0">Nunca perca um prazo importante com nosso sistema de acompanhamento e lembretes.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-item shadow-sm border">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="mb-3 text-center">Colaboração em Equipe</h4>
                        <p class="text-muted mb-0">Trabalhe de forma colaborativa com sua equipe, atribuindo tarefas e compartilhando informações.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-item shadow-sm border">
                        <div class="feature-icon mx-auto">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4 class="mb-3 text-center">Relatórios e Análises</h4>
                        <p class="text-muted mb-0">Obtenha insights valiosos com relatórios detalhados sobre seu desempenho em licitações.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 py-md-7 bg-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-3">Planos que se Adaptam às suas Necessidades</h2>
                    <p class="lead text-muted">Escolha o plano ideal para seu perfil e comece a transformar a gestão de licitações.</p>
                    <div class="d-flex justify-content-center mt-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary px-4 active" id="btnMensal">Mensal</button>
                            <button type="button" class="btn btn-outline-primary px-4" id="btnAnual">Anual</button>
                        </div>
                        <div class="ms-3 badge bg-warning d-flex align-items-center">
                            Economize 20% no plano anual
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="pricing-card h-100">
                        <div class="text-center mb-4">
                            <h5 class="text-uppercase fw-bold">Pessoa Física</h5>
                            <div class="pricing-monthly">
                                <h2 class="display-5 fw-bold mb-0">R$ 97</h2>
                                <p class="text-muted">por mês</p>
                            </div>
                            <div class="pricing-annual d-none">
                                <h2 class="display-5 fw-bold mb-0">R$ 77,60</h2>
                                <p class="text-muted">por mês (cobrado anualmente)</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>1 segmento de atuação</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Acesso a licitações básicas</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Alertas limitados (10/mês)</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Armazenamento limitado de propostas (20)</span>
                            </li>
                            <li class="d-flex align-items-center mb-3 text-muted">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                <span>Classificação de relevância</span>
                            </li>
                            <li class="d-flex align-items-center mb-3 text-muted">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                <span>Análise de concorrência</span>
                            </li>
                        </ul>
                        <div class="text-center mt-auto">
                            <a href="{{ route('register') }}?plan=pessoa_fisica_basico" class="btn btn-outline-primary w-100">Começar Agora</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="pricing-card h-100 popular">
                        <div class="text-center mb-4">
                            <h5 class="text-uppercase fw-bold">Empresa</h5>
                            <div class="pricing-monthly">
                                <h2 class="display-5 fw-bold mb-0">R$ 297</h2>
                                <p class="text-muted">por mês</p>
                            </div>
                            <div class="pricing-annual d-none">
                                <h2 class="display-5 fw-bold mb-0">R$ 237,60</h2>
                                <p class="text-muted">por mês (cobrado anualmente)</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>1 Usuário Master + 2 usuários dependentes</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>1 segmento de atuação compartilhado</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Dashboard unificado para a empresa</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Gerenciamento básico de propostas</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Alertas ilimitados</span>
                            </li>
                            <li class="d-flex align-items-center mb-3 text-muted">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                <span>Integração com outros sistemas</span>
                            </li>
                        </ul>
                        <div class="text-center mt-auto">
                            <a href="{{ route('register') }}?plan=empresa_basico" class="btn btn-primary w-100">Escolher este Plano</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="pricing-card h-100">
                        <div class="text-center mb-4">
                            <h5 class="text-uppercase fw-bold">Grupo Empresarial</h5>
                            <div class="pricing-monthly">
                                <h2 class="display-5 fw-bold mb-0">R$ 1.997</h2>
                                <p class="text-muted">por mês</p>
                            </div>
                            <div class="pricing-annual d-none">
                                <h2 class="display-5 fw-bold mb-0">R$ 1.597,60</h2>
                                <p class="text-muted">por mês (cobrado anualmente)</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>1 Administrador de Grupo</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Até 3 empresas com Usuário Master cada</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Até 5 usuários dependentes por empresa</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Dashboard consolidado do grupo</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Alertas ilimitados</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Relatórios comparativos entre empresas</span>
                            </li>
                        </ul>
                        <div class="text-center mt-auto">
                            <a href="{{ route('register') }}?plan=grupo_basico" class="btn btn-outline-primary w-100">Solicitar Demonstração</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">Precisa de um plano personalizado? <a href="#contact" class="text-primary">Entre em contato</a> para saber mais.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h2 class="display-5 fw-bold mb-4">Comece a Transformar Sua Gestão de Licitações Hoje</h2>
                    <p class="lead mb-4">Junte-se a centenas de empresas que já utilizam nossa plataforma para encontrar oportunidades e vencer licitações.</p>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="{{ route('register') }}" class="btn btn-white btn-lg px-4">Começar Gratuitamente</a>
                        <a href="#contact" class="btn btn-outline-light btn-lg px-4">Falar com um Consultor</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Sistema Bidding" height="40" class="mb-4">
                    <p class="mb-4">Uma plataforma completa para encontrar, analisar e participar de licitações públicas de forma eficiente e organizada.</p>
                    <div class="d-flex">
                        <a href="#" class="social-icon">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="footer-title">Plataforma</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="footer-link">Recursos</a></li>
                        <li><a href="#pricing" class="footer-link">Planos</a></li>
                        <li><a href="#testimonials" class="footer-link">Depoimentos</a></li>
                        <li><a href="#faq" class="footer-link">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="footer-title">Recursos</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Busca Inteligente</a></li>
                        <li><a href="#" class="footer-link">Alertas Personalizados</a></li>
                        <li><a href="#" class="footer-link">Gestão de Propostas</a></li>
                        <li><a href="#" class="footer-link">Relatórios e Análises</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="footer-title">Empresa</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Sobre Nós</a></li>
                        <li><a href="#" class="footer-link">Blog</a></li>
                        <li><a href="#" class="footer-link">Carreiras</a></li>
                        <li><a href="#contact" class="footer-link">Contato</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="footer-title">Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Termos de Uso</a></li>
                        <li><a href="#" class="footer-link">Privacidade</a></li>
                        <li><a href="#" class="footer-link">Cookies</a></li>
                        <li><a href="#" class="footer-link">Licenças</a></li>
                    </ul>
                </div>
            </div>
            <hr class="mt-5 mb-4" style="border-color: rgba(255, 255, 255, 0.1);">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} Sistema Bidding. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-muted">Desenvolvido para gestão eficiente de licitações</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Alternância entre preço mensal e anual
            const btnMensal = document.getElementById('btnMensal');
            const btnAnual = document.getElementById('btnAnual');
            const pricingMonthly = document.querySelectorAll('.pricing-monthly');
            const pricingAnnual = document.querySelectorAll('.pricing-annual');

            if (btnMensal && btnAnual) {
                btnMensal.addEventListener('click', function() {
                    btnMensal.classList.add('active');
                    btnMensal.classList.remove('btn-outline-primary');
                    btnMensal.classList.add('btn-primary');

                    btnAnual.classList.remove('active');
                    btnAnual.classList.remove('btn-primary');
                    btnAnual.classList.add('btn-outline-primary');

                    pricingMonthly.forEach(el => el.classList.remove('d-none'));
                    pricingAnnual.forEach(el => el.classList.add('d-none'));
                });

                btnAnual.addEventListener('click', function() {
                    btnAnual.classList.add('active');
                    btnAnual.classList.remove('btn-outline-primary');
                    btnAnual.classList.add('btn-primary');

                    btnMensal.classList.remove('active');
                    btnMensal.classList.remove('btn-primary');
                    btnMensal.classList.add('btn-outline-primary');

                    pricingAnnual.forEach(el => el.classList.remove('d-none'));
                    pricingMonthly.forEach(el => el.classList.add('d-none'));
                });
            }
        });
    </script>
</body>
</html>
