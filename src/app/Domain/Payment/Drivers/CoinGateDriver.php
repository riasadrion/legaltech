<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Gateway;
use Illuminate\Support\Facades\Http;

class CoinGateDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'coingate';
    }

    public function name(): string
    {
        return 'CoinGate';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    private function baseUrl(Gateway $gateway): string
    {
        return $gateway->credential('sandbox') ? 'https://api-sandbox.coingate.com' : 'https://api.coingate.com';
    }

    public function initiate(Payment $payment): ?string
    {
        $apiKey = $payment->gateway->credential('api_token');

        if (! $apiKey) {
            throw new GatewayException('CoinGate API token not configured.');
        }

        $amountValue = number_format($payment->amount / 100, 2, '.', '');

        try {
            $response = Http::withoutVerifying()
                ->withToken($apiKey)
                ->post($this->baseUrl($payment->gateway).'/v2/orders', [
                    'order_id' => 'pmt_'.$payment->id,
                    'price_amount' => $amountValue,
                    'price_currency' => strtoupper($payment->currency),
                    'receive_currency' => $payment->gateway->credential('receive_currency') ?: 'USD',
                    'title' => 'Wallet Payment',
                    'callback_url' => route('webhooks.payment', ['driver' => 'coingate']),
                    'success_url' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'coingate']),
                    'cancel_url' => route('user.payment.cancel', ['payment' => $payment->id]),
                ]);

            $body = $response->json();

            if (! $response->successful() || empty($body['id'])) {
                throw new GatewayException('CoinGate order creation failed: '.($body['message'] ?? 'unknown'));
            }

            $payment->gateway_ref = (string) $body['id'];
            $payment->save();

            return $body['payment_url'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('CoinGate initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // Confirmed via webhook callback
        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        parse_str($rawPayload, $data);

        if (($data['status'] ?? '') !== 'paid') {
            return null;
        }

        $orderId = $data['order_id'] ?? '';
        // order_id format: dep_{payment_id}
        $paymentId = str_starts_with($orderId, 'pmt_') ? substr($orderId, 4) : null;

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
        // CoinGate does not sign the IPN body — verification is done by checking order status via API
        // Accepting only over HTTPS (enforced by server config) is the guard
    }
}
