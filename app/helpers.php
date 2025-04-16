<?php

use App\Models\Tenant;

if (!function_exists('tenant')) {
    function tenant()
    {
        $tenantParam = request()->route('tenant');

        if ($tenantParam instanceof Tenant) {
            return $tenantParam;
        }

        // Se veio como string (slug), busque o model
        return Tenant::where('slug', $tenantParam)->first();
    }
}
