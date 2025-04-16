<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();
        return view('saas.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('saas.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:6',
        ]);

        // Cria Tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
        ]);

        // Cria Admin vinculado ao Tenant
        \App\Models\User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => bcrypt($request->admin_password),
            'type' => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])->with('success', 'Tenant e Admin criados com sucesso!');
    }


    public function edit(Tenant $tenant)
    {
        return view('saas.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('saas.tenants.index')->with('success', 'Tenant atualizado com sucesso!');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('saas.tenants.index')->with('success', 'Tenant removido com sucesso!');
    }
}
