<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Tenant;

class InjectTenant
{
    public function handle($request, Closure $next)
    {
        $tenantParam = Route::current()?->parameter('tenant');

        // Se a rota não tem o parâmetro tenant, continua
        if (!$tenantParam) {
            return $next($request);
        }

        // Resolve o tenant (slug ou model)
        $tenant = $tenantParam instanceof Tenant
            ? $tenantParam
            : Tenant::where('slug', $tenantParam)->first();

        // Se não encontrou o tenant, redireciona para a home
        if (!$tenant) {
            return redirect('/')
                ->withErrors('Estabelecimento não encontrado.');
        }

        // Compartilha o tenant com as views
        View::share('tenant', $tenant);

        // Salva o tenant anterior na sessão para o JavaScript limpar o localStorage
        session(['__previous_tenant' => session('__current_tenant')]);
        session(['__current_tenant' => $tenant->slug]);

        // Se não estiver logado, pode continuar
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Se for cliente, continua em rotas públicas
        if ($user->type === 'client') {
            if ($user->tenant_id !== $tenant->id) {
                Auth::logout();

                return redirect()->route('tenant.public.menu', $tenant->slug)
                    ->withErrors('Você foi deslogado por estar em outro estabelecimento.');
            }
            return $next($request);
        }

        // Se for admin ou garçom de outro tenant, desloga e redireciona
        if ($user->tenant_id !== $tenant->id) {
            Auth::logout();

            // Evita erro de rota ausente se estiver fora do contexto de tenant
            $loginRoute = route('login', ['tenant' => $tenant->slug], false);

            return redirect($loginRoute)
                ->withErrors('Você foi deslogado por tentar acessar outro estabelecimento.');
        }

        return $next($request);
    }
}
