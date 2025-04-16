<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantSubscriptionActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->type === 'admin') {
            
            $expired = !$user->paid_until || now()->gt($user->paid_until);

            $tenant = Tenant::find($user->tenant_id);
            if ($expired) {
                if (!$request->routeIs('tenant.billing.page', $tenant->slug)) {
                    return redirect()->route('tenant.billing.page', $tenant->slug)->with('error', 'Sua assinatura expirou. Renove para continuar.');
                }
            }
        }

        return $next($request);
    }
}
