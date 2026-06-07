<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;

class MollieDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'mollie';
    }

    public function name(): string
    {
        return 'Mollie';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $apiKey = $payment->gateway->credential('api_key');

        if (! $apiKey) {
            throw new GatewayException('Mollie API key not configured.');
        }

        $amountValue = number_format($payment->amount / 100, 2, '.', '');

        try {
            $response = Http::withoutVerifying()
                ->withToken($apiKey)
                ->post('https://api.mollie.com/v2/payments', [
                    'amount' => ['currency' => strtoupper($payment->currency), 'value' => $amountValue],
                    'description' => 'Wallet Payment #'.$payment->id,
                    'redirectUrl' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'mollie']),
                    'webhookUrl' => route('webhooks.payment', ['driver' => 'mollie']),
                    'metadata' => ['payment_id' => (string) $payment->id],
                ]);

            $body = $response->json();

            if (! $response->successful() || empty($body['id'])) {
                throw new GatewayException('Mollie payment creation failed: '.($body['detail'] ?? 'unknown'));
            }

            $payment->gateway_ref = $body['id'];
            $payment->save();

            return $body['_links']['checkout']['href'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Mollie initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // Status confirmed via webhook; return page shows pending
        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        parse_str($rawPayload, $data);
        $mollieId = $data['id'] ?? null;

        if (! $mollieId) {
            return null;
        }

        $apiKey = $gateway->credential('api_key');

        try {
            $response = Http::withoutVerifying()
                ->withToken($apiKey)
                ->get("https://api.mollie.com/v2/payments/{$mollieId}");

            $body = $response->json();

            $paymentId = $body['metadata']['payment_id'] ?? null;

            if (! $paymentId) {
                return null;
            }

            $payment = Payment::find($paymentId);
            if (! $payment || $payment->isCompleted()) {
                return $payment;
            }

            if (($body['status'] ?? '') === 'paid') {
                $payment->status = Payment::STATUS_COMPLETED;
                $payment->save();
            } elseif (in_array($body['status'] ?? '', ['failed', 'canceled', 'expired'], true)) {
                $payment->status = Payment::STATUS_FAILED;
                $payment->save();
            }

            return $payment;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function verifyWebhookSignature(Gateway $gateway, string $rawPayload, array $headers): void
    {
        // Mollie does not sign webhook bodies — IP allowlist or HTTPS is the guard
        // We do verify the payment ID via API call in handleWebhook instead
    }
}
