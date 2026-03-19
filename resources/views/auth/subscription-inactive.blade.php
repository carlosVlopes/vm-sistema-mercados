<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinatura Inativa — RepassesJá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(160deg, #faf8f6 0%, #FFF5ED 40%, #FFE7D0 100%); }
        .btn-primary { background-color: #FC6E20; }
        .btn-primary:hover { background-color: #e05a10; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-8">

    <div class="w-full max-w-md space-y-6">

        {{-- Brand --}}
        <div class="flex justify-center">
            <a href="{{ route('home') }}">
                @include('filament.brand-logo')
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm text-center">

            <div class="mb-6">
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h1 class="text-gray-900 text-xl font-bold mb-2">Assinatura Inativa</h1>
                <p class="text-gray-500 text-sm">Sua assinatura não está ativa. Para continuar utilizando o sistema, reative sua assinatura.</p>
            </div>

            <a href="{{ route('assinatura.reativar') }}" class="btn-primary inline-flex items-center justify-center w-full rounded-lg py-2.5 text-sm font-semibold text-white transition-colors">
                Reativar Assinatura
            </a>

            <div class="mt-4">
                <form action="{{ route('filament.painel.auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition">
                        Sair da conta
                    </button>
                </form>
            </div>

        </div>

    </div>
</body>
</html>
