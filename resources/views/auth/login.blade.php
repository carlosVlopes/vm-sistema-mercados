<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RepassesJá — Acessar Painel</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary: #FC6E20;
      --primary-dark: #e05a10;
      --primary-light: #ff8c4a;
      --accent: #FFE7D0;
      --dark: #1B1B1B;
      --gray-700: #323232;
      --gray-600: #4a4745;
      --gray-500: #6b6866;
      --gray-400: #8a8785;
      --gray-200: #e2dfdb;
      --gray-100: #f2efec;
      --gray-50: #faf8f6;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(160deg, #faf8f6 0%, #FFF5ED 40%, #FFE7D0 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 32px 16px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 48px;
      text-decoration: none;
    }

    .logo img {
      height: 36px;
    }

    .card-container {
      width: 100%;
      max-width: 560px;
      background: #fff;
      border-radius: 20px;
      padding: 48px 40px;
      box-shadow: 0 4px 32px rgba(252,110,32,0.08), 0 1px 4px rgba(0,0,0,0.06);
    }

    .heading {
      text-align: center;
      margin-bottom: 8px;
    }

    .heading h1 {
      font-size: 24px;
      font-weight: 800;
      color: var(--dark);
      letter-spacing: -0.5px;
    }

    .heading p {
      font-size: 14px;
      color: var(--gray-500);
      margin-top: 6px;
    }

    .divider {
      height: 1px;
      background: var(--gray-100);
      margin: 28px 0;
    }

    .options {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .option-btn {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 22px 24px;
      border-radius: 14px;
      border: 1.5px solid var(--gray-200);
      background: #fff;
      cursor: pointer;
      text-decoration: none;
      transition: border-color 0.18s, box-shadow 0.18s, transform 0.15s, background 0.18s;
    }

    .option-btn:hover {
      border-color: var(--primary);
      box-shadow: 0 4px 20px rgba(252,110,32,0.12);
      transform: translateY(-2px);
      background: #fffbf8;
    }

    .option-btn:active {
      transform: translateY(0px);
    }

    .option-icon {
      width: 52px;
      height: 52px;
      border-radius: 12px;
      background: var(--accent);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .option-icon svg {
      width: 26px;
      height: 26px;
    }

    .option-text {
      flex: 1;
      text-align: left;
    }

    .option-text strong {
      display: block;
      font-size: 16px;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 2px;
    }

    .option-text span {
      font-size: 13px;
      color: var(--gray-500);
    }

    .option-arrow {
      color: var(--gray-400);
      transition: color 0.18s, transform 0.18s;
    }

    .option-btn:hover .option-arrow {
      color: var(--primary);
      transform: translateX(4px);
    }

    .footer-note {
      text-align: center;
      margin-top: 32px;
      font-size: 12px;
      color: var(--gray-400);
    }

    .footer-note a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }
  </style>
</head>
<body>

  <a href="{{ route('home') }}" class="logo">
    @include('filament.brand-logo')
  </a>

  <div class="card-container">
    <div class="heading">
      <h1>Bem-vindo de volta!</h1>
      <p>Selecione seu tipo de acesso para continuar</p>
    </div>

    <div class="divider"></div>

    <div class="options">

      <a href="{{ route('auth.login.mercado') }}" class="option-btn">
        <div class="option-icon">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" stroke="#FC6E20" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 22V12h6v10" stroke="#FC6E20" stroke-width="2" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="option-text">
          <strong>Dono do Mercado</strong>
          <span>Acesse o painel do seu estabelecimento</span>
        </div>
        <svg class="option-arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M8 5l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>

      <a href="{{ route('auth.login.sindico') }}" class="option-btn">
        <div class="option-icon">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="3" stroke="#FC6E20" stroke-width="2"/>
            <path d="M7 9h10M7 13h6" stroke="#FC6E20" stroke-width="2" stroke-linecap="round"/>
            <circle cx="17" cy="15" r="2" stroke="#FC6E20" stroke-width="2"/>
          </svg>
        </div>
        <div class="option-text">
          <strong>Síndico</strong>
          <span>Acesse o painel de gestão do condomínio</span>
        </div>
        <svg class="option-arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M8 5l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>

    </div>
  </div>

  <p class="footer-note">
    Ainda não tem conta? <a href="{{ route('auth.register') }}">Cadastre-se</a>
  </p>

</body>
</html>
