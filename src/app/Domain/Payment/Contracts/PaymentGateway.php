<?php

namespace App\Domain\Payment\Contracts;

use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Gateway;
use App\Models\User;

interface PaymentGateway
{
    /**
     * The driver key stored in the gateways table (e.g. "stripe", "paypal", "manual").
     */
    public function driver(): string;

    /**
     * Human-readable name shown in admin.
     */
    public function name(): string;

    /**
     * Whether this gateway supports webhook-based confirmation
     * (false = manual approval flow instead).
     */
    public function supportsWebhook(): bool;

    /**
     * Initiate a payment. Returns a URL to redirect the user to,
     * or null for manual/no-redirect gateways.
     */
    public function initiate(Payment $payment): ?string;

    /**
     * Handle the return redirect from the gateway after payment.
     * Should update payment status and return the payment.
     */
    public function handleReturn(Payment $payment, array $params): Payment;

    /**
     * Verify and process an incoming webhook payload.
     * Must be idempotent — check webhook_events before acting.
     * Returns the affected payment or null if irrelevant event.
     */
    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment;

    /**
     * Verify the webhook signature. Throws on mismatch.
     */
    public function verifyWebhookSignature(Gateway $gateway, string $rawPayload, array $headers): void;
}
