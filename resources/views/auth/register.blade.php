<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — RepassesJá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(160deg, #faf8f6 0%, #FFF5ED 40%, #FFE7D0 100%); }
        .fi-input:focus { outline: none; border-color: #FC6E20; box-shadow: 0 0 0 3px rgba(252, 110, 32, 0.15); }
        .btn-primary { background-color: #FC6E20; }
        .btn-primary:hover { background-color: #e05a10; }
        .btn-secondary { background-color: #f3f4f6; color: #374151; }
        .btn-secondary:hover { background-color: #e5e7eb; }
        .link-primary { color: #FC6E20; }
        .link-primary:hover { color: #e05a10; }
        .step-indicator { width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; transition: all 0.3s; }
        .step-active { background-color: #FC6E20; color: #fff; }
        .step-inactive { background-color: #e5e7eb; color: #9ca3af; }
        .step-completed { background-color: #10b981; color: #fff; }
        .step-line { height: 2px; flex: 1; transition: background-color 0.3s; }
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
        <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">

            {{-- Step Indicator --}}
            <div class="flex items-center justify-center mb-8 px-4">
                <div class="step-indicator step-active" id="step-dot-1">1</div>
                <div class="step-line bg-gray-200 mx-2" id="step-line"></div>
                <div class="step-indicator step-inactive" id="step-dot-2">2</div>
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

            <form action="{{ route('auth.register.submit') }}" method="POST" id="register-form">
                @csrf

                {{-- STEP 1: Dados Pessoais --}}
                <div id="step-1">
                    <div class="mb-6 text-center">
                        <h1 class="text-gray-900 text-xl font-bold mb-1">Crie sua conta</h1>
                        <p class="text-gray-500 text-sm">Preencha seus dados para começar.</p>
                    </div>

                    <div class="space-y-4">
                        {{-- Nome --}}
                        <div>
                            <label for="name" class="block text-gray-700 text-sm font-semibold mb-1.5">Nome completo</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                placeholder="Seu nome completo"
                                required
                                class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                            >
                        </div>

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
                        </div>

                        {{-- CPF ou CNPJ --}}
                        <div>
                            <label for="document" class="block text-gray-700 text-sm font-semibold mb-1.5">CPF ou CNPJ</label>
                            <input
                                type="text"
                                name="document"
                                id="document"
                                value="{{ old('document') }}"
                                placeholder="000.000.000-00"
                                required
                                maxlength="18"
                                class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                            >
                        </div>

                        {{-- Celular --}}
                        <div>
                            <label for="phonenumer" class="block text-gray-700 text-sm font-semibold mb-1.5">Celular</label>
                            <input
                                type="text"
                                name="phonenumer"
                                id="phonenumer"
                                value="{{ old('phonenumer') }}"
                                placeholder="(00) 00000-0000"
                                required
                                maxlength="15"
                                class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                            >
                        </div>

                        {{-- Senha --}}
                        <div>
                            <label for="password" class="block text-gray-700 text-sm font-semibold mb-1.5">Senha</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    placeholder="Mínimo 8 caracteres"
                                    required
                                    minlength="8"
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
                        </div>

                        {{-- Confirmar Senha --}}
                        <div>
                            <label for="password_confirmation" class="block text-gray-700 text-sm font-semibold mb-1.5">Confirmar senha</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    placeholder="••••••••"
                                    required
                                    minlength="8"
                                    class="fi-input w-full rounded-lg px-3 py-2.5 pr-10 text-sm text-gray-900 transition border border-gray-300 bg-white"
                                >
                                <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 bg-transparent border-none cursor-pointer">
                                    <svg id="eye-confirm" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="button" onclick="goToStep2()" class="btn-primary w-full rounded-lg py-2.5 text-sm font-semibold text-white transition-colors cursor-pointer mt-2">
                            Continuar
                        </button>
                    </div>
                </div>

                {{-- STEP 2: Pagamento --}}
                <div id="step-2" class="hidden">
                    <div class="mb-6 text-center">
                        <h1 class="text-gray-900 text-xl font-bold mb-1">Dados de Pagamento</h1>
                        <p class="text-gray-500 text-sm">Insira os dados do cartão para ativar sua assinatura.</p>
                    </div>

                    {{-- Plano --}}
                    <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Plano Profissional</p>
                                <p class="text-xs text-gray-500">Acesso completo ao sistema</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold" style="color: #FC6E20;">R$ 49,90</p>
                                <p class="text-xs text-gray-500">/mês</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- Número do Cartão --}}
                        <div>
                            <label for="card_number" class="block text-gray-700 text-sm font-semibold mb-1.5">Número do cartão</label>
                            <input
                                type="text"
                                name="card_number"
                                id="card_number"
                                placeholder="0000 0000 0000 0000"
                                required
                                maxlength="19"
                                class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                            >
                        </div>

                        {{-- Nome no Cartão --}}
                        <div>
                            <label for="card_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Nome no cartão</label>
                            <input
                                type="text"
                                name="card_name"
                                id="card_name"
                                placeholder="Como está impresso no cartão"
                                required
                                class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Validade --}}
                            <div>
                                <label for="card_expiry" class="block text-gray-700 text-sm font-semibold mb-1.5">Validade</label>
                                <input
                                    type="text"
                                    name="card_expiry"
                                    id="card_expiry"
                                    placeholder="MM/AA"
                                    required
                                    maxlength="5"
                                    class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                                >
                            </div>

                            {{-- CVV --}}
                            <div>
                                <label for="card_cvv" class="block text-gray-700 text-sm font-semibold mb-1.5">CVV</label>
                                <input
                                    type="text"
                                    name="card_cvv"
                                    id="card_cvv"
                                    placeholder="000"
                                    required
                                    maxlength="4"
                                    class="fi-input w-full rounded-lg px-3 py-2.5 text-sm text-gray-900 transition border border-gray-300 bg-white"
                                >
                            </div>
                        </div>

                        <div class="flex gap-3 mt-2">
                            <button type="button" onclick="goToStep1()" class="btn-secondary flex-1 rounded-lg py-2.5 text-sm font-semibold transition-colors cursor-pointer">
                                Voltar
                            </button>
                            <button type="submit" class="btn-primary flex-1 rounded-lg py-2.5 text-sm font-semibold text-white transition-colors cursor-pointer">
                                Finalizar Cadastro
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Já tem uma conta?
                    <a href="{{ route('auth.login') }}" class="link-primary font-semibold">Entrar</a>
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

        function goToStep2() {
            // Validate step 1 fields
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const document_ = document.getElementById('document');
            const phone = document.getElementById('phonenumer');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            const fields = [name, email, document_, phone, password, passwordConfirm];
            let valid = true;

            fields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    valid = false;
                } else {
                    field.style.borderColor = '#d1d5db';
                }
            });

            if (password.value.length < 8) {
                password.style.borderColor = '#ef4444';
                valid = false;
            }

            if (password.value !== passwordConfirm.value) {
                passwordConfirm.style.borderColor = '#ef4444';
                valid = false;
            }

            if (!valid) return;

            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            document.getElementById('step-dot-1').className = 'step-indicator step-completed';
            document.getElementById('step-dot-1').innerHTML = '&#10003;';
            document.getElementById('step-dot-2').className = 'step-indicator step-active';
            document.getElementById('step-line').style.backgroundColor = '#10b981';
        }

        function goToStep1() {
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-dot-1').className = 'step-indicator step-active';
            document.getElementById('step-dot-1').innerHTML = '1';
            document.getElementById('step-dot-2').className = 'step-indicator step-inactive';
            document.getElementById('step-line').style.backgroundColor = '#e5e7eb';
        }

        // Mask: CPF/CNPJ
        document.getElementById('document').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length <= 11) {
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                v = v.substring(0, 14);
                v = v.replace(/^(\d{2})(\d)/, '$1.$2');
                v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
                v = v.replace(/(\d{4})(\d)/, '$1-$2');
            }
            e.target.value = v;
        });

        // Mask: Phone
        document.getElementById('phonenumer').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            v = v.substring(0, 11);
            if (v.length > 6) {
                v = v.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            } else if (v.length > 2) {
                v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (v.length > 0) {
                v = v.replace(/^(\d{0,2})/, '($1');
            }
            e.target.value = v;
        });

        // Mask: Card Number
        document.getElementById('card_number').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 16);
            v = v.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = v;
        });

        // Mask: Expiry
        document.getElementById('card_expiry').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 4);
            if (v.length > 2) {
                v = v.replace(/^(\d{2})(\d)/, '$1/$2');
            }
            e.target.value = v;
        });

        // Mask: CVV
        document.getElementById('card_cvv').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
    </script>
</body>
</html>
