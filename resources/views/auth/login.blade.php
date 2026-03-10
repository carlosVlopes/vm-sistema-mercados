<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — RepassesJá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(160deg, #faf8f6 0%, #FFF5ED 40%, #FFE7D0 100%); }
        .fi-input:focus { outline: none; border-color: #FC6E20; box-shadow: 0 0 0 3px rgba(252, 110, 32, 0.15); }
        .btn-primary { background-color: #FC6E20; }
        .btn-primary:hover { background-color: #e05a10; }
        .link-primary { color: #FC6E20; }
        .link-primary:hover { color: #e05a10; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4">

    <div class="w-full max-w-sm space-y-6">

        {{-- Brand --}}
        <div class="flex justify-center">
            <a href="{{ route('home') }}">
                @include('filament.brand-logo')
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">

            <div class="mb-6 text-center">
                <h1 class="text-gray-900 text-xl font-bold mb-1">Bem-vindo de volta</h1>
                <p class="text-gray-500 text-sm">Entre com suas credenciais para acessar o painel.</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('auth.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-1.5">E-mail</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="seu@email.com"
                        required
                        class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                    >
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Senha --}}
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-1.5">Senha</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            required
                            class="fi-input w-full rounded-lg px-3 py-2.5 pr-10 text-sm text-gray-900 transition border border-gray-300 bg-white"
                        >
                        <button type="button" onclick="togglePassword('password', 'eye-password')"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 bg-transparent border-none cursor-pointer">
                            <svg id="eye-password" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lembrar --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                        Lembrar-me
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full rounded-lg py-2.5 text-sm font-semibold text-white transition-colors cursor-pointer">
                    Entrar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Ainda não tem conta?
                    <a href="{{ route('auth.register') }}" class="link-primary font-semibold">Cadastre-se</a>
                </p>
            </div>

        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">&larr; Voltar ao site</a>
        </div>

    </div>

    <script>
        const eyeOpen = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
        const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;

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
