<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RepassesJá — Login Síndico</title>
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
    }

    .card-header {
      text-align: center;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--accent);
      color: var(--primary);
      font-size: 12px;
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 20px;
      margin-bottom: 16px;
    }

    .badge svg { width: 13px; height: 13px; }

    h1 {
      font-size: 22px;
      font-weight: 800;
      color: var(--dark);
      letter-spacing: -0.4px;
      margin-bottom: 4px;
    }

    .subtitle {
      font-size: 13px;
      color: var(--gray-500);
      margin-bottom: 28px;
    }

    .divider {
      height: 1px;
      background: var(--gray-100);
      margin-bottom: 28px;
    }

    .alert {
      padding: 10px 12px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 18px;
    }

    .alert-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: var(--danger);
    }

    .alert-success {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      color: #15803d;
    }

    .form-group {
      margin-bottom: 18px;
    }

    label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 6px;
    }

    input[type="email"],
    input[type="password"],
    input[type="text"] {
      width: 100%;
      padding: 12px 14px;
      border-radius: 10px;
      border: 1.5px solid var(--gray-200);
      font-size: 14px;
      font-family: 'Inter', sans-serif;
      color: var(--dark);
      background: var(--gray-50);
      transition: border-color 0.18s, box-shadow 0.18s;
      outline: none;
    }

    input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(252,110,32,0.10);
      background: #fff;
    }

    .field-error {
      color: var(--danger);
      font-size: 12px;
      margin-top: 6px;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper input {
      padding-right: 42px;
    }

    .toggle-pw {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: var(--gray-400);
      padding: 0;
      display: flex;
      align-items: center;
    }

    .toggle-pw:hover { color: var(--primary); }

    .row-between {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      margin-top: 4px;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: 13px;
      color: var(--gray-600);
      cursor: pointer;
      user-select: none;
    }

    .remember input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: var(--primary);
      cursor: pointer;
    }

    .forgot {
      font-size: 13px;
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }

    .forgot:hover { text-decoration: underline; }

    .btn-primary {
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

    .back-link {
      text-align: center;
      margin-top: 24px;
      font-size: 13px;
      color: var(--gray-500);
    }

    .back-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }

    .back-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <a href="{{ route('auth.login') }}" class="logo">
    @include('filament.brand-logo')
  </a>

  <div class="card-container">

    <div class="card-header">
      <div class="badge">
        <svg viewBox="0 0 24 24" fill="none">
          <rect x="3" y="3" width="18" height="18" rx="3" stroke="#FC6E20" stroke-width="2.2"/>
          <path d="M7 9h10M7 13h6" stroke="#FC6E20" stroke-width="2.2" stroke-linecap="round"/>
          <circle cx="17" cy="15" r="2" stroke="#FC6E20" stroke-width="2"/>
        </svg>
        Síndico
      </div>
      <h1>Acesse seu painel</h1>
      <p class="subtitle">Entre com suas credenciais para ver seus repasses</p>
    </div>

    <div class="divider"></div>

    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('auth.login.sindico.submit') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="seu@email.com" autocomplete="email" required />
        @error('email')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group">
        <label for="password">Senha</label>
        <div class="password-wrapper">
          <input type="password" name="password" id="password" placeholder="••••••••" autocomplete="current-password" required />
          <button class="toggle-pw" type="button" onclick="togglePw()" aria-label="Mostrar/ocultar senha">
            <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        @error('password')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="row-between">
        <label class="remember">
          <input type="checkbox" name="remember" /> Lembrar de mim
        </label>
        <a href="#" class="forgot">Esqueci minha senha</a>
      </div>

      <button type="submit" class="btn-primary">Entrar no Painel</button>
    </form>

  </div>

  <p class="back-link">
    <a href="{{ route('auth.login') }}">← Voltar à seleção de acesso</a>
  </p>

  <script>
    function togglePw() {
      const pw = document.getElementById('password');
      pw.type = pw.type === 'password' ? 'text' : 'password';
    }
  </script>

</body>
</html>
