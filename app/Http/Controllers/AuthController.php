<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Stripe\StripeClient;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('filament.painel.pages.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('filament.painel.pages.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas. Verifique seu e-mail e senha.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            if (! Auth::user()->hasActiveSubscription()) {
                return redirect()->route('auth.register.checkout');
            }

            return redirect()->route('filament.painel.pages.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $pendingUser = User::where('email', $request->email)
            ->where('subscription_status', 'pending')
            ->first();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($pendingUser?->id),
            ],
            'document' => ['required', 'string', 'max:18', Rule::unique('users', 'document')->ignore($pendingUser?->id)],
            'phonenumer' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'document.required' => 'O CPF/CNPJ é obrigatório.',
            'document.unique' => 'Este CPF ou CNPJ já está em uso.',
            'phonenumer.required' => 'O celular é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));

        if ($pendingUser) {
            $pendingUser->update([
                'name' => $validated['name'],
                'document' => $validated['document'],
                'phonenumer' => $validated['phonenumer'],
                'password' => Hash::make($validated['password']),
            ]);
            $user = $pendingUser;

            if (! $user->stripe_customer_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => ['user_id' => $user->id],
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            }
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'document' => $validated['document'],
                'phonenumer' => $validated['phonenumer'],
                'password' => Hash::make($validated['password']),
                'subscription_status' => 'pending',
            ]);

            $customer = $stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $request->session()->put('pending_user_id', $user->id);

        return redirect()->route('auth.register.checkout');
    }

    public function showCheckout(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');

        if (! $userId && Auth::check() && ! Auth::user()->hasActiveSubscription()) {
            $userId = Auth::id();
        }

        if (! $userId) {
            return redirect()->route('auth.register')
                ->withErrors(['email' => 'Sessão expirada. Por favor, preencha seus dados novamente.']);
        }

        $user = User::find($userId);

        if (! $user || $user->hasActiveSubscription()) {
            $request->session()->forget('pending_user_id');

            if ($user?->hasActiveSubscription()) {
                Auth::login($user);

                return redirect()->route('filament.painel.pages.dashboard');
            }

            return redirect()->route('auth.register');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $checkoutSession = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'customer' => $user->stripe_customer_id,
            'client_reference_id' => (string) $user->id,
            'line_items' => [[
                'price' => config('services.stripe.price_id'),
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'return_url' => route('auth.register.return').'?session_id={CHECKOUT_SESSION_ID}',
        ]);

        return view('auth.register-checkout', [
            'clientSecret' => $checkoutSession->client_secret,
            'stripePublishableKey' => config('services.stripe.publishable'),
        ]);
    }

    public function checkoutReturn(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('auth.register');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        if ($session->status === 'complete') {
            $user = User::find($session->client_reference_id);

            if ($user && ! $user->hasActiveSubscription()) {
                $user->update([
                    'stripe_subscription_id' => $session->subscription,
                    'subscription_status' => 'active',
                ]);
            }

            if ($user) {
                $request->session()->forget('pending_user_id');
                Auth::login($user);

                return redirect()->route('filament.painel.pages.dashboard');
            }
        }

        if ($session->status === 'open') {
            return redirect()->route('auth.register.checkout');
        }

        return redirect()->route('auth.register')
            ->withErrors(['email' => 'Sua sessão de pagamento expirou. Tente novamente.']);
    }

    public function reactivateSubscription(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('auth.login');
        }

        if ($user->hasActiveSubscription()) {
            return redirect()->route('filament.painel.pages.dashboard');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        if (! $user->stripe_customer_id) {
            $customer = $stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $checkoutSession = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'customer' => $user->stripe_customer_id,
            'client_reference_id' => (string) $user->id,
            'line_items' => [[
                'price' => config('services.stripe.price_id'),
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'return_url' => route('auth.register.return').'?session_id={CHECKOUT_SESSION_ID}',
        ]);

        return view('auth.register-checkout', [
            'clientSecret' => $checkoutSession->client_secret,
            'stripePublishableKey' => config('services.stripe.publishable'),
        ]);
    }
}
