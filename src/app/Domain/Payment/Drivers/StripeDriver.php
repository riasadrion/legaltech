<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'stripe';
    }

    public function name(): string
    {
        return 'Stripe';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $stripe = $this->client($payment->gateway);

        try {
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($payment->currency),
                        'unit_amount' => (int) round($payment->amount * 100),  // Stripe expects cents
                        'product_data' => ['name' => 'Wallet Payment'],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'stripe']).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('user.payment.cancel', ['payment' => $payment->id]),
                'metadata' => ['payment_id' => $payment->id],
            ]);

            $payment->gateway_ref = $session->id;
            $payment->save();

            return $session->url;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Stripe initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // Stripe confirms via webhook — the return URL just shows a "pending" screen
        // Status will be updated when checkout.session.completed webhook fires
        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);
        $type = $event['type'] ?? '';

        if (! in_array($type, ['checkout.session.completed', 'payment_intent.succeeded'], true)) {
            return null;
        }

        $paymentId = null;
        if ($type === 'checkout.session.completed') {
            $paymentId = $event['data']['object']['metadata']['payment_id'] ?? null;
        } elseif ($type === 'payment_intent.succeeded') {
            $paymentId = $event['data']['object']['metadata']['payment_id'] ?? null;
        }

        if (! $paymentId) {
            return null;
        }

        $payment = Payment::find($paymentId);
        if (! $payment || $payment->isCompleted()) {
            return $payment;
        }

        $payment->status = Payment::STATUS_COMPLETED;
        $payment->save();

        return $payment;
    }

    public function verifyWebhookSignature(Gateway $gateway, string $rawPayload, array $headers): void
    {
        $secret = $gateway->credential('webhook_secret');
        if (! $secret) {
            throw new WebhookSignatureException('Stripe webhook secret not configured.');
        }

        $sigHeader = $headers['stripe-signature'] ?? $headers['Stripe-Signature'] ?? '';

        try {
            Webhook::constructEvent($rawPayload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            throw new WebhookSignatureException('Stripe webhook signature mismatch.', 0, $e);
        }
    }

    private function client(Gateway $gateway): StripeClient
    {
        $key = $gateway->credential('secret_key');
        if (! $key) {
            throw new GatewayException('Stripe secret key not configured.');
        }

        return new StripeClient($key);
    }
}
