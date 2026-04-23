<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senha Criada - RepassesJá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f9fafb; }
        .fi-btn:hover { background-color: #e05a10; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4">

    <div class="w-full max-w-sm space-y-6">

        {{-- Brand --}}
        <div class="flex justify-center">
            <a href="/">
                @include('filament.brand-logo')
            </a>
        </div>

        {{-- Card --}}
        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">

            {{-- Ícone de sucesso --}}
            <div class="flex justify-center mb-4">
                <div style="background-color: #f0fdf4; border-radius: 9999px; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                         fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
            </div>

            <div class="text-center mb-6">
                <h1 class="text-gray-900 text-xl font-bold mb-1">Senha criada com sucesso!</h1>
                <p style="color: #6b7280; font-size: 0.875rem;">
                    Você será redirecionado para o login em <span id="countdown" class="font-semibold" style="color: #FC6E20;">5</span> segundos.
                </p>
            </div>

            <a
                href="{{ route('auth.login.sindico') }}"
                class="fi-btn block w-full text-center rounded-lg py-2.5 text-sm font-semibold text-white transition-colors"
                style="background-color: #FC6E20; text-decoration: none;"
            >
                Ir para o Login
            </a>

        </div>

    </div>

    <script>
        let seconds = 5;
        const el = document.getElementById('countdown');
        const timer = setInterval(() => {
            seconds--;
            el.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = '{{ route('auth.login.sindico') }}';
            }
        }, 1000);
    </script>
</body>
</html>
