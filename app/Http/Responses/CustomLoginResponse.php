<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->type === 'super_admin') {
            return redirect()->route('saas.dashboard');
        }

        if ($user->type === 'admin') {
            return redirect()->route('tenant.dashboard', ['tenant' => $user->tenant->slug]);
        }

        if ($user->type === 'garcom') {
            return redirect()->route('garcom.dashboard', ['tenant' => $user->tenant->slug]);
        }

        if ($user->type === 'client') {
            // Redireciona o cliente para a página pública do cardápio do seu tenant
            return redirect()->route('tenant.public.menu', ['tenant' => $user->tenant->slug]);
        }

        // fallback
        return redirect()->route('login')->withErrors(['email' => 'Tipo de usuário inválido.']);
    }
}
