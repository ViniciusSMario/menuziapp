<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;

class SetCurrentTenant
{
    public function handle($request, Closure $next)
    {
        $tenantParam = $request->route('tenant');

        if ($tenantParam instanceof Tenant) {
            app()->instance('tenant', $tenantParam);
        } else {
            $tenant = Tenant::where('slug', $tenantParam)->firstOrFail();
            app()->instance('tenant', $tenant);
        }

        return $next($request);
    }
}
