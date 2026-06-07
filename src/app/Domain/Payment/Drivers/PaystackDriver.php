<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;

class PaystackDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'paystack';
    }

    public function name(): string
    {
        return 'Paystack';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $secretKey = $payment->gateway->credential('secret_key');

        if (! $secretKey) {
            throw new GatewayException('Paystack secret key not configured.');
        }

        try {
            $response = Http::withoutVerifying()
                ->withToken($secretKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $payment->user->email,
                    'amount' => (int) round($payment->amount * 100),  // Paystack expects kobo (cents)
                    'currency' => strtoupper($payment->currency),
                    'reference' => 'pmt_'.$payment->id.'_'.time(),
                    'callback_url' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'paystack']),
                    'metadata' => ['payment_id' => $payment->id],
                ]);

            $body = $response->json();

            if (! $response->successful() || ! ($body['status'] ?? false)) {
                throw new GatewayException('Paystack initialization failed: '.($body['message'] ?? 'unknown'));
            }

            $payment->gateway_ref = $body['data']['reference'];
            $payment->save();

            return $body['data']['authorization_url'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Paystack initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        $reference = $params['reference'] ?? $payment->gateway_ref;

        if (! $reference || $payment->isCompleted()) {
            return $payment->fresh();
        }

        $secretKey = $payment->gateway->credential('secret_key');

        try {
            $response = Http::withoutVerifying()
                ->withToken($secretKey)
                ->get("https://api.paystack.co/transaction/verify/{$reference}");

            $body = $response->json();

            if (($body['data']['status'] ?? '') === 'success') {
                $payment->status = Payment::STATUS_COMPLETED;
                $payment->save();
            }
        } catch (\Throwable $e) {
            // verification failed — status stays pending
        }

        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);

        if (($event['event'] ?? '') !== 'charge.success') {
            return null;
        }

        $paymentId = $event['data']['metadata']['payment_id'] ?? null;

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
            throw new WebhookSignatureException('Paystack webhook secret not configured.');
        }

        $signature = $headers['x-paystack-signature'] ?? '';
        $expected = hash_hmac('sha512', $rawPayload, $secret);

        if (! hash_equals($expected, $signature)) {
            throw new WebhookSignatureException('Paystack webhook signature mismatch.');
        }
    }
}
