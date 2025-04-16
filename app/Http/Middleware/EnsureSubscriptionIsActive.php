<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSubscriptionIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Apenas aplica para usuários do tipo "admin"
        if ($user && $user->type === 'admin') {
            // Verifica se o usuário tem uma assinatura ativa
            if (!$user->subscribed('default')) {
                return redirect()->route('billing.page')->with('error', 'Você precisa ter uma assinatura ativa para acessar esta área.');
            }
        }

        return $next($request);
    }
}
