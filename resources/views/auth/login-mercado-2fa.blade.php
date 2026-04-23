<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação em duas etapas — RepassesJá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(160deg, #faf8f6 0%, #FFF5ED 40%, #FFE7D0 100%); }
        .fi-input:focus { outline: none; border-color: #FC6E20; box-shadow: 0 0 0 3px rgba(252,110,32,0.15); }
        .btn-primary { background-color: #FC6E20; }
        .btn-primary:hover { background-color: #e05a10; }
        .link-primary { color: #FC6E20; }
        .link-primary:hover { color: #e05a10; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-8">
    <div class="w-full max-w-md space-y-6">
        <div class="flex justify-center">
            @include('filament.brand-logo')
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
            <div class="mb-6 text-center">
                <h1 class="text-gray-900 text-xl font-bold mb-1">Verificação em duas etapas</h1>
                <p class="text-gray-500 text-sm">
                    Abra o aplicativo autenticador e digite o código de 6 dígitos.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('auth.login.mercado.2fa.submit') }}" method="POST" class="space-y-4"
                  x-data="{ mode: 'code' }">
                @csrf

                <div x-show="mode === 'code'">
                    <label for="code" class="block text-gray-700 text-sm font-semibold mb-1.5">Código do aplicativo</label>
                    <input
                        type="text"
                        name="code"
                        id="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="000000"
                        autofocus
                        class="fi-input w-full rounded-lg px-3 py-2.5 text-center text-lg tracking-widest text-gray-900 transition border border-gray-300 bg-white"
                    >
                </div>

                <div x-show="mode === 'recovery'" x-cloak>
                    <label for="recovery_code" class="block text-gray-700 text-sm font-semibold mb-1.5">Código de recuperação</label>
                    <input
                        type="text"
                        name="recovery_code"
                        id="recovery_code"
                        autocomplete="off"
                        placeholder="xxxxxxxxxx-xxxxxxxxxx"
                        class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                    >
                </div>

                <button type="submit" class="btn-primary w-full rounded-lg py-2.5 text-sm font-semibold text-white transition-colors cursor-pointer">
                    Verificar e entrar
                </button>

                <div class="text-center">
                    <button type="button"
                            x-show="mode === 'code'"
                            x-on:click="mode = 'recovery'"
                            class="link-primary text-sm font-semibold bg-transparent border-0 cursor-pointer">
                        Usar código de recuperação
                    </button>
                    <button type="button"
                            x-show="mode === 'recovery'"
                            x-cloak
                            x-on:click="mode = 'code'"
                            class="link-primary text-sm font-semibold bg-transparent border-0 cursor-pointer">
                        Voltar para código do aplicativo
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('auth.login.mercado') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    &larr; Cancelar e voltar ao login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
