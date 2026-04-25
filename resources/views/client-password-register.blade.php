<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <title>Definir Senha - RepassesJá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f9fafb; }
        input::placeholder { color: #9ca3af; }
        .fi-input:focus {
            outline: none;
            border-color: #FC6E20;
            box-shadow: 0 0 0 3px rgba(252, 110, 32, 0.15);
        }
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

            <div class="mb-6 text-center">
                <h1 class="text-gray-900 text-xl font-bold mb-1">Olá, {{ $user->name }}</h1>
                <p style="color: #6b7280; font-size: 0.875rem;">
                    Defina sua senha para acessar o sistema.
                </p>
            </div>

            <x-alert-popup />

            <form action="{{ route('registrar-senha.store', ['token' => $token]) }}" method="POST" class="space-y-4">
                @csrf

                {{-- Senha --}}
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-1.5">
                        Senha <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Mín. 8 caracteres, maiúscula, minúscula e número"
                            minlength="8"
                            class="fi-input w-full rounded-lg px-3 py-2.5 pr-10 text-sm text-gray-900 transition"
                            style="background-color: #ffffff; border: 1px solid {{ $errors->has('password') ? '#ef4444' : '#d1d5db' }};"
                        >
                        <button type="button" onclick="togglePassword('password', 'eye-password')"
                                class="absolute inset-y-0 right-0 flex items-center px-3"
                                style="color: #9ca3af; background: none; border: none; cursor: pointer;">
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

                {{-- Confirmar Senha --}}
                <div>
                    <label for="password_confirm" class="block text-gray-700 text-sm font-semibold mb-1.5">
                        Confirmar Senha <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirm"
                            id="password_confirm"
                            placeholder="••••••••"
                            class="fi-input w-full rounded-lg px-3 py-2.5 pr-10 text-sm text-gray-900 transition"
                            style="background-color: #ffffff; border: 1px solid {{ $errors->has('password_confirm') ? '#ef4444' : '#d1d5db' }};"
                        >
                        <button type="button" onclick="togglePassword('password_confirm', 'eye-confirm')"
                                class="absolute inset-y-0 right-0 flex items-center px-3"
                                style="color: #9ca3af; background: none; border: none; cursor: pointer;">
                            <svg id="eye-confirm" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirm')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="fi-btn w-full rounded-lg py-2.5 text-sm font-semibold text-white transition-colors mt-2"
                    style="background-color: #FC6E20; border: none; cursor: pointer;"
                >
                    Salvar Senha
                </button>

            </form>
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
