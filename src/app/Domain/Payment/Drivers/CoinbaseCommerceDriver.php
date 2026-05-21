<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Gateway;
use Illuminate\Support\Facades\Http;

class CoinbaseCommerceDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'coinbase_commerce';
    }

    public function name(): string
    {
        return 'Coinbase Commerce';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $apiKey = $payment->gateway->credential('api_key');

        if (! $apiKey) {
            throw new GatewayException('Coinbase Commerce API key not configured.');
        }

        $amountValue = number_format($payment->amount / 100, 2, '.', '');

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'X-CC-Api-Key' => $apiKey,
                    'X-CC-Version' => '2018-03-22',
                ])
                ->post('https://api.commerce.coinbase.com/charges', [
                    'name' => 'Wallet Payment',
                    'description' => 'Payment #'.$payment->id,
                    'pricing_type' => 'fixed_price',
                    'local_price' => ['amount' => $amountValue, 'currency' => strtoupper($payment->currency)],
                    'redirect_url' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'coinbase_commerce']),
                    'cancel_url' => route('user.payment.cancel', ['payment' => $payment->id]),
                    'metadata' => ['payment_id' => (string) $payment->id],
                ]);

            $body = $response->json();

            if (! $response->successful() || empty($body['data']['code'])) {
                throw new GatewayException('Coinbase Commerce charge creation failed: '.($body['error']['message'] ?? 'unknown'));
            }

            $payment->gateway_ref = $body['data']['code'];
            $payment->save();

            return $body['data']['hosted_url'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Coinbase Commerce initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // Coinbase Commerce confirms via webhook — return page shows pending
        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);
        $type = $event['event']['type'] ?? '';

        if (! in_array($type, ['charge:confirmed', 'charge:resolved'], true)) {
            return null;
        }

        $paymentId = $event['event']['data']['metadata']['payment_id'] ?? null;

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
            throw new WebhookSignatureException('Coinbase Commerce webhook secret not configured.');
        }

        $signature = $headers['x-cc-webhook-signature'] ?? '';
        $expected = hash_hmac('sha256', $rawPayload, $secret);

        if (! hash_equals($expected, $signature)) {
            throw new WebhookSignatureException('Coinbase Commerce webhook signature mismatch.');
        }
    }
}
