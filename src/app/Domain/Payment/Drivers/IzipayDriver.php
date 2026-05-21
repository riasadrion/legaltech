<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Gateway;
use Illuminate\Support\Facades\Http;

class IzipayDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'izipay';
    }

    public function name(): string
    {
        return 'Izipay';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    private function baseUrl(Gateway $gateway): string
    {
        return $gateway->credential('sandbox')
            ? 'https://api.sandbox.izipay.pe'
            : 'https://api.izipay.pe';
    }

    public function initiate(Payment $payment): ?string
    {
        $shopId = $payment->gateway->credential('shop_id');
        $apiKey = $payment->gateway->credential('api_key');
        $publicKey = $payment->gateway->credential('public_key');

        if (! $shopId || ! $apiKey || ! $publicKey) {
            throw new GatewayException('Izipay credentials not configured (shop_id, api_key, public_key required).');
        }

        $orderId = 'pmt_'.$payment->id.'_'.time();

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($shopId, $apiKey)
                ->post($this->baseUrl($payment->gateway).'/api$payment/V4/Charge/CreatePayment', [
                    'amount' => (int) round($payment->amount * 100),  // Izipay expects cents
                    'currency' => strtoupper($payment->currency),
                    'orderId' => $orderId,
                    'customer' => [
                        'email' => $payment->user->email,
                    ],
                    'ipnTargetUrl' => route('webhooks.payment', ['driver' => 'izipay']),
                    'formAction' => 'PAYMENT',
                ]);

            $body = $response->json();

            if (! $response->successful() || ($body['status'] ?? '') !== 'SUCCESS') {
                throw new GatewayException('Izipay payment creation failed: '.($body['answer']['errorMessage'] ?? 'unknown'));
            }

            $formToken = $body['answer']['formToken'] ?? null;

            if (! $formToken) {
                throw new GatewayException('Izipay did not return a form token.');
            }

            $payment->gateway_ref = $orderId;
            $payment->save();

            // Redirect to the embedded payment page which renders the Izipay JS form
            return route('user.payment.izipay_form', [
                'payment' => $payment->id,
                'form_token' => $formToken,
                'public_key' => $publicKey,
            ]);
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Izipay initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // Status confirmed via IPN webhook
        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);

        if (($event['orderStatus'] ?? '') !== 'PAID') {
            return null;
        }

        $orderId = $event['orderId'] ?? '';
        // orderId format: dep_{payment_id}_{timestamp}
        if (! str_starts_with($orderId, 'pmt_')) {
            return null;
        }
        $parts = explode('_', $orderId);
        $paymentId = $parts[1] ?? null;

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
        $apiKey = $gateway->credential('api_key');
        if (! $apiKey) {
            throw new WebhookSignatureException('Izipay API key not configured.');
        }

        $event = json_decode($rawPayload, true);
        $hashField = $event['hash'] ?? '';

        unset($event['hash']);
        ksort($event);

        $fields = array_map(fn ($v) => is_array($v) ? json_encode($v) : (string) $v, $event);
        $computed = hash_hmac('sha256', implode('+', $fields), $apiKey);

        if (! hash_equals($computed, $hashField)) {
            throw new WebhookSignatureException('Izipay webhook signature mismatch.');
        }
    }
}
