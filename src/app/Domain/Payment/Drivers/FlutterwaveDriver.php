<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;

class FlutterwaveDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'flutterwave';
    }

    public function name(): string
    {
        return 'Flutterwave';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $secretKey = $payment->gateway->credential('secret_key');

        if (! $secretKey) {
            throw new GatewayException('Flutterwave secret key not configured.');
        }

        try {
            $response = Http::withoutVerifying()
                ->withToken($secretKey)
                ->post('https://api.flutterwave.com/v3/payments', [
                    'tx_ref' => 'pmt_'.$payment->id.'_'.time(),
                    'amount' => $payment->amount / 100,
                    'currency' => strtoupper($payment->currency),
                    'redirect_url' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'flutterwave']),
                    'customer' => [
                        'email' => $payment->user->email,
                        'name' => $payment->user->name,
                    ],
                    'meta' => ['payment_id' => $payment->id],
                    'customizations' => ['title' => 'Wallet Payment'],
                ]);

            $body = $response->json();

            if (! $response->successful() || ($body['status'] ?? '') !== 'success') {
                throw new GatewayException('Flutterwave payment creation failed: '.($body['message'] ?? 'unknown'));
            }

            $payment->gateway_ref = $body['data']['id'] ?? null;
            $payment->save();

            return $body['data']['link'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Flutterwave initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        $status = $params['status'] ?? '';
        $txId = $params['transaction_id'] ?? '';

        if ($status !== 'successful' || ! $txId || $payment->isCompleted()) {
            return $payment->fresh();
        }

        $secretKey = $payment->gateway->credential('secret_key');

        try {
            $response = Http::withoutVerifying()
                ->withToken($secretKey)
                ->get("https://api.flutterwave.com/v3/transactions/{$txId}/verify");

            $body = $response->json();

            if (($body['data']['status'] ?? '') === 'successful') {
                $payment->status = Payment::STATUS_COMPLETED;
                $payment->save();
            }
        } catch (\Throwable $e) {
            // verification failed — stays pending
        }

        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);

        if (($event['event'] ?? '') !== 'charge.completed') {
            return null;
        }

        $paymentId = $event['data']['meta']['payment_id'] ?? null;

        if (! $paymentId) {
            return null;
        }

        $payment = Payment::find($paymentId);
        if (! $payment || $payment->isCompleted()) {
            return $payment;
        }

        if (($event['data']['status'] ?? '') === 'successful') {
            $payment->status = Payment::STATUS_COMPLETED;
            $payment->save();
        }

        return $payment;
    }

    public function verifyWebhookSignature(Gateway $gateway, string $rawPayload, array $headers): void
    {
        $secret = $gateway->credential('webhook_secret');
        if (! $secret) {
            throw new WebhookSignatureException('Flutterwave webhook secret not configured.');
        }

        $signature = $headers['verif-hash'] ?? '';

        if (! hash_equals($secret, $signature)) {
            throw new WebhookSignatureException('Flutterwave webhook signature mismatch.');
        }
    }
}
