<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientPasswordRegister;

class ClientController extends Controller
{
    public function registerPassword($token)
    {
        $hash = hash('sha256', $token);

        $user = Client::where('register_token', $hash)
            ->where('register_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return redirect()->route('filament.sindico.auth.login')->withErrors(['token' => 'Token inválido ou expirado.']);
        }

        return view('client-password-register', ['token' => $token, 'user' => $user]);
    }

    public function storePassword(ClientPasswordRegister $request)
    {
        $request->only(['token', 'password', 'password_confirm']);

        $hash = hash('sha256', $request->token);

        $user = Client::where('register_token', $hash)
            ->where('register_token_expires_at', '>', now())
            ->whereNull('password')
            ->first();

        if (!$user) {
            return redirect()->back()->withErrors(['token' => 'Token inválido ou expirado.']);
        }

        $user->password = bcrypt($request->password);
        $user->register_token = null;
        $user->register_token_expires_at = null;
        $user->save();

        return redirect()->route('registrar-senha.sucesso');
    }
}
