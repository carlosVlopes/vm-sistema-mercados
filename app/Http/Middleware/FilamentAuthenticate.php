<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;

class FilamentAuthenticate extends Authenticate
{
    protected function redirectTo($request): ?string
    {
        $guard = Filament::getCurrentPanel()?->getAuthGuard();

        return route($guard === 'client' ? 'auth.login.sindico' : 'auth.login.mercado');
    }
}
