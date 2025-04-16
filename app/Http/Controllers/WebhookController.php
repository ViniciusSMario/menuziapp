<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verifica o token enviado no header
        $token = $request->header('X-Signature');
    
        if ($token !== env('WEBHOOK_SECRET')) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }
    
        // Continua com o processamento do pagamento
        $data = $request->all();
    
        $user = \App\Models\User::find($data['user_id'] ?? null);
    
        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }
    
        if (($data['status'] ?? null) === 'paid') {
            $user->paid_until = now()->addMonth();
            $user->subscription_active = true;
            $user->save();
    
            return response()->json(['success' => true]);
        }
    
        return response()->json(['message' => 'Pagamento não confirmado']);
    }    
}
