<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Stripe\Webhook;
use Carbon\Carbon;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $signature, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('⚠️ Stripe Webhook - Payload inválido', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('⚠️ Stripe Webhook - Assinatura inválida', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'invoice.paid') {
            $invoice = $event->data->object;

            // Stripe customer ID
            $stripeCustomerId = $invoice->customer ?? null;

            // Pega o primeiro item da fatura (assumindo que só há 1 plano)
            $lineItem = $invoice->lines->data[0] ?? null;
            $periodEndTimestamp = $lineItem?->period->end ?? null;

            if ($stripeCustomerId && $periodEndTimestamp) {
                $user = User::where('stripe_id', $stripeCustomerId)->first();

                if ($user) {
                    $user->paid_until = Carbon::createFromTimestamp($periodEndTimestamp);
                    $user->subscription_active = true;

                    // Se ainda estiver em período de teste, encerra
                    if ($user->trial_ends_at && now()->lt($user->trial_ends_at)) {
                        $user->trial_ends_at = null;
                    }

                    $user->save();

                    Log::info("✅ Assinatura ativada para {$user->email} até {$user->paid_until}");
                } else {
                    Log::warning("❌ Webhook recebido mas usuário não encontrado para stripe_id: {$stripeCustomerId}");
                }
            } else {
                Log::warning('❌ Webhook invoice.paid sem stripe_id ou period_end');
            }
        }

        return response()->json(['received' => true], 200);
    }
}
