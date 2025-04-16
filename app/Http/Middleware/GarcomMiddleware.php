<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class GarcomMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type === 'garcom') {
            return $next($request);
        }

        abort(403, 'Acesso não autorizado.');
    }
}
