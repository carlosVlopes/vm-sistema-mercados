<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="referrer" content="no-referrer">
  <title>RepassesJá — Definir Senha</title>
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

    .required {
      color: var(--danger);
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

    input.has-error {
      border-color: var(--danger);
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
      margin-top: 6px;
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

    <div class="card-header">
      <div class="badge">
        <svg viewBox="0 0 24 24" fill="none">
          <rect x="3" y="3" width="18" height="18" rx="3" stroke="#FC6E20" stroke-width="2.2"/>
          <path d="M7 9h10M7 13h6" stroke="#FC6E20" stroke-width="2.2" stroke-linecap="round"/>
          <circle cx="17" cy="15" r="2" stroke="#FC6E20" stroke-width="2"/>
        </svg>
        Síndico
      </div>
      <h1>Olá, {{ $user->name }}</h1>
      <p class="subtitle">Defina sua senha para acessar o sistema</p>
    </div>

    <div class="divider"></div>

    <x-alert-popup />

    <form action="{{ route('registrar-senha.store', ['token' => $token]) }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="password">Senha <span class="required">*</span></label>
        <div class="password-wrapper">
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Mín. 8 caracteres, maiúscula, minúscula e número"
            minlength="8"
            autocomplete="new-password"
            required
            class="{{ $errors->has('password') ? 'has-error' : '' }}"
          />
          <button class="toggle-pw" type="button" onclick="togglePassword('password', 'eye-password')" aria-label="Mostrar/ocultar senha">
            <svg id="eye-password" width="18" height="18" viewBox="0 0 24 24" fill="none">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        @error('password')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group">
        <label for="password_confirm">Confirmar Senha <span class="required">*</span></label>
        <div class="password-wrapper">
          <input
            type="password"
            name="password_confirm"
            id="password_confirm"
            placeholder="••••••••"
            autocomplete="new-password"
            required
            class="{{ $errors->has('password_confirm') ? 'has-error' : '' }}"
          />
          <button class="toggle-pw" type="button" onclick="togglePassword('password_confirm', 'eye-confirm')" aria-label="Mostrar/ocultar senha">
            <svg id="eye-confirm" width="18" height="18" viewBox="0 0 24 24" fill="none">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        @error('password_confirm')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" class="btn-primary">Salvar Senha</button>
    </form>

  </div>

  <script>
    const eyeOpen = `<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>`;
    const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-7-11-7a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>`;

    function togglePassword(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      icon.innerHTML = isHidden ? eyeClosed : eyeOpen;
    }
  </script>

</body>
</html>
