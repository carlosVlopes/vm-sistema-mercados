<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $existing = \DB::table('webhook_events')->where('stripe_event_id', $event->id)->first();

        if ($existing && $existing->processed_at) {
            return response()->json(['status' => 'duplicate']);
        }

        $webhookRowId = $existing->id ?? \DB::table('webhook_events')->insertGetId([
            'stripe_event_id' => $event->id,
            'type' => $event->type,
            'payload' => json_encode($event->toArray()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            match ($event->type) {
                'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event->data->object),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
                'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
                default => null,
            };

            DB::table('webhook_events')
                ->where('id', $webhookRowId)
                ->update(['processed_at' => now(), 'updated_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Stripe webhook failed', [
                'event' => $event->id,
                'type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleCheckoutSessionCompleted(object $session): void
    {
        $user = User::find($session->client_reference_id);

        if (! $user || $user->subscription_status === 'active') {
            return;
        }

        $user->stripe_subscription_id = $session->subscription;
        $user->subscription_status = 'active';
        $user->save();
    }

    private function handleSubscriptionUpdated(object $subscription): void
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (! $user) {
            return;
        }

        $user->subscription_status = $subscription->status;
        $user->save();
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (! $user) {
            return;
        }

        $user->subscription_status = 'canceled';
        $user->save();
    }
}
