<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.  
     */
    public function create(Tenant $tenant): View
    {
        return view('auth.login', compact('tenant'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, Tenant $tenant): RedirectResponse
    {
        $request->authenticate();
    
        $request->session()->regenerate();
    
        $user = Auth::user();
    
        if ($user->tenant_id !== $tenant->id) {
            Auth::logout();
            return redirect()->route('login', $tenant->slug)->withErrors([
                'email' => 'Você não pertence a este estabelecimento.',
            ]);
        }
    
        if ($user->type === 'super_admin') {
            return redirect()->route('saas.dashboard');
        } else if ($user->type === 'admin') {
            return redirect()->route('tenant.dashboard', $tenant->slug);
        } else if ($user->type === 'garcom') {
            return redirect()->route('garcom.dashboard', $tenant->slug);
        } else if ($user->type === 'client') {
            return redirect()->route('tenant.public.menu', $tenant->slug);
        } else {
            return redirect()->route('login', $tenant->slug)->withErrors([
                'email' => 'Tipo de usuário inválido.'
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
