<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            return redirect()->route('filament.painel.pages.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'document' => ['required', 'string', 'max:18'],
            'phonenumer' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'card_number' => ['required', 'string'],
            'card_name' => ['required', 'string'],
            'card_expiry' => ['required', 'string'],
            'card_cvv' => ['required', 'string'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'document.required' => 'O CPF/CNPJ é obrigatório.',
            'phonenumer.required' => 'O celular é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
            'card_number.required' => 'O número do cartão é obrigatório.',
            'card_name.required' => 'O nome no cartão é obrigatório.',
            'card_expiry.required' => 'A validade é obrigatória.',
            'card_cvv.required' => 'O CVV é obrigatório.',
        ]);

        // TODO: Processar pagamento com gateway antes de criar o usuário

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'document' => $validated['document'],
            'phonenumer' => $validated['phonenumer'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('filament.painel.pages.dashboard');
    }
}
