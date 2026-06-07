<?php

namespace App\Domain\Payment\Drivers;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Payment\Exceptions\WebhookSignatureException;
use App\Domain\Payment\Models\Gateway;
use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PayPalDriver implements PaymentGateway
{
    public function driver(): string
    {
        return 'paypal';
    }

    public function name(): string
    {
        return 'PayPal';
    }

    public function supportsWebhook(): bool
    {
        return true;
    }

    public function initiate(Payment $payment): ?string
    {
        $email = $payment->gateway->credential('business_email');
        $sandbox = (bool) ($payment->gateway->config['sandbox'] ?? false);
        $base = $sandbox
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';

        $amount = number_format($payment->amount / 100, 2, '.', '');

        $params = http_build_query([
            'cmd' => '_xclick',
            'business' => $email,
            'item_name' => 'Payment #'.$payment->id,
            'item_number' => $payment->id,
            'amount' => $amount,
            'currency_code' => $payment->currency,
            'no_shipping' => 1,
            'return' => route('user.payment.return', ['payment' => $payment->id, 'gateway' => 'paypal']),
            'cancel_return' => route('user.payment.cancel', ['payment' => $payment->id]),
            'notify_url' => route('webhooks.payment', 'paypal'),
            'custom' => $payment->id,
            'charset' => 'utf-8',
        ]);

        return $base.'?'.$params;
    }

    public function handleReturn(Payment $payment, array $params): Payment
    {
        // PayPal Standard marks payment via IPN; on return we mark as pending
        // and let the IPN webhook do the final status update.
        if ($payment->isPending()) {
            $payment->status = Payment::STATUS_PENDING;
            $payment->save();
        }

        return $payment->fresh();
    }

    public function handleWebhook(Gateway $gateway, string $rawPayload, array $headers): ?Payment
    {
        parse_str($rawPayload, $ipn);

        $paymentId = $ipn['custom'] ?? null;
        if (! $paymentId) {
            return null;
        }

        $payment = Payment::find($paymentId);
        if (! $payment || $payment->isCompleted()) {
            return $payment;
        }

        $paymentStatus = strtolower($ipn['payment_status'] ?? '');

        if ($paymentStatus === 'completed') {
            $payment->status = Payment::STATUS_COMPLETED;
            $payment->save();
        } elseif (in_array($paymentStatus, ['failed', 'denied', 'reversed', 'refunded'])) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
        }

        return $payment;
    }

    public function verifyWebhookSignature(Gateway $gateway, string $rawPayload, array $headers): void
    {
        // PayPal IPN verification: POST the payload back to PayPal with cmd=_notify-validate
        $sandbox = (bool) ($gateway->config['sandbox'] ?? false);
        $url = $sandbox
            ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.paypal.com/cgi-bin/webscr';

        $response = Http::asForm()->post($url, array_merge(
            ['cmd' => '_notify-validate'],
            Arr::except(request()->post(), [])
        ));

        if ($response->body() !== 'VERIFIED') {
            throw new WebhookSignatureException('PayPal IPN verification failed.');
        }
    }
}
