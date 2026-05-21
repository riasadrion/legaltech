<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Gateway;

class ManualDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'manual';
    }

    public function name(): string
    {
        return 'Manual / Bank Transfer';
    }

    public function supportsWebhook(): bool
    {
        return false;
    }

    public function initiate(Payment $payment): ?string
    {
        // No redirect — user uploads a receipt, admin approves
        $payment->status = Payment::STATUS_AWAITING_APPROVAL;
        $payment->save();

        return null;
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // No return URL for manual
        return $payment;
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        // Manual driver has no webhooks
        return null;
    }

    public function verifyWebhookSignature(Gateway $gateway, string $rawPayload, array $headers): void
    {
        // No webhook signature for manual
    }
}
