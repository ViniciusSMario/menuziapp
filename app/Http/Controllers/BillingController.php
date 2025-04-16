<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('billing.index', [
            'user' => $user,
            'isActive' => $user->subscription_active,
            'paidUntil' => $user->paid_until,
            'isTrial' => $user->trial_ends_at && now()->lt($user->trial_ends_at),
        ]);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'customer' => $user->stripe_id,
            'line_items' => [[
                'price' => 'price_1R6Am3CKWcIpOQBwpJkfkUWc', // seu price ID
                'quantity' => 1,
            ]],
            'subscription_data' => [
                'trial_period_days' => 7, // 👈 aqui está o trial de 7 dias
            ],
            'success_url' => route('tenant.billing.success', ['tenant' => tenant()->slug]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('tenant.billing.page', ['tenant' => tenant()->slug]),
        ]);

        return redirect($session->url);
    }


    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('tenant.dashboard', ['tenat' => tenant()->slug])->with('error', 'Sessão inválida ou ausente.');
        }

        return view('billing.success');
    }
}
