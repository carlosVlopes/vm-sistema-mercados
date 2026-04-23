<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->hasActiveSubscription()) {
            return $next($request);
        }

        if ($request->routeIs(
            'filament.painel.pages.configuracoes',
            'filament.painel.pages.assinatura',
            'filament.painel.auth.logout',
            'auth.login.mercado',
            'assinatura.inativa',
            'assinatura.reativar',
            'auth.register.return',
            'stripe.webhook',
        )) {
            return $next($request);
        }

        if ($user->subscription_status === 'pending') {
            return redirect()->route('auth.register.checkout');
        }

        return redirect()->route('assinatura.inativa');
    }
}
