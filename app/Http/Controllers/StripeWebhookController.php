<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handleCheckoutSessionCompleted(object $session): void
    {
        $user = User::find($session->client_reference_id);

        if (! $user) {
            return;
        }

        $user->where('id', $user->id)
            ->where('subscription_status', '!=', 'active')
            ->update([
                'stripe_subscription_id' => $session->subscription,
                'subscription_status' => 'active',
            ]);
    }

    private function handleSubscriptionUpdated(object $subscription): void
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (! $user) {
            return;
        }

        $user->update([
            'subscription_status' => $subscription->status,
        ]);
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (! $user) {
            return;
        }

        $user->update([
            'subscription_status' => 'canceled',
        ]);
    }
}
