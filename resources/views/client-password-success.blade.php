<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RepassesJá — Senha Criada</title>
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
      --success: #10b981;
      --success-bg: #f0fdf4;
      --success-dark: #16a34a;
      --danger: #dc2626;
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
      margin-bottom: 32px;
      text-decoration: none;
    }

    .logo img {
      height: 36px;
    }

    .card-container {
      width: 100%;
      max-width: 440px;
      background: #fff;
      border-radius: 20px;
      padding: 44px 40px;
      box-shadow: 0 4px 32px rgba(252,110,32,0.08), 0 1px 4px rgba(0,0,0,0.06);
      text-align: center;
    }

    .success-icon {
      width: 64px;
      height: 64px;
      border-radius: 9999px;
      background: var(--success-bg);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 0 0 6px rgba(16,185,129,0.08);
    }

    .success-icon svg {
      width: 32px;
      height: 32px;
    }

    h1 {
      font-size: 22px;
      font-weight: 800;
      color: var(--dark);
      letter-spacing: -0.4px;
      margin-bottom: 8px;
    }

    .subtitle {
      font-size: 13px;
      color: var(--gray-500);
      margin-bottom: 28px;
      line-height: 1.5;
    }

    .countdown {
      font-weight: 700;
      color: var(--primary);
    }

    .btn-primary {
      display: block;
      width: 100%;
      padding: 14px;
      border-radius: 10px;
      background: linear-gradient(135deg, #FC6E20 0%, #ff8c4a 100%);
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      border: none;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
      text-decoration: none;
      text-align: center;
      transition: opacity 0.18s, transform 0.15s, box-shadow 0.18s;
      box-shadow: 0 4px 14px rgba(252,110,32,0.30);
      letter-spacing: 0.1px;
    }

    .btn-primary:hover {
      opacity: 0.92;
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(252,110,32,0.38);
    }

    .btn-primary:active {
      transform: translateY(0);
    }
  </style>
</head>
<body>

  <a href="/" class="logo">
    @include('filament.brand-logo')
  </a>

  <div class="card-container">

    <div class="success-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>

    <h1>Senha criada com sucesso!</h1>
    <p class="subtitle">
      Você será redirecionado para o painel em <span id="countdown" class="countdown">5</span> segundos.
    </p>

    <a href="{{ route('filament.sindico.pages.dashboard') }}" class="btn-primary">
      Acessar Painel
    </a>

  </div>

  <script>
    let seconds = 5;
    const el = document.getElementById('countdown');
    const timer = setInterval(() => {
      seconds--;
      el.textContent = seconds;
      if (seconds <= 0) {
        clearInterval(timer);
        window.location.href = '{{ route('filament.sindico.pages.dashboard') }}';
      }
    }, 1000);
  </script>

</body>
</html>
