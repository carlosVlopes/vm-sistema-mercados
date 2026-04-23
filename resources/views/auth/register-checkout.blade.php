<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento — RepassesJá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(160deg, #faf8f6 0%, #FFF5ED 40%, #FFE7D0 100%); }
        .btn-secondary { background-color: #f3f4f6; color: #374151; }
        .btn-secondary:hover { background-color: #e5e7eb; }
        .step-indicator { width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; transition: all 0.3s; }
        .step-active { background-color: #FC6E20; color: #fff; }
        .step-completed { background-color: #10b981; color: #fff; }
        .step-line { height: 2px; flex: 1; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-8">

    <div class="w-full max-w-lg space-y-6">

        {{-- Brand --}}
        <div class="flex justify-center">
            <a href="{{ route('home') }}">
                @include('filament.brand-logo')
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">

            {{-- Step Indicator --}}
            <div class="flex items-center justify-center mb-8 px-4">
                <div class="step-indicator step-completed">&#10003;</div>
                <div class="step-line bg-emerald-400 mx-2"></div>
                <div class="step-indicator step-active">2</div>
            </div>

            <div class="mb-6 text-center">
                <h1 class="text-gray-900 text-xl font-bold mb-1">Dados de Pagamento</h1>
                <p class="text-gray-500 text-sm">Finalize sua assinatura para ativar o acesso.</p>
            </div>

            {{-- Plano --}}
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Plano Profissional</p>
                        <p class="text-xs text-gray-500">Acesso completo ao sistema</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold" style="color: #FC6E20;">R$ 45,00</p>
                        <p class="text-xs text-gray-500">/mês</p>
                    </div>
                </div>
            </div>

            {{-- Loading (hidden after Stripe mounts) --}}
            <div id="checkout-loading" class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-3 text-gray-500 text-sm">Carregando checkout...</span>
            </div>

            {{-- Stripe Embedded Checkout (must be empty) --}}
            <div id="checkout" class="min-h-[300px]"></div>

            <div class="mt-6">
                <a href="{{ route('auth.register') }}" class="btn-secondary inline-flex items-center justify-center w-full rounded-lg py-2.5 text-sm font-semibold transition-colors">
                    &larr; Voltar
                </a>
            </div>

        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">&larr; Voltar ao site</a>
        </div>

    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        (async function() {
            const stripe = Stripe(@json($stripePublishableKey));

            const checkout = await stripe.initEmbeddedCheckout({
                clientSecret: @json($clientSecret),
            });

            document.getElementById('checkout-loading').style.display = 'none';
            checkout.mount('#checkout');
        })();
    </script>
</body>
</html>
