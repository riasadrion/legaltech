<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;

class NowPaymentsDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'nowpayments';
    }

    public function name(): string
    {
        return 'NOWPayments';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    private function baseUrl(Gateway $gateway): string
    {
        return $gateway->credential('sandbox') ? 'https://api-sandbox.nowpayments.io' : 'https://api.nowpayments.io';
    }

    public function initiate(Payment $payment): ?string
    {
        $apiKey = $payment->gateway->credential('api_key');

        if (! $apiKey) {
            throw new GatewayException('NOWPayments API key not configured.');
        }

        $amountValue = number_format($payment->amount / 100, 2, '.', '');

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['x-api-key' => $apiKey])
                ->post($this->baseUrl($payment->gateway).'/v1/invoice', [
                    'price_amount' => $amountValue,
                    'price_currency' => strtolower($payment->currency),
                    'pay_currency' => strtolower($payment->gateway->credential('pay_currency') ?: 'btc'),
                    'order_id' => 'pmt_'.$payment->id,
                    'order_description' => 'Wallet Payment #'.$payment->id,
                    'success_url' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'nowpayments']),
                    'cancel_url' => route('user.payment.cancel', ['payment' => $payment->id]),
                    'ipn_callback_url' => route('webhooks.payment', ['driver' => 'nowpayments']),
                ]);

            $body = $response->json();

            if (! $response->successful() || empty($body['id'])) {
                throw new GatewayException('NOWPayments invoice creation failed: '.($body['message'] ?? 'unknown'));
            }

            $payment->gateway_ref = (string) $body['id'];
            $payment->save();

            return $body['invoice_url'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('NOWPayments initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // Confirmed via IPN webhook
        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);

        $paymentStatus = $event['payment_status'] ?? '';

        if (! in_array($paymentStatus, ['finished', 'confirmed'], true)) {
            return null;
        }

        $orderId = $event['order_id'] ?? '';
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
        $ipnSecret = $gateway->credential('ipn_secret');
        if (! $ipnSecret) {
            throw new WebhookSignatureException('NOWPayments IPN secret not configured.');
        }

        $signature = $headers['x-nowpayments-sig'] ?? '';

        $sorted = json_decode($rawPayload, true);
        ksort($sorted, SORT_STRING);
        $expected = hash_hmac('sha512', json_encode($sorted), $ipnSecret);

        if (! hash_equals($expected, strtolower($signature))) {
            throw new WebhookSignatureException('NOWPayments webhook signature mismatch.');
        }
    }
}
