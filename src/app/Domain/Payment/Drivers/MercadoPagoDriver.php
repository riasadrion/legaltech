<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\GatewayException;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Facades\Http;

class MercadoPagoDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'mercadopago';
    }

    public function name(): string
    {
        return 'Mercado Pago';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $accessToken = $payment->gateway->credential('access_token');

        if (! $accessToken) {
            throw new GatewayException('Mercado Pago access token not configured.');
        }

        try {
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->post('https://api.mercadopago.com/checkout/preferences', [
                    'items' => [[
                        'title' => 'Wallet Payment',
                        'quantity' => 1,
                        'unit_price' => $payment->amount / 100,
                        'currency_id' => strtoupper($payment->currency),
                    ]],
                    'back_urls' => [
                        'success' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'mercadopago']),
                        'failure' => route('user.payment.cancel', ['payment' => $payment->id]),
                        'pending' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'mercadopago']),
                    ],
                    'auto_return' => 'approved',
                    'notification_url' => route('webhooks.payment', ['driver' => 'mercadopago']),
                    'external_reference' => (string) $payment->id,
                ]);

            $body = $response->json();

            if (! $response->successful() || empty($body['id'])) {
                throw new GatewayException('Mercado Pago preference creation failed: '.($body['message'] ?? 'unknown'));
            }

            $payment->gateway_ref = $body['id'];
            $payment->save();

            $isSandbox = (bool) $payment->gateway->credential('sandbox');

            return $isSandbox ? $body['sandbox_init_point'] : $body['init_point'];
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
            throw new GatewayException('Mercado Pago initiation failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        $status = $params['collection_status'] ?? $params['status'] ?? '';

        if ($status === 'approved' && ! $payment->isCompleted()) {
            $payment->status = Payment::STATUS_COMPLETED;
            $payment->save();
        }

        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        $event = json_decode($rawPayload, true);

        if (($event['type'] ?? '') !== 'payment') {
            return null;
        }

        $paymentId = $event['data']['id'] ?? null;
        if (! $paymentId) {
            return null;
        }

        $accessToken = $gateway->credential('access_token');

        try {
            $response = Http::withoutVerifying()
                ->withToken($accessToken)
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

            $body = $response->json();

            $paymentId = $body['external_reference'] ?? null;
            if (! $paymentId) {
                return null;
            }

            $payment = Payment::find($paymentId);
            if (! $payment || $payment->isCompleted()) {
                return $payment;
            }

            if (($body['status'] ?? '') === 'approved') {
                $payment->status = Payment::STATUS_COMPLETED;
                $payment->save();
            }

            return $payment;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function verifyWebhookSignature(Gateway $gateway, string $rawPayload, array $headers): void
    {
        // Mercado Pago uses x-signature header in newer API versions
        // For v1 IPN webhooks, the data is verified by fetching payment details from API in handleWebhook
        $xSignature = $headers['x-signature'] ?? '';
        $xRequestId = $headers['x-request-id'] ?? '';

        if (! $xSignature) {
            return; // older IPN format — verified via API lookup
        }

        $secret = $gateway->credential('webhook_secret');
        if (! $secret) {
            throw new WebhookSignatureException('Mercado Pago webhook secret not configured.');
        }

        $event = json_decode($rawPayload, true);
        $dataId = $event['data']['id'] ?? '';
        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:";

        // Extract ts from x-signature header
        $parts = [];
        foreach (explode(',', $xSignature) as $part) {
            [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
            $parts[trim($k)] = trim($v);
        }

        $ts = $parts['ts'] ?? '';
        $v1 = $parts['v1'] ?? '';
        $manifest .= $ts;

        $expected = hash_hmac('sha256', $manifest, $secret);

        if (! hash_equals($expected, $v1)) {
            throw new WebhookSignatureException('Mercado Pago webhook signature mismatch.');
        }
    }
}
