<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientPasswordRegister;

class ClientController extends Controller
{
    public function registerPassword($token)
    {
        $user = Client::where('register_token', $token)->first();

        if (!$user) {
            return redirect()->route('filament.sindico.auth.login')->withErrors(['token' => 'Token inválido ou expirado.']);
        }

        return view('client-password-register', ['token' => $token, 'user' => $user]);
    }    

    public function storePassword(ClientPasswordRegister $request)
    {
        $request->only(['token', 'password', 'password_confirm']);

        $user = Client::where('register_token', $request->token)->where('password', null)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['token' => 'Token inválido ou expirado.']);
        }

        $user->password = bcrypt($request->password);
        $user->register_token = null;
        $user->save();

        return redirect()->route('registrar-senha.sucesso');
    }
}
