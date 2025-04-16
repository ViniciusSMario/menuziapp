<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterTenantController extends Controller
{
    public function index()
    {
        return view('menuziApp');
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'slug'       => 'required|alpha_dash|unique:tenants,slug',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
        ]);

        // Cria o tenant
        $tenant = \App\Models\Tenant::create([
            'name' => $request->store_name,
            'slug' => $request->slug,
            'main_color' => '#3b82f6',
        ]);

        // Cria o usuário admin
        $user = \App\Models\User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'type' => 'admin',
            'trial_ends_at' => now()->addDays(7),
            'subscription_active' => false,
        ]);

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $customer = $stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
        ]);
        $user->stripe_id = $customer->id;
        $user->save();

        // Autentica o usuário e redireciona
        Auth::login($user);

        return redirect()->route('tenant.billing.checkout', ['tenant' => $tenant->slug]);
    }
}
