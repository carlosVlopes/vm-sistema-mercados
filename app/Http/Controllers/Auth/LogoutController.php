<?php

namespace App\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;

class LogoutController
{
    public function __invoke(): RedirectResponse
    {
        $panel = Filament::getCurrentPanel();
        $guard = $panel->getAuthGuard();

        auth()->guard('web')->logout();
        auth()->guard('client')->logout();

        session()->invalidate();
        session()->regenerateToken();

        $route = $guard === 'client' ? 'auth.login.sindico' : 'auth.login.mercado';

        return redirect()->route($route);
    }
}
