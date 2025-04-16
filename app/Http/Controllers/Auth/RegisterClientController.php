<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterClientController extends Controller
{
    public function create(Tenant $tenant)
    {
        return view('auth.register-client', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:20|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email ?? $request->name . uniqid() . '@guest.com',
            'phone' => preg_replace('/\D/', '', $request->phone), // limpa máscara
            'tenant_id' => $tenant->id,
            'password' => Hash::make($request->password),
            'type' => 'client',
        ]);

        Auth::login($user);

        return redirect()->route('tenant.public.menu', $tenant->slug);
    }
}