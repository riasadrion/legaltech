<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Gateway;
use Illuminate\Support\Facades\Http;

class RazorpayDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'razorpay';
    }

    public function name(): string
    {
        return 'Razorpay';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $key = $payment->gateway->credential('key_id');
        $secret = $payment->gateway->credential('key_secret');

        if (! $key || ! $secret) {
            throw new GatewayException('Razorpay credentials not configured.');
        }

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($key, $secret)
                ->post('https://api.razorpay.com/v1/payment_links', [
                    'amount' => (int) round($payment->amount * 100),  // Razorpay expects paise (cents)
                    'currency' => strtoupper($payment->currency),
                    'description' => 'Wallet Payment',
                    'callback_url' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'razorpay']),
                    'callback_method' => 'get',
                    'notes' => ['payment_id' => (string) $payment->id],
                ]);

            $body = $response->json();

            if (! $response->successful() || empty($body['id'])) {
                throw new GatewayException('Razorpay order creation failed: '.($body['error']['description'] ?? 'unknown'));
            }

            $payment->gateway_ref = $body['id'];
            $payment->save();

            return $body['short_url'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Razorpay initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        $status = $params['razorpay_payment_link_status'] ?? '';

        if ($status === 'paid' && ! $payment->isCompleted()) {
            $payment->status = Payment::STATUS_COMPLETED;
            $payment->save();
        }

        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);
        $type = $event['event'] ?? '';

        if ($type !== 'payment_link.paid') {
            return null;
        }

        $paymentId = $event['payload']['payment_link']['entity']['notes']['payment_id'] ?? null;

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
            throw new WebhookSignatureException('Razorpay webhook secret not configured.');
        }

        $signature = $headers['x-razorpay-signature'] ?? '';
        $expected = hash_hmac('sha256', $rawPayload, $secret);

        if (! hash_equals($expected, $signature)) {
            throw new WebhookSignatureException('Razorpay webhook signature mismatch.');
        }
    }
}
