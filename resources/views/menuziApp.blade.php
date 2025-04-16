<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>MenuziApp - Seu sistema de delivery completo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta name="theme-color" content="#001c40">
    <link rel="icon" href="{{ asset('images/MenuziAppLogo.png') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            scroll-behavior: smooth;
            overflow-x: hidden;
            max-width: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #f9fafc;
            color: #333;
        }

        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #eaeaea;
            padding: 14px 0;
        }

        .navbar-brand {
            font-weight: 800;
            color: #001c40;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link {
            color: #0766a5;
            font-weight: 500;
            margin-left: 1rem;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #ff6b00;
        }

        .hero {
            background: linear-gradient(to right, #001c40, #0766a5);
            color: white;
            padding: 140px 0 100px;
        }

        .hero h1 {
            font-size: 2.8rem;
            line-height: 1.2;
        }

        .hero .btn {
            background-color: #ff6b00;
            color: white;
            font-weight: 600;
            padding: 14px 36px;
            border-radius: 50px;
            transition: all 0.3s ease-in-out;
        }

        .hero .btn:hover {
            background-color: #e55a00;
            transform: translateY(-2px);
        }

        .section-title {
            font-weight: 800;
            font-size: 2rem;
            color: #001c40;
        }

        .benefits-icon {
            font-size: 2.7rem;
            margin-bottom: 15px;
            color: #057ecf;
        }

        .feature-block {
            background: white;
            border-radius: 18px;
            padding: 32px 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease;
        }

        .feature-block:hover {
            transform: translateY(-4px);
        }

        .feature-block h6,
        .feature-block h5 {
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 10px;
            color: #333;
        }

        .cta-section {
            background-color: #ff6b00;
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2.2rem;
        }

        .cta-section .btn {
            background-color: white;
            color: #ff6b00;
            font-weight: 600;
            padding: 14px 36px;
            border-radius: 50px;
            transition: all 0.3s ease-in-out;
        }

        .cta-section .btn:hover {
            background-color: #f1f1f1;
            transform: translateY(-2px);
        }

        .mockup-img {
            max-width: 100%;
            border-radius: 20px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        }

        footer {
            background: #001c40;
            color: #ccc;
            font-size: 14px;
            padding: 30px 0;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-in-out;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            scroll-behavior: smooth;
            overflow-x: hidden;
            max-width: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #f9fafc;
            color: #333;
        }

        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #eaeaea;
            padding: 14px 0;
        }

        .navbar-brand {
            font-weight: 800;
            color: #001c40;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link {
            color: #0766a5;
            font-weight: 500;
            margin-left: 1rem;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #ff6b00;
        }

        .hero {
            background: linear-gradient(to right, #001c40, #0766a5);
            color: white;
            padding: 140px 0 100px;
        }

        .hero h1 {
            font-size: 2.8rem;
            line-height: 1.2;
        }

        .hero .btn {
            background-color: #ff6b00;
            color: white;
            font-weight: 600;
            padding: 14px 36px;
            border-radius: 50px;
            transition: all 0.3s ease-in-out;
        }

        .hero .btn:hover {
            background-color: #e55a00;
            transform: translateY(-2px);
        }

        .section-title {
            font-weight: 800;
            font-size: 2rem;
            color: #001c40;
        }

        .benefits-icon {
            font-size: 2.7rem;
            margin-bottom: 15px;
            color: #057ecf;
        }

        .feature-block {
            background: white;
            border-radius: 18px;
            padding: 32px 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease;
        }

        .feature-block:hover {
            transform: translateY(-4px);
        }

        .feature-block h6,
        .feature-block h5 {
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 10px;
            color: #333;
        }

        .cta-section {
            background-color: #ff6b00;
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2.2rem;
        }

        .cta-section .btn {
            background-color: white;
            color: #ff6b00;
            font-weight: 600;
            padding: 14px 36px;
            border-radius: 50px;
            transition: all 0.3s ease-in-out;
        }

        .cta-section .btn:hover {
            background-color: #f1f1f1;
            transform: translateY(-2px);
        }

        .mockup-img {
            max-width: 100%;
            border-radius: 20px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        }

        footer {
            background: #001c40;
            color: #ccc;
            font-size: 14px;
            padding: 30px 0;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-in-out;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .text-orange {
            color: #ff6b00;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/MenuziAppLogo.png') }}" width="70px" height="70px" alt="MenuziApp Logo">
            </a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#funciona">Como Funciona</a></li>
                    <li class="nav-item"><a class="nav-link" href="#funcionalidades">Funcionalidades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#publico">Para quem é</a></li>
                    <li class="nav-item"><a class="nav-link" href="#depoimentos">Depoimentos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#cta">Teste Grátis</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero text-center text-md-start">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-md-6">
                    <h1 class="display-5 fw-bold">Se preocupe com o seu cardápio,<br><span class="text-orange">o resto
                            é com o MenuziApp.</span></h1>
                    <p class="lead mt-3 mb-4">Sistema completo com gestão de pedidos, controle de caixa e cardápio
                        digital com visual de aplicativo.</p>
                    <a href="{{ route('register.tenant') }}" class="btn btn-lg shadow">Comece Grátis</a>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('images/menuziapp.png') }}" alt="Mockup" class="mockup-img mt-4 mt-md-0">
                </div>
            </div>
        </div>
    </section>

    <!-- Como Funciona -->
    <section id="funciona" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title mb-5">Como Funciona</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-block">
                        <div class="benefits-icon">📝</div>
                        <h5 class="fw-bold">Cadastre seu negócio</h5>
                        <p>Crie sua conta e configure seu cardápio com fotos e preços.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-block">
                        <div class="benefits-icon">📲</div>
                        <h5 class="fw-bold">Compartilhe com seus clientes</h5>
                        <p>Envie o link ou QR Code para acessarem seu menu digital.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-block">
                        <div class="benefits-icon">✅</div>
                        <h5 class="fw-bold">Receba e gerencie pedidos</h5>
                        <p>Controle os pedidos em tempo real, com painel de caixa e impressão.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Funcionalidades -->
    <section id="funcionalidades" class="py-5">
        <div class="container text-center">
            <h2 class="section-title mb-5">Funcionalidades Poderosas</h2>
            <div class="row g-4">
               
                <div class="col-md-4">
                    <div class="feature-block">
                        <div class="benefits-icon">💳</div>
                        <h6>Relatórios por forma de pagamento</h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-block">
                        <div class="benefits-icon">📈</div>
                        <h6>Relatórios de vendas e caixa</h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-block">
                        <div class="benefits-icon">🧾</div>
                        <h6>Impressão de pedidos e comandas</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Para quem é -->
    <section id="publico" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title mb-4">Ideal para...</h2>
            <p class="lead">Seja qual for o seu tipo de negócio gastronômico, o MenuziApp se adapta:</p>
            <div class="row g-4 mt-4">
                <div class="col-md-3">
                    <div class="feature-block">
                        <div class="benefits-icon">🍕</div>
                        <h6>Pizzarias</h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-block">
                        <div class="benefits-icon">🍔</div>
                        <h6>Lanchonetes</h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-block">
                        <div class="benefits-icon">🍜</div>
                        <h6>Restaurantes</h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-block">
                        <div class="benefits-icon">🧁</div>
                        <h6>Confeitarias e cafés</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos -->
    <section id="depoimentos" class="py-5">
        <div class="container text-center">
            <h2 class="section-title mb-5">Depoimentos de quem já usa</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-block">
                        <p>"Simplesmente fantástico! Meus pedidos triplicaram depois do MenuziApp."</p><strong>– João,
                            Hamburgueria do Zé</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-block">
                        <p>"Muito fácil de configurar e meus clientes adoram o cardápio online."</p><strong>– Camila,
                            Café da Praça</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-block">
                        <p>"Economizei com atendente e organizei meu caixa com facilidade."</p><strong>– Marcos,
                            Pizzaria da Vila</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section id="cta" class="cta-section text-center fade-up">
        <div class="container">
            <h2 class="display-6 fw-bold mb-3">Ganhe 7 dias gratuitos</h2>
            <p class="mb-4 lead">Comece a vender online agora mesmo com o sistema mais completo para delivery local.
            </p>
            <a href="{{ route('register.tenant') }}" class="btn btn-lg px-5 py-3">Criar Minha Loja</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <small>&copy; {{ date('Y') }} MenuziApp — Todos os direitos reservados.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const elements = document.querySelectorAll('.fade-up');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        elements.forEach(el => observer.observe(el));
    </script>

</body>

</html>
