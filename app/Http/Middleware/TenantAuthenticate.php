<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class TenantAuthenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Pega o tenant da rota atual
        $tenant = Route::current()?->parameter('tenant');

        // Se não tiver tenant, redireciona para /
        if (!$tenant) {
            return '/';
        }

        // Redireciona para a rota de login com o tenant
        return route('login', ['tenant' => $tenant]);
    }
}
