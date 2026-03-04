<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RepasseJá — Gestão de Repasses para Mercados de Condomínio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FC6E20;
            --primary-dark: #e05a10;
            --primary-light: #ff8c4a;
            --accent: #FFE7D0;
            --dark: #1B1B1B;
            --gray-50: #faf8f6;
            --gray-100: #f2efec;
            --gray-200: #e2dfdb;
            --gray-400: #8a8785;
            --gray-500: #6b6866;
            --gray-600: #4a4745;
            --gray-700: #323232;
            --gray-900: #1B1B1B;
            --success: #10b981;
            --gradient: linear-gradient(135deg, #FC6E20 0%, #ff8c4a 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--gray-700);
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            transition: all 0.3s ease;
            background: transparent;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            padding: 0.6rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            color: var(--accent);
        }

        .nav-link {
            font-weight: 500;
            color: var(--gray-600) !important;
            margin: 0 0.3rem;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .btn-nav {
            background: var(--gradient);
            color: #fff !important;
            border: none;
            border-radius: 50px;
            padding: 0.55rem 1.6rem;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-nav:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(252, 110, 32, 0.4);
        }

        /* Hero */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(160deg, #faf8f6 0%, #FFF5ED 40%, #FFE7D0 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(252, 110, 32, 0.08) 0%, transparent 70%);
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 231, 208, 0.3) 0%, transparent 70%);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(252, 110, 32, 0.08);
            color: var(--primary);
            padding: 0.45rem 1.2rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(252, 110, 32, 0.15);
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--dark);
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 1.5rem;
        }

        .hero h1 span {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--gray-500);
            line-height: 1.7;
            margin-bottom: 2rem;
            max-width: 520px;
        }

        .btn-hero {
            background: var(--gradient);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 0.9rem 2.5rem;
            font-size: 1.05rem;
            font-weight: 700;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(252, 110, 32, 0.4);
            color: #fff;
        }

        .btn-hero-outline {
            background: transparent;
            color: var(--gray-700);
            border: 2px solid var(--gray-200);
            border-radius: 50px;
            padding: 0.85rem 2rem;
            font-size: 1.05rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-hero-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .hero-mockup {
            position: relative;
            z-index: 1;
        }

        .video-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.03);
            aspect-ratio: 16 / 9;
            background: var(--gray-100);
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .floating-card {
            position: absolute;
            background: #fff;
            border-radius: 16px;
            padding: 1rem 1.3rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            z-index: 2;
            animation: float 3s ease-in-out infinite;
        }

        .floating-card-1 {
            top: -15px;
            right: -25px;
        }

        .floating-card-2 {
            bottom: 30px;
            left: -30px;
            animation-delay: 1.5s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Stats */
        .stats-bar {
            background: #fff;
            border-top: 1px solid var(--gray-100);
            border-bottom: 1px solid var(--gray-100);
            padding: 3rem 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -1px;
        }

        .stat-number span {
            color: var(--primary);
        }

        .stat-label {
            color: var(--gray-500);
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 0.3rem;
        }

        /* Sections */
        section {
            padding: 6rem 0;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(252, 110, 32, 0.07);
            color: var(--primary);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -1px;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--gray-500);
            max-width: 600px;
            margin: 0 auto 3.5rem;
        }

        /* Problems */
        .problems {
            background: var(--dark);
            color: #fff;
        }

        .problems .section-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #f87171;
        }

        .problems .section-title {
            color: #fff;
        }

        .problems .section-subtitle {
            color: var(--gray-400);
        }

        .problem-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s;
        }

        .problem-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-4px);
        }

        .problem-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(248, 113, 113, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
            font-size: 1.3rem;
            color: #f87171;
        }

        .problem-card h5 {
            font-weight: 700;
            margin-bottom: 0.7rem;
            color: #fff;
        }

        .problem-card p {
            color: var(--gray-400);
            font-size: 0.92rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Features */
        .features {
            background: var(--gray-50);
        }

        .feature-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.2rem;
            height: 100%;
            border: 1px solid var(--gray-100);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .feature-card:hover::after {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(252, 110, 32, 0.1) 0%, rgba(255, 231, 208, 0.4) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.3rem;
            font-size: 1.4rem;
            color: var(--primary);
        }

        .feature-card h5 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.7rem;
        }

        .feature-card p {
            color: var(--gray-500);
            font-size: 0.92rem;
            line-height: 1.6;
            margin: 0;
        }

        /* How it works */
        .step-card {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--gradient);
            color: #fff;
            font-weight: 800;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 1;
        }

        .step-connector {
            position: absolute;
            top: 32px;
            left: 55%;
            width: 90%;
            height: 2px;
            background: var(--gray-200);
            z-index: 0;
        }

        .step-card h5 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.6rem;
        }

        .step-card p {
            color: var(--gray-500);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        /* Panels */
        .panels {
            background: linear-gradient(160deg, #FFF5ED 0%, #FFE7D0 50%, #faf8f6 100%);
        }

        .panel-showcase {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.08);
        }

        .panel-tabs {
            display: flex;
            background: var(--gray-50);
            padding: 0.5rem;
            gap: 0.5rem;
        }

        .panel-tab {
            flex: 1;
            padding: 1rem;
            border: none;
            background: transparent;
            border-radius: 12px;
            font-weight: 600;
            color: var(--gray-500);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .panel-tab.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .panel-tab i {
            margin-right: 0.4rem;
        }

        .panel-content {
            padding: 2.5rem;
        }

        .panel-content-item {
            display: none;
        }

        .panel-content-item.active {
            display: block;
            animation: fadeIn 0.4s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .panel-feature-list {
            list-style: none;
            padding: 0;
        }

        .panel-feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .panel-feature-list li:last-child {
            border-bottom: none;
        }

        .panel-feature-list .check {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 8px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            margin-top: 2px;
        }

        .panel-feature-list h6 {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.2rem;
        }

        .panel-feature-list p {
            color: var(--gray-500);
            font-size: 0.85rem;
            margin: 0;
        }

        /* Pricing */
        .pricing-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            border: 1px solid var(--gray-200);
            height: 100%;
            transition: all 0.3s;
            position: relative;
        }

        .pricing-card.featured {
            border-color: var(--primary);
            box-shadow: 0 20px 50px rgba(252, 110, 32, 0.15);
            transform: scale(1.03);
        }

        .pricing-card.featured .pricing-badge {
            display: inline-block;
        }

        .pricing-badge {
            display: none;
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gradient);
            color: #fff;
            padding: 0.3rem 1.2rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pricing-card h4 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.3rem;
        }

        .pricing-card .price {
            font-size: 3rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -2px;
            margin: 1.2rem 0 0.5rem;
        }

        .pricing-card .price span {
            font-size: 1rem;
            font-weight: 500;
            color: var(--gray-500);
            letter-spacing: 0;
        }

        .pricing-card .price-description {
            color: var(--gray-500);
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .pricing-features {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }

        .pricing-features li {
            padding: 0.5rem 0;
            color: var(--gray-600);
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .pricing-features li i {
            color: var(--success);
            font-size: 0.85rem;
        }

        .btn-pricing {
            width: 100%;
            padding: 0.85rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            border: 2px solid var(--gray-200);
            background: transparent;
            color: var(--dark);
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-pricing:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .pricing-card.featured .btn-pricing {
            background: var(--gradient);
            color: #fff;
            border: none;
        }

        .pricing-card.featured .btn-pricing:hover {
            box-shadow: 0 8px 25px rgba(252, 110, 32, 0.4);
            transform: translateY(-2px);
        }

        /* Testimonials */
        .testimonials {
            background: var(--gray-50);
        }

        .testimonial-card {
            background: #fff;
            border-radius: 20px;
            padding: 2rem;
            height: 100%;
            border: 1px solid var(--gray-100);
        }

        .testimonial-stars {
            color: #fbbf24;
            margin-bottom: 1rem;
        }

        .testimonial-card blockquote {
            color: var(--gray-600);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .testimonial-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: 0.9rem;
        }

        .testimonial-author h6 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.1rem;
            font-size: 0.9rem;
        }

        .testimonial-author small {
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        /* FAQ */
        .faq-item {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            margin-bottom: 0.8rem;
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-item:hover {
            border-color: var(--primary-light);
        }

        .faq-question {
            padding: 1.3rem 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--dark);
            user-select: none;
        }

        .faq-question i {
            transition: transform 0.3s;
            color: var(--gray-400);
        }

        .faq-item.open .faq-question i {
            transform: rotate(180deg);
            color: var(--primary);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-answer-inner {
            padding: 0 1.5rem 1.3rem;
            color: var(--gray-500);
            line-height: 1.7;
            font-size: 0.92rem;
        }

        /* CTA */
        .cta {
            background: var(--dark);
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(252, 110, 32, 0.2) 0%, transparent 70%);
        }

        .cta::after {
            content: '';
            position: absolute;
            bottom: -40%;
            right: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 231, 208, 0.3) 0%, transparent 70%);
        }

        .cta h2 {
            font-size: 2.8rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            margin-bottom: 1rem;
        }

        .cta p {
            color: var(--gray-400);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .btn-cta {
            background: #fff;
            color: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 1rem 2.8rem;
            font-size: 1.05rem;
            font-weight: 700;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
            color: var(--primary);
        }

        /* Footer */
        footer {
            background: var(--gray-900);
            color: var(--gray-400);
            padding: 4rem 0 2rem;
        }

        .footer-brand {
            font-weight: 800;
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: 0.8rem;
        }

        .footer-brand i {
            color: var(--accent);
        }

        footer h6 {
            font-weight: 700;
            color: #fff;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.6rem;
        }

        .footer-links a {
            color: var(--gray-400);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 2rem;
            margin-top: 3rem;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            .section-title {
                font-size: 2rem;
            }
            .cta h2 {
                font-size: 2rem;
            }
            .pricing-card.featured {
                transform: scale(1);
            }
            .step-connector {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .hero h1 {
                font-size: 2rem;
            }
            section {
                padding: 4rem 0;
            }
            .hero {
                min-height: auto;
                padding: 8rem 0 4rem;
            }
            .floating-card {
                display: none;
            }
        }

        /* Scroll animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-arrow-left-right"></i> RepassesJá
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto me-3 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#problemas">Problemas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#funcionalidades">Funcionalidades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#como-funciona">Como funciona</a></li>
                    <li class="nav-item"><a class="nav-link" href="#paineis">Painéis</a></li>
                    <li class="nav-item"><a class="nav-link" href="#precos">Preços</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                </ul>
                <a href="{{ route('filament.painel.auth.login') }}" class="btn btn-nav">Acessar</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="hero-badge">
                        <i class="bi bi-lightning-charge-fill"></i> Integrado com VMPAY — Vendas automáticas
                    </div>
                    <h1>Repasses de condomínio <span>100% automáticos</span></h1>
                    <p>Integrado com a API da VMPAY, o sistema puxa automaticamente todas as vendas dos seus condomínios. Sem digitação manual, sem erros. Transparência total para síndicos.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#" class="btn-hero">
                            Teste grátis por 10 dias <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#como-funciona" class="btn-hero-outline">
                            <i class="bi bi-play-circle"></i> Como funciona
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-4 mt-4">
                        <div class="d-flex">
                            <div class="mockup-avatar" style="background: var(--primary); width: 32px; height: 32px; font-size: 0.7rem; margin-right: -8px; border: 2px solid #fff; position: relative; z-index: 3;">RC</div>
                            <div class="mockup-avatar" style="background: var(--accent); width: 32px; height: 32px; font-size: 0.7rem; margin-right: -8px; border: 2px solid #fff; position: relative; z-index: 2;">MS</div>
                            <div class="mockup-avatar" style="background: var(--success); width: 32px; height: 32px; font-size: 0.7rem; border: 2px solid #fff; position: relative; z-index: 1;">JL</div>
                        </div>
                        <small class="text-muted"><strong style="color: var(--dark);">+200 mercados</strong> já utilizam</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-mockup">
                        <div class="floating-card floating-card-1">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16,185,129,0.1); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-check-lg" style="color: var(--success); font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--dark);">Repasse confirmado!</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Cond. Flores — R$ 2.340</div>
                                </div>
                            </div>
                        </div>
                        <div class="video-wrapper">
                            <iframe src="https://www.youtube.com/embed/kPa7bsKwL-c" title="Veja como funciona o RepasseJá" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <div class="stats-bar">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">+200<span>+</span></div>
                        <div class="stat-label">Mercados ativos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">R$<span>5M</span></div>
                        <div class="stat-label">Repassados por mês</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">1.500<span>+</span></div>
                        <div class="stat-label">Síndicos conectados</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">99<span>%</span></div>
                        <div class="stat-label">Satisfação dos clientes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Problems -->
    <section class="problems" id="problemas">
        <div class="container text-center">
            <div class="section-badge"><i class="bi bi-exclamation-triangle-fill"></i> O Problema</div>
            <h2 class="section-title">Gerenciar repasses manualmente é um pesadelo</h2>
            <p class="section-subtitle">Se você reconhece alguma dessas situações, o RepasseJá foi feito para você.</p>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div class="problem-card">
                        <div class="problem-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                        <h5>Planilhas intermináveis</h5>
                        <p>Controlar repasses em planilhas é lento, propenso a erros e impossível de escalar conforme seu negócio cresce.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="problem-card">
                        <div class="problem-icon"><i class="bi bi-people"></i></div>
                        <h5>Síndicos sem visibilidade</h5>
                        <p>Síndicos cobram informações por WhatsApp, e-mail e telefone. Você perde tempo respondendo um a um.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="problem-card">
                        <div class="problem-icon"><i class="bi bi-exclamation-diamond"></i></div>
                        <h5>Falta de transparência</h5>
                        <p>Sem um registro claro e acessível, a confiança entre mercado e condomínio fica comprometida.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="funcionalidades">
        <div class="container text-center">
            <div class="section-badge"><i class="bi bi-stars"></i> Funcionalidades</div>
            <h2 class="section-title">Tudo que você precisa em um só lugar</h2>
            <p class="section-subtitle">Funcionalidades pensadas para simplificar a rotina de donos de mercado e síndicos.</p>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-cloud-arrow-down"></i></div>
                        <h5>Integração automática VMPAY</h5>
                        <p>Todas as vendas dos condomínios são puxadas automaticamente da API VMPAY. Zero digitação manual.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-calculator"></i></div>
                        <h5>Cálculos automáticos</h5>
                        <p>Taxas de máquina, impostos e percentual do condomínio calculados automaticamente sobre as vendas importadas.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-layout-text-window-reverse"></i></div>
                        <h5>Painel do síndico</h5>
                        <p>Cada síndico acessa seu próprio painel com histórico de repasses, valores e comprovantes.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
                        <h5>Relatórios detalhados</h5>
                        <p>Visualize relatórios por período, condomínio ou status. Dados reais vindos direto da VMPAY.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                        <h5>Anexo de comprovantes</h5>
                        <p>Anexe comprovantes bancários diretamente ao repasse. Tudo documentado em um clique.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <h5>Segurança e controle</h5>
                        <p>Dados criptografados, controle de permissões e log de todas as ações realizadas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="como-funciona">
        <div class="container text-center">
            <div class="section-badge"><i class="bi bi-gear"></i> Como funciona</div>
            <h2 class="section-title">Comece em 3 passos simples</h2>
            <p class="section-subtitle">Do cadastro ao primeiro repasse em menos de 5 minutos.</p>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4 reveal">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-connector d-none d-md-block"></div>
                        <h5>Conecte sua conta VMPAY</h5>
                        <p>Crie sua conta, configure seu token da API VMPAY e todos os seus condomínios são importados automaticamente.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-connector d-none d-md-block"></div>
                        <h5>Vendas sincronizadas</h5>
                        <p>O sistema puxa todas as vendas da VMPAY automaticamente e calcula os valores de repasse com precisão.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h5>Repasse e transparência</h5>
                        <p>Gere o repasse, anexe comprovantes e o síndico acompanha tudo pelo painel dele.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Panels -->
    <section class="panels" id="paineis">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge"><i class="bi bi-window-stack"></i> Painéis</div>
                <h2 class="section-title">Dois painéis, uma plataforma</h2>
                <p class="section-subtitle">Cada tipo de usuário tem acesso às funcionalidades certas para sua necessidade.</p>
            </div>
            <div class="row justify-content-center reveal">
                <div class="col-lg-10">
                    <div class="panel-showcase">
                        <div class="panel-tabs">
                            <button class="panel-tab active" data-panel="owner">
                                <i class="bi bi-shop"></i> Painel do Dono do Mercado
                            </button>
                            <button class="panel-tab" data-panel="syndic">
                                <i class="bi bi-person-badge"></i> Painel do Síndico
                            </button>
                        </div>
                        <div class="panel-content">
                            <div class="panel-content-item active" id="panel-owner">
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-4 mb-md-0">
                                        <h4 style="font-weight: 800; color: var(--dark); margin-bottom: 0.5rem;">Controle total com dados da VMPAY</h4>
                                        <p style="color: var(--gray-500); font-size: 0.95rem;">Vendas importadas automaticamente da API VMPAY. Crie repasses com valores precisos e acompanhe tudo em tempo real.</p>
                                    </div>
                                    <div class="col-md-7">
                                        <ul class="panel-feature-list">
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Sincronização automática VMPAY</h6>
                                                    <p>Vendas de todos os condomínios importadas automaticamente. Sem digitação manual.</p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Criação de repasses</h6>
                                                    <p>Registre novos repasses com valor, data, comprovante e observações.</p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Gestão de condomínios e síndicos</h6>
                                                    <p>Cadastre condomínios, vincule síndicos e gerencie permissões de acesso.</p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Relatórios e exportação</h6>
                                                    <p>Gere relatórios por período e exporte em PDF ou Excel.</p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-content-item" id="panel-syndic">
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-4 mb-md-0">
                                        <h4 style="font-weight: 800; color: var(--dark); margin-bottom: 0.5rem;">Transparência para o síndico</h4>
                                        <p style="color: var(--gray-500); font-size: 0.95rem;">O síndico acompanha cada repasse recebido, com comprovantes e histórico completo, sem precisar pedir nada.</p>
                                    </div>
                                    <div class="col-md-7">
                                        <ul class="panel-feature-list">
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Histórico de repasses</h6>
                                                    <p>Visualize todos os repasses recebidos com data, valor e comprovante.</p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Comprovantes acessíveis</h6>
                                                    <p>Baixe ou visualize comprovantes bancários a qualquer momento.</p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Notificações por e-mail</h6>
                                                    <p>Receba um aviso sempre que um novo repasse for registrado.</p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="check"><i class="bi bi-check-lg"></i></div>
                                                <div>
                                                    <h6>Acesso simplificado</h6>
                                                    <p>Login fácil, sem necessidade de instalar nada. Acesse pelo navegador.</p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="precos">
        <div class="container text-center">
            <div class="section-badge"><i class="bi bi-tag"></i> Preço</div>
            <h2 class="section-title">Simples e sem surpresas</h2>
            <p class="section-subtitle">Um único plano com acesso completo. Teste grátis por 10 dias, sem cartão de crédito.</p>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5 reveal">
                    <div class="pricing-card">
                        <h4>Teste Grátis</h4>
                        <p style="color: var(--gray-500); font-size: 0.88rem;">Experimente tudo por 10 dias</p>
                        <div class="price">R$0 <span>/ 10 dias</span></div>
                        <p class="price-description">Acesso completo a todas as funcionalidades</p>
                        <ul class="pricing-features">
                            <li><i class="bi bi-check-circle-fill"></i> Integração VMPAY completa</li>
                            <li><i class="bi bi-check-circle-fill"></i> Condomínios ilimitados</li>
                            <li><i class="bi bi-check-circle-fill"></i> Sincronização automática de vendas</li>
                            <li><i class="bi bi-check-circle-fill"></i> Painel do síndico</li>
                            <li><i class="bi bi-check-circle-fill"></i> Relatórios e comprovantes</li>
                            <li><i class="bi bi-check-circle-fill"></i> Suporte completo</li>
                        </ul>
                        <button class="btn-pricing">Começar grátis</button>
                    </div>
                </div>
                <div class="col-lg-5 reveal">
                    <div class="pricing-card featured">
                        <div class="pricing-badge">Plano Único</div>
                        <h4>Completo</h4>
                        <p style="color: var(--gray-500); font-size: 0.88rem;">Tudo que você precisa para gerenciar seus repasses</p>
                        <div class="price">R$29,99 <span>/mês</span></div>
                        <p class="price-description">Mesmo acesso do teste grátis, sem limite de tempo</p>
                        <ul class="pricing-features">
                            <li><i class="bi bi-check-circle-fill"></i> Integração VMPAY completa</li>
                            <li><i class="bi bi-check-circle-fill"></i> Condomínios ilimitados</li>
                            <li><i class="bi bi-check-circle-fill"></i> Sincronização automática de vendas</li>
                            <li><i class="bi bi-check-circle-fill"></i> Painel do síndico</li>
                            <li><i class="bi bi-check-circle-fill"></i> Relatórios e comprovantes</li>
                            <li><i class="bi bi-check-circle-fill"></i> Cálculos automáticos de taxas</li>
                            <li><i class="bi bi-check-circle-fill"></i> Suporte prioritário</li>
                        </ul>
                        <button class="btn-pricing">Assinar agora</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container text-center">
            <div class="section-badge"><i class="bi bi-chat-quote"></i> Depoimentos</div>
            <h2 class="section-title">Quem usa, recomenda</h2>
            <p class="section-subtitle">Veja o que nossos clientes dizem sobre o RepasseJá.</p>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <blockquote>"Antes eu passava horas por semana montando planilhas de repasse. Com o RepasseJá, faço em minutos e os síndicos nem precisam me ligar mais."</blockquote>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar" style="background: var(--primary);">RC</div>
                            <div>
                                <h6>Roberto Carlos</h6>
                                <small>Dono de mercado — SP</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <blockquote>"Como síndica, ter acesso aos comprovantes de repasse a qualquer hora me dá muito mais segurança na prestação de contas."</blockquote>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar" style="background: var(--accent);">MS</div>
                            <div>
                                <h6>Maria Silva</h6>
                                <small>Síndica — Cond. das Flores</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <blockquote>"Gerencio 3 mercados e 22 condomínios. Sem o RepasseJá seria impossível manter tudo organizado. Ferramenta essencial."</blockquote>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar" style="background: var(--success);">JL</div>
                            <div>
                                <h6>José Lima</h6>
                                <small>Rede de mercados — RJ</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge"><i class="bi bi-question-circle"></i> FAQ</div>
                <h2 class="section-title">Perguntas frequentes</h2>
                <p class="section-subtitle">Tire suas dúvidas sobre o RepasseJá.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-item">
                        <div class="faq-question">
                            O que é um repasse de mercado de condomínio?
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                É a transferência de valores que o mercado do condomínio faz ao condomínio referente às vendas realizadas. O dono do mercado repassa periodicamente uma porcentagem ou valor fixo ao condomínio, e o síndico precisa acompanhar esses valores.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            O síndico precisa pagar algo para usar a plataforma?
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                Não! O acesso do síndico é totalmente gratuito. Ele recebe um convite do dono do mercado e pode acessar seu painel sem nenhum custo.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Posso testar antes de assinar?
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                Sim! Oferecemos 10 dias de teste grátis com acesso completo a todas as funcionalidades — igual ao plano pago. Sem precisar de cartão de crédito.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como funciona a integração com a VMPAY?
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                Basta configurar seu token da API VMPAY no sistema. A partir daí, todas as vendas dos seus condomínios são importadas automaticamente, com cálculo de taxas e valores de repasse feitos de forma precisa.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Os dados são seguros?
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                Sim. Utilizamos criptografia de ponta a ponta, servidores seguros e controle rigoroso de acesso. Seus dados financeiros estão protegidos com os mais altos padrões de segurança.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como o síndico acessa o painel?
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                O dono do mercado cadastra o síndico na plataforma e ele recebe um e-mail com link de acesso. Basta clicar, criar uma senha e pronto — acesso pelo navegador, sem instalar nada.
                            </div>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Posso cancelar a qualquer momento?
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">
                                Sim, sem multa e sem burocracia. Você pode cancelar seu plano a qualquer momento diretamente pelo painel. Seus dados ficam disponíveis por 30 dias após o cancelamento.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="container text-center position-relative" style="z-index: 1;">
            <h2 class="reveal">Pronto para simplificar<br>seus repasses?</h2>
            <p class="reveal">Integração automática com VMPAY, vendas sincronizadas<br>e repasses calculados sem esforço. Teste grátis por 10 dias.</p>
            <a href="#" class="btn-cta reveal">
                Começar grátis por 10 dias <i class="bi bi-arrow-right"></i>
            </a>
            <p class="mt-3 reveal" style="color: var(--gray-500); font-size: 0.85rem;">
                <i class="bi bi-credit-card"></i> Sem cartão de crédito &nbsp;•&nbsp;
                <i class="bi bi-clock"></i> Acesso completo por 10 dias &nbsp;•&nbsp;
                <i class="bi bi-tag"></i> Depois apenas R$29,99/mês
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <i class="bi bi-arrow-left-right"></i> RepasseJá
                    </div>
                    <p style="font-size: 0.9rem; max-width: 280px;">A plataforma que conecta donos de mercados de condomínio e síndicos com transparência e eficiência.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Produto</h6>
                    <ul class="footer-links">
                        <li><a href="#funcionalidades">Funcionalidades</a></li>
                        <li><a href="#precos">Preços</a></li>
                        <li><a href="#paineis">Painéis</a></li>
                        <li><a href="#">Changelog</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Empresa</h6>
                    <ul class="footer-links">
                        <li><a href="#">Sobre nós</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Carreiras</a></li>
                        <li><a href="#">Contato</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Suporte</h6>
                    <ul class="footer-links">
                        <li><a href="#">Central de ajuda</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#">Status</a></li>
                        <li><a href="#">API Docs</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Legal</h6>
                    <ul class="footer-links">
                        <li><a href="#">Termos de uso</a></li>
                        <li><a href="#">Privacidade</a></li>
                        <li><a href="#">LGPD</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center">
                <p class="mb-0" style="font-size: 0.85rem;">&copy; 2026 RepasseJá. Todos os direitos reservados.</p>
                <div class="d-flex gap-3">
                    <a href="#" style="color: var(--gray-400); font-size: 1.1rem;"><i class="bi bi-instagram"></i></a>
                    <a href="#" style="color: var(--gray-400); font-size: 1.1rem;"><i class="bi bi-linkedin"></i></a>
                    <a href="#" style="color: var(--gray-400); font-size: 1.1rem;"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Close mobile menu
                    const collapse = document.querySelector('.navbar-collapse');
                    if (collapse.classList.contains('show')) {
                        bootstrap.Collapse.getInstance(collapse).hide();
                    }
                }
            });
        });

        // Panel tabs
        document.querySelectorAll('.panel-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.panel-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.panel-content-item').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('panel-' + tab.dataset.panel).classList.add('active');
            });
        });

        // FAQ accordion
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                const answer = item.querySelector('.faq-answer');
                const isOpen = item.classList.contains('open');

                // Close all
                document.querySelectorAll('.faq-item').forEach(i => {
                    i.classList.remove('open');
                    i.querySelector('.faq-answer').style.maxHeight = null;
                });

                // Open clicked
                if (!isOpen) {
                    item.classList.add('open');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });

        // Scroll reveal
        const revealElements = document.querySelectorAll('.reveal');
        const revealOnScroll = () => {
            revealElements.forEach(el => {
                const top = el.getBoundingClientRect().top;
                if (top < window.innerHeight - 80) {
                    el.classList.add('visible');
                }
            });
        };
        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll();

        // Stat counter animation
        const animateCounters = () => {
            document.querySelectorAll('.stat-number').forEach(counter => {
                const text = counter.textContent;
                if (counter.dataset.animated) return;
                const top = counter.getBoundingClientRect().top;
                if (top < window.innerHeight - 50) {
                    counter.dataset.animated = 'true';
                }
            });
        };
        window.addEventListener('scroll', animateCounters);
    </script>
</body>
</html>
