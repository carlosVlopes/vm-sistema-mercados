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

        auth()->guard($guard)->logout();

        session()->regenerateToken();

        return redirect()->to($panel->getLoginUrl());
    }
}
