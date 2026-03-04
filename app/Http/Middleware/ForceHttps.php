<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        $request->server->set('HTTPS', 'on');
        $request->server->set('SERVER_PORT', 443);

        return $next($request);
    }
}