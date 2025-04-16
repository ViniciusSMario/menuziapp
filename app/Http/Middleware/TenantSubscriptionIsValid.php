<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TenantSubscriptionIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();

        // Busca o user admin do tenant
        $admin = User::where('tenant_id', $tenant->id)
            ->where('type', 'admin')
            ->first();

        // Se não encontrou admin, bloqueia por segurança
        if (!$admin) {
            return response()->view('site.blocked', ['tenant' => $tenant]);
        }

        $expired = !$admin->paid_until || now()->gt($admin->paid_until);

        if ($expired || !$admin->subscription_active) {
            return response()->view('site.blocked', ['tenant' => $tenant]);
        }

        return $next($request);
    }
}
